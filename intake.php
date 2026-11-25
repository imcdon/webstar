<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
$currentPage = 'intake';
$config = webstar_config();
$pageTitle = 'Project Intake | Webstar';
$pageDescription = 'Submit project details for your Webstar website — business info and the services you need.';
$canonicalPath = 'intake/';
$robotsMeta = 'noindex, follow';

$intakeStatus = isset($_GET['intake_status']) ? (string) $_GET['intake_status'] : '';
$intakeReason = isset($_GET['intake_reason']) ? (string) $_GET['intake_reason'] : '';
$intakeFields = isset($_GET['intake_fields']) ? (string) $_GET['intake_fields'] : '';

$packageSlug = isset($_GET['package']) ? trim((string) $_GET['package']) : '';
$intakeRedirect = webstar_url('intake.php') . ($packageSlug !== '' ? '?package=' . rawurlencode($packageSlug) : '');

include __DIR__ . '/library/layout-start.php';
?>
<section class="home-section home-section--night">
    <div class="home-section__inner">
        <div class="landing-content contact-panel">
            <h1 class="home-section__heading">Project intake</h1>
            <p class="contact-panel__intro">This form is used to obtain information about your business relevant to making a new website, making accounts for services, and finding out what services you require.</p>

            <?php include __DIR__ . '/library/package-intake-form.php'; ?>
        </div>
    </div>
</section>
<?php include __DIR__ . '/library/layout-end.php'; ?>
