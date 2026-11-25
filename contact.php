<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
$currentPage = 'contact';
$config = webstar_config();
$pageTitle = 'Contact Webstar Business Services | Metro Detroit';
$pageDescription = 'Contact Webstar Business Services in Metro Detroit — ask a question, book a free 1-on-1, or call and email. Serving Michigan small businesses.';
$canonicalPath = 'contact/';

// Legacy booking query params → /book/ (hash #book handled in page script)
$legacyBookKeys = ['book_status', 'book_reason', 'book_fields'];
$legacyParams = [];
foreach (array_merge($legacyBookKeys, ['plan']) as $key) {
    if (isset($_GET[$key]) && (string) $_GET[$key] !== '') {
        $legacyParams[$key] = (string) $_GET[$key];
    }
}
$hasLegacyBookStatus = false;
foreach ($legacyBookKeys as $key) {
    if (isset($legacyParams[$key])) {
        $hasLegacyBookStatus = true;
        break;
    }
}
// Old contact?plan=… links meant booking; inquiry status responses stay here
if ($hasLegacyBookStatus || (isset($legacyParams['plan']) && !isset($_GET['status']) && !isset($_GET['reason']))) {
    $target = webstar_url('book.php');
    if ($legacyParams !== []) {
        $target .= '?' . http_build_query($legacyParams);
    }
    header('Location: ' . $target, true, 301);
    exit;
}

$status = isset($_GET['status']) ? (string) $_GET['status'] : '';
$reason = isset($_GET['reason']) ? (string) $_GET['reason'] : '';
$fieldsParam = isset($_GET['fields']) ? (string) $_GET['fields'] : '';
$fieldLabels = [
    'name' => 'Name',
    'email' => 'Email',
    'phone' => 'Phone',
    'message' => 'Message',
];
$validationFields = [];
$planSlug = isset($_GET['plan']) ? (string) $_GET['plan'] : '';
$planPrefill = '';
if ($planSlug !== '') {
    $plan = webstar_offer_by_slug($planSlug);
    if ($plan !== null) {
        $title = (string) ($plan['title'] ?? '');
        $price = (string) ($plan['price'] ?? '');
        $period = trim((string) ($plan['period'] ?? ''));
        if ($period !== '') {
            $planPrefill = 'I’m interested in the ' . $title . ' plan (' . $price . ' ' . $period . '). My website is: ';
        } else {
            $planPrefill = 'I’m interested in the ' . $title . ' (' . $price . '). ';
        }
    }
}
if ($reason === 'validation' && $fieldsParam !== '') {
    foreach (explode(',', $fieldsParam) as $field) {
        $field = trim($field);
        if (isset($fieldLabels[$field])) {
            $validationFields[] = $fieldLabels[$field];
        }
    }
}

$bookUrl = webstar_url('book.php');
include __DIR__ . '/library/layout-start.php';
?>
<section class="home-section home-section--night">
    <div class="home-section__inner">
        <div class="landing-content contact-panel">
            <h1 class="home-section__heading">Contact Webstar</h1>
            <p class="contact-panel__intro">Send a question anytime. Serving <?php echo webstar_h($config['service_area']); ?>.</p>

            <div class="contact-form-block" id="inquiry">
                <?php if ($status === 'success') : ?>
                    <div class="schedule-message schedule-message--success" role="status">
                        <p>Thank you — your message was sent. I’ll get back to you soon.</p>
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
                            <p>Form delivery is not configured yet. Please call <a href="<?php echo webstar_h(webstar_phone_href()); ?>"><?php echo webstar_h(webstar_phone_display()); ?></a> or email <a href="mailto:<?php echo webstar_h($config['email']); ?>"><?php echo webstar_h($config['email']); ?></a>.</p>
                        <?php else : ?>
                            <p>We could not send your message right now. Please try again, call <a href="<?php echo webstar_h(webstar_phone_href()); ?>"><?php echo webstar_h(webstar_phone_display()); ?></a>, or email <a href="mailto:<?php echo webstar_h($config['email']); ?>"><?php echo webstar_h($config['email']); ?></a>.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <form class="intake-form contact-form" method="post" action="<?php echo webstar_h(webstar_url('library/process-contact.php')); ?>" novalidate>
                    <div class="schedule-form__honeypot" aria-hidden="true">
                        <label for="contact-hp">Leave blank</label>
                        <input type="text" id="contact-hp" name="company_url" tabindex="-1" autocomplete="off">
                    </div>
                    <div class="intake-form__field">
                        <label for="contact-name">Name <span class="schedule-form__required">*</span></label>
                        <input class="schedule-form__input" type="text" id="contact-name" name="name" required maxlength="120" autocomplete="name">
                    </div>
                    <div class="intake-form__field">
                        <label for="contact-email">Email <span class="schedule-form__required">*</span></label>
                        <input class="schedule-form__input" type="email" id="contact-email" name="email" required maxlength="254" autocomplete="email">
                    </div>
                    <div class="intake-form__field">
                        <label for="contact-phone">Phone</label>
                        <input class="schedule-form__input" type="tel" id="contact-phone" name="phone" maxlength="30" autocomplete="tel">
                    </div>
                    <div class="intake-form__field">
                        <label for="contact-message">Message <span class="schedule-form__required">*</span></label>
                        <textarea class="schedule-form__input" id="contact-message" name="message" required maxlength="4000" rows="6"><?php echo webstar_h($planPrefill); ?></textarea>
                    </div>
                    <div class="intake-form__actions">
                        <button type="submit" class="home-section__btn home-section__btn--primary">Send message</button>
                    </div>
                </form>
            </div>

            <div class="contact-panel__ways" aria-label="Other ways to reach us">
                <div class="contact-way">
                    <h2 class="contact-way__title">Book a free 1-on-1</h2>
                    <p class="contact-way__text">Ready to talk? Confirm package and price on a 30-minute call.</p>
                    <p class="contact-way__action">
                        <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h($bookUrl); ?>">Choose a time</a>
                    </p>
                </div>
                <div class="contact-way">
                    <h2 class="contact-way__title">Call</h2>
                    <p class="contact-way__text">Prefer to talk it through? Call or text and I’ll follow up as soon as I can.</p>
                    <p class="contact-way__action">
                        <a href="<?php echo webstar_h(webstar_phone_href()); ?>"><?php echo webstar_h(webstar_phone_display()); ?></a>
                    </p>
                </div>
                <div class="contact-way">
                    <h2 class="contact-way__title">Email</h2>
                    <p class="contact-way__text">Send a note anytime—questions about scope, timing, or whether Webstar is a fit.</p>
                    <p class="contact-way__action">
                        <a href="mailto:<?php echo webstar_h($config['email']); ?>"><?php echo webstar_h($config['email']); ?></a>
                    </p>
                </div>
            </div>
            <p class="contact-panel__intake-note">Already had your call? <a href="<?php echo webstar_h(webstar_url('intake.php')); ?>">Open project intake</a>.</p>
        </div>
    </div>
</section>
<script>
(function () {
    if (location.hash === '#book') {
        var q = location.search || '';
        location.replace(<?php echo json_encode($bookUrl, JSON_UNESCAPED_SLASHES); ?> + q);
    }
})();
</script>
<?php include __DIR__ . '/library/layout-end.php'; ?>
