<?php
declare(strict_types=1);
require __DIR__ . '/library/helpers.php';
$currentPage = 'about';
$config = webstar_config();
$pageTitle = 'About — Webstar Business Services';
$pageDescription = 'About Webstar Business Services and owner Ian McDonnell. Web design and marketing for Metro Detroit small businesses.';
$canonicalPath = 'about.php';
include __DIR__ . '/library/layout-start.php';
?>
<section class="home-section home-section--night">
    <div class="home-section__inner">
        <div class="landing-content about-panel">
            <h1 class="home-section__heading">About</h1>
            <div class="about-panel__bio">
                <img
                    class="about-panel__photo"
                    src="assets/images/ian-pic.jpg"
                    alt="Ian McDonnell, founder of Webstar Business Services"
                    width="400"
                    height="533"
                >
                <p>Hi, my name is Ian McDonnell and I’m the owner of Webstar Business Services LLC. Webstar is the result of 4 years of small business development and website making in the fishing industry across different niches like retail/ecommerce, blogging, and travel/tourism.</p>
                <p>This first started with North Star Outfitters LLC, an ecommerce website that specialized in clearance and liquidation fishing gear bought from wholesalers and consignment from brick and mortar stores. The business generated more than $15,000 in its first year of operation. This business wasn’t without its problems; over time growing commission fees, growing advertising fees, and growing shipping costs drove profits down. It was time for a pivot.</p>
                <p>After North Star Outfitters, while working in another industry, I made Earth Odyssey LLC. <a href="https://earth-odyssey.com" target="_blank" rel="noopener noreferrer">Earth-odyssey.com</a> is a passion project featuring a blog, a photo gallery, and free tech tools for fishermen and hunters. While it’s still in its early stages and construction, I plan for it to be a long term project and be completely free to the public.</p>
                <p>To date and after I started Earth Odyssey I’ve been privileged to lead and help in several new businesses in the fishing guide industry, several that have grown to be established in the industry. Chrome City Guide Service LLC, a Salmon and Steelhead fishing guide service out of Longview, Washington, Soldotna Fishing Guides – Kenai River LLC out of Soldotna, Alaska, and Superior Ice Adventures LLC out of Marquette, Michigan.</p>
                <p>The goal of Webstar Business Services LLC is to provide simple solutions to growing businesses for low cost. Whether it’s just a marketing package or a broad website with dozens of pages and third party integrations, I pride myself on being the most affordable, the most efficient, and the least headache.</p>
            </div>
            <div class="hero__actions about-panel__actions">
                <a class="home-section__btn home-section__btn--primary" href="contact.php">Contact</a>
                <a class="home-section__btn home-section__btn--secondary" href="tel:<?php echo webstar_h($config['phone_tel']); ?>">Call <?php echo webstar_h($config['phone_display']); ?></a>
            </div>
        </div>
    </div>
</section>

<section class="home-section home-section--deep" id="reviews">
    <div class="home-section__inner">
        <h2 class="home-section__heading">Reviews</h2>
        <div class="reviews-list">
            <article class="review-bubble">
                <p class="review-bubble__stars" aria-label="5 out of 5 stars">★★★★★</p>
                <h3 class="review-bubble__name">Alex Rivera</h3>
                <p class="review-bubble__text">Placeholder review text. Replace this with a real client quote about working with Webstar Business Services.</p>
            </article>
            <article class="review-bubble">
                <p class="review-bubble__stars" aria-label="5 out of 5 stars">★★★★★</p>
                <h3 class="review-bubble__name">Jordan Lee</h3>
                <p class="review-bubble__text">Placeholder review text. Replace this with a real client quote about turnaround time, design quality, or communication.</p>
            </article>
            <article class="review-bubble">
                <p class="review-bubble__stars" aria-label="5 out of 5 stars">★★★★★</p>
                <h3 class="review-bubble__name">Sam Patel</h3>
                <p class="review-bubble__text">Placeholder review text. Replace this with a real client quote about the finished website or marketing package.</p>
            </article>
        </div>
    </div>
</section>
<?php include __DIR__ . '/library/layout-end.php'; ?>
