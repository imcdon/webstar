<?php
/*
 * contact/index.php
 */
require __DIR__ . '/../includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$flash_success = $_SESSION['contact_success'] ?? '';
$flash_error = $_SESSION['contact_error'] ?? '';
$form_data = $_SESSION['contact_form'] ?? [];
unset($_SESSION['contact_success'], $_SESSION['contact_error'], $_SESSION['contact_form']);

$page_title = 'Contact | ' . $site_name;
require __DIR__ . '/../includes/header.php';
?>

<section class="page-hero">
    <div class="container-wide">
        <div class="eyebrow text-on-dark-muted">Get in touch</div>
        <h1 class="heading-display text-display-xl text-on-dark">Contact</h1>
        <p class="page-hero-intro"><?= htmlspecialchars($contact_intro) ?></p>
    </div>
</section>

<section class="section section-default">
    <div class="container-wide contact-layout">
        <div class="info-cards">
            <div class="info-card">
                <h3>Phone</h3>
                <p><a href="<?= htmlspecialchars($phone_href) ?>"><?= htmlspecialchars($phone) ?></a></p>
            </div>
            <div class="info-card">
                <h3>Email</h3>
                <p><a href="<?= htmlspecialchars($email_href) ?>"><?= htmlspecialchars($email) ?></a></p>
            </div>
            <div class="info-card">
                <h3>Location</h3>
                <p>
                    <?php if (!empty($address['note'])): ?>
                        <?= htmlspecialchars($address['note']) ?><br>
                    <?php endif; ?>
                    <?php if (!empty($address['line1'])): ?>
                        <?= htmlspecialchars($address['line1']) ?><br>
                    <?php endif; ?>
                    <?= htmlspecialchars(trim(($address['city'] ?? '') . ', ' . ($address['region'] ?? '') . ' ' . ($address['postal_code'] ?? ''), ', ')) ?>
                </p>
            </div>
            <div class="info-card">
                <h3>Hours</h3>
                <?php foreach ($hours as $row): ?>
                    <p><?= htmlspecialchars($row['day']) ?>: <?= htmlspecialchars($row['hours']) ?></p>
                <?php endforeach; ?>
            </div>
        </div>

        <div>
            <?php if ($flash_success): ?>
                <p class="form-success"><?= htmlspecialchars($flash_success) ?></p>
            <?php endif; ?>
            <?php if ($flash_error): ?>
                <p class="form-error"><?= htmlspecialchars($flash_error) ?></p>
            <?php endif; ?>

            <form class="contact-form" method="post" action="<?= htmlspecialchars(url('contact/submit.php')) ?>" novalidate>
                <div class="hp-field" aria-hidden="true">
                    <label for="website">Website</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>

                <label for="name">Name</label>
                <input type="text" id="name" name="name" required value="<?= htmlspecialchars($form_data['name'] ?? '') ?>">

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?= htmlspecialchars($form_data['email'] ?? '') ?>">

                <label for="phone_field">Phone</label>
                <input type="tel" id="phone_field" name="phone" value="<?= htmlspecialchars($form_data['phone'] ?? '') ?>">

                <label for="service">Preferred trip / service</label>
                <select id="service" name="service">
                    <?php foreach ($contact_service_options as $value => $label): ?>
                        <option value="<?= htmlspecialchars($value) ?>"<?= (($form_data['service'] ?? '') === $value) ? ' selected' : '' ?>>
                            <?= htmlspecialchars($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="message">Message</label>
                <textarea id="message" name="message" rows="6" required><?= htmlspecialchars($form_data['message'] ?? '') ?></textarea>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Send message</button>
                    <a class="btn-secondary" href="<?= htmlspecialchars($phone_href) ?>">Or call now</a>
                </div>
            </form>
        </div>
    </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
