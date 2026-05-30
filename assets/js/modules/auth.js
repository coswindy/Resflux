(function () {
  'use strict';

  var tabs = document.querySelector('.auth-tabs');
  if (tabs) {
    tabs.addEventListener('click', function (e) {
      var tab = e.target.closest('.auth-tab');
      if (!tab) return;

      tabs.querySelectorAll('.auth-tab').forEach(function (t) { t.classList.remove('active'); });
      tab.classList.add('active');

      var target = tab.getAttribute('data-tab');
      document.querySelectorAll('.auth-form').forEach(function (f) { f.classList.remove('active'); });
      var form = document.getElementById('auth-' + target);
      if (form) form.classList.add('active');
    });
  }

  // AJAX Login / Register
  var forms = document.querySelectorAll('.ajax-auth-form');
  if (!forms.length) return;

  forms.forEach(function (form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();

      var action = form.getAttribute('data-action');
      var formData = new FormData(form);
      formData.append('action', 'techpress_' + action);
      formData.append('nonce', techpress.nonce);

      var msgEl = form.querySelector('.auth-msg');
      msgEl.textContent = '\u5904\u7406\u4E2D...';
      msgEl.className = 'auth-msg';

      fetch(techpress.ajax_url, {
        method: 'POST',
        body: formData
      })
        .then(function (res) { return res.json(); })
        .then(function (res) {
          if (res.success) {
            msgEl.textContent = res.data.msg;
            msgEl.className = 'auth-msg success';
            setTimeout(function () { location.reload(); }, 800);
          } else {
            msgEl.textContent = res.data.msg;
            msgEl.className = 'auth-msg error';
          }
        })
        .catch(function () {
          msgEl.textContent = '\u8BF7\u6C42\u5931\u8D25';
          msgEl.className = 'auth-msg error';
        });
    });
  });
})();
