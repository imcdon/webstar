<?php
?>
    </main>

    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-brand">
                <a class="logo logo-footer" href="<?= htmlspecialchars(url()) ?>">
                    <span class="logo-text"><?= htmlspecialchars($site_name) ?></span>
                </a>
                <p class="footer-tagline"><?= htmlspecialchars($site_tagline) ?></p>
                <p class="footer-trust"><?= htmlspecialchars($trust_line) ?></p>
            </div>
            <div class="footer-contact">
                <a class="footer-phone" href="<?= htmlspecialchars($phone_href) ?>"><?= htmlspecialchars($phone) ?></a>
                <div class="footer-social">
                    <?php if (!empty($social['instagram'])): ?>
                        <a href="<?= htmlspecialchars($social['instagram']) ?>" target="_blank" rel="noopener noreferrer">Instagram</a>
                    <?php endif; ?>
                    <?php if (!empty($social['facebook'])): ?>
                        <a href="<?= htmlspecialchars($social['facebook']) ?>" target="_blank" rel="noopener noreferrer">Facebook</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <p class="footer-copy">&copy; <?= date('Y') ?> <?= htmlspecialchars($company_name) ?>. Guided by <?= htmlspecialchars($guide_name) ?>.</p>
    </footer>

    <div class="mobile-cta" aria-label="Call">
        <a class="mobile-cta__btn" href="<?= htmlspecialchars($phone_href) ?>">Call <?= htmlspecialchars($phone) ?></a>
    </div>

    <script src="<?= htmlspecialchars(url('assets/js/main.js')) ?>" defer></script>
</body>
</html>
