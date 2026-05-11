/* ============================================================
   DISPUTE-RESOLUTION.JS
   ============================================================ */

'use strict';

function showDemoVerdict() {
  const panel = document.getElementById('verdict-panel');
  if (panel) {
    panel.style.display = 'block';
    panel.scrollIntoView({ behavior: 'smooth', block: 'center' });
    SH.showToast('⚖️ Arbitration verdict issued by Dr. James Rodriguez', 'info', 5000);
  }
}

document.addEventListener('DOMContentLoaded', () => {

  /* ── Auto-scroll saferoom to bottom ─────────────────── */
  const msgs = document.querySelector('.saferoom-messages');
  if (msgs) msgs.scrollTop = msgs.scrollHeight;

  /* ── Countdown timer for hearing ────────────────────── */
  function updateHearingCountdown() {
    const hearingDate = new Date('2025-05-14T14:00:00Z');
    const now = new Date();
    const diff = hearingDate - now;

    if (diff <= 0) return;

    const days    = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours   = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

    const el = document.querySelector('.arbitrator-schedule strong');
    if (el) {
      el.textContent = `May 14, 2025 at 14:00 UTC (in ${days}d ${hours}h ${minutes}m)`;
    }
  }

  updateHearingCountdown();
  setInterval(updateHearingCountdown, 60000);

});
