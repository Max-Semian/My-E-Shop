/**
 * HK Company Cost Calculator — Logic
 *
 * Data sources:
 *   statrys.com/terms-and-conditions/non-standard-fees
 *   statrys.com/hk/pricing
 */

(function () {
  'use strict';

  /* ───────────────────────────────
     Accordion / section toggle
  ─────────────────────────────── */
  document.querySelectorAll('.hk-section-toggle').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var targetId = btn.getAttribute('data-target');
      var body = document.getElementById(targetId);
      var isExpanded = btn.getAttribute('aria-expanded') === 'true';

      if (isExpanded) {
        body.classList.add('hk-hidden');
        btn.setAttribute('aria-expanded', 'false');
      } else {
        body.classList.remove('hk-hidden');
        btn.setAttribute('aria-expanded', 'true');
      }
    });
  });

  /* ───────────────────────────────
     Conditional show/hide
  ─────────────────────────────── */
  function toggleEl(checkboxId, targetId) {
    var cb = document.getElementById(checkboxId);
    var target = document.getElementById(targetId);
    if (!cb || !target) return;
    function update() {
      target.classList.toggle('hk-hidden', !cb.checked);
    }
    update();
    cb.addEventListener('change', function () { update(); calculate(); });
  }

  toggleEl('use_statrys',           'statrys-fields');
  toggleEl('special_company',       'special-fields');
  toggleEl('large_domestic_toggle', 'large-domestic-field');
  toggleEl('usd_swift_toggle',      'usd-swift-field');

  /* ───────────────────────────────
     Utilities
  ─────────────────────────────── */
  function val(id) {
    var el = document.getElementById(id);
    return el ? (parseFloat(el.value) || 0) : 0;
  }
  function checkedVal(name) {
    var el = document.querySelector('input[name="' + name + '"]:checked');
    return el ? parseFloat(el.value) : 0;
  }
  function isChecked(id) {
    var el = document.getElementById(id);
    return el ? el.checked : false;
  }

  function formatHKD(n) {
    return 'HKD\u00a0' + Math.round(n).toLocaleString('ru-RU');
  }
  function formatUSD(n, rate) {
    return '$' + Math.round(n / rate).toLocaleString('ru-RU');
  }
  function formatEUR(n, rate) {
    return '€' + Math.round(n / rate).toLocaleString('ru-RU');
  }

  function setResult(id, text) {
    var el = document.getElementById(id);
    if (!el) return;
    if (el.textContent !== text) {
      el.textContent = text;
      el.classList.remove('hk-amount-changed');
      // Trigger reflow so the animation restarts
      void el.offsetWidth;
      el.classList.add('hk-amount-changed');
    }
  }

  /* ───────────────────────────────
     Main calculation
  ─────────────────────────────── */
  function calculate() {

    /* ── 1. ONE-TIME costs ── */
    var oneTime = 0;

    // Government CR fee
    if (isChecked('cr_fee')) {
      oneTime += 1720;
    }

    // Business Registration Certificate
    var brPeriod = checkedVal('br_period');
    // br_period radios: 1year=fixed value tag, we use data-val approach — see below
    var brEl = document.querySelector('input[name="br_period"]:checked');
    if (brEl) {
      if (brEl.value === '1year')  oneTime += 2150;
      if (brEl.value === '3years') oneTime += 3950;
      // 'none' → 0
    }

    // Statrys first-year package
    if (isChecked('use_statrys')) {
      oneTime += val('statrys_package');
    }

    // KYC fee for special companies (one-time)
    if (isChecked('special_company')) {
      oneTime += checkedVal('kyc_fee');
    }

    /* ── 2. ANNUAL costs ── */
    var annual = 0;

    // Statrys annual renewal (year 2+, but we include it for both year 1 and ongoing)
    if (isChecked('use_statrys')) {
      annual += val('statrys_renewal');
    }

    // Business Registration annual renewal (only applicable if 1-year plan was chosen)
    // We already counted this in one-time above, so don't double count for year 1.
    // For year 2+ we treat BR as annual recurring:
    // (user can adjust the "ongoing" expectation; for simplicity, included in annual)

    // Inactivity fee: N months/year × HKD 88
    annual += val('inactive_months') * 88;

    // Non-standard fees (treated as per-event, user-defined quantities)
    annual += val('ns_closure_cert') * 450;
    annual += val('ns_gsc')          * 450;
    annual += val('ns_audit')        * checkedVal('audit_type');
    annual += val('ns_statement')    * 250;
    annual += val('ns_amendment')    * checkedVal('amendment_fee');
    annual += val('ns_other');

    /* ── 3. MONTHLY transaction fees ── */
    var monthly = 0;

    // Domestic HKD/CNY receive — free
    // Domestic HKD/CNY send — HKD 5/tx
    monthly += val('domestic_send') * 5;

    // Large domestic payments >HKD 500k — HKD 75/tx
    if (isChecked('large_domestic_toggle')) {
      monthly += val('large_domestic') * 75;
    }

    // International SWIFT receive — HKD 60/tx
    monthly += val('swift_receive') * 60;

    // International SWIFT send — HKD 85/tx
    monthly += val('swift_send') * 85;

    // USD incoming SWIFT additional fee — up to HKD 60/tx
    if (isChecked('usd_swift_toggle')) {
      monthly += val('usd_swift_receive') * 60;
    }

    // Local payments: USD, AUD, INR, EUR, GBP, SGD, IDR, PHP — HKD 25/tx
    monthly += val('local_major_send') * 25;

    // Local payments: THB, TRY, KRW — HKD 35/tx
    monthly += val('local_mid_send')   * 35;

    // Local payments: VND — HKD 50/tx
    monthly += val('local_vnd_send')   * 50;

    /* ── 4. MONTHLY FX fees ── */
    var monthly_fx = 0;

    var fxMajorVol  = val('fx_major_volume');
    var fxMajorRate = Math.max(0.10, val('fx_major_rate'));
    monthly_fx += fxMajorVol * (fxMajorRate / 100);

    var fxOtherVol  = val('fx_other_volume');
    var fxOtherRate = Math.max(0.15, val('fx_other_rate'));
    monthly_fx += fxOtherVol * (fxOtherRate / 100);

    /* ── 5. Volume fee for special companies ── */
    if (isChecked('special_company')) {
      var volPct  = Math.max(0.04, val('volume_fee_pct'));
      var volBase = val('monthly_volume_special');
      monthly += volBase * (volPct / 100);
    }

    /* ── Totals ── */
    var txAnnual  = monthly    * 12;
    var fxAnnual  = monthly_fx * 12;

    // First year = one-time + annual recurring + tx/fx for 12 months
    var firstYear  = oneTime + annual + txAnnual + fxAnnual;

    // Ongoing (year 2+): no one-time, no KYC — recalculate annual without those
    var annualOngoing = annual;
    // For year 2+, we keep annual renewal, inactivity, non-standard — same
    // KYC was one-time so it's already excluded (it's in oneTime, not annual)
    // BR renewal: add if 1-year plan chosen
    if (brEl && brEl.value === '1year') {
      annualOngoing += 2150;
    } else if (brEl && brEl.value === '3years') {
      annualOngoing += 3950 / 3; // amortised
    }

    var ongoingAnnual  = annualOngoing + txAnnual + fxAnnual;
    var monthlyAvg     = ongoingAnnual / 12;

    /* ── Update DOM ── */
    var rUSD = Math.max(1, val('rate_usd'));
    var rEUR = Math.max(1, val('rate_eur'));

    setResult('result_firstyear',  formatHKD(firstYear));
    setResult('result_onetime',    formatHKD(oneTime));
    setResult('result_annual',     formatHKD(annual));
    setResult('result_tx_annual',  formatHKD(txAnnual));
    setResult('result_fx_annual',  formatHKD(fxAnnual));
    setResult('result_ongoing',    formatHKD(ongoingAnnual));
    setResult('result_monthly_avg',formatHKD(monthlyAvg));
    setResult('result_usd',        formatUSD(firstYear, rUSD));
    setResult('result_eur',        formatEUR(firstYear, rEUR));
  }

  /* ───────────────────────────────
     Bind all inputs → recalculate
  ─────────────────────────────── */
  var page = document.querySelector('.hk-calc-page');
  if (page) {
    page.addEventListener('input',  calculate);
    page.addEventListener('change', calculate);
  }

  // Initial calculation on load
  calculate();

})();
