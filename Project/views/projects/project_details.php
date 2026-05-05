<?php
session_start(); 
require_once "../../Models/user.php";
require_once "../../Models/project.php";
require_once "../../Controllers/post_project.php";
require_once "../../Controllers/notifycontrollers.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_project"])) {
    if (!empty($_POST["title"]) && !empty($_POST["description"])) {
        
        $project = new project();
        $project->title = $_POST["title"];
        $project->description = $_POST["description"];
        $project->milestones = $_POST["milestones"];
        $project->budget = $_POST["budget"];
        
        $project->client_id = $_SESSION["userid"]; 

        $post_controller = new post_project();
        if ($post_controller->post_project($project)) {
            $notifiy=new notification();
            $notifiy_title="New Project Posted";
            $notifiy_msg="A new project: " . $project->title . " is available now. Check it out ";

            // $notifiy->notify_all_freelancers($notifiy_title,$notifiy_msg);

            echo "Project published successfully!";
            header("Location: ../../views/client-dashboard.php");
        } else {
            echo "Failed to publish project.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>publish project</title>
    <link rel="stylesheet" href="/Project/public/assets/css/client-dashboard.css">
</head>
<body>
    <form action="project_details.php" method="POST">
                        <div class="form-group">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-input" placeholder="What do you need?" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-textarea" placeholder="Provide details..." required></textarea>
                        </div>
                        <div class="form-group">
                    <label class="form-label">Milestones</label>
                            <input type="text" name="milestones" class="form-input" placeholder="Milestones...." required>>
                </div>
                        <div class="form-group">
                            <label class="form-label">Budget ($)</label>
                            <input type="number" name="budget" class="form-input" placeholder="e.g. 500">
                        </div>
                        <button type="submit" name="submit_project" class="btn btn-primary">Publish Project</button>
                    </form>
                </div>
    
</body>
</html>