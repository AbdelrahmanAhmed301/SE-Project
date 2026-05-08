<?php
require_once "DBcontrollers.php";
$db = DBcontrollers::getInstance();

$proposal_id = $_GET['id'];
$status = $_GET['status'];

if (isset($proposal_id) && isset($status)) {

    $update_query = "UPDATE proposal SET status = '$status' WHERE proposal_id = '$proposal_id'";
    $db->insertquery($update_query);


    

if ($status == 'Accepted') {
    
    $proposal_data = $db->Select_query("SELECT * FROM proposal WHERE proposal_id = '$proposal_id'");
    $project_id = $proposal_data[0]['project_id'];
    $freelancer_id = $proposal_data[0]['freelancer_id'];


    $db->insertquery("UPDATE projects SET status = 'In Progress' WHERE project_id = '$project_id'");

    require_once "../Models/contract.php";
    require_once "contractController.php";

    $contract = new contract();
    $contract->project_id = $project_id;
    $contract->freelancer_id = $freelancer_id;
    $contract->deadline = $proposal_data[0]['Delivery_time']; 
    $contract->revision_limts = 3; 
    $contract->revision_used = 0;
    $contract->create_at = date("Y-m-d H:i:s");

    $contract_manager = new createcontract();
    $contract_manager->create_contract($contract);
}
    header("Location: ../views/Client/client-dashboard.php?msg=StatusUpdated");
}
?>