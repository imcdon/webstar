<?php
declare(strict_types=1);
if (webstar_phone_display() === '') {
    return;
}
?>
<aside class="mobile-cta-bar" aria-label="Quick actions">
    <a class="mobile-cta-bar__btn mobile-cta-bar__btn--call" href="<?php echo webstar_h(webstar_phone_href()); ?>">Call</a>
    <a class="mobile-cta-bar__btn mobile-cta-bar__btn--packages" href="<?php echo webstar_h(webstar_url('packages.php')); ?>">Packages</a>
</aside>
