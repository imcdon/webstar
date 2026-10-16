(function () {
    var MOBILE_BREAKPOINT = 768;
    var header = document.querySelector('.site-header');
    var toggle = document.querySelector('.site-header__menu-toggle');

    if (!header || !toggle) {
        return;
    }

    function isMobile() {
        return window.matchMedia('(max-width: ' + MOBILE_BREAKPOINT + 'px)').matches;
    }

    function setMenuOpen(open) {
        header.classList.toggle('site-header--nav-open', open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    }

    function closeMenu() {
        setMenuOpen(false);
    }

    toggle.addEventListener('click', function () {
        if (!isMobile()) {
            return;
        }
        setMenuOpen(!header.classList.contains('site-header--nav-open'));
    });

    window.addEventListener('resize', function () {
        if (!isMobile()) {
            closeMenu();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && header.classList.contains('site-header--nav-open')) {
            closeMenu();
            toggle.focus();
        }
    });
})();

(function () {
    var carousel = document.querySelector('.home-carousel');
    if (!carousel) {
        return;
    }

    var slides = carousel.querySelectorAll('.home-carousel__slide');
    var prevButton = carousel.querySelector('.home-carousel__arrow--prev');
    var nextButton = carousel.querySelector('.home-carousel__arrow--next');
    var counter = carousel.querySelector('.home-carousel__counter');

    if (!slides.length || !prevButton || !nextButton || !counter) {
        return;
    }

    var currentIndex = 0;

    function render() {
        slides.forEach(function (slide, index) {
            slide.classList.toggle('is-active', index === currentIndex);
        });
        counter.textContent = (currentIndex + 1) + ' / ' + slides.length;
    }

    function goTo(index) {
        if (index < 0) {
            currentIndex = slides.length - 1;
        } else if (index >= slides.length) {
            currentIndex = 0;
        } else {
            currentIndex = index;
        }
        render();
    }

    prevButton.addEventListener('click', function () {
        goTo(currentIndex - 1);
    });

    nextButton.addEventListener('click', function () {
        goTo(currentIndex + 1);
    });

    carousel.addEventListener('keydown', function (event) {
        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            goTo(currentIndex - 1);
        } else if (event.key === 'ArrowRight') {
            event.preventDefault();
            goTo(currentIndex + 1);
        }
    });

    render();
})();

(function () {
    window.dataLayer = window.dataLayer || [];

    function pushEvent(name, params) {
        window.dataLayer.push(Object.assign({ event: name }, params || {}));
        if (typeof window.gtag === 'function') {
            window.gtag('event', name, params || {});
        }
    }

    document.querySelectorAll('a[href^="tel:"]').forEach(function (link) {
        link.addEventListener('click', function () {
            pushEvent('phone_click', { link_url: link.getAttribute('href') || '' });
        });
    });

    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            var id = form.getAttribute('id') || '';
            var action = form.getAttribute('action') || '';
            if (id.indexOf('intake') !== -1 || action.indexOf('process-package-intake') !== -1) {
                pushEvent('intake_submit', { form_id: id });
            } else if (action.indexOf('process-contact') !== -1) {
                pushEvent('contact_submit', { form_id: id });
            }
        });
    });

    document.querySelectorAll('a[href*="intake"], a.home-section__btn--primary').forEach(function (link) {
        if (link.getAttribute('href') && link.getAttribute('href').indexOf('intake') !== -1) {
            link.addEventListener('click', function () {
                pushEvent('intake_cta_click', { link_url: link.href });
            });
        }
    });
})();
