(function () {
  'use strict';

  var toggle = document.querySelector('.menu-toggle');
  var nav = document.getElementById('main-nav');

  if (!toggle || !nav) return;

  toggle.addEventListener('click', function () {
    var ul = nav.querySelector('ul');
    if (ul) {
      ul.classList.toggle('open');
      toggle.classList.toggle('is-open', ul.classList.contains('open'));
      toggle.setAttribute('aria-expanded', ul.classList.contains('open') ? 'true' : 'false');
    }
  });

  document.addEventListener('click', function (e) {
    var ul = nav.querySelector('ul');
    if (ul && ul.classList.contains('open') && !nav.contains(e.target) && !toggle.contains(e.target)) {
      ul.classList.remove('open');
      toggle.classList.remove('is-open');
      toggle.setAttribute('aria-expanded', 'false');
    }
  });
})();
