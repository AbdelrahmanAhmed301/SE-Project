/* ============================================================
   JOB-POSTING.JS — Wizard, Niche Fields, Milestone Builder
   ============================================================ */

'use strict';

let currentStep = 1;
let milestoneCount = 3;

/* ── Niche-specific fields ───────────────────────────────── */
const nicheFields = {
  'data-science': `
    <div class="niche-field-group">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Primary Data Stack</label>
          <select class="select">
            <option>Python (PyTorch / TensorFlow)</option>
            <option>R / Stan</option>
            <option>Spark / Databricks</option>
            <option>Julia</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Cloud Platform</label>
          <select class="select">
            <option>AWS SageMaker</option>
            <option>Google Vertex AI</option>
            <option>Azure ML</option>
            <option>On-premise</option>
            <option>Not specified</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Deliverable Format</label>
          <select class="select">
            <option>Jupyter Notebooks</option>
            <option>Python package</option>
            <option>REST API</option>
            <option>Docker container</option>
          </select>
        </div>
      </div>
    </div>`,
  'legal': `
    <div class="niche-field-group">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Legal Jurisdiction</label>
          <select class="select">
            <option>United States (Federal)</option>
            <option>United Kingdom</option>
            <option>European Union</option>
            <option>International / Cross-border</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Practice Area</label>
          <select class="select">
            <option>Contract Law</option>
            <option>Intellectual Property</option>
            <option>M&amp;A / Due Diligence</option>
            <option>Regulatory Compliance</option>
            <option>Employment Law</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Bar Admission Required</label>
          <select class="select">
            <option>Yes — specific jurisdiction</option>
            <option>Yes — any common law</option>
            <option>No — advisory only</option>
          </select>
        </div>
      </div>
    </div>`,
  'translation': `
    <div class="niche-field-group">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Source Language</label>
          <select class="select">
            <option>English</option><option>German</option><option>French</option>
            <option>Spanish</option><option>Japanese</option><option>Chinese (Mandarin)</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Target Language</label>
          <select class="select">
            <option>French</option><option>English</option><option>German</option>
            <option>Spanish</option><option>Japanese</option><option>Korean</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Document Type</label>
          <select class="select">
            <option>Legal / Contract</option>
            <option>Medical / Clinical</option>
            <option>Patent</option>
            <option>Financial / Audit</option>
            <option>Technical Manual</option>
          </select>
        </div>
      </div>
    </div>`,
  'finance': `
    <div class="niche-field-group">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Engagement Type</label>
          <select class="select">
            <option>Financial Modeling</option>
            <option>Valuation / DCF</option>
            <option>Due Diligence</option>
            <option>Audit Support</option>
            <option>CFO Advisory</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Company Stage</label>
          <select class="select">
            <option>Pre-seed / Seed</option>
            <option>Series A–C</option>
            <option>Growth / Pre-IPO</option>
            <option>Public Company</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Reporting Standard</label>
          <select class="select">
            <option>GAAP</option>
            <option>IFRS</option>
            <option>Both</option>
            <option>Not applicable</option>
          </select>
        </div>
      </div>
    </div>`,
  'engineering': `
    <div class="niche-field-group">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">Engineering Discipline</label>
          <select class="select">
            <option>Mechanical</option><option>Aerospace</option><option>Biomedical</option>
            <option>Materials Science</option><option>Chemical</option><option>Civil / Structural</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Engagement Type</label>
          <select class="select">
            <option>R&amp;D / Feasibility</option>
            <option>Design &amp; Prototyping</option>
            <option>Testing &amp; Certification</option>
            <option>Technical Review</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">PE License Required</label>
          <select class="select">
            <option>Yes</option>
            <option>Preferred</option>
            <option selected>Not required</option>
          </select>
        </div>
      </div>
    </div>`,
};

function handleNicheChange(value) {
  const container = document.getElementById('niche-fields');
  container.innerHTML = nicheFields[value] || '';
}

/* ── Wizard Navigation ───────────────────────────────────── */
function goToStep(step) {
  // Validate step 1 before advancing
  if (step > currentStep) {
    if (currentStep === 1) {
      const title = document.getElementById('project-title')?.value.trim();
      const niche = document.getElementById('niche-select')?.value;
      const desc  = document.getElementById('project-desc')?.value.trim();
      if (!title || !niche || !desc) {
        SH.showToast('Please fill in all required fields before continuing.', 'warning');
        return;
      }
    }
  }

  // Update panels
  document.querySelectorAll('.wizard-panel').forEach(p => p.classList.remove('active'));
  document.getElementById(`step-panel-${step}`)?.classList.add('active');

  // Update step indicators
  document.querySelectorAll('.wizard-step').forEach((s, i) => {
    const stepNum = i + 1;
    s.classList.remove('active', 'complete');
    if (stepNum < step) s.classList.add('complete');
    if (stepNum === step) s.classList.add('active');
    // Update number for complete steps
    s.querySelector('.ws-num').textContent = stepNum < step
      ? '✓'
      : stepNum;
  });

  // Update connector lines
  document.querySelectorAll('.wizard-line').forEach((line, i) => {
    line.classList.toggle('complete', i + 1 < step);
  });

  currentStep = step;
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

/* ── Budget Summary ──────────────────────────────────────── */
function updateBudgetSummary() {
  const amounts = [...document.querySelectorAll('.milestone-amount')]
    .map(i => parseFloat(i.value) || 0);
  const total = amounts.reduce((a, b) => a + b, 0);
  const fee   = total * 0.05;
  const grand = total + fee;

  document.querySelector('.budget-total').textContent = '$' + total.toLocaleString();
  document.getElementById('platform-fee').textContent = '$' + fee.toLocaleString();
  document.getElementById('total-payment').textContent = '$' + grand.toLocaleString();
}

/* ── Milestone Builder ───────────────────────────────────── */
function addMilestone() {
  milestoneCount++;
  const container = document.getElementById('milestones-container');

  // Add connector
  const connector = document.createElement('div');
  connector.className = 'milestone-connector';
  connector.innerHTML = `<div class="mc-line"></div><span class="mc-label">Funds locked before Phase ${milestoneCount} begins</span><div class="mc-line"></div>`;
  container.appendChild(connector);

  // Add new milestone card
  const card = document.createElement('div');
  card.className = 'milestone-card card';
  card.dataset.milestone = milestoneCount;
  card.innerHTML = `
    <div class="milestone-card-header">
      <div class="flex items-center gap-3">
        <div class="ms-phase-badge">Phase ${milestoneCount}</div>
        <input type="text" class="input" value="New Phase" style="flex:1;font-weight:600">
      </div>
      <button class="btn btn-icon btn-ghost" onclick="deleteMilestone(this)" title="Remove phase">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
      </button>
    </div>
    <div class="form-row" style="margin-top:var(--space-4)">
      <div class="form-group">
        <label class="form-label">Milestone Amount</label>
        <div class="input-icon-wrap">
          <span class="icon">$</span>
          <input type="number" class="input milestone-amount" value="0" min="0" oninput="updateBudgetSummary()">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Due Date</label>
        <input type="date" class="input">
      </div>
      <div class="form-group">
        <label class="form-label">Inspection Window</label>
        <select class="select">
          <option>3 days</option><option selected>5 days</option><option>7 days</option><option>14 days</option>
        </select>
      </div>
    </div>
    <div class="form-group" style="margin-top:var(--space-3)">
      <label class="form-label">Deliverables for this phase</label>
      <textarea class="textarea" placeholder="What must the specialist deliver to unlock payment?" style="min-height:80px"></textarea>
    </div>
    <div class="form-group" style="margin-top:var(--space-3)">
      <label class="form-label">Free Revision Limit</label>
      <select class="select" style="max-width:200px">
        <option>0 revisions</option><option selected>1 revision</option><option>2 revisions</option><option>3 revisions</option>
      </select>
    </div>`;
  container.appendChild(card);
  SH.showToast(`Phase ${milestoneCount} added`, 'success');
}

function deleteMilestone(btn) {
  const card = btn.closest('.milestone-card');
  const connector = card.previousElementSibling;
  if (connector?.classList.contains('milestone-connector')) connector.remove();
  else card.nextElementSibling?.remove();
  card.remove();
  updateBudgetSummary();
  renumberMilestones();
  SH.showToast('Phase removed', 'info');
}

function renumberMilestones() {
  document.querySelectorAll('.milestone-card').forEach((card, i) => {
    const badge = card.querySelector('.ms-phase-badge');
    if (badge) badge.textContent = `Phase ${i + 1}`;
  });
  milestoneCount = document.querySelectorAll('.milestone-card').length;
}

/* ── Tag Input ───────────────────────────────────────────── */
function addTag(e, input) {
  if (e.key === 'Enter' && input.value.trim()) {
    e.preventDefault();
    const tag = document.createElement('span');
    tag.className = 'tag';
    tag.innerHTML = `${input.value.trim()} <button onclick="removeTag(this)">×</button>`;
    input.before(tag);
    input.value = '';
  }
}

function removeTag(btn) {
  btn.closest('.tag').remove();
}

/* ── Radio Options ───────────────────────────────────────── */
document.querySelectorAll('.radio-option').forEach(opt => {
  opt.addEventListener('click', () => {
    opt.closest('.radio-group').querySelectorAll('.radio-option').forEach(o => o.classList.remove('selected'));
    opt.classList.add('selected');
    opt.querySelector('input[type=radio]').checked = true;
  });
});

/* ── Submit ──────────────────────────────────────────────── */
function submitProject() {
  SH.openModal('modal-success');
}

/* ── Init ────────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  updateBudgetSummary();
});
