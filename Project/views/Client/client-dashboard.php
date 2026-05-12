<?php




session_start();


require_once "../../Controllers/DBcontrollers.php";
require_once "../../Controllers/post_project.php";
require_once "../../Models/contract.php";
require_once "../../Controllers/FreelancerProfileController.php";


require_once "../../Models/user.php"; 

if (!isset($_SESSION["userid"])) {
    header("Location: ../../views/Auth/login.php");
    exit();
}


if ($_SESSION["user_roleid"] != 2) {

    header("Location: ../../views/Auth/login.php");
    exit();
}


$db = DBcontrollers::getInstance(); 


require_once "../../Models/user.php";
if (user::isBanned($_SESSION["userid"], $db)) {
    echo "<div style='text-align:center; margin-top:100px; font-family:sans-serif;'>
            <h1 style='color:#ef4444;'>Account Suspended</h1>
            <p>Your account has been banned by the administrator. You cannot perform any actions.</p>
            <a href='../Auth/logout.php' style='color:#3b82f6;'>Logout</a>
        </div>";
    exit(); 
}


$current_user_id = $_SESSION["userid"]; 
$notifications = $db->Select_query("SELECT * FROM notification WHERE user_id = '$current_user_id'");

$user_data = $db->Select_query("SELECT * FROM user WHERE user_id = '$current_user_id'");
$current_user = $user_data[0] ?? null;
// 

$my_own_projects = $db->Select_query("SELECT * FROM projects WHERE client_id = '$current_user_id'");
$contracts = $db->Select_query("
    SELECT c.* FROM contract c 
    JOIN projects p ON c.project_id = p.project_id 
    WHERE p.client_id = '$current_user_id'
");




$post_controller = new post_project();
$total_spent = $post_controller->get_total_spent($current_user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WorkNest — Client Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- <link rel="stylesheet" href="/Project/public/assets/css/client-dashboard.css"> -->
    <link rel="stylesheet" href="../../public/assets/css/client-dashboard.css">
    <!-- <link rel="stylesheet" href="../../public/assets/css/home.css"> -->
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="logo">
        <div class="logo-mark"></div>
        <div class="logo-sub">Client Portal</div>
    </div>

    <div class="nav-section">
        <div class="nav-label">Main Menu</div>
       <div class="nav-item active" onclick="showSection('dashboard', this)">
    <div class="icon">⬡</div> Dashboard
</div>
        <div class="nav-item" onclick="showSection('projects', this)">
    <div class="icon">◈</div> My Projects
</div>
            <div class="nav-item">
        <a href="../../views/projects/project_details.php" style="text-decoration:none; color:inherit;">
            <div class="icon"></div>  + Post a Project
        </a>

        </div>
        </div>
            <div class="nav-item">
        <a href="../../views/proposal_request/proposal_request.php" style="text-decoration:none; color:inherit;">
            <div class="icon"></div>  +proposal-request
        </a>
        </div>
        <!-- Contracts Button -->
<div class="nav-section">
    <div class="nav-label">Contracts</div>

    <?php if (!empty($contracts)): ?>
        <?php foreach ($contracts as $row): ?>
            
            <div class="nav-item">
                <a 
                    href="../contract_details/contract_details.php?contract_id=<?php echo $row['contract_id']; ?>" 
                    style="text-decoration:none; color:inherit; display:flex; align-items:center; gap:10px;"
                >
                    <div class="icon">📄</div>
                    Contract #<?php echo $row['contract_id']; ?>
                </a>
            </div>

        <?php endforeach; ?>
    <?php else: ?>

        <div class="nav-item">
            <div class="icon">📄</div>
            No Contracts
        </div>

    <?php endif; ?>
</div>
    </div>
    </div>
        <div class="nav-item" onclick="navigate('messages', this)">
            <div class="icon">◻</div> Messages
    
        </div>
    </div>

    <div class="nav-section">
        <div class="nav-label">Account</div>
        <div class="nav-item" onclick="navigate('wallet', this)">
            <div class="icon">◉</div> Wallet
        </div>
        <div class="nav-item" onclick="navigate('settings', this)">
            <div class="icon">◌</div> Settings
        </div>
    </div>

    <div class="sidebar-bottom">
    <div class="user-card">
        <div class="avatar">
            <?php echo strtoupper(substr($current_user['username'] ?? 'U', 0, 1)); ?>
        </div>
        <div class="user-info">
            <div class="user-name"><?php echo htmlspecialchars($current_user['username'] ?? 'Unknown'); ?></div>
            <div class="user-plan">Client Account</div>
        </div>
    </div>
</div>
</aside>

<!-- Main Content -->
<main class="main">
    <header class="topbar">
        <div class="page-title" id="page-title">Dashboard</div>
        <div class="topbar-actions">
            <!-- onclick="openModal('create-project-modal')" -->
            <button class="btn btn-primary btn-sm" ><a href="../../views/projects/project_details.php">+ New Project</a></button>
        <div class="icon-btn" onclick="open_modal('notification-modal')">
    🔔
</div>
        </div>
    </header>



    

    <div class="content">
        <!-- Stats Overview -->
        <section class="section active" id="section-dashboard">
                <div class="stat-card gold">
                    <div class="stat-label">ACTIVE PROJECTS</div>
                        <div class="stat-value">
                            <?php if(!empty($my_own_projects)){
                                echo count($my_own_projects);
                            }
                                else{
                                    echo 0;
                                } ?>
                        </div>
                    </div>
                <div class="stat-card green">
                    <div class="stat-label">TOTAL SPENT</div>
                    <div class="stat-value">
                        $<?php echo number_format($total_spent); ?>
                        </div>
                </div>
                <div class="stat-card blue">
                    <div class="stat-label">MESSAGES</div>
                    <div class="stat-value"></div>
                </div>
            </div>

            <!-- Recent Projects List -->
                <div class="card">
        <div class="card-header">
            <div class="card-title">My Recent Projects</div>
        </div>
        
        <?php if ($my_own_projects): ?>
            <?php foreach ($my_own_projects as $project): ?>
                <div class="project-item">
                    <div class="project-info">
                        <div class="project-name"><?php echo $project['title']; ?></div>
                        <div class="project-meta"><?php echo substr($project['description'], 0, 50) . "..."; ?></div>
                    </div>
                    <div class="status-badge status-active">Active</div>
                    <div class="project-budget">$<?php echo $project['budget']; ?></div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="padding: 20px;">No projects posted yet.</p>
        <?php endif; ?>
    </div>
        </section>

        <!-- Post Project Section (Inline version) -->
        <section class="section" id="section-projects">
            <div class="card">
                <div class="card-header"><div class="card-title"><a href="../../views/projects/project_details.php">+ post New Project</a></div></div>
                <div style="padding:24px">
                </div>
            </div>
        </section>
    </div>
</main>

     <div class="overlay" id="notification-modal">

    <div class="modal">

        <div class="modal-head">
            <div class="modal-title">Notifications</div>

            <button class="modal-close"
                onclick="close_modal('notification-modal')">
                ×
            </button>
        </div>

        <div class="modal-body">

            <?php if (!empty($notifications)): ?>

                <?php foreach ($notifications as $notif): ?>

                    <div class="notification-item">

                        <strong>
                            <?php echo htmlspecialchars($notif['title']); ?>
                        </strong>

                        <p>
                            <?php echo htmlspecialchars($notif['msg']); ?>
                        </p>

                        <small>
                            <?php echo $notif['create_at']; ?>
                        </small>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <p>No new notifications.</p>

            <?php endif; ?>

        </div>

    </div>

</div>



<script src="../../public/assets/js/client-dashboard.js"></script>
</body>
</html>