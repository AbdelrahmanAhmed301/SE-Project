<?php
session_start();
require_once "../../Controllers/DBcontrollers.php";
require_once "../../Controllers/FreelancerProfileController.php";

$db = DBcontrollers::getInstance();
$profile_ctrl = new FreelancerProfileController();

if (!isset($_SESSION['userid'])) {
    header("Location: ../../views/Auth/login.php");
    exit();
}

$user_id = $_SESSION['userid'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $bio = $db->connection->real_escape_string($_POST['bio']);

    $db->insertquery("DELETE FROM freelancer_skills WHERE freelancer_id = '$user_id'");

    if (!empty($_POST['skills'])) {
        foreach ($_POST['skills'] as $skill_id) {
            $skill_id = $db->connection->real_escape_string($skill_id);

            $skill_data = $db->Select_query("SELECT skill_name FROM skills WHERE skill_id = '$skill_id'");
            if(!empty($skill_data)) {
                $skill_name = $skill_data[0]['skill_name'];

                $db->insertquery("INSERT INTO freelancer_skills (freelancer_id, skill_id, skill_name, bio) 
                                VALUES ('$user_id', '$skill_id', '$skill_name', '$bio')");
            }
        }
    }

    $db->insertquery("UPDATE freelancer_profiles SET bio = '$bio' WHERE user_id = '$user_id'");

    if (isset($_FILES['document']) && $_FILES['document']['error'] == 0) {
        $upload_dir = "../../public/uploads/docs/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $file_name = time() . "_" . basename($_FILES['document']['name']);
        $target_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['document']['tmp_name'], $target_path)) {
            $profile_ctrl->submitVerificationDocument($user_id, 'Professional Certificate', $target_path);
        }
    }

    header("Location: ../../views/Freelancer/freelancer-dashboard.php");
    exit();
}

$available_skills = $db->Select_query("SELECT * FROM skills"); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Professional Profile</title>
    <link rel="stylesheet" href="../../public/assets/css/onboarding.css">
</head>
<body>

<div class="onboarding-container">
    <form action="" method="POST" enctype="multipart/form-data" class="onboarding-card">
        <h2>Complete Your Professional Profile</h2>
        <p>Your Bio and Skills will be stored and reviewed by the admin.</p>

        <label>About You (Bio):</label>
        <textarea name="bio" required placeholder="This bio will be saved with your skills..."></textarea>

        <label>Select Your Main Skills:</label>
        <div class="skills-grid">
            <?php if(!empty($available_skills)): foreach($available_skills as $skill): ?>
                <div class="skill-item">
                    <input type="checkbox" name="skills[]" value="<?php echo $skill['skill_id']; ?>" id="skill_<?php echo $skill['skill_id']; ?>">
                    <label for="skill_<?php echo $skill['skill_id']; ?>">
                        <?php echo $skill['skill_name']; ?>
                    </label>
                </div>
            <?php endforeach; endif; ?>
        </div>

        <label>Upload Certificate or ID for Verification:</label>
        <input type="file" name="document" accept=".pdf,.jpg,.png" required>

        <button type="submit" class="btn-submit">Submit for Review</button>
    </form>
</div>

</body>
</html>