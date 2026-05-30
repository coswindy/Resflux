(function () {
  'use strict';

  var bar = document.getElementById('progress-bar');
  if (!bar) return;

  window.addEventListener('scroll', function () {
    var scrollTop = window.scrollY;
    var docHeight = document.documentElement.scrollHeight - window.innerHeight;
    if (docHeight > 0) {
      bar.style.width = (scrollTop / docHeight) * 100 + '%';
    }
  }, { passive: true });
})();
