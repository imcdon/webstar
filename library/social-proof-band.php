<?php

declare(strict_types=1);

/**
 * Homepage As Seen On logo marquee + optional Google review marquee + Leave a review CTA.
 */
$platforms = webstar_as_seen_on_platforms();
$reviews = require __DIR__ . '/reviews-data.php';
if (!is_array($reviews)) {
    $reviews = [];
}
$reviews = array_values(array_filter($reviews, static function ($row): bool {
    return is_array($row)
        && trim((string) ($row['quote'] ?? '')) !== ''
        && trim((string) ($row['name'] ?? '')) !== '';
}));

/**
 * @param list<array{id: string, label: string, url_key: string, svg: string}> $platforms
 */
$buildLogoItems = static function (array $platforms, string $idSuffix): string {
    $html = '';
    foreach ($platforms as $platform) {
        $url = webstar_profile_url((string) $platform['url_key']);
        $svg = str_replace('webstar-ig-grad', 'webstar-ig-grad-' . $idSuffix, (string) $platform['svg']);
        $mark = '<span class="as-seen-on__mark" aria-hidden="true">' . $svg . '</span>';
        if ($url !== '') {
            $html .= '<a class="as-seen-on__item" href="' . webstar_h($url) . '" target="_blank" rel="noopener noreferrer" aria-label="' . webstar_h((string) $platform['label']) . '">' . $mark . '</a>';
        } else {
            $html .= '<span class="as-seen-on__item as-seen-on__item--static" role="img" aria-label="' . webstar_h((string) $platform['label']) . '">' . $mark . '</span>';
        }
    }

    return $html;
};
$logoItemsA = $buildLogoItems($platforms, 'a');
$logoItemsB = $buildLogoItems($platforms, 'b');

$reviewItems = '';
foreach ($reviews as $review) {
    $rating = (int) ($review['rating'] ?? 5);
    if ($rating < 1) {
        $rating = 1;
    }
    if ($rating > 5) {
        $rating = 5;
    }
    $stars = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
    $source = trim((string) ($review['source'] ?? 'Google'));
    $reviewItems .= '<figure class="review-rail__card">'
        . '<div class="review-rail__stars" aria-label="' . webstar_h((string) $rating) . ' out of 5 stars">' . webstar_h($stars) . '</div>'
        . '<blockquote class="review-rail__quote">' . webstar_h((string) $review['quote']) . '</blockquote>'
        . '<figcaption class="review-rail__by"><strong>' . webstar_h((string) $review['name']) . '</strong>'
        . ($source !== '' ? ' · ' . webstar_h($source) : '')
        . '</figcaption></figure>';
}
?>
<section class="social-proof" id="as-seen-on" aria-label="As seen on and reviews">
    <div class="social-proof__inner">
        <p class="social-proof__label">As Seen On</p>
        <div class="marquee" data-marquee>
            <div class="marquee__viewport">
                <div class="marquee__track">
                    <div class="marquee__group">
                        <?php echo $logoItemsA; ?>
                    </div>
                    <div class="marquee__group" aria-hidden="true">
                        <?php echo $logoItemsB; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($reviewItems !== '') : ?>
            <p class="social-proof__label social-proof__label--reviews">Verified Google reviews</p>
            <div class="marquee marquee--reviews" data-marquee>
                <div class="marquee__viewport">
                    <div class="marquee__track">
                        <div class="marquee__group">
                            <?php echo $reviewItems; ?>
                        </div>
                        <div class="marquee__group" aria-hidden="true">
                            <?php echo $reviewItems; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="social-proof__cta">
            <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h(webstar_url('reviews/')); ?>">Leave us a review</a>
        </div>
    </div>
</section>
