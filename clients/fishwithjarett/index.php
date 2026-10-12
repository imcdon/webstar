<?php
require __DIR__ . '/includes/config.php';

$page_title = $site_name . ' | Salmon and Trout Fishing on Lake Michigan Tributaries';
$body_class = 'page-home';
require __DIR__ . '/includes/header.php';
?>

<section class="hero" aria-label="Welcome">
    <div class="hero__visual" aria-hidden="true">
        <img
            class="hero__img"
            src="<?= htmlspecialchars(url($hero_image)) ?>"
            alt=""
            width="1242"
            height="931"
            fetchpriority="high"
            decoding="async"
        >
    </div>
    <div class="hero__overlay"></div>
    <div class="hero__content">
        <p class="hero__brand"><?= htmlspecialchars($site_name) ?></p>
        <h1 class="hero__title">Salmon and Trout Fishing on Lake Michigan Tributaries</h1>
        <p class="hero__support">Fish Wisconsin, Indiana, and Michigan waters with licensed guide Jarett Carlson - Guided fishing trips built around the rivers and runs.</p>
        <div class="hero__contact">
            <a class="hero__phone" href="<?= htmlspecialchars($phone_href) ?>"><?= htmlspecialchars($phone) ?></a>
            <div class="hero__social" aria-label="Social">
                <?php if (!empty($social['instagram'])): ?>
                    <a class="social-icon" href="<?= htmlspecialchars($social['instagram']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false">
                            <path fill="currentColor" d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                        </svg>
                    </a>
                <?php endif; ?>
                <?php if (!empty($social['facebook'])): ?>
                    <a class="social-icon" href="<?= htmlspecialchars($social['facebook']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false">
                            <path fill="currentColor" d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="section section--locations" id="locations-species">
    <div class="section__inner">
        <h2 class="section__title">Locations and Fish Species</h2>

        <h3 class="section__subtitle">Locations: Lake Michigan Tributaries</h3>
        <div class="place-grid">
            <?php foreach ($location_cards as $card): ?>
                <article class="place-card place-card--<?= htmlspecialchars($card['tone']) ?>">
                    <div class="place-card__media" aria-hidden="true"></div>
                    <div class="place-card__shade"></div>
                    <div class="place-card__body">
                        <h4 class="place-card__title"><?= htmlspecialchars($card['title']) ?></h4>
                        <p class="place-card__meta"><?= htmlspecialchars($card['rivers']) ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <h3 class="section__subtitle section__subtitle--spaced">Species</h3>
        <div class="place-grid">
            <?php foreach ($species_cards as $card): ?>
                <article class="place-card place-card--photo place-card--<?= htmlspecialchars($card['tone']) ?>">
                    <img
                        class="place-card__img"
                        src="<?= htmlspecialchars(url($card['image'])) ?>"
                        alt="<?= htmlspecialchars($card['alt']) ?>"
                        width="1200"
                        height="900"
                        loading="lazy"
                        decoding="async"
                    >
                    <div class="place-card__shade" aria-hidden="true"></div>
                    <div class="place-card__body">
                        <h4 class="place-card__title"><?= htmlspecialchars($card['title']) ?></h4>
                        <p class="place-card__meta"><?= htmlspecialchars($card['season']) ?></p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--guide" id="meet-your-guide">
    <div class="section__inner">
        <h2 class="section__title guide-heading">Meet your Guide - Jarett Carlson</h2>
        <div class="guide-layout">
            <figure class="guide-portrait">
                <img
                    src="<?= htmlspecialchars(url('assets/img/Jarett-Carlson-Portrait.jpg')) ?>"
                    alt="Jarett Carlson"
                    width="1440"
                    height="1080"
                    loading="lazy"
                    decoding="async"
                >
            </figure>
            <div class="guide-copy">
                <p>Jarett Carlson is a licensed and insured fishing guide in Wisconsin, Indiana, and Michigan. Jarett specializes in Lake Michigan tributaries for cold-water species like Trout, Steelhead, and Salmon. Jarett is the owner and operator of fishwithjarett.com.</p>
            </div>
        </div>
    </div>
</section>

<section class="section section--cta" id="book">
    <div class="section__inner section__inner--narrow">
        <h2 class="section__title">Ready to fish?</h2>
        <p class="section__lead">Call and talk with Jarett. He will plan a trip that is best for the time of year, what you'd like to fish for, and where you're located.</p>
        <div class="cta-contact">
            <a class="btn btn--primary btn--lg" href="<?= htmlspecialchars($phone_href) ?>"><?= htmlspecialchars($phone) ?></a>
            <div class="cta-social" aria-label="Social">
                <?php if (!empty($social['instagram'])): ?>
                    <a class="social-icon social-icon--on-light" href="<?= htmlspecialchars($social['instagram']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false">
                            <path fill="currentColor" d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                        </svg>
                    </a>
                <?php endif; ?>
                <?php if (!empty($social['facebook'])): ?>
                    <a class="social-icon social-icon--on-light" href="<?= htmlspecialchars($social['facebook']) ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook">
                        <svg viewBox="0 0 24 24" width="22" height="22" aria-hidden="true" focusable="false">
                            <path fill="currentColor" d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<section class="section section--client-photos" id="client-photos">
    <div class="section__inner">
        <h2 class="section__title">Client Photos</h2>
        <div class="client-gallery">
            <?php foreach ($client_photos as $photo): ?>
                <figure class="client-gallery__item client-gallery__item--<?= htmlspecialchars($photo['layout']) ?>">
                    <img
                        src="<?= htmlspecialchars(url($photo['image'])) ?>"
                        alt="<?= htmlspecialchars($photo['alt']) ?>"
                        width="800"
                        height="800"
                        loading="lazy"
                        decoding="async"
                    >
                </figure>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
