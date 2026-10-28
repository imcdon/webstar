<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
$config = webstar_config();
$pageTitle = 'Accessibility Statement | Webstar';
$pageDescription = 'Webstar Business Services accessibility statement: our WCAG 2.1 AA target for this site and the websites we build, and how to report an accessibility issue.';
$canonicalPath = 'accessibility/';
include __DIR__ . '/library/layout-start.php';
?>
<div class="landing-shell">
<section class="landing-content">
    <h1>Accessibility statement</h1>
    <p class="legal-updated">Last updated: <?php echo date('F Y'); ?></p>

    <p><?php echo webstar_h($config['legal_name']); ?> wants everyone to be able to use this website and the websites we build for clients. Our target for both is conformance with the <a href="https://www.w3.org/TR/WCAG21/" target="_blank" rel="noopener noreferrer">Web Content Accessibility Guidelines (WCAG) 2.1 Level AA</a>.</p>

    <h2>What we do</h2>
    <ul>
        <li>Semantic HTML with a logical heading order, landmarks, and labelled forms</li>
        <li>Keyboard-operable navigation, menus, and forms with visible focus states</li>
        <li>Text alternatives for meaningful images and decorative images hidden from assistive technology</li>
        <li>Color contrast that meets AA ratios for text and interactive elements</li>
        <li>Layouts that reflow on small screens and respect browser zoom up to 200%</li>
        <li>Reduced-motion preferences honored where animation is used</li>
    </ul>

    <h2>Known limitations</h2>
    <p>Third-party embeds (maps, booking widgets, video players) may not fully meet AA on their own. Where we use them we provide an alternative path such as a phone number, address, or direct link. Client-supplied content (photos, PDFs, copy) is published as provided unless remediation is in scope.</p>

    <h2>Client websites</h2>
    <p>Every Webstar build is checked against the practices above before launch. Accessibility conformance for a client site is a shared responsibility: Webstar delivers an accessible foundation; the client is responsible for content they add after handoff unless a Care Plan covers it. Webstar does not provide legal advice about accessibility obligations under the ADA or other laws.</p>

    <h2>Report a problem</h2>
    <p>If you have difficulty using this site or a site we built, tell us and we will fix it or provide the information another way.</p>
    <ul>
        <li>Email: <a href="mailto:<?php echo webstar_h($config['email']); ?>"><?php echo webstar_h($config['email']); ?></a></li>
        <li>Phone or text: <a href="<?php echo webstar_h(webstar_phone_href()); ?>"><?php echo webstar_h(webstar_phone_display()); ?></a></li>
    </ul>
    <p>We aim to respond within two business days.</p>

    <p class="landing-content__footer-links"><a href="<?php echo webstar_h(webstar_url('')); ?>">Webstar home</a> · <a href="<?php echo webstar_h(webstar_url('privacy.php')); ?>">Privacy</a> · <a href="<?php echo webstar_h(webstar_url('terms.php')); ?>">Terms</a></p>
</section>
</div>
<?php include __DIR__ . '/library/layout-end.php'; ?>
