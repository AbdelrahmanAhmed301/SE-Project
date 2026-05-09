<?php
require_once __DIR__ . '/../Models/project.php';
require_once __DIR__ . '/../Models/contract.php';
require_once __DIR__ . '/DBcontrollers.php';

class createcontract {
    private $db;

    public function __construct() {
        $this->db = DBcontrollers::getInstance();
    }

    public function create_contract(contract $contract) {
        
        $query = "INSERT INTO contract (project_id, freelancer_id, deadline, revision_limits, revision_used, create_at)
                VALUES ('$contract->project_id', '$contract->freelancer_id', '$contract->deadline', 
                        '$contract->revision_limits','$contract->revision_used', '$contract->create_at')";
        
        $result = $this->db->insertquery($query);
        
        return $result ? mysqli_insert_id($this->db->get_connection()) : false;
    }

    public function get_contract_details($contract_id) {
    
    $query = "SELECT c.*, p.title as project_name, 
            pr.bid_amount as budget, 
            u_f.username as freelancer_name, u_c.username as client_name
            FROM contract c
            JOIN projects p ON c.project_id = p.project_id
            JOIN proposal pr ON c.project_id = pr.project_id AND c.freelancer_id = pr.freelancer_id
            JOIN user u_f ON c.freelancer_id = u_f.user_id
            JOIN user u_c ON p.client_id = u_c.user_id
            WHERE c.contract_id = '$contract_id' AND pr.status = 'Accepted' LIMIT 1";

    return $this->db->Select_query($query);
}
}