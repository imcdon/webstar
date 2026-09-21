/**
 * Superior Ice Adventures — nav, hero carousel, gallery lightbox
 */
(function () {
  "use strict";

  /* ---------- Header scroll state ---------- */
  var header = document.getElementById("site-header");
  var hasHero = document.querySelector(".hero-carousel, .service-hero, .page-hero");

  function updateHeader() {
    if (!header) return;
    var scrolled = window.scrollY > 24;
    header.classList.toggle("is-scrolled", scrolled);
    if (!hasHero) {
      header.classList.add("is-solid");
    }
  }

  updateHeader();
  window.addEventListener("scroll", updateHeader, { passive: true });

  /* ---------- Mobile nav ---------- */
  var toggle = document.getElementById("nav-toggle");
  var menu = document.getElementById("nav-menu");

  if (toggle && menu) {
    toggle.addEventListener("click", function () {
      var open = toggle.getAttribute("aria-expanded") === "true";
      toggle.setAttribute("aria-expanded", String(!open));
      menu.classList.toggle("is-open", !open);
      if (header) header.classList.toggle("is-menu-open", !open);
      document.body.style.overflow = open ? "" : "hidden";
    });

    menu.querySelectorAll("a").forEach(function (link) {
      link.addEventListener("click", function () {
        toggle.setAttribute("aria-expanded", "false");
        menu.classList.remove("is-open");
        if (header) header.classList.remove("is-menu-open");
        document.body.style.overflow = "";
      });
    });

    menu.querySelectorAll(".nav-dropdown-toggle").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var parent = btn.closest(".has-dropdown");
        if (!parent) return;
        var isOpen = parent.classList.contains("is-open");
        menu.querySelectorAll(".has-dropdown").forEach(function (el) {
          el.classList.remove("is-open");
          var t = el.querySelector(".nav-dropdown-toggle");
          if (t) t.setAttribute("aria-expanded", "false");
        });
        if (!isOpen) {
          parent.classList.add("is-open");
          btn.setAttribute("aria-expanded", "true");
        }
      });
    });
  }

  /* ---------- Hero fade carousel ---------- */
  var carousel = document.querySelector(".hero-carousel");
  if (carousel) {
    var slides = Array.prototype.slice.call(carousel.querySelectorAll(".hero-slide"));
    var dots = Array.prototype.slice.call(carousel.querySelectorAll(".hero-dot"));
    var current = 0;
    var timer = null;
    var interval = 9000;

    function goTo(index) {
      if (!slides.length) return;
      slides[current].classList.remove("is-active");
      if (dots[current]) dots[current].classList.remove("is-active");
      current = (index + slides.length) % slides.length;
      slides[current].classList.add("is-active");
      if (dots[current]) dots[current].classList.add("is-active");
    }

    function next() {
      goTo(current + 1);
    }

    function start() {
      stop();
      if (slides.length > 1) {
        timer = setInterval(next, interval);
      }
    }

    function stop() {
      if (timer) {
        clearInterval(timer);
        timer = null;
      }
    }

    dots.forEach(function (dot, i) {
      dot.addEventListener("click", function () {
        goTo(i);
        start();
      });
    });

    carousel.addEventListener("mouseenter", stop);
    carousel.addEventListener("mouseleave", start);
    start();
  }

  /* ---------- Gallery lightbox ---------- */
  var lightbox = document.getElementById("lightbox");
  var lightboxImg = lightbox ? lightbox.querySelector("img") : null;
  var lightboxClose = lightbox ? lightbox.querySelector(".lightbox-close") : null;

  document.querySelectorAll("[data-lightbox]").forEach(function (btn) {
    btn.addEventListener("click", function () {
      if (!lightbox || !lightboxImg) return;
      var src = btn.getAttribute("data-src") || "";
      var alt = btn.getAttribute("data-alt") || "";
      lightboxImg.src = src;
      lightboxImg.alt = alt;
      lightbox.classList.add("is-open");
      document.body.style.overflow = "hidden";
    });
  });

  function closeLightbox() {
    if (!lightbox) return;
    lightbox.classList.remove("is-open");
    document.body.style.overflow = "";
  }

  if (lightboxClose) {
    lightboxClose.addEventListener("click", closeLightbox);
  }

  if (lightbox) {
    lightbox.addEventListener("click", function (e) {
      if (e.target === lightbox) closeLightbox();
    });
  }

  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") closeLightbox();
  });
})();
