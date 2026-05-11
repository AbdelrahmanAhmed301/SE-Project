<?php
session_start();

// 1. استدعاء الملفات الضرورية مرة واحدة فقط
require_once __DIR__ . "/../../Controllers/AdminDashboardController.php";
require_once __DIR__ . "/../../Controllers/FreelancerProfileController.php";
require_once __DIR__ . "/../../Models/user.php";

$db = DBcontrollers::getInstance(); 

// 2. تعريف الكنترولرز مرة واحدة
$admin_controller = new AdminDashboardController();
$profile_ctrl = new FreelancerProfileController();

// 3. معالجة قرارات الأدمن (Approve/Reject) أولاً
if (isset($_GET['approve_doc']) && isset($_GET['f_id'])) {
    $doc_id = $_GET['approve_doc'];
    $f_id = $_GET['f_id'];
    
    // تنفيذ عملية التوثيق
    $profile_ctrl->adminReviewDocument($doc_id, $f_id, 'approved', 'Verified by Admin');
    
    // توجيه الصفحة لنفسها لمسح بيانات الـ GET من الرابط وتحديث البيانات
    header("Location: admin-dashboard.php?success=1");
    exit();
}

// 4. جلب البيانات والإحصائيات بعد معالجة أي تغييرات
$total_user = $admin_controller->get_num_users();
$total_project = $admin_controller->get_num_project();
$all_user = $admin_controller->get_all_users();
$all_project = $admin_controller->getAllProjects();

$user_stats = $admin_controller->get_specify_user();
$all_admin      = $user_stats['admin_count'];
$all_client     = $user_stats['client_count'];
$all_freelancer = $user_stats['freelancer_count'];

// جلب المستندات المعلقة لعرضها في الجدول
// جلب الملفات مع التأكد من اسم عمود المستخدم (غالباً username في مشروعك)
$pending_docs = $db->Select_query("
    SELECT d.*, u.username as name 
    FROM freelancer_documents d 
    JOIN user u ON d.freelancer_id = u.user_id 
    WHERE d.status = 'pending'
");
?>



<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard — Freelance Platform</title>
  <link rel="stylesheet" href="../../public/assets/css/admin-dashboard.css"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet"/>
</head>
<body>

<!-- ═══════════════════ SIDEBAR ═══════════════════ -->
<aside class="sidebar">
  <div class="logo">
    <span class="logo-icon">◈</span>
    <span>Gig<strong>Admin</strong></span>
  </div>

  <nav class="nav">
    <span class="nav-label">MAIN</span>
    <a class="nav-item active" href="#">
      <span class="nav-icon">▣</span> Dashboard
    </a>
    <a class="nav-item" href="#">
      <span class="nav-icon">◎</span> Projects
      <span class="badge">41</span>
    </a>
    <a class="nav-item" href="#">
      <span class="nav-icon">◉</span> Users
    </a>
    <a class="nav-item" href="#">
      <span class="nav-icon">◈</span> Payments
      <span class="badge warn">3</span>
    </a>

    <span class="nav-label">MANAGEMENT</span>
    <a class="nav-item" href="#">
      <span class="nav-icon">⬡</span> Disputes
      <span class="badge danger">7</span>
    </a>
    <a class="nav-item" href="#">
      <span class="nav-icon">◇</span> Milestones
    </a>
    <a class="nav-item" href="#">
      <span class="nav-icon">▽</span> Audit Log
    </a>
    <a class="nav-item" href="#">
      <span class="nav-icon">○</span> Roles & Access
    </a>
  </nav>

  <div class="sidebar-footer">
    <div class="admin-pill">
      <div class="admin-dot"></div>
      <span>Super Admin</span>
    </div>
  </div>
</aside>

<!-- ═══════════════════ MAIN ═══════════════════ -->
<main class="main">

  <!-- TOP BAR -->
  <div class="topbar">
    <div>
      <h1 class="page-title">Dashboard <span>Overview</span></h1>
      <p class="page-sub">Saturday, 9 May 2026</p>
    </div>
    <div class="topbar-right">
      <div class="search-box">
        <span>⌕</span>
        <input type="text" placeholder="Search anything..."/>
      </div>
      <button class="btn-icon">🔔</button>
      <button class="btn-primary">+ New Report</button>
    </div>
  </div>

  <!-- ═══ STAT CARDS ═══ -->
  <div class="stats-grid">

    <div class="stat-card" style="--accent-clr:#4f8ef7">
      <div class="stat-top">
        <span class="stat-label">Total Users</span>
        <span class="stat-ico" style="background:rgba(79,142,247,.12);color:#4f8ef7">◉</span>
      </div>
      <div class="stat-value"><?php echo $total_user?></div>
      <div class="stat-bar">
        <div class="stat-fill" style="width:74%;background:#4f8ef7"></div>
      </div>
    </div>

    <div class="stat-card" style="--accent-clr:#22c87a">
      <div class="stat-top">
        <span class="stat-label">Active Projects</span>
        <span class="stat-ico" style="background:rgba(34,200,122,.12);color:#22c87a">▣</span>
      </div>
      <div class="stat-value"><?php echo $total_project ?></div>
      <div class="stat-bar">
        <div class="stat-fill" style="width:58%;background:#22c87a"></div>
      </div>
    </div>

    <div class="stat-card" style="--accent-clr:#f59e0b">
      <div class="stat-top">
        <span class="stat-label">Total Budget</span>
        <span class="stat-ico" style="background:rgba(245,158,11,.12);color:#f59e0b">◈</span>
      </div>
      <div class="stat-value">$842K</div>
      <div class="stat-bar">
        <div class="stat-fill" style="width:88%;background:#f59e0b"></div>
      </div>
    </div>

    <div class="stat-card" style="--accent-clr:#ef4444">
      <div class="stat-top">
        <span class="stat-label">Open Disputes</span>
        <span class="stat-ico" style="background:rgba(239,68,68,.12);color:#ef4444">⬡</span>
      </div>
      <div class="stat-value">7</div>
      <div class="stat-row">
        <span class="chip chip-down">▼ 3</span>
        <span class="stat-hint">need action today</span>
      </div>
      <div class="stat-bar">
        <div class="stat-fill" style="width:28%;background:#ef4444"></div>
      </div>
    </div>

  </div>

  <!-- ═══ SECONDARY ROW ═══ -->
  <div class="row-2">

    <!-- User breakdown -->
    <div class="card">
      <div class="card-head">
        <h2 class="card-title">User Breakdown</h2>
        <span class="card-link">View all →</span>
      </div>

      <div class="user-types">
        <div class="user-type-row">
          <div class="ut-left">
            <div class="ut-dot" style="background:#4f8ef7"></div>
            <span>Clients</span>
          </div>
          <div class="ut-bar-wrap">
            <div class="ut-bar" style="width:68%;background:#4f8ef7"></div>
          </div>
          <strong><?php echo $all_client ?></strong>
        </div>
        <div class="user-type-row">
          <div class="ut-left">
            <div class="ut-dot" style="background:#22c87a"></div>
            <span>Freelancers</span>
          </div>
          <div class="ut-bar-wrap">
            <div class="ut-bar" style="width:52%;background:#22c87a"></div>
          </div>
          <strong><?php echo $all_freelancer?></strong>
        </div>
        <div class="user-type-row">
          <div class="ut-left">
            <div class="ut-dot" style="background:#a8b4c8"></div>
            <span>Admins</span>
          </div>
          <div class="ut-bar-wrap">
            <div class="ut-bar" style="width:6%;background:#a8b4c8"></div>
          </div>
          <strong><?php $all_admin ?></strong>
        </div>
      </div>

      <!-- quick filter -->
      <div class="filter-row">
        <span class="filter-label">Quick filter:</span>
        <button class="filter-btn active">All</button>
        <button class="filter-btn">Clients</button>
        <button class="filter-btn">Freelancers</button>
        <button class="filter-btn">Admins</button>
      </div>

      <!-- mini user table -->
      <table class="mini-table">
        <thead>
          <tr>
            <th>Name</th><th>Role</th><th>Status</th><th>Joined</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><div class="user-cell"><div class="av av-blue">AH</div><span>Ahmed Hassan</span></div></td>
            <td><span class="tag tag-blue">Client</span></td>
            <td><span class="dot-green"></span> Active</td>
            <td class="muted">Jan 12, 2026</td>
          </tr>
          <tr>
            <td><div class="user-cell"><div class="av av-green">SR</div><span>Sara Radwan</span></div></td>
            <td><span class="tag tag-green">Freelancer</span></td>
            <td><span class="dot-green"></span> Active</td>
            <td class="muted">Feb 4, 2026</td>
          </tr>
          <tr>
            <td><div class="user-cell"><div class="av av-amber">KM</div><span>Karim Mostafa</span></div></td>
            <td><span class="tag tag-green">Freelancer</span></td>
            <td><span class="dot-warn"></span> Pending</td>
            <td class="muted">Mar 19, 2026</td>
          </tr>
          <tr>
            <td><div class="user-cell"><div class="av av-red">NN</div><span>Nour Nabil</span></div></td>
            <td><span class="tag tag-blue">Client</span></td>
            <td><span class="dot-red"></span> Suspended</td>
            <td class="muted">Apr 2, 2026</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Right column -->
    <div class="right-col">

      <!-- Projects overview -->
      <div class="card">
        <div class="card-head">
          <h2 class="card-title">Projects Overview</h2>
          <span class="card-link">See all →</span>
        </div>
        <div class="proj-grid">
          <div class="proj-num" style="border-color:#22c87a">
            <span style="color:#22c87a">64</span><small>Active</small>
          </div>
          <div class="proj-num" style="border-color:#f59e0b">
            <span style="color:#f59e0b">38</span><small>In Review</small>
          </div>
          <div class="proj-num" style="border-color:#4f8ef7">
            <span style="color:#4f8ef7">19</span><small>Completed</small>
          </div>
          <div class="proj-num" style="border-color:#ef4444">
            <span style="color:#ef4444">7</span><small>Cancelled</small>
          </div>
        </div>
        <div class="budget-block">
          <div class="budget-row">
            <span>Total Budget</span><strong>$842,300</strong>
          </div>
          <div class="budget-row">
            <span>In Escrow</span><strong class="clr-amber">$314,000</strong>
          </div>
          <div class="budget-row">
            <span>Released</span><strong class="clr-green">$528,300</strong>
          </div>
        </div>
      </div>

      <!-- Alerts -->
      <div class="card">
        <div class="card-head">
          <h2 class="card-title">Needs Action</h2>
        </div>
        <div class="alert-list">
          <div class="alert-item">
            <div class="alert-ico" style="background:rgba(239,68,68,.12)">⬡</div>
            <div class="alert-body">
              <div class="alert-title">Dispute #D-0041 — Fund Split Pending</div>
              <div class="alert-sub">Project: UI Redesign · 2h ago</div>
            </div>
            <button class="act-btn">Resolve</button>
          </div>
          <div class="alert-item">
            <div class="alert-ico" style="background:rgba(245,158,11,.12)">◉</div>
            <div class="alert-body">
              <div class="alert-title">KYC Verification Failed — Freelancer #F-0217</div>
              <div class="alert-sub">Identity Mgmt · 5h ago</div>
            </div>
            <button class="act-btn">Review</button>
          </div>
          <div class="alert-item">
            <div class="alert-ico" style="background:rgba(79,142,247,.12)">◈</div>
            <div class="alert-body">
              <div class="alert-title">Payout #P-0088 Cooling-Off Complete</div>
              <div class="alert-sub">Financial Escrow · 1h ago</div>
            </div>
            <button class="act-btn act-green">Approve</button>
          </div>
          <div class="alert-item">
            <div class="alert-ico" style="background:rgba(239,68,68,.12)">▽</div>
            <div class="alert-body">
              <div class="alert-title">Scope Creep Amendment — needs bilateral sign</div>
              <div class="alert-sub">Project #PRJ-0074 · 3h ago</div>
            </div>
            <button class="act-btn">View</button>
          </div>
        </div>
      </div>

    </div><!-- /right-col -->
  </div>
  <section class="card" style="margin-top: 20px;">
    <div class="card-header">
        <div class="card-title">Pending Document Verifications</div>
    </div>
    <div style="padding: 20px;">
        <?php if (!empty($pending_docs)): ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f4f4f4; text-align: left;">
                        <th style="padding: 10px; border-bottom: 2px solid #ddd;">Freelancer</th>
                        <th style="padding: 10px; border-bottom: 2px solid #ddd;">Type</th>
                        <th style="padding: 10px; border-bottom: 2px solid #ddd;">Document</th>
                        <th style="padding: 10px; border-bottom: 2px solid #ddd;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pending_docs as $doc): ?>
                        <tr>
                            <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($doc['name']); ?></td>
                            <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($doc['document_type']); ?></td>
                            <td style="padding: 10px; border-bottom: 1px solid #eee;">
                                <a href="<?php echo htmlspecialchars($doc['document_path']); ?>" target="_blank" style="color: #4f8ef7; text-decoration: none;">View File</a>
                            </td>
                            <td style="padding: 10px; border-bottom: 1px solid #eee;">
                                <a href="admin-dashboard.php?approve_doc=<?php echo $doc['document_id']; ?>&f_id=<?php echo $doc['freelancer_id']; ?>" 
                                   style="background: #22c87a; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; font-size: 12px;">
                                   Approve
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color: #666; text-align: center;">No pending documents to review.</p>
        <?php endif; ?>
    </div>
</section>


  <div class="row-3">
    <div class="card">
      <div class="card-head">
        <h2 class="card-title">Recent Transactions</h2>
        <span class="card-link">Full ledger →</span>
      </div>
      <table class="mini-table">
        <thead>
          <tr><th>TX ID</th><th>Project</th><th>Amount</th><th>Currency</th><th>Status</th></tr>
        </thead>
        <tbody>
          <tr>
            <td class="mono">#TX-9921</td>
            <td>API Integration</td>
            <td class="clr-green">+$1,200</td>
            <td><span class="tag tag-blue">USD</span></td>
            <td><span class="dot-green"></span> Cleared</td>
          </tr>
          <tr>
            <td class="mono">#TX-9920</td>
            <td>Mobile App Design</td>
            <td class="clr-amber">$800</td>
            <td><span class="tag tag-amber">EUR</span></td>
            <td><span class="dot-warn"></span> Pending</td>
          </tr>
          <tr>
            <td class="mono">#TX-9918</td>
            <td>Brand Identity</td>
            <td class="clr-green">+$3,500</td>
            <td><span class="tag tag-blue">USD</span></td>
            <td><span class="dot-green"></span> Cleared</td>
          </tr>
          <tr>
            <td class="mono">#TX-9915</td>
            <td>SEO Audit</td>
            <td class="clr-red">-$200</td>
            <td><span class="tag tag-green">GBP</span></td>
            <td><span class="dot-red"></span> Refunded</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="card milestone-card">
      <div class="card-head">
        <h2 class="card-title">Milestone Status</h2>
      </div>
      <div class="ms-list">
        <div class="ms-row">
          <div class="ms-info">
            <span class="ms-name">Mobile App — Phase 2</span>
            <span class="ms-date muted">Due May 14</span>
          </div>
          <div class="ms-prog-wrap">
            <div class="ms-prog" style="width:72%;background:#4f8ef7"></div>
          </div>
          <span class="ms-pct">72%</span>
          <span class="tag tag-blue">In Progress</span>
        </div>
        <div class="ms-row">
          <div class="ms-info">
            <span class="ms-name">Brand Identity — Delivery</span>
            <span class="ms-date muted">Due May 11</span>
          </div>
          <div class="ms-prog-wrap">
            <div class="ms-prog" style="width:100%;background:#22c87a"></div>
          </div>
          <span class="ms-pct">100%</span>
          <span class="tag tag-green">Completed</span>
        </div>
        <div class="ms-row">
          <div class="ms-info">
            <span class="ms-name">API Integration — Phase 1</span>
            <span class="ms-date clr-red">Overdue</span>
          </div>
          <div class="ms-prog-wrap">
            <div class="ms-prog" style="width:40%;background:#ef4444"></div>
          </div>
          <span class="ms-pct">40%</span>
          <span class="tag tag-red">Delayed</span>
        </div>
        <div class="ms-row">
          <div class="ms-info">
            <span class="ms-name">SEO Audit — Report</span>
            <span class="ms-date muted">Due May 20</span>
          </div>
          <div class="ms-prog-wrap">
            <div class="ms-prog" style="width:25%;background:#f59e0b"></div>
          </div>
          <span class="ms-pct">25%</span>
          <span class="tag tag-amber">Review</span>
        </div>
      </div>
    </div>

  </div><!-- /row-3 -->

</main><!-- /main -->

</body>
</html>
