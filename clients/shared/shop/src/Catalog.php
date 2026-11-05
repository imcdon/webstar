<?php

declare(strict_types=1);

namespace Webstar\Shop;

use PDO;
use RuntimeException;
use Square\Catalog\Requests\ListCatalogRequest;
use Square\Types\BatchGetInventoryCountsRequest;

final class Catalog
{
    public function __construct(
        private readonly Config $config,
        private readonly PDO $db,
    ) {
    }

    public static function make(Config $config): self
    {
        return new self($config, Db::pdo());
    }

    /**
     * Sync Square catalog + inventory into SQLite. No-op source when demoMode —
     * use seed-local.php instead.
     */
    public function syncFromSquare(): array
    {
        if ($this->config->demoMode) {
            throw new RuntimeException('Demo mode: use bin/seed-local.php to load seed/products.json.');
        }

        $square = SquareClientFactory::make($this->config);
        $objects = [];
        $images = [];
        $categories = [];
        /** @var array<string, array{name:string, values:array<string,string>}> $optionDefs squareOptionId => def */
        $optionDefs = [];

        $iterator = $square->catalog->list(new ListCatalogRequest([
            'types' => 'ITEM,IMAGE,CATEGORY,ITEM_OPTION',
        ]));

        foreach ($iterator as $object) {
            if ($object->isImage()) {
                $img = $object->asImage();
                $images[$img->getId()] = $img->getImageData()?->getUrl();
            } elseif ($object->isCategory()) {
                $cat = $object->asCategory();
                $categories[$cat->getId()] = $cat->getCategoryData()?->getName() ?? 'Category';
            } elseif ($object->isItemOption()) {
                $opt = $object->asItemOption();
                $data = $opt->getItemOptionData();
                $values = [];
                foreach ($data?->getValues() ?? [] as $valObj) {
                    if (!$valObj->isItemOptionVal()) {
                        continue;
                    }
                    $val = $valObj->asItemOptionVal();
                    $values[$val->getId()] = (string) ($val->getItemOptionValueData()?->getName() ?? '');
                }
                $optionDefs[$opt->getId()] = [
                    'name' => (string) ($data?->getDisplayName() ?? $data?->getName() ?? 'Option'),
                    'values' => $values,
                ];
            } elseif ($object->isItem()) {
                $objects[] = $object;
            }
        }

        $this->db->beginTransaction();
        try {
            $this->db->exec('DELETE FROM product_images');
            $this->db->exec('DELETE FROM variations');
            $this->db->exec('DELETE FROM products');
            $this->db->exec('DELETE FROM categories');
            $this->clearFts();

            $catStmt = $this->db->prepare(
                'INSERT INTO categories (id, square_id, name, slug, sort_order) VALUES (?,?,?,?,?)'
            );
            $sort = 0;
            $catMap = [];
            foreach ($categories as $squareId => $name) {
                $id = Db::newId('cat_');
                $slug = Helpers::slugify($name);
                $catStmt->execute([$id, $squareId, $name, $slug, $sort++]);
                $catMap[$squareId] = $id;
            }

            $prodStmt = $this->db->prepare(
                'INSERT INTO products (id, square_id, category_id, name, slug, description, description_html, image_url, local_image, options_json, is_active, sort_order, synced_at)
                 VALUES (?,?,?,?,?,?,?,?,?,?,1,?,datetime(\'now\'))'
            );
            $varStmt = $this->db->prepare(
                'INSERT INTO variations (id, product_id, square_id, name, sku, price_cents, track_inventory, quantity, weight_label, option_values_json, is_active, sort_order)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?)'
            );
            $imgStmt = $this->db->prepare(
                'INSERT INTO product_images (product_id, square_id, url, local_path, sort_order) VALUES (?,?,?,?,?)'
            );

            $localSeed = $this->localSeedIndex();
            $usedSlugs = [];

            $variationIds = [];
            $pSort = 0;
            foreach ($objects as $object) {
                $catalogItem = $object->asItem();
                $squareItemId = $catalogItem->getId();
                $item = $catalogItem->getItemData();
                if ($item === null) {
                    continue;
                }
                $name = (string) $item->getName();
                $productId = Db::newId('prd_');
                $slug = $baseSlug = Helpers::slugify($name);
                for ($n = 2; isset($usedSlugs[$slug]); $n++) {
                    $slug = $baseSlug . '-' . $n;
                }
                $usedSlugs[$slug] = true;
                $seedMatch = $localSeed[strtolower($name)] ?? null;

                // Option groups this item uses, in Square's order; values filled from variations below
                $itemOptions = [];
                foreach ($item->getItemOptions() ?? [] as $optRef) {
                    $oid = (string) $optRef->getItemOptionId();
                    if (isset($optionDefs[$oid])) {
                        $itemOptions[$oid] = ['name' => $optionDefs[$oid]['name'], 'values' => []];
                    }
                }
                $catId = null;
                $catIds = $item->getCategories() ?? [];
                if ($catIds) {
                    $first = $catIds[0];
                    $squareCatId = is_object($first) && method_exists($first, 'getId') ? $first->getId() : (string) $first;
                    $catId = $catMap[$squareCatId] ?? null;
                }
                // Square also uses categoryId on older shapes
                if ($catId === null && method_exists($item, 'getCategoryId') && $item->getCategoryId()) {
                    $catId = $catMap[$item->getCategoryId()] ?? null;
                }

                $imageUrl = null;
                $imageIds = $item->getImageIds() ?? [];
                if ($imageIds) {
                    $imageUrl = $images[$imageIds[0]] ?? null;
                }
                // Square has no image → fall back to seed image(s) for this listing
                $seedImages = $seedMatch['images'] ?? [];
                if ($imageUrl === null && $seedImages) {
                    $imageUrl = $seedImages[0];
                }

                // Collect variations first so options_json can list only values actually sold
                $pendingVars = [];
                foreach ($item->getVariations() ?? [] as $variationObj) {
                    if (!$variationObj->isItemVariation()) {
                        continue;
                    }
                    $catalogVariation = $variationObj->asItemVariation();
                    $v = $catalogVariation->getItemVariationData();
                    if ($v === null) {
                        continue;
                    }
                    $optionValues = [];
                    foreach ($v->getItemOptionValues() ?? [] as $ov) {
                        $oid = (string) $ov->getItemOptionId();
                        $vid = (string) $ov->getItemOptionValueId();
                        if (!isset($optionDefs[$oid])) {
                            continue;
                        }
                        $optName = $optionDefs[$oid]['name'];
                        $valName = $optionDefs[$oid]['values'][$vid] ?? $vid;
                        $optionValues[$optName] = $valName;
                        if (!isset($itemOptions[$oid])) {
                            $itemOptions[$oid] = ['name' => $optName, 'values' => []];
                        }
                        if (!in_array($valName, $itemOptions[$oid]['values'], true)) {
                            $itemOptions[$oid]['values'][] = $valName;
                        }
                    }
                    $pendingVars[] = [$catalogVariation->getId(), $v, $optionValues];
                }

                // Order option values by Square's ordinal (definition order)
                $optionsJson = [];
                foreach ($itemOptions as $oid => $opt) {
                    $defined = array_values($optionDefs[$oid]['values'] ?? []);
                    $ordered = array_values(array_filter($defined, fn ($n) => in_array($n, $opt['values'], true)));
                    $optionsJson[] = ['name' => $opt['name'], 'values' => $ordered ?: $opt['values']];
                }

                $prodStmt->execute([
                    $productId,
                    $squareItemId,
                    $catId,
                    $name,
                    $slug,
                    strip_tags((string) ($item->getDescription() ?? $item->getDescriptionHtml() ?? '')),
                    (string) ($item->getDescriptionHtml() ?? ''),
                    $imageUrl,
                    $seedMatch['local_image'] ?? null,
                    json_encode($optionsJson, JSON_UNESCAPED_SLASHES),
                    $pSort++,
                ]);

                $imgSort = 0;
                foreach ($imageIds as $imgId) {
                    if (!empty($images[$imgId])) {
                        $imgStmt->execute([$productId, $imgId, $images[$imgId], null, $imgSort++]);
                    }
                }
                if ($imgSort === 0) {
                    foreach ($seedImages as $i => $url) {
                        $imgStmt->execute([$productId, null, $url, $i === 0 ? ($seedMatch['local_image'] ?? null) : null, $imgSort++]);
                    }
                }

                $vSort = 0;
                foreach ($pendingVars as [$squareVarId, $v, $optionValues]) {
                    $varId = Db::newId('var_');
                    $price = $v->getPriceMoney()?->getAmount() ?? 0;
                    $track = (bool) ($v->getTrackInventory() ?? true);
                    $vName = (string) ($v->getName() ?? 'Default');
                    if ($optionValues !== [] && ($vName === '' || $vName === 'Regular' || $vName === 'Default')) {
                        $vName = implode(' / ', $optionValues);
                    }
                    $weightSource = $optionValues['Size'] ?? $vName;
                    $varStmt->execute([
                        $varId,
                        $productId,
                        $squareVarId,
                        $vName,
                        $v->getSku(),
                        (int) $price,
                        $track ? 1 : 0,
                        0,
                        $this->extractWeight($weightSource),
                        json_encode($optionValues, JSON_UNESCAPED_SLASHES),
                        1,
                        $vSort++,
                    ]);
                    if ($squareVarId !== '') {
                        $variationIds[] = $squareVarId;
                    }
                }
            }

            $this->rebuildFts();
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }

        $counts = $this->syncInventory($variationIds);

        $this->setMeta('last_catalog_sync', date('c'));
        return [
            'products' => (int) $this->db->query('SELECT COUNT(*) FROM products')->fetchColumn(),
            'variations' => (int) $this->db->query('SELECT COUNT(*) FROM variations')->fetchColumn(),
            'inventory_updated' => $counts,
        ];
    }

    /**
     * @param list<string> $squareVariationIds
     */
    public function syncInventory(array $squareVariationIds = []): int
    {
        if ($this->config->demoMode) {
            return 0;
        }
        if ($squareVariationIds === []) {
            $squareVariationIds = $this->db->query(
                "SELECT square_id FROM variations WHERE square_id IS NOT NULL AND square_id != ''"
            )->fetchAll(PDO::FETCH_COLUMN);
        }
        if ($squareVariationIds === []) {
            return 0;
        }

        $square = SquareClientFactory::make($this->config);
        $updated = 0;
        $chunks = array_chunk($squareVariationIds, 100);
        $stmt = $this->db->prepare('UPDATE variations SET quantity = ? WHERE square_id = ?');

        foreach ($chunks as $chunk) {
            $response = $square->inventory->batchGetCounts(new BatchGetInventoryCountsRequest([
                'catalogObjectIds' => $chunk,
                'locationIds' => [$this->config->squareLocationId],
            ]));
            foreach ($response as $count) {
                if (($count->getState() ?? '') !== 'IN_STOCK') {
                    continue;
                }
                $qty = (int) ((float) ($count->getQuantity() ?? '0'));
                $stmt->execute([$qty, $count->getCatalogObjectId()]);
                $updated++;
            }
        }

        $this->setMeta('last_inventory_sync', date('c'));
        return $updated;
    }

    /**
     * Seed from normalized listings (see SeedCatalog::load) — demo mode or pre-Square.
     *
     * @param list<array<string,mixed>> $listings
     */
    public function seedFromArray(array $listings): int
    {
        $this->db->beginTransaction();
        try {
            $this->db->exec('DELETE FROM product_images');
            $this->db->exec('DELETE FROM variations');
            $this->db->exec('DELETE FROM products');
            $this->db->exec('DELETE FROM categories');
            $this->clearFts();

            $categories = [];
            foreach ($listings as $p) {
                $categories[(string) ($p['category'] ?? 'Tackle')] = true;
            }

            $catStmt = $this->db->prepare(
                'INSERT INTO categories (id, square_id, name, slug, sort_order) VALUES (?,?,?,?,?)'
            );
            $catMap = [];
            $i = 0;
            foreach (array_keys($categories) as $name) {
                $id = Db::newId('cat_');
                $catStmt->execute([$id, 'local_' . Helpers::slugify($name), $name, Helpers::slugify($name), $i++]);
                $catMap[$name] = $id;
            }

            $prodStmt = $this->db->prepare(
                'INSERT INTO products (id, square_id, category_id, name, slug, description, description_html, image_url, local_image, options_json, is_active, sort_order, synced_at)
                 VALUES (?,?,?,?,?,?,?,?,?,?,1,?,datetime(\'now\'))'
            );
            $varStmt = $this->db->prepare(
                'INSERT INTO variations (id, product_id, square_id, name, sku, price_cents, track_inventory, quantity, weight_label, option_values_json, is_active, sort_order)
                 VALUES (?,?,?,?,?,?,1,?,?,?,1,?)'
            );
            $imgStmt = $this->db->prepare(
                'INSERT INTO product_images (product_id, square_id, url, local_path, sort_order) VALUES (?,?,?,?,?)'
            );

            $pSort = 0;
            foreach ($listings as $p) {
                $name = (string) ($p['name'] ?? 'Product');
                $key = Helpers::slugify((string) ($p['key'] ?? $name));
                $productId = 'prd_' . $key;
                $catId = $catMap[(string) ($p['category'] ?? 'Tackle')] ?? null;
                $localImage = $p['local_image'] ?? null;
                $imageUrl = $p['images'][0] ?? null;

                $prodStmt->execute([
                    $productId,
                    'local_' . $key,
                    $catId,
                    $name,
                    Helpers::slugify($name),
                    (string) ($p['description'] ?? ''),
                    (string) ($p['description'] ?? ''),
                    $imageUrl,
                    $localImage,
                    json_encode($p['options'] ?? [], JSON_UNESCAPED_SLASHES),
                    $pSort,
                ]);

                foreach (($p['images'] ?? []) as $ii => $url) {
                    if (is_string($url)) {
                        $imgStmt->execute([$productId, null, $url, $ii === 0 ? $localImage : null, $ii]);
                    }
                }

                foreach ($p['variations'] ?? [] as $vi => $v) {
                    $vName = (string) ($v['name'] ?? 'Default');
                    $optionValues = $v['option_values'] ?? [];
                    $weightSource = $optionValues['Size'] ?? $vName;
                    $varStmt->execute([
                        'var_' . $key . '_' . $vi,
                        $productId,
                        'local_var_' . $key . '_' . $vi,
                        $vName,
                        $v['sku'] ?? null,
                        (int) ($v['price_cents'] ?? 0),
                        (int) ($v['quantity'] ?? 25),
                        $this->extractWeight($weightSource) ?? $this->extractWeight($name),
                        json_encode($optionValues, JSON_UNESCAPED_SLASHES),
                        $vi,
                    ]);
                }
                $pSort++;
            }

            $this->rebuildFts();
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }

        $this->setMeta('last_catalog_sync', date('c'));
        $this->setMeta('seed_source', 'local');
        return $pSort;
    }

    public function rebuildFts(): void
    {
        $this->clearFts();
        $rows = $this->db->query(
            'SELECT p.id, p.name, p.description, COALESCE(c.name, \'\') AS category
             FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.is_active = 1'
        )->fetchAll();
        $ins = $this->db->prepare('INSERT INTO products_fts (product_id, name, description, category) VALUES (?,?,?,?)');
        foreach ($rows as $row) {
            $ins->execute([$row['id'], $row['name'], $row['description'], $row['category']]);
        }
    }

    private function clearFts(): void
    {
        $this->db->exec('DELETE FROM products_fts');
    }

    /** Repopulate FTS if it is empty but products exist (e.g. right after the 003 migration). */
    private function ensureFts(): void
    {
        $ftsCount = (int) $this->db->query('SELECT COUNT(*) FROM products_fts')->fetchColumn();
        if ($ftsCount === 0 && (int) $this->db->query('SELECT COUNT(*) FROM products')->fetchColumn() > 0) {
            $this->rebuildFts();
        }
    }

    /**
     * @param array{q?:string,category?:string,weight?:string,min_price?:int,max_price?:int,sort?:string,page?:int,per_page?:int} $filters
     * @return array{items: list<array>, total: int, page: int, per_page: int}
     */
    public function search(array $filters = []): array
    {
        $page = max(1, (int) ($filters['page'] ?? 1));
        $perPage = max(1, min(48, (int) ($filters['per_page'] ?? 24)));
        $offset = ($page - 1) * $perPage;

        $where = ['p.is_active = 1'];
        $params = [];

        if (!empty($filters['q'])) {
            $this->ensureFts();
            $where[] = 'p.id IN (SELECT product_id FROM products_fts WHERE products_fts MATCH ?)';
            $params[] = $this->ftsQuery((string) $filters['q']);
        }
        if (!empty($filters['category'])) {
            $where[] = 'c.slug = ?';
            $params[] = (string) $filters['category'];
        }
        if (!empty($filters['weight'])) {
            $where[] = 'EXISTS (SELECT 1 FROM variations v2 WHERE v2.product_id = p.id AND v2.weight_label = ? AND v2.is_active = 1)';
            $params[] = (string) $filters['weight'];
        }
        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $where[] = '(SELECT MIN(price_cents) FROM variations vx WHERE vx.product_id = p.id AND vx.is_active = 1) >= ?';
            $params[] = (int) $filters['min_price'];
        }
        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $where[] = '(SELECT MIN(price_cents) FROM variations vx WHERE vx.product_id = p.id AND vx.is_active = 1) <= ?';
            $params[] = (int) $filters['max_price'];
        }

        $sort = (string) ($filters['sort'] ?? 'featured');
        $order = match ($sort) {
            'price_asc' => 'min_price ASC, p.name ASC',
            'price_desc' => 'min_price DESC, p.name ASC',
            'name' => 'p.name ASC',
            'newest' => 'p.created_at DESC',
            default => 'p.sort_order ASC, p.name ASC',
        };

        $sqlWhere = implode(' AND ', $where);
        $countStmt = $this->db->prepare(
            "SELECT COUNT(*) FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE {$sqlWhere}"
        );
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                       (SELECT MIN(price_cents) FROM variations v WHERE v.product_id = p.id AND v.is_active = 1) AS min_price,
                       (SELECT MAX(price_cents) FROM variations v WHERE v.product_id = p.id AND v.is_active = 1) AS max_price,
                       (SELECT SUM(quantity) FROM variations v WHERE v.product_id = p.id AND v.is_active = 1) AS total_qty,
                       (SELECT ROUND(AVG(rating),1) FROM reviews r WHERE r.product_id = p.id AND r.is_approved = 1) AS avg_rating,
                       (SELECT COUNT(*) FROM reviews r WHERE r.product_id = p.id AND r.is_approved = 1) AS review_count
                FROM products p
                LEFT JOIN categories c ON c.id = p.category_id
                WHERE {$sqlWhere}
                ORDER BY {$order}
                LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return [
            'items' => $stmt->fetchAll(),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
        ];
    }

    public function getBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM products p LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.slug = ? AND p.is_active = 1 LIMIT 1'
        );
        $stmt->execute([$slug]);
        $product = $stmt->fetch();
        if (!$product) {
            return null;
        }
        $product['options'] = json_decode((string) ($product['options_json'] ?? '[]'), true) ?: [];
        $product['variations'] = $this->variationsFor($product['id']);
        $product['images'] = $this->imagesFor($product['id']);
        $product['reviews'] = $this->approvedReviews($product['id']);
        $product['avg_rating'] = $this->avgRating($product['id']);
        $product['review_count'] = count($product['reviews']);
        return $product;
    }

    public function getById(string $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        return $product ?: null;
    }

    public function getVariation(string $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT v.*, p.name AS product_name, p.slug AS product_slug, p.image_url, p.local_image
             FROM variations v JOIN products p ON p.id = v.product_id WHERE v.id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getVariationBySquareId(string $squareId): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT v.*, p.name AS product_name, p.slug AS product_slug
             FROM variations v JOIN products p ON p.id = v.product_id WHERE v.square_id = ? LIMIT 1'
        );
        $stmt->execute([$squareId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /** @return list<array> */
    public function variationsFor(string $productId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM variations WHERE product_id = ? AND is_active = 1 ORDER BY sort_order, name'
        );
        $stmt->execute([$productId]);
        $rows = $stmt->fetchAll();
        foreach ($rows as &$row) {
            $row['option_values'] = json_decode((string) ($row['option_values_json'] ?? '{}'), true) ?: [];
        }
        unset($row);
        return $rows;
    }

    /**
     * Seed listings keyed by lowercase name, for image fallbacks when Square has none.
     *
     * @return array<string, array{local_image: ?string, images: list<string>}>
     */
    private function localSeedIndex(): array
    {
        $clientRoot = dirname($this->config->sqlitePath, 2);
        try {
            $listings = SeedCatalog::load($clientRoot);
        } catch (\Throwable) {
            return [];
        }
        $index = [];
        foreach ($listings as $l) {
            $index[strtolower((string) $l['name'])] = [
                'local_image' => $l['local_image'] ?? null,
                'images' => $l['images'] ?? [],
            ];
        }
        return $index;
    }

    /** @return list<array> */
    public function imagesFor(string $productId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order'
        );
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    /** @return list<array> */
    public function categories(): array
    {
        return $this->db->query(
            'SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.is_active = 1) AS product_count
             FROM categories c ORDER BY c.sort_order, c.name'
        )->fetchAll();
    }

    /** @return list<string> */
    public function weightLabels(): array
    {
        return $this->db->query(
            "SELECT DISTINCT weight_label FROM variations WHERE weight_label IS NOT NULL AND weight_label != '' ORDER BY weight_label"
        )->fetchAll(PDO::FETCH_COLUMN);
    }

    /** @return list<array> */
    public function related(string $productId, ?string $categoryId, int $limit = 4): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, c.name AS category_name,
                    (SELECT MIN(price_cents) FROM variations v WHERE v.product_id = p.id) AS min_price
             FROM products p LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.is_active = 1 AND p.id != ? AND (? IS NULL OR p.category_id = ?)
             ORDER BY p.sort_order LIMIT ?'
        );
        $stmt->execute([$productId, $categoryId, $categoryId, $limit]);
        return $stmt->fetchAll();
    }

    /** @return list<array> */
    public function approvedReviews(string $productId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM reviews WHERE product_id = ? AND is_approved = 1 ORDER BY created_at DESC'
        );
        $stmt->execute([$productId]);
        return $stmt->fetchAll();
    }

    public function avgRating(string $productId): ?float
    {
        $stmt = $this->db->prepare(
            'SELECT AVG(rating) FROM reviews WHERE product_id = ? AND is_approved = 1'
        );
        $stmt->execute([$productId]);
        $v = $stmt->fetchColumn();
        return $v === null ? null : round((float) $v, 1);
    }

    public function setMeta(string $key, string $value): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO sync_meta (key, value, updated_at) VALUES (?,?,datetime(\'now\'))
             ON CONFLICT(key) DO UPDATE SET value = excluded.value, updated_at = excluded.updated_at'
        );
        $stmt->execute([$key, $value]);
    }

    public function getMeta(string $key): ?string
    {
        $stmt = $this->db->prepare('SELECT value FROM sync_meta WHERE key = ?');
        $stmt->execute([$key]);
        $v = $stmt->fetchColumn();
        return $v === false ? null : (string) $v;
    }

    private function extractWeight(string $name): ?string
    {
        if (preg_match('/(\d+(?:\.\d+)?|\d+\/\d+)\s*oz/i', $name, $m)) {
            return strtolower(trim($m[0]));
        }
        return null;
    }

    private function ftsQuery(string $q): string
    {
        $parts = preg_split('/\s+/', trim($q)) ?: [];
        $out = [];
        foreach ($parts as $part) {
            $part = preg_replace('/[^a-zA-Z0-9_-]/', '', $part) ?? '';
            if ($part !== '') {
                $out[] = $part . '*';
            }
        }
        return $out ? implode(' ', $out) : '""';
    }
}
