(function () {
  'use strict';

  var html = document.documentElement;
  var toggle = document.getElementById('dark-toggle');
  var stored = localStorage.getItem('techpress_dark');
  var config = html.getAttribute('data-theme') || 'auto';

  function apply(theme) {
    if (theme === 'on') {
      html.setAttribute('data-theme', 'dark');
      if (toggle) toggle.classList.add('is-dark');
    } else if (theme === 'off') {
      html.removeAttribute('data-theme');
      if (toggle) toggle.classList.remove('is-dark');
    } else {
      var prefer = window.matchMedia('(prefers-color-scheme: dark)').matches;
      if (prefer) {
        html.setAttribute('data-theme', 'dark');
        if (toggle) toggle.classList.add('is-dark');
      } else {
        html.removeAttribute('data-theme');
        if (toggle) toggle.classList.remove('is-dark');
      }
    }
  }

  if (stored) {
    apply(stored);
  } else {
    apply(config);
  }

  if (toggle) {
    toggle.addEventListener('click', function () {
      var isDark = html.getAttribute('data-theme') === 'dark';
      var newVal = isDark ? 'off' : 'on';
      localStorage.setItem('techpress_dark', newVal);
      apply(newVal);
    });
  }

  if (config === 'auto') {
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
      if (!localStorage.getItem('techpress_dark')) {
        apply('auto');
      }
    });
  }
})();
