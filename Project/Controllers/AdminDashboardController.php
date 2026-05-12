<?php
require_once __DIR__ . "/DBcontrollers.php";

class AdminDashboardController {
    private $db;
    public function __construct() {
        $this->db = DBcontrollers::getInstance();
    }

    public function getAllProjects() {
        $sql = "SELECT project_id, title, status, budget FROM Projects";
        $result= $this->db->Select_query($sql);
        if($result){
            return $result;
        }
        else{
            return false;
        }
    }
    public function get_all_users() {
        $sql = "SELECT * FROM user ";
        $result= $this->db->Select_query($sql);
        if($result){
            return $result;
        }
        else{
            return false;
        }
    }

public function get_num_users() {
    $sql = "SELECT count(*) FROM user";
    $result = $this->db->Select_query($sql);
    
    if ($result && isset($result[0])) {
        return array_values($result[0])[0];
    }
    return 0;
}

public function get_num_project() {
    $sql = "SELECT count(*) FROM projects";
    $result = $this->db->Select_query($sql);
    
    if ($result && isset($result[0])) {
        return array_values($result[0])[0];
    }
    return 0;
}

public function get_specify_user(){
    $sql = "SELECT 
            COUNT(CASE WHEN role_id = 1 THEN 1 END) AS admin_count,
            COUNT(CASE WHEN role_id = 2 THEN 1 END) AS client_count,
            COUNT(CASE WHEN role_id = 3 THEN 1 END) AS freelancer_count
        FROM user";

        $result = $this->db->Select_query($sql);
        if($result){
            return $result[0];

        }
        else{
            return 0;
        }
        
}

public function deleteUser($user_id) {

    $sql = "DELETE FROM user WHERE user_id = '$user_id'";
    return $this->db->insertquery($sql); 
}

// Add this method inside AdminDashboardController class
public function getRecentProjects($limit = 5) {
    $sql = "SELECT p.*, u.username as client_name 
            FROM projects p 
            JOIN user u ON p.client_id = u.user_id 
            ORDER BY p.project_id DESC LIMIT $limit";
    return $this->db->Select_query($sql);
}

public function getTotalBudget() {
    $sql = "SELECT SUM(budget) as total FROM projects";
    $result = $this->db->Select_query($sql);
    return ($result && $result[0]['total']) ? $result[0]['total'] : 0;
}

public function getSystemLogs($limit = 5) {
    $sql = "SELECT 'User' as type, username as detail, 'Recently' as time FROM user 
            UNION 
            SELECT 'Project' as type, title as detail, 'Recently' as time FROM projects 
            ORDER BY detail DESC LIMIT $limit"; 
    
    return $this->db->Select_query($sql);
}

public function getProposalsStats() {
    $sql = "SELECT 
            COUNT(*) as total_proposals,
            COUNT(CASE WHEN status = 'Accepted' THEN 1 END) as accepted_count,
            COUNT(CASE WHEN status = 'Pending' THEN 1 END) as pending_count,
            COUNT(CASE WHEN status = 'Rejected' THEN 1 END) as rejected_count
            FROM proposal";
    $result = $this->db->Select_query($sql);
    return ($result) ? $result[0] : ['total_proposals'=>0, 'accepted_count'=>0, 'pending_count'=>0, 'rejected_count'=>0];
}

public function getRecentProposals($limit = 5) {
    $sql = "SELECT p.*, u.username as freelancer_name, pr.title as project_title 
            FROM proposal p
            JOIN user u ON p.freelancer_id = u.user_id
            JOIN projects pr ON p.project_id = pr.project_id
            ORDER BY p.proposal_id DESC LIMIT $limit";
            
    return $this->db->Select_query($sql);
}

public function sanctionUser($userId, $status) {

    $sql = "UPDATE user SET account_status = '$status' WHERE user_id = '$userId'";
    return $this->db->insertquery($sql);
}

public function assignArbitrator($disputeId, $adminId) {
    $sql = "UPDATE disputes SET arbitrator_id = '$adminId', status = 'In-Review' WHERE id = '$disputeId'";
    return $this->db->insertquery($sql);
}

public function executeVerdict($disputeId, $f_percent, $c_percent) {
    if (($f_percent + $c_percent) != 100) return false; 
    
    $sql = "UPDATE disputes 
            SET freelancer_share = '$f_percent', client_share = '$c_percent', status = 'Resolved' 
            WHERE id = '$disputeId'";
    return $this->db->insertquery($sql);
}

}