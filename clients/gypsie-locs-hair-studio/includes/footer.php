<?php

declare(strict_types=1);
?>
    </main>

    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-brand">
                <p class="footer-brand__name">Gypsie Locs</p>
                <p class="footer-brand__tag"><?= htmlspecialchars($site_tagline) ?></p>
            </div>
            <div class="footer-meta">
                <p>
                    <?= htmlspecialchars($address['line1']) ?><br>
                    <?= htmlspecialchars($address['line2']) ?><br>
                    <?= htmlspecialchars($address['city'] . ', ' . $address['region'] . ' ' . $address['postal_code']) ?>
                </p>
                <p><a href="<?= htmlspecialchars($phone_href) ?>"><?= htmlspecialchars($phone) ?></a></p>
                <p><a href="<?= htmlspecialchars($booking_url) ?>" rel="noopener noreferrer">Book on GlossGenius</a></p>
            </div>
            <nav class="footer-nav" aria-label="Footer">
                <ul>
                    <?php foreach ($nav_links as $label => $href): ?>
                        <li><a href="<?= htmlspecialchars(url($href)) ?>"><?= htmlspecialchars($label) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div>
        <p class="footer-fine"><?= htmlspecialchars($policy_note) ?></p>
    </footer>

    <div class="mobile-cta" role="region" aria-label="Quick book">
        <a class="btn btn--primary" href="<?= htmlspecialchars($booking_url) ?>" rel="noopener noreferrer">Book online</a>
        <a class="btn btn--ghost" href="<?= htmlspecialchars($phone_href) ?>">Call</a>
    </div>

    <script src="<?= htmlspecialchars(url('assets/js/main.js')) ?>?v=<?= (int) @filemtime(dirname(__DIR__) . '/assets/js/main.js') ?>" defer></script>
</body>
</html>
