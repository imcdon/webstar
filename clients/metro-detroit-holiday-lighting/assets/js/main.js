(function () {
  var toggle = document.querySelector(".nav-toggle");
  var nav = document.getElementById("site-nav");
  if (toggle && nav) {
    toggle.addEventListener("click", function () {
      var open = nav.classList.toggle("is-open");
      toggle.setAttribute("aria-expanded", open ? "true" : "false");
      toggle.setAttribute("aria-label", open ? "Close menu" : "Open menu");
      if (!open) {
        document.querySelectorAll(".nav-item--dropdown.is-open").forEach(function (item) {
          item.classList.remove("is-open");
          var btn = item.querySelector(".nav-dropdown-toggle");
          if (btn) btn.setAttribute("aria-expanded", "false");
        });
      }
    });
  }

  document.querySelectorAll(".nav-dropdown-toggle").forEach(function (btn) {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      var item = btn.closest(".nav-item--dropdown");
      if (!item) return;
      var open = item.classList.toggle("is-open");
      btn.setAttribute("aria-expanded", open ? "true" : "false");
    });
  });

  document.querySelectorAll(".faq-item > button").forEach(function (btn) {
    btn.addEventListener("click", function () {
      var item = btn.parentElement;
      var open = item.classList.toggle("is-open");
      btn.setAttribute("aria-expanded", open ? "true" : "false");
    });
  });

  initScrollStage();

  function initScrollStage() {
    var stage = document.querySelector("[data-scroll-stage]");
    if (!stage) return;

    var frames = Array.prototype.slice.call(stage.querySelectorAll(".scroll-stage__frame"));
    var dots = Array.prototype.slice.call(stage.querySelectorAll(".scroll-stage__dot"));
    var caption = stage.querySelector("[data-scroll-caption]");
    if (!frames.length) return;

    var reduce =
      window.matchMedia &&
      window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    if (reduce) {
      stage.classList.add("scroll-stage--reduced");
      frames.forEach(function (frame, i) {
        frame.classList.toggle("is-active", i === 0);
        frame.style.opacity = i === 0 ? "1" : "0";
        frame.style.transform = "none";
        frame.style.clipPath = "none";
      });
      dots.forEach(function (dot, i) {
        dot.classList.toggle("is-active", i === 0);
      });
      return;
    }

    var ticking = false;
    var lastIndex = 0;

    function update() {
      ticking = false;
      var rect = stage.getBoundingClientRect();
      var track = stage.offsetHeight - window.innerHeight;
      if (track <= 0) track = 1;
      var scrolled = Math.min(Math.max(-rect.top, 0), track);
      var progress = scrolled / track;
      var count = frames.length;
      var raw = progress * (count - 1);
      var index = Math.min(count - 1, Math.floor(raw));
      var blend = raw - index;

      frames.forEach(function (frame, i) {
        var opacity = 0;
        var scale = 1.04;
        var wipe = 100;

        if (i === index) {
          opacity = 1 - blend * 0.85;
          scale = 1 + blend * 0.03;
          wipe = blend * 18;
          frame.classList.add("is-active");
        } else if (i === index + 1) {
          opacity = blend;
          scale = 1.04 - blend * 0.04;
          wipe = 0;
          frame.classList.add("is-active");
        } else {
          frame.classList.remove("is-active");
        }

        frame.style.opacity = String(opacity);
        frame.style.transform = "scale(" + scale + ")";
        frame.style.clipPath =
          i === index + 1
            ? "inset(0 0 0 " + (100 - blend * 100) + "%)"
            : i === index
              ? "inset(0 " + wipe + "% 0 0)"
              : "inset(0 0 0 0)";
        frame.style.zIndex = i === index || i === index + 1 ? "1" : "0";
      });

      var active = blend > 0.55 && index < count - 1 ? index + 1 : index;
      if (active !== lastIndex) {
        lastIndex = active;
        dots.forEach(function (dot, i) {
          dot.classList.toggle("is-active", i === active);
        });
        if (caption) {
          var nextCaption = frames[active].getAttribute("data-caption") || "";
          caption.textContent = nextCaption;
        }
      }
    }

    function onScroll() {
      if (!ticking) {
        ticking = true;
        window.requestAnimationFrame(update);
      }
    }

    window.addEventListener("scroll", onScroll, { passive: true });
    window.addEventListener("resize", onScroll, { passive: true });
    update();
  }
})();
