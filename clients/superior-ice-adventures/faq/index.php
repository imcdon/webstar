<?php
/*
 * faq/index.php
 */
require __DIR__ . '/../includes/config.php';

$page_title = 'FAQ | ' . $site_name;
require __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container-wide">
        <div class="eyebrow text-on-dark-muted">Help</div>
        <h1 class="heading-display text-display-xl text-on-dark">Frequently asked questions</h1>
        <p class="page-hero-intro"><?= htmlspecialchars($faq_intro) ?></p>
    </div>
</section>

<section class="section section-default">
    <div class="container-narrow">
        <?php foreach ($faq_categories as $cat): ?>
            <div class="faq-category">
                <h2><?= htmlspecialchars($cat['name']) ?></h2>
                <?php foreach ($cat['items'] as $item): ?>
                    <details class="faq-item">
                        <summary><?= htmlspecialchars($item['q']) ?></summary>
                        <div class="faq-answer"><?= htmlspecialchars($item['a']) ?></div>
                    </details>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="section section-dark cta-strip">
    <div class="container-narrow text-center">
        <div class="eyebrow text-on-dark-muted">Still have questions?</div>
        <h2 class="heading-display text-display-md text-on-dark">Give us a call</h2>
        <div class="hero-actions" style="justify-content: center; margin-top: 1.75rem;">
            <a class="btn-primary" href="<?= htmlspecialchars($phone_href) ?>">Call Now</a>
            <a class="btn-outline-light" href="<?= htmlspecialchars(url('contact/')) ?>">Contact form</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
