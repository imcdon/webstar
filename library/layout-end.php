</main>
<?php if (empty($skipMobileCta)) : ?>
<?php include __DIR__ . '/mobile-cta-bar.php'; ?>
<?php endif; ?>
<?php include __DIR__ . '/footer.php'; ?>
<script src="<?php echo webstar_h(webstar_url('scripts/scripts.js')); ?>?v=<?php echo (int) @filemtime(dirname(__DIR__) . '/scripts/scripts.js'); ?>" defer></script>
<?php if (!empty($extraScripts)) : ?>
<?php echo $extraScripts; ?>
<?php endif; ?>
</body>
</html>
