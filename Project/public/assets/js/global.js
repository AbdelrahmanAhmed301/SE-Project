/* ============================================================
   GLOBAL.JS — SpecialistHub Shared Utilities
   ============================================================ */

'use strict';

/* ── Tabs ────────────────────────────────────────────────── */
function initTabs(containerSelector = '.tabs') {
  document.querySelectorAll(containerSelector).forEach(tabsEl => {
    const tabs = tabsEl.querySelectorAll('.tab[data-tab]');
    tabs.forEach(tab => {
      tab.addEventListener('click', () => {
        const target = tab.dataset.tab;
        // Deactivate siblings
        tabsEl.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        // Switch panels — look in parent or document
        const panel = (tabsEl.closest('[data-tabs-scope]') || document)
          .querySelectorAll('.tab-content');
        panel.forEach(p => {
          p.classList.toggle('active', p.dataset.panel === target);
        });
      });
    });
    // Activate first
    tabs[0]?.click();
  });
}

/* ── Dropdowns ───────────────────────────────────────────── */
function initDropdowns() {
  document.addEventListener('click', e => {
    const trigger = e.target.closest('[data-dropdown-trigger]');
    if (trigger) {
      const dropdown = trigger.closest('.dropdown');
      dropdown?.classList.toggle('open');
      e.stopPropagation();
      return;
    }
    document.querySelectorAll('.dropdown.open').forEach(d => d.classList.remove('open'));
  });
}

/* ── Modals ──────────────────────────────────────────────── */
function openModal(id) {
  const el = document.getElementById(id);
  if (el) { el.classList.add('open'); document.body.style.overflow = 'hidden'; }
}

function closeModal(id) {
  const el = document.getElementById(id);
  if (el) { el.classList.remove('open'); document.body.style.overflow = ''; }
}

function initModals() {
  // Open triggers
  document.querySelectorAll('[data-modal-open]').forEach(btn => {
    btn.addEventListener('click', () => openModal(btn.dataset.modalOpen));
  });
  // Close triggers
  document.querySelectorAll('[data-modal-close]').forEach(btn => {
    btn.addEventListener('click', () => {
      const backdrop = btn.closest('.modal-backdrop');
      if (backdrop) { backdrop.classList.remove('open'); document.body.style.overflow = ''; }
    });
  });
  // Backdrop click
  document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
    backdrop.addEventListener('click', e => {
      if (e.target === backdrop) { backdrop.classList.remove('open'); document.body.style.overflow = ''; }
    });
  });
  // Escape key
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal-backdrop.open').forEach(b => {
        b.classList.remove('open'); document.body.style.overflow = '';
      });
    }
  });
}

/* ── Toast Notifications ─────────────────────────────────── */
function showToast(message, type = 'default', duration = 3500) {
  let container = document.querySelector('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  const icons = {
    success: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>`,
    error:   `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`,
    warning: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/></svg>`,
    info:    `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>`,
    default: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/></svg>`,
  };

  const colors = {
    success: '#059669',
    error:   '#DC2626',
    warning: '#D97706',
    info:    '#0EA5E9',
    default: '#6B7280',
  };

  const toast = document.createElement('div');
  toast.className = 'toast';
  toast.style.borderLeft = `4px solid ${colors[type] || colors.default}`;
  toast.innerHTML = `
    <span style="color:${colors[type] || colors.default}">${icons[type] || icons.default}</span>
    <span style="flex:1">${message}</span>
    <button onclick="this.parentElement.remove()" style="color:#9CA3AF;line-height:1;font-size:1.2rem;margin-left:8px">&times;</button>
  `;
  container.appendChild(toast);
  setTimeout(() => toast.remove(), duration);
}

/* ── Progress Animation ──────────────────────────────────── */
function animateProgress(el, targetPct, delay = 100) {
  setTimeout(() => {
    const fill = el.querySelector('.progress-bar-fill') || el;
    fill.style.width = targetPct + '%';
  }, delay);
}

/* ── Number Counter Animation ────────────────────────────── */
function animateCounter(el, from, to, duration = 1200, prefix = '', suffix = '') {
  const step = (to - from) / (duration / 16);
  let current = from;
  const timer = setInterval(() => {
    current += step;
    if ((step > 0 && current >= to) || (step < 0 && current <= to)) {
      current = to;
      clearInterval(timer);
    }
    el.textContent = prefix + Math.round(current).toLocaleString() + suffix;
  }, 16);
}

/* ── Format Helpers ──────────────────────────────────────── */
const fmt = {
  currency(amount, currency = 'USD') {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency, maximumFractionDigits: 0 }).format(amount);
  },
  number(n) { return new Intl.NumberFormat('en-US').format(n); },
  percent(n, decimals = 1) { return n.toFixed(decimals) + '%'; },
  date(d, opts = {}) {
    return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', ...opts });
  },
  relativeTime(d) {
    const diff = (Date.now() - new Date(d)) / 1000;
    if (diff < 60)   return 'just now';
    if (diff < 3600) return `${Math.floor(diff/60)}m ago`;
    if (diff < 86400)return `${Math.floor(diff/3600)}h ago`;
    return `${Math.floor(diff/86400)}d ago`;
  },
};

/* ── Star Rating Render ──────────────────────────────────── */
function renderStars(rating, max = 5) {
  let html = '';
  for (let i = 1; i <= max; i++) {
    const filled = i <= Math.floor(rating);
    const half   = !filled && i <= rating + 0.5;
    html += `<svg width="14" height="14" viewBox="0 0 24 24" fill="${filled ? '#F59E0B' : (half ? 'url(#half)' : 'none')}" stroke="${filled || half ? '#F59E0B' : '#D1D5DB'}" stroke-width="2">
      <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
    </svg>`;
  }
  return `<span style="display:inline-flex;gap:2px;align-items:center">${html}</span>`;
}

/* ── Lazy Image Load ─────────────────────────────────────── */
function initLazyImages() {
  const obs = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        const img = e.target;
        if (img.dataset.src) { img.src = img.dataset.src; obs.unobserve(img); }
      }
    });
  });
  document.querySelectorAll('img[data-src]').forEach(img => obs.observe(img));
}

/* ── Active Nav Link (from pathname) ─────────────────────── */
function initActiveNav() {
  const path = window.location.pathname.split('/').pop() || 'index.html';
  document.querySelectorAll('.nav-link, .sidebar-link').forEach(link => {
    const href = (link.getAttribute('href') || '').split('/').pop();
    if (href && href === path) link.classList.add('active');
  });
}

/* ── Simple Form Validator ───────────────────────────────── */
function validateForm(formEl) {
  let valid = true;
  formEl.querySelectorAll('[required]').forEach(input => {
    const val = input.value.trim();
    const group = input.closest('.form-group');
    const errorEl = group?.querySelector('.form-error');
    if (!val) {
      valid = false;
      input.style.borderColor = 'var(--danger)';
      if (errorEl) errorEl.style.display = 'block';
    } else {
      input.style.borderColor = '';
      if (errorEl) errorEl.style.display = 'none';
    }
  });
  return valid;
}

/* ── Init All ────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  initTabs();
  initDropdowns();
  initModals();
  initLazyImages();
  initActiveNav();
});

/* ── Exports (for inline use) ────────────────────────────── */
window.SH = { openModal, closeModal, showToast, animateProgress, animateCounter, fmt, renderStars, validateForm };
