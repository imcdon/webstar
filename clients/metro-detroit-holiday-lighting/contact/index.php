<?php
declare(strict_types=1);
require dirname(__DIR__) . '/includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$success = (string) ($_SESSION['contact_success'] ?? '');
$error = (string) ($_SESSION['contact_error'] ?? '');
$form = $_SESSION['contact_form'] ?? [];
unset($_SESSION['contact_success'], $_SESSION['contact_error'], $_SESSION['contact_form']);

$page_title = 'Contact & Quote | Metro Detroit Holiday Lighting';
$page_description = 'Request a holiday lighting quote for your Metro Detroit home, business, or HOA. Call (248) 525-4715 or send photos by email.';
$canonical_path = '/contact/';
$schema_breadcrumbs = [
    ['name' => 'Home', 'url' => absolute_url('')],
    ['name' => 'Contact', 'url' => absolute_url('contact/')],
];

require dirname(__DIR__) . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <p class="breadcrumbs"><a href="<?= htmlspecialchars(url()) ?>">Home</a> / Contact</p>
        <h1>Get a quote</h1>
        <p>Text or email a few clear photos of the house, storefront, or community entrance. Include your city and what you want lit.</p>
    </div>
</section>

<section class="section">
    <div class="container contact-layout">
        <aside class="contact-card">
            <h2>Fastest path</h2>
            <ul>
                <li><strong>Call / text:</strong> <a href="<?= htmlspecialchars($phone_href) ?>"><?= htmlspecialchars($phone) ?></a></li>
                <li><strong>Email photos:</strong> <a href="<?= htmlspecialchars($email_href) ?>"><?= htmlspecialchars($email) ?></a></li>
                <li>Tell us residential, commercial, or HOA.</li>
                <li>Steven answers and quotes from the photos.</li>
            </ul>
            <p style="margin:1.25rem 0 0;color:var(--ink-muted);"><?= htmlspecialchars($trust_line) ?></p>
        </aside>

        <div class="contact-form">
            <h2>Contact form</h2>
            <?php if ($success !== ''): ?>
                <div class="form-message form-message--ok" role="status"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            <?php if ($error !== ''): ?>
                <div class="form-message form-message--err" role="alert"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" action="<?= htmlspecialchars(url('contact/submit.php')) ?>" novalidate>
                <div class="hp" aria-hidden="true">
                    <label for="website">Website</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>
                <div class="form-field">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required maxlength="120" value="<?= htmlspecialchars((string) ($form['name'] ?? '')) ?>" autocomplete="name">
                </div>
                <div class="form-field">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" maxlength="40" value="<?= htmlspecialchars((string) ($form['phone'] ?? '')) ?>" autocomplete="tel">
                </div>
                <div class="form-field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required maxlength="160" value="<?= htmlspecialchars((string) ($form['email'] ?? '')) ?>" autocomplete="email">
                </div>
                <div class="form-field">
                    <label for="property_type">Property type</label>
                    <select id="property_type" name="property_type">
                        <?php
                        $types = ['' => 'Select one', 'home' => 'Home', 'business' => 'Business / storefront', 'hoa' => 'HOA / community'];
                        $selected = (string) ($form['property_type'] ?? '');
                        foreach ($types as $value => $label):
                            ?>
                            <option value="<?= htmlspecialchars($value) ?>"<?= $selected === $value ? ' selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-field">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" maxlength="80" value="<?= htmlspecialchars((string) ($form['city'] ?? '')) ?>" placeholder="e.g. Troy, Birmingham, Livonia">
                </div>
                <div class="form-field">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" required maxlength="4000" placeholder="What should be lit? Any timeline notes? Mention that you’ll text or email photos."><?= htmlspecialchars((string) ($form['message'] ?? '')) ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send message</button>
            </form>
        </div>
    </div>
</section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
