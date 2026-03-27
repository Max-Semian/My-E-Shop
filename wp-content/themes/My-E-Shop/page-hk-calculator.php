<?php
/*
 * Template Name: HK Company Cost Calculator
 * Template Post Type: page
 *
 * Калькулятор стоимости открытия и обслуживания компании в Гонконге
 * Данные: statrys.com/terms-and-conditions/non-standard-fees
 *         statrys.com/hk/pricing
 */

get_header();
?>

<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/assets/css/hk-calculator.css">

<div class="hk-calc-page">

  <div class="hk-calc-hero">
    <div class="hk-calc-hero__inner">
      <div class="hk-calc-hero__badge">Гонконг · HKD</div>
      <h1 class="hk-calc-hero__title">Калькулятор стоимости<br>открытия компании в Гонконге</h1>
      <p class="hk-calc-hero__sub">Рассчитайте полную картину расходов: регистрация, счёт, транзакции, FX и нестандартные комиссии — на основе тарифов <a href="https://statrys.com" target="_blank" rel="noopener">Statrys</a></p>
    </div>
  </div>

  <div class="hk-calc-layout">

    <!-- ══════════════════════════════
         ЛЕВАЯ КОЛОНКА — ВВОД ДАННЫХ
    ══════════════════════════════ -->
    <div class="hk-calc-inputs">

      <!-- ─── СЕКЦИЯ 1: Регистрация ─── -->
      <div class="hk-card" id="section-registration">
        <button class="hk-section-toggle" data-target="reg-body" aria-expanded="true">
          <span class="hk-section-toggle__icon">🏢</span>
          <span>Регистрация компании <em>(единовременно)</em></span>
          <span class="hk-chevron">▼</span>
        </button>
        <div class="hk-section-body" id="reg-body">

          <p class="hk-hint">Обязательные государственные сборы при регистрации компании с ограниченной ответственностью в Гонконге.</p>

          <div class="hk-field">
            <label class="hk-label hk-label--check">
              <input type="checkbox" id="cr_fee" checked>
              <span>Государственная пошлина за регистрацию (Companies Registry)</span>
            </label>
            <span class="hk-tag hk-tag--fixed">HKD 1&nbsp;720</span>
            <p class="hk-hint-sm">Фиксированная пошлина Реестра компаний Гонконга за регистрацию private limited company.</p>
          </div>

          <div class="hk-field">
            <label class="hk-label">Свидетельство о регистрации бизнеса (Business Registration Certificate)</label>
            <div class="hk-radio-group">
              <label class="hk-radio-item">
                <input type="radio" name="br_period" value="1year" checked>
                <span>1 год — HKD 2&nbsp;150</span>
              </label>
              <label class="hk-radio-item">
                <input type="radio" name="br_period" value="3years">
                <span>3 года — HKD 3&nbsp;950</span>
              </label>
              <label class="hk-radio-item">
                <input type="radio" name="br_period" value="none">
                <span>Не включать</span>
              </label>
            </div>
            <p class="hk-hint-sm">Обязателен для ведения бизнеса в Гонконге. Первый год может быть освобождён от оплаты — уточните на официальном сайте IRD.</p>
          </div>

          <div class="hk-divider"></div>
          <p class="hk-label--section">Пакет услуг Statrys (необязательно)</p>

          <div class="hk-field">
            <label class="hk-label hk-label--check">
              <input type="checkbox" id="use_statrys" checked>
              <span>Использовать Statrys для регистрации и обслуживания</span>
            </label>
          </div>

          <div id="statrys-fields">
            <div class="hk-field">
              <label class="hk-label" for="statrys_package">Стоимость пакета 1-й год (регистрация + секретарь + адрес)</label>
              <div class="hk-input-row">
                <span class="hk-prefix">HKD</span>
                <input type="number" id="statrys_package" class="hk-input" value="7800" min="0" step="100">
              </div>
              <p class="hk-hint-sm">🔗 Актуальную цену уточните на <a href="https://statrys.com/hk/company-registration" target="_blank" rel="noopener">statrys.com/hk/company-registration</a>. В сумму входят: услуги инкорпорации, секретарь компании (год 1), зарегистрированный адрес (год 1), сканирование почты.</p>
            </div>

            <div class="hk-field">
              <label class="hk-label" for="statrys_renewal">Ежегодное продление (секретарь + адрес, год 2+)</label>
              <div class="hk-input-row">
                <span class="hk-prefix">HKD</span>
                <input type="number" id="statrys_renewal" class="hk-input" value="4990" min="0" step="100">
              </div>
              <p class="hk-hint-sm">Ежегодная подписка автоматически продлевается за 2 месяца до истечения.</p>
            </div>
          </div>

        </div>
      </div>

      <!-- ─── СЕКЦИЯ 2: Бизнес-счёт ─── -->
      <div class="hk-card" id="section-account">
        <button class="hk-section-toggle" data-target="acc-body" aria-expanded="true">
          <span class="hk-section-toggle__icon">🏦</span>
          <span>Бизнес-счёт <em>(настройка)</em></span>
          <span class="hk-chevron">▼</span>
        </button>
        <div class="hk-section-body" id="acc-body">

          <div class="hk-info-row">
            <span>Мульти-валютный счёт (11 валют)</span>
            <span class="hk-tag hk-tag--free">Бесплатно</span>
          </div>
          <div class="hk-info-row">
            <span>Ежемесячная плата</span>
            <span class="hk-tag hk-tag--free">Нет</span>
          </div>
          <div class="hk-info-row">
            <span>Минимальный депозит</span>
            <span class="hk-tag hk-tag--free">Нет</span>
          </div>

          <div class="hk-divider"></div>

          <div class="hk-field">
            <label class="hk-label hk-label--check">
              <input type="checkbox" id="special_company">
              <span>«Особая» компания (Special Company)</span>
            </label>
            <p class="hk-hint-sm">Компании, зарегистрированные не в Гонконге / Сингапуре, или имеющие сложную структуру / определённый профиль бизнеса. Влияет на KYC и Volume Fee.</p>
          </div>

          <div id="special-fields" class="hk-hidden">
            <div class="hk-field hk-field--indent">
              <label class="hk-label">KYC Fee (единовременно)</label>
              <div class="hk-radio-group">
                <label class="hk-radio-item">
                  <input type="radio" name="kyc_fee" value="1500" checked>
                  <span>Стандартный — HKD 1&nbsp;500</span>
                </label>
                <label class="hk-radio-item">
                  <input type="radio" name="kyc_fee" value="3900">
                  <span>Расширенный — HKD 3&nbsp;900</span>
                </label>
              </div>
            </div>

            <div class="hk-field hk-field--indent">
              <label class="hk-label" for="volume_fee_pct">Volume Fee (% от объёма платежей в месяц)</label>
              <div class="hk-input-row">
                <input type="number" id="volume_fee_pct" class="hk-input" value="0.04" min="0.04" step="0.01">
                <span class="hk-suffix">%</span>
              </div>
              <p class="hk-hint-sm">Минимальная ставка — 0.04%. Применяется только к «особым» компаниям.</p>
            </div>

            <div class="hk-field hk-field--indent">
              <label class="hk-label" for="monthly_volume_special">Ежемесячный объём платежей (для расчёта Volume Fee)</label>
              <div class="hk-input-row">
                <span class="hk-prefix">HKD</span>
                <input type="number" id="monthly_volume_special" class="hk-input" value="0" min="0" step="1000">
              </div>
            </div>
          </div>

          <div class="hk-divider"></div>

          <div class="hk-field">
            <label class="hk-label" for="inactive_months">Месяцев с неактивностью (<5 исходящих платежей)</label>
            <div class="hk-input-row">
              <input type="number" id="inactive_months" class="hk-input hk-input--sm" value="0" min="0" max="12" step="1">
              <span class="hk-suffix">мес/год × HKD 88</span>
            </div>
            <p class="hk-hint-sm">Комиссия за неактивность: HKD 88 за каждый месяц, в котором совершено менее 5 исходящих платежей.</p>
          </div>

        </div>
      </div>

      <!-- ─── СЕКЦИЯ 3: Транзакции ─── -->
      <div class="hk-card" id="section-transactions">
        <button class="hk-section-toggle" data-target="tx-body" aria-expanded="true">
          <span class="hk-section-toggle__icon">💳</span>
          <span>Транзакции <em>(ежемесячный объём)</em></span>
          <span class="hk-chevron">▼</span>
        </button>
        <div class="hk-section-body" id="tx-body">

          <p class="hk-hint">Введите среднее количество транзакций каждого типа <strong>в месяц</strong>.</p>

          <p class="hk-label--section">🇭🇰 Внутренние платежи (HKD / CNY)</p>

          <div class="hk-tx-row">
            <div class="hk-tx-label">
              <span>Получение (Collect)</span>
              <span class="hk-tag hk-tag--free">HKD 0</span>
            </div>
            <div class="hk-tx-input">
              <input type="number" id="domestic_receive" class="hk-input hk-input--sm" value="0" min="0" step="1">
              <span class="hk-suffix">шт/мес</span>
            </div>
          </div>

          <div class="hk-tx-row">
            <div class="hk-tx-label">
              <span>Отправка (Send)</span>
              <span class="hk-tag">HKD 5 / шт</span>
            </div>
            <div class="hk-tx-input">
              <input type="number" id="domestic_send" class="hk-input hk-input--sm" value="0" min="0" step="1">
              <span class="hk-suffix">шт/мес</span>
            </div>
          </div>

          <div class="hk-field">
            <label class="hk-label hk-label--check">
              <input type="checkbox" id="large_domestic_toggle">
              <span>Крупные внутренние платежи (&gt;HKD 500&#8239;000 или эквивалент CNY)</span>
            </label>
          </div>
          <div id="large-domestic-field" class="hk-hidden">
            <div class="hk-tx-row hk-field--indent">
              <div class="hk-tx-label">
                <span>Крупный исходящий платёж&nbsp;&gt;&nbsp;HKD 500k</span>
                <span class="hk-tag">HKD 75 / шт</span>
              </div>
              <div class="hk-tx-input">
                <input type="number" id="large_domestic" class="hk-input hk-input--sm" value="0" min="0" step="1">
                <span class="hk-suffix">шт/мес</span>
              </div>
            </div>
          </div>

          <div class="hk-divider"></div>
          <p class="hk-label--section">🌍 Международные SWIFT-платежи</p>

          <div class="hk-tx-row">
            <div class="hk-tx-label">
              <span>Получение SWIFT</span>
              <span class="hk-tag">HKD 60 / шт</span>
            </div>
            <div class="hk-tx-input">
              <input type="number" id="swift_receive" class="hk-input hk-input--sm" value="0" min="0" step="1">
              <span class="hk-suffix">шт/мес</span>
            </div>
          </div>

          <div class="hk-tx-row">
            <div class="hk-tx-label">
              <span>Отправка SWIFT</span>
              <span class="hk-tag">HKD 85 / шт</span>
            </div>
            <div class="hk-tx-input">
              <input type="number" id="swift_send" class="hk-input hk-input--sm" value="0" min="0" step="1">
              <span class="hk-suffix">шт/мес</span>
            </div>
          </div>

          <div class="hk-field">
            <label class="hk-label hk-label--check">
              <input type="checkbox" id="usd_swift_toggle">
              <span>Входящие USD SWIFT (банк-корреспондент снимает посредническую комиссию)</span>
            </label>
            <p class="hk-hint-sm">Дополнительная комиссия до HKD 60 за входящий USD SWIFT-перевод (сверх стандартного HKD 60 за получение).</p>
          </div>
          <div id="usd-swift-field" class="hk-hidden">
            <div class="hk-tx-row hk-field--indent">
              <div class="hk-tx-label">
                <span>Входящие USD SWIFT</span>
                <span class="hk-tag">до HKD 60 / шт</span>
              </div>
              <div class="hk-tx-input">
                <input type="number" id="usd_swift_receive" class="hk-input hk-input--sm" value="0" min="0" step="1">
                <span class="hk-suffix">шт/мес</span>
              </div>
            </div>
          </div>

          <div class="hk-divider"></div>
          <p class="hk-label--section">📡 Локальные платежи в иностранной валюте</p>
          <p class="hk-hint-sm">США, Канада, Великобритания, ЕС, Австралия, Индия, Индонезия, Филиппины, Таиланд, Вьетнам, Корея, Турция</p>

          <div class="hk-tx-row">
            <div class="hk-tx-label">
              <span>USD, AUD, INR, EUR, GBP, SGD, IDR, PHP</span>
              <span class="hk-tag">HKD 25 / шт</span>
            </div>
            <div class="hk-tx-input">
              <input type="number" id="local_major_send" class="hk-input hk-input--sm" value="0" min="0" step="1">
              <span class="hk-suffix">шт/мес</span>
            </div>
          </div>

          <div class="hk-tx-row">
            <div class="hk-tx-label">
              <span>THB, TRY, KRW</span>
              <span class="hk-tag">HKD 35 / шт</span>
            </div>
            <div class="hk-tx-input">
              <input type="number" id="local_mid_send" class="hk-input hk-input--sm" value="0" min="0" step="1">
              <span class="hk-suffix">шт/мес</span>
            </div>
          </div>

          <div class="hk-tx-row">
            <div class="hk-tx-label">
              <span>VND</span>
              <span class="hk-tag">HKD 50 / шт</span>
            </div>
            <div class="hk-tx-input">
              <input type="number" id="local_vnd_send" class="hk-input hk-input--sm" value="0" min="0" step="1">
              <span class="hk-suffix">шт/мес</span>
            </div>
          </div>

        </div>
      </div>

      <!-- ─── СЕКЦИЯ 4: FX / Конвертация ─── -->
      <div class="hk-card" id="section-fx">
        <button class="hk-section-toggle" data-target="fx-body" aria-expanded="false">
          <span class="hk-section-toggle__icon">💱</span>
          <span>Валютный обмен (FX) <em>(необязательно)</em></span>
          <span class="hk-chevron">▼</span>
        </button>
        <div class="hk-section-body hk-hidden" id="fx-body">

          <p class="hk-hint">Введите ежемесячный объём FX-конвертаций для расчёта примерных расходов на курсовую разницу.</p>

          <p class="hk-label--section">Основные валюты (from 0.1%)</p>
          <p class="hk-hint-sm">HKD, USD, EUR, CNY, GBP, SGD, JPY, AUD, CHF, NZD, CAD</p>

          <div class="hk-field">
            <label class="hk-label" for="fx_major_volume">Объём конвертации в месяц</label>
            <div class="hk-input-row">
              <span class="hk-prefix">HKD</span>
              <input type="number" id="fx_major_volume" class="hk-input" value="0" min="0" step="1000">
            </div>
          </div>
          <div class="hk-field">
            <label class="hk-label" for="fx_major_rate">Применяемая ставка (%)</label>
            <div class="hk-input-row">
              <input type="number" id="fx_major_rate" class="hk-input hk-input--sm" value="0.10" min="0.10" max="1" step="0.01">
              <span class="hk-suffix">% (мин. 0.10%)</span>
            </div>
          </div>

          <div class="hk-divider"></div>
          <p class="hk-label--section">Прочие валюты (from 0.15%)</p>
          <p class="hk-hint-sm">INR, IDR, PHP, KRW, THB, TRY, VND</p>

          <div class="hk-field">
            <label class="hk-label" for="fx_other_volume">Объём конвертации в месяц</label>
            <div class="hk-input-row">
              <span class="hk-prefix">HKD</span>
              <input type="number" id="fx_other_volume" class="hk-input" value="0" min="0" step="1000">
            </div>
          </div>
          <div class="hk-field">
            <label class="hk-label" for="fx_other_rate">Применяемая ставка (%)</label>
            <div class="hk-input-row">
              <input type="number" id="fx_other_rate" class="hk-input hk-input--sm" value="0.15" min="0.15" max="1" step="0.01">
              <span class="hk-suffix">% (мин. 0.15%)</span>
            </div>
          </div>

        </div>
      </div>

      <!-- ─── СЕКЦИЯ 5: Нестандартные комиссии ─── -->
      <div class="hk-card" id="section-nonstandard">
        <button class="hk-section-toggle" data-target="ns-body" aria-expanded="false">
          <span class="hk-section-toggle__icon">⚡</span>
          <span>Нестандартные комиссии <em>(разовые/редкие)</em></span>
          <span class="hk-chevron">▼</span>
        </button>
        <div class="hk-section-body hk-hidden" id="ns-body">

          <p class="hk-hint">Добавьте разовые или периодические нестандартные расходы. Источник: <a href="https://statrys.com/terms-and-conditions/non-standard-fees" target="_blank" rel="noopener">statrys.com/terms-and-conditions/non-standard-fees</a></p>

          <div class="hk-ns-row">
            <div class="hk-ns-info">
              <strong>Справка о закрытии счёта</strong>
              <em>HKD 450 / шт</em>
              <small>Подтверждение, что счёт закрыт и не имеет обязательств</small>
            </div>
            <div class="hk-ns-input">
              <input type="number" id="ns_closure_cert" class="hk-input hk-input--sm" value="0" min="0" step="1">
              <span class="hk-suffix">шт</span>
            </div>
          </div>

          <div class="hk-ns-row">
            <div class="hk-ns-info">
              <strong>Справка об активном статусе (Good Standing Certificate)</strong>
              <em>HKD 450 / шт</em>
              <small>Подтверждение, что счёт активен и не имеет обременений</small>
            </div>
            <div class="hk-ns-input">
              <input type="number" id="ns_gsc" class="hk-input hk-input--sm" value="0" min="0" step="1">
              <span class="hk-suffix">шт</span>
            </div>
          </div>

          <div class="hk-ns-row">
            <div class="hk-ns-info">
              <strong>Письмо для аудиторов (Audit Confirmation Letter)</strong>
              <em>HKD 500 (электронное) / HKD 600 (физическое)</em>
            </div>
            <div class="hk-ns-input hk-ns-input--flex">
              <div class="hk-radio-group hk-radio-group--inline">
                <label class="hk-radio-item">
                  <input type="radio" name="audit_type" value="500" checked>
                  <span>Эл.</span>
                </label>
                <label class="hk-radio-item">
                  <input type="radio" name="audit_type" value="600">
                  <span>Физ.</span>
                </label>
              </div>
              <input type="number" id="ns_audit" class="hk-input hk-input--sm" value="0" min="0" step="1">
              <span class="hk-suffix">шт</span>
            </div>
          </div>

          <div class="hk-ns-row">
            <div class="hk-ns-info">
              <strong>Банковская выписка с ручной обработкой</strong>
              <em>HKD 250 / шт</em>
            </div>
            <div class="hk-ns-input">
              <input type="number" id="ns_statement" class="hk-input hk-input--sm" value="0" min="0" step="1">
              <span class="hk-suffix">шт</span>
            </div>
          </div>

          <div class="hk-ns-row">
            <div class="hk-ns-info">
              <strong>Отмена / изменение транзакции</strong>
              <em>HKD 200–500 / шт</em>
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
              <span class="hk-suffix">шт</span>
            </div>
          </div>

          <div class="hk-ns-row">
            <div class="hk-ns-info">
              <strong>Дополнительные услуги (прочее)</strong>
              <em>Введите сумму вручную</em>
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
         ПРАВАЯ КОЛОНКА — РЕЗУЛЬТАТ
    ══════════════════════════════ -->
    <div class="hk-calc-summary">
      <div class="hk-summary-card" id="summary-sticky">

        <div class="hk-summary-header">
          <span>📊</span>
          <h2>Итоговый расчёт</h2>
        </div>

        <div class="hk-summary-block hk-summary-block--highlight">
          <p class="hk-summary-label">Первый год (всего)</p>
          <p class="hk-summary-amount" id="result_firstyear">HKD 0</p>
        </div>

        <div class="hk-summary-divider"></div>

        <div class="hk-summary-breakdown">
          <div class="hk-summary-row">
            <span>Единовременно (организация)</span>
            <strong id="result_onetime">HKD 0</strong>
          </div>
          <div class="hk-summary-row">
            <span>Ежегодные расходы</span>
            <strong id="result_annual">HKD 0</strong>
          </div>
          <div class="hk-summary-row">
            <span>Ежемесячные транзакции × 12</span>
            <strong id="result_tx_annual">HKD 0</strong>
          </div>
          <div class="hk-summary-row">
            <span>FX-конвертация × 12</span>
            <strong id="result_fx_annual">HKD 0</strong>
          </div>
        </div>

        <div class="hk-summary-divider"></div>

        <div class="hk-summary-block">
          <p class="hk-summary-label">Ежегодно (год 2+)</p>
          <p class="hk-summary-amount hk-summary-amount--sm" id="result_ongoing">HKD 0</p>
        </div>

        <div class="hk-summary-block">
          <p class="hk-summary-label">В среднем в месяц (год 2+)</p>
          <p class="hk-summary-amount hk-summary-amount--sm" id="result_monthly_avg">HKD 0</p>
        </div>

        <div class="hk-summary-divider"></div>

        <div class="hk-summary-convert">
          <p class="hk-summary-label">Курс перевода</p>
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
            <div>Первый год ≈ <strong id="result_usd">$0</strong> USD</div>
            <div>Первый год ≈ <strong id="result_eur">€0</strong> EUR</div>
          </div>
        </div>

        <div class="hk-summary-footer">
          <p>📌 Данные из <a href="https://statrys.com/terms-and-conditions/non-standard-fees" target="_blank" rel="noopener">Statrys Non-Standard Fees</a> и <a href="https://statrys.com/hk/pricing" target="_blank" rel="noopener">Statrys Pricing</a>. Тарифы могут меняться. Актуальные цены — на сайте Statrys.</p>
        </div>

      </div>
    </div>

  </div><!-- /.hk-calc-layout -->

</div><!-- /.hk-calc-page -->

<script src="<?php echo get_template_directory_uri(); ?>/assets/js/hk-calculator.js"></script>

<?php get_footer(); ?>
