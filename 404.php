<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
http_response_code(404);
$pageTitle = 'Page not found — Webstar Business Services';
$pageDescription = 'The page you requested could not be found.';
$robotsMeta = 'noindex, follow';
$skipCanonical = true;
include __DIR__ . '/library/layout-start.php';
?>
<section class="home-section home-section--night"><div class="home-section__inner">
<h1 class="home-section__heading">Page not found</h1>
<p class="home-section__textbox"><a href="<?php echo webstar_h(webstar_url('')); ?>">Return home</a> or <a href="<?php echo webstar_h(webstar_url('contact.php')); ?>">contact us</a>.</p>
</div></section>
<?php include __DIR__ . '/library/layout-end.php'; ?>
