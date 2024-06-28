document.addEventListener("DOMContentLoaded", () => {
  fetch('/wp-json/async-admin-bar/v1/html/', { method: 'POST'/*, headers: { 'X-WP-Nonce': nonce }*/ }).then(response => {
    response.text().then(html => html && document.body.insertAdjacentHTML("beforeend", html));
  }).catch(() => {});
});
