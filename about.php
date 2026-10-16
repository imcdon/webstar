<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
$currentPage = 'about';
$config = webstar_config();
$pageTitle = 'About Ian McDonnell & Webstar | Metro Detroit Web Design';
$pageDescription = 'Meet Ian McDonnell, founder of Webstar Business Services. Custom websites with Google Business Profile, Analytics, and Search Console setup for Metro Detroit and Michigan small businesses.';
$canonicalPath = 'about/';
include __DIR__ . '/library/layout-start.php';
?>
<section class="home-section home-section--night">
    <div class="home-section__inner">
        <div class="landing-content about-panel">
            <h1 class="home-section__heading">About Webstar</h1>
            <p class="contact-panel__intro">Webstar Business Services LLC helps small businesses in <?php echo webstar_h($config['service_area']); ?> launch custom websites that show up on Google — paid once and owned outright, or month-to-month with Website as a Service. Either way, your domain and files are yours.</p>
            <div class="about-panel__bio">
                <?php echo webstar_picture('assets/images/ian-pic.JPG', [
                    'alt' => 'Ian McDonnell, founder of Webstar Business Services',
                    'class' => 'about-panel__photo',
                    'width' => 400,
                    'height' => 533,
                    'loading' => 'lazy',
                ]); ?>
                <p>Hi, my name is Ian McDonnell and I’m the owner of Webstar Business Services LLC. Webstar is the result of 4 years of small business development and website making in the fishing industry across different niches like retail/ecommerce, blogging, and travel/tourism.</p>
                <p>This first started with North Star Outfitters LLC, an ecommerce website that specialized in clearance and liquidation fishing gear bought from wholesalers and consignment from brick and mortar stores. The business generated more than $15,000 in its first year of operation. This business wasn’t without its problems; over time growing commission fees, growing advertising fees, and growing shipping costs drove profits down. It was time for a pivot.</p>
                <p>After North Star Outfitters, while working in another industry, I made Earth Odyssey LLC. <a href="https://earth-odyssey.com" target="_blank" rel="noopener noreferrer">Earth-odyssey.com</a> is a passion project featuring a blog, a photo gallery, and free tech tools for fishermen and hunters. While it’s still in its early stages and construction, I plan for it to be a long term project and be completely free to the public.</p>
                <p>To date and after I started Earth Odyssey I’ve been privileged to lead and help in several new businesses in the fishing guide industry, several that have grown to be established in the industry. Chrome City Guide Service LLC, a Salmon and Steelhead fishing guide service out of Longview, Washington, Soldotna Fishing Guides – Kenai River LLC out of Soldotna, Alaska, and Superior Ice Adventures LLC out of Marquette, Michigan.</p>
                <p>The goal of Webstar Business Services LLC is to provide simple, well-built solutions to growing businesses at a fair price. Whether it’s a three-page site with Google Business Profile or a broad website with dozens of pages and third-party integrations, I pride myself on clear scope, fast turnaround, and the least headache — with Google setup, a 30-day fix window, and full ownership included on every project.</p>
            </div>
            <div class="about-panel__actions package-page__actions">
                <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h(webstar_url('packages.php')); ?>">View packages</a>
                <a class="home-section__btn home-section__btn--secondary" href="<?php echo webstar_h(webstar_url('contact.php')); ?>">Contact</a>
            </div>
        </div>
    </div>
</section>

<section class="home-section home-section--deep" id="proof">
    <div class="home-section__inner">
        <h2 class="home-section__heading">Proof</h2>
        <p class="home-section__textbox">Real projects and places—not fabricated reviews. Webstar’s work spans outdoor and guide businesses plus Michigan-rooted products.</p>
        <ul class="about-proof-list">
            <li><strong>Cities &amp; regions:</strong> Metro Detroit and Michigan; client work also in Longview, WA.</li>
            <li><strong>Industries:</strong> Fishing and ice-fishing guide services, outdoor outfitters, and local service businesses.</li>
            <li><strong>Named live site:</strong>
                <a href="https://www.chromecityguideservicellc.com" target="_blank" rel="noopener noreferrer">Chrome City Guide Service</a> (Longview, WA). Additional client sites are listed on <a href="<?php echo webstar_h(webstar_url('examples.php')); ?>">Examples</a> when they launch.
            </li>
            <li><strong>Business:</strong> Michigan LLC · written agreements on every project · Stripe invoicing · <a href="<?php echo webstar_h(webstar_url('process.php')); ?>">published process</a> · <a href="<?php echo webstar_h(webstar_url('accessibility.php')); ?>">accessibility statement</a>.</li>
        </ul>
        <p class="home-section__textbox">See live sites on the Examples page. We publish only client quotes approved in writing — until then we stick to verifiable work.</p>
    </div>
</section>

<?php include __DIR__ . '/library/testimonials-block.php'; ?>

<section class="home-section home-section--night" id="client-work">
    <div class="home-section__inner">
        <h2 class="home-section__heading">Client work</h2>
        <p class="home-section__textbox">See live sites and the types of projects Webstar delivers on the Examples page. When you’re ready, pick a package and submit the intake form to start.</p>
        <div class="package-page__actions">
            <a class="home-section__btn home-section__btn--primary" href="<?php echo webstar_h(webstar_url('examples.php')); ?>">See examples</a>
            <a class="home-section__btn home-section__btn--secondary" href="<?php echo webstar_h(webstar_package_url('simple-website')); ?>">Start with Simple Website</a>
        </div>
    </div>
</section>
<?php include __DIR__ . '/library/layout-end.php'; ?>
