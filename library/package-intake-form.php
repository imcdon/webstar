<?php
if (!isset($packageSlug) || !isset($packageTitle)) { return; }
$status = $intakeStatus ?? '';
$reason = $intakeReason ?? '';
$fieldsParam = $intakeFields ?? '';
$fieldLabels = [
    'company_name' => 'Company name',
    'contact_name' => 'Contact name',
    'phone' => 'Phone',
    'email' => 'Email',
    'business_summary' => 'Business summary',
    'desired_services' => 'Desired services',
    'logo' => 'Logo',
];
$validationFields = [];
if ($reason === 'validation' && $fieldsParam !== '') {
    foreach (explode(',', $fieldsParam) as $field) {
        $field = trim($field);
        if (isset($fieldLabels[$field])) { $validationFields[] = $fieldLabels[$field]; }
    }
}
$formId = 'intake-' . preg_replace('/[^a-z0-9-]+/i', '-', $packageSlug);
?>
<details class="intake-details" id="intake-<?php echo webstar_h($packageSlug); ?>">
    <summary>Project requirements for <?php echo webstar_h($packageTitle); ?></summary>
    <?php if ($status === 'success') : ?>
        <div class="schedule-message schedule-message--success" role="status"><p>Thank you — we received your project details and will follow up shortly.</p></div>
    <?php elseif ($status === 'error') : ?>
        <div class="schedule-message schedule-message--error" role="alert">
            <?php if ($reason === 'validation' && $validationFields !== []) : ?>
                <p>Please check the following and try again:</p>
                <ul class="schedule-message__list"><?php foreach ($validationFields as $label) : ?><li><?php echo webstar_h($label); ?></li><?php endforeach; ?></ul>
            <?php elseif ($reason === 'config') : ?>
                <p>Form delivery is not configured yet. Please call <a href="tel:<?php echo webstar_h(webstar_config()['phone_tel']); ?>"><?php echo webstar_h(webstar_config()['phone_display']); ?></a> or email <?php echo webstar_h(webstar_config()['email']); ?>.</p>
            <?php else : ?>
                <p>We could not send your request. Please try again or call us directly.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <form class="intake-form schedule-form" method="post" action="library/process-package-intake.php" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="package_slug" value="<?php echo webstar_h($packageSlug); ?>">
        <input type="hidden" name="redirect" value="<?php echo webstar_h($intakeRedirect ?? ''); ?>">
        <div class="schedule-form__honeypot" aria-hidden="true">
            <label for="<?php echo webstar_h($formId); ?>-website">Website</label>
            <input type="text" id="<?php echo webstar_h($formId); ?>-website" name="website" tabindex="-1" autocomplete="off">
        </div>
        <div class="intake-form__field schedule-form__field">
            <label for="<?php echo webstar_h($formId); ?>-company">Company name <span class="schedule-form__required">*</span></label>
            <input class="schedule-form__input" type="text" id="<?php echo webstar_h($formId); ?>-company" name="company_name" required maxlength="160">
        </div>
        <div class="intake-form__field schedule-form__field">
            <label for="<?php echo webstar_h($formId); ?>-contact">Contact name <span class="schedule-form__required">*</span></label>
            <input class="schedule-form__input" type="text" id="<?php echo webstar_h($formId); ?>-contact" name="contact_name" required maxlength="120">
        </div>
        <div class="intake-form__field schedule-form__field">
            <label for="<?php echo webstar_h($formId); ?>-phone">Phone <span class="schedule-form__required">*</span></label>
            <input class="schedule-form__input" type="tel" id="<?php echo webstar_h($formId); ?>-phone" name="phone" required maxlength="30" autocomplete="tel">
        </div>
        <div class="intake-form__field schedule-form__field">
            <label for="<?php echo webstar_h($formId); ?>-email">Email <span class="schedule-form__required">*</span></label>
            <input class="schedule-form__input" type="email" id="<?php echo webstar_h($formId); ?>-email" name="email" required maxlength="254" autocomplete="email">
        </div>
        <div class="intake-form__field schedule-form__field">
            <label for="<?php echo webstar_h($formId); ?>-logo-url">Logo URL (optional if uploading)</label>
            <input class="schedule-form__input" type="url" id="<?php echo webstar_h($formId); ?>-logo-url" name="logo_url" maxlength="500" placeholder="https://">
        </div>
        <div class="intake-form__field schedule-form__field">
            <label for="<?php echo webstar_h($formId); ?>-logo-file">Upload logo (optional, max 5 MB)</label>
            <input class="schedule-form__input" type="file" id="<?php echo webstar_h($formId); ?>-logo-file" name="logo_file" accept=".png,.jpg,.jpeg,.webp,.svg,.pdf">
        </div>
        <div class="intake-form__field schedule-form__field">
            <label for="<?php echo webstar_h($formId); ?>-summary">Business summary <span class="schedule-form__required">*</span></label>
            <textarea class="schedule-form__input" id="<?php echo webstar_h($formId); ?>-summary" name="business_summary" required maxlength="4000"></textarea>
        </div>
        <div class="intake-form__field schedule-form__field">
            <label for="<?php echo webstar_h($formId); ?>-services">Desired services <span class="schedule-form__required">*</span></label>
            <textarea class="schedule-form__input" id="<?php echo webstar_h($formId); ?>-services" name="desired_services" required maxlength="2000"></textarea>
        </div>
        <div class="intake-form__actions schedule-form__actions">
            <button type="submit" class="home-section__btn home-section__btn--primary">Submit project details</button>
        </div>
    </form>
</details>

