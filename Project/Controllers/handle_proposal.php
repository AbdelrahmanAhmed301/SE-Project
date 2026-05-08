<?php
require_once "DBcontrollers.php";
$db = DBcontrollers::getInstance();

$proposal_id = $_GET['id'];
$status = $_GET['status'];

if (isset($proposal_id) && isset($status)) {

    $update_query = "UPDATE proposal SET status = '$status' WHERE proposal_id = '$proposal_id'";
    $db->insertquery($update_query);


    if ($status == 'Accepted') {
        $get_project = $db->Select_query("SELECT project_id FROM proposal WHERE proposal_id = '$proposal_id'");
        $project_id = $get_project[0]['project_id'];
        
        $update_project = "UPDATE projects SET status = 'In Progress' WHERE id = '$project_id'";
        $db->insertquery($update_project);
    }

    header("Location: ../views/Client/client-dashboard.php?msg=StatusUpdated");
}
?>