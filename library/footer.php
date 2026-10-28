<?php
$config = webstar_config();
$seoFooter = webstar_seo_footer_links(6);
?>
<footer class="site-footer">
    <div class="site-footer__inner">
        <p class="site-footer__brand"><?php echo webstar_h($config['business_name']); ?></p>
        <p class="site-footer__contact">
            <?php if (webstar_phone_display() !== '') : ?>
                Phone: <a href="<?php echo webstar_h(webstar_phone_href()); ?>" class="site-footer__link"><?php echo webstar_h(webstar_phone_display()); ?></a>
                <span class="site-footer__contact-sep" aria-hidden="true"> · </span>
            <?php endif; ?>
            Email: <a href="mailto:<?php echo webstar_h($config['email']); ?>" class="site-footer__link"><?php echo webstar_h($config['email']); ?></a>
        </p>
        <p class="site-footer__area">Service area: <?php echo webstar_h($config['service_area']); ?></p>

        <div class="site-footer__grid">
            <nav class="site-footer__sitemap" aria-label="Footer sitemap">
                <p class="site-footer__sitemap-title">Sitemap</p>
                <ul class="site-footer__sitemap-list">
                    <li><a href="<?php echo webstar_h(webstar_url('index.php')); ?>" class="site-footer__link">Home</a></li>
                    <li><a href="<?php echo webstar_h(webstar_url('packages.php')); ?>" class="site-footer__link">Packages</a></li>
                    <li><a href="<?php echo webstar_h(webstar_url('plans.php')); ?>" class="site-footer__link">Monthly Plans</a></li>
                    <li><a href="<?php echo webstar_h(webstar_url('process.php')); ?>" class="site-footer__link">Process</a></li>
                    <li><a href="<?php echo webstar_h(webstar_url('resources.php')); ?>" class="site-footer__link">Resources</a></li>
                    <li><a href="<?php echo webstar_h(webstar_url('service-areas.php')); ?>" class="site-footer__link">Service areas</a></li>
                    <li><a href="<?php echo webstar_h(webstar_url('industries.php')); ?>" class="site-footer__link">Industries</a></li>
                    <li><a href="<?php echo webstar_h(webstar_url('examples.php')); ?>" class="site-footer__link">Examples</a></li>
                    <li><a href="<?php echo webstar_h(webstar_url('reviews.php')); ?>" class="site-footer__link">Reviews</a></li>
                    <li><a href="<?php echo webstar_h(webstar_url('about.php')); ?>" class="site-footer__link">About</a></li>
                    <li><a href="<?php echo webstar_h(webstar_url('contact.php')); ?>" class="site-footer__link">Contact</a></li>
                    <li><a href="<?php echo webstar_h(webstar_url('portal/')); ?>" class="site-footer__link">Client Portal</a></li>
                    <li><a href="<?php echo webstar_h(webstar_url('privacy.php')); ?>" class="site-footer__link">Privacy</a></li>
                    <li><a href="<?php echo webstar_h(webstar_url('terms.php')); ?>" class="site-footer__link">Terms</a></li>
                    <li><a href="<?php echo webstar_h(webstar_url('accessibility.php')); ?>" class="site-footer__link">Accessibility</a></li>
                </ul>
            </nav>

            <?php if ($seoFooter['areas'] !== []) : ?>
                <nav class="site-footer__sitemap" aria-label="Service areas">
                    <p class="site-footer__sitemap-title">Service areas</p>
                    <ul class="site-footer__sitemap-list site-footer__sitemap-list--stack">
                        <?php foreach ($seoFooter['areas'] as $link) : ?>
                            <li><a href="<?php echo webstar_h($link['href']); ?>" class="site-footer__link"><?php echo webstar_h($link['label']); ?></a></li>
                        <?php endforeach; ?>
                        <?php if (($seoFooter['areas_total'] ?? 0) > count($seoFooter['areas'])) : ?>
                            <li><a href="<?php echo webstar_h(webstar_url('service-areas.php')); ?>" class="site-footer__link">All service areas</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>

            <?php if ($seoFooter['industries'] !== []) : ?>
                <nav class="site-footer__sitemap" aria-label="Industries">
                    <p class="site-footer__sitemap-title">Industries</p>
                    <ul class="site-footer__sitemap-list site-footer__sitemap-list--stack">
                        <?php foreach ($seoFooter['industries'] as $link) : ?>
                            <li><a href="<?php echo webstar_h($link['href']); ?>" class="site-footer__link"><?php echo webstar_h($link['label']); ?></a></li>
                        <?php endforeach; ?>
                        <?php if (($seoFooter['industries_total'] ?? 0) > count($seoFooter['industries'])) : ?>
                            <li><a href="<?php echo webstar_h(webstar_url('industries.php')); ?>" class="site-footer__link">All industries</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>

        <p class="site-footer__meta">&copy; <?php echo date('Y'); ?> <?php echo webstar_h($config['legal_name'] ?? $config['business_name']); ?> · <?php echo webstar_h($config['locality'] ?? 'Metro Detroit'); ?>, <?php echo webstar_h($config['region'] ?? 'MI'); ?>. All rights reserved.</p>
    </div>
</footer>
