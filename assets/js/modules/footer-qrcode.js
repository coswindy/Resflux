(function () {
  'use strict';

  var modal = document.getElementById('qrcode-modal');
  var image = document.getElementById('qrcode-image');
  var backdrop = document.getElementById('qrcode-backdrop');
  var closeBtn = document.getElementById('qrcode-close');

  if (!modal || !image || !backdrop || !closeBtn) return;

  function open(url) {
    image.src = url;
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function close() {
    modal.classList.remove('active');
    document.body.style.overflow = '';
  }

  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-qrcode]');
    if (btn) {
      e.preventDefault();
      open(btn.getAttribute('data-qrcode'));
    }
  });

  backdrop.addEventListener('click', close);
  closeBtn.addEventListener('click', close);

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') close();
  });
})();
