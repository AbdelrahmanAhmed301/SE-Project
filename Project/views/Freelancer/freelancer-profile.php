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

$proposals = $db->Select_query("
SELECT * FROM proposal
WHERE freelancer_id = '$current_user_id'
");

$deliveries = $db->Select_query("
SELECT * FROM deliveries
WHERE freelancer_id = '$current_user_id'
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($current_user['username'] ?? 'Guest'); ?> — SpecialistHub Profile</title>
    <link rel="stylesheet" href="../../public/assets-1/css/global.css">
    <link rel="stylesheet" href="../../public/assets-1/css/freelancer-profile.css">

    <title>Freelancer Profile</title>
</head>
<body>

  <!-- ── NAVBAR ───────────────────────────────────────── -->
  <nav class="navbar">
    <div class="container">
      <a href="index.html" class="nav-logo">
        <div class="nav-logo-mark">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
        </div>
        <span class="nav-logo-text">SpecialistHub</span>
      </a>
      <div class="nav-links">
        <a href="index.html" class="nav-link">Home</a>
        <a href="job-posting.html" class="nav-link">Find Work</a>
        <a href="milestone-dashboard.html" class="nav-link">Dashboard</a>
      </div>
      <div class="nav-actions">
        <a href="#" class="btn btn-ghost btn-sm">Messages</a>
        <div class="dropdown">
          <button class="flex items-center gap-2" data-dropdown-trigger>
            <div class="avatar-placeholder avatar-sm" style="background:var(--brand-primary);font-size:0.75rem"><?php echo strtoupper(substr($current_user['username'] ?? 'G', 0, 1)); ?></div>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
          <div class="dropdown-menu">
            <div class="dropdown-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              My Profile
            </div>
            <div class="dropdown-item">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
              Settings
            </div>
            <div class="dropdown-divider"></div>
            <div class="dropdown-item" style="color:var(--danger)">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
              Sign Out
            </div>
            

          </div>
        </div>
      </div>
    </div>
  </nav>

  <!-- PROFILE HEADER -->
<div class="profile-cover">
  <div class="profile-cover-bg"></div>

  <div class="container">
    <div class="profile-header">

      <!-- Avatar -->
      <div class="profile-avatar-wrap">

        <div class="avatar-placeholder avatar-2xl"
        style="background:linear-gradient(135deg,#0F62FE,#6929C4);
        font-size:2.25rem;
        border:4px solid white">

          <?= strtoupper(substr($current_user['username'],0,2)) ?>

        </div>

        <div class="status-dot online"
        style="position:absolute;
        bottom:6px;
        right:6px;
        width:14px;
        height:14px;
        border:2px solid white">
        </div>

      </div>

      <!-- INFO -->
      <div class="profile-header-info">

        <div class="flex items-center gap-3 flex-wrap">

          <h1 class="profile-name">
            <?= htmlspecialchars($current_user['username']) ?>
          </h1>

          <?php if($current_user['role_id'] == 1): ?>
            <span class="badge badge-purple">Admin</span>
          <?php elseif($current_user['role_id'] == 2): ?>
            <span class="badge badge-blue">Client</span>
          <?php elseif($current_user['role_id'] == 3): ?>
            <span class="badge badge-green">Freelancer</span>
          <?php endif; ?>

          <span class="badge badge-green">✓ Verified</span>

        </div>

        <!-- ROLE -->
        <div class="profile-title">

          <?php

          if($current_user['role_id'] == 1){
              echo "Platform Administrator";
          }
          elseif($current_user['role_id'] == 2){
              echo "Project Client";
          }
          elseif($current_user['role_id'] == 3){
              echo "Freelancer";
          }
          else{
              echo "User";
          }

          ?>

        </div>

        <!-- META -->
        <div class="profile-meta">

          <span>

            <svg width="14" height="14"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2">

              <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
              <circle cx="12" cy="10" r="3"/>

            </svg>

            <?= htmlspecialchars($current_user['email']) ?>

          </span>

          <span>

            <svg width="14" height="14"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2">

              <circle cx="12" cy="12" r="10"/>
              <polyline points="12 6 12 12 16 14"/>

            </svg>

            Active Account

          </span>

        </div>

      </div>

      <!-- ACTIONS -->
      <div class="profile-header-actions">

        <button class="btn btn-primary">
          Contact <?= htmlspecialchars($current_user['username']) ?>
        </button>

        <button class="btn btn-secondary">

          <svg width="16"
          height="16"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2">

            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>

          </svg>

          Message

        </button>

      </div>

    </div>
  </div>
</div>





<!-- PROFILE BODY -->
<div class="container"
style="margin-top:var(--space-8);
padding-bottom:var(--space-16)">

  <div class="profile-layout">

    <!-- MAIN -->
    <div class="profile-main">

      <!-- STATS -->
      <div class="profile-stats-row card">

    <div class="stat-block">
        <div class="stat-value">
            <?= count($projects) ?>
        </div>

        <div class="stat-label">
            Available Projects
        </div>
    </div>

    <div class="stat-divider-v"></div>

    <div class="stat-block">
        <div class="stat-value">
            <?= count($contracts) ?>
        </div>

        <div class="stat-label">
            Contracts
        </div>
    </div>

    <div class="stat-divider-v"></div>

    <div class="stat-block">
        <div class="stat-value">
            <?= count($notifications) ?>
        </div>
        <div class="stat-block">





        <div class="stat-label">
            Notifications
        </div>
    </div>

</div>




      <!-- ABOUT -->
      <div class="card"
      style="margin-top:var(--space-6);
      margin-bottom:var(--space-4)">

        <h3 class="font-semibold"
        style="margin-bottom:var(--space-4)">

          About User

        </h3>

        <p class="text-md"
        style="color:var(--gray-600);
        line-height:1.8">

          Welcome to the profile of
          <strong>
            <?= htmlspecialchars($current_user['username']) ?>
          </strong>.


          and currently has the role of

          <strong>

            <?php

            if($current_user['role_id'] == 1){
                echo "Administrator";
            }
            elseif($current_user['role_id'] == 2){
                echo "Client";
            }
            elseif($current_user['role_id'] == 3){
                echo "Freelancer";
            }
            else{
                echo "User";
            }

            ?>

          </strong>.

        </p>

      </div>





      <!-- PROJECTS -->
      <div class="card">

        <h3 class="font-semibold"
        style="margin-bottom:var(--space-4)">

          Available Projects

        </h3>

        <?php if($projects): ?>

          <div class="grid grid-2"
          style="gap:var(--space-4)">

          <?php foreach($projects as $project): ?>

            <div class="portfolio-item card card-hover">

              <div class="portfolio-header">

                <div class="portfolio-icon">
                  📁
                </div>

                <span class="badge badge-blue">

                  <?= htmlspecialchars($project['status'] ?? 'Pending') ?>

                </span>

              </div>

              <div class="font-semibold"
              style="margin-top:var(--space-3)">

                <?= htmlspecialchars($project['title']) ?>

              </div>

              <p class="text-sm text-muted"
              style="margin-top:var(--space-2)">

                <?= htmlspecialchars($project['description']) ?>

              </p>

              <div style="margin-top:var(--space-3)">

                <span class="badge badge-gray">

                  Budget:
                  $<?= htmlspecialchars($project['budget']) ?>

                </span>

              </div>
              <a 
             href="../../views/Bid_submission/bid_submit.php?project_id=<?php echo $project['project_id']; ?>" 
                 class="btn btn-primary btn-sm"
                       >Apply Now
                      
                      </a>

            </div>

          <?php endforeach; ?>

          </div>

        <?php else: ?>

          <p>No projects available.</p>

        <?php endif; ?>

      </div>

    </div>





    <!-- SIDEBAR -->
    <div class="profile-sidebar">

      <!-- USER INFO -->
      <div class="card"
      style="margin-bottom:var(--space-4)">

        <div class="font-semibold"
        style="margin-bottom:var(--space-4)">

          User Information

        </div>

        <div class="quick-facts">

          <div class="qf-item">

            <span class="qf-label">
              Username
            </span>

            <span class="qf-value">

              <?= htmlspecialchars($current_user['username']) ?>

            </span>

          </div>

          <div class="qf-item">

            <span class="qf-label">
              Email
            </span>

            <span class="qf-value">

              <?= htmlspecialchars($current_user['email']) ?>

            </span>

          </div>

          <div class="qf-item">

            <span class="qf-label">
              Role
            </span>

            <span class="qf-value">

              <?php

              if($current_user['role_id'] == 1){
                  echo "Admin";
              }
              elseif($current_user['role_id'] == 2){
                  echo "Client";
              }
              elseif($current_user['role_id'] == 3){
                  echo "Freelancer";
              }
              else{
                  echo "User";
              }

              ?>

            </span>

          </div>

          <div class="qf-item">

            <span class="qf-label">
              Notifications
            </span>

            <span class="qf-value">

              <?= count($notifications) ?>

            </span>

          </div>

          <div class="qf-item">

            <span class="qf-label">
              Contracts
            </span>

            <span class="qf-value">

              <?= count($contracts) ?>

            </span>

          </div>
          <div class="qf-item">

            <span class="qf-label">
              deliveries 
            </span>

            <span class="qf-value">

              <?= count($deliveries) ?>

            </span>

          </div>

        </div>

      </div>






      </div>

    </div>

  </div>
  <div class="sec-head">

    <h2>My Contracts</h2>

    <?php if (!empty($contracts)): ?>

        <?php foreach ($contracts as $row): ?>

            <div class="project-item" style="margin-bottom:15px; padding:15px; border:1px solid #ddd; border-radius:10px;">

                <p>
                    <strong>Contract ID:</strong>
                    <?= htmlspecialchars($row['contract_id']) ?>
                </p>

                <p>
                    <strong>Status:</strong>
                    <?= htmlspecialchars($row['status']) ?>
                </p>

                <p>
                    <strong>Deadline:</strong>
                    <?= htmlspecialchars($row['deadline']) ?>
                </p>

                <p>
                    <strong>Revisions:</strong>
                    <?= $row['revision_used'] ?> / <?= $row['revision_limits'] ?>
                </p>

                <a href="../contract_details/contract_details.php?contract_id=<?= $row['contract_id'] ?>" 
                   class="btn-primary">

                    View Details

                </a>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <p>No contracts found.</p>

    <?php endif; ?>

</div>

</div>
    



  <script src="js/global.js"></script>
  <script src="js/freelancer-profile.js"></script>
</body>
</html>
