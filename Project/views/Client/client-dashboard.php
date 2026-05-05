<?php
session_start();


if (!isset($_SESSION["userid"]) || empty($_SESSION["userid"])) {
    header("Location: ../../views/Auth/login.php"); 
    exit();
}

require_once "../../Controllers/DBcontrollers.php";
require_once "../../Controllers/post_project.php";

require_once "../../Models/user.php"; 

$db = new DBcontrollers();
$db->openconnection();

$current_user_id = $_SESSION["userid"]; 

$user_data = $db->Select_query("SELECT * FROM user WHERE user_id = '$current_user_id'");
$current_user = $user_data[0] ?? null;


$projects = $db->Select_query("SELECT * FROM projects WHERE client_id = '$current_user_id'");

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
    <link rel="stylesheet" href="/Project/public/assets/css/client-dashboard.css">
    <link rel="stylesheet" href="../../public/assets/css/home.css">
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
        <div class="nav-item active" onclick="navigate('dashboard', this)">
            <div class="icon">⬡</div> Dashboard
        </div>
        <div class="nav-item" onclick="navigate('projects', this)">
            <div class="icon">◈</div> My Projects
        </div>
            <div class="nav-item">
        <a href="../../views/projects/project_details.php" style="text-decoration:none; color:inherit;">
            <div class="icon"></div>  + Post a Project
        </a>
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
            <div class="icon-btn">🔔</div>
        </div>
    </header>

    <div class="content">
        <!-- Stats Overview -->
        <section class="section active" id="section-dashboard">
                <div class="stat-card gold">
                    <div class="stat-label">ACTIVE PROJECTS</div>
                        <div class="stat-value">
                            <?php if(!empty($projects)){
                                echo count($projects);
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
        
        <?php if ($projects): ?>
            <?php foreach ($projects as $project): ?>
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
        <section class="section" id="section-post-project">
            <div class="card">
                <div class="card-header"><div class="card-title"><a href="../../views/projects/project_details.php">+ post New Project</a></div></div>
                <div style="padding:24px">
                </div>
            </div>
        </section>
    </div>
</main>

<div class="modal-overlay" id="create-project-modal">
    <div class="modal">
        <!-- <form action="post_project.php" method="POST"> 
            <div class="modal-header">
                <div class="modal-title">New Project</div>
                <button type="button" class="modal-close"><a href="../../views/projects/project_details.php">+ post New Project</a></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Project Title</label>
                    <input type="text" name="title" class="form-input" placeholder="Name your project" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-input" placeholder="Details..." required style="min-height: 100px;"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Miletones</label>
                    <textarea name="Miletones" class="form-input" placeholder="Miletones" required style="min-height: 100px;"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Budget ($)</label>
                    <input type="number" name="budget" class="form-input" placeholder="e.g. 1000">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="submit_project" class="btn btn-primary" style="width:100%">Post Now</button>
            </div>
        </form> -->
    </div>
</div>
<!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Worklio</h3>
                    <p>The marketplace for specialized professional services</p>
                </div>
                <div class="footer-section">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="../../views/main_pages/about.php">About Us</a></li>
                        <li><a href="../../views/Auth/login.php">How It Works</a></li>
                        <li><a href="../../views/main_pages/Contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>For Clients</h4>
                    <ul>
                        <li><a href="../../Auth/login.php">Post a Project</a></li>
                        <li><a href="../../Auth/login.php">Browse Specialists</a></li>
                        <li><a href="#">Pricing</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>For Freelancers</h4>
                    <ul>
                        <li><a href="../../views/Auth/login.php">Find Work</a></li>
                        <li><a href="../../views/Auth/login.php">Get Verified</a></li>
                        <li><a href="#">Resources</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Worklio. All rights reserved.</p>
            </div>
        </div>
    </footer>

<script src="/Project/public/assets/js/client-dashboard.js"></script>
</body>
</html>