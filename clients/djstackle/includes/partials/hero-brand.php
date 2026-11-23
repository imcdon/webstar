<?php

declare(strict_types=1);

use Webstar\Shop\Helpers;
?>
<section class="hero-brand" aria-label="DJ's Tackle">
  <div class="hero-brand__media" aria-hidden="true">
    <img
      src="<?= Helpers::e(url('assets/img/hero-home.jpg')) ?>"
      alt=""
      width="1024"
      height="682"
      fetchpriority="high"
      decoding="async"
    >
  </div>
  <div class="hero-brand__shade" aria-hidden="true"></div>
  <div class="container hero-brand__inner">
    <img
      class="hero-brand__logo"
      src="<?= Helpers::e(url('assets/img/logo-full.png')) ?>"
      alt="DJ's Tackle — Gettin' Jiggy With It"
      width="220"
      height="224"
    >
    <h1 class="hero-brand__title">Chip-free jigs for the Detroit River</h1>
    <p class="hero-brand__lead">Hand-painted walleye tackle from our Sterling Heights workshop — ready for the D, St. Clair, and Erie.</p>
    <div class="hero-brand__actions">
      <a class="btn btn-primary" href="<?= Helpers::e(url('shop/')) ?>">Shop tackle</a>
      <a class="btn btn-secondary" href="<?= Helpers::e($phone_href) ?>">Call Jeff</a>
    </div>
  </div>
</section>
