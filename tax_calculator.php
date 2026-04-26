<?php
// PHP Tax Calculation Logic (New Regime - India)
$ctc = isset($_POST['ctc']) ? floatval($_POST['ctc']) : 1500000;
$basic_pct = isset($_POST['basic_pct']) ? floatval($_POST['basic_pct']) : 0.4;
$nps = isset($_POST['nps']) ? $_POST['nps'] : 'No';
$meal = isset($_POST['meal']) ? $_POST['meal'] : 'No';

// Calculations
$basic = ($basic_pct == 0.4 || $basic_pct == 0.5) ? $ctc * $basic_pct : 0;
$employer_epf = ($ctc > 0) ? 1950 * 12 : 0;
$gratuity = $basic * 0.0481;
$standard_deduction = ($ctc > 0) ? 75000 : 0;
$nps_amount = ($nps === 'Yes') ? $basic * 0.14 : 0;
$meal_amount = ($ctc > 0 && $meal === 'Yes') ? 4000 * 12 : 0;

$income_from_salary = $ctc - $employer_epf - $gratuity;
$taxable_income = $income_from_salary - $standard_deduction - $nps_amount - $meal_amount;
$taxable_income = max(0, $taxable_income);

// Tax slabs (New Regime FY 2025-26)
$tax_0_4   = 0;
$tax_4_8   = ($taxable_income > 400000)  ? min($taxable_income - 400000,  400000) * 0.05 : 0;
$tax_8_12  = ($taxable_income > 800000)  ? min($taxable_income - 800000,  400000) * 0.10 : 0;
$tax_12_16 = ($taxable_income > 1200000) ? min($taxable_income - 1200000, 400000) * 0.15 : 0;
$tax_16_20 = ($taxable_income > 1600000) ? min($taxable_income - 1600000, 400000) * 0.20 : 0;
$total_tax = ($taxable_income > 1200000) ? $tax_4_8 + $tax_8_12 + $tax_12_16 + $tax_16_20 : 0;

// Marginal relief
$amount_exceed_12l = ($taxable_income > 1200000) ? ($taxable_income - 1200000) : 0;
$tax_after_relief = ($total_tax < $amount_exceed_12l) ? $total_tax : $amount_exceed_12l;

function fmt($n) { return '₹' . number_format($n, 0); }
function fmtM($n) { return '₹' . number_format($n/12, 0); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Income Tax Calculator — New Regime</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --bg: #0a0a0f;
    --surface: #111118;
    --card: #16161f;
    --border: #2a2a3a;
    --accent: #f5a623;
    --accent2: #e8420f;
    --green: #22c55e;
    --text: #e8e8f0;
    --muted: #7070a0;
    --slab1: #1e3a2f;
    --slab2: #1a2e3f;
    --slab3: #2f1e3a;
    --slab4: #3a2e1e;
  }

  body {
    background: var(--bg);
    color: var(--text);
    font-family: 'DM Sans', sans-serif;
    min-height: 100vh;
    background-image:
      radial-gradient(ellipse 60% 40% at 80% 10%, rgba(245,166,35,0.07) 0%, transparent 60%),
      radial-gradient(ellipse 40% 50% at 10% 80%, rgba(232,66,15,0.05) 0%, transparent 60%);
  }

  .shell {
    max-width: 1100px;
    margin: 0 auto;
    padding: 48px 24px 80px;
  }

  /* Header */
  header {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    margin-bottom: 48px;
    gap: 16px;
    flex-wrap: wrap;
  }
  .logo {
    display: flex;
    align-items: center;
    gap: 14px;
  }
  .logo-icon {
    width: 52px; height: 52px;
    background: linear-gradient(135deg, var(--accent), var(--accent2));
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 26px;
    box-shadow: 0 8px 24px rgba(245,166,35,0.3);
  }
  h1 {
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(32px, 5vw, 52px);
    letter-spacing: 1px;
    line-height: 1;
    background: linear-gradient(90deg, var(--accent), #fff 60%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
  }
  .badge {
    font-size: 11px;
    font-weight: 600;
    color: var(--accent);
    border: 1px solid rgba(245,166,35,0.35);
    padding: 4px 10px;
    border-radius: 99px;
    letter-spacing: 1px;
    text-transform: uppercase;
    margin-top: 6px;
    display: inline-block;
  }

  /* Layout */
  .layout {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    align-items: start;
  }
  @media(max-width: 720px) { .layout { grid-template-columns: 1fr; } }

  /* Cards */
  .card {
    background: var(--card);
    border: 1px solid var(--border);
    border-radius: 20px;
    overflow: hidden;
  }
  .card-head {
    padding: 20px 24px 16px;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .card-head h2 {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 20px;
    letter-spacing: 1px;
    color: var(--accent);
  }
  .card-body { padding: 24px; }

  /* Form */
  .field { margin-bottom: 22px; }
  label {
    display: block;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--muted);
    margin-bottom: 8px;
  }
  .input-wrap {
    position: relative;
    display: flex;
    align-items: center;
  }
  .rupee {
    position: absolute;
    left: 14px;
    color: var(--accent);
    font-family: 'DM Mono', monospace;
    font-size: 15px;
    pointer-events: none;
  }
  input[type="number"], input[type="range"] {
    width: 100%;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 10px;
    color: var(--text);
    font-family: 'DM Mono', monospace;
    font-size: 16px;
    padding: 12px 14px 12px 34px;
    outline: none;
    transition: border-color .2s;
    -moz-appearance: textfield;
  }
  input[type="number"]::-webkit-inner-spin-button { -webkit-appearance: none; }
  input[type="number"]:focus { border-color: var(--accent); }
  .hint {
    font-size: 12px;
    color: var(--muted);
    margin-top: 5px;
    font-family: 'DM Mono', monospace;
  }

  /* Toggle */
  .toggle-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 12px 16px;
    margin-bottom: 14px;
  }
  .toggle-label {
    font-size: 14px;
    color: var(--text);
    font-weight: 500;
  }
  .toggle-label span {
    font-size: 11px;
    color: var(--muted);
    display: block;
    font-family: 'DM Mono', monospace;
    margin-top: 2px;
  }
  .toggle {
    position: relative;
    width: 44px; height: 24px;
    flex-shrink: 0;
  }
  .toggle input { opacity: 0; width: 0; height: 0; }
  .slider {
    position: absolute; inset: 0;
    background: var(--border);
    border-radius: 99px;
    cursor: pointer;
    transition: background .2s;
  }
  .slider::before {
    content: '';
    position: absolute;
    width: 18px; height: 18px;
    left: 3px; top: 3px;
    background: #fff;
    border-radius: 50%;
    transition: transform .2s;
  }
  .toggle input:checked + .slider { background: var(--accent); }
  .toggle input:checked + .slider::before { transform: translateX(20px); }

  /* Basic % radio */
  .radio-group { display: flex; gap: 10px; }
  .radio-opt {
    flex: 1;
    cursor: pointer;
  }
  .radio-opt input { display: none; }
  .radio-box {
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 10px;
    font-size: 15px;
    font-family: 'DM Mono', monospace;
    font-weight: 500;
    transition: all .2s;
  }
  .radio-opt input:checked + .radio-box {
    border-color: var(--accent);
    background: rgba(245,166,35,0.12);
    color: var(--accent);
  }

  /* Submit */
  .btn {
    width: 100%;
    padding: 16px;
    background: linear-gradient(135deg, var(--accent), var(--accent2));
    border: none;
    border-radius: 12px;
    color: #0a0a0f;
    font-family: 'Bebas Neue', sans-serif;
    font-size: 20px;
    letter-spacing: 2px;
    cursor: pointer;
    margin-top: 8px;
    box-shadow: 0 6px 24px rgba(245,166,35,0.35);
    transition: transform .15s, box-shadow .15s;
  }
  .btn:hover { transform: translateY(-2px); box-shadow: 0 10px 32px rgba(245,166,35,0.45); }
  .btn:active { transform: translateY(0); }

  /* Results */
  .big-number {
    text-align: center;
    padding: 28px 20px;
    background: linear-gradient(135deg, rgba(245,166,35,0.08), rgba(232,66,15,0.06));
    border-radius: 16px;
    margin-bottom: 24px;
    border: 1px solid rgba(245,166,35,0.2);
  }
  .big-label {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: var(--muted);
    margin-bottom: 8px;
  }
  .big-val {
    font-family: 'Bebas Neue', sans-serif;
    font-size: clamp(40px, 7vw, 64px);
    color: var(--accent);
    line-height: 1;
  }
  .big-sub {
    font-size: 13px;
    color: var(--muted);
    margin-top: 6px;
    font-family: 'DM Mono', monospace;
  }

  /* Income table */
  .data-table { width: 100%; border-collapse: collapse; }
  .data-table tr { border-bottom: 1px solid var(--border); }
  .data-table tr:last-child { border-bottom: none; }
  .data-table td {
    padding: 11px 4px;
    font-size: 13px;
  }
  .data-table td:first-child { color: var(--muted); }
  .data-table td:not(:first-child) {
    text-align: right;
    font-family: 'DM Mono', monospace;
    font-size: 13px;
  }
  .row-highlight td { color: var(--text); font-weight: 600; }
  .row-green td:not(:first-child) { color: var(--green); }
  .row-red td:not(:first-child) { color: #f87171; }
  .col-head {
    font-size: 10px;
    letter-spacing: 1px;
    text-transform: uppercase;
    color: var(--muted);
    padding-bottom: 8px;
  }
  .col-head td { border-bottom: 1px solid var(--border); padding-bottom: 8px; }

  /* Slab bars */
  .slab { margin-bottom: 14px; }
  .slab-head {
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    margin-bottom: 6px;
  }
  .slab-name { color: var(--muted); }
  .slab-amt { font-family: 'DM Mono', monospace; color: var(--text); }
  .bar-bg {
    height: 6px;
    background: var(--border);
    border-radius: 99px;
    overflow: hidden;
  }
  .bar-fill {
    height: 100%;
    border-radius: 99px;
    transition: width .4s ease;
  }

  /* Summary pills */
  .pill-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-top: 20px;
  }
  .pill {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 14px;
    text-align: center;
  }
  .pill-val {
    font-family: 'DM Mono', monospace;
    font-size: 17px;
    font-weight: 500;
    color: var(--text);
  }
  .pill-lbl {
    font-size: 11px;
    color: var(--muted);
    letter-spacing: .5px;
    margin-top: 4px;
  }

  .section-title {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: var(--muted);
    margin: 22px 0 12px;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .section-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border);
  }

  .relief-box {
    background: rgba(34,197,94,0.07);
    border: 1px solid rgba(34,197,94,0.2);
    border-radius: 12px;
    padding: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 16px;
  }
  .relief-label { font-size: 13px; color: var(--muted); }
  .relief-val { font-family: 'DM Mono', monospace; color: var(--green); font-size: 18px; font-weight: 500; }

  footer {
    text-align: center;
    margin-top: 60px;
    font-size: 12px;
    color: var(--muted);
    letter-spacing: .5px;
  }
</style>
</head>
<body>
<div class="shell">

  <header>
    <div class="logo">
      <div class="logo-icon">₹</div>
      <div>
        <h1>Tax Calculator</h1>
        <span class="badge">New Regime · FY 2025-26</span>
      </div>
    </div>
  </header>

  <div class="layout">

    <!-- INPUT PANEL -->
    <div class="card">
      <div class="card-head">
        <span>⚙️</span>
        <h2>Inputs</h2>
      </div>
      <div class="card-body">
        <form method="POST">

          <div class="field">
            <label>CTC (Annual)</label>
            <div class="input-wrap">
              <span class="rupee">₹</span>
              <input type="number" name="ctc" value="<?= htmlspecialchars($ctc) ?>" min="0" step="1">
            </div>
            <div class="hint">Cost to Company per year</div>
          </div>

          <div class="field">
            <label>Basic Salary %</label>
            <div class="radio-group">
              <label class="radio-opt">
                <input type="radio" name="basic_pct" value="0.4" <?= $basic_pct == 0.4 ? 'checked' : '' ?>>
                <div class="radio-box">40%</div>
              </label>
              <label class="radio-opt">
                <input type="radio" name="basic_pct" value="0.5" <?= $basic_pct == 0.5 ? 'checked' : '' ?>>
                <div class="radio-box">50%</div>
              </label>
            </div>
            <div class="hint">% of CTC towards Basic Salary</div>
          </div>

          <div class="field">
            <label>Optional Benefits</label>
            <div class="toggle-row">
              <div class="toggle-label">
                NPS Contribution
                <span>14% of Basic (Employer)</span>
              </div>
              <label class="toggle">
                <input type="checkbox" name="nps" value="Yes" <?= $nps === 'Yes' ? 'checked' : '' ?>>
                <span class="slider"></span>
              </label>
            </div>
            <div class="toggle-row">
              <div class="toggle-label">
                Meal Allowance
                <span>₹4,000/month tax-free</span>
              </div>
              <label class="toggle">
                <input type="checkbox" name="meal" value="Yes" <?= $meal === 'Yes' ? 'checked' : '' ?>>
                <span class="slider"></span>
              </label>
            </div>
          </div>

          <button type="submit" class="btn">Calculate Tax →</button>
        </form>
      </div>
    </div>

    <!-- RESULTS PANEL -->
    <div>

      <!-- Final Tax -->
      <div class="card" style="margin-bottom:24px;">
        <div class="card-head">
          <span>🧮</span>
          <h2>Your Tax Liability</h2>
        </div>
        <div class="card-body">
          <div class="big-number">
            <div class="big-label">Tax After Marginal Relief</div>
            <div class="big-val"><?= fmt($tax_after_relief) ?></div>
            <div class="big-sub"><?= fmtM($tax_after_relief) ?> / month &nbsp;·&nbsp; <?= number_format($ctc > 0 ? ($tax_after_relief/$ctc)*100 : 0, 1) ?>% effective rate</div>
          </div>

          <!-- Income breakdown -->
          <div class="section-title">Income Breakdown</div>
          <table class="data-table">
            <tr class="col-head">
              <td>Component</td>
              <td>Yearly</td>
              <td>Monthly</td>
            </tr>
            <tr>
              <td>CTC</td>
              <td><?= fmt($ctc) ?></td>
              <td><?= fmtM($ctc) ?></td>
            </tr>
            <tr>
              <td>Basic (<?= ($basic_pct*100) ?>%)</td>
              <td><?= fmt($basic) ?></td>
              <td><?= fmtM($basic) ?></td>
            </tr>
            <tr class="row-red">
              <td>Employer EPF</td>
              <td>–<?= fmt($employer_epf) ?></td>
              <td>–<?= fmtM($employer_epf) ?></td>
            </tr>
            <tr class="row-red">
              <td>Gratuity (4.81%)</td>
              <td>–<?= fmt($gratuity) ?></td>
              <td>–<?= fmtM($gratuity) ?></td>
            </tr>
            <tr class="row-highlight">
              <td>Income from Salary</td>
              <td><?= fmt($income_from_salary) ?></td>
              <td><?= fmtM($income_from_salary) ?></td>
            </tr>
            <tr class="row-red">
              <td>Standard Deduction</td>
              <td>–<?= fmt($standard_deduction) ?></td>
              <td>—</td>
            </tr>
            <?php if ($nps_amount > 0): ?>
            <tr class="row-red">
              <td>NPS (14%)</td>
              <td>–<?= fmt($nps_amount) ?></td>
              <td>–<?= fmtM($nps_amount) ?></td>
            </tr>
            <?php endif; ?>
            <?php if ($meal_amount > 0): ?>
            <tr class="row-red">
              <td>Meal Allowance</td>
              <td>–<?= fmt($meal_amount) ?></td>
              <td>–<?= fmtM($meal_amount) ?></td>
            </tr>
            <?php endif; ?>
            <tr class="row-green">
              <td>Taxable Income</td>
              <td><?= fmt($taxable_income) ?></td>
              <td><?= fmtM($taxable_income) ?></td>
            </tr>
          </table>

        </div>
      </div>

      <!-- Tax Slabs -->
      <div class="card">
        <div class="card-head">
          <span>📊</span>
          <h2>Tax Slab Breakdown</h2>
        </div>
        <div class="card-body">
          <?php
          $max_tax = max($total_tax, 1);
          $slabs = [
            ['0L – 4L',   '0%',  $tax_0_4,   '#4ade80', '#1e3a2f'],
            ['4L – 8L',   '5%',  $tax_4_8,   '#60a5fa', '#1a2e3f'],
            ['8L – 12L',  '10%', $tax_8_12,  '#c084fc', '#2f1e3a'],
            ['12L – 16L', '15%', $tax_12_16, '#fb923c', '#3a2e1e'],
            ['16L – 20L', '20%', $tax_16_20, '#f87171', '#3a1e1e'],
          ];
          foreach ($slabs as $s):
            $pct = $max_tax > 0 ? min(100, ($s[2]/$max_tax)*100) : 0;
          ?>
          <div class="slab">
            <div class="slab-head">
              <span class="slab-name"><?= $s[0] ?> <span style="color:<?= $s[3] ?>">(<?= $s[1] ?>)</span></span>
              <span class="slab-amt"><?= fmt($s[2]) ?></span>
            </div>
            <div class="bar-bg">
              <div class="bar-fill" style="width:<?= $pct ?>%;background:<?= $s[3] ?>"></div>
            </div>
          </div>
          <?php endforeach; ?>

          <div style="display:flex;justify-content:space-between;padding:14px 0 8px;border-top:1px solid var(--border);margin-top:8px">
            <span style="font-size:13px;font-weight:600">Total Tax (Before Relief)</span>
            <span style="font-family:'DM Mono',monospace;font-size:15px;color:var(--accent)"><?= fmt($total_tax) ?></span>
          </div>

          <?php if ($taxable_income > 1200000): ?>
          <div class="relief-box">
            <div>
              <div style="font-size:13px;color:var(--text);font-weight:600">Marginal Relief Applied</div>
              <div class="relief-label">Excess over ₹12L: <?= fmt($amount_exceed_12l) ?></div>
            </div>
            <div class="relief-val"><?= fmt($tax_after_relief) ?></div>
          </div>
          <?php else: ?>
          <div class="relief-box" style="background:rgba(34,197,94,0.05)">
            <div style="font-size:13px;color:var(--green)">✓ Income ≤ ₹12L — Zero Tax Liability</div>
          </div>
          <?php endif; ?>

          <?php $cess = $tax_after_relief * 0.04; $total_with_cess = $tax_after_relief + $cess; ?>
          <div style="background:rgba(96,165,250,0.07);border:1px solid rgba(96,165,250,0.25);border-radius:12px;padding:14px 16px;margin-top:14px;display:flex;justify-content:space-between;align-items:center;gap:12px;">
            <div style="display:flex;align-items:flex-start;gap:10px;">
              <span style="font-size:16px;margin-top:1px;">&#8505;&#65039;</span>
              <div>
                <div style="font-size:13px;font-weight:600;color:#93c5fd;">4% Health &amp; Education Cess</div>
                <div style="font-size:12px;color:var(--muted);margin-top:3px;font-family:'DM Mono',monospace;">Applicable on tax amount &middot; <?= fmt($tax_after_relief) ?> &times; 4%</div>
              </div>
            </div>
            <div style="text-align:right;flex-shrink:0;">
              <div style="font-family:'DM Mono',monospace;font-size:15px;color:#93c5fd;">+<?= fmt($cess) ?></div>
              <div style="font-size:11px;color:var(--muted);margin-top:2px;">Total: <?= fmt($total_with_cess) ?></div>
            </div>
          </div>

          <div class="pill-row">
            <div class="pill">
              <div class="pill-val"><?= number_format($ctc > 0 ? ($tax_after_relief/$ctc)*100 : 0, 2) ?>%</div>
              <div class="pill-lbl">Effective Tax Rate</div>
            </div>
            <div class="pill">
              <div class="pill-val"><?= fmt($ctc - $tax_after_relief) ?></div>
              <div class="pill-lbl">Take-Home (Yearly)</div>
            </div>
            <div class="pill">
              <div class="pill-val"><?= fmtM($ctc - $tax_after_relief) ?></div>
              <div class="pill-lbl">Take-Home (Monthly)</div>
            </div>
            <div class="pill">
              <div class="pill-val"><?= fmtM($tax_after_relief) ?></div>
              <div class="pill-lbl">Tax per Month</div>
            </div>
          </div>

        </div>
      </div>

    </div><!-- /results -->
  </div><!-- /layout -->

  <footer>
    Built for New Tax Regime · FY 2025-26 · Based on Budget 2025 slabs<br>
    For reference only. Consult a CA for precise filing.
  </footer>

</div>
</body>
</html>
