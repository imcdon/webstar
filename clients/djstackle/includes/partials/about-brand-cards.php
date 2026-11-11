<?php

declare(strict_types=1);

use Webstar\Shop\Helpers;

/** @var string $about_brands_heading Defaults when not set */
$about_brands_heading = $about_brands_heading ?? "Other Hand Made Brands at DJ's Tackle";
?>
<div class="about-brands<?= !empty($about_brands_compact) ? ' about-brands--compact' : '' ?>">
  <h3 class="about-brands-heading"><?= Helpers::e($about_brands_heading) ?></h3>
  <div class="about-brand-grid">
    <article class="about-brand-card">
      <a class="about-brand-logo" href="https://ripdipfishing.com/" target="_blank" rel="noopener noreferrer">
        <img
          src="<?= Helpers::e(url('assets/img/brands/rip-dip-fishing.jpg')) ?>"
          alt="Rip Dip Fishing logo"
          width="200"
          height="196"
          loading="lazy"
        >
      </a>
      <div class="about-brand-copy">
        <h4><a href="https://ripdipfishing.com/" target="_blank" rel="noopener noreferrer">Rip Dip Fishing</a></h4>
        <p>Rip Dip Fishing is a small soft plastics company out of Commerce, Michigan specializing in custom colors and durable plastics for Walleye anglers jigging the Detroit and St. Clair River.</p>
        <div class="about-brand-actions">
          <a class="btn btn-primary" href="<?= Helpers::e(url('shop/?category=walleye-jigs')) ?>">Shop Rip Dip</a>
          <a class="about-brand-link" href="https://ripdipfishing.com/" target="_blank" rel="noopener noreferrer">Visit ripdipfishing.com →</a>
        </div>
      </div>
    </article>
    <article class="about-brand-card">
      <a class="about-brand-logo" href="https://arcticspinner.com/" target="_blank" rel="noopener noreferrer">
        <img
          src="<?= Helpers::e(url('assets/img/brands/arctic-spinners.png')) ?>"
          alt="Arctic Spinners logo"
          width="280"
          height="120"
          loading="lazy"
        >
      </a>
      <div class="about-brand-copy">
        <h4><a href="https://arcticspinner.com/" target="_blank" rel="noopener noreferrer">Artic Spinners</a></h4>
        <p>In 1967, fishing guide Jack Wadkins of Bear Lake, Michigan designed the Artic Spinner. For nearly 60 years, Artic Spinners have been a staple for Salmon anglers as well as Pike and Musky fishermen. Now owned and operated by Doug Richardson of Luckey, Ohio.</p>
        <div class="about-brand-actions">
          <a class="btn btn-primary" href="<?= Helpers::e(url('shop/?category=arctic-spinners')) ?>">Shop Artic Spinners</a>
          <a class="about-brand-link" href="https://arcticspinner.com/" target="_blank" rel="noopener noreferrer">Visit arcticspinner.com →</a>
        </div>
      </div>
    </article>
  </div>
</div>
