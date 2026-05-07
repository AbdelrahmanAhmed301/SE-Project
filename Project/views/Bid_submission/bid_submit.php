<?php
session_start(); 

require_once "../../Models/user.php";
require_once "../../Models/project.php";
require_once "../../Models/notification.php"; 
require_once "../../Controllers/post_project.php";
require_once "../../Controllers/notifycontrollers.php";


if (!isset($_SESSION["userid"]) || empty($_SESSION["userid"])) {
    header("Location: ../../views/Auth/login.php"); 
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["Submit Proposal"])) {
    if (!empty($_POST["bid_amount"]) && !empty($_POST["bid_amount"]) && !empty($_POST['delivery_time'])) {
        $db = DBcontrollers::getInstance(); 
        $proposal = new proposal();
        $proposal->bid_amount=$_POST['bid_amount'];
        $proposal->cover_letter = $_POST["cover_letter"];
        $proposal->freelancer_id = $_SESSION["userid"];






        
            
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
    <form action="../../views/Bid_submission/bid_submit.php" method="post">
        <h2>Submit Your Proposal</h2>
        
        <div class="field">
            <div class="form-group">
                <label class="label">Job Title</label>
                <input class="input" name="job_title" type="text" value="Web Development Project" readonly>
            </div>
        </div>

        <div class="field-row">
            <div class="form-group">
                <label class="label">Your Bid ($)</label>
                <input class="input" name="bid_amount"  type="number" placeholder="e.g. 3500" required>
            </div>
            
            <div class="form-group">
                <label class="label">Delivery Time</label>
                <select class="select" name="delivery_time">
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

        <button type="submit" class="btn-submit" id="Submit Proposal">Submit Proposal</button>
    </form>
</div>

</body>
</html>