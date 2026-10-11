// "Added to cart" toast
const params = new URLSearchParams(location.search);
if (params.has('added')) {
  const t = document.createElement('div'); t.className = 'toast'; t.textContent = 'Added to cart ✓';
  document.body.appendChild(t); setTimeout(() => t.remove(), 1800);
  params.delete('added');
  history.replaceState(null, '', location.pathname + (params.toString() ? '?' + params : ''));
}
// Special-offer countdown
const cd = document.getElementById('cd');
if (cd) {
  const end = +cd.dataset.end, set = (k, v) => cd.querySelector('[data-' + k + ']').textContent = String(v).padStart(2, '0');
  const tick = () => { const s = Math.max(0, Math.floor((end - Date.now()) / 1000));
    set('d', Math.floor(s / 86400)); set('h', Math.floor(s % 86400 / 3600)); set('m', Math.floor(s % 3600 / 60)); set('s', s % 60); };
  tick(); setInterval(tick, 1000);
}
