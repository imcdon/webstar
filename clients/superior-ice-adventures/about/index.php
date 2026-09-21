<?php
/*
 * about/index.php
 */
require __DIR__ . '/../includes/config.php';

$page_title = 'About | ' . $site_name;
$page_description = $about['hero_intro'];
require __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container-wide">
        <div class="eyebrow text-on-dark-muted"><?= htmlspecialchars($about['hero_eyebrow']) ?></div>
        <h1 class="heading-display text-display-xl text-on-dark"><?= htmlspecialchars($about['hero_title']) ?></h1>
        <p class="page-hero-intro"><?= htmlspecialchars($about['hero_intro']) ?></p>
    </div>
</section>

<section class="section section-default">
    <div class="container-wide about-layout">
        <div class="prose-content">
            <h2 class="heading-display text-display-md"><?= htmlspecialchars($about['story_title']) ?></h2>
            <?php foreach ($about['story_paragraphs'] as $para): ?>
                <p><?= htmlspecialchars($para) ?></p>
            <?php endforeach; ?>
        </div>
        <div class="about-photos">
            <?php foreach ($about['images'] as $i => $img): ?>
                <div class="about-photo<?= $i === 0 ? ' wide' : '' ?>" style="background-image: url('<?= htmlspecialchars(url(ltrim($img, '/'))) ?>')"></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-bone">
    <div class="container-wide">
        <div class="guide-block">
            <div class="guide-photo" style="background-image: url('<?= htmlspecialchars(url(ltrim($about['guide']['image'], '/'))) ?>')"></div>
            <div class="prose-content">
                <div class="eyebrow">Our Guide</div>
                <h2 class="heading-display text-display-md" style="margin-top: 0.5rem;"><?= htmlspecialchars($about['guide']['name']) ?></h2>
                <p style="color: var(--accent); font-weight: 600; margin: 0.5rem 0 1rem;"><?= htmlspecialchars($about['guide']['title']) ?></p>
                <?php foreach ($about['guide']['bio'] as $para): ?>
                    <p><?= htmlspecialchars($para) ?></p>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($about['testimonials'])): ?>
<section class="section section-default">
    <div class="container-wide">
        <div class="section-header">
            <div>
                <div class="eyebrow">Testimonials</div>
                <h2 class="heading-display text-display-lg">What anglers say</h2>
            </div>
        </div>
        <div class="testimonials-grid">
            <?php foreach ($about['testimonials'] as $t): ?>
                <div class="testimonial-card">
                    <blockquote>“<?= htmlspecialchars($t['quote']) ?>”</blockquote>
                    <?php if (!empty($t['name'])): ?>
                        <cite>— <?= htmlspecialchars($t['name']) ?><?= !empty($t['location']) ? ', ' . htmlspecialchars($t['location']) : '' ?></cite>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section section-dark cta-strip">
    <div class="container-narrow text-center">
        <div class="eyebrow text-on-dark-muted">Get on the ice</div>
        <h2 class="heading-display text-display-md text-on-dark">Call to plan your trip</h2>
        <div class="hero-actions" style="justify-content: center; margin-top: 1.75rem;">
            <a class="btn-primary" href="<?= htmlspecialchars($phone_href) ?>">Call Now</a>
            <a class="btn-outline-light" href="<?= htmlspecialchars(url('contact/')) ?>">Contact</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
