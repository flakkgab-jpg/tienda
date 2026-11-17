/* ============================================================
   The Fragrance Obsession by Milos — animaciones y filtros
   ============================================================ */
(function () {
    'use strict';

    var sinMovimiento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var puntero = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

    /* ───────── Menú móvil ───────── */
    var btn = document.querySelector('.menu-btn');
    var menu = document.getElementById('menu');
    if (btn && menu) {
        var cerrar = function () {
            menu.classList.remove('abierto');
            btn.setAttribute('aria-expanded', 'false');
        };
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var abierto = menu.classList.toggle('abierto');
            btn.setAttribute('aria-expanded', abierto ? 'true' : 'false');
        });
        document.addEventListener('click', function (e) {
            if (!menu.contains(e.target)) cerrar();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') cerrar();
        });
    }

    /* ───────── Aparición al hacer scroll (con escalonado) ───────── */
    var revelables = [].slice.call(document.querySelectorAll('.reveal'));

    function mostrarElemento(el) {
        el.classList.add('visible');
        // Quita el retraso escalonado para que filtros e inclinación respondan al instante
        setTimeout(function () { el.style.setProperty('--d', '0s'); }, 1500);
    }

    if ('IntersectionObserver' in window && !sinMovimiento) {
        var obsReveal = new IntersectionObserver(function (entradas) {
            var i = 0;
            entradas.forEach(function (en) {
                if (!en.isIntersecting) return;
                en.target.style.setProperty('--d', (i * 70) + 'ms');
                i++;
                mostrarElemento(en.target);
                obsReveal.unobserve(en.target);
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        revelables.forEach(function (el) { obsReveal.observe(el); });
    } else {
        revelables.forEach(function (el) { el.classList.add('visible'); });
    }

    /* ───────── Contadores animados ───────── */
    function contar(el) {
        var meta = parseInt(el.getAttribute('data-contar'), 10) || 0;
        if (sinMovimiento) { el.textContent = meta; return; }
        var dur = 1300;
        var t0 = performance.now();
        (function paso(t) {
            var p = Math.min((t - t0) / dur, 1);
            el.textContent = Math.round(meta * (1 - Math.pow(1 - p, 3)));
            if (p < 1) requestAnimationFrame(paso);
        })(t0);
    }

    var contadores = [].slice.call(document.querySelectorAll('[data-contar]'));
    if ('IntersectionObserver' in window && !sinMovimiento) {
        contadores.forEach(function (el) { el.textContent = '0'; });
        var obsContar = new IntersectionObserver(function (entradas) {
            entradas.forEach(function (en) {
                if (!en.isIntersecting) return;
                contar(en.target);
                obsContar.unobserve(en.target);
            });
        }, { threshold: 0.6 });
        contadores.forEach(function (el) { obsContar.observe(el); });
    }

    /* ───────── Cristal: reflejo que sigue al mouse + inclinación 3D ───────── */
    document.querySelectorAll('.glass').forEach(function (el) {
        var inclinable = el.hasAttribute('data-tilt') || el.classList.contains('card');

        el.addEventListener('pointermove', function (e) {
            if (e.pointerType !== 'mouse') return;
            var r = el.getBoundingClientRect();
            var x = e.clientX - r.left;
            var y = e.clientY - r.top;
            el.style.setProperty('--mx', x + 'px');
            el.style.setProperty('--my', y + 'px');

            if (inclinable && puntero && !sinMovimiento) {
                var rx = ((y / r.height) - 0.5) * -9;
                var ry = ((x / r.width) - 0.5) * 9;
                el.style.transform = 'perspective(900px) rotateX(' + rx.toFixed(2) + 'deg) rotateY(' + ry.toFixed(2) + 'deg)';
            }
        });
        el.addEventListener('pointerleave', function () {
            el.style.transform = '';
        });
    });

    /* ───────── Luz que sigue el cursor + parallax del fondo ───────── */
    var glow = document.querySelector('.cursor-glow');
    var blobs = [].slice.call(document.querySelectorAll('.blob'));
    if (glow && puntero && !sinMovimiento) {
        var gx = 0, gy = 0, tx = 0, ty = 0, raf = null;

        blobs.forEach(function (b) { b.style.transition = 'translate 1.6s ease-out'; });

        var bucle = function () {
            gx += (tx - gx) * 0.12;
            gy += (ty - gy) * 0.12;
            glow.style.transform = 'translate3d(' + gx.toFixed(1) + 'px,' + gy.toFixed(1) + 'px,0)';
            if (Math.abs(tx - gx) > 0.5 || Math.abs(ty - gy) > 0.5) {
                raf = requestAnimationFrame(bucle);
            } else {
                raf = null;
            }
        };

        window.addEventListener('pointermove', function (e) {
            if (e.pointerType !== 'mouse') return;
            tx = e.clientX;
            ty = e.clientY;
            glow.classList.add('activo');
            if (!raf) raf = requestAnimationFrame(bucle);

            var dx = (e.clientX / window.innerWidth - 0.5);
            var dy = (e.clientY / window.innerHeight - 0.5);
            blobs.forEach(function (b, i) {
                var k = (i + 1) * 26;
                b.style.translate = (dx * k).toFixed(1) + 'px ' + (dy * k).toFixed(1) + 'px';
            });
        });
        document.addEventListener('pointerleave', function () { glow.classList.remove('activo'); });
    }

    /* ───────── Buscador y filtros del catálogo ───────── */
    var grid = document.getElementById('grid');
    if (grid) {
        var tarjetas = [].slice.call(grid.querySelectorAll('.card'));
        var campo = document.getElementById('buscar');
        var chips = [].slice.call(document.querySelectorAll('.chip'));
        var sinRes = document.getElementById('sin-resultados');
        var filtro = 'todos';
        var texto = '';

        var normalizar = function (s) {
            return (s || '').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
        };
        tarjetas.forEach(function (c) { c.setAttribute('data-nombre', normalizar(c.getAttribute('data-nombre'))); });

        var ocultar = function (c) {
            if (c.hidden) return;
            c.classList.add('salir');
            clearTimeout(c._t);
            c._t = setTimeout(function () { c.hidden = true; }, 280);
        };
        var mostrar = function (c) {
            clearTimeout(c._t);
            c.classList.add('visible');
            c.style.setProperty('--d', '0s');
            if (c.hidden) {
                c.hidden = false;
                c.classList.add('salir');
                requestAnimationFrame(function () {
                    requestAnimationFrame(function () { c.classList.remove('salir'); });
                });
            } else {
                c.classList.remove('salir');
            }
        };

        var aplicar = function () {
            var visibles = 0;
            tarjetas.forEach(function (c) {
                var ok = (filtro === 'todos' || c.getAttribute('data-estado') === filtro) &&
                         c.getAttribute('data-nombre').indexOf(texto) !== -1;
                if (ok) { visibles++; mostrar(c); } else { ocultar(c); }
            });
            if (sinRes) sinRes.hidden = visibles > 0;
        };

        chips.forEach(function (chip) {
            chip.addEventListener('click', function () {
                chips.forEach(function (c) { c.classList.remove('activo'); });
                chip.classList.add('activo');
                filtro = chip.getAttribute('data-filtro');
                aplicar();
            });
        });

        if (campo) {
            campo.addEventListener('input', function () {
                texto = normalizar(campo.value.trim());
                aplicar();
            });
        }
    }

    /* ───────── Transición suave entre páginas ───────── */
    document.addEventListener('click', function (e) {
        var a = e.target.closest ? e.target.closest('a') : null;
        if (!a || e.defaultPrevented || e.button !== 0) return;
        if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        if (a.target === '_blank' || a.hasAttribute('download')) return;
        if (a.origin !== location.origin) return;
        if (a.pathname === location.pathname && a.hash) return;
        if (sinMovimiento) return;

        e.preventDefault();
        document.body.classList.add('saliendo');
        setTimeout(function () { location.href = a.href; }, 230);
    });
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) document.body.classList.remove('saliendo');
    });
})();
