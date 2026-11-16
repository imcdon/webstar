<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

$page_title = 'Services & Pricing | Gypsie Locs Hair Studio';
$page_description = 'Color, Dream Catchers extensions, cuts, and treatments at Gypsie Locs Hair Studio in Waterford, MI. Book online or start with a free consultation.';
$canonical_path = '/services/';
$body_class = 'page-services';

require dirname(__DIR__) . '/includes/header.php';
?>

<header class="page-hero">
    <div class="section-inner">
        <p class="page-hero__brand">Gypsie Locs</p>
        <h1>Services</h1>
        <p class="page-hero__lead">Starting prices from the public menu. Extension and correction work is quoted after a consult so the plan matches your hair — not a guess from a form.</p>
        <a class="btn btn--primary" href="<?= htmlspecialchars($booking_services_url) ?>" rel="noopener noreferrer">Book a service</a>
    </div>
</header>

<section class="section">
    <div class="section-inner">
        <?php foreach ($service_groups as $group): ?>
            <div class="menu-block">
                <h2><?= htmlspecialchars($group['title']) ?></h2>
                <ul class="menu-list">
                    <?php foreach ($group['items'] as $item): ?>
                        <li>
                            <div class="menu-list__name">
                                <span><?= htmlspecialchars($item['name']) ?></span>
                                <?php if ($item['note'] !== ''): ?>
                                    <small><?= htmlspecialchars($item['note']) ?></small>
                                <?php endif; ?>
                            </div>
                            <span class="menu-list__price"><?= htmlspecialchars($item['price']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
        <p class="fine-print">Prices subject to change. Live availability and exact quotes are on GlossGenius.</p>
    </div>
</section>

<section class="cta-band">
    <div class="section-inner cta-band__inner">
        <h2 class="cta-band__title">Ready when you are.</h2>
        <a class="btn btn--primary" href="<?= htmlspecialchars($booking_url) ?>" rel="noopener noreferrer">Book online</a>
    </div>
</section>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
