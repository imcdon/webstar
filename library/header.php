<?php
declare(strict_types=1);
if (!isset($currentPage)) {
    $currentPage = '';
}
if (!isset($headerVariant)) {
    $headerVariant = 'full';
}
$config = webstar_config();
$navItems = [
    ['id' => 'home', 'label' => 'Home', 'href' => webstar_url('index.php')],
    ['id' => 'packages', 'label' => 'Packages', 'href' => webstar_url('packages.php')],
    ['id' => 'examples', 'label' => 'Examples', 'href' => webstar_url('examples.php')],
    ['id' => 'about', 'label' => 'About', 'href' => webstar_url('about.php')],
    ['id' => 'contact', 'label' => 'Contact', 'href' => webstar_url('contact.php')],
    ['id' => 'portal', 'label' => 'Client Portal', 'href' => webstar_url('portal/')],
    ['id' => 'book', 'label' => 'Call Now', 'href' => 'tel:' . $config['phone_tel']],
];
$headerClass = 'site-header';
if ($headerVariant === 'landing') {
    $headerClass .= ' site-header--landing';
}
?>
<header class="<?php echo webstar_h($headerClass); ?>">
    <div class="site-header__brand">
        <a href="<?php echo webstar_h(webstar_url('index.php')); ?>" class="site-header__logo">
            <img src="<?php echo webstar_h(webstar_url('assets/images/star-icon-teal.png')); ?>" alt="" class="site-header__logo-mark" width="28" height="28">
            <span><?php echo webstar_h($config['business_name']); ?></span>
        </a>
        <a href="tel:<?php echo webstar_h($config['phone_tel']); ?>" class="site-header__phone"><?php echo webstar_h($config['phone_display']); ?></a>
    </div>
    <button type="button" class="site-header__menu-toggle" aria-expanded="false" aria-controls="site-nav" aria-label="Open menu">
        <span class="site-header__menu-bar" aria-hidden="true"></span>
        <span class="site-header__menu-bar" aria-hidden="true"></span>
        <span class="site-header__menu-bar" aria-hidden="true"></span>
    </button>
    <nav id="site-nav" class="site-header__nav">
        <ul class="site-header__nav-list">
            <?php foreach ($navItems as $item) : ?>
                <?php
                $isActive = $currentPage === $item['id'];
                $linkClass = 'site-header__nav-link';
                if ($item['id'] === 'book') {
                    $linkClass .= ' site-header__nav-link--cta';
                }
                if ($isActive) {
                    $linkClass .= ' is-active';
                }
                ?>
                <li class="site-header__nav-item">
                    <a href="<?php echo webstar_h($item['href']); ?>" class="<?php echo webstar_h($linkClass); ?>" <?php echo $isActive ? 'aria-current="page"' : ''; ?>><?php echo webstar_h($item['label']); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</header>
