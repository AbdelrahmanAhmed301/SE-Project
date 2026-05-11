<?php
require_once "../../Models/contract.php";
session_start();


if (!isset($_SESSION["userid"])) {
    header("Location: ../../views/Auth/login.php");
    exit();
}


if ($_SESSION["user_roleid"] != 3) {

    header("Location: ../../views/Auth/login.php");
    exit();
}
require_once "../../Controllers/DBcontrollers.php";
require_once "../../Controllers/post_project.php";
require_once "../../Controllers/notifycontrollers.php";
require_once "../../Models/user.php";
require_once "../../Models/proposal.php";
$db = DBcontrollers::getInstance(); 

$projects = $db->Select_query("
    SELECT * FROM projects 
    WHERE status = 'Pending' 
    OR status IS NULL 
");



$current_user_id = $_SESSION["userid"];
$notifications = $db->Select_query("SELECT * FROM notification WHERE user_id = '$current_user_id'");
$contracts = $db->Select_query("SELECT * FROM contract WHERE freelancer_id = '$current_user_id'");
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
<link rel="stylesheet" href="../../public/assets/css/home.css">
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
     <div class="nav-group">
    <div class="nav-group-label">Contracts</div>

    <?php foreach ($contracts as $row): ?>

        <a 
            href="../contract_details/contract_details.php?contract_id=<?php echo $row['contract_id']; ?>" 
            class="nav-link"
        >
            <span class="ico">📄</span>
            Contract #<?php echo $row['contract_id']; ?>
        </a>

    <?php endforeach; ?>

</div>
    <div class="nav-group">
      <div class="nav-group-label">Account</div>
      <div class="nav-link" onclick="go('messages',this)"><span class="ico">💬</span> Messages <span class="pill green">2</span></div>
      <div class="nav-link" onclick="go('profile',this)"><span class="ico">👤</span> My Profile</div>
      <div class="nav-link" onclick="go('settings',this)"><span class="ico">⚙️</span> Settings</div>
    </div>
  </nav>

  <div class="sidebar-user">
  <div class="ava"><?php echo strtoupper(substr($current_user['username'] ?? 'G', 0, 1)); ?></div>
    <div>
      <div class="user-name"><?php echo htmlspecialchars($current_user['username'] ?? 'Guest'); ?></div>
      <div class="user-role">Full-Stack Dev</div>
    </div>
  </div>
</aside>



<!-- Main -->
<main class="main">
  <header class="topbar">
    <div class="topbar-title" id="topbar-title">Dashboard</div>
    <div class="search">🔍 Search...</div>

<?php 

$unread_count = 0;
foreach ($notifications as $n) {
    if ($n['is_read'] == 0) $unread_count++;
}
?>
<div class="icon-btn" onclick="open_modal('notification-modal'); markNotificationsAsRead();">
    🔔
    <?php if ($unread_count > 0): ?>
        <div id="notif-dot" class="dot" style="background: red; width: 8px; height: 8px; border-radius: 50%; position: absolute; top: 0; right: 0;"></div>
    <?php endif; ?>
</div>
</div>
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

        <!--  Description Added -->
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
        <a 
   href="../../views/Bid_submission/bid_submit.php?project_id=<?php echo $project['project_id']; ?>" 
   class="btn btn-primary btn-sm"
>Apply Now
</a>
          
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




    

    <!-- ═══ INVOICES ═══ -->
    <section class="section" id="sec-invoices">
      <div class="sec-head">
        <div class="sec-title">Invoices</div>
        <button class="btn btn-primary" onclick="open_modal('invoice-modal')">+ Create Invoice</button>
      </div>
      <div class="sec-head">
      

    </section>


  </div>
</main>




<!-- Toast -->
<div id="toast-el" class="toast">✓ <span id="toast-txt"></span></div>

<div class="overlay" id="notification-modal">
  <div class="modal">
    <div class="modal-head">
      <div class="modal-title">Notifications</div>
      <button class="modal-close" onclick="close_modal('notification-modal')">×</button>
    </div>
    <div class="modal-body">
      <?php if (!empty($notifications)): ?>
        <?php foreach ($notifications as $notif): ?>
          <div style="padding: 10px; border-bottom: 1px solid #eee;">
            <strong><?php echo htmlspecialchars($notif['title']); ?></strong>
            <p style="font-size: 12px; color: #666;"><?php echo htmlspecialchars($notif['msg']); ?></p>
            <small><?php echo $notif['create_at']; ?></small>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No new notifications.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="../../public/assets/js/freelancer-dashboard.js"></script>
</body>
</html>

