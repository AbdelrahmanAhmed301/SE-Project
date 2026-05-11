/* ============================================================
   ESCROW-PAYMENTS.JS
   ============================================================ */

'use strict';

/* ── FX Rates (simulated) ────────────────────────────────── */
const fxRates = {
  USD: { EUR: 0.92, GBP: 0.79, symbol: '$' },
  EUR: { USD: 1.09, GBP: 0.86, symbol: '€' },
  GBP: { USD: 1.27, EUR: 1.16, symbol: '£' },
};

let activeCurrency = 'USD';

function setCurrency(currency, btn) {
  activeCurrency = currency;
  document.querySelectorAll('.currency-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');

  const rateEl = document.getElementById('fx-rate');
  if (currency === 'USD') {
    rateEl.textContent = '1 USD = 0.92 EUR';
  } else if (currency === 'EUR') {
    rateEl.textContent = '1 EUR = 1.09 USD';
  } else {
    rateEl.textContent = '1 GBP = 1.27 USD';
  }

  // Update all balance amounts with conversion animation
  const rates = { USD: 1, EUR: 0.92, GBP: 0.79 };
  const symbols = { USD: '$', EUR: '€', GBP: '£' };
  const baseAmounts = [24800, 3420, 5000, 142600];
  const rate = rates[currency];
  const sym  = symbols[currency];

  document.querySelectorAll('.balance-amount').forEach((el, i) => {
    const converted = Math.round(baseAmounts[i] * rate);
    SH.animateCounter(el, 0, converted, 600, sym, '');
  });

  SH.showToast(`Currency switched to ${currency}`, 'info', 2000);
}

/* ── Partial Release Controls ────────────────────────────── */
function updateReleaseLabel(value) {
  const val = parseInt(value);
  const max = parseInt(document.getElementById('release-range').max);
  document.getElementById('release-label').textContent = '$' + val.toLocaleString();
  document.getElementById('partial-to-specialist').textContent = '$' + val.toLocaleString();
  document.getElementById('partial-remaining').textContent = '$' + (max - val).toLocaleString();
}

function updatePartialMax() {
  const sel = document.getElementById('partial-project');
  const max = parseInt(sel.value);
  const range = document.getElementById('release-range');
  range.max = max;
  range.value = Math.round(max / 2);
  document.getElementById('partial-max-label').textContent = '$' + max.toLocaleString() + ' max';
  updateReleaseLabel(range.value);
}

function confirmPartialRelease() {
  const amount = document.getElementById('release-label').textContent;
  SH.showToast(`Partial release of ${amount} initiated. Funds in transit.`, 'success');
}

/* ── Escrow Release ──────────────────────────────────────── */
function releaseEscrow(amount, specialist) {
  if (confirm(`Release $${amount.toLocaleString()} to ${specialist}? This action cannot be undone.`)) {
    SH.showToast(`$${amount.toLocaleString()} released to ${specialist}`, 'success');
  }
}

/* ── Quick Amount Setter ─────────────────────────────────── */
function setAmount(val) {
  const input = document.querySelector('#modal-add-funds .input[type=number]');
  if (input) input.value = val;
}

/* ── Init ────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  updatePartialMax();

  // Animate balance counters on load
  const amounts = [24800, 3420, 5000, 142600];
  document.querySelectorAll('.balance-amount').forEach((el, i) => {
    SH.animateCounter(el, 0, amounts[i], 900, '$', '');
  });
});
