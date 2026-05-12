<?php
session_start();
require_once "../../Controllers/DBcontrollers.php";
$db = DBcontrollers::getInstance();

$user_id = isset($_GET['id']) ? $_GET['id'] : ($_SESSION['userid'] ?? null);

if (!$user_id) {
    die("Error: User ID is missing.");
}

$query = "SELECT u.username, p.verification_status, p.bio, p.rating_avg, p.total_reviews 
          FROM user u 
          LEFT JOIN freelancer_profiles p ON u.user_id = p.user_id 
          WHERE u.user_id = '$user_id'";

$user_data = $db->Select_query($query);

if (empty($user_data)) {
    die("Error: This profile does not exist.");
}

$freelancer = $user_data[0];

$skills_query = "SELECT skill_name FROM freelancer_skills WHERE freelancer_id = '$user_id'";
$my_skills = $db->Select_query($skills_query) ?? []; 

$display_bio = !empty($freelancer['bio']) ? $freelancer['bio'] : "No bio available.";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Freelancer Profile</title>
    <link rel="stylesheet" href="../../public/assets/css/profile.css">
</head>
<body>
<div class="profile-container">
    <div class="profile-card">
        <div class="profile-header">
            <img src="../../public/assets/images/default-avatar.png" alt="Profile" class="avatar">
            <h2><?php echo htmlspecialchars($freelancer['username']); ?></h2>
            <?php if(($freelancer['verification_status'] ?? '') == 'verified'): ?>
                <span class="badge verified">✓ Verified</span>
            <?php else: ?>
                <span class="badge pending">Under Review</span>
            <?php endif; ?>
        </div>

        <div class="profile-section">
            <h3>About Me</h3>
            <p class="bio-text"><?php echo nl2br(htmlspecialchars($display_bio)); ?></p>
        </div>

        <div class="profile-section">
            <h3>Skills</h3>
            <div class="skills-list">
                <?php if(!empty($my_skills)): ?>
                    <?php foreach($my_skills as $skill): ?>
                        <span class="skill-tag"><?php echo htmlspecialchars($skill['skill_name']); ?></span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No skills added yet.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="rating-display">
            <span class="stars">⭐ <?php echo number_format($freelancer['rating_avg'] ?? 0, 1); ?></span>
            <span class="reviews-count">(<?php echo $freelancer['total_reviews'] ?? 0; ?> reviews)</span>
        </div>
    </div>
</div>
</body>
</html>