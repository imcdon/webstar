<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
$currentPage = 'contact';
$config = webstar_config();
$pageTitle = 'Contact — Webstar Business Services';
$pageDescription = 'Contact Webstar Business Services for general inquiries, booking, or project questions.';
$canonicalPath = 'contact.php';

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
if ($reason === 'validation' && $fieldsParam !== '') {
    foreach (explode(',', $fieldsParam) as $field) {
        $field = trim($field);
        if (isset($fieldLabels[$field])) {
            $validationFields[] = $fieldLabels[$field];
        }
    }
}

include __DIR__ . '/library/layout-start.php';
?>
<section class="home-section home-section--night">
    <div class="home-section__inner">
        <div class="landing-content contact-panel">
            <h1 class="home-section__heading">Contact</h1>
            <p class="contact-panel__intro">Have a question, want to talk through a project, or ready to book? Reach out directly or send a general inquiry below.</p>

            <div class="contact-panel__ways">
                <div class="contact-way">
                    <h2 class="contact-way__title">Email</h2>
                    <p class="contact-way__text">The best way to reach me is by email. Send a note anytime and I’ll follow up.</p>
                    <p class="contact-way__action">
                        <a href="mailto:<?php echo webstar_h($config['email']); ?>"><?php echo webstar_h($config['email']); ?></a>
                    </p>
                </div>
                <div class="contact-way">
                    <h2 class="contact-way__title">Book a package</h2>
                    <p class="contact-way__text">Ready to start a project? Choose a package and submit the intake form with your logo, company info, and goals.</p>
                    <p class="contact-way__action">
                        <a class="home-section__btn home-section__btn--primary" href="packages.php">View packages</a>
                    </p>
                </div>
            </div>

            <div class="contact-form-block" id="intake">
                <h2 class="contact-form-block__title">General inquiry</h2>
                <p class="contact-form-block__lead">Use this form for questions, quotes, or anything that isn’t a full package intake yet.</p>

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
                            <p>Form delivery is not configured yet. Please email <a href="mailto:<?php echo webstar_h($config['email']); ?>"><?php echo webstar_h($config['email']); ?></a>.</p>
                        <?php else : ?>
                            <p>We could not send your message right now. Please try again or email <a href="mailto:<?php echo webstar_h($config['email']); ?>"><?php echo webstar_h($config['email']); ?></a>.</p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <form class="intake-form contact-form" method="post" action="library/process-contact.php" novalidate>
                    <div class="schedule-form__honeypot" aria-hidden="true">
                        <label for="contact-website">Website</label>
                        <input type="text" id="contact-website" name="website" tabindex="-1" autocomplete="off">
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
                        <textarea class="schedule-form__input" id="contact-message" name="message" required maxlength="4000" rows="6"></textarea>
                    </div>
                    <div class="intake-form__actions">
                        <button type="submit" class="home-section__btn home-section__btn--primary">Send message</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/library/layout-end.php'; ?>
