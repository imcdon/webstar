<?php
declare(strict_types=1);
require dirname(__DIR__) . '/library/portal-auth.php';

portal_require_login();
$client = portal_current_client();
$projects = portal_client_projects();
$isAdmin = portal_is_admin();
$displayName = (string) ($client['display_name'] ?? 'Client');
$heading = $isAdmin ? 'All Projects' : 'My Projects';

$currentPage = 'portal';
$pageTitle = $heading . ' — Webstar Business Services';
$pageDescription = $isAdmin
    ? 'All Webstar client staging projects.'
    : 'Your Webstar projects in progress.';
$canonicalPath = 'portal/projects.php';
$robotsMeta = 'noindex, nofollow';
include dirname(__DIR__) . '/library/layout-start.php';
?>
<section class="home-section home-section--night">
    <div class="home-section__inner">
        <div class="portal-projects">
            <div class="portal-projects__header">
                <div>
                    <h1 class="home-section__heading"><?php echo webstar_h($heading); ?></h1>
                    <p class="portal-projects__welcome">Welcome, <?php echo webstar_h($displayName); ?>.</p>
                </div>
                <a class="home-section__btn home-section__btn--secondary" href="<?php echo webstar_h(webstar_url('portal/logout.php')); ?>">Log out</a>
            </div>

            <?php if ($projects === []) : ?>
                <p class="home-section__textbox"><?php echo $isAdmin ? 'No client projects are registered yet.' : 'No projects are assigned to this account yet.'; ?></p>
            <?php else : ?>
                <div class="portal-project-grid">
                    <?php foreach ($projects as $project) :
                        $preview = portal_project_preview_url($project);
                        $clientLabel = trim((string) ($project['client_name'] ?? ''));
                        ?>
                        <article class="portal-project-card">
                            <p class="portal-project-card__status"><?php echo webstar_h((string) ($project['status'] ?? 'In progress')); ?></p>
                            <?php if ($isAdmin && $clientLabel !== '') : ?>
                                <p class="portal-project-card__client"><?php echo webstar_h($clientLabel); ?></p>
                            <?php endif; ?>
                            <h2 class="portal-project-card__title"><?php echo webstar_h((string) ($project['title'] ?? 'Project')); ?></h2>
                            <p class="portal-project-card__meta">Staging preview hosted by Webstar until launch.</p>
                            <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h($preview); ?>" target="_blank" rel="noopener noreferrer">Open preview</a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php include dirname(__DIR__) . '/library/layout-end.php'; ?>
