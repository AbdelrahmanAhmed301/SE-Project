<?php
// handle_proposal.php

require_once "DBcontrollers.php";
require_once "contractController.php";
require_once "../Models/contract.php";

$db = DBcontrollers::getInstance();

$proposal_id = $_GET['id'] ?? null;
$status = $_GET['status'] ?? null;

if ($proposal_id && $status) {


    $update_query = "UPDATE proposal SET status = '$status' WHERE proposal_id = '$proposal_id'";
    $db->insertquery($update_query);

    
    if ($status == 'Accepted') {
    
        $proposal_data = $db->Select_query("SELECT * FROM proposal WHERE proposal_id = '$proposal_id'");
        
        if ($proposal_data) {
            $project_id = $proposal_data[0]['project_id'];
            $freelancer_id = $proposal_data[0]['freelancer_id'];
            $raw_delivery_time = $proposal_data[0]['Delivery_time']; 

        
            $db->insertquery("UPDATE projects SET status = 'In Progress' WHERE project_id = '$project_id'");

    
            $contract = new contract();
            $contract->project_id = $project_id;
            $contract->freelancer_id = $freelancer_id;

        
            $clean_time = str_replace('_', ' ', $raw_delivery_time);
            $contract->deadline = date('Y-m-d', strtotime($clean_time));

            $contract->revision_limits = 3; 
            $contract->revision_used = 0;
            $contract->create_at = date("Y-m-d H:i:s");

        
            $contract_manager = new createcontract();
            $new_id = $contract_manager->create_contract($contract);

        
            if ($new_id) {
                header("Location: ../views/contract_details/contract_details.php?contract_id=$new_id");
                exit();
            }
        }
    }


    header("Location: ../views/proposal_request/proposal_request.php?msg=StatusUpdated");
    exit();
} 
?>