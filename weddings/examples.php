<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/library/helpers.php';

$websites = [
    [
        'slug' => 'ava-jordan',
        'names' => 'Ava & Jordan',
        'style' => 'Garden evening',
        'path' => 'demos/ava-jordan',
    ],
    [
        'slug' => 'maya-cole',
        'names' => 'Maya & Cole',
        'style' => 'City loft',
        'path' => 'demos/maya-cole',
    ],
    [
        'slug' => 'elena-sam',
        'names' => 'Elena & Sam',
        'style' => 'Coastal weekend',
        'path' => 'demos/elena-sam',
    ],
];

$invites = [
    [
        'slug' => 'noir-crest',
        'names' => 'Riley & Blake',
        'style' => 'Noir crest',
        'path' => 'demos/invites/noir-crest',
    ],
    [
        'slug' => 'botanical-seal',
        'names' => 'Nora & Theo',
        'style' => 'Botanical seal',
        'path' => 'demos/invites/botanical-seal',
    ],
    [
        'slug' => 'ivory-script',
        'names' => 'Camille & Owen',
        'style' => 'Ivory script',
        'path' => 'demos/invites/ivory-script',
    ],
];

$pageTitle = 'Wedding Website & Invite Examples — Webstar Weddings';
$pageDescription = 'Demo wedding websites and digital invitations by Webstar Weddings.';
$canonicalPath = 'weddings/examples/';
$weddingsPage = 'examples';
$showSeal = false;
include __DIR__ . '/includes/layout-start.php';

function ww_render_carousel(string $id, string $heading, array $items): void
{
    ?>
    <section class="ww-carousel-block" aria-label="<?php echo webstar_h($heading); ?>">
        <div class="ww-carousel-block__head">
            <h2 class="ww-carousel-block__title"><?php echo webstar_h($heading); ?></h2>
            <div class="ww-carousel-block__controls">
                <button type="button" class="ww-carousel__btn" data-carousel-prev="<?php echo webstar_h($id); ?>" aria-label="Previous <?php echo webstar_h($heading); ?>">&#8249;</button>
                <button type="button" class="ww-carousel__btn" data-carousel-next="<?php echo webstar_h($id); ?>" aria-label="Next <?php echo webstar_h($heading); ?>">&#8250;</button>
            </div>
        </div>
        <div class="ww-carousel" id="<?php echo webstar_h($id); ?>" data-carousel>
            <div class="ww-carousel__track">
                <?php foreach ($items as $i => $item) : ?>
                    <a class="ww-carousel__slide<?php echo $i === 0 ? ' is-active' : ''; ?>" href="<?php echo webstar_h(webstar_weddings_url($item['path'])); ?>" data-carousel-slide>
                        <span class="ww-carousel__badge">Demo</span>
                        <strong class="ww-carousel__names"><?php echo webstar_h($item['names']); ?></strong>
                        <span class="ww-carousel__style"><?php echo webstar_h($item['style']); ?></span>
                        <span class="ww-carousel__cta">Open demo</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
}
?>
<div class="ww-examples-shell" id="ww-examples-shell">
    <?php
    ww_render_carousel('ww-carousel-websites', 'Wedding websites', $websites);
    ww_render_carousel('ww-carousel-invites', 'Digital invites', $invites);
    ?>
</div>
<?php include __DIR__ . '/includes/layout-end.php'; ?>
