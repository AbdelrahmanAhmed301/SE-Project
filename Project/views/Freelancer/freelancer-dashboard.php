<?php
require_once "../../Models/contract.php";
session_start();

// التحقق من تسجيل الدخول
if (!isset($_SESSION["userid"])) {
    header("Location: ../../views/Auth/login.php");
    exit();
}

// التحقق من الرتبة (فريلانسر)
if ($_SESSION["user_roleid"] != 3) {
    header("Location: ../../views/Auth/login.php");
    exit();
}

require_once "../../Controllers/DBcontrollers.php";
require_once "../../Controllers/post_project.php";
require_once "../../Controllers/notifycontrollers.php";
require_once "../../Models/user.php";
require_once "../../Models/proposal.php";
require_once "../../Controllers/FreelancerProfileController.php";

$db = DBcontrollers::getInstance(); 
$profile_ctrl = new FreelancerProfileController();
$current_user_id = $_SESSION["userid"];

// --- جلب البيانات الأساسية للعرض ---
$user_info = $db->Select_query("
    SELECT u.*, p.verification_status, p.reputation_score, p.show_earnings, p.show_client_names, p.show_contact
    FROM user u
    LEFT JOIN freelancer_profiles p ON u.user_id = p.user_id
    WHERE u.user_id = '$current_user_id'
");
$current_user = $user_info[0] ?? null;

$projects = $db->Select_query("SELECT * FROM projects WHERE status = 'Pending' OR status IS NULL");
$notifications = $db->Select_query("SELECT * FROM notification WHERE user_id = '$current_user_id'");
$contracts = $db->Select_query("SELECT * FROM contract WHERE freelancer_id = '$current_user_id'");

// جلب حالة التوثيق الحالية
$verification = $profile_ctrl->getVerificationStatus($current_user_id);
$verif_status = $verification['status']; 

$reputation = $current_user['reputation_score'] ?? 0;

// --- معالجة الطلبات (POST Handling) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    // 1. معالجة رفع مستندات التوثيق
    if ($_POST['action'] === 'submit_document' && isset($_FILES['document'])) {
        $doc_type = $_POST['document_type'] ?? 'ID/Certificate';
        
        // مسار الفولدر (تأكد من وجوده في مشروعك)
        $upload_dir = "../../public/uploads/docs/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // اسم الملف فريد باستخدام الوقت لمنع التكرار
        $file_name = time() . "_" . basename($_FILES['document']['name']);
        $target_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['document']['tmp_name'], $target_path)) {
            // حفظ المسار في قاعدة البيانات وتحديث الحالة لـ documents_uploaded
            $profile_ctrl->submitVerificationDocument($current_user_id, $doc_type, $target_path);
            header("Location: freelancer-dashboard.php?success=upload");
            exit();
        } else {
            $error = "فشل في رفع الملف، تأكد من صلاحيات المجلد.";
        }
    }

    // 2. معالجة تحديث إعدادات الخصوصية
    if ($_POST['action'] === 'update_privacy') {
        $show_earnings = isset($_POST['show_earnings']) ? 1 : 0;
        $show_clients  = isset($_POST['show_client_names']) ? 1 : 0;
        $show_contact  = isset($_POST['show_contact']) ? 1 : 0;

        $profile_ctrl->updatePrivacySettings($current_user_id, $show_earnings, $show_clients, $show_contact);
        header("Location: freelancer-dashboard.php?success=privacy");
        exit();
    }

    $profile_exists = $db->Select_query("SELECT user_id FROM freelancer_profiles WHERE user_id = '$current_user_id'");
    if (empty($profile_exists)) {
        $db->insertquery("INSERT INTO freelancer_profiles (user_id, verification_status) VALUES ('$current_user_id', 'pending')");
    }
}
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
<div class="card" style="margin-bottom:16px;">
    <div class="card-head">
        <div class="card-title">🔐 Credential Verification</div>
    </div>
    <div style="padding:16px;">

        <p>Status:
            <?php
            $badge_colors = [
                'pending'            => '#f59e0b', // Orange
                'documents_uploaded' => '#4f8ef7', // Blue
                'verified'           => '#22c87a', // Green
                'rejected'           => '#ef4444', // Red
            ];
            // Use the $verif_status variable defined in the PHP logic at the top
            $color = $badge_colors[$verif_status] ?? '#888';
            echo "<span style='background:{$color};color:#fff;padding:4px 12px;border-radius:12px;font-size:12px;font-weight:bold;'>
                    " . ucwords(str_replace('_', ' ', $verif_status)) . "
                  </span>";
            ?>
        </p>

        <p style="margin-top:12px; font-size: 15px;">
            ⭐ Reputation Score: <strong><?php echo number_format($reputation, 1); ?> / 100</strong>
        </p>

        <?php if ($verif_status === 'pending' || $verif_status === 'rejected'): ?>
            <div style="margin-top:15px; padding:12px; border:1px dashed #ccc; border-radius:8px;">
                <h4 style="margin-bottom:10px; font-size:14px;">Upload documents to get verified:</h4>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="submit_document">
                    
                    <div style="margin-bottom:10px;">
                        <label style="display:block; font-size:12px; color:#666;">Document Type:</label>
                        <select name="document_type" style="width:100%; padding:8px; border-radius:4px; border:1px solid #ddd;">
                            <option value="license">Professional License</option>
                            <option value="certificate">Academic Certificate</option>
                            <option value="id">National ID Card</option>
                        </select>
                    </div>

                    <div style="margin-bottom:10px;">
                        <label style="display:block; font-size:12px; color:#666;">Select File (PDF/JPG):</label>
                        <input type="file" name="document" required style="font-size:13px;">
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm" style="width:100%; padding:10px;">
                        Submit for Review
                    </button>
                </form>
            </div>
        <?php elseif ($verif_status === 'documents_uploaded'): ?>
            <div style="margin-top:15px; padding:10px; background:#eef6ff; color:#4f8ef7; border-radius:6px; text-align:center;">
                ⏳ <strong>Under Review:</strong> Admin is verifying your documents.
            </div>
        <?php elseif ($verif_status === 'verified'): ?>
            <div style="margin-top:15px; padding:10px; background:#e8f9f1; color:#22c87a; border-radius:6px; text-align:center;">
                ✅ <strong>Verified:</strong> Your profile is officially trusted.
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card" style="margin-bottom:16px;">
    <div class="card-head">
        <div class="card-title">🔒 Profile Privacy Settings</div>
    </div>
    <div style="padding:16px;">
        <p style="font-size:13px; color:#666; margin-bottom:15px;">Control what clients can see on your public profile.</p>
        
        <form method="POST">
            <input type="hidden" name="action" value="update_privacy">
            
            <label style="display:flex; align-items:center; margin-bottom:12px; cursor:pointer;">
                <input type="checkbox" name="show_earnings" style="margin-right:10px;"
                    <?php echo ($current_user['show_earnings'] ?? 0) ? 'checked' : ''; ?>>
                <span style="font-size:14px;">Show total earnings to clients</span>
            </label>

            <label style="display:flex; align-items:center; margin-bottom:12px; cursor:pointer;">
                <input type="checkbox" name="show_client_names" style="margin-right:10px;"
                    <?php echo ($current_user['show_client_names'] ?? 0) ? 'checked' : ''; ?>>
                <span style="font-size:14px;">Show previous client names in reviews</span>
            </label>

            <label style="display:flex; align-items:center; margin-bottom:15px; cursor:pointer;">
                <input type="checkbox" name="show_contact" style="margin-right:10px;"
                    <?php echo ($current_user['show_contact'] ?? 0) ? 'checked' : ''; ?>>
                <span style="font-size:14px;">Show my direct contact info</span>
            </label>

            <button type="submit" class="btn btn-primary btn-sm" style="background:#4f8ef7; border:none; padding:8px 20px; border-radius:4px; color:#fff; cursor:pointer;">
                Save Changes
            </button>
        </form>
    </div>
</div>

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

