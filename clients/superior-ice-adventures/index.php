<?php
/*
 * index.php - Home page: hero carousel, value props, services, story, articles, CTA.
 */
require __DIR__ . '/includes/config.php';

$latest = [];
try {
    require_once __DIR__ . '/includes/articles.php';
    $latest = get_published_articles(3);
} catch (Throwable $e) {
    $latest = $latest_articles_fallback;
}

$page_title = $site_name . ($site_tagline !== '' ? ' | ' . $site_tagline : '');
require __DIR__ . '/includes/header.php';

function sia_icon(string $name): string
{
    $icons = [
        'map-pin' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>',
        'fish' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6.5 12c0-2.5 4-7 9.5-7 2 0 4 .5 5.5 1.5-1 2-1.5 4-1.5 5.5s.5 3.5 1.5 5.5C20 18.5 18 19 16 19c-5.5 0-9.5-4.5-9.5-7z"/><path d="M6.5 12H2"/><circle cx="16" cy="10.5" r="1" fill="currentColor"/></svg>',
        'compass' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>',
        'phone' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
    ];
    return $icons[$name] ?? $icons['compass'];
}
?>

<section class="hero-carousel" aria-label="Featured">
    <div class="hero-slides">
        <?php foreach ($hero_slides as $i => $slide): ?>
            <div class="hero-slide<?= $i === 0 ? ' is-active' : '' ?>" data-index="<?= $i ?>">
                <div class="hero-slide-bg" style="background-image: url('<?= htmlspecialchars(url(ltrim($slide['image'], '/'))) ?>');<?= !empty($slide['image_position']) ? ' background-position: ' . htmlspecialchars($slide['image_position']) . ';' : '' ?>"></div>
                <div class="hero-slide-overlay"></div>
                <div class="hero-slide-content">
                    <?php
                    $slide_eyebrow = trim((string) ($slide['eyebrow'] ?? ''));
                    $hero_eyebrow = $slide_eyebrow !== '' ? $slide_eyebrow : $location_eyebrow;
                    ?>
                    <?php if ($hero_eyebrow !== ''): ?>
                        <div class="eyebrow"><?= htmlspecialchars($hero_eyebrow) ?></div>
                    <?php endif; ?>
                    <h1 class="heading-display text-display-xl hero-title"><?= htmlspecialchars($slide['title']) ?></h1>
                    <p class="hero-caption"><?= htmlspecialchars($slide['caption']) ?></p>
                    <div class="hero-actions">
                        <a class="btn-primary btn-call-plain" href="<?= htmlspecialchars($phone_href) ?>">Call Now</a>
                        <a class="btn-outline-light" href="<?= htmlspecialchars(url('contact/')) ?>">Contact</a>
                        <?php
                        $slideHref = $slide['href'] ?? '#services';
                        $slideHrefOut = (str_starts_with($slideHref, '#') || preg_match('#^https?://#i', $slideHref))
                            ? $slideHref
                            : url(ltrim($slideHref, '/'));
                        ?>
                        <a class="btn-read-more" href="<?= htmlspecialchars($slideHrefOut) ?>">Read more</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php if (count($hero_slides) > 1): ?>
        <div class="hero-dots" role="tablist" aria-label="Carousel slides">
            <?php foreach ($hero_slides as $i => $slide): ?>
                <button type="button" class="hero-dot<?= $i === 0 ? ' is-active' : '' ?>" aria-label="Go to slide <?= $i + 1 ?>"></button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="section section-bone">
    <div class="container-wide">
        <div class="value-grid">
            <?php foreach ($value_props as $prop): ?>
                <div>
                    <div class="value-icon"><?= sia_icon($prop['icon']) ?></div>
                    <h3><?= htmlspecialchars($prop['title']) ?></h3>
                    <p><?= htmlspecialchars($prop['copy']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-default" id="services">
    <div class="container-wide">
        <div class="section-header">
            <div>
                <div class="eyebrow">What we offer</div>
                <h2 class="heading-display text-display-lg">Services</h2>
            </div>
        </div>
        <div class="card-grid cols-4">
            <?php foreach ($services as $svc): ?>
                <a class="service-card" href="<?= htmlspecialchars(url($svc['slug'] . '/')) ?>">
                    <div class="service-card-img" style="background-image: url('<?= htmlspecialchars(url(ltrim($svc['thumb_image'], '/'))) ?>')"></div>
                    <div class="service-card-body">
                        <h3><?= htmlspecialchars($svc['nav_label']) ?></h3>
                        <p><?= htmlspecialchars($svc['card_blurb']) ?></p>
                        <span class="service-card-link">Learn more →</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-dark section-lg grain">
    <div class="container-wide story-grid">
        <div class="story-copy">
            <div class="eyebrow text-on-dark-muted"><?= htmlspecialchars($home_story['eyebrow']) ?></div>
            <h2 class="heading-display text-display-lg text-on-dark"><?= htmlspecialchars($home_story['headline']) ?></h2>
            <?php foreach ($home_story['paragraphs'] as $para): ?>
                <p><?= htmlspecialchars($para) ?></p>
            <?php endforeach; ?>
            <?php if (($home_story['guide_name'] ?? '') !== ''): ?>
                <div class="story-guide">
                    <strong><?= htmlspecialchars($home_story['guide_name']) ?></strong>
                    <span><?= htmlspecialchars($home_story['guide_role']) ?></span>
                </div>
            <?php endif; ?>
            <div class="hero-actions">
                <a class="btn-outline-light" href="<?= htmlspecialchars(url('about/')) ?>">About us</a>
            </div>
        </div>
        <div class="story-photos">
            <?php foreach ($home_story['images'] as $i => $img): ?>
                <div class="story-photo<?= $i === 0 ? ' wide' : '' ?>" style="background-image: url('<?= htmlspecialchars(url(ltrim($img, '/'))) ?>')"></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-default">
    <div class="container-wide">
        <div class="section-header">
            <div>
                <div class="eyebrow">From the blog</div>
                <h2 class="heading-display text-display-lg">Latest articles</h2>
            </div>
            <a class="link-arrow" href="<?= htmlspecialchars(url('articles/')) ?>">View all articles →</a>
        </div>
        <?php if ($latest): ?>
            <div class="card-grid cols-3">
                <?php foreach ($latest as $article): ?>
                    <a class="article-card" href="<?= htmlspecialchars(url(ltrim($article['url'], '/'))) ?>">
                        <div class="article-card-img" style="background-image: url('<?= htmlspecialchars(url(ltrim($article['thumbnail'], '/'))) ?>')"></div>
                        <h3><?= htmlspecialchars($article['title']) ?></h3>
                        <p><?= htmlspecialchars($article['blurb']) ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>No articles published yet. <a href="<?= htmlspecialchars(url('articles/')) ?>">Visit the articles page</a>.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/partials/final-cta.php'; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
