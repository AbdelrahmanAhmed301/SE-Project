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

}