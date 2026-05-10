<?php
session_start(); 
require_once "../../Controllers/contractController.php";

// 1. التأكد من تسجيل الدخول
if (!isset($_SESSION["userid"])) {
    header("Location: ../../views/Auth/login.php"); 
    exit();
}

$contract_id = $_GET['contract_id'] ?? die("Error: Contract ID undefined");
$manager = new createcontract();
$result = $manager->get_contract_details($contract_id);

if (!$result || count($result) == 0) {
    die("Error: Contract not found");
}

$contract_data = $result[0];
$project_id = $contract_data['project_id'];
$user_role = $_SESSION['user_roleid']; // هنا بنعرف هو مين
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contract Details</title>
    <link rel="stylesheet" href="/Project/public/assets/css/client-dashboard.css">
    <style>
        .contract-card { border: 1px solid #ddd; padding: 20px; border-radius: 8px; max-width: 800px; margin: 20px auto; }
        .section-box { background: #f9f9f9; padding: 15px; border-radius: 5px; margin-top: 15px; }
        .btn-success { background: #28a745; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 4px; }
        .btn-warning { background: #ffc107; color: black; padding: 10px 20px; border: none; cursor: pointer; border-radius: 4px; }
        .btn-primary { background: #007bff; color: white; padding: 10px 20px; text-decoration: none; display: inline-block; border-radius: 4px; }
    </style>
</head>
<body>

<div class="contract-card">
    <h1>Project: <?php echo htmlspecialchars($contract_data['project_name']); ?></h1>
    
    <div class="info-section">
        <p><strong>Freelancer:</strong> <?php echo htmlspecialchars($contract_data['freelancer_name']); ?></p>
        <p><strong>Client:</strong> <?php echo htmlspecialchars($contract_data['client_name']); ?></p>
        <p><strong>Deadline:</strong> <?php echo htmlspecialchars($contract_data['deadline']); ?></p>
        <p><strong>Budget:</strong> $<?php echo htmlspecialchars($contract_data['budget']); ?></p>
        <p><strong>Revisions:</strong> <?php echo $contract_data['revision_used']; ?> / <?php echo $contract_data['revision_limits']; ?></p>
    </div>

    <?php if ($user_role != 2): ?> 
        <div class="section-box">
            <h3>Freelancer Tools</h3>
            <p>You can submit your work using the button below:</p>
            <a href="../../views/submit_work/submit_work.php?project_id=<?php echo $project_id; ?>" class="btn-primary">
                Submit Work
            </a>
        </div>
    <?php endif; ?>

    <?php if ($user_role == 2): ?>
        <div class="section-box">
            <h3>Client Actions</h3>
            <p>Review the submitted work then take an action:</p>
            
            <form action="../contract_details/handel_contractcontroller.php" method="POST">
                <input type="hidden" name="contract_id" value="<?php echo $contract_id; ?>">
                
                <button type="submit" name="action" value="approve" class="btn-success">
                    Approve Work & Finish
                </button>

                <?php if ($contract_data['revision_used'] < $contract_data['revision_limits']): ?>
                    <button type="submit" name="action" value="revise" class="btn-warning">
                        Request Revision
                    </button>
                <?php else: ?>
                    <p style="color: red; margin-top: 10px;">Revision limit reached.</p>
                <?php endif; ?>
            </form>
        </div>
    <?php endif; ?>

</div>

</body>
</html>