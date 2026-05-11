/* ============================================================
   LANDING.JS — Home Page Interactions
   ============================================================ */

'use strict';

document.addEventListener('DOMContentLoaded', () => {

  /* ── Animate Hero Stats on Load ─────────────────────── */
  const stats = [
    { id: 'stat-specialists', from: 0, to: 12400, suffix: '+' },
    { id: 'stat-success',     from: 90, to: 98.2, suffix: '%', decimals: 1 },
    { id: 'stat-countries',   from: 0, to: 74, suffix: '' },
  ];

  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      stats.forEach(s => {
        const el = document.getElementById(s.id);
        if (!el) return;
        const step = (s.to - s.from) / (1200 / 16);
        let current = s.from;
        const timer = setInterval(() => {
          current += step;
          if (current >= s.to) { current = s.to; clearInterval(timer); }
          el.textContent = (s.decimals
            ? current.toFixed(s.decimals)
            : Math.round(current).toLocaleString()) + (s.suffix || '');
        }, 16);
      });
      observer.disconnect();
    });
  }, { threshold: 0.3 });

  const heroStats = document.querySelector('.hero-stats');
  if (heroStats) observer.observe(heroStats);

  /* ── Sticky Navbar shadow on scroll ─────────────────── */
  const navbar = document.querySelector('.navbar');
  window.addEventListener('scroll', () => {
    navbar?.classList.toggle('scrolled', window.scrollY > 10);
  }, { passive: true });

  /* ── Smooth scroll for anchor links ─────────────────── */
  document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener('click', e => {
      const target = document.querySelector(link.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  /* ── Animate cards on scroll ─────────────────────────── */
  const fadeEls = document.querySelectorAll('.niche-card, .hiw-step, .feature-item');
  const fadeObs = new IntersectionObserver(entries => {
    entries.forEach((entry, i) => {
      if (entry.isIntersecting) {
        entry.target.style.animationDelay = `${i * 60}ms`;
        entry.target.classList.add('fade-in-up');
        fadeObs.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });
  fadeEls.forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(16px)';
    el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
    fadeObs.observe(el);
  });
});

// CSS class to trigger animation
const style = document.createElement('style');
style.textContent = `.fade-in-up { opacity: 1 !important; transform: translateY(0) !important; } .navbar.scrolled { box-shadow: 0 1px 12px rgba(0,0,0,0.08); }`;
document.head.appendChild(style);
