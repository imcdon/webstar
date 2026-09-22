<?php
declare(strict_types=1);
require dirname(__DIR__) . '/library/portal-auth.php';

portal_start_session();
if (portal_current_username() !== null) {
    header('Location: ' . webstar_url('portal/projects.php'), true, 303);
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = (string) ($_POST['username'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    if (portal_attempt_login($username, $password)) {
        header('Location: ' . webstar_url('portal/projects.php'), true, 303);
        exit;
    }
    $error = 'Invalid username or password.';
}

$currentPage = 'portal';
$pageTitle = 'Client Portal Login — Webstar Business Services';
$pageDescription = 'Log in to the Webstar client portal to view your projects in progress.';
$canonicalPath = 'portal/login.php';
$robotsMeta = 'noindex, nofollow';
include dirname(__DIR__) . '/library/layout-start.php';
?>
<section class="home-section home-section--night">
    <div class="home-section__inner">
        <div class="landing-content portal-panel">
            <h1 class="home-section__heading">Client Portal</h1>
            <p class="portal-panel__lead">Sign in to view your websites in progress.</p>

            <?php if ($error !== '') : ?>
                <div class="schedule-message schedule-message--error" role="alert">
                    <p><?php echo webstar_h($error); ?></p>
                </div>
            <?php endif; ?>

            <form class="intake-form portal-login-form" method="post" action="" novalidate>
                <div class="intake-form__field">
                    <label for="portal-username">Username</label>
                    <input class="schedule-form__input" type="text" id="portal-username" name="username" required maxlength="80" autocomplete="username" autofocus>
                </div>
                <div class="intake-form__field">
                    <label for="portal-password">Password</label>
                    <input class="schedule-form__input" type="password" id="portal-password" name="password" required maxlength="200" autocomplete="current-password">
                </div>
                <div class="intake-form__actions">
                    <button type="submit" class="home-section__btn home-section__btn--primary">Log in</button>
                </div>
            </form>
        </div>
    </div>
</section>
<?php include dirname(__DIR__) . '/library/layout-end.php'; ?>
