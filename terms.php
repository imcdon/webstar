<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
$config = webstar_config();
$pageTitle = 'Terms of Service | Webstar';
$pageDescription = 'Terms of service for Webstar Business Services website packages, Website as a Service, and Care Plans: pricing, payment, ownership, warranty, hosting, and liability.';
$canonicalPath = 'terms/';
include __DIR__ . '/library/layout-start.php';
?>
<div class="landing-shell">
<section class="landing-content">
    <h1>Terms of Service</h1>
    <p class="legal-updated">Last updated: <?php echo date('F Y'); ?> · <?php echo webstar_h($config['legal_name']); ?>, Michigan. These terms summarize how we work; the signed Service Agreement for your project controls if anything differs.</p>

    <h2>1. Services and pricing</h2>
    <p>We offer three one-time website packages (Simple Website, Business Website, Business Growth Website), a monthly Website as a Service plan, and optional monthly Care Plans. Published prices are ranges or starting points; the exact fee for your project is fixed in writing in your Service Agreement before work starts. Turnaround times are estimates that begin once we have complete intake information, a signed agreement, and the first payment.</p>

    <h2>2. Payment</h2>
    <ul>
        <li><strong>One-time packages:</strong> 50% deposit at signing and 50% when the build is complete and ready for your review, or pay in full at signing. We deploy after the balance clears. Business Growth projects may use a written milestone schedule.</li>
        <li><strong>Website as a Service:</strong> $150 per month by Stripe autopay with a 6-month minimum term. The first payment starts the build. If you cancel before month 6, the remaining minimum-term payments are due before site files transfer.</li>
        <li><strong>Care Plans:</strong> month-to-month by Stripe autopay; cancel with 30 days’ written notice.</li>
        <li>Invoices unpaid 15 days past due may accrue a late fee of 1.5% per month and work may pause. A payment method stays on file until a project is paid in full.</li>
        <li>Fees exclude third-party costs (domain registration, your own hosting, stock photos, booking software, printing) unless your agreement says they are included.</li>
    </ul>

    <h2>3. Ownership</h2>
    <p>Your domain is registered in your name and is always yours. You own the final website and design files on full payment of a one-time package, or at the end of the 6-month minimum term on Website as a Service. Google Business Profile, Analytics, and Search Console accounts are created with you as owner. Webstar retains ownership of its pre-existing tools and code libraries and licenses them to you for use in your site. We may show completed work in our portfolio unless you ask us not to before launch.</p>

    <h2>4. Revisions and change orders</h2>
    <p>Every package includes two rounds of reasonable revisions. Work outside the written scope — new pages, features, integrations, or a change of direction — is handled by a simple written change order (email is fine) with a price and timeline before we start it.</p>

    <h2>5. Post-launch warranty</h2>
    <p>For 30 days after launch we fix, at no charge, anything in scope that does not work as delivered (broken links, form errors, layout defects). The warranty does not cover changes you or third parties make, hosting outages, third-party service changes, or new requests.</p>

    <h2>6. Hosting and handoff</h2>
    <p>By default we deploy to a hosting account in your name and you are responsible for hosting, renewals, backups, and updates after handoff. Under a Care Plan or Website as a Service the site runs on Webstar’s managed server and we handle hosting, backups, SSL, monitoring, and updates per the plan. On cancellation we provide a complete export of your site and help point your domain to its new home.</p>

    <h2>7. Your responsibilities</h2>
    <p>You provide accurate business information and content you have the right to use, timely feedback (target three business days), and access to accounts needed for the work. You are responsible for the legality of your content, for customer data collected by your site, and for completing Google’s verification steps.</p>

    <h2>8. Accessibility and compliance</h2>
    <p>We build to WCAG 2.1 AA practices (see our <a href="<?php echo webstar_h(webstar_url('accessibility.php')); ?>">accessibility statement</a>). We do not provide legal advice and do not warrant that any site meets a specific legal standard; content you add after handoff is your responsibility.</p>

    <h2>9. No guaranteed results</h2>
    <p>We set up and measure Google services properly, but we do not guarantee search rankings, review counts, traffic, leads, or revenue.</p>

    <h2>10. Confidentiality</h2>
    <p>Each party keeps the other’s non-public business information confidential and uses it only for the project. Credentials are shared through secure channels and never stored in public repositories.</p>

    <h2>11. Limitation of liability and indemnity</h2>
    <p>To the fullest extent permitted by law, Webstar’s total liability is limited to the fees you paid for the project or, for monthly plans, the fees paid in the preceding six months. Neither party is liable for indirect or consequential damages. Each party indemnifies the other against third-party claims arising from its own content, breach, or negligence.</p>

    <h2>12. Termination</h2>
    <p>Either party may terminate for material breach not cured within 10 days of written notice. If you end a project for convenience after work begins, you pay for work performed and non-refundable third-party costs. Website as a Service minimum-term and Care Plan notice rules in Section 2 apply.</p>

    <h2>13. Force majeure, assignment, governing law</h2>
    <p>Neither party is liable for delays caused by events beyond reasonable control (outages, natural events, third-party platform changes). Neither party may assign the agreement without consent, except Webstar may use subcontractors under the same confidentiality terms. These terms are governed by Michigan law; venue is the state or federal courts serving Oakland County, Michigan. Both parties agree to try good-faith negotiation before filing a claim.</p>

    <h2>14. Contact</h2>
    <p><?php echo webstar_h($config['legal_name']); ?> · <a href="mailto:<?php echo webstar_h($config['email']); ?>"><?php echo webstar_h($config['email']); ?></a> · <a href="<?php echo webstar_h(webstar_phone_href()); ?>"><?php echo webstar_h(webstar_phone_display()); ?></a> · <?php echo webstar_h($config['service_area']); ?>.</p>

    <p class="landing-content__footer-links"><a href="<?php echo webstar_h(webstar_url('')); ?>">Webstar home</a> · <a href="<?php echo webstar_h(webstar_url('privacy.php')); ?>">Privacy</a> · <a href="<?php echo webstar_h(webstar_url('accessibility.php')); ?>">Accessibility</a></p>
</section>
</div>
<?php include __DIR__ . '/library/layout-end.php'; ?>
