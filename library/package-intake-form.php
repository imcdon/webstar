<?php
$status = $intakeStatus ?? '';
$reason = $intakeReason ?? '';
$fieldsParam = $intakeFields ?? '';
$fieldLabels = [
    'business_name' => 'Business name',
    'business_email' => 'Business email',
    'business_phone' => 'Business phone',
    'contact_phone' => 'Contact phone',
    'business_address' => 'Business address',
    'mailing_address' => 'Mailing address',
    'sell_services' => 'What you sell / services you provide',
    'current_systems' => 'Current systems used',
    'open_to_change' => 'Open to changing software',
    'goals' => 'Business goals',
    'services' => 'Services needed',
];
$validationFields = [];
if ($reason === 'validation' && $fieldsParam !== '') {
    foreach (explode(',', $fieldsParam) as $field) {
        $field = trim($field);
        if (isset($fieldLabels[$field])) {
            $validationFields[] = $fieldLabels[$field];
        }
    }
}
$formId = 'intake-project';
$config = webstar_config();
$prefillPackage = isset($packageSlug) ? (string) $packageSlug : '';
$bookUrl = webstar_url('book.php');
$phoneDisplay = webstar_phone_display();
$phoneHref = webstar_phone_href();

$websiteTiers = [
    'simple-website' => 'Simple Website (3–5 pages, simple, call to book)',
    'business-website' => 'Business Website (5–10 pages, booking/ecommerce installed, custom flyers, banners, and more)',
    'business-growth-website' => 'Business Growth Website (10+ pages, booking/ecommerce, SEO, custom services)',
    'hosting' => 'Hosting (switch to Webstar hosting, free add-ons and changes, routine maintenance and upkeep)',
];
$serviceOptions = [
    'google-business-profile' => 'Google Business Profile (Google Maps)',
    'apple-maps' => 'Apple Maps',
    'chatgpt-business-profile' => 'ChatGPT Business Profile',
    'yelp' => 'Yelp',
    'photography' => 'Photography',
    'social-media' => 'Social Media',
    'ai-agents' => 'AI Agents',
    'business-email' => 'Business Email Connection',
    'google-analytics' => 'Google Analytics',
];
$prefillWebsite = $prefillPackage !== '' && isset($websiteTiers[$prefillPackage]);
?>
<section class="intake-block" id="intake">
    <div class="intake-block__inner">
        <?php if ($status === 'success') : ?>
            <div class="schedule-message schedule-message--success" role="status">
                <p>Thank you — we received your project details and will follow up shortly.</p>
            </div>
        <?php elseif ($status === 'error') : ?>
            <div class="schedule-message schedule-message--error" role="alert">
                <?php if ($reason === 'validation' && $validationFields !== []) : ?>
                    <p>Please check the following and try again:</p>
                    <ul class="schedule-message__list">
                        <?php foreach ($validationFields as $label) : ?>
                            <li><?php echo webstar_h($label); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php elseif ($reason === 'config') : ?>
                    <p>Form delivery is not configured yet. Please call <a href="<?php echo webstar_h($phoneHref); ?>"><?php echo webstar_h($phoneDisplay); ?></a> or email <a href="mailto:<?php echo webstar_h($config['email']); ?>"><?php echo webstar_h($config['email']); ?></a>.</p>
                <?php else : ?>
                    <p>We could not send your request. Please try again, call <a href="<?php echo webstar_h($phoneHref); ?>"><?php echo webstar_h($phoneDisplay); ?></a> or email <a href="mailto:<?php echo webstar_h($config['email']); ?>"><?php echo webstar_h($config['email']); ?></a>.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form class="intake-form schedule-form" method="post" action="<?php echo webstar_h(webstar_url('library/process-package-intake.php')); ?>" novalidate>
            <input type="hidden" name="redirect" value="<?php echo webstar_h($intakeRedirect ?? webstar_url('intake.php')); ?>">

            <div class="schedule-form__honeypot" aria-hidden="true">
                <label for="<?php echo webstar_h($formId); ?>-hp">Company URL</label>
                <input type="text" id="<?php echo webstar_h($formId); ?>-hp" name="company_url" tabindex="-1" autocomplete="off">
            </div>

            <div class="intake-form__grid">
                <div class="intake-form__field schedule-form__field intake-form__field--full">
                    <label for="<?php echo webstar_h($formId); ?>-business-name">Business name <span class="intake-form__optional">(as it appears on filing)</span> <span class="schedule-form__required">*</span></label>
                    <input class="schedule-form__input" type="text" id="<?php echo webstar_h($formId); ?>-business-name" name="business_name" required maxlength="160" autocomplete="organization">
                </div>
                <div class="intake-form__field schedule-form__field">
                    <label for="<?php echo webstar_h($formId); ?>-business-email">Business email <span class="schedule-form__required">*</span></label>
                    <input class="schedule-form__input" type="email" id="<?php echo webstar_h($formId); ?>-business-email" name="business_email" required maxlength="254" autocomplete="email">
                </div>
                <div class="intake-form__field schedule-form__field">
                    <label for="<?php echo webstar_h($formId); ?>-business-phone">Business phone <span class="schedule-form__required">*</span></label>
                    <input class="schedule-form__input" type="tel" id="<?php echo webstar_h($formId); ?>-business-phone" name="business_phone" required maxlength="30" autocomplete="tel">
                </div>
                <div class="intake-form__field schedule-form__field">
                    <label for="<?php echo webstar_h($formId); ?>-contact-phone">Contact phone <span class="schedule-form__required">*</span></label>
                    <input class="schedule-form__input" type="tel" id="<?php echo webstar_h($formId); ?>-contact-phone" name="contact_phone" required maxlength="30" autocomplete="tel">
                </div>
                <div class="intake-form__field schedule-form__field intake-form__field--full">
                    <label for="<?php echo webstar_h($formId); ?>-business-address">Business address <span class="schedule-form__required">*</span></label>
                    <textarea class="schedule-form__input" id="<?php echo webstar_h($formId); ?>-business-address" name="business_address" required maxlength="1000" rows="3" autocomplete="street-address"></textarea>
                </div>
                <div class="intake-form__field schedule-form__field intake-form__field--full">
                    <label for="<?php echo webstar_h($formId); ?>-mailing-address">Mailing address <span class="schedule-form__required">*</span></label>
                    <textarea class="schedule-form__input" id="<?php echo webstar_h($formId); ?>-mailing-address" name="mailing_address" required maxlength="1000" rows="3"></textarea>
                </div>
            </div>

            <p class="intake-form__ein-note"><span class="schedule-form__required">*</span> Additional services may require an EIN/Tax ID.</p>

            <h3 class="intake-form__section-title">Business information</h3>

            <div class="intake-form__grid">
                <div class="intake-form__field schedule-form__field intake-form__field--full">
                    <label for="<?php echo webstar_h($formId); ?>-sell">What do you sell / what services do you provide? <span class="intake-form__optional">(optional)</span></label>
                    <textarea class="schedule-form__input" id="<?php echo webstar_h($formId); ?>-sell" name="sell_services" maxlength="4000" rows="4"></textarea>
                </div>
                <div class="intake-form__field schedule-form__field intake-form__field--full">
                    <label for="<?php echo webstar_h($formId); ?>-systems">Current systems used (Shopify, Calendly, etc.) <span class="intake-form__optional">(optional)</span></label>
                    <textarea class="schedule-form__input" id="<?php echo webstar_h($formId); ?>-systems" name="current_systems" maxlength="2000" rows="3"></textarea>
                </div>
                <fieldset class="intake-form__field schedule-form__field intake-form__field--full intake-form__choice">
                    <legend>Are you open to changing booking/payment/inventory software? <span class="intake-form__optional">(optional)</span></legend>
                    <label class="intake-form__check">
                        <input type="radio" name="open_to_change" value="yes">
                        <span>Yes</span>
                    </label>
                    <label class="intake-form__check">
                        <input type="radio" name="open_to_change" value="no">
                        <span>No</span>
                    </label>
                </fieldset>
                <div class="intake-form__field schedule-form__field intake-form__field--full">
                    <label for="<?php echo webstar_h($formId); ?>-goals">Describe your goals for your business / what you’re trying to accomplish. <span class="intake-form__optional">(optional)</span></label>
                    <textarea class="schedule-form__input" id="<?php echo webstar_h($formId); ?>-goals" name="goals" maxlength="4000" rows="4"></textarea>
                </div>
            </div>

            <hr class="intake-form__divider" />

            <p class="intake-form__services-lead">Pricing and information needed differs for each item provided. Check every box that applies. For quick turnaround times, supply all required info with this form. If you’re unsure, call <a href="<?php echo webstar_h($phoneHref); ?>"><?php echo webstar_h($phoneDisplay); ?></a> or schedule a Google Meet with Ian at <a href="<?php echo webstar_h($bookUrl); ?>">webstarbusinessservices.com/book/</a>.</p>

            <fieldset class="intake-form__services">
                <legend class="intake-form__services-legend">Services needed <span class="schedule-form__required">*</span></legend>

                <label class="intake-form__check">
                    <input type="checkbox" name="services[]" value="website"<?php echo $prefillWebsite ? ' checked' : ''; ?>>
                    <span>Website</span>
                </label>
                <div class="intake-form__nested" role="group" aria-label="Website options">
                    <?php foreach ($websiteTiers as $tierSlug => $tierLabel) : ?>
                        <label class="intake-form__check">
                            <input type="checkbox" name="website_tiers[]" value="<?php echo webstar_h($tierSlug); ?>"<?php echo $prefillPackage === $tierSlug ? ' checked' : ''; ?>>
                            <span><?php echo webstar_h($tierLabel); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>

                <?php foreach ($serviceOptions as $serviceSlug => $serviceLabel) : ?>
                    <label class="intake-form__check">
                        <input type="checkbox" name="services[]" value="<?php echo webstar_h($serviceSlug); ?>">
                        <span><?php echo webstar_h($serviceLabel); ?></span>
                    </label>
                <?php endforeach; ?>
            </fieldset>

            <div class="intake-form__actions schedule-form__actions">
                <button type="submit" class="home-section__btn home-section__btn--primary">Submit project details</button>
            </div>
        </form>
    </div>
</section>
