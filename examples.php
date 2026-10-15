<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
$currentPage = 'examples';
$pageTitle = 'Website Examples & Client Work — Webstar Business Services';
$pageDescription = 'Live websites built by Webstar Business Services. Metro Detroit web design with clear packages and intake.';
$canonicalPath = 'examples/';
include __DIR__ . '/library/layout-start.php';
?>
<section class="home-section home-section--night">
    <div class="home-section__inner">
        <h1 class="home-section__heading">Examples &amp; client work</h1>
        <p class="home-section__textbox examples-lead">Live client sites you can visit today. More examples are added when projects launch on their own domains.</p>
        <div class="examples-grid">
            <?php foreach (webstar_examples() as $example) :
                $packageSlug = (string) ($example['package_slug'] ?? 'simple-website');
                $title = (string) $example['title'];
                $siteUrl = (string) $example['url'];
                ?>
                <article class="example-panel">
                    <?php
                    $thumb = trim((string) ($example['thumbnail'] ?? ''));
                    if ($thumb !== '') :
                        ?>
                        <a class="example-panel__media" href="<?php echo webstar_h($siteUrl); ?>" target="_blank" rel="noopener noreferrer">
                            <img
                                src="<?php echo webstar_h(webstar_url($thumb)); ?>"
                                alt="<?php echo webstar_h($title); ?> website screenshot"
                                width="640"
                                height="400"
                                loading="lazy"
                            >
                        </a>
                    <?php else : ?>
                        <a class="example-panel__media example-panel__media--placeholder" href="<?php echo webstar_h($siteUrl); ?>" target="_blank" rel="noopener noreferrer">
                            <span><?php echo webstar_h($title); ?></span>
                        </a>
                    <?php endif; ?>
                    <div class="example-panel__body example-panel__body--compact">
                        <h2 class="example-panel__title"><?php echo webstar_h($title); ?></h2>
                        <div class="example-panel__actions">
                            <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h($siteUrl); ?>" target="_blank" rel="noopener noreferrer">Visit live site</a>
                            <a class="home-section__btn home-section__btn--secondary" href="<?php echo webstar_h(webstar_package_url($packageSlug)); ?>">Start similar project</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<div class="cta-band">
    <p>Ready to build? Websites start at $350 one-time with Google Business Profile included, or $150/mo with Website as a Service.</p>
    <div class="hero__actions">
        <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h(webstar_url('packages.php')); ?>">View all packages</a>
        <a class="home-section__btn home-section__btn--secondary" href="<?php echo webstar_h(webstar_url('contact.php')); ?>">Ask a question first</a>
    </div>
</div>
<?php include __DIR__ . '/library/layout-end.php'; ?>
