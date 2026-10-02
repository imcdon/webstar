(function () {
    'use strict';

    var STORAGE_KEY = 'ww_entered';
    var body = document.body;
    var gate = document.getElementById('ww-seal-gate');
    var seal = document.getElementById('ww-seal');
    var skip = document.getElementById('ww-seal-skip');
    var home = document.getElementById('ww-home');

    function detectTier() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return 'minimal';
        }
        var conn = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
        if (conn) {
            if (conn.saveData) {
                return 'minimal';
            }
            var type = String(conn.effectiveType || '');
            if (type === 'slow-2g' || type === '2g') {
                return 'minimal';
            }
            if (type === '3g') {
                return 'medium';
            }
        }
        if (typeof navigator.deviceMemory === 'number' && navigator.deviceMemory > 0 && navigator.deviceMemory < 4) {
            return 'medium';
        }
        if (typeof navigator.hardwareConcurrency === 'number' && navigator.hardwareConcurrency > 0 && navigator.hardwareConcurrency < 4) {
            return 'medium';
        }
        return 'full';
    }

    function playSealSound(tier) {
        if (tier === 'minimal') {
            return;
        }
        try {
            var Ctx = window.AudioContext || window.webkitAudioContext;
            if (!Ctx) {
                return;
            }
            var ctx = new Ctx();
            var now = ctx.currentTime;
            var gain = ctx.createGain();
            gain.connect(ctx.destination);
            var vol = tier === 'medium' ? 0.045 : 0.08;
            gain.gain.setValueAtTime(0.0001, now);
            gain.gain.exponentialRampToValueAtTime(vol, now + 0.02);
            gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.45);

            var osc1 = ctx.createOscillator();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(660, now);
            osc1.frequency.exponentialRampToValueAtTime(880, now + 0.12);
            osc1.connect(gain);
            osc1.start(now);
            osc1.stop(now + 0.5);

            if (tier === 'full') {
                var osc2 = ctx.createOscillator();
                osc2.type = 'triangle';
                osc2.frequency.setValueAtTime(990, now + 0.05);
                osc2.connect(gain);
                osc2.start(now + 0.05);
                osc2.stop(now + 0.35);
            }
        } catch (e) {
            /* ignore audio failures */
        }
    }

    function spawnParticles() {
        var layer = gate.querySelector('.ww-particles');
        if (!layer) {
            return;
        }
        layer.innerHTML = '';
        var i;
        for (i = 0; i < 14; i += 1) {
            var p = document.createElement('span');
            p.className = 'ww-particle';
            p.style.left = '50%';
            p.style.top = '45%';
            var angle = (Math.PI * 2 * i) / 14;
            var dist = 60 + Math.random() * 80;
            p.style.setProperty('--dx', Math.cos(angle) * dist + 'px');
            p.style.setProperty('--dy', Math.sin(angle) * dist + 'px');
            p.style.animationDelay = (Math.random() * 0.12) + 's';
            layer.appendChild(p);
        }
    }

    function revealHome() {
        body.classList.remove('ww-body--sealed');
        if (home) {
            home.classList.add('is-revealed');
        }
        try {
            sessionStorage.setItem(STORAGE_KEY, '1');
        } catch (e) {
            /* ignore */
        }
    }

    function openSeal(withSound) {
        var tier = body.getAttribute('data-ww-tier') || 'full';
        if (withSound) {
            playSealSound(tier);
        }
        if (tier === 'full') {
            spawnParticles();
        }
        gate.classList.add('is-opening');
        window.setTimeout(function () {
            gate.classList.add('is-open');
            revealHome();
        }, tier === 'medium' ? 400 : 750);
    }

    function skipGate() {
        gate.classList.add('is-open');
        revealHome();
    }

    function initSeal() {
        if (!gate || !seal) {
            return;
        }

        var tier = detectTier();
        body.setAttribute('data-ww-tier', tier);

        var already;
        try {
            already = sessionStorage.getItem(STORAGE_KEY) === '1';
        } catch (e) {
            already = false;
        }

        if (tier === 'minimal' || already) {
            skipGate();
            return;
        }

        seal.addEventListener('click', function () {
            openSeal(true);
        });

        if (skip) {
            skip.addEventListener('click', function () {
                skipGate();
            });
        }
    }

    function initCarousels() {
        var carousels = document.querySelectorAll('[data-carousel]');
        carousels.forEach(function (carousel) {
            var id = carousel.id;
            var slides = Array.prototype.slice.call(carousel.querySelectorAll('[data-carousel-slide]'));
            if (slides.length === 0) {
                return;
            }
            var index = 0;

            function show(i) {
                index = (i + slides.length) % slides.length;
                slides.forEach(function (slide, n) {
                    slide.classList.toggle('is-active', n === index);
                });
            }

            var prev = document.querySelector('[data-carousel-prev="' + id + '"]');
            var next = document.querySelector('[data-carousel-next="' + id + '"]');
            if (prev) {
                prev.addEventListener('click', function () {
                    show(index - 1);
                });
            }
            if (next) {
                next.addEventListener('click', function () {
                    show(index + 1);
                });
            }
            show(0);
        });
    }

    initSeal();
    initCarousels();
})();
