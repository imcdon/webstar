<?php
$config = webstar_config();
?>
<footer class="site-footer">
    <div class="site-footer__inner">
        <p class="site-footer__brand"><?php echo webstar_h($config['business_name']); ?></p>
        <p class="site-footer__contact">Email: <a href="mailto:<?php echo webstar_h($config['email']); ?>" class="site-footer__link"><?php echo webstar_h($config['email']); ?></a></p>
        <p class="site-footer__area">Service area: <?php echo webstar_h($config['service_area']); ?></p>
        <nav class="site-footer__sitemap" aria-label="Footer sitemap">
            <p class="site-footer__sitemap-title">Sitemap</p>
            <ul class="site-footer__sitemap-list">
                <li><a href="<?php echo webstar_h(webstar_url('index.php')); ?>" class="site-footer__link">Home</a></li>
                <li><a href="<?php echo webstar_h(webstar_url('packages.php')); ?>" class="site-footer__link">Packages</a></li>
                <li><a href="<?php echo webstar_h(webstar_url('examples.php')); ?>" class="site-footer__link">Examples</a></li>
                <li><a href="<?php echo webstar_h(webstar_url('about.php')); ?>" class="site-footer__link">About</a></li>
                <li><a href="<?php echo webstar_h(webstar_url('contact.php')); ?>" class="site-footer__link">Contact</a></li>
                <li><a href="<?php echo webstar_h(webstar_url('portal/')); ?>" class="site-footer__link">Client Portal</a></li>
                <li><a href="<?php echo webstar_h(webstar_url('privacy.php')); ?>" class="site-footer__link">Privacy</a></li>
                <li><a href="<?php echo webstar_h(webstar_url('terms.php')); ?>" class="site-footer__link">Terms</a></li>
            </ul>
        </nav>
        <p class="site-footer__meta">&copy; <?php echo date('Y'); ?> <?php echo webstar_h($config['business_name']); ?>. All rights reserved.</p>
    </div>
</footer>
