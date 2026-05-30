(function () {
  'use strict';

  var wrapper = document.querySelector('.entry-content-wrapper');
  if (!wrapper || !window.techpress_readmore) return;

  var threshold = parseInt(window.techpress_readmore.threshold, 10) || 1800;
  var content = wrapper.querySelector('.entry-content');

  if (!content || content.scrollHeight <= threshold) return;

  wrapper.style.maxHeight = threshold + 'px';
  wrapper.classList.add('collapsed');

  var overlay = document.createElement('div');
  overlay.className = 'read-more-overlay';
  overlay.innerHTML = '<button class="read-more-btn">' + (window.techpress_readmore.text || '阅读余下全文 ↓') + '</button>';
  wrapper.appendChild(overlay);

  overlay.querySelector('.read-more-btn').addEventListener('click', function () {
    wrapper.style.maxHeight = wrapper.scrollHeight + 'px';
    wrapper.classList.remove('collapsed');
    overlay.classList.add('hidden');
  });
})();
