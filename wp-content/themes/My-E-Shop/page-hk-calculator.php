<?php
/*
 * Template Name: HK Company Cost Calculator
 * Template Post Type: page
 *
 * Calculator for the cost of opening and maintaining a company in Hong Kong
 * Data: statrys.com/terms-and-conditions/non-standard-fees
 *         statrys.com/hk/pricing
 */

get_header();
?>

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/hk-calculator.css">

<div class="hk-calc-page">

  <div class="hk-calc-hero">
    <div class="hk-calc-hero__inner">
      <div class="hk-calc-hero__badge">Hong Kong · HKD</div>
      <h1 class="hk-calc-hero__title">Cost calculator<br>for opening a company in Hong Kong</h1>
      <p class="hk-calc-hero__sub">Calculate the full picture of costs: registration, account, transactions, FX and non-standard fees — based on <a href="https://statrys.com" target="_blank" rel="noopener">Statrys</a> pricing</p>
    </div>
  </div>

  <div class="hk-calc-layout">

    <!-- ══════════════════════════════
         LEFT COLUMN — DATA ENTRY
    ══════════════════════════════ -->
    <div class="hk-calc-inputs">

      <!-- ─── SECTION 1: Registration ─── -->
      <div class="hk-card" id="section-registration">
        <button class="hk-section-toggle" data-target="reg-body" aria-expanded="true">
          <span class="hk-section-toggle__icon">🏢</span>
          <span>Company registration <em>(one-time)</em></span>
          <span class="hk-chevron">▼</span>
        </button>
        <div class="hk-section-body" id="reg-body">

          <p class="hk-hint">Mandatory government fees for registering a limited liability company in Hong Kong.</p>

          <div class="hk-field">
            <label class="hk-label hk-label--check">
              <input type="checkbox" id="cr_fee" checked>
              <span>Government registration fee (Companies Registry)</span>
            </label>
            <span class="hk-tag hk-tag--fixed">HKD 1&nbsp;720</span>
            <p class="hk-hint-sm">Fixed fee of the Hong Kong Companies Registry for registering a private limited company.</p>
          </div>

          <div class="hk-field">
            <label class="hk-label">Business Registration Certificate</label>
            <div class="hk-radio-group">
              <label class="hk-radio-item">
                <input type="radio" name="br_period" value="1year" checked>
                <span>1 year — HKD 2&nbsp;150</span>
              </label>
              <label class="hk-radio-item">
                <input type="radio" name="br_period" value="3years">
                <span>3 years — HKD 3&nbsp;950</span>
              </label>
              <label class="hk-radio-item">
                <input type="radio" name="br_period" value="none">
                <span>Do not include</span>
              </label>
            </div>
            <p class="hk-hint-sm">Required to conduct business in Hong Kong. The first year may be exempt from payment — check on the official IRD website.</p>
          </div>

          <div class="hk-divider"></div>
          <p class="hk-label--section">Statrys service package (optional)</p>

          <div class="hk-field">
            <label class="hk-label hk-label--check">
              <input type="checkbox" id="use_statrys" checked>
              <span>Use Statrys for registration and maintenance</span>
            </label>
          </div>

          <div id="statrys-fields">
            <div class="hk-field">
              <label class="hk-label" for="statrys_package">Package cost, year 1 (registration + secretary + address)</label>
              <div class="hk-input-row">
                <span class="hk-prefix">HKD</span>
                <input type="number" id="statrys_package" class="hk-input" value="7800" min="0" step="100">
              </div>
              <p class="hk-hint-sm">🔗 Check the current price at <a href="https://statrys.com/hk/company-registration" target="_blank" rel="noopener">statrys.com/hk/company-registration</a>. The amount includes: incorporation services, company secretary (year 1), registered address (year 1), mail scanning.</p>
            </div>

            <div class="hk-field">
              <label class="hk-label" for="statrys_renewal">Annual renewal (secretary + address, year 2+)</label>
              <div class="hk-input-row">
                <span class="hk-prefix">HKD</span>
                <input type="number" id="statrys_renewal" class="hk-input" value="4990" min="0" step="100">
              </div>
              <p class="hk-hint-sm">The annual subscription renews automatically 2 months before expiry.</p>
            </div>
          </div>

        </div>
      </div>

      <!-- ─── SECTION 2: Business account ─── -->
      <div class="hk-card" id="section-account">
        <button class="hk-section-toggle" data-target="acc-body" aria-expanded="true">
          <span class="hk-section-toggle__icon">🏦</span>
          <span>Business account <em>(setup)</em></span>
          <span class="hk-chevron">▼</span>
        </button>
        <div class="hk-section-body" id="acc-body">

          <div class="hk-info-row">
            <span>Multi-currency account (11 currencies)</span>
            <span class="hk-tag hk-tag--free">Free</span>
          </div>
          <div class="hk-info-row">
            <span>Monthly fee</span>
            <span class="hk-tag hk-tag--free">None</span>
          </div>
          <div class="hk-info-row">
            <span>Minimum deposit</span>
            <span class="hk-tag hk-tag--free">None</span>
          </div>

          <div class="hk-divider"></div>

          <div class="hk-field">
            <label class="hk-label hk-label--check">
              <input type="checkbox" id="special_company">
              <span>"Special" company (Special Company)</span>
            </label>
            <p class="hk-hint-sm">Companies registered outside Hong Kong / Singapore, or with a complex structure / a specific business profile. Affects KYC and Volume Fee.</p>
          </div>

          <div id="special-fields" class="hk-hidden">
            <div class="hk-field hk-field--indent">
              <label class="hk-label">KYC Fee (one-time)</label>
              <div class="hk-radio-group">
                <label class="hk-radio-item">
                  <input type="radio" name="kyc_fee" value="1500" checked>
                  <span>Standard — HKD 1&nbsp;500</span>
                </label>
                <label class="hk-radio-item">
                  <input type="radio" name="kyc_fee" value="3900">
                  <span>Enhanced — HKD 3&nbsp;900</span>
                </label>
              </div>
            </div>

            <div class="hk-field hk-field--indent">
              <label class="hk-label" for="volume_fee_pct">Volume Fee (% of monthly payment volume)</label>
              <div class="hk-input-row">
                <input type="number" id="volume_fee_pct" class="hk-input" value="0.04" min="0.04" step="0.01">
                <span class="hk-suffix">%</span>
              </div>
              <p class="hk-hint-sm">Minimum rate — 0.04%. Applies only to "special" companies.</p>
            </div>

            <div class="hk-field hk-field--indent">
              <label class="hk-label" for="monthly_volume_special">Monthly payment volume (for calculating Volume Fee)</label>
              <div class="hk-input-row">
                <span class="hk-prefix">HKD</span>
                <input type="number" id="monthly_volume_special" class="hk-input" value="0" min="0" step="1000">
              </div>
            </div>
          </div>

          <div class="hk-divider"></div>

          <div class="hk-field">
            <label class="hk-label" for="inactive_months">Months of inactivity (<5 outgoing payments)</label>
            <div class="hk-input-row">
              <input type="number" id="inactive_months" class="hk-input hk-input--sm" value="0" min="0" max="12" step="1">
              <span class="hk-suffix">mo/year × HKD 88</span>
            </div>
            <p class="hk-hint-sm">Inactivity fee: HKD 88 for each month with fewer than 5 outgoing payments.</p>
          </div>

        </div>
      </div>

      <!-- ─── SECTION 3: Transactions ─── -->
      <div class="hk-card" id="section-transactions">
        <button class="hk-section-toggle" data-target="tx-body" aria-expanded="true">
          <span class="hk-section-toggle__icon">💳</span>
          <span>Transactions <em>(monthly volume)</em></span>
          <span class="hk-chevron">▼</span>
        </button>
        <div class="hk-section-body" id="tx-body">

          <p class="hk-hint">Enter the average number of transactions of each type <strong>per month</strong>.</p>

          <p class="hk-label--section">🇭🇰 Domestic payments (HKD / CNY)</p>

          <div class="hk-tx-row">
            <div class="hk-tx-label">
              <span>Receive (Collect)</span>
              <span class="hk-tag hk-tag--free">HKD 0</span>
            </div>
            <div class="hk-tx-input">
              <input type="number" id="domestic_receive" class="hk-input hk-input--sm" value="0" min="0" step="1">
              <span class="hk-suffix">pcs/mo</span>
            </div>
          </div>

          <div class="hk-tx-row">
            <div class="hk-tx-label">
              <span>Send</span>
              <span class="hk-tag">HKD 5 / pc</span>
            </div>
            <div class="hk-tx-input">
              <input type="number" id="domestic_send" class="hk-input hk-input--sm" value="0" min="0" step="1">
              <span class="hk-suffix">pcs/mo</span>
            </div>
          </div>

          <div class="hk-field">
            <label class="hk-label hk-label--check">
              <input type="checkbox" id="large_domestic_toggle">
              <span>Large domestic payments (&gt;HKD 500&#8239;000 or CNY equivalent)</span>
            </label>
          </div>
          <div id="large-domestic-field" class="hk-hidden">
            <div class="hk-tx-row hk-field--indent">
              <div class="hk-tx-label">
                <span>Large outgoing payment&nbsp;&gt;&nbsp;HKD 500k</span>
                <span class="hk-tag">HKD 75 / pc</span>
              </div>
              <div class="hk-tx-input">
                <input type="number" id="large_domestic" class="hk-input hk-input--sm" value="0" min="0" step="1">
                <span class="hk-suffix">pcs/mo</span>
              </div>
            </div>
          </div>

          <div class="hk-divider"></div>
          <p class="hk-label--section">🌍 International SWIFT payments</p>

          <div class="hk-tx-row">
            <div class="hk-tx-label">
              <span>Receive SWIFT</span>
              <span class="hk-tag">HKD 60 / pc</span>
            </div>
            <div class="hk-tx-input">
              <input type="number" id="swift_receive" class="hk-input hk-input--sm" value="0" min="0" step="1">
              <span class="hk-suffix">pcs/mo</span>
            </div>
          </div>

          <div class="hk-tx-row">
            <div class="hk-tx-label">
              <span>Send SWIFT</span>
              <span class="hk-tag">HKD 85 / pc</span>
            </div>
            <div class="hk-tx-input">
              <input type="number" id="swift_send" class="hk-input hk-input--sm" value="0" min="0" step="1">
              <span class="hk-suffix">pcs/mo</span>
            </div>
          </div>

          <div class="hk-field">
            <label class="hk-label hk-label--check">
              <input type="checkbox" id="usd_swift_toggle">
              <span>Incoming USD SWIFT (correspondent bank charges an intermediary fee)</span>
            </label>
            <p class="hk-hint-sm">Additional fee of up to HKD 60 per incoming USD SWIFT transfer (on top of the standard HKD 60 for receiving).</p>
          </div>
          <div id="usd-swift-field" class="hk-hidden">
            <div class="hk-tx-row hk-field--indent">
              <div class="hk-tx-label">
                <span>Incoming USD SWIFT</span>
                <span class="hk-tag">up to HKD 60 / pc</span>
              </div>
              <div class="hk-tx-input">
                <input type="number" id="usd_swift_receive" class="hk-input hk-input--sm" value="0" min="0" step="1">
                <span class="hk-suffix">pcs/mo</span>
              </div>
            </div>
          </div>

          <div class="hk-divider"></div>
          <p class="hk-label--section">📡 Local payments in foreign currency</p>
          <p class="hk-hint-sm">USA, Canada, UK, EU, Australia, India, Indonesia, Philippines, Thailand, Vietnam, Korea, Turkey</p>

          <div class="hk-tx-row">
            <div class="hk-tx-label">
              <span>USD, AUD, INR, EUR, GBP, SGD, IDR, PHP</span>
              <span class="hk-tag">HKD 25 / pc</span>
            </div>
            <div class="hk-tx-input">
              <input type="number" id="local_major_send" class="hk-input hk-input--sm" value="0" min="0" step="1">
              <span class="hk-suffix">pcs/mo</span>
            </div>
          </div>

          <div class="hk-tx-row">
            <div class="hk-tx-label">
              <span>THB, TRY, KRW</span>
              <span class="hk-tag">HKD 35 / pc</span>
            </div>
            <div class="hk-tx-input">
              <input type="number" id="local_mid_send" class="hk-input hk-input--sm" value="0" min="0" step="1">
              <span class="hk-suffix">pcs/mo</span>
            </div>
          </div>

          <div class="hk-tx-row">
            <div class="hk-tx-label">
              <span>VND</span>
              <span class="hk-tag">HKD 50 / pc</span>
            </div>
            <div class="hk-tx-input">
              <input type="number" id="local_vnd_send" class="hk-input hk-input--sm" value="0" min="0" step="1">
              <span class="hk-suffix">pcs/mo</span>
            </div>
          </div>

        </div>
      </div>

      <!-- ─── SECTION 4: FX / Conversion ─── -->
      <div class="hk-card" id="section-fx">
        <button class="hk-section-toggle" data-target="fx-body" aria-expanded="false">
          <span class="hk-section-toggle__icon">💱</span>
          <span>Currency exchange (FX) <em>(optional)</em></span>
          <span class="hk-chevron">▼</span>
        </button>
        <div class="hk-section-body hk-hidden" id="fx-body">

          <p class="hk-hint">Enter the monthly volume of FX conversions to estimate approximate exchange-rate costs.</p>

          <p class="hk-label--section">Major currencies (from 0.1%)</p>
          <p class="hk-hint-sm">HKD, USD, EUR, CNY, GBP, SGD, JPY, AUD, CHF, NZD, CAD</p>

          <div class="hk-field">
            <label class="hk-label" for="fx_major_volume">Conversion volume per month</label>
            <div class="hk-input-row">
              <span class="hk-prefix">HKD</span>
              <input type="number" id="fx_major_volume" class="hk-input" value="0" min="0" step="1000">
            </div>
          </div>
          <div class="hk-field">
            <label class="hk-label" for="fx_major_rate">Applied rate (%)</label>
            <div class="hk-input-row">
              <input type="number" id="fx_major_rate" class="hk-input hk-input--sm" value="0.10" min="0.10" max="1" step="0.01">
              <span class="hk-suffix">% (min. 0.10%)</span>
            </div>
          </div>

          <div class="hk-divider"></div>
          <p class="hk-label--section">Other currencies (from 0.15%)</p>
          <p class="hk-hint-sm">INR, IDR, PHP, KRW, THB, TRY, VND</p>

          <div class="hk-field">
            <label class="hk-label" for="fx_other_volume">Conversion volume per month</label>
            <div class="hk-input-row">
              <span class="hk-prefix">HKD</span>
              <input type="number" id="fx_other_volume" class="hk-input" value="0" min="0" step="1000">
            </div>
          </div>
          <div class="hk-field">
            <label class="hk-label" for="fx_other_rate">Applied rate (%)</label>
            <div class="hk-input-row">
              <input type="number" id="fx_other_rate" class="hk-input hk-input--sm" value="0.15" min="0.15" max="1" step="0.01">
              <span class="hk-suffix">% (min. 0.15%)</span>
            </div>
          </div>

        </div>
      </div>

      <!-- ─── SECTION 5: Non-standard fees ─── -->
      <div class="hk-card" id="section-nonstandard">
        <button class="hk-section-toggle" data-target="ns-body" aria-expanded="false">
          <span class="hk-section-toggle__icon">⚡</span>
          <span>Non-standard fees <em>(one-off/rare)</em></span>
          <span class="hk-chevron">▼</span>
        </button>
        <div class="hk-section-body hk-hidden" id="ns-body">

          <p class="hk-hint">Add one-off or occasional non-standard costs. Source: <a href="https://statrys.com/terms-and-conditions/non-standard-fees" target="_blank" rel="noopener">statrys.com/terms-and-conditions/non-standard-fees</a></p>

          <div class="hk-ns-row">
            <div class="hk-ns-info">
              <strong>Account closure certificate</strong>
              <em>HKD 450 / pc</em>
              <small>Confirmation that the account is closed and has no liabilities</small>
            </div>
            <div class="hk-ns-input">
              <input type="number" id="ns_closure_cert" class="hk-input hk-input--sm" value="0" min="0" step="1">
              <span class="hk-suffix">pcs</span>
            </div>
          </div>

          <div class="hk-ns-row">
            <div class="hk-ns-info">
              <strong>Good Standing Certificate</strong>
              <em>HKD 450 / pc</em>
              <small>Confirmation that the account is active and has no encumbrances</small>
            </div>
            <div class="hk-ns-input">
              <input type="number" id="ns_gsc" class="hk-input hk-input--sm" value="0" min="0" step="1">
              <span class="hk-suffix">pcs</span>
            </div>
          </div>

          <div class="hk-ns-row">
            <div class="hk-ns-info">
              <strong>Audit Confirmation Letter</strong>
              <em>HKD 500 (electronic) / HKD 600 (physical)</em>
            </div>
            <div class="hk-ns-input hk-ns-input--flex">
              <div class="hk-radio-group hk-radio-group--inline">
                <label class="hk-radio-item">
                  <input type="radio" name="audit_type" value="500" checked>
                  <span>Elec.</span>
                </label>
                <label class="hk-radio-item">
                  <input type="radio" name="audit_type" value="600">
                  <span>Phys.</span>
                </label>
              </div>
              <input type="number" id="ns_audit" class="hk-input hk-input--sm" value="0" min="0" step="1">
              <span class="hk-suffix">pcs</span>
            </div>
          </div>

          <div class="hk-ns-row">
            <div class="hk-ns-info">
              <strong>Bank statement with manual processing</strong>
              <em>HKD 250 / pc</em>
            </div>
            <div class="hk-ns-input">
              <input type="number" id="ns_statement" class="hk-input hk-input--sm" value="0" min="0" step="1">
              <span class="hk-suffix">pcs</span>
            </div>
          </div>

          <div class="hk-ns-row">
            <div class="hk-ns-info">
              <strong>Transaction cancellation / amendment</strong>
              <em>HKD 200–500 / pc</em>
            </div>
            <div class="hk-ns-input hk-ns-input--flex">
              <div class="hk-radio-group hk-radio-group--inline">
                <label class="hk-radio-item">
                  <input type="radio" name="amendment_fee" value="200" checked>
                  <span>200</span>
                </label>
                <label class="hk-radio-item">
                  <input type="radio" name="amendment_fee" value="350">
                  <span>350</span>
                </label>
                <label class="hk-radio-item">
                  <input type="radio" name="amendment_fee" value="500">
                  <span>500</span>
                </label>
              </div>
              <input type="number" id="ns_amendment" class="hk-input hk-input--sm" value="0" min="0" step="1">
              <span class="hk-suffix">pcs</span>
            </div>
          </div>

          <div class="hk-ns-row">
            <div class="hk-ns-info">
              <strong>Additional services (other)</strong>
              <em>Enter the amount manually</em>
            </div>
            <div class="hk-ns-input">
              <span class="hk-prefix">HKD</span>
              <input type="number" id="ns_other" class="hk-input" value="0" min="0" step="100">
            </div>
          </div>

        </div>
      </div>

    </div><!-- /.hk-calc-inputs -->

    <!-- ══════════════════════════════
         RIGHT COLUMN — RESULT
    ══════════════════════════════ -->
    <div class="hk-calc-summary">
      <div class="hk-summary-card" id="summary-sticky">

        <div class="hk-summary-header">
          <span>📊</span>
          <h2>Total calculation</h2>
        </div>

        <div class="hk-summary-block hk-summary-block--highlight">
          <p class="hk-summary-label">First year (total)</p>
          <p class="hk-summary-amount" id="result_firstyear">HKD 0</p>
        </div>

        <div class="hk-summary-divider"></div>

        <div class="hk-summary-breakdown">
          <div class="hk-summary-row">
            <span>One-time (setup)</span>
            <strong id="result_onetime">HKD 0</strong>
          </div>
          <div class="hk-summary-row">
            <span>Annual costs</span>
            <strong id="result_annual">HKD 0</strong>
          </div>
          <div class="hk-summary-row">
            <span>Monthly transactions × 12</span>
            <strong id="result_tx_annual">HKD 0</strong>
          </div>
          <div class="hk-summary-row">
            <span>FX conversion × 12</span>
            <strong id="result_fx_annual">HKD 0</strong>
          </div>
        </div>

        <div class="hk-summary-divider"></div>

        <div class="hk-summary-block">
          <p class="hk-summary-label">Annually (year 2+)</p>
          <p class="hk-summary-amount hk-summary-amount--sm" id="result_ongoing">HKD 0</p>
        </div>

        <div class="hk-summary-block">
          <p class="hk-summary-label">On average per month (year 2+)</p>
          <p class="hk-summary-amount hk-summary-amount--sm" id="result_monthly_avg">HKD 0</p>
        </div>

        <div class="hk-summary-divider"></div>

        <div class="hk-summary-convert">
          <p class="hk-summary-label">Conversion rate</p>
          <div class="hk-convert-row">
            <label>
              <span>USD/HKD</span>
              <input type="number" id="rate_usd" class="hk-input hk-input--mini" value="7.80" step="0.01" min="1">
            </label>
            <label>
              <span>EUR/HKD</span>
              <input type="number" id="rate_eur" class="hk-input hk-input--mini" value="8.50" step="0.01" min="1">
            </label>
          </div>
          <div class="hk-convert-result">
            <div>First year ≈ <strong id="result_usd">$0</strong> USD</div>
            <div>First year ≈ <strong id="result_eur">€0</strong> EUR</div>
          </div>
        </div>

        <div class="hk-summary-footer">
          <p>📌 Data from <a href="https://statrys.com/terms-and-conditions/non-standard-fees" target="_blank" rel="noopener">Statrys Non-Standard Fees</a> and <a href="https://statrys.com/hk/pricing" target="_blank" rel="noopener">Statrys Pricing</a>. Rates may change. Current prices are on the Statrys website.</p>
        </div>

      </div>
    </div>

  </div><!-- /.hk-calc-layout -->

</div><!-- /.hk-calc-page -->

<script src="<?php echo get_template_directory_uri(); ?>/assets/js/hk-calculator.js"></script>

<?php get_footer(); ?>
