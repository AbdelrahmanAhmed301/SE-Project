/* ============================================================
   ADMIN-DASHBOARD.JS
   ============================================================ */

'use strict';

/* ── Generate sparkline bar charts ──────────────────────── */
function buildSparkline(containerId, data, color) {
  const container = document.getElementById(containerId);
  if (!container) return;
  const max = Math.max(...data);
  data.forEach((val, i) => {
    const bar = document.createElement('div');
    bar.className = 'metric-bar';
    const heightPct = Math.max(10, Math.round((val / max) * 100));
    bar.style.height = '0%';
    bar.style.background = color || 'var(--brand-primary)';
    container.appendChild(bar);
    setTimeout(() => { bar.style.height = heightPct + '%'; }, 100 + i * 40);
  });
}

/* ── KYC Approve ─────────────────────────────────────────── */
function approveKYC(btn, name) {
  const row = btn.closest('tr');
  row.querySelector('td:nth-child(5)').innerHTML = '<span class="badge badge-green">✓ Verified</span>';
  btn.parentElement.innerHTML = '<span class="text-sm text-success font-semibold">Approved</span>';
  SH.showToast(`KYC approved for ${name}. Welcome email sent.`, 'success');
}

/* ── Live metric pulse ───────────────────────────────────── */
function startLiveMetrics() {
  setInterval(() => {
    const contractEl = document.getElementById('metric-contracts');
    if (contractEl) {
      const current = parseInt(contractEl.textContent.replace(/\D/g, ''));
      const delta   = Math.floor(Math.random() * 3) - 1;
      contractEl.textContent = (current + delta).toLocaleString();
    }

    const el = document.getElementById('last-updated');
    if (el) el.textContent = 'just now';
  }, 8000);
}

/* ── Init ────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {

  // Sparklines
  buildSparkline('chart-contracts', [620, 680, 710, 695, 740, 780, 810, 847], '#0F62FE');
  buildSparkline('chart-escrow',    [5.2, 6.1, 6.8, 7.0, 7.4, 7.9, 8.1, 8.4], '#6929C4');
  buildSparkline('chart-disputes',  [2.8, 2.5, 2.4, 2.2, 2.1, 2.0, 1.9, 1.8], '#059669');
  buildSparkline('chart-revenue',   [88, 95, 104, 110, 118, 126, 135, 142], '#D97706');

  // Animate metric values
  const metrics = [
    { id: 'metric-contracts', val: 847, prefix: '', suffix: '' },
    { id: 'metric-revenue',   val: 142, prefix: '$', suffix: 'K' },
  ];
  metrics.forEach(m => {
    const el = document.getElementById(m.id);
    if (el) SH.animateCounter(el, 0, m.val, 1000, m.prefix, m.suffix);
  });

  startLiveMetrics();

});
