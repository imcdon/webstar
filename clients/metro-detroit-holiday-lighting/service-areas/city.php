<?php
declare(strict_types=1);
require dirname(__DIR__) . '/includes/config.php';

$slug = strtolower(trim((string) ($_GET['city'] ?? '')));
$city = mdhl_city_by_slug($slug);
if ($city === null) {
    http_response_code(404);
    require dirname(__DIR__) . '/404.php';
    exit;
}

$name = $city['name'];
$county = $city['county'];
$page_title = "Holiday Lighting in {$name}, MI | Metro Detroit Holiday Lighting";
$page_description = "Professional Christmas and holiday lighting installation in {$name}, {$county} County. Design, install, mid-season care, and take-down by Steven. Call (248) 525-4715.";
$canonical_path = '/service-areas/' . $city['slug'] . '/';
$schema_home = true;
$schema_breadcrumbs = [
    ['name' => 'Home', 'url' => absolute_url('')],
    ['name' => 'Service Areas', 'url' => absolute_url('service-areas/')],
    ['name' => $name, 'url' => absolute_url('service-areas/' . $city['slug'] . '/')],
];

$nearby = [];
foreach ($city['nearby'] as $nearSlug) {
    $near = mdhl_city_by_slug($nearSlug);
    if ($near !== null) {
        $nearby[] = $near;
    }
}

require dirname(__DIR__) . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <p class="breadcrumbs">
            <a href="<?= htmlspecialchars(url()) ?>">Home</a> /
            <a href="<?= htmlspecialchars(url('service-areas/')) ?>">Service Areas</a> /
            <?= htmlspecialchars($name) ?>
        </p>
        <h1>Holiday lighting in <?= htmlspecialchars($name) ?></h1>
        <p>Custom holiday lighting for <?= htmlspecialchars($name) ?> homes, businesses, and HOAs in <?= htmlspecialchars($county) ?> County — designed and installed by Steven with <?= (int) $years_experience ?> years of Metro Detroit experience.</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="card-grid" style="margin-bottom:2rem;">
            <article class="card">
                <h2 style="font-size:1.15rem;margin:0 0 0.55rem;">Homes in <?= htmlspecialchars($name) ?></h2>
                <p>Rooflines, trees, walks, and accents that fit the architecture — without you on a ladder in December.</p>
            </article>
            <article class="card">
                <h2 style="font-size:1.15rem;margin:0 0 0.55rem;">Businesses &amp; storefronts</h2>
                <p>Polished commercial displays that stay maintained from install through take-down.</p>
            </article>
            <article class="card">
                <h2 style="font-size:1.15rem;margin:0 0 0.55rem;">HOAs &amp; communities</h2>
                <p>Entrances and shared spaces with one clear plan and one owner who answers the phone.</p>
            </article>
        </div>

        <div class="owner__panel" style="margin-bottom:2rem;">
            <h2 style="margin-top:0;font-size:1.35rem;">How to get a quote in <?= htmlspecialchars($name) ?></h2>
            <p>Text or email photos of the property to <a href="<?= htmlspecialchars($phone_href) ?>"><?= htmlspecialchars($phone) ?></a> or <a href="<?= htmlspecialchars($email_href) ?>"><?= htmlspecialchars($email) ?></a>. Mention <?= htmlspecialchars($name) ?> and whether it is a home, business, or HOA. Steven follows up with a plan.</p>
            <p style="margin-bottom:0;"><a class="btn btn-primary" href="<?= htmlspecialchars(url('contact/')) ?>">Open contact form</a></p>
        </div>

        <?php if ($nearby !== []): ?>
            <h2>Nearby cities we also serve</h2>
            <ul class="city-list" style="margin-top:0.85rem;">
                <?php foreach ($nearby as $near): ?>
                    <li>
                        <a href="<?= htmlspecialchars(url('service-areas/' . $near['slug'] . '/')) ?>">
                            <?= htmlspecialchars($near['name']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <div class="cta-band" style="margin-top:2.5rem;">
            <h2>Book <?= htmlspecialchars($name) ?> holiday lighting</h2>
            <p><?= htmlspecialchars($trust_line) ?></p>
            <div class="cta-band__actions">
                <a class="btn btn-primary" href="<?= htmlspecialchars($phone_href) ?>"><?= htmlspecialchars($phone) ?></a>
                <a class="btn btn-ghost" href="<?= htmlspecialchars(url('gallery/')) ?>">Gallery</a>
            </div>
        </div>
    </div>
</section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
