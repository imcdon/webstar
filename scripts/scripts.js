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
    var buttons = document.querySelectorAll('[data-package-select]');
    var panel = document.querySelector('[data-package-detail]');
    if (!buttons.length || !panel) {
        return;
    }

    var titleEl = panel.querySelector('[data-detail-title]');
    var priceEl = panel.querySelector('[data-detail-price]');
    var turnaroundEl = panel.querySelector('[data-detail-turnaround]');
    var includesEl = panel.querySelector('[data-detail-includes]');
    var intakeEl = panel.querySelector('[data-detail-intake]');

    function setSelected(button) {
        buttons.forEach(function (btn) {
            var active = btn === button;
            btn.classList.toggle('is-selected', active);
            btn.setAttribute('aria-expanded', active ? 'true' : 'false');
        });
    }

    function showPackage(button) {
        var title = button.getAttribute('data-title') || '';
        var price = button.getAttribute('data-price') || '';
        var turnaround = button.getAttribute('data-turnaround') || '';
        var slug = button.getAttribute('data-slug') || '';
        var includes = [];

        try {
            includes = JSON.parse(button.getAttribute('data-includes') || '[]');
        } catch (err) {
            includes = [];
        }

        titleEl.textContent = title;
        priceEl.textContent = price;
        turnaroundEl.textContent = 'Turnaround: ' + turnaround;
        includesEl.innerHTML = '';
        includes.forEach(function (item) {
            var li = document.createElement('li');
            li.textContent = item;
            includesEl.appendChild(li);
        });
        intakeEl.setAttribute('href', 'package.php?slug=' + encodeURIComponent(slug));

        panel.hidden = false;
        setSelected(button);
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    buttons.forEach(function (button) {
        button.addEventListener('click', function () {
            if (button.classList.contains('is-selected') && !panel.hidden) {
                panel.hidden = true;
                setSelected(null);
                return;
            }
            showPackage(button);
        });
    });
})();
