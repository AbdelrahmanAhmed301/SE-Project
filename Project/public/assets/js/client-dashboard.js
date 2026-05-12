  // Navigation
function navigate(section, navEl) {
document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
document.getElementById('section-' + section).classList.add('active');

document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
if (navEl) navEl.classList.add('active');

const titles = {
    dashboard: 'Dashboard',
    projects: 'My Projects',
    'post-project': 'Post a Project',
    freelancers: 'Browse Talent',
    messages: 'Messages',
    contracts: 'Contracts',
    invoices: 'Invoices',
    wallet: 'Wallet',
    settings: 'Settings'
};
document.getElementById('page-title').textContent = titles[section] || section;
}

// Modals
function openModal(id) {
document.getElementById(id).classList.add('open');
}

function closeModal(id) {
document.getElementById(id).classList.remove('open');
}

document.querySelectorAll('.modal-overlay').forEach(overlay => {
overlay.addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('open');
});
});

// Steps
let currentStep = 1;
function nextStep(step) {
document.getElementById('post-step-' + currentStep).style.display = 'none';
document.getElementById('step' + currentStep).classList.remove('active');
document.getElementById('step' + currentStep).classList.add('done');

if (step < currentStep) {
    document.getElementById('step' + currentStep).classList.remove('done');
}

currentStep = step;
document.getElementById('post-step-' + step).style.display = 'block';
document.getElementById('step' + step).classList.add('active');
document.getElementById('step' + step).classList.remove('done');

// Update review title
const title = document.getElementById('proj-title');
if (title && title.value) {
    const rt = document.getElementById('review-title');
    if (rt) rt.textContent = title.value;
}
}

function publishProject() {
closeModal('create-project-modal');
showToast('🚀 Project published! Freelancers will start applying soon.', 'success');
navigate('projects', document.querySelectorAll('.nav-item')[1]);
// Reset steps
currentStep = 1;
['post-step-1','post-step-2','post-step-3','post-step-4'].forEach((id, i) => {
    const el = document.getElementById(id);
    if (el) el.style.display = i === 0 ? 'block' : 'none';
});
['step1','step2','step3','step4'].forEach((id, i) => {
    const el = document.getElementById(id);
    if (el) { el.classList.remove('active','done'); if (i===0) el.classList.add('active'); }
});
}

// Skills
function addSkill(e) {
if (e.key === 'Enter') {
    const input = document.getElementById('skill-input');
    const val = input.value.trim();
    if (!val) return;
    const tag = document.createElement('div');
    tag.className = 'skill-tag';
    tag.innerHTML = val + ' <span onclick="removeTag(this)">×</span>';
    input.parentNode.insertBefore(tag, input);
    input.value = '';
}
}

function removeTag(el) {
el.parentNode.remove();
}

// Milestones
let msCount = 1;
function addMilestone() {
msCount++;
const list = document.getElementById('milestone-list');
const item = document.createElement('div');
item.className = 'milestone-item';
item.innerHTML = `<div class="milestone-num">${msCount}</div><input type="text" class="form-input" placeholder="Milestone description..." style="flex:1;margin:0"><input type="number" class="form-input" placeholder="$" style="width:90px;margin:0">`;
list.appendChild(item);
}

// Toast
function showToast(msg, type = 'success') {
const toast = document.getElementById('toast');
document.getElementById('toast-msg').textContent = msg;
document.getElementById('toast-icon').textContent = type === 'success' ? '✓' : 'ℹ';
toast.className = 'toast ' + type;
setTimeout(() => toast.classList.add('show'), 10);
setTimeout(() => toast.classList.remove('show'), 3500);
}

// Message send
function sendMessage(e) {
if (e.key === 'Enter') {
    const input = document.getElementById('chat-input');
    const val = input.value.trim();
    if (!val) return;
    const msgArea = input.parentNode.previousElementSibling;
    const msg = document.createElement('div');
    msg.style.cssText = 'display:flex;justify-content:flex-end';
    msg.innerHTML = `<div style="background:rgba(232,201,122,0.15);border-radius:12px 12px 4px 12px;padding:10px 14px;max-width:70%;font-size:13px;color:var(--text)">${val}<div style="font-size:11px;color:rgba(232,201,122,0.5);margin-top:4px">Just now</div></div>`;
    msgArea.appendChild(msg);
    msgArea.scrollTop = msgArea.scrollHeight;
    input.value = '';
}
}

function selectConversation(el) {
el.classList.remove('unread');
el.querySelector('.unread-dot') && el.querySelector('.unread-dot').remove();
}

function open_modal(id){
    document.getElementById(id).classList.add("active");
}

function close_modal(id){
    document.getElementById(id).classList.remove("active");
}

function showSection(sectionName, element){

    document.querySelectorAll('.section').forEach(section => {
        section.style.display = 'none';
    });

    document.getElementById('section-' + sectionName).style.display = 'block';

    document.querySelectorAll('.nav-item').forEach(item => {
        item.classList.remove('active');
    });

    element.classList.add('active');
}


function open_modal(id){
    document.getElementById(id).classList.add('active');
}

function close_modal(id){
    document.getElementById(id).classList.remove('active');
}


