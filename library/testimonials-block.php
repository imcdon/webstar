<?php
/**
 * Testimonials block. Shows approved client quotes from testimonials-data.php
 * and always offers a link to leave a Google review.
 */
$testimonials = require __DIR__ . '/testimonials-data.php';
$configT = webstar_config();
$reviewUrl = (string) ($configT['google_review_url'] ?? ($configT['google_maps_url'] ?? ''));
?>
<section class="home-section home-section--panel" id="testimonials">
    <div class="home-section__inner">
        <h2 class="home-section__heading">What clients say</h2>
        <?php if ($testimonials !== []) : ?>
            <div class="testimonial-grid">
                <?php foreach ($testimonials as $t) : ?>
                    <figure class="testimonial">
                        <blockquote class="testimonial__quote"><?php echo webstar_h((string) $t['quote']); ?></blockquote>
                        <figcaption class="testimonial__by">
                            <strong><?php echo webstar_h((string) $t['name']); ?></strong>
                            <?php if (($t['role'] ?? '') !== '' || ($t['business'] ?? '') !== '') : ?>
                                · <?php echo webstar_h(trim(((string) ($t['role'] ?? '')) . ' ' . ((string) ($t['business'] ?? '')))); ?>
                            <?php endif; ?>
                            <?php if (($t['url'] ?? '') !== '') : ?>
                                · <a href="<?php echo webstar_h((string) $t['url']); ?>" target="_blank" rel="noopener noreferrer">Visit site</a>
                            <?php endif; ?>
                        </figcaption>
                    </figure>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p class="home-section__textbox">We publish only quotes clients approve in writing, so this section grows with each launch. In the meantime, the <a href="<?php echo webstar_h(webstar_url('examples.php')); ?>">Examples page</a> shows live client sites you can visit yourself.</p>
        <?php endif; ?>
        <?php if ($reviewUrl !== '') : ?>
            <p class="home-section__textbox">Worked with Webstar? <a href="<?php echo webstar_h($reviewUrl); ?>" target="_blank" rel="noopener noreferrer">Leave a Google review</a> — it helps other Michigan owners find us.</p>
        <?php endif; ?>
    </div>
</section>
