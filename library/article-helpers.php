<?php

declare(strict_types=1);

/**
 * JSON-LD graphs for a resources blog article (Article + BreadcrumbList).
 *
 * @return list<array<string, mixed>>
 */
function webstar_article_json_ld(array $article, string $articleUrl, string $title): array
{
    $config = webstar_config();
    $published = (string) ($article['published'] ?? '');
    $updated = (string) ($article['updated'] ?? $published);

    $breadcrumbs = [
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            [
                '@type' => 'ListItem',
                'position' => 1,
                'name' => 'Home',
                'item' => webstar_production_site_url(''),
            ],
            [
                '@type' => 'ListItem',
                'position' => 2,
                'name' => 'Resources',
                'item' => webstar_production_site_url('resources/'),
            ],
            [
                '@type' => 'ListItem',
                'position' => 3,
                'name' => $title,
                'item' => $articleUrl,
            ],
        ],
    ];

    $articleNode = [
        '@type' => 'Article',
        'headline' => $title,
        'description' => (string) ($article['meta_description'] ?? ''),
        'url' => $articleUrl,
        'datePublished' => $published,
        'dateModified' => $updated,
        'author' => [
            '@type' => 'Person',
            'name' => 'Ian McDonnell',
            'url' => webstar_production_site_url('about/'),
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => (string) ($config['legal_name'] ?? $config['business_name']),
            'url' => webstar_production_site_url(''),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => webstar_production_site_url('assets/images/star-icon-gold.png'),
            ],
        ],
        'mainEntityOfPage' => [
            '@type' => 'WebPage',
            '@id' => $articleUrl,
        ],
    ];

    $keyword = trim((string) ($article['primary_keyword'] ?? ''));
    if ($keyword !== '') {
        $articleNode['keywords'] = $keyword;
    }

    return [$breadcrumbs, $articleNode];
}

/**
 * Articles sorted newest published first.
 *
 * @return list<array<string, mixed>>
 */
function webstar_articles_sorted(): array
{
    $articles = webstar_articles();
    usort($articles, static function (array $a, array $b): int {
        return strcmp((string) ($b['published'] ?? ''), (string) ($a['published'] ?? ''));
    });

    return $articles;
}

/**
 * Path to the HTML body partial for an article slug.
 */
function webstar_article_body_path(string $slug): string
{
    $safe = preg_replace('/[^a-z0-9-]/', '', $slug) ?? '';

    return __DIR__ . '/articles/' . $safe . '.php';
}
