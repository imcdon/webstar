<?php

declare(strict_types=1);

require dirname(__DIR__) . '/includes/config.php';

use Webstar\Shop\Helpers;

$page_title = "About | DJ's Tackle";
$page_description = "DJ's Tackle was created by fishermen for fishermen — a family-owned LLC in Sterling Heights, MI.";
$canonical_path = '/about/';
require dirname(__DIR__) . '/includes/header.php';
?>
<section class="page-hero">
  <div class="container">
    <h1>About DJ's Tackle</h1>
  </div>
</section>

<section class="section about-block">
  <div class="container about-block-grid">
    <div class="about-block-copy">
      <h2>Family owned</h2>
      <p>DJ's Tackle was founded in 2020, by Dawn and Jeff Campbell (The D and J in DJ's Tackle!). D and J founded DJ's Tackle with the goal of providing high quality and custom tackle for Walleye anglers in the Great Lakes area.</p>
      <p>Founded in Sterling Heights, Michigan, we serve customers online around the USA, and our customers that buy from the stores we supply.</p>
      <p>We offer a premium Walleye fishing tackle, as well as custom ordering. Walleye Anglers that have a particular color in mind, or custom quantities can place a custom order by calling Jeff, or sending us an email through the contact form.</p>
      <p>Thank you for supporting small businesses!</p>
      <div class="about-block-actions">
        <a class="btn btn-primary" href="<?= Helpers::e(url('shop/')) ?>">Shop</a>
        <a class="btn btn-secondary" href="<?= Helpers::e($phone_href) ?>">Call Jeff</a>
        <a class="btn btn-secondary" href="<?= Helpers::e(url('contact/')) ?>">Contact form</a>
      </div>
    </div>
    <div class="about-photo-wrap about-photo-wrap-sm">
      <figure class="about-photo">
        <img
          src="<?= Helpers::e(url('assets/img/kid-painting.jpg')) ?>"
          alt="Teaching the next generation how to paint jigs in the DJ's Tackle workshop"
          width="800"
          height="1000"
          loading="lazy"
        >
      </figure>
      <img
        class="about-badge about-badge-family"
        src="<?= Helpers::e(url('assets/img/badges/family-owned.png')) ?>"
        alt="Family Owned and Operated"
        width="200"
        height="200"
      >
      <img
        class="about-badge about-badge-usa"
        src="<?= Helpers::e(url('assets/img/badges/made-in-usa.png')) ?>"
        alt="Made in USA"
        width="240"
        height="240"
      >
    </div>
  </div>
</section>

<section class="section about-block about-block-alt">
  <div class="container about-block-grid about-block-grid-reverse">
    <div class="about-block-copy">
      <h2>Hand Crafted — USA Made</h2>
      <p>All of our products are made by hand right here in Sterling Heights, Michigan. Our goal is to provide quality baits and lures for our customers fishing the Detroit River, St. Clair River, Lake St. Clair, Lake Erie, and surrounding great lakes and tributaries.</p>
    </div>
    <div class="about-photo-stack">
      <figure class="about-photo">
        <img
          src="<?= Helpers::e(url('assets/img/dude-painting.jpg')) ?>"
          alt="Hand-painting jig heads with powder coat in the workshop"
          width="800"
          height="1000"
          loading="lazy"
        >
      </figure>
      <figure class="about-photo">
        <img
          src="<?= Helpers::e(url('assets/img/dawn-painting.jpg')) ?>"
          alt="Jigs drying on the rack after hand painting"
          width="800"
          height="1000"
          loading="lazy"
        >
      </figure>
    </div>
  </div>

  <div class="container">
    <?php require dirname(__DIR__) . '/includes/partials/about-brand-cards.php'; ?>
  </div>
</section>
<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
