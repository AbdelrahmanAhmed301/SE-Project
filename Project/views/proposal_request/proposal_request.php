<?php
session_start();

require_once "../../Controllers/DBcontrollers.php";
require_once "../../Controllers/post_project.php";

require_once "../../Models/user.php"; 
$db = DBcontrollers::getInstance(); 
$current_user_id = $_SESSION["userid"]; 
$proposals = $db->Select_query("
    SELECT 
        p.*, 
        pr.title as project_title, 
        u.username as freelancer_name 
    FROM proposal p
    JOIN projects pr ON p.project_id = pr.project_id
    JOIN user u ON p.freelancer_id = u.user_id
    WHERE pr.client_id = '$current_user_id' 
    AND (p.status = 'Pending' OR p.status IS NULL OR p.status = '')
");






?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../public/assets/css/proposal_request.css">
    <title>Proposals</title>
</head>
<body>
    <section class="proposals-section">
    <h3>Incoming Proposals</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Project</th>
                <th>Freelancer</th>
                <th>Bid Amount</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($proposals as $row): ?>
            <tr>
                <td><?php echo $row['project_title']; ?></td>
                <td><?php echo $row['freelancer_name']; ?></td>
                <td>$<?php echo $row['bid_amount']; ?></td>
                <td>
                    <a href="../../Controllers/handle_proposal.php?id=<?php echo $row['proposal_id']; ?>&status=Accepted" class="btn btn-success btn-sm">Accept</a>
                    <a href="../../Controllers/handle_proposal.php?id=<?php echo $row['proposal_id']; ?>&status=Rejected" class="btn btn-danger btn-sm">Reject</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
    
</body>
</html>
