(function () {
  const base = (window.DJT && window.DJT.base) || '/';

  function qs(sel, root) { return (root || document).querySelector(sel); }
  function qsa(sel, root) { return Array.from((root || document).querySelectorAll(sel)); }

  async function post(path, data) {
    const body = new FormData();
    Object.entries(data || {}).forEach(([k, v]) => body.append(k, v));
    const res = await fetch(base.replace(/\/?$/, '/') + path.replace(/^\//, ''), {
      method: 'POST',
      body,
      credentials: 'same-origin',
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    const json = await res.json().catch(() => ({}));
    if (!res.ok || json.ok === false) {
      throw new Error(json.error || 'Request failed');
    }
    return json;
  }

  function setCartCount(n) {
    qsa('[data-cart-count]').forEach((el) => { el.textContent = String(n); });
    if (window.DJT) window.DJT.cartCount = n;
  }

  function openDrawer(html) {
    const drawer = qs('[data-cart-drawer]');
    if (!drawer) return;
    qs('[data-cart-drawer-body]', drawer).innerHTML = html || '<p>Added to cart.</p>';
    drawer.hidden = false;
  }

  document.addEventListener('click', async (e) => {
    const addBtn = e.target.closest('[data-add-to-cart]');
    if (addBtn) {
      e.preventDefault();
      const variationId = addBtn.getAttribute('data-variation-id') || qs('[name="variation_id"]')?.value;
      const qty = qs('[name="qty"]')?.value || addBtn.getAttribute('data-qty') || '1';
      if (!variationId) return;
      addBtn.disabled = true;
      try {
        const json = await post('cart/api.php', { action: 'add', variation_id: variationId, qty });
        setCartCount(json.count || 0);
        openDrawer(json.drawer_html || '<p>Added to cart.</p>');
      } catch (err) {
        alert(err.message);
      } finally {
        addBtn.disabled = false;
      }
      return;
    }

    const wishBtn = e.target.closest('[data-wishlist-toggle]');
    if (wishBtn) {
      e.preventDefault();
      const productId = wishBtn.getAttribute('data-product-id');
      try {
        const json = await post('wishlist/api.php', { action: 'toggle', product_id: productId });
        wishBtn.setAttribute('aria-pressed', json.added ? 'true' : 'false');
        wishBtn.textContent = json.added ? 'Saved' : 'Save';
      } catch (err) {
        alert(err.message);
      }
      return;
    }

    if (e.target.closest('[data-cart-close]') || e.target.matches('[data-cart-drawer]')) {
      const drawer = qs('[data-cart-drawer]');
      if (drawer && (e.target.matches('[data-cart-drawer]') || e.target.closest('[data-cart-close]'))) {
        drawer.hidden = true;
      }
    }
  });

  function applyVariation(v) {
    const input = qs('[name="variation_id"]');
    if (input) input.value = v ? v.id : '';
    const priceEl = qs('[data-product-price]');
    if (priceEl && v) priceEl.textContent = v.price;
    const badge = qs('[data-stock-badge]');
    if (badge) {
      if (v) {
        badge.className = 'badge ' + (v.stock_class || 'in-stock');
        badge.textContent = v.stock;
      } else {
        badge.className = 'badge sold-out';
        badge.textContent = 'Unavailable';
      }
    }
    const skuEl = qs('[data-selected-sku]');
    if (skuEl) skuEl.textContent = v && v.sku ? 'SKU: ' + v.sku : '';
    const add = qs('[data-add-to-cart]');
    if (add) {
      add.setAttribute('data-variation-id', v ? v.id : '');
      add.disabled = !v || v.sold_out;
    }
    const alertInput = qs('form input[name="variation_id"]');
    if (alertInput && v) alertInput.value = v.id;
  }

  // Legacy flat variation picker
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-variation]');
    if (!btn) return;
    qsa('[data-variation]').forEach((b) => b.setAttribute('aria-pressed', 'false'));
    btn.setAttribute('aria-pressed', 'true');
    applyVariation({
      id: btn.getAttribute('data-variation'),
      price: btn.getAttribute('data-price'),
      stock: btn.getAttribute('data-stock'),
      stock_class: btn.getAttribute('data-stock-class'),
      sold_out: (btn.getAttribute('data-stock-class') || '') === 'sold-out'
    });
  });

  // Option-group picker (Size × Color …)
  (function initOptionPicker() {
    const picker = qs('[data-option-picker]');
    const dataEl = qs('[data-variations-json]');
    if (!picker || !dataEl) return;
    let variations = [];
    try { variations = JSON.parse(dataEl.textContent || '[]'); } catch (_) { return; }

    const groups = qsa('[data-option-group]', picker).map((g) => g.getAttribute('data-option-group'));
    const current = {};
    groups.forEach((name) => {
      const checked = qs('[data-option-name="' + CSS.escape(name) + '"][aria-checked="true"]', picker);
      if (checked) current[name] = checked.getAttribute('data-option-value');
    });

    function matches(v, sel) {
      return Object.keys(sel).every((k) => v.options[k] === sel[k]);
    }

    function refresh() {
      // Mark which values are still purchasable given the other selections
      groups.forEach((name) => {
        const others = Object.assign({}, current);
        delete others[name];
        qsa('[data-option-name="' + CSS.escape(name) + '"]', picker).forEach((btn) => {
          const val = btn.getAttribute('data-option-value');
          const candidates = variations.filter((v) => v.options[name] === val && matches(v, others));
          const available = candidates.some((v) => !v.sold_out);
          btn.classList.toggle('is-unavailable', candidates.length === 0 || !available);
          btn.setAttribute('aria-checked', current[name] === val ? 'true' : 'false');
        });
        const label = qs('[data-option-group="' + CSS.escape(name) + '"] [data-option-current]', picker);
        if (label) label.textContent = current[name] || '';
      });

      const complete = groups.every((name) => current[name]);
      const v = complete ? variations.find((x) => matches(x, current)) : null;
      applyVariation(v || null);
    }

    picker.addEventListener('click', (e) => {
      const btn = e.target.closest('[data-option-name]');
      if (!btn) return;
      current[btn.getAttribute('data-option-name')] = btn.getAttribute('data-option-value');
      refresh();
    });

    refresh();
  })();

  // Recently viewed
  const productId = document.body.getAttribute('data-product-id');
  if (productId) {
    try {
      const key = 'djt_recent';
      const list = JSON.parse(localStorage.getItem(key) || '[]').filter((id) => id !== productId);
      list.unshift(productId);
      localStorage.setItem(key, JSON.stringify(list.slice(0, 8)));
    } catch (_) {}
  }
})();
