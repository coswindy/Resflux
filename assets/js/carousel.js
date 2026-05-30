(function () {
  'use strict';

  var track = document.getElementById('carousel-track');
  var prevBtn = document.getElementById('carousel-prev');
  var nextBtn = document.getElementById('carousel-next');
  var dots = document.getElementById('carousel-dots');

  if (!track) return;

  var slides = track.children;
  var total = slides.length;
  var index = 0;
  var autoTimer = null;
  var isAnimating = false;

  function init() {
    if (total < 2) {
      if (prevBtn) prevBtn.style.display = 'none';
      if (nextBtn) nextBtn.style.display = 'none';
      return;
    }

    for (var i = 0; i < total; i++) {
      var dot = document.createElement('button');
      dot.setAttribute('aria-label', '跳转到第 ' + (i + 1) + ' 张');
      if (i === 0) dot.className = 'active';
      dot.addEventListener('click', function (idx) {
        return function () { goTo(idx); };
      }(i));
      dots.appendChild(dot);
    }

    if (prevBtn) prevBtn.addEventListener('click', function () { navigate(-1); });
    if (nextBtn) nextBtn.addEventListener('click', function () { navigate(1); });

    initTouch();
    startAuto();
  }

  function goTo(idx) {
    if (isAnimating) return;
    if (idx < 0) idx = total - 1;
    if (idx >= total) idx = 0;
    if (idx === index) return;

    isAnimating = true;
    index = idx;
    track.style.transform = 'translateX(-' + (index * 100) + '%)';

    var allDots = dots.querySelectorAll('button');
    for (var i = 0; i < allDots.length; i++) {
      allDots[i].className = i === index ? 'active' : '';
    }

    setTimeout(function () { isAnimating = false; }, 550);
  }

  function navigate(dir) {
    resetAuto();
    goTo(index + dir);
  }

  function startAuto() {
    if (total < 2) return;
    stopAuto();
    autoTimer = setInterval(function () { goTo(index + 1); }, 5000);
  }

  function stopAuto() {
    if (autoTimer) { clearInterval(autoTimer); autoTimer = null; }
  }

  function resetAuto() { stopAuto(); startAuto(); }

  // Touch / drag support
  function initTouch() {
    var startX = 0, startY = 0, isDragging = false;

    track.addEventListener('touchstart', function (e) {
      startX = e.changedTouches[0].screenX;
      startY = e.changedTouches[0].screenY;
      isDragging = true;
      stopAuto();
    }, { passive: true });

    track.addEventListener('touchend', function (e) {
      if (!isDragging) return;
      isDragging = false;
      var endX = e.changedTouches[0].screenX;
      var endY = e.changedTouches[0].screenY;
      var diffX = startX - endX;
      var diffY = startY - endY;
      if (Math.abs(diffX) > 50 && Math.abs(diffX) > Math.abs(diffY) * 1.2) {
        goTo(index + (diffX > 0 ? 1 : -1));
      }
      startAuto();
    }, { passive: true });
  }

  // Keyboard
  document.addEventListener('keydown', function (e) {
    if (e.key === 'ArrowLeft') navigate(-1);
    if (e.key === 'ArrowRight') navigate(1);
  });

  // Pause on hover
  var wrapper = document.getElementById('carousel-wrapper');
  if (wrapper) {
    wrapper.addEventListener('mouseenter', stopAuto);
    wrapper.addEventListener('mouseleave', startAuto);
  }

  init();
})();
