(function () {
  const cfg = window.DJT_CHECKOUT || {};
  const form = document.getElementById('checkout-form');
  if (!form) return;

  const money = (cents) => {
    const n = (Number(cents) || 0) / 100;
    return n.toLocaleString('en-US', { style: 'currency', currency: cfg.currency || 'USD' });
  };

  const qs = (sel, root) => (root || document).querySelector(sel);
  const errEl = qs('[data-checkout-error]');
  let giftToken = '';
  let calcTimer = null;
  let lastTotals = null;
  let card = null;
  let giftCard = null;
  let payments = null;

  function showError(msg) {
    if (!errEl) return;
    if (!msg) {
      errEl.hidden = true;
      errEl.textContent = '';
      return;
    }
    errEl.hidden = false;
    errEl.textContent = msg;
  }

  function collectForm() {
    const method = (form.querySelector('input[name="shipping_method"]:checked') || {}).value || 'flat';
    return {
      name: form.name.value.trim(),
      email: form.email.value.trim(),
      phone: form.phone.value.trim(),
      shipping_method: method,
      address: {
        line1: form.address_line1.value.trim(),
        line2: form.address_line2.value.trim(),
        city: form.address_city.value.trim(),
        state: form.address_state.value.trim(),
        postal: form.address_postal.value.trim(),
        country: cfg.country || 'US'
      },
      coupon: form.coupon.value.trim(),
      gift_card_token: giftToken
    };
  }

  function updateTotals(t) {
    lastTotals = t;
    const set = (key, cents, hideZero) => {
      const el = qs('[data-total="' + key + '"]');
      if (!el) return;
      if (hideZero && !cents) {
        el.textContent = '—';
        return;
      }
      el.textContent = money(cents);
    };
    set('subtotal', t.subtotal_cents);
    set('shipping', t.shipping_cents);
    set('tax', t.tax_cents);
    set('discount', t.discount_cents);
    set('gift', t.gift_applied_cents ? -t.gift_applied_cents : 0);
    set('due', t.due_cents);
    const discRow = qs('[data-discount-row]');
    if (discRow) discRow.hidden = !(t.discount_cents > 0);
    const giftRow = qs('[data-gift-row]');
    if (giftRow) giftRow.hidden = !(t.gift_applied_cents > 0);
    const payBtn = qs('[data-card-pay]');
    if (payBtn && !cfg.demo) {
      payBtn.disabled = false;
      payBtn.textContent = t.due_cents > 0 ? 'Pay ' + money(t.due_cents) : 'Complete order';
    }
  }

  async function postJson(url, body) {
    const res = await fetch(url, {
      method: 'POST',
      credentials: 'same-origin',
      headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify(body)
    });
    const json = await res.json().catch(() => ({}));
    if (!res.ok || json.ok === false) {
      throw new Error(json.error || 'Request failed');
    }
    return json;
  }

  async function refreshTotals() {
    try {
      const json = await postJson(cfg.calcUrl, collectForm());
      updateTotals(json.totals);
      showError('');
      const couponMsg = qs('[data-coupon-msg]');
      if (couponMsg) {
        couponMsg.textContent = json.totals.coupon
          ? 'Applied: ' + json.totals.coupon.name
          : '';
      }
    } catch (e) {
      const msg = String(e.message || '');
      if (/display_name|MISSING_REQUIRED/i.test(msg)) {
        showError('Enter your name to see tax and shipping totals.');
      } else if (/API request failed/i.test(msg)) {
        showError('Could not calculate totals. Check your address and try again.');
      } else {
        showError(msg);
      }
    }
  }

  function scheduleCalc() {
    clearTimeout(calcTimer);
    calcTimer = setTimeout(refreshTotals, 350);
  }

  form.addEventListener('input', scheduleCalc);
  form.addEventListener('change', () => {
    const method = (form.querySelector('input[name="shipping_method"]:checked') || {}).value;
    const fields = qs('[data-shipping-fields]');
    if (fields) fields.hidden = method === 'pickup';
    scheduleCalc();
  });

  qs('[data-apply-coupon]')?.addEventListener('click', () => refreshTotals());

  async function payWithSource(sourceId, verificationToken) {
    showError('');
    const payload = Object.assign(collectForm(), {
      source_id: sourceId || '',
      verification_token: verificationToken || '',
      gift_card_token: giftToken
    });
    const payBtn = qs('[data-card-pay]');
    if (payBtn) payBtn.disabled = true;
    try {
      const json = await postJson(cfg.payUrl, payload);
      window.location.href = json.redirect;
    } catch (e) {
      showError(e.message);
      if (payBtn) payBtn.disabled = false;
    }
  }

  qs('[data-demo-pay]')?.addEventListener('click', () => payWithSource('', ''));

  async function initSquare() {
    try {
      await initSquareInner();
    } catch (e) {
      console.error(e);
      showError(e.message || 'Payment form failed to load. Check Square Application ID (sandbox-sq0idb-…) and refresh.');
      const payBtn = qs('[data-card-pay]');
      if (payBtn) payBtn.disabled = true;
    }
  }

  async function initSquareInner() {
    if (cfg.demo) {
      refreshTotals();
      return;
    }
    if (!window.Square) {
      showError('Square Payments SDK failed to load.');
      return;
    }
    if (!cfg.appId || cfg.appId === 'REPLACE_ME') {
      showError('Set square_application_id in shop-config.php (Sandbox Application ID).');
      return;
    }
    if (cfg.appId.indexOf('sandbox-') !== 0 && (cfg.appId.indexOf('sq0idp-') === 0)) {
      showError('Wrong Application ID: use the Sandbox ID (starts with sandbox-sq0idb-), not the Production sq0idp- ID.');
      return;
    }
    payments = window.Square.payments(cfg.appId, cfg.locationId);

    card = await payments.card();
    await card.attach('#card-container');

    try {
      giftCard = await payments.giftCard();
      await giftCard.attach('#gift-card-container');
    } catch (_) {
      // gift cards may be unavailable in some sandbox apps
    }

    qs('[data-apply-gift]')?.addEventListener('click', async () => {
      if (!giftCard) return;
      showError('');
      try {
        const result = await giftCard.tokenize();
        if (result.status === 'OK') {
          giftToken = result.token;
          qs('[data-gift-msg]').textContent = 'Gift card applied.';
          await refreshTotals();
        } else {
          throw new Error((result.errors && result.errors[0] && result.errors[0].message) || 'Gift card failed');
        }
      } catch (e) {
        showError(e.message);
      }
    });

    async function buildPaymentRequest() {
      const totals = lastTotals || { due_cents: 0, currency: cfg.currency };
      return payments.paymentRequest({
        countryCode: cfg.country || 'US',
        currencyCode: totals.currency || cfg.currency || 'USD',
        total: {
          amount: ((totals.due_cents || 0) / 100).toFixed(2),
          label: "DJ's Tackle"
        }
      });
    }

    async function attachWallet(method, selector) {
      try {
        const req = await buildPaymentRequest();
        const instance = await payments[method](req);
        const can = await instance.canMakePayment();
        if (!can) {
          const el = qs(selector);
          if (el) el.style.display = 'none';
          return null;
        }
        await instance.attach(selector);
        instance.addEventListener('ontokenization', async (event) => {
          const { tokenResult } = event.detail;
          if (tokenResult.status === 'OK') {
            await payWithSource(tokenResult.token, '');
          } else {
            showError((tokenResult.errors && tokenResult.errors[0] && tokenResult.errors[0].message) || 'Wallet payment failed');
          }
        });
        return instance;
      } catch (_) {
        const el = qs(selector);
        if (el) el.style.display = 'none';
        return null;
      }
    }

    await refreshTotals();
    await attachWallet('applePay', '#apple-pay');
    await attachWallet('googlePay', '#google-pay');
    await attachWallet('cashAppPay', '#cash-app-pay');

    qs('[data-card-pay]')?.addEventListener('click', async () => {
      showError('');
      const payBtn = qs('[data-card-pay]');
      if (payBtn) payBtn.disabled = true;
      try {
        if (!card) {
          throw new Error('Card form is not ready. Refresh the page.');
        }
        const due = lastTotals ? lastTotals.due_cents : 0;
        if (due <= 0 && giftToken) {
          await payWithSource('', '');
          return;
        }
        const result = await card.tokenize();
        if (result.status !== 'OK') {
          throw new Error((result.errors && result.errors[0] && result.errors[0].message) || 'Card tokenization failed');
        }
        let verificationToken = '';
        try {
          const details = collectForm();
          const verification = await payments.verifyBuyer(result.token, {
            amount: ((lastTotals && lastTotals.due_cents) || 0) / 100 + '',
            currencyCode: cfg.currency || 'USD',
            intent: 'CHARGE',
            billingContact: {
              givenName: details.name.split(' ')[0] || details.name,
              familyName: details.name.split(' ').slice(1).join(' ') || undefined,
              email: details.email,
              phone: details.phone || undefined,
              addressLines: details.shipping_method === 'flat' ? [details.address.line1] : undefined,
              city: details.address.city || undefined,
              state: details.address.state || undefined,
              postalCode: details.address.postal || undefined,
              countryCode: details.address.country || 'US'
            }
          });
          verificationToken = verification.token || '';
        } catch (_) {
          // 3DS optional in sandbox for many test cards
        }
        await payWithSource(result.token, verificationToken);
      } catch (e) {
        showError(e.message);
        if (payBtn) payBtn.disabled = false;
      }
    });
  }

  initSquare();
})();
