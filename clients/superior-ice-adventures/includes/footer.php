<?php
/*
 * footer.php - Site footer, sticky mobile CTA, and closing HTML tags.
 */
?>
    </main>

    <footer class="site-footer">
        <div class="container-wide footer-grid">
            <div class="footer-brand">
                <a class="logo logo-footer" href="<?= htmlspecialchars(url()) ?>">
                    <span class="logo-text"><?= htmlspecialchars($site_name) ?></span>
                </a>
                <?php if ($site_tagline !== ''): ?>
                    <p class="footer-tagline"><?= htmlspecialchars($site_tagline) ?></p>
                <?php endif; ?>
                <?php if ($email !== ''): ?>
                    <p><a href="<?= htmlspecialchars($email_href) ?>"><?= htmlspecialchars($email) ?></a></p>
                <?php endif; ?>
                <?php if (!empty($social['instagram']) || !empty($social['facebook'])): ?>
                    <div class="footer-social">
                        <?php if (!empty($social['instagram'])): ?>
                            <a href="<?= htmlspecialchars($social['instagram']) ?>" target="_blank" rel="noopener noreferrer">Instagram</a>
                        <?php endif; ?>
                        <?php if (!empty($social['facebook'])): ?>
                            <a href="<?= htmlspecialchars($social['facebook']) ?>" target="_blank" rel="noopener noreferrer">Facebook</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="footer-col">
                <h3 class="footer-heading">Services</h3>
                <ul>
                    <?php foreach ($nav_services as $svc): ?>
                        <li><a href="<?= htmlspecialchars(url(ltrim($svc['href'], '/'))) ?>"><?= htmlspecialchars($svc['label']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="footer-col">
                <h3 class="footer-heading">Explore</h3>
                <ul>
                    <?php foreach ($nav_links as $label => $href): ?>
                        <li><a href="<?= htmlspecialchars(url(ltrim($href, '/'))) ?>"><?= htmlspecialchars($label) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="footer-col">
                <h3 class="footer-heading">Contact</h3>
                <ul>
                    <?php if ($phone !== ''): ?>
                        <li><a href="<?= htmlspecialchars($phone_href) ?>"><?= htmlspecialchars($phone) ?></a></li>
                    <?php endif; ?>
                    <li><a href="<?= htmlspecialchars(url('contact/')) ?>">Contact form</a></li>
                    <?php if (($address['city'] ?? '') !== ''): ?>
                        <li><?= htmlspecialchars(trim(($address['city'] ?? '') . ', ' . ($address['region'] ?? ''), ', ')) ?></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div class="container-wide footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars($company_name) ?>. All rights reserved.</p>
        </div>
    </footer>

    <script src="<?= htmlspecialchars(url('assets/js/main.js')) ?>" defer></script>
</body>
</html>
