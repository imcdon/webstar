<?php
declare(strict_types=1);
require dirname(__DIR__) . '/includes/config.php';

$page_title = 'Holiday Lighting Gallery | Metro Detroit Holiday Lighting';
$page_description = 'Photos of professional holiday lighting installs by Metro Detroit Holiday Lighting across Oakland, Wayne, and Macomb County.';
$canonical_path = '/gallery/';
$schema_breadcrumbs = [
    ['name' => 'Home', 'url' => absolute_url('')],
    ['name' => 'Gallery', 'url' => absolute_url('gallery/')],
];

require dirname(__DIR__) . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <p class="breadcrumbs"><a href="<?= htmlspecialchars(url()) ?>">Home</a> / Gallery</p>
        <h1>Gallery</h1>
        <p>Finished holiday lighting projects across Metro Detroit. Photos update as new installs are added.</p>
    </div>
</section>

<section class="section">
    <div class="container-wide">
        <?php if ($gallery_images === []): ?>
            <div class="gallery-empty">
                <p><strong>Photos coming soon.</strong> Drop finished project images into <code>assets/img/gallery/</code> and push — this page lists them automatically.</p>
                <p style="margin-top:1rem;">
                    <a class="btn btn-primary" href="<?= htmlspecialchars($phone_href) ?>">Call <?= htmlspecialchars($phone) ?></a>
                    <a class="btn btn-ghost" href="<?= htmlspecialchars(url('contact/')) ?>">Request a quote</a>
                </p>
            </div>
        <?php else:
            $gallery_landscape = array_values(array_filter($gallery_images, static fn ($img) => ($img['orientation'] ?? 'landscape') !== 'portrait'));
            $gallery_portrait = array_values(array_filter($gallery_images, static fn ($img) => ($img['orientation'] ?? '') === 'portrait'));
            ?>
            <?php if ($gallery_landscape !== []): ?>
            <div class="gallery-grid gallery-grid--landscape">
                <?php foreach ($gallery_landscape as $img): ?>
                    <a href="<?= htmlspecialchars($img['url']) ?>" target="_blank" rel="noopener noreferrer">
                        <img
                            src="<?= htmlspecialchars($img['url']) ?>"
                            alt="<?= htmlspecialchars($img['alt']) ?>"
                            loading="lazy"
                            width="<?= (int) ($img['width'] ?: 900) ?>"
                            height="<?= (int) ($img['height'] ?: 675) ?>"
                        >
                    </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <?php if ($gallery_portrait !== []): ?>
            <div class="gallery-grid gallery-grid--portrait<?= $gallery_landscape !== [] ? ' gallery-grid--portrait-below' : '' ?>">
                <?php foreach ($gallery_portrait as $img): ?>
                    <a href="<?= htmlspecialchars($img['url']) ?>" target="_blank" rel="noopener noreferrer">
                        <img
                            src="<?= htmlspecialchars($img['url']) ?>"
                            alt="<?= htmlspecialchars($img['alt']) ?>"
                            loading="lazy"
                            width="<?= (int) ($img['width'] ?: 600) ?>"
                            height="<?= (int) ($img['height'] ?: 900) ?>"
                        >
                    </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <div class="cta-band" style="margin-top:2.5rem;">
                <h2>Want a display like these?</h2>
                <p>Text photos of your property to <?= htmlspecialchars($phone) ?> or send them by email.</p>
                <div class="cta-band__actions">
                    <a class="btn btn-primary" href="<?= htmlspecialchars($phone_href) ?>">Call now</a>
                    <a class="btn btn-ghost" href="<?= htmlspecialchars(url('contact/')) ?>">Contact form</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
