<?php
session_start();

// 1. استدعاء الملفات الضرورية مرة واحدة فقط
require_once __DIR__ . "/../../Controllers/AdminDashboardController.php";
require_once __DIR__ . "/../../Controllers/FreelancerProfileController.php";
require_once __DIR__ . "/../../Models/user.php";

$db = DBcontrollers::getInstance(); 

$admin_controller = new AdminDashboardController();
$profile_ctrl = new FreelancerProfileController();

if (isset($_GET['approve_doc']) && isset($_GET['f_id'])) {
    $doc_id = $_GET['approve_doc'];
    $f_id = $_GET['f_id'];

    $profile_ctrl->adminReviewDocument($doc_id, $f_id, 'approved', 'Verified by Admin');

    header("Location: admin-dashboard.php?success=1");
    exit();
}

if (isset($_GET['delete_freelancer'])) {
    $delete = $_GET['delete_freelancer'];
    $admin_controller->deleteUser($delete);
    
    header("Location: admin-dashboard.php?deleted=1");
    exit();
}

if (isset($_GET['ban_user'])) {
    $u_id = $_GET['ban_user'];
    $admin_controller->sanctionUser($u_id, 'Banned'); 
    header("Location: admin-dashboard.php?action=banned");
    exit();
}

if (isset($_GET['activate_user'])) {
    $u_id = $_GET['activate_user'];
    $admin_controller->sanctionUser($u_id, 'Active');
    header("Location: admin-dashboard.php?action=activated");
    exit();
}

$total_user = $admin_controller->get_num_users();
$total_project = $admin_controller->get_num_project();
$all_user = $admin_controller->get_all_users();
$all_project = $admin_controller->getAllProjects();

$user_stats = $admin_controller->get_specify_user();
$all_admin      = $user_stats['admin_count'];
$all_client     = $user_stats['client_count'];
$all_freelancer = $user_stats['freelancer_count'];


$pending_docs = $db->Select_query("
    SELECT d.*, u.username as name 
    FROM freelancer_documents d 
    JOIN user u ON d.freelancer_id = u.user_id 
    WHERE d.status = 'pending'
");

$active_projects = $db->Select_query("
    SELECT 
        p.project_id, 
        p.title, 
        pr.freelancer_id, 
        u.username as freelancer_name
    FROM projects p
    JOIN proposal pr ON p.project_id = pr.project_id
    JOIN user u ON pr.freelancer_id = u.user_id
    WHERE pr.status = 'accepted' 
    AND p.status = 'In Progress'
");


$recent_projects = $admin_controller->getRecentProjects(4);

$total_budget = $admin_controller->getTotalBudget();
$system_activities = $admin_controller->getSystemLogs(4);

$proposal_stats = $admin_controller->getProposalsStats();
$recent_proposals = $admin_controller->getRecentProposals(5);
?>




<!DOCTYPE html>
<html lang="en" dir="ltr">
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
      <div class="stat-value">$<?php echo number_format($total_budget / 1000, 1); ?>K<</div>
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
          <strong><?php echo $all_admin ?></strong>
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
      <section class="card" style="margin-top: 20px;">
    <div class="card-head">
        <h2 class="card-title">  (User Management)</h2>
    </div>
    <div style="padding: 20px;">
        <table class="mini-table" style="width: 100%;">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>email </th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
   <?php 
if ($all_user):
    foreach ($all_user as $user): 
        if ($user['role_id'] != 1): ?>
        <tr>
            <td>
                <div class="user-cell">
                    <div class="av <?php echo ($user['role_id'] == 3) ? 'av-green' : 'av-blue'; ?>">
                        <?php echo substr($user['username'], 0, 2); ?>
                    </div>
                    <span>
                        <?php echo htmlspecialchars($user['username']); ?>
                        <small style="display:block; color:#888; font-size:10px;">
                            <?php echo ($user['role_id'] == 3) ? 'Freelancer' : 'Client'; ?>
                        </small>
                    </span>
                </div>
            </td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            
            <td>
                <span style="padding: 2px 8px; border-radius: 12px; font-size: 11px; background: <?php echo ($user['account_status'] == 'Banned') ? '#fee2e2; color:#ef4444;' : '#dcfce7; color:#16a34a;'; ?>">
                    <?php echo $user['account_status'] ?? 'Active'; ?>
                </span>
            </td>

            <td>
                <a href="admin-dashboard.php?delete_freelancer=<?php echo $user['user_id']; ?>" 
                   onclick="return confirm('Are you sure you want to delete?');"
                   style="background: #ef4444; color: white; padding: 5px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; display: inline-block;">
                    Delete
                </a>

                <?php if (($user['account_status'] ?? 'Active') == 'Active'): ?>
                    <a href="admin-dashboard.php?ban_user=<?php echo $user['user_id']; ?>" 
                       onclick="return confirm('Are you sure you want to BAN this user?');"
                       style="background: #1f2937; color: white; padding: 5px 12px; border-radius: 4px; text-decoration: none; font-size: 12px;">
                       Ban
                    </a>
                <?php else: ?>
                    <a href="admin-dashboard.php?activate_user=<?php echo $user['user_id']; ?>" 
                       style="background: #10b981; color: white; padding: 5px 12px; border-radius: 4px; text-decoration: none; font-size: 12px;">
                       Activate
                    </a>
                <?php endif; ?>
            </td>
        </tr>
    <?php 
        endif; 
    endforeach;
endif; ?>
        endforeach; 
    endif; ?>
</tbody>
        </table>
    </div>
</section>

    <!-- Right column -->
    <div class="right-col">

      <!-- Projects overview -->
      <div class="card">
        <div class="card-head">
          <h2 class="card-title">Projects Overview</h2>
          <span class="card-link">See all →</span>
        </div>
        <div class="card">
    <div class="card-head">
        <h2 class="card-title">Recent Projects</h2>
        <span class="card-link">See all →</span>
    </div>
    <div class="proj-list" style="padding: 10px;">
        <?php if($recent_projects): foreach($recent_projects as $proj): ?>
            <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #eee;">
                <div>
                    <div style="font-weight: 600; font-size: 14px;"><?php echo htmlspecialchars($proj['title']); ?></div>
                    <small style="color: #666;">Client: <?php echo htmlspecialchars($proj['client_name']); ?></small>
                </div>
                <div style="text-align: right;">
                    <span class="tag <?php echo ($proj['status'] == 'active') ? 'tag-green' : 'tag-amber'; ?>">
                        <?php echo $proj['status']; ?>
                    </span>
                    <div style="font-weight: bold; margin-top: 4px;">$<?php echo number_format($proj['budget']); ?></div>
                </div>
            </div>
        <?php endforeach; else: ?>
            <p>No projects found.</p>
        <?php endif; ?>
    </div>
</div>
      <!-- Alerts -->
      <div class="stat-card" style="--accent-clr:#f59e0b">
  <div class="stat-top">
    <span class="stat-label">Total Projects Value</span>
    <span class="stat-ico" style="background:rgba(245,158,11,.12);color:#f59e0b">◈</span>
  </div>
  <div class="stat-value">$<?php echo number_format($total_budget / 1000, 1); ?>K</div>
  <div class="stat-bar">
    <div class="stat-fill" style="width:85%;background:#f59e0b"></div>
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

                            <!-- اسم الفريلانسر -->
                            <td>
                                <strong><?php echo htmlspecialchars($doc['name']); ?></strong>
                            </td>

                            <!-- نوع الوثيقة -->
                            <td>
                                <?php echo htmlspecialchars($doc['document_type'] ?? 'N/A'); ?>
                            </td>

                            <td>
                                <a href="<?php echo htmlspecialchars($doc['file_path']); ?>" target="_blank">
                                    View Document
                                </a>
                            </td>

                            <td>
                                <a href="admin-dashboard.php?approve_doc=<?php echo $doc['document_id']; ?>&f_id=<?php echo $doc['freelancer_id']; ?>"
                                   style="background:#22c55e;color:white;padding:6px 10px;border-radius:5px;text-decoration:none;">
                                    Approve
                                </a>


                            </td>

                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>

        <?php else: ?>
            <p style="color:#666;text-align:center;">No pending documents to review.</p>
        <?php endif; ?>

    </div>
</section>

<section class="card" style="margin-top: 20px;">
    <div class="card-head">
        <h2 class="card-title">Active Contracts & Rating</h2>
    </div>
    <div style="padding: 20px;">
        <?php if (!empty($active_contracts)): ?>
            <table class="mini-table" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Freelancer</th>
                        <th>Set Rating & Complete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($active_projects as $proj): ?>
    <tr>
        <td><?php echo htmlspecialchars($proj['title']); ?></td>
        <td><?php echo htmlspecialchars($proj['freelancer_name']); ?></td>
        <td>
            <form action="process_rating.php" method="POST">
                <input type="hidden" name="project_id" value="<?php echo $proj['project_id']; ?>">
                <input type="hidden" name="freelancer_id" value="<?php echo $proj['freelancer_id']; ?>">
                
                <select name="rating" required>
                    <option value="5">⭐⭐⭐⭐⭐</option>
                    <option value="4">⭐⭐⭐⭐</option>
                    <option value="3">⭐⭐⭐</option>
                    <option value="2">⭐⭐</option>
                    <option value="1">⭐</option>
                </select>
                
                <button type="submit" name="complete_project">Complete Project</button>
            </form>
        </td>
    </tr>
<?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p style="color: #666; text-align: center;">No active contracts to rate.</p>
        <?php endif; ?>
    </div>
</section>


  <div class="stat-card" style="--accent-clr:#8b5cf6">
  <div class="stat-top">
    <span class="stat-label">Total Proposals Sent</span>
    <span class="stat-ico" style="background:rgba(139,92,246,.12);color:#8b5cf6">✉</span>
  </div>
  <div class="stat-value"><?php echo $proposal_stats['total_proposals']; ?></div>
  <div class="stat-bar">
    <div class="stat-fill" style="width:100%;background:#8b5cf6"></div>
  </div>
  <div style="font-size: 11px; margin-top: 5px; color: #666;">
    <span style="color:#22c55e">● <?php echo $proposal_stats['accepted_count']; ?> Accepted</span> | 
    <span style="color:#f59e0b">● <?php echo $proposal_stats['pending_count']; ?> Pending</span>
  </div>
</div>

    <section class="card" style="margin-top: 20px;">
    <div class="card-head">
        <h2 class="card-title">Recent Proposals Overview</h2>
    </div>
    <div style="padding: 20px;">
        <table class="mini-table" style="width: 100%;">
            <thead>
                <tr>
                    <th>Freelancer</th>
                    <th>Project</th>
                    <th>Bid Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if($recent_proposals): foreach($recent_proposals as $prop): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($prop['freelancer_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($prop['project_title']); ?></td>
                        <td>$<?php echo number_format($prop['bid_amount']); ?></td>
                        <td>
                            <span class="tag <?php 
                                echo ($prop['status'] == 'Accepted') ? 'tag-green' : 
                                    (($prop['status'] == 'Pending') ? 'tag-amber' : 'tag-red'); 
                            ?>">
                                <?php echo $prop['status']; ?>
                            </span>
                        </td>
                        <td>
    <small>
        <?php 
        echo isset($prop['created_at']) ? date('M d, Y', strtotime($prop['created_at'])) : 'Recently'; 
        ?>
    </small>
</td>
                    </tr>
                <?php endforeach; else: ?>
                    <tr><td colspan="5" style="text-align:center;">No proposals sent yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
  </div><!-- /row-3 -->

</main><!-- /main -->



<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-box input');
    const rows = document.querySelectorAll('.mini-table tbody tr');
    const filterBtns = document.querySelectorAll('.filter-btn');

    // 1. Live Search Logic
    searchInput.addEventListener('keyup', function() {
        const query = this.value.toLowerCase();
        rows.forEach(row => {
            const name = row.querySelector('td:first-child span').textContent.toLowerCase();
            const email = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            row.style.display = (name.includes(query) || email.includes(query)) ? '' : 'none';
        });
    });

    // 2. Dynamic Filtering Logic
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Update active class
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const filterType = this.textContent.trim().toLowerCase();
            
            rows.forEach(row => {
                const role = row.querySelector('small').textContent.trim().toLowerCase();
                if (filterType === 'all' || role === filterType.slice(0, -1)) { // slice to handle 's' in Freelancers
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
});
</script>

</body>
</html>
