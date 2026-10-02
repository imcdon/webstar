</main>
<footer class="ww-footer">
    <div class="ww-footer__inner">
        <p class="ww-footer__brand">Webstar Weddings</p>
        <nav class="ww-footer__nav" aria-label="Weddings sitemap">
            <a href="<?php echo webstar_h(webstar_weddings_url()); ?>">Home</a>
            <a href="<?php echo webstar_h(webstar_weddings_url('services')); ?>">Services</a>
            <a href="<?php echo webstar_h(webstar_weddings_url('examples')); ?>">Examples</a>
            <a href="<?php echo webstar_h(webstar_phone_href()); ?>"><?php echo webstar_h(webstar_phone_display()); ?></a>
            <a href="mailto:weddings@webstarbusinessservices.com">weddings@webstarbusinessservices.com</a>
        </nav>
        <p class="ww-footer__keywords">Wedding website design · Digital wedding invitations · RSVP-ready couple sites · Metro Detroit &amp; beyond</p>
        <a class="ww-footer__back" href="<?php echo webstar_h(webstar_url('')); ?>">Back to Webstar Business Services</a>
        <p class="ww-footer__meta">&copy; <?php echo date('Y'); ?> Webstar Business Services. All rights reserved.</p>
    </div>
</footer>
<script src="<?php echo webstar_h(webstar_url('weddings/assets/weddings.js')); ?>?v=<?php echo (int) @filemtime(__DIR__ . '/../assets/weddings.js'); ?>" defer></script>
</body>
</html>
