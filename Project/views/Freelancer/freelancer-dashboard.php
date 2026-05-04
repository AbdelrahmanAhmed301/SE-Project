<?php
session_start();
require_once "../../Controllers/DBcontrollers.php";
require_once "../../Controllers/post_project.php";
$db = new DBcontrollers();
$db->openconnection();

$projects = $db->Select_query("
    SELECT * FROM projects 
    WHERE status = 'Pending' 
    OR status IS NULL 
");

$current_user_id = $_SESSION["userid"];
$user_info = $db->Select_query("SELECT * FROM user WHERE user_id = '$current_user_id'");
$current_user = $user_info[0] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>WorkNest — Freelancer Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../../public/assets/css/freelancer.css">
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
  <div class="logo">Worklio<span></span></div>

  <nav>
    <div class="nav-group">
      <div class="nav-group-label">Main</div>
      <div class="nav-link active" onclick="go('dashboard',this)"><span class="ico">⊞</span> Dashboard</div>
      <div class="nav-link" onclick="go('jobs',this)"><span class="ico">🔍</span> Find Jobs <span class="pill">12</span></div>
      <div class="nav-link" onclick="go('proposals',this)"><span class="ico">📤</span> My Proposals <span class="pill amber">3</span></div>
      <div class="nav-link" onclick="go('projects',this)"><span class="ico">📁</span> Active Projects</div>
    </div>
    <div class="nav-group">
      <div class="nav-group-label">Finance</div>
      <div class="nav-link" onclick="go('earnings',this)"><span class="ico">💰</span> Earnings</div>
      <div class="nav-link" onclick="go('invoices',this)"><span class="ico">🧾</span> Invoices</div>
    </div>
    <div class="nav-group">
      <div class="nav-group-label">Account</div>
      <div class="nav-link" onclick="go('messages',this)"><span class="ico">💬</span> Messages <span class="pill green">2</span></div>
      <div class="nav-link" onclick="go('profile',this)"><span class="ico">👤</span> My Profile</div>
      <div class="nav-link" onclick="go('settings',this)"><span class="ico">⚙️</span> Settings</div>
    </div>
  </nav>

  <div class="sidebar-user">
    <div class="ava"><?php echo strtoupper(substr($current_user['username'] ?? 'U', 0, 1)); ?></div>
    <div>
      <div class="user-name"><?php echo $current_user['username']  ?></div>
      <div class="user-role">Full-Stack Dev</div>
    </div>
  </div>
</aside>

<!-- Main -->
<main class="main">
  <header class="topbar">
    <div class="topbar-title" id="topbar-title">Dashboard</div>
    <div class="search">🔍 Search...</div>
    <div class="icon-btn">🔔<div class="dot"></div></div>
    <button class="btn btn-primary btn-sm" onclick="open_modal('proposal-modal')">+ Send Proposal</button>
  </header>

  <div class="content">

    <!-- ═══ DASHBOARD ═══ -->
    <section class="section active" id="sec-dashboard">
      <div class="grid-4">
        <div class="stat">
          <div class="stat-label">MONTHLY EARNINGS</div>
          <div class="stat-val">$3,240</div>
          <div class="stat-note up">↑ 18% vs last month</div>
        </div>
        <div class="stat">
          <div class="stat-label">ACTIVE PROJECTS</div>
          <div class="stat-val">4</div>
          <div class="stat-note">2 due this week</div>
        </div>
        <div class="stat">
          <div class="stat-label">PROPOSALS SENT</div>
          <div class="stat-val">7</div>
          <div class="stat-note">3 awaiting reply</div>
        </div>
        <div class="stat">
          <div class="stat-label">PROFILE VIEWS</div>
          <div class="stat-val">142</div>
          <div class="stat-note up">↑ 32 this week</div>
        </div>
      </div>

<div class="card">
  <div class="card-head">
    <div class="card-title">Active Projects</div>
    <div class="card-link" onclick="go('projects', null)">View all</div>
  </div>

<?php if (!empty($projects)): ?>
  <?php foreach ($projects as $project): ?>

    <div class="list-item" style="align-items:center;">

      <div class="list-icon" style="background:#eef1fc">
        🖥
      </div>

      <div class="list-main" style="min-width:0;">

        <div class="list-title" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
          <?php echo htmlspecialchars($project['title']); ?>
        </div>

        <!-- ✅ Description Added -->
        <div class="list-desc" style="font-size:12px;color:#888;margin-top:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
          <?php echo htmlspecialchars($project['description']); ?>
        </div>

        <div class="list-sub" style="color:#777;">
          Due: <?php echo $project['due_date'] ?? 'No date'; ?>
        </div>

        <div class="bar" style="margin-top:7px;width:160px;">
          <div class="bar-fill" style="width:60%"></div>
        </div>

      </div>

      <div style="text-align:right; min-width:90px;">

        <div style="font-weight:700;font-size:13px;color:var(--accent);">
          $<?php echo number_format($project['budget']); ?>
        </div>
        <div class="badge badge-green" style="margin-top:4px; display:inline-block;">
        <button class="btn btn-primary btn-sm" onclick="open_modal('proposal-modal')">Apply Now</button>
          
        </div>

        <div class="badge badge-green" style="margin-top:4px; display:inline-block;">
          <?php echo $project['status'] ?? 'Active'; ?>
          
        </div>
      
  

      </div>

    </div>

  <?php endforeach; ?>

<?php else: ?>
  <p style="padding: 20px;">No active projects</p>
<?php endif; ?>

</div>

        <!-- Earnings Chart -->
        <div class="card">
          <div class="card-head">
            <div class="card-title">Earnings — 2026</div>
          </div>
          <div style="padding:16px 16px 8px">
            <div class="chart">
              <div class="bar-col"><div class="b" style="height:45px"></div><div class="bl"></div></div>
              <div class="bar-col"><div class="b" style="height:62px"></div><div class="bl"></div></div>
              <div class="bar-col"><div class="b" style="height:38px"></div><div class="bl"></div></div>
              <div class="bar-col"><div class="b" style="height:80px;background:var(--green)"></div><div class="bl"></div></div>
            </div>
            <div style="margin-top:12px;border-top:1px solid var(--border);padding-top:12px">
              <div style="font-size:11px;color:var(--muted)"></div>
              <div style="font-size:22px;font-weight:700;letter-spacing:-0.5px;color:var(--green)"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activity + New Jobs -->
      <div class="grid-2">
        <div class="card">
          <div class="card-head">
            <div class="card-title">Recent Activity</div>
          </div>
          <div style="padding:4px 0">
            <div class="list-item">
              <div style="width:8px;height:8px;border-radius:50%;background:var(--green);flex-shrink:0;margin-top:2px"></div>
              <div>
                <div style="font-size:13px"><b></b> </div>
                <div style="font-size:11px;color:var(--muted)"></div>
              </div>
            </div>
            <div class="list-item">
              <div style="width:8px;height:8px;border-radius:50%;background:var(--accent);flex-shrink:0;margin-top:2px"></div>
              <div>
                <div style="font-size:13px"> <b></b> </div>
                <div style="font-size:11px;color:var(--muted)"></div>
              </div>
            </div>
            <div class="list-item">
              <div style="width:8px;height:8px;border-radius:50%;background:var(--amber);flex-shrink:0;margin-top:2px"></div>
              <div>
                <div style="font-size:13px"> <b></b></div>
                <div style="font-size:11px;color:var(--muted)"></div>
              </div>
            </div>
            <div class="list-item">
              <div style="width:8px;height:8px;border-radius:50%;background:var(--muted2);flex-shrink:0;margin-top:2px"></div>
              <div>
                <div style="font-size:13px"> <b>React Dashboard</b></div>
                <div style="font-size:11px;color:var(--muted)"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-head">
            <div class="card-title"></div>
            <div class="card-link" onclick="go('jobs', null)"></div>
          </div>
          <div class="list-item">
            <div class="list-main">
              <div class="list-title"></div>
              <div class="list-sub"></div>
            </div>
            <button class="btn btn-ghost btn-sm" onclick="open_modal('proposal-modal')">Apply</button>
          </div>
          <div class="list-item">
            <div class="list-main">
              <div class="list-title"></div>
              <div class="list-sub"></div>
            </div>
            <button class="btn btn-ghost btn-sm" onclick="open_modal('proposal-modal')">Apply</button>
          </div>
          <div class="list-item">
            <div class="list-main">
              <div class="list-title"></div>
              <div class="list-sub"></div>
            </div>
            <button class="btn btn-ghost btn-sm" onclick="open_modal('proposal-modal')">Apply</button>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ FIND JOBS ═══ -->
    <section class="section" id="sec-jobs">
      <div class="sec-head">
        <div class="sec-title">Find Jobs</div>
        <div style="display:flex;gap:10px">
          <input class="input" style="width:200px" placeholder="Search jobs...">
          <select class="select" style="width:auto">
            <option></option>
            <option></option>
            <option></option>
            <option></option>
          </select>
          <select class="select" style="width:auto">
            <option></option>
            <option></option>
            <option></option>
            <option></option>
          </select>
        </div>
      </div>
      <div style="display:flex;flex-direction:column;gap:12px">

        <div class="card" style="padding:18px 20px">
          <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px">
            <div style="flex:1">
              <div style="font-size:15px;font-weight:700;margin-bottom:4px">Next.js SaaS Dashboard</div>
              <div style="font-size:12px;color:var(--muted);margin-bottom:10px">Fixed Price · $2,000–$4,000 · 1–2 months · Posted 1 hour ago</div>
              <div style="font-size:13px;color:#444;line-height:1.6;margin-bottom:12px">Looking for a senior Next.js developer to build a modern SaaS analytics dashboard with real-time data, charts, and role-based access control.</div>
              <div style="display:flex;flex-wrap:wrap;gap:6px">
                <span class="skill-chip">Next.js</span><span class="skill-chip">TypeScript</span><span class="skill-chip">Tailwind</span><span class="skill-chip">Prisma</span>
              </div>
            </div>
            <div style="text-align:right;flex-shrink:0">
              <div style="font-size:18px;font-weight:700;color:var(--accent)">$4,000</div>
              <div style="font-size:11px;color:var(--muted);margin-bottom:10px">Fixed</div>
              <button class="btn btn-primary btn-sm" onclick="open_modal('proposal-modal')">Apply Now</button>
            </div>
          </div>
          <div style="display:flex;align-items:center;gap:16px;margin-top:12px;padding-top:12px;border-top:1px solid var(--border)">
            <div style="font-size:12px;color:var(--muted)">⭐ Client 4.9 · 22 hires · <span style="color:var(--green)">●</span> Payment Verified</div>
            <div style="font-size:12px;color:var(--muted)">14 proposals</div>
          </div>
        </div>

        <div class="card" style="padding:18px 20px">
          <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px">
            <div style="flex:1">
              <div style="font-size:15px;font-weight:700;margin-bottom:4px">React Native Fitness App</div>
              <div style="font-size:12px;color:var(--muted);margin-bottom:10px">Fixed Price · $5,000–$10,000 · 2–4 months · Posted 3 hours ago</div>
              <div style="font-size:13px;color:#444;line-height:1.6;margin-bottom:12px">Need an experienced React Native developer to build a cross-platform fitness app with workout tracking, nutrition logs, and social features.</div>
              <div style="display:flex;flex-wrap:wrap;gap:6px">
                <span class="skill-chip">React Native</span><span class="skill-chip">Firebase</span><span class="skill-chip">Redux</span>
              </div>
            </div>
            <div style="text-align:right;flex-shrink:0">
              <div style="font-size:18px;font-weight:700;color:var(--accent)">$10,000</div>
              <div style="font-size:11px;color:var(--muted);margin-bottom:10px">Fixed</div>
              <button class="btn btn-primary btn-sm" onclick="open_modal('proposal-modal')">Apply Now</button>
            </div>
          </div>
          <div style="display:flex;align-items:center;gap:16px;margin-top:12px;padding-top:12px;border-top:1px solid var(--border)">
            <div style="font-size:12px;color:var(--muted)">⭐ Client 4.7 · 8 hires · <span style="color:var(--green)">●</span> Payment Verified</div>
            <div style="font-size:12px;color:var(--muted)">6 proposals</div>
          </div>
        </div>

        <div class="card" style="padding:18px 20px">
          <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px">
            <div style="flex:1">
              <div style="font-size:15px;font-weight:700;margin-bottom:4px">Node.js API — Stripe Integration</div>
              <div style="font-size:12px;color:var(--muted);margin-bottom:10px">Hourly · $60–$90/hr · Less than 1 month · Posted 6 hours ago</div>
              <div style="font-size:13px;color:#444;line-height:1.6;margin-bottom:12px">We need a backend developer to integrate Stripe subscriptions, webhooks, and billing portal into our existing Node.js/Express API.</div>
              <div style="display:flex;flex-wrap:wrap;gap:6px">
                <span class="skill-chip">Node.js</span><span class="skill-chip">Stripe</span><span class="skill-chip">PostgreSQL</span>
              </div>
            </div>
            <div style="text-align:right;flex-shrink:0">
              <div style="font-size:18px;font-weight:700;color:var(--accent)">$90/hr</div>
              <div style="font-size:11px;color:var(--muted);margin-bottom:10px">Hourly</div>
              <button class="btn btn-primary btn-sm" onclick="open_modal('proposal-modal')">Apply Now</button>
            </div>
          </div>
          <div style="display:flex;align-items:center;gap:16px;margin-top:12px;padding-top:12px;border-top:1px solid var(--border)">
            <div style="font-size:12px;color:var(--muted)">⭐ Client 5.0 · 35 hires · <span style="color:var(--green)">●</span> Payment Verified</div>
            <div style="font-size:12px;color:var(--muted)">3 proposals</div>
          </div>
        </div>

      </div>
    </section>

    <!-- ═══ PROPOSALS ═══ -->
    <section class="section" id="sec-proposals">
      <div class="sec-head">
        <div class="sec-title">My Proposals</div>
        <button class="btn btn-primary" onclick="open_modal('proposal-modal')">+ New Proposal</button>
      </div>
      <div class="card">
        <table class="tbl">
          <thead>
            <tr>
              <th>Job Title</th>
              <th>Bid</th>
              <th>Submitted</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><div style="font-weight:600">Next.js SaaS Dashboard</div><div style="font-size:11px;color:var(--muted)">Fixed · $2k–$4k</div></td>
              <td style="color:var(--accent);font-weight:600">$3,500</td>
              <td style="color:var(--muted)">Jan 14, 2026</td>
              <td><span class="badge badge-amber">Pending</span></td>
              <td><button class="btn btn-ghost btn-sm" onclick="open_modal('proposal-modal')">Edit</button></td>
            </tr>
            <tr>
              <td><div style="font-weight:600">React Native Fitness App</div><div style="font-size:11px;color:var(--muted)">Fixed · $5k–$10k</div></td>
              <td style="color:var(--accent);font-weight:600">$8,000</td>
              <td style="color:var(--muted)">Jan 12, 2026</td>
              <td><span class="badge badge-amber">Pending</span></td>
              <td><button class="btn btn-ghost btn-sm">Withdraw</button></td>
            </tr>
            <tr>
              <td><div style="font-weight:600">WordPress Theme Custom</div><div style="font-size:11px;color:var(--muted)">Fixed · $500–$1k</div></td>
              <td style="color:var(--accent);font-weight:600">$900</td>
              <td style="color:var(--muted)">Jan 10, 2026</td>
              <td><span class="badge badge-green">Hired</span></td>
              <td><button class="btn btn-ghost btn-sm">View</button></td>
            </tr>
            <tr>
              <td><div style="font-weight:600">Landing Page Design</div><div style="font-size:11px;color:var(--muted)">Fixed · $300–$600</div></td>
              <td style="color:var(--accent);font-weight:600">$550</td>
              <td style="color:var(--muted)">Jan 5, 2026</td>
              <td><span class="badge badge-gray">Declined</span></td>
              <td><button class="btn btn-ghost btn-sm">View</button></td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- ═══ ACTIVE PROJECTS ═══ -->
    <section class="section" id="sec-projects">
      <div class="sec-head">
        <div class="sec-title">Active Projects</div>
      </div>
      <div class="card">
        <table class="tbl">
          <thead>
            <tr><th>Project</th><th>Client</th><th>Budget</th><th>Progress</th><th>Deadline</th><th>Status</th><th>Action</th></tr>
          </thead>
          <tbody>
            <tr>
              <td><div style="font-weight:600">E-commerce Redesign</div><div style="font-size:11px;color:var(--muted)">UI/UX + Dev</div></td>
              <td>Ahmed K.</td>
              <td style="color:var(--accent);font-weight:600">$4,500</td>
              <td>
                <div style="font-size:11px;color:var(--muted);margin-bottom:4px">68%</div>
                <div class="bar" style="width:110px"><div class="bar-fill" style="width:68%"></div></div>
              </td>
              <td style="color:var(--muted)">Jan 30</td>
              <td><span class="badge badge-green">Active</span></td>
              <td>
                <div style="display:flex;gap:6px">
                  <button class="btn btn-ghost btn-sm" onclick="open_modal('project-modal')">View</button>
                  <button class="btn btn-ghost btn-sm">Message</button>
                </div>
              </td>
            </tr>
            <tr>
              <td><div style="font-weight:600">Mobile Banking App</div><div style="font-size:11px;color:var(--muted)">React Native</div></td>
              <td>Sara M.</td>
              <td style="color:var(--accent);font-weight:600">$8,000</td>
              <td>
                <div style="font-size:11px;color:var(--muted);margin-bottom:4px">40%</div>
                <div class="bar" style="width:110px"><div class="bar-fill" style="width:40%"></div></div>
              </td>
              <td style="color:var(--muted)">Feb 10</td>
              <td><span class="badge badge-green">Active</span></td>
              <td>
                <div style="display:flex;gap:6px">
                  <button class="btn btn-ghost btn-sm" onclick="open_modal('project-modal')">View</button>
                  <button class="btn btn-ghost btn-sm">Message</button>
                </div>
              </td>
            </tr>
            <tr>
              <td><div style="font-weight:600">REST API Integration</div><div style="font-size:11px;color:var(--muted)">Node.js</div></td>
              <td>Layla T.</td>
              <td style="color:var(--accent);font-weight:600">$1,200</td>
              <td>
                <div style="font-size:11px;color:var(--muted);margin-bottom:4px">85%</div>
                <div class="bar" style="width:110px"><div class="bar-fill" style="width:85%;background:var(--amber)"></div></div>
              </td>
              <td style="color:var(--muted)">Jan 22</td>
              <td><span class="badge badge-amber">In Review</span></td>
              <td>
                <div style="display:flex;gap:6px">
                  <button class="btn btn-ghost btn-sm" onclick="open_modal('project-modal')">View</button>
                  <button class="btn btn-primary btn-sm" onclick="toast('Delivery submitted!')">Deliver</button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- ═══ EARNINGS ═══ -->
    <section class="section" id="sec-earnings">
      <div class="sec-head"><div class="sec-title">Earnings</div></div>
      <div class="grid-4" style="margin-bottom:20px">
        <div class="stat"><div class="stat-label">THIS MONTH</div><div class="stat-val">$3,240</div><div class="stat-note up">↑ 18%</div></div>
        <div class="stat"><div class="stat-label">LAST MONTH</div><div class="stat-val">$2,740</div></div>
        <div class="stat"><div class="stat-label">TOTAL 2026</div><div class="stat-val">$18,460</div><div class="stat-note up">On track</div></div>
        <div class="stat"><div class="stat-label">PENDING</div><div class="stat-val">$5,200</div><div class="stat-note">In escrow</div></div>
      </div>
      <div class="card">
        <div class="card-head"><div class="card-title">Transaction History</div></div>
        <table class="tbl">
          <thead><tr><th>Description</th><th>Client</th><th>Date</th><th>Amount</th><th>Status</th></tr></thead>
          <tbody>
            <tr>
              <td><div style="font-weight:600">Milestone #2 — E-commerce</div></td>
              <td>Ahmed K.</td>
              <td style="color:var(--muted)">Jan 14</td>
              <td style="color:var(--green);font-weight:600">+$2,000</td>
              <td><span class="badge badge-green">Paid</span></td>
            </tr>
            <tr>
              <td><div style="font-weight:600">Full Project — WP Theme</div></td>
              <td>Nour S.</td>
              <td style="color:var(--muted)">Jan 10</td>
              <td style="color:var(--green);font-weight:600">+$900</td>
              <td><span class="badge badge-green">Paid</span></td>
            </tr>
            <tr>
              <td><div style="font-weight:600">Milestone #1 — Banking App</div></td>
              <td>Sara M.</td>
              <td style="color:var(--muted)">Jan 5</td>
              <td style="color:var(--green);font-weight:600">+$1,600</td>
              <td><span class="badge badge-green">Paid</span></td>
            </tr>
            <tr>
              <td><div style="font-weight:600">API Integration — Escrow</div></td>
              <td>Layla T.</td>
              <td style="color:var(--muted)">Pending</td>
              <td style="color:var(--muted);font-weight:600">$1,200</td>
              <td><span class="badge badge-amber">In Escrow</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- ═══ INVOICES ═══ -->
    <section class="section" id="sec-invoices">
      <div class="sec-head">
        <div class="sec-title">Invoices</div>
        <button class="btn btn-primary" onclick="open_modal('invoice-modal')">+ Create Invoice</button>
      </div>
      <div class="card">
        <table class="tbl">
          <thead><tr><th>Invoice</th><th>Client</th><th>Project</th><th>Amount</th><th>Due Date</th><th>Status</th><th>Action</th></tr></thead>
          <tbody>
            <tr>
              <td><strong>#INV-032</strong></td>
              <td>Ahmed K.</td>
              <td>E-commerce Redesign</td>
              <td style="color:var(--accent);font-weight:600">$2,000</td>
              <td style="color:var(--muted)">Jan 20</td>
              <td><span class="badge badge-amber">Unpaid</span></td>
              <td><div style="display:flex;gap:6px"><button class="btn btn-ghost btn-sm">View</button><button class="btn btn-ghost btn-sm">Remind</button></div></td>
            </tr>
            <tr>
              <td><strong>#INV-031</strong></td>
              <td>Sara M.</td>
              <td>Banking App M1</td>
              <td style="color:var(--accent);font-weight:600">$1,600</td>
              <td style="color:var(--muted)">Jan 15</td>
              <td><span class="badge badge-green">Paid</span></td>
              <td><button class="btn btn-ghost btn-sm">Download</button></td>
            </tr>
            <tr>
              <td><strong>#INV-030</strong></td>
              <td>Nour S.</td>
              <td>WP Theme</td>
              <td style="color:var(--accent);font-weight:600">$900</td>
              <td style="color:var(--muted)">Jan 10</td>
              <td><span class="badge badge-green">Paid</span></td>
              <td><button class="btn btn-ghost btn-sm">Download</button></td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- ═══ MESSAGES ═══ -->
    <section class="section" id="sec-messages">
      <div class="sec-head"><div class="sec-title">Messages</div></div>
      <div class="card" style="display:grid;grid-template-columns:260px 1fr;min-height:460px">
        <div style="border-right:1px solid var(--border)">
          <div style="padding:12px"><input class="input" placeholder="Search..." style="font-size:12px"></div>
          <div class="list-item" style="background:#f0f4ff" onclick="this.style.background='#f0f4ff'">
            <div class="ava" style="width:34px;height:34px;font-size:12px">AK</div>
            <div class="list-main">
              <div class="list-title">Ahmed K.</div>
              <div class="list-sub">Hi! Milestone #2 approved ✓</div>
            </div>
            <div style="font-size:10px;color:var(--muted)">12:40</div>
          </div>
          <div class="list-item" onclick="">
            <div class="ava green" style="width:34px;height:34px;font-size:12px">SM</div>
            <div class="list-main">
              <div class="list-title">Sara M.</div>
              <div class="list-sub">Can you push by Friday?</div>
            </div>
            <div style="font-size:10px;color:var(--muted)">10:05</div>
          </div>
          <div class="list-item" onclick="">
            <div class="ava amber" style="width:34px;height:34px;font-size:12px">LT</div>
            <div class="list-main">
              <div class="list-title">Layla T.</div>
              <div class="list-sub">Please review the API docs</div>
            </div>
            <div style="font-size:10px;color:var(--muted)">Yest.</div>
          </div>
        </div>
        <div style="display:flex;flex-direction:column">
          <div style="padding:14px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:10px">
            <div class="ava" style="width:32px;height:32px;font-size:11px">AK</div>
            <div><div style="font-size:13px;font-weight:600">Ahmed K.</div><div style="font-size:11px;color:var(--green)">● Online</div></div>
          </div>
          <div style="flex:1;padding:16px 20px;display:flex;flex-direction:column;gap:12px;overflow-y:auto" id="chat-messages">
            <div style="display:flex;justify-content:flex-start">
              <div style="background:var(--bg);border-radius:10px 10px 10px 3px;padding:9px 13px;max-width:70%;font-size:13px">
                Milestone #2 looks great — approved! 🎉
                <div style="font-size:10px;color:var(--muted);margin-top:3px">12:38 PM</div>
              </div>
            </div>
            <div style="display:flex;justify-content:flex-end">
              <div style="background:var(--accent);color:#fff;border-radius:10px 10px 3px 10px;padding:9px 13px;max-width:70%;font-size:13px">
                Thank you! Starting milestone #3 today.
                <div style="font-size:10px;color:rgba(255,255,255,0.6);margin-top:3px">12:41 PM</div>
              </div>
            </div>
          </div>
          <div style="padding:12px 16px;border-top:1px solid var(--border);display:flex;gap:8px">
            <input class="input" placeholder="Type a message..." id="msg-input" onkeydown="send_msg(event)" style="flex:1">
            <button class="btn btn-primary btn-sm" onclick="send_msg({key:'Enter'})">Send</button>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ PROFILE ═══ -->
    <section class="section" id="sec-profile">
      <div class="sec-head"><div class="sec-title">My Profile</div></div>
      <div class="grid-2">
        <div style="display:flex;flex-direction:column;gap:16px">
          <div class="card" style="padding:22px">
            <div style="display:flex;align-items:center;gap:14px;margin-bottom:18px">
              <div class="ava" style="width:56px;height:56px;font-size:20px;border-radius:12px">YA</div>
              <div>
                <div style="font-size:17px;font-weight:700"><?php echo  $current_user['username']  ?></div>
                <div style="font-size:13px;color:var(--muted)">Full-Stack Developer · Cairo, Egypt</div>
                <div style="font-size:12px;color:var(--accent);margin-top:2px">⭐ 4.9 (58 reviews)</div>
              </div>
              <button class="btn btn-ghost btn-sm" style="margin-left:auto">Edit Photo</button>
            </div>
            <div class="form-row">
              <div class="form-group"><label class="label">FIRST NAME</label><input class="input" value="Youssef"></div>
              <div class="form-group"><label class="label">LAST NAME</label><input class="input" value="Ahmed"></div>
            </div>
            <div class="form-group"><label class="label">TITLE</label><input class="input" value="Full-Stack Developer (React · Node · Mobile)"></div>
            <div class="form-group"><label class="label">BIO</label><textarea class="textarea">5+ years building web and mobile apps. I specialize in React, Next.js, Node.js, and React Native. I love clean code, fast delivery, and clear communication.</textarea></div>
            <div class="form-row">
              <div class="form-group"><label class="label">HOURLY RATE ($)</label><input class="input" type="number" value="85"></div>
              <div class="form-group"><label class="label">LOCATION</label><input class="input" value="Cairo, Egypt"></div>
            </div>
          </div>

          <div class="card" style="padding:20px">
            <div style="font-size:13px;font-weight:600;margin-bottom:12px">Skills</div>
            <div class="chips-wrap" id="skills-wrap">
              <span class="skill-chip">React <span onclick="rm(this)">×</span></span>
              <span class="skill-chip">Next.js <span onclick="rm(this)">×</span></span>
              <span class="skill-chip">Node.js <span onclick="rm(this)">×</span></span>
              <span class="skill-chip">React Native <span onclick="rm(this)">×</span></span>
              <span class="skill-chip">TypeScript <span onclick="rm(this)">×</span></span>
              <input type="text" class="chip-input" placeholder="Add skill + Enter" onkeydown="add_skill(event)">
            </div>
          </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:16px">
          <div class="card" style="padding:20px">
            <div style="font-size:13px;font-weight:600;margin-bottom:12px">Availability</div>
            <div class="toggle-row">
              <div><div class="toggle-label">Available for work</div><div class="toggle-sub">Show as available on your profile</div></div>
              <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
            </div>
            <div class="toggle-row">
              <div><div class="toggle-label">Open to fixed-price</div><div class="toggle-sub">Accept fixed budget projects</div></div>
              <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
            </div>
            <div class="toggle-row">
              <div><div class="toggle-label">Open to hourly</div><div class="toggle-sub">Accept hourly rate contracts</div></div>
              <label class="switch"><input type="checkbox"><span class="slider"></span></label>
            </div>
          </div>

          <div class="card" style="padding:20px">
            <div style="font-size:13px;font-weight:600;margin-bottom:14px">Stats at a glance</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
              <div style="background:var(--bg);border-radius:8px;padding:14px;text-align:center">
                <div style="font-size:22px;font-weight:700;color:var(--accent)">58</div>
                <div style="font-size:11px;color:var(--muted)">Total Jobs</div>
              </div>
              <div style="background:var(--bg);border-radius:8px;padding:14px;text-align:center">
                <div style="font-size:22px;font-weight:700;color:var(--green)">97%</div>
                <div style="font-size:11px;color:var(--muted)">Satisfaction</div>
              </div>
              <div style="background:var(--bg);border-radius:8px;padding:14px;text-align:center">
                <div style="font-size:22px;font-weight:700">$85k</div>
                <div style="font-size:11px;color:var(--muted)">Total Earned</div>
              </div>
              <div style="background:var(--bg);border-radius:8px;padding:14px;text-align:center">
                <div style="font-size:22px;font-weight:700;color:var(--amber)">5 yrs</div>
                <div style="font-size:11px;color:var(--muted)">Experience</div>
              </div>
            </div>
          </div>

          <div style="display:flex;justify-content:flex-end;gap:8px">
            <button class="btn btn-ghost">Cancel</button>
            <button class="btn btn-primary" onclick="toast('Profile saved!')">Save Profile</button>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ SETTINGS ═══ -->
    <section class="section" id="sec-settings">
      <div class="sec-head"><div class="sec-title">Settings</div></div>
      <div style="max-width:560px;display:flex;flex-direction:column;gap:16px">
        <div class="card" style="padding:20px">
          <div style="font-size:13px;font-weight:600;margin-bottom:14px">Account</div>
          <div class="form-group"><label class="label">EMAIL</label><input class="input" value="youssef@dev.io"></div>
          <div class="form-row">
            <div class="form-group"><label class="label">NEW PASSWORD</label><input class="input" type="password" placeholder="••••••••"></div>
            <div class="form-group"><label class="label">CONFIRM PASSWORD</label><input class="input" type="password" placeholder="••••••••"></div>
          </div>
          <button class="btn btn-primary btn-sm" onclick="toast('Account updated!')">Update Account</button>
        </div>

        <div class="card" style="padding:20px">
          <div style="font-size:13px;font-weight:600;margin-bottom:14px">Notifications</div>
          <div class="toggle-row">
            <div><div class="toggle-label">New messages</div></div>
            <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
          </div>
          <div class="toggle-row">
            <div><div class="toggle-label">Proposal responses</div></div>
            <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
          </div>
          <div class="toggle-row">
            <div><div class="toggle-label">Payment received</div></div>
            <label class="switch"><input type="checkbox" checked><span class="slider"></span></label>
          </div>
          <div class="toggle-row">
            <div><div class="toggle-label">Marketing emails</div></div>
            <label class="switch"><input type="checkbox"><span class="slider"></span></label>
          </div>
        </div>

        <div class="card" style="padding:20px">
          <div style="font-size:13px;font-weight:600;margin-bottom:14px">Payout Method</div>
          <div style="display:flex;gap:10px;align-items:center;background:var(--bg);border-radius:8px;padding:12px;margin-bottom:10px">
            <div style="font-size:20px">💳</div>
            <div style="flex:1"><div style="font-size:13px;font-weight:600">Visa •••• 4242</div><div style="font-size:11px;color:var(--muted)">Default</div></div>
            <button class="btn btn-ghost btn-sm">Change</button>
          </div>
          <button class="btn btn-ghost btn-sm">+ Add Payout Method</button>
        </div>
      </div>
    </section>

  </div>
</main>

<!-- ═══ MODALS ═══ -->

<!-- Proposal Modal -->
<div class="overlay" id="proposal-modal">
  <div class="modal">
    <div class="modal-head">
      <div class="modal-title">Send a Proposal</div>
      <button class="modal-close" onclick="close_modal('proposal-modal')">×</button>
    </div>
    <div class="modal-body">
      <div class="form-group"><label class="label">JOB</label><input class="input" value=""></div>
      <div class="form-row">
        <div class="form-group"><label class="label">YOUR BID ($)</label><input class="input" type="number" placeholder="e.g. 3500"></div>
        <div class="form-group"><label class="label">DELIVERY TIME</label>
          <select class="select">
            <option>1 week</option><option>2 weeks</option><option selected>1 month</option><option>2 months</option>
          </select>
        </div>
      </div>
      <div class="form-group"><label class="label">COVER LETTER</label><textarea class="textarea" style="min-height:120px" placeholder="Introduce yourself and explain why you're a great fit..."></textarea></div>
    </div>
    <div class="modal-foot">
      <button class="btn btn-ghost" onclick="close_modal('proposal-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="close_modal('proposal-modal');toast('Proposal submitted!')">Submit Proposal</button>
    </div>
  </div>
</div>

<!-- Project Detail Modal -->
<div class="overlay" id="project-modal">
  <div class="modal" style="width:540px">
    <div class="modal-head">
      <div class="modal-title">E-commerce Redesign</div>
      <button class="modal-close" onclick="close_modal('project-modal')">×</button>
    </div>
    <div class="modal-body">
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:18px">
        <div style="background:var(--bg);border-radius:8px;padding:12px;text-align:center"><div style="font-size:10px;color:var(--muted)">BUDGET</div><div style="font-size:18px;font-weight:700;color:var(--accent)">$4,500</div></div>
        <div style="background:var(--bg);border-radius:8px;padding:12px;text-align:center"><div style="font-size:10px;color:var(--muted)">PROGRESS</div><div style="font-size:18px;font-weight:700;color:var(--accent)">68%</div></div>
        <div style="background:var(--bg);border-radius:8px;padding:12px;text-align:center"><div style="font-size:10px;color:var(--muted)">DEADLINE</div><div style="font-size:18px;font-weight:700">Jan 30</div></div>
      </div>
      <div style="font-size:13px;color:#555;line-height:1.7;margin-bottom:16px">Full redesign of an e-commerce platform. Includes product pages, cart, checkout, and admin dashboard. Mobile-first, Figma handoff required.</div>
      <div style="font-size:12px;font-weight:600;color:var(--muted);margin-bottom:8px">MILESTONES</div>
      <div style="display:flex;flex-direction:column;gap:8px">
        <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:var(--green-light);border-radius:8px">
          <div style="width:22px;height:22px;border-radius:50%;background:var(--green);color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center">✓</div>
          <div style="flex:1;font-size:13px">Discovery & Wireframes</div>
          <div style="font-size:12px;color:var(--green);font-weight:600">$1,000 Paid</div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:var(--green-light);border-radius:8px">
          <div style="width:22px;height:22px;border-radius:50%;background:var(--green);color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center">✓</div>
          <div style="flex:1;font-size:13px">UI Design System</div>
          <div style="font-size:12px;color:var(--green);font-weight:600">$1,500 Paid</div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:var(--amber-light);border-radius:8px">
          <div style="width:22px;height:22px;border-radius:50%;background:var(--amber);color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center">3</div>
          <div style="flex:1;font-size:13px">Full Page Designs</div>
          <div style="font-size:12px;color:var(--amber);font-weight:600">$1,500 In Progress</div>
        </div>
        <div style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:var(--bg);border-radius:8px">
          <div style="width:22px;height:22px;border-radius:50%;background:var(--muted2);color:#fff;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center">4</div>
          <div style="flex:1;font-size:13px;color:var(--muted)">Figma Handoff</div>
          <div style="font-size:12px;color:var(--muted);font-weight:600">$500 Pending</div>
        </div>
      </div>
    </div>
    <div class="modal-foot">
      <button class="btn btn-ghost" onclick="close_modal('project-modal')">Close</button>
      <button class="btn btn-primary" onclick="close_modal('project-modal');toast('Milestone delivered!')">Submit Milestone</button>
    </div>
  </div>
</div>

<!-- Invoice Modal -->
<div class="overlay" id="invoice-modal">
  <div class="modal">
    <div class="modal-head">
      <div class="modal-title">Create Invoice</div>
      <button class="modal-close" onclick="close_modal('invoice-modal')">×</button>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div class="form-group"><label class="label">CLIENT</label>
          <select class="select"><option>Ahmed K.</option><option>Sara M.</option><option>Layla T.</option></select>
        </div>
        <div class="form-group"><label class="label">PROJECT</label>
          <select class="select"><option>E-commerce Redesign</option><option>Banking App</option></select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="label">AMOUNT ($)</label><input class="input" type="number" placeholder="e.g. 2000"></div>
        <div class="form-group"><label class="label">DUE DATE</label><input class="input" type="date"></div>
      </div>
      <div class="form-group"><label class="label">DESCRIPTION</label><textarea class="textarea" placeholder="e.g. Milestone #3 — Full Page Designs"></textarea></div>
    </div>
    <div class="modal-foot">
      <button class="btn btn-ghost" onclick="close_modal('invoice-modal')">Cancel</button>
      <button class="btn btn-primary" onclick="close_modal('invoice-modal');toast('Invoice created & sent!')">Create & Send</button>
    </div>
  </div>
</div>

<!-- Toast -->
<div id="toast-el" class="toast">✓ <span id="toast-txt"></span></div>

<script>
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
</script>
</body>
</html>

