<?php
session_start(); 

require_once "../../Models/user.php";
require_once "../../Models/project.php";
require_once "../../Models/notification.php"; 
require_once "../../Controllers/post_project.php";
require_once "../../Controllers/notifycontrollers.php";
require_once "../../Models/proposal.php";
require_once "../../Controllers/bidcontroller.php";
$project_id = $_GET['project_id'];

if (!isset($_SESSION["userid"]) || empty($_SESSION["userid"])) {
    header("Location: ../../views/Auth/login.php"); 
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit_proposal"])) {
    if (!empty($_POST["bid_amount"]) && !empty($_POST["bid_amount"]) && !empty($_POST['Delivery_time'])) {
        $db = DBcontrollers::getInstance(); 
        $proposal = new proposal();

        $proposal->bid_amount=$_POST['bid_amount'];
        $proposal->cover_letter = $_POST["cover_letter"];
        $proposal->freelancer_id = $_SESSION["userid"];
        $proposal->Delivery_time=$_POST['Delivery_time'];
        $proposal->project_id = $project_id;

        $proposal_bid=new bidcontroller();

        if($proposal_bid->display_proposal($proposal)){
            

            header("Location: ../../views/Freelancer/freelancer-dashboard.php");
            exit();
            

        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Bid</title>
    
    <link rel="stylesheet" href="../../public/assets/css/bid.css"> 
</head>
<body>

<div class="form-container">
    <form action="bid_submit.php?project_id=<?php echo $project_id; ?>" method="post">
    
    
        <h2>Submit Your Proposal</h2>
        
        <div class="field">
            <div class="form-group">
                <label class="label">Job Title</label>
                <input class="input" name="job_title" type="text" value="" readonly>
            </div>
        </div>

        <div class="field-row">
            <div class="form-group">
                <label class="label">Your Bid ($)</label>
                <input class="input" name="bid_amount"  type="number" placeholder="e.g. 3500" required>
                <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
            </div>
            
            <div class="form-group">
                <label class="label" >Delivery Time</label>
                <select class="select" name="Delivery_time">
                    <option value="1_week">1 week</option>
                    <option value="2_weeks">2 weeks</option>
                    <option value="1_month" selected>1 month</option>
                    <option value="2_months">2 months</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="label">Cover Letter</label>
            <textarea class="textarea" name="cover_letter" placeholder="Introduce yourself and explain why you're a great fit..."></textarea>
        </div>

        <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($project_id); ?>">
        <button type="submit" name="submit_proposal" class="btn-submit">Submit Proposal</button>
    </form>
</div>

</body>
</html>