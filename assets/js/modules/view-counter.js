(function () {
  'use strict';

  if (!techpress.is_single || !techpress.post_id) return;

  var sessionKey = 'techpress_viewed_' + techpress.post_id;
  if (sessionStorage.getItem(sessionKey)) return;

  var params = new URLSearchParams({
    action: 'techpress_record_view',
    post_id: techpress.post_id,
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
        sessionStorage.setItem(sessionKey, '1');
      }
    })
    .catch(function () {
      console.error('[TechPress] Failed to record post view');
    });
})();
