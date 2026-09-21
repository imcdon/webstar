<?php
/*
 * gallery/index.php
 */
require __DIR__ . '/../includes/config.php';

$page_title = 'Gallery | ' . $site_name;
require __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container-wide">
        <div class="eyebrow text-on-dark-muted">Photos</div>
        <h1 class="heading-display text-display-xl text-on-dark">Gallery</h1>
        <p class="page-hero-intro"><?= htmlspecialchars($gallery_intro) ?></p>
    </div>
</section>

<section class="section section-default">
    <div class="container-wide">
        <div class="gallery-grid">
            <?php foreach ($gallery_items as $item): ?>
                <button type="button"
                        class="gallery-item"
                        data-lightbox
                        data-src="<?= htmlspecialchars(url(ltrim($item['src'], '/'))) ?>"
                        data-alt="<?= htmlspecialchars($item['alt']) ?>">
                    <div class="gallery-item-img" style="background-image: url('<?= htmlspecialchars(url(ltrim($item['src'], '/'))) ?>')"></div>
                </button>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="Image preview">
    <button type="button" class="lightbox-close" aria-label="Close">&times;</button>
    <img src="" alt="">
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
