(function () {
  'use strict';

  var buttons = document.querySelectorAll('.share-btn');
  if (!buttons.length) return;

  var url = encodeURIComponent(window.location.href);
  var title = encodeURIComponent(document.title);

  buttons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var type = btn.getAttribute('data-share');
      var shareUrl = '';

      switch (type) {
        case 'weibo':
          shareUrl = 'https://service.weibo.com/share/share.php?url=' + url + '&title=' + title;
          break;
        case 'twitter':
          shareUrl = 'https://twitter.com/intent/tweet?url=' + url + '&text=' + title;
          break;
        case 'linkedin':
          shareUrl = 'https://www.linkedin.com/sharing/share-offsite/?url=' + url;
          break;
        case 'copy':
          navigator.clipboard.writeText(window.location.href).then(function () {
            btn.classList.add('copied');
            setTimeout(function () {
              btn.classList.remove('copied');
            }, 2000);
          });
          return;
      }

      if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=500');
      }
    });
  });
})();
