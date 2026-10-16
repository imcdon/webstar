<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
require __DIR__ . '/library/article-helpers.php';

$config = webstar_config();
$articles = webstar_articles_sorted();
$feedUrl = webstar_production_site_url('resources/feed/');
$hubUrl = webstar_production_site_url('resources/');
$siteTitle = (string) $config['business_name'];
$buildDate = gmdate('D, d M Y H:i:s') . ' GMT';

header('Content-Type: application/rss+xml; charset=UTF-8');
header('X-Robots-Tag: noindex');

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title><?php echo webstar_h($siteTitle); ?> — Guides &amp; Resources</title>
        <link><?php echo webstar_h($hubUrl); ?></link>
        <description>Practical website and Google guides for Michigan small businesses by Webstar Business Services.</description>
        <language>en-us</language>
        <lastBuildDate><?php echo webstar_h($buildDate); ?></lastBuildDate>
        <atom:link href="<?php echo webstar_h($feedUrl); ?>" rel="self" type="application/rss+xml"/>
        <?php foreach ($articles as $article) :
            $slug = (string) ($article['slug'] ?? '');
            if ($slug === '') {
                continue;
            }
            $itemUrl = webstar_production_site_url('resources/' . rawurlencode($slug) . '/');
            $pub = (string) ($article['published'] ?? '');
            $pubRfc = $pub !== '' ? gmdate('D, d M Y H:i:s', strtotime($pub . ' 12:00:00 UTC')) . ' GMT' : $buildDate;
            $desc = (string) ($article['excerpt'] ?? $article['meta_description'] ?? '');
            ?>
        <item>
            <title><?php echo webstar_h((string) ($article['title'] ?? $slug)); ?></title>
            <link><?php echo webstar_h($itemUrl); ?></link>
            <guid isPermaLink="true"><?php echo webstar_h($itemUrl); ?></guid>
            <pubDate><?php echo webstar_h($pubRfc); ?></pubDate>
            <description><?php echo webstar_h($desc); ?></description>
        </item>
        <?php endforeach; ?>
    </channel>
</rss>
