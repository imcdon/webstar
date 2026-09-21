<?php
/*
 * article.php - Single published article view (Markdown body).
 */
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/articles.php';
require __DIR__ . '/../includes/markdown.php';

$slug = $_GET['slug'] ?? '';
$article = null;

try {
    $article = $slug ? get_article_by_slug($slug) : null;
} catch (Throwable $e) {
    $article = null;
}

if (!$article) {
    http_response_code(404);
    $page_title = 'Article not found | ' . $site_name;
    require __DIR__ . '/../includes/header.php';
    echo '<section class="container article-page"><h1 class="heading-display text-display-md">Article not found</h1><p class="article-back"><a href="' . htmlspecialchars(url('articles/')) . '">&larr; Back to articles</a></p></section>';
    require __DIR__ . '/../includes/footer.php';
    exit;
}

$page_title = $article['title'] . ' | ' . $site_name;
$page_description = $article['blurb'];
require __DIR__ . '/../includes/header.php';
?>

<article class="container article-page">
    <p class="article-back article-back-top"><a href="<?= htmlspecialchars(url('articles/')) ?>">&larr; All articles</a></p>
    <header class="article-header">
        <?php if (!empty($article['category_name'])): ?>
            <div class="eyebrow"><?= htmlspecialchars($article['category_name']) ?></div>
        <?php endif; ?>
        <h1 class="heading-display text-display-lg" style="margin-top: 0.5rem;"><?= htmlspecialchars($article['title']) ?></h1>
        <p class="article-meta" style="margin-top: 0.75rem;">
            <?php if ($article['published_at']): ?>
                <?= htmlspecialchars(date('F j, Y', strtotime($article['published_at']))) ?>
            <?php endif; ?>
        </p>
    </header>
    <div class="article-body">
        <?= render_markdown($article['body']) ?>
    </div>
    <p class="article-back"><a href="<?= htmlspecialchars(url('articles/')) ?>">&larr; All articles</a></p>
</article>

<?php require __DIR__ . '/../includes/footer.php'; ?>
