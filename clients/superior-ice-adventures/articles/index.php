<?php
/*
 * index.php - Public list of published articles.
 */
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/categories.php';
require __DIR__ . '/../includes/articles.php';
require __DIR__ . '/../includes/search.php';

$page_title = 'Articles | ' . $site_name;
$perPage = 20;
$categorySlug = isset($_GET['category']) && $_GET['category'] !== '' ? trim($_GET['category']) : null;
$query = normalize_search_query($_GET['q'] ?? '');
$page = max(1, (int) ($_GET['page'] ?? 1));
$isSearch = $query !== null;

$categories = [];
$featured = null;
$articles = [];
$totalPages = 1;
$searchResults = null;
$dbError = false;

try {
    $categories = get_categories('article');

    if ($isSearch) {
        $searchResults = search_articles_scope($query, $page, $perPage, $categorySlug);
        if ($page > $searchResults['total_pages']) {
            $page = $searchResults['total_pages'];
            $searchResults = search_articles_scope($query, $page, $perPage, $categorySlug);
        }
        $featured = null;
        $articles = [];
        $totalPages = $searchResults['total_pages'];
    } else {
        $featured = get_featured_article();
        $excludeId = $featured ? (int) $featured['id'] : null;
        $total = count_published_articles($categorySlug, $excludeId);
        $totalPages = max(1, (int) ceil($total / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }
        $articles = get_published_articles_page($page, $perPage, $categorySlug, $excludeId);
    }
} catch (Throwable $e) {
    $dbError = true;
}

function articles_page_url(?string $categorySlug, int $pageNum = 1, ?string $searchQuery = null): string
{
    $params = [];
    if ($searchQuery !== null && $searchQuery !== '') {
        $params['q'] = $searchQuery;
    }
    if ($categorySlug !== null && $categorySlug !== '') {
        $params['category'] = $categorySlug;
    }
    if ($pageNum > 1) {
        $params['page'] = $pageNum;
    }
    $queryString = $params ? '?' . http_build_query($params) : '';

    return url('articles/') . $queryString;
}

require __DIR__ . '/../includes/header.php';
?>

<section class="articles-page">
    <div class="container articles-page-header">
        <div class="eyebrow">Blog</div>
        <h1 class="heading-display text-display-lg">Articles</h1>
        <p style="margin-top: 1rem; color: var(--fg-muted); max-width: 36rem;">
            <?= htmlspecialchars($articles_intro) ?>
        </p>

        <div class="articles-toolbar">
            <form class="articles-search" method="get" action="<?= htmlspecialchars(url('articles/')) ?>" role="search">
                <?php if ($categorySlug): ?>
                    <input type="hidden" name="category" value="<?= htmlspecialchars($categorySlug) ?>">
                <?php endif; ?>
                <label class="visually-hidden" for="articles-q">Search articles</label>
                <input type="search" id="articles-q" name="q" placeholder="Search articles…" value="<?= htmlspecialchars($query ?? '') ?>">
                <button type="submit" class="btn-primary">Search</button>
            </form>
        </div>

        <?php if ($categories): ?>
            <div class="category-pills">
                <a class="category-pill<?= $categorySlug === null ? ' is-active' : '' ?>" href="<?= htmlspecialchars(articles_page_url(null, 1, $query)) ?>">All</a>
                <?php foreach ($categories as $cat): ?>
                    <a class="category-pill<?= $categorySlug === $cat['slug'] ? ' is-active' : '' ?>"
                       href="<?= htmlspecialchars(articles_page_url($cat['slug'], 1, $query)) ?>">
                        <?= htmlspecialchars($cat['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="container">
        <?php if ($dbError): ?>
            <div class="empty-state">
                <p>Articles will appear here once the database is set up. See <code>DEPLOY.md</code> for setup steps.</p>
            </div>
        <?php elseif ($isSearch && $searchResults): ?>
            <?php if (!$searchResults['has_results']): ?>
                <div class="empty-state"><p>No results for “<?= htmlspecialchars($query) ?>”.</p></div>
            <?php else: ?>
                <div class="article-list">
                    <?php foreach ($searchResults['articles'] as $result): ?>
                        <a class="article-row" href="<?= htmlspecialchars(url(ltrim($result['url'], '/'))) ?>">
                            <div class="article-row-img" style="background-image: url('<?= htmlspecialchars(url(ltrim($result['thumbnail'] ?? DEFAULT_ARTICLE_THUMBNAIL, '/'))) ?>')"></div>
                            <div>
                                <div class="article-meta"><?= htmlspecialchars($result['meta']) ?></div>
                                <h3><?= htmlspecialchars($result['title']) ?></h3>
                                <p><?= $result['description'] ?></p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <?php if ($featured && $categorySlug === null): ?>
                <a class="featured-article" href="<?= htmlspecialchars(url(ltrim($featured['url'], '/'))) ?>">
                    <div class="featured-article-img" style="background-image: url('<?= htmlspecialchars(url(ltrim($featured['thumbnail'], '/'))) ?>')"></div>
                    <div>
                        <div class="eyebrow">Featured</div>
                        <h2 class="heading-display text-display-md" style="margin-top: 0.5rem;"><?= htmlspecialchars($featured['title']) ?></h2>
                        <p style="margin-top: 0.75rem; color: var(--fg-muted);"><?= htmlspecialchars($featured['blurb']) ?></p>
                    </div>
                </a>
            <?php endif; ?>

            <?php if ($articles): ?>
                <div class="article-list">
                    <?php foreach ($articles as $article): ?>
                        <a class="article-row" href="<?= htmlspecialchars(url(ltrim($article['url'], '/'))) ?>">
                            <div class="article-row-img" style="background-image: url('<?= htmlspecialchars(url(ltrim($article['thumbnail'], '/'))) ?>')"></div>
                            <div>
                                <div class="article-meta">
                                    <?php if (!empty($article['category_name'])): ?>
                                        <?= htmlspecialchars($article['category_name']) ?>
                                        <?php if (!empty($article['published_at'])): ?> · <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if (!empty($article['published_at'])): ?>
                                        <?= htmlspecialchars(date('M j, Y', strtotime($article['published_at']))) ?>
                                    <?php endif; ?>
                                </div>
                                <h3><?= htmlspecialchars($article['title']) ?></h3>
                                <p><?= htmlspecialchars($article['blurb']) ?></p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php elseif (!$featured): ?>
                <div class="empty-state"><p>No published articles yet. Check back soon, or visit <a href="<?= htmlspecialchars(url('admin/')) ?>">Admin</a> to write one.</p></div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($totalPages > 1): ?>
            <nav class="pagination" aria-label="Pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i === $page): ?>
                        <span class="is-current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="<?= htmlspecialchars(articles_page_url($categorySlug, $i, $query)) ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
            </nav>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
