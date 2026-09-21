<?php
/*
 * partials/final-cta.php - Ready to fish Lake Superior silhouette CTA.
 * Expects $final_cta from config.
 */
?>
<section class="section section-dark cta-strip">
    <div class="container-wide cta-superior-wrap">
        <div class="cta-superior">
            <div class="cta-superior-shape" aria-hidden="true"></div>
            <div class="cta-superior-inner">
                <div class="eyebrow"><?= htmlspecialchars($final_cta['eyebrow']) ?></div>
                <h2 class="heading-display text-display-md"><?= htmlspecialchars($final_cta['headline']) ?></h2>
                <p class="cta-copy"><?= htmlspecialchars($final_cta['copy']) ?></p>
                <div class="hero-actions" style="justify-content: center; margin-top: 1.5rem;">
                    <a class="btn-primary btn-call-plain" href="<?= htmlspecialchars($phone_href) ?>">Call Now</a>
                    <a class="btn-outline-light" href="<?= htmlspecialchars(url('contact/')) ?>">Contact</a>
                </div>
            </div>
        </div>
    </div>
</section>
