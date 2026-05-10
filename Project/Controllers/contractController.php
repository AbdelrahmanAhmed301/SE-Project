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
// أضف هذه الميثودز داخل كلاس createcontract في ملف contractController.php

// الكود القديم:
// $query = "SELECT * FROM deliveries WHERE project_id = '$project_id' ORDER BY create_at DESC";

// الكود الجديد (بدون ترتيب):
public function get_deliveries($project_id) {
    $query = "SELECT * FROM deliveries WHERE project_id = '$project_id'";
    return $this->db->Select_query($query);
}

public function update_revision_status($contract_id, $used_revisions) {
    // زيادة عدد الريفيجن المستخدمة
    $query = "UPDATE contract SET revision_used = '$used_revisions' WHERE contract_id = '$contract_id'";
    return $this->db->insertquery($query); // بفرض إن insertquery بتنفذ أي Update/Delete برضه
}
}