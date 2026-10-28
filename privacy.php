<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
$config = webstar_config();
$pageTitle = 'Privacy Policy | Webstar';
$pageDescription = 'Privacy policy for Webstar Business Services: what we collect through forms and analytics, how we use it, how long we keep it, and how to contact us about your data.';
$canonicalPath = 'privacy/';
include __DIR__ . '/library/layout-start.php';
?>
<div class="landing-shell">
<section class="landing-content">
    <h1>Privacy Policy</h1>
    <p class="legal-updated">Last updated: <?php echo date('F Y'); ?> · Applies to <?php echo webstar_h($config['domain']); ?> and forms operated by <?php echo webstar_h($config['legal_name']); ?>.</p>

    <h2>Who we are</h2>
    <p><?php echo webstar_h($config['legal_name']); ?> (“Webstar,” “we,” “us”) is a Michigan limited liability company providing web design, development, and marketing services to small businesses. Contact: <a href="mailto:<?php echo webstar_h($config['email']); ?>"><?php echo webstar_h($config['email']); ?></a> · <a href="<?php echo webstar_h(webstar_phone_href()); ?>"><?php echo webstar_h(webstar_phone_display()); ?></a>.</p>

    <h2>Information we collect</h2>
    <h3>Information you give us</h3>
    <ul>
        <li><strong>Consultation booking and contact forms:</strong> name, company, email, phone, the time you book, what you want to talk about, and any logo or file you upload.</li>
        <li><strong>Client Portal:</strong> a username and password we issue to view your project status and staging previews.</li>
        <li><strong>Projects:</strong> business details, content, photos, and account access you share so we can build and launch your site.</li>
    </ul>
    <h3>Information collected automatically</h3>
    <ul>
        <li><strong>Server logs:</strong> IP address, browser type, pages requested, and timestamps, kept by our hosting provider for security and troubleshooting.</li>
        <li><strong>Analytics:</strong> we may use Google Analytics 4 to understand site usage in aggregate. IP addresses are not stored by GA4, and we do not use advertising features or sell audience data.</li>
        <li><strong>Cookies:</strong> essential cookies for the Client Portal session and, if analytics is enabled, Google’s first-party analytics cookies. We do not use advertising cookies.</li>
    </ul>

    <h2>How we use information</h2>
    <ul>
        <li>Respond to your inquiry and provide quotes</li>
        <li>Deliver, support, and invoice the services you request</li>
        <li>Operate the Client Portal and staging previews</li>
        <li>Improve this website and protect it from abuse</li>
        <li>Meet legal, tax, and accounting obligations</li>
    </ul>
    <p>We do not sell personal information and we do not send marketing email without your consent.</p>

    <h2>Who we share it with</h2>
    <p>Only service providers needed to run the business, under their own privacy terms: email delivery (Google Workspace / SMTP), payments (Stripe — we never see or store full card numbers), hosting and DNS (cPanel host, Cloudflare), analytics (Google), file storage (Google Drive), and e-signature (Google Workspace eSignature). We may disclose information if required by law or to protect our rights.</p>

    <h2>How long we keep it</h2>
    <ul>
        <li>Inquiries that do not become projects: up to 12 months</li>
        <li>Client project records, agreements, and invoices: 7 years for tax and legal purposes</li>
        <li>Server logs: per the hosting provider’s rotation, typically 30–90 days</li>
        <li>Client Portal accounts: removed within 90 days of project completion unless you have a Care Plan</li>
    </ul>

    <h2>Security</h2>
    <p>We use HTTPS across the site, password-protected staging and portal access, hashed portal credentials, and keep secrets out of public code repositories. No method of transmission or storage is perfectly secure; if we learn of a breach affecting your data we will notify you without unreasonable delay.</p>

    <h2>Your choices and rights</h2>
    <ul>
        <li>Ask what personal information we hold about you, and request correction or deletion</li>
        <li>Opt out of analytics with the <a href="https://tools.google.com/dlpage/gaoptout" target="_blank" rel="noopener noreferrer">Google Analytics opt-out add-on</a> or browser settings</li>
        <li>Unsubscribe from any non-transactional email using the link in the message</li>
    </ul>
    <p>Email <a href="mailto:<?php echo webstar_h($config['email']); ?>"><?php echo webstar_h($config['email']); ?></a> and we will respond within 30 days.</p>

    <h2>Client websites</h2>
    <p>Websites we build for clients are operated by those clients. Their forms, analytics, and policies are governed by the client’s own privacy policy, not this one, even when Webstar hosts the site under a Care Plan or Website as a Service.</p>

    <h2>Children</h2>
    <p>Our services are for businesses. We do not knowingly collect information from children under 13.</p>

    <h2>Changes</h2>
    <p>We will post updates here with a new “last updated” date. Material changes will be noted on this page for at least 30 days.</p>

    <p class="landing-content__footer-links"><a href="<?php echo webstar_h(webstar_url('')); ?>">Webstar home</a> · <a href="<?php echo webstar_h(webstar_url('terms.php')); ?>">Terms</a> · <a href="<?php echo webstar_h(webstar_url('accessibility.php')); ?>">Accessibility</a></p>
</section>
</div>
<?php include __DIR__ . '/library/layout-end.php'; ?>
