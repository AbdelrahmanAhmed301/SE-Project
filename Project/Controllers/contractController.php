<?php
require_once '../../Models/project.php';
require_once '../../Models/contract.php';
require_once "../../Controllers/DBcontrollers.php";
class createcontract{
    private $db;

    public function create_contract(contract $contract){
        if($this->db = DBcontrollers::getInstance()){
            
            $query = "INSERT INTO contract (project_id, freelancer_id, deadline, revision_limts, revision_used, create_at)
            VALUES ('$contract->project_id', '$contract->freelancer_id', '$contract->deadline', '$contract->revision_limts', '$contract->revision_used', '$contract->create_at')";
            $result = $this->db->insertquery($query);



        if(!$result){
            die("MySQL Error: " . mysqli_error($this->db->get_connection()));
        } else {
            return $result; 
        }

        }
    



}

public function get_contract_details($contract_id) {
    $this->db = DBcontrollers::getInstance();
    $query = "SELECT c.*, p.title as project_name, p.budget,
            u_f.username as freelancer_name, u_c.username as client_name
            FROM contract c
            JOIN projects p ON c.project_id = p.project_id
            JOIN user u_f ON c.freelancer_id = u_f.user_id
            JOIN user u_c ON p.client_id = u_c.user_id
            WHERE c.contract_id = '$contract_id'";
    
    $result = $this->db->Select_query($query);

    if(!$result){
            die("MySQL Error: " . mysqli_error($this->db->get_connection()));
        } else {
            return $result; 
        }
}
}
?>