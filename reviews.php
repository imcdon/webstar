<?php

declare(strict_types=1);

require __DIR__ . '/library/helpers.php';
require_once __DIR__ . '/library/as-seen-on.php';

$currentPage = '';
$pageTitle = 'Leave a Review | Webstar';
$pageDescription = 'Leave a review for Webstar Business Services on Google, Yelp, Facebook, LinkedIn, or Instagram. Honest feedback helps Metro Detroit owners find us.';
$canonicalPath = 'reviews/';

$destinations = [
    [
        'id' => 'google',
        'label' => 'Google',
        'blurb' => 'Share your experience on Google Business Profile. This is the fastest way to help local owners find Webstar.',
        'url' => webstar_google_review_destination(),
        'cta' => 'Review us on Google',
    ],
    [
        'id' => 'yelp',
        'label' => 'Yelp',
        'blurb' => 'Leave a Yelp review when our listing is live.',
        'url' => webstar_profile_url('yelp_url'),
        'cta' => 'Review us on Yelp',
    ],
    [
        'id' => 'facebook',
        'label' => 'Facebook',
        'blurb' => 'Recommend Webstar on Facebook when the page is published.',
        'url' => webstar_profile_url('facebook_url'),
        'cta' => 'Review us on Facebook',
    ],
    [
        'id' => 'linkedin',
        'label' => 'LinkedIn',
        'blurb' => 'Recommend the company page on LinkedIn when it is ready.',
        'url' => webstar_profile_url('linkedin_url'),
        'cta' => 'Recommend on LinkedIn',
    ],
    [
        'id' => 'instagram',
        'label' => 'Instagram',
        'blurb' => 'Follow and mention @Webstar when the Instagram profile is live.',
        'url' => webstar_profile_url('instagram_url'),
        'cta' => 'Open Instagram',
    ],
];

include __DIR__ . '/library/layout-start.php';
?>
<section class="home-section home-section--night">
    <div class="home-section__inner">
        <h1 class="home-section__heading">Leave us a review</h1>
        <p class="home-section__textbox">If Webstar built your site or helped you get found on Google, a short public review helps the next Metro Detroit owner decide. Pick the platform you already use.</p>

        <ul class="review-destinations">
            <?php foreach ($destinations as $dest) :
                $url = trim((string) $dest['url']);
                $ready = $url !== '';
                ?>
                <li class="review-destination<?php echo $ready ? '' : ' review-destination--soon'; ?>">
                    <h2 class="review-destination__title"><?php echo webstar_h((string) $dest['label']); ?></h2>
                    <p class="review-destination__blurb"><?php echo webstar_h((string) $dest['blurb']); ?></p>
                    <?php if ($ready) : ?>
                        <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h($url); ?>" target="_blank" rel="noopener noreferrer"><?php echo webstar_h((string) $dest['cta']); ?></a>
                    <?php else : ?>
                        <p class="review-destination__pending">Link coming soon — profile not published yet.</p>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <p class="home-section__textbox"><a href="<?php echo webstar_h(webstar_url('contact.php')); ?>">Questions first?</a> Or browse <a href="<?php echo webstar_h(webstar_url('examples.php')); ?>">live client examples</a>.</p>
    </div>
</section>
<?php include __DIR__ . '/library/layout-end.php'; ?>
