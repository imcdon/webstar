<?php
/*
 * category-edit.php - Create or edit an article category.
 */
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/categories.php';

require_admin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$category = $id ? get_category_by_id($id) : null;
$error = '';
$type = 'article';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $imagePath = trim($_POST['image_path'] ?? '');
    $sortOrder = (int) ($_POST['sort_order'] ?? 0);

    if ($name === '') {
        $error = 'Name is required.';
    } else {
        if ($slug === '') {
            $slug = slugify_category($name);
        }

        if ($slug === '') {
            $error = 'Slug is required.';
        } elseif ($imagePath === '') {
            $error = 'Image path is required.';
        } else {
            try {
                save_category([
                    'type'        => $type,
                    'slug'        => $slug,
                    'name'        => $name,
                    'image_path'  => $imagePath,
                    'sort_order'  => $sortOrder,
                    'is_featured' => 0,
                ], $id ?: null);

                header('Location: ' . url('admin/categories.php') . '?saved=1');
                exit;
            } catch (PDOException $e) {
                if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
                    $error = 'A category with that slug already exists.';
                } else {
                    throw $e;
                }
            }
        }
    }
}

$form = $category ?: [
    'type'        => $type,
    'name'        => '',
    'slug'        => '',
    'image_path'  => '',
    'sort_order'  => 0,
];

$page_title = ($id ? 'Edit' : 'New') . ' Category | ' . $site_name;
$body_class = 'page-admin';
require __DIR__ . '/../includes/header.php';
?>

<section class="container admin-page">
    <h1 class="section-title"><?= $id ? 'Edit' : 'New' ?> Category</h1>
    <p class="section-intro"><a href="<?= htmlspecialchars(url('admin/categories.php')) ?>">&larr; Back to categories</a></p>

    <?php if ($error): ?>
        <p class="form-error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form class="admin-form" method="post" action="" style="max-width: 32rem;">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($form['name']) ?>" required>

        <label for="slug">Slug (URL)</label>
        <input type="text" id="slug" name="slug" value="<?= htmlspecialchars($form['slug']) ?>" placeholder="auto-from-name">

        <label for="image_path">Image path</label>
        <input type="text" id="image_path" name="image_path" value="<?= htmlspecialchars($form['image_path']) ?>" placeholder="/assets/img/categories/example.webp" required>

        <label for="sort_order">Sort order</label>
        <input type="number" id="sort_order" name="sort_order" value="<?= (int) $form['sort_order'] ?>" min="0">

        <div class="form-actions">
            <button type="submit" class="btn-primary">Save category</button>
            <a class="btn-secondary" href="<?= htmlspecialchars(url('admin/categories.php')) ?>">Cancel</a>
        </div>
    </form>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
