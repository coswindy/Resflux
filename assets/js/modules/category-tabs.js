(function () {
  'use strict';

  var tabsWrap = document.getElementById('cat-tabs');
  var grid = document.getElementById('post-grid');
  var loadBtn = document.getElementById('load-more-btn');

  if (!tabsWrap && !loadBtn) return;

  var currentCat = loadBtn ? parseInt(loadBtn.getAttribute('data-cat'), 10) : 0;
  var page = loadBtn ? parseInt(loadBtn.getAttribute('data-page'), 10) : 1;

  function fetchPosts(catId, pageNum, replace) {
    var params = new URLSearchParams({
      action: 'techpress_load_more',
      page: pageNum,
      cat: catId,
      nonce: techpress.nonce
    });

    fetch(techpress.ajax_url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: params.toString()
    })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (data.success) {
          if (replace) {
            grid.innerHTML = data.data.html;
          } else {
            grid.insertAdjacentHTML('beforeend', data.data.html);
          }

          page = data.data.page;
          if (loadBtn) {
            loadBtn.textContent = data.data.has_next ? '\u52A0\u8F7D\u66F4\u591A \u2193' : '\u6CA1\u6709\u66F4\u591A\u4E86';
            loadBtn.style.display = data.data.has_next ? '' : 'none';
            loadBtn.classList.remove('loading');
            loadBtn.setAttribute('data-page', page);
            loadBtn.setAttribute('data-cat', catId);
          }
        }
      })
      .catch(function () {
        if (loadBtn) {
          loadBtn.textContent = '\u52A0\u8F7D\u66F4\u591A \u2193';
          loadBtn.classList.remove('loading');
        }
      });
  }

  if (tabsWrap) {
    tabsWrap.addEventListener('click', function (e) {
      var tab = e.target.closest('.cat-tab');
      if (!tab) return;

      var catId = parseInt(tab.getAttribute('data-cat'), 10);
      if (catId === currentCat) return;

      tabsWrap.querySelectorAll('.cat-tab').forEach(function (t) { t.classList.remove('active'); });
      tab.classList.add('active');
      currentCat = catId;
      page = 1;

      fetchPosts(catId, 1, true);
    });
  }

  if (loadBtn) {
    loadBtn.addEventListener('click', function () {
      var nextPage = page + 1;
      loadBtn.textContent = '\u52A0\u8F7D\u4E2D...';
      loadBtn.classList.add('loading');
      fetchPosts(currentCat, nextPage, false);
    });
  }
})();
