(function () {
  'use strict';

  var modal = document.getElementById('search-modal');
  var openBtn = document.getElementById('search-toggle');
  var closeBtn = document.getElementById('search-close');

  if (!modal || !openBtn) return;

  function isInputFocused() {
    var tag = document.activeElement ? document.activeElement.tagName : '';
    return tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT';
  }

  function openSearch() {
    modal.classList.add('active');
    setTimeout(function () {
      var input = modal.querySelector('input');
      if (input) input.focus();
    }, 200);
  }

  function closeSearch() {
    modal.classList.remove('active');
  }

  openBtn.addEventListener('click', openSearch);

  if (closeBtn) closeBtn.addEventListener('click', closeSearch);

  modal.addEventListener('click', function (e) {
    if (e.target === modal) closeSearch();
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && modal.classList.contains('active')) closeSearch();
    if (e.key === '/' && !e.ctrlKey && !e.metaKey && !isInputFocused()) {
      e.preventDefault();
      openSearch();
    }
  });
})();
