<?php
session_start();
require_once __DIR__ . "/contractController.php";
require_once __DIR__ . "/notifycontrollers.php";

$db = DBcontrollers::getInstance();
$notify = new notifycontrollers();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {

    $contract_id = $_POST['contract_id'];
    $action = $_POST['action'];

    $contract_data = $db->Select_query("SELECT * FROM contract WHERE contract_id = '$contract_id'");
    
    if (empty($contract_data)) {
        die("Contract not found");
    }

    $contract_info = $contract_data[0];
    $freelancer_id = $contract_info['freelancer_id'];
    $project_id = $contract_info['project_id'];

    if ($action == 'approve') {

        $db->insertquery("UPDATE projects SET status = 'Completed' WHERE project_id = '$project_id'");

        $db->insertquery("UPDATE contract SET status = 'Completed' WHERE contract_id = '$contract_id'");

        $msg = "client approve work sucessfully  $project_id.";
        $notify->add_notification($freelancer_id, $msg);

        header("Location: ../views/contract_details/contract_details.php?contract_id=$contract_id&status=completed");
        exit();
    } 

    elseif ($action == 'revise') {
        if ($contract_info['revision_used'] < $contract_info['revision_limits']) {

            $db->insertquery("UPDATE contract SET revision_used = revision_used + 1 WHERE contract_id = '$contract_id'");

            $db->insertquery("UPDATE projects SET status = 'Revision Requested' WHERE project_id = '$project_id'");

            $msg = " client request change some thing in prolect $project_id.";
            $notify->add_notification($freelancer_id, $msg);

            header("Location: ../views/contract_details/contract_details.php?contract_id=$contract_id&status=revision_sent");
            exit();
        } else {
            die("reached to limt");
        }
    }
} else {

    header("Location: ../views/Client/client-dashboard.php");
    exit();
}