<?php

declare(strict_types=1);

use Webstar\Shop\Helpers;

/** @var list<array{name:string,location:string,url?:string}> $retail_stores */
$retail_stores = $retail_stores ?? [];
if ($retail_stores === []) {
    return;
}

$retail_stores_heading = $retail_stores_heading ?? 'Find us in these stores';
$retail_stores_lead = $retail_stores_lead ?? 'Pick up DJ\'s Tackle at bait shops and retailers across southeast Michigan.';

$render_store = static function (array $store): void {
    $name = (string) ($store['name'] ?? '');
    $location = (string) ($store['location'] ?? '');
    if ($name === '' && $location === '') {
        return;
    }
    $url = isset($store['url']) ? trim((string) $store['url']) : '';
    if ($url !== '') {
        echo '<a class="retail-store-card" href="' . Helpers::e($url) . '" target="_blank" rel="noopener noreferrer">';
    } else {
        echo '<div class="retail-store-card">';
    }
    echo '<strong>' . Helpers::e($name) . '</strong>';
    echo '<span>' . Helpers::e($location) . '</span>';
    echo $url !== '' ? '</a>' : '</div>';
};
?>
<div class="retail-stores">
  <div class="retail-stores-head">
    <h3 class="retail-stores-heading"><?= Helpers::e($retail_stores_heading) ?></h3>
    <?php if ($retail_stores_lead !== ''): ?>
      <p class="retail-stores-lead"><?= Helpers::e($retail_stores_lead) ?></p>
    <?php endif; ?>
  </div>
  <div class="retail-stores-marquee" data-retail-marquee tabindex="0" aria-label="<?= Helpers::e($retail_stores_heading) ?>">
    <div class="retail-stores-track">
      <div class="retail-stores-set">
        <?php foreach ($retail_stores as $store) {
            $render_store($store);
        } ?>
      </div>
      <div class="retail-stores-set" aria-hidden="true">
        <?php foreach ($retail_stores as $store) {
            $render_store($store);
        } ?>
      </div>
    </div>
  </div>
</div>
