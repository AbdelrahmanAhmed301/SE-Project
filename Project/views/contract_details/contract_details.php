<?php
session_start(); 
require_once "../../Controllers/contractController.php";
$db = DBcontrollers::getInstance();
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

$deliveries = $manager->get_deliveries($project_id);

$project_info = $db->Select_query("SELECT status FROM projects WHERE project_id = '$project_id'");
$project_status = $project_info[0]['status'] ?? '';
$user_role = $_SESSION['user_roleid'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Contract Details</title>

    <link rel="stylesheet" href="/Project/public/assets/css/client-dashboard.css">
    <link rel="stylesheet" href="/Project/public/assets/css/contract-details.css">
</head>

<body>

<div class="contract-card">

    <h1 class="project-title">
        Project: <?php echo htmlspecialchars($contract_data['project_name']); ?>
    </h1>

    <div class="info-section">
        <p><strong>Freelancer:</strong>
            <?php echo htmlspecialchars($contract_data['freelancer_name']); ?>
        </p>

        <p><strong>Client:</strong>
            <?php echo htmlspecialchars($contract_data['client_name']); ?>
        </p>

        <p><strong>Deadline:</strong>
            <?php echo htmlspecialchars($contract_data['deadline']); ?>
        </p>

        <p><strong>Budget:</strong>
            $<?php echo htmlspecialchars($contract_data['budget']); ?>
        </p>

        <p><strong>Revisions:</strong>
            <?php echo $contract_data['revision_used']; ?> /
            <?php echo $contract_data['revision_limits']; ?>
        </p>
    </div>

    <?php if ($user_role != 2): ?>

        <div class="section-box">
            <h3>Freelancer Tools</h3>
            <p>You can submit your work using the button below:</p>
            <a href="../../views/submit_work/submit_work.php?project_id=<?php echo $project_id; ?>"
               class="btn btn-primary">
                Submit Work
            </a>
        </div>
    <?php endif; ?>
    <?php if ($user_role == 2): ?>

        <div class="section-box">

            <h3>Project Deliveries</h3>

            <?php if (!empty($deliveries)): ?>

                <table class="delivery-table">
                    <thead>
                        <tr>
                            <th>Message</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($deliveries as $file): ?>

                        <tr>
                            <td>
                                <?php echo htmlspecialchars($file['message']); ?>
                            </td> <td>
                                <a href="../../upload/deliveries/<?php echo $file['file_path']; ?>"
                                download class="btn btn-primary small-btn"> Download File </a>
                                </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No files submitted yet.</p>

            <?php endif; ?>

        </div>



        <div class="section-box review-box">

            <h3>Review Actions</h3>

            <p>
                After reviewing the files above, please take an action:
            </p>
            <?php if ($project_status != 'Completed'): ?>
                <?php endif; ?>

            <form action="../../Controllers/handel_contractcontroller.php" method="POST">
                class="review-form">

                <input type="hidden" name="contract_id" value="<?php echo $contract_id; ?>">

                <button type="submit" name="action" value="approve"
                        class="btn btn-success">

                    Approve & Complete Project
                </button>


                <?php if ($contract_data['revision_used'] < $contract_data['revision_limits']): ?>

                    <button type="submit"
                            name="action"
                            value="revise"
                            class="btn btn-warning">

                        Request Revision
                        (<?php echo $contract_data['revision_used']; ?>/<?php echo $contract_data['revision_limits']; ?>)

                    </button>

                <?php else: ?>

                    <span class="revision-limit">
                        Revision limit reached
                    </span>
                <?php endif; ?>
            </form>

        </div>

    <?php endif; ?>

</div>

</body>
</html>