(function () {
  const showModal = document.querySelector('[data-show-modal]');
  if (showModal) {
    const storageKey = 'djt_novi_fishing_show_2027';
    const everyVisit = !!(window.DJT && window.DJT.fishingShowEveryVisit);
    const dismissed = everyVisit
      ? false
      : (() => {
          try {
            return window.localStorage.getItem(storageKey) === '1';
          } catch {
            return false;
          }
        })();
    const closeControls = showModal.querySelectorAll('[data-show-modal-close]');
    const panel = showModal.querySelector('.show-modal__panel');
    let lastFocus = null;

    function closeModal() {
      showModal.hidden = true;
      document.body.classList.remove('show-modal-open');
      if (!everyVisit) {
        try {
          window.localStorage.setItem(storageKey, '1');
        } catch {
          /* ignore */
        }
      }
      if (lastFocus && typeof lastFocus.focus === 'function') {
        lastFocus.focus();
      }
    }

    function openModal() {
      lastFocus = document.activeElement;
      showModal.hidden = false;
      document.body.classList.add('show-modal-open');
      const primary = showModal.querySelector('.show-modal__close') || panel;
      window.requestAnimationFrame(() => primary.focus());
    }

    closeControls.forEach((el) => {
      el.addEventListener('click', closeModal);
    });
    document.addEventListener('keydown', (event) => {
      if (showModal.hidden) return;
      if (event.key === 'Escape') {
        event.preventDefault();
        closeModal();
      }
    });

    if (!dismissed) {
      window.requestAnimationFrame(openModal);
    }
  }

  const carousel = document.querySelector('[data-hero-carousel]');
  if (carousel) {
    const viewport = carousel.querySelector('.hero-carousel-viewport');
    const track = carousel.querySelector('[data-hero-carousel-track]');
    const slides = carousel.querySelectorAll('[data-hero-slide]');
    const dots = carousel.querySelectorAll('[data-hero-dot]');
    const prev = carousel.querySelector('[data-hero-prev]');
    const next = carousel.querySelector('[data-hero-next]');
    if (viewport && track && slides.length > 0) {
      let index = 0;
      let timer = 0;
      const total = slides.length;
      const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
      const intervalMs = 7000;

      function slideWidth() {
        return viewport.getBoundingClientRect().width;
      }

      function go(to) {
        index = (to + total) % total;
        const offset = index * slideWidth();
        track.style.transform = 'translate3d(' + (-offset) + 'px, 0, 0)';
        dots.forEach((dot, i) => {
          const active = i === index;
          dot.classList.toggle('is-active', active);
          dot.setAttribute('aria-selected', active ? 'true' : 'false');
        });
      }

      function restartAuto() {
        if (reducedMotion || total < 2) return;
        window.clearInterval(timer);
        timer = window.setInterval(() => go(index + 1), intervalMs);
      }

      prev?.addEventListener('click', () => {
        go(index - 1);
        restartAuto();
      });
      next?.addEventListener('click', () => {
        go(index + 1);
        restartAuto();
      });
      dots.forEach((dot) => {
        dot.addEventListener('click', () => {
          const target = Number(dot.getAttribute('data-hero-dot'));
          if (!Number.isNaN(target)) {
            go(target);
            restartAuto();
          }
        });
      });
      carousel.addEventListener('mouseenter', () => window.clearInterval(timer));
      carousel.addEventListener('mouseleave', restartAuto);
      carousel.addEventListener('focusin', () => window.clearInterval(timer));
      carousel.addEventListener('focusout', (event) => {
        if (!carousel.contains(event.relatedTarget)) restartAuto();
      });
      document.addEventListener('keydown', (event) => {
        if (!carousel.contains(document.activeElement) && document.activeElement !== document.body) return;
        if (event.key === 'ArrowLeft') {
          event.preventDefault();
          go(index - 1);
          restartAuto();
        } else if (event.key === 'ArrowRight') {
          event.preventDefault();
          go(index + 1);
          restartAuto();
        }
      });
      go(0);
      restartAuto();
      window.addEventListener('resize', () => go(index));
    }
  }

  const toggle = document.querySelector('[data-nav-toggle]');
  const panel = document.querySelector('[data-nav]');
  const search = document.querySelector('[data-nav-search]');
  if (!toggle || !panel) {
    return;
  }

  function setOpen(open) {
    panel.classList.toggle('is-open', open);
    panel.hidden = !open;
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    toggle.classList.toggle('is-open', open);
    document.body.classList.toggle('nav-open', open);
    if (open && search) {
      window.requestAnimationFrame(() => search.focus());
    }
  }

  toggle.addEventListener('click', () => {
    setOpen(!panel.classList.contains('is-open'));
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && panel.classList.contains('is-open')) {
      setOpen(false);
      toggle.focus();
    }
  });
})();
