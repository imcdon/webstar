<?php
declare(strict_types=1);
require __DIR__ . '/includes/config.php';

$page_title = 'Holiday Lighting Installation in Metro Detroit | Metro Detroit Holiday Lighting';
$page_description = 'Professional Christmas and holiday lighting for Metro Detroit homes, businesses, and HOAs. Owner-operated by Steven with 20 years of experience. Call (248) 525-4715.';
$canonical_path = '/';
$schema_home = true;
$schema_faq = true;
$preload_image = $hero_image !== '' ? $hero_image : null;
$body_class = 'page-home';

require __DIR__ . '/includes/header.php';
?>
<section class="hero<?= $hero_image !== '' ? ' hero--photo' : '' ?>">
    <?php if ($hero_image !== ''): ?>
        <div class="hero__media" aria-hidden="true">
            <img src="<?= htmlspecialchars($hero_image) ?>" alt="" width="1600" height="900" fetchpriority="high">
        </div>
        <div class="hero__shade" aria-hidden="true"></div>
    <?php endif; ?>
    <div class="hero__content">
        <h1>Full Service Holiday Lighting For Metro Detroit</h1>
        <p class="hero__lead">Custom lighting layouts for Residential, Commercial, and Home Owners Associations.</p>
        <div class="hero__actions">
            <a class="btn btn-primary" href="<?= htmlspecialchars($phone_href) ?>">
                <svg class="btn__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.81.36 1.6.68 2.35a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.75.32 1.54.55 2.35.68A2 2 0 0 1 22 16.92z"/></svg>
                <?= htmlspecialchars($phone) ?>
            </a>
            <a class="btn btn-ghost" href="<?= htmlspecialchars(url('contact/')) ?>">
                <svg class="btn__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                Request a quote
            </a>
            <?php if ($gallery_images !== []): ?>
                <a class="btn btn-ghost" href="<?= htmlspecialchars(url('gallery/')) ?>">See the work</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section section--tight" id="who-we-serve">
    <div class="container">
        <div class="section__head">
            <h2>Lighting For Anything - Anywhere</h2>
        </div>
        <div class="card-grid audience-grid">
            <?php foreach ($audiences as $audience): ?>
                <article class="audience-card<?= empty($audience['has_image']) ? ' audience-card--placeholder' : '' ?>">
                    <div class="audience-card__media" aria-hidden="true">
                        <?php if (!empty($audience['has_image'])): ?>
                            <img src="<?= htmlspecialchars(url($audience['image'])) ?>" alt="" width="800" height="600" loading="lazy" decoding="async">
                        <?php endif; ?>
                    </div>
                    <div class="audience-card__shade" aria-hidden="true"></div>
                    <div class="audience-card__content">
                        <h3><?= htmlspecialchars($audience['title']) ?></h3>
                        <p><?= htmlspecialchars($audience['body']) ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section" id="process">
    <div class="container">
        <div class="section__head">
            <h2>Full-season holiday lighting.</h2>
            <p class="section__lead">From the first photo to spring take-down, the process stays simple.</p>
        </div>
        <div class="process">
            <?php foreach ($process_steps as $i => $step): ?>
                <article class="process-step">
                    <div class="process-step__num" aria-hidden="true"><?= $i + 1 ?></div>
                    <div>
                        <h3><?= htmlspecialchars($step['title']) ?></h3>
                        <p><?= htmlspecialchars($step['body']) ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if ($gallery_images !== []): ?>
<section class="section section--tight" id="work">
    <div class="container-wide">
        <div class="section__head container" style="margin-left:0;padding-left:0;">
            <h2>Examples of Our Work</h2>
            <p class="section__lead">Recent installs across Metro Detroit. More on the gallery page.</p>
        </div>
        <div class="gallery-preview">
            <?php foreach (array_slice($gallery_images, 0, 6) as $img): ?>
                <a href="<?= htmlspecialchars(url('gallery/')) ?>">
                    <img src="<?= htmlspecialchars($img['url']) ?>" alt="<?= htmlspecialchars($img['alt']) ?>" loading="lazy" width="800" height="600">
                </a>
            <?php endforeach; ?>
        </div>
        <p class="container" style="margin-top:1.25rem;">
            <a class="btn btn-ghost" href="<?= htmlspecialchars(url('gallery/')) ?>">Open full gallery</a>
        </p>
    </div>
</section>
<?php endif; ?>

<section class="section" id="about">
    <div class="container owner">
        <div class="owner__copy">
            <p class="eyebrow">About</p>
            <h2>20 Years Of Design</h2>
            <p class="section__lead">With <?= (int) $years_experience ?> years of experience, Steven has shaped holiday lighting layouts across Metro Detroit. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
            <p>
                <a class="btn btn-primary" href="<?= htmlspecialchars($phone_href) ?>">
                    <svg class="btn__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.81.36 1.6.68 2.35a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.75.32 1.54.55 2.35.68A2 2 0 0 1 22 16.92z"/></svg>
                    <?= htmlspecialchars($phone) ?>
                </a>
            </p>
        </div>
        <div class="owner__photo<?= $owner_image === '' ? ' owner__photo--empty' : '' ?>">
            <?php if ($owner_image !== ''): ?>
                <img src="<?= htmlspecialchars(url($owner_image)) ?>" alt="<?= htmlspecialchars($owner_image_alt) ?>" width="900" height="1100" loading="lazy" decoding="async">
            <?php else: ?>
                <div class="owner__photo-placeholder" aria-hidden="true"></div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if ($testimonials !== []): ?>
<section class="section section--tight" id="testimonials">
    <div class="container">
        <div class="section__head">
            <h2>What clients say</h2>
        </div>
        <div class="testimonials">
            <?php foreach ($testimonials as $t): ?>
                <blockquote class="testimonial">
                    <p>“<?= htmlspecialchars($t['quote']) ?>”</p>
                    <footer>
                        — <?= htmlspecialchars($t['name']) ?>
                        <?php if (!empty($t['detail'])): ?>
                            · <?= htmlspecialchars($t['detail']) ?>
                        <?php endif; ?>
                    </footer>
                </blockquote>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="section" id="faq">
    <div class="container">
        <div class="section__head">
            <h2>FAQ</h2>
        </div>
        <div class="faq-list">
            <?php foreach ($faq_items as $i => $item): ?>
                <div class="faq-item">
                    <button type="button" aria-expanded="false" id="faq-btn-<?= $i ?>"><?= htmlspecialchars($item['q']) ?></button>
                    <div><?= htmlspecialchars($item['a']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section" id="quote">
    <div class="container">
        <div class="cta-band">
            <h2>Ready for a quote?</h2>
            <p>Text or email a few photos of the house, storefront, or community entrance. Include your city. Steven will follow up.</p>
            <div class="cta-band__actions">
                <a class="btn btn-primary" href="<?= htmlspecialchars($phone_href) ?>"><?= htmlspecialchars($phone) ?></a>
                <a class="btn btn-ghost" href="<?= htmlspecialchars($email_href) ?>"><?= htmlspecialchars($email) ?></a>
                <a class="btn btn-ghost" href="<?= htmlspecialchars(url('contact/')) ?>">Contact form</a>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
