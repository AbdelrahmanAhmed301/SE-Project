/* ============================================================
   FREELANCER-PROFILE.JS
   ============================================================ */

'use strict';

document.addEventListener('DOMContentLoaded', () => {

  /* ── Animate progress bars on load ─────────────────── */
  const bars = document.querySelectorAll('.progress-bar-fill');
  const obs = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        const target = e.target.style.width;
        e.target.style.width = '0%';
        setTimeout(() => { e.target.style.width = target; }, 100);
        obs.unobserve(e.target);
      }
    });
  }, { threshold: 0.1 });
  bars.forEach(b => obs.observe(b));

  /* ── Animate reputation ring ─────────────────────── */
  const ring = document.querySelector('.rep-ring circle:last-child');
  if (ring) {
    const circumference = 2 * Math.PI * 42;
    const score = 95;
    const offset = circumference * (1 - score / 100);
    ring.style.strokeDasharray = circumference;
    ring.style.strokeDashoffset = circumference;
    setTimeout(() => {
      ring.style.transition = 'stroke-dashoffset 1s ease';
      ring.style.strokeDashoffset = offset;
    }, 300);
  }

  /* ── Tabs re-initialize (profile-scoped) ─────────── */
  // Already handled by global.js initTabs()

});
