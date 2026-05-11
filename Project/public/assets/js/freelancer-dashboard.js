
function go(sec, el) {
document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
document.getElementById('sec-' + sec).classList.add('active');
document.querySelectorAll('.nav-link').forEach(n => n.classList.remove('active'));
if (el) el.classList.add('active');
const titles = { dashboard:'Dashboard', jobs:'Find Jobs', proposals:'My Proposals', projects:'Active Projects', earnings:'Earnings', invoices:'Invoices', messages:'Messages', profile:'My Profile', settings:'Settings' };
document.getElementById('topbar-title').textContent = titles[sec] || sec;
}

function open_modal(id) { document.getElementById(id).classList.add('open'); }
function close_modal(id) { document.getElementById(id).classList.remove('open'); }

document.querySelectorAll('.overlay').forEach(o => {
o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
});

function toast(msg) {
const el = document.getElementById('toast-el');
document.getElementById('toast-txt').textContent = msg;
el.classList.add('show');
setTimeout(() => el.classList.remove('show'), 2800);
}

function add_skill(e) {
if (e.key !== 'Enter') return;
const inp = e.target;
const val = inp.value.trim();
if (!val) return;
const chip = document.createElement('span');
chip.className = 'skill-chip';
chip.innerHTML = val + ' <span onclick="rm(this)">×</span>';
inp.parentNode.insertBefore(chip, inp);
inp.value = '';
}

function rm(el) { el.parentNode.remove(); }

function send_msg(e) {
if (e.key !== 'Enter') return;
const inp = document.getElementById('msg-input');
const val = inp.value.trim();
if (!val) return;
const area = document.getElementById('chat-messages');
const div = document.createElement('div');
div.style.cssText = 'display:flex;justify-content:flex-end';
div.innerHTML = `<div style="background:var(--accent);color:#fff;border-radius:10px 10px 3px 10px;padding:9px 13px;max-width:70%;font-size:13px">${val}<div style="font-size:10px;color:rgba(255,255,255,0.6);margin-top:3px">Just now</div></div>`;
area.appendChild(div);
area.scrollTop = area.scrollHeight;
inp.value = '';
}

function go(sec, el) {
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.getElementById('sec-' + sec).classList.add('active');
    document.querySelectorAll('.nav-link').forEach(n => n.classList.remove('active'));
    if (el) el.classList.add('active');
    const titles = { dashboard:'Dashboard', jobs:'Find Jobs', proposals:'My Proposals', projects:'Active Projects', earnings:'Earnings', invoices:'Invoices', messages:'Messages', profile:'My Profile', settings:'Settings' };
    document.getElementById('topbar-title').textContent = titles[sec] || sec;
  }

  function open_modal(id) { document.getElementById(id).classList.add('open'); }
  function close_modal(id) { document.getElementById(id).classList.remove('open'); }

  document.querySelectorAll('.overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
  });

  function toast(msg) {
    const el = document.getElementById('toast-el');
    document.getElementById('toast-txt').textContent = msg;
    el.classList.add('show');
    setTimeout(() => el.classList.remove('show'), 2800);
  }

  function add_skill(e) {
    if (e.key !== 'Enter') return;
    const inp = e.target;
    const val = inp.value.trim();
    if (!val) return;
    const chip = document.createElement('span');
    chip.className = 'skill-chip';
    chip.innerHTML = val + ' <span onclick="rm(this)">×</span>';
    inp.parentNode.insertBefore(chip, inp);
    inp.value = '';
  }

  function rm(el) { el.parentNode.remove(); }

  function send_msg(e) {
    if (e.key !== 'Enter') return;
    const inp = document.getElementById('msg-input');
    const val = inp.value.trim();
    if (!val) return;
    const area = document.getElementById('chat-messages');
    const div = document.createElement('div');
    div.style.cssText = 'display:flex;justify-content:flex-end';
    div.innerHTML = `<div style="background:var(--accent);color:#fff;border-radius:10px 10px 3px 10px;padding:9px 13px;max-width:70%;font-size:13px">${val}<div style="font-size:10px;color:rgba(255,255,255,0.6);margin-top:3px">Just now</div></div>`;
    area.appendChild(div);
    area.scrollTop = area.scrollHeight;
    inp.value = '';
  }
  function markNotificationsAsRead() {
    const dot = document.getElementById('notif-dot');
    if (dot) {
        dot.style.display = 'none';
    }
    fetch('../../Controllers/mark_read.php', {
        method: 'POST'
    })
    .then(response => response.text())
    .then(data => console.log("Notifications updated"));
}