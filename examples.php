<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
$currentPage = 'examples';
$pageTitle = 'Examples — Webstar Business Services';
$pageDescription = 'See website and marketing examples from previous Webstar Business Services clients.';
$canonicalPath = 'examples.php';
include __DIR__ . '/library/layout-start.php';
?>
<section class="home-section home-section--night">
    <div class="home-section__inner">
        <h1 class="home-section__heading">Examples</h1>
        <p class="home-section__textbox examples-lead">See website and marketing examples from our previous clients</p>
        <div class="examples-grid">
            <?php foreach (webstar_examples() as $example) : ?>
                <a
                    class="example-panel"
                    href="<?php echo webstar_h((string) $example['url']); ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <div class="example-panel__media">
                        <img
                            src="<?php echo webstar_h((string) $example['thumbnail']); ?>"
                            alt="<?php echo webstar_h((string) $example['title']); ?> homepage"
                            width="640"
                            height="400"
                            loading="lazy"
                        >
                    </div>
                    <div class="example-panel__body">
                        <p class="example-panel__type"><?php echo webstar_h((string) $example['type']); ?></p>
                        <h2 class="example-panel__title"><?php echo webstar_h((string) $example['title']); ?></h2>
                        <p class="example-panel__desc"><?php echo webstar_h((string) $example['description']); ?></p>
                        <span class="example-panel__link">Visit site</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php include __DIR__ . '/library/layout-end.php'; ?>
