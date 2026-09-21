<?php
/*
 * call-button.php - Lake Superior icon + Call Now label.
 * Optional: $call_class (extra classes), $call_label (default Call Now).
 */
$call_class = trim('btn-call ' . ($call_class ?? ''));
$call_label = $call_label ?? 'Call Now';
$lake_src = url('assets/img/textures/lake-superior.png');
?>
<a class="<?= htmlspecialchars($call_class) ?>" href="<?= htmlspecialchars($phone_href) ?>">
    <img class="btn-call-lake" src="<?= htmlspecialchars($lake_src) ?>" alt="" width="160" height="80" decoding="async">
    <span class="btn-call-label"><?= htmlspecialchars($call_label) ?></span>
</a>
<?php
unset($call_class, $call_label, $lake_src);
