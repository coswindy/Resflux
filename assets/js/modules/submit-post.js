(function () {
  'use strict';

  var form = document.getElementById('techpress-submit-form');
  if (!form) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    var data = new FormData(form);
    data.append('action', 'techpress_submit_post');
    data.append('nonce', techpress.nonce);

    var msgEl = form.querySelector('.submit-msg');
    msgEl.textContent = '\u63D0\u4EA4\u4E2D...';
    msgEl.className = 'submit-msg';
    form.querySelector('button[type="submit"]').disabled = true;

    fetch(techpress.ajax_url, {
      method: 'POST',
      body: data
    })
      .then(function (res) { return res.json(); })
      .then(function (res) {
        form.querySelector('button[type="submit"]').disabled = false;
        if (res.success) {
          msgEl.textContent = res.data.msg;
          msgEl.className = 'submit-msg success';
          form.reset();
        } else {
          msgEl.textContent = res.data.msg;
          msgEl.className = 'submit-msg error';
        }
      })
      .catch(function () {
        form.querySelector('button[type="submit"]').disabled = false;
        msgEl.textContent = '\u63D0\u4EA4\u5931\u8D25\uFF0C\u8BF7\u91CD\u8BD5';
        msgEl.className = 'submit-msg error';
      });
  });
})();
