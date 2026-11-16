<?php
/*
 * footer.php - Site footer, sticky mobile CTA, closing tags.
 */
?>
    </main>

    <footer class="site-footer">
        <div class="footer-grid">
            <div class="footer-brand">
                <a class="logo logo-footer" href="<?= htmlspecialchars(url()) ?>">
                    <img class="logo-mark" src="<?= htmlspecialchars(url('assets/img/logo-new.png')) ?>" alt="" width="60" height="60">
                    <span class="logo-text"><span class="logo-text__line">Metro Detroit</span><span class="logo-text__line">Holiday Lighting</span></span>
                </a>
                <p class="footer-trust"><?= htmlspecialchars($trust_line) ?></p>
            </div>
            <div class="footer-col">
                <h2 class="footer-heading">Explore</h2>
                <ul>
                    <?php foreach ($nav_links as $label => $href): ?>
                        <?php if ($label === 'Services') {
                            continue;
                        } ?>
                        <li><a href="<?= htmlspecialchars(url($href)) ?>"><?= htmlspecialchars($label) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php if (!empty($service_nav)): ?>
            <div class="footer-col">
                <h2 class="footer-heading">Services</h2>
                <ul>
                    <?php foreach ($service_nav as $label => $href): ?>
                        <li><a href="<?= htmlspecialchars(url($href)) ?>"><?= htmlspecialchars($label) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            <div class="footer-col">
                <h2 class="footer-heading">Contact</h2>
                <ul>
                    <li><a href="<?= htmlspecialchars($phone_href) ?>"><?= htmlspecialchars($phone) ?></a></li>
                    <li><a href="<?= htmlspecialchars($email_href) ?>"><?= htmlspecialchars($email) ?></a></li>
                    <li>Oakland · Wayne · Macomb</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($company_name) ?>. All rights reserved.</p>
            <p class="footer-credit">
                Powered by
                <a href="https://www.webstarbusinessservices.com/" target="_blank" rel="noopener noreferrer">Webstar Business Services</a>
            </p>
        </div>
    </footer>

    <aside class="mobile-cta" aria-label="Quick contact">
        <a class="mobile-cta__call" href="<?= htmlspecialchars($phone_href) ?>">Call</a>
        <a class="mobile-cta__quote" href="<?= htmlspecialchars($sms_href) ?>">Text a photo</a>
    </aside>

    <script src="<?= htmlspecialchars(url('assets/js/main.js')) ?>?v=<?= (int) @filemtime(dirname(__DIR__) . '/assets/js/main.js') ?>" defer></script>
</body>
</html>
