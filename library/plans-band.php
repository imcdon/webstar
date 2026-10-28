<?php
/**
 * "Two ways to pay" band: one-time package vs Website as a Service, plus Care Plan teaser.
 * Include on home and packages pages after webstar_config() is available.
 */
$waasBand = webstar_waas();
?>
<section class="home-section home-section--panel" id="ways-to-pay">
    <div class="home-section__inner">
        <h2 class="home-section__heading">Two ways to pay</h2>
        <div class="ways-grid">
            <article class="package-card">
                <h3 class="package-card__title">Own it outright</h3>
                <p class="package-card__price">One-time fee</p>
                <p class="package-card__summary">Pick a website package, pay 50% to start and 50% at build complete (or pay in full), and we deploy to hosting you own. No monthly fees to Webstar unless you add a Care Plan.</p>
                <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h(webstar_url('packages.php')); ?>">See website packages</a>
            </article>
            <article class="package-card package-card--featured">
                <span class="package-card__badge">New</span>
                <h3 class="package-card__title"><?php echo webstar_h((string) $waasBand['title']); ?></h3>
                <p class="package-card__price"><?php echo webstar_h((string) $waasBand['price']); ?> <span class="plan-price__period"><?php echo webstar_h((string) $waasBand['period']); ?></span></p>
                <p class="package-card__meta"><?php echo webstar_h((string) $waasBand['minimum_term']); ?> · you own the files after month 6</p>
                <p class="package-card__summary">Website build, hosting, Google setup, and ongoing care for one flat monthly payment. No large upfront cost.</p>
                <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h(webstar_url('plans.php')); ?>#waas">How Website as a Service works</a>
            </article>
        </div>
        <p class="home-section__textbox plan-note">After launch, an optional <a href="<?php echo webstar_h(webstar_url('plans.php')); ?>#care-plans">Care Plan</a> at $50/mo covers hosting, backups, updates, edits, Google reporting, and a quarterly strategy call. Every website comes with a 30-day post-launch fix window either way.</p>
    </div>
</section>
