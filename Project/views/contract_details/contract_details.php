<?php
session_start(); 

require_once "../../Models/user.php";
require_once "../../Models/project.php";
require_once "../../Controllers/contractController.php";
require_once "../../Models/contract.php";
require_once "../../Models/project.php";

if (!isset($_SESSION["userid"]) || empty($_SESSION["userid"])) {
    header("Location: ../../views/Auth/login.php"); 
    exit();
}

if (isset($_GET['contract_id']) && !empty($_GET['contract_id'])) {
    $contract_id = $_GET['contract_id']; 
    $manager = new createcontract();
    $result = $manager->get_contract_details($contract_id);

    if ($result && count($result) > 0) {
        $contract_data = $result[0]; 

        $project = $contract_data['project_id']; 

        
    } else {
        die("contract not exist in database");
    }
} else {
    
    die("contract_id undefined");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contract_details</title>
    <link rel="stylesheet" href="/Project/public/assets/css/client-dashboard.css">
</head>
<>
    <div class="contract-card">
    <h1> project_name: <?php echo $contract_data['project_name']; ?></h1>
    
    <div class="info-section">
        <p><strong>freelancer:</strong> <?php echo $contract_data['freelancer_name']; ?></p>
        <p><strong>client</strong> <?php echo $contract_data['client_name']; ?></p>
        <p><strong>deadline</strong> <?php echo $contract_data['deadline']; ?></p>
        <p><strong>budget</strong> <?php echo $contract_data['budget']; ?></p>
        <p><strong>revision_limits </strong> <?php echo $contract_data['revision_limits']?></p>
        <p><strong>revision_used </strong> <?php echo $contract_data['revision_used']?></p>
    </div>

    <div class="actions">
    <!-- 
        <a href="../../Controllers/update_status.php?id=<?php echo $contract_id; ?>&action=approve" class="btn-success">Approve Work</a>
        <a href="../../Controllers/update_status.php?id=<?php echo $contract_id; ?>&action=revise" class="btn-warning">Revision Request</a>
     -->

    <?php if (isset($_SESSION['user_roleid']) && $_SESSION['user_roleid'] == 3): ?>
        <a href="../submit_work/submit_work.php?project_id=<?php echo $project; ?>" class="btn-primary">
            Submit Work
        </a>
    <?php endif; ?>
</div>
</div>
                </div>

</body>
</html>