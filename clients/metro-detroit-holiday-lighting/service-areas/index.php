<?php
declare(strict_types=1);
require dirname(__DIR__) . '/includes/config.php';

$page_title = 'Holiday Lighting Service Areas in Metro Detroit | Metro Detroit Holiday Lighting';
$page_description = 'Professional holiday lighting across Oakland, Wayne, and Macomb County — Birmingham, Troy, Rochester Hills, Livonia, Sterling Heights, and more. Call (248) 525-4715.';
$canonical_path = '/service-areas/';
$schema_home = true;
$schema_breadcrumbs = [
    ['name' => 'Home', 'url' => absolute_url('')],
    ['name' => 'Service Areas', 'url' => absolute_url('service-areas/')],
];

$byCounty = mdhl_cities_by_county();

require dirname(__DIR__) . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <p class="breadcrumbs"><a href="<?= htmlspecialchars(url()) ?>">Home</a> / Service Areas</p>
        <h1>Metro Detroit service areas</h1>
        <p>Holiday lighting for homes, businesses, and HOAs across Oakland, Wayne, and Macomb County. Pick your city for local details, or call <?= htmlspecialchars($phone) ?>.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php foreach ($byCounty as $county => $cities): ?>
            <div class="county-block">
                <h2><?= htmlspecialchars($county) ?> County</h2>
                <ul class="city-list">
                    <?php foreach ($cities as $city): ?>
                        <li>
                            <a href="<?= htmlspecialchars(url('service-areas/' . $city['slug'] . '/')) ?>">
                                <?= htmlspecialchars($city['name']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>

        <div class="cta-band" style="margin-top:1rem;">
            <h2>Don’t see your city?</h2>
            <p>We still may serve you. Call or send photos with your city name.</p>
            <div class="cta-band__actions">
                <a class="btn btn-primary" href="<?= htmlspecialchars($phone_href) ?>"><?= htmlspecialchars($phone) ?></a>
                <a class="btn btn-ghost" href="<?= htmlspecialchars(url('contact/')) ?>">Contact form</a>
            </div>
        </div>
    </div>
</section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
