<?php
session_start();

require_once "../../Controllers/FreelancerProfileController.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_project'])) {
    $profile_ctrl = new FreelancerProfileController();

    $project_id = $_POST['project_id']; 
    $freelancer_id = $_POST['freelancer_id'];
    $rating = $_POST['rating'];

    $result = $profile_ctrl->adminCompleteProject($project_id, $freelancer_id, $rating);

    if ($result) {
        header("Location: ../../views/Admin/admin-dashboard.php?success=project_completed"); 
        exit();
    } else {
        echo "Error: Could not complete the project rating.";
    }
}