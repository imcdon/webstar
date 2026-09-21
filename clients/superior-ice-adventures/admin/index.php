<?php
/*
 * index.php - Admin dashboard (single admin).
 */
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/articles.php';

$user = require_admin();
$articles = get_all_articles();

$page_title = 'Admin | ' . $site_name;
$body_class = 'page-admin';
require __DIR__ . '/../includes/header.php';
?>

<section class="container admin-page">
    <h1 class="section-title">Admin</h1>
    <p class="section-intro">Logged in as <?= htmlspecialchars($user['username']) ?>.</p>

    <?php if (isset($_GET['deleted'])): ?>
        <p class="form-success">Article deleted.</p>
    <?php endif; ?>

    <div class="admin-actions">
        <a class="btn-primary" href="<?= htmlspecialchars(url('admin/edit.php')) ?>">New article</a>
        <a class="btn-secondary" href="<?= htmlspecialchars(url('admin/categories.php')) ?>">Manage categories</a>
        <a class="btn-secondary" href="<?= htmlspecialchars(url('admin/logout.php')) ?>">Log out</a>
    </div>

    <h2 class="section-title">Articles</h2>
    <?php if ($articles): ?>
        <ul class="admin-list">
            <?php foreach ($articles as $article): ?>
                <li>
                    <a href="<?= htmlspecialchars(url('admin/edit.php')) ?>?id=<?= (int) $article['id'] ?>"><?= htmlspecialchars($article['title']) ?></a>
                    <span class="status-badge status-<?= htmlspecialchars($article['status']) ?>"><?= htmlspecialchars($article['status']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No articles yet.</p>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
