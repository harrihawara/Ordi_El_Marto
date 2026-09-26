/* Matthias Schmid – Heilmasseur Wien | kleine UI-Helfer, kein Framework nötig */
(function () {
  'use strict';

  /* --- Mobile-Navigation --- */
  var toggle = document.querySelector('.nav-toggle');
  var nav = document.getElementById('hauptnavigation');

  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      var open = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      toggle.querySelector('.visually-hidden').textContent = open ? 'Menü schließen' : 'Menü öffnen';
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && nav.classList.contains('is-open')) {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.focus();
      }
    });
  }

  /* --- Header wird beim Scrollen über dem Hero sichtbar --- */
  var header = document.querySelector('.site-header--transparent');
  var hero = document.querySelector('.hero');
  if (header && hero && 'IntersectionObserver' in window) {
    var sentinel = document.createElement('div');
    sentinel.style.cssText = 'position:absolute;top:70vh;height:1px;width:1px;';
    hero.appendChild(sentinel);
    new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        header.classList.toggle('is-scrolled', !entry.isIntersecting);
      });
    }, { threshold: 0 }).observe(sentinel);
  }

  /* --- Bewegungseffekte nur, wenn der Browser sie nicht unterdrückt --- */
  var magBewegung = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* --- Logokreise drehen sich beim Scrollen --- */
  var ringEl = document.querySelector('.banner-rings--dreht');

  if (magBewegung && ringEl) {
    var laeuft = false;

    var zeichne = function () {
      laeuft = false;
      ringEl.style.setProperty('--ring-rot', (window.scrollY * 0.22).toFixed(1) + 'deg');
    };

    var anfordern = function () {
      if (!laeuft) { laeuft = true; window.requestAnimationFrame(zeichne); }
    };

    window.addEventListener('scroll', anfordern, { passive: true });
    zeichne();
  }

  /* --- Terminformular: Leistung aus der URL vorauswählen (?leistung=schroepfen) --- */
  var select = document.getElementById('leistung');
  if (select) {
    var wunsch = new URLSearchParams(window.location.search).get('leistung');
    if (wunsch) {
      Array.prototype.forEach.call(select.options, function (opt) {
        if (opt.value === wunsch) { select.value = wunsch; }
      });
    }
  }

  /* --- Terminformular: Bestätigung ohne Backend ---
     Hinweis für die Entwicklung: Sobald ein Mailversand/Buchungstool
     angebunden ist, kann dieser Block entfernt werden.                  */
  var form = document.getElementById('terminformular');
  if (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var status = document.getElementById('formular-status');
      if (!status) return;
      status.hidden = false;
      status.textContent = 'Vielen Dank für Ihre Anfrage! Sie wurde noch nicht versendet – '
        + 'das Formular muss vor dem Livegang mit einem Mailversand verbunden werden.';
      status.focus();
    });
  }
})();
