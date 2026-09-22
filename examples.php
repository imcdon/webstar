<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
$config = webstar_config();
$currentPage = 'examples';
$pageTitle = 'Website Examples & Client Work — Webstar Business Services';
$pageDescription = 'See real websites built by Webstar Business Services for guide services and small businesses. Metro Detroit web design with clear packages and intake.';
$canonicalPath = 'examples/';
include __DIR__ . '/library/layout-start.php';
?>
<section class="home-section home-section--night">
    <div class="home-section__inner">
        <h1 class="home-section__heading">Examples &amp; client work</h1>
        <p class="home-section__textbox examples-lead">Live sites and projects from Webstar clients. Want something similar? Choose the matching package and submit the intake form.</p>
        <div class="examples-grid">
            <?php foreach (webstar_examples() as $example) :
                $packageSlug = (string) ($example['package_slug'] ?? 'simple-website');
                $package = webstar_package_by_slug($packageSlug);
                $packageLabel = $package !== null ? (string) $package['title'] : 'View packages';
                ?>
                <article class="example-panel">
                    <a class="example-panel__media" href="<?php echo webstar_h((string) $example['url']); ?>" target="_blank" rel="noopener noreferrer">
                        <img
                            src="<?php echo webstar_h((string) $example['thumbnail']); ?>"
                            alt="<?php echo webstar_h((string) $example['title']); ?> website screenshot"
                            width="640"
                            height="400"
                            loading="lazy"
                        >
                    </a>
                    <div class="example-panel__body">
                        <p class="example-panel__type"><?php echo webstar_h((string) $example['type']); ?> · <?php echo webstar_h((string) ($example['industry'] ?? '')); ?></p>
                        <h2 class="example-panel__title"><?php echo webstar_h((string) $example['title']); ?></h2>
                        <?php if (($example['location'] ?? '') !== '') : ?>
                            <p class="example-panel__meta"><?php echo webstar_h((string) $example['location']); ?></p>
                        <?php endif; ?>
                        <p class="example-panel__desc"><?php echo webstar_h((string) ($example['case_summary'] ?? $example['description'])); ?></p>
                        <div class="example-panel__actions">
                            <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h(webstar_package_url($packageSlug)); ?>">Start similar project</a>
                            <a class="example-panel__link" href="<?php echo webstar_h((string) $example['url']); ?>" target="_blank" rel="noopener noreferrer">Visit live site</a>
                        </div>
                        <?php if ($package !== null) : ?>
                            <p class="example-panel__package-hint">Typical fit: <?php echo webstar_h($packageLabel); ?></p>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<div class="cta-band">
    <p>Ready to build? Packages start at $50 for marketing and $200 for a three-page website.</p>
    <div class="hero__actions">
        <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h(webstar_url('packages.php')); ?>">View all packages</a>
        <a class="home-section__btn home-section__btn--secondary" href="<?php echo webstar_h(webstar_url('contact.php')); ?>">Ask a question first</a>
    </div>
</div>
<?php include __DIR__ . '/library/layout-end.php'; ?>
