<?php
require_once 'controllers/DBcontrollers.php';

class AdminModel {
    private $db;

    public function __construct() {
        $this->db = DBcontrollers::getInstance();
    }

    public function getSystemStats() {
        $sql = "SELECT 
                (SELECT COUNT(*) FROM Projects) as total_projects,
                (SELECT COUNT(*) FROM User) as total_users";
        
        $result = $this->db->Select_query($sql);

        return $result ? $result[0] : ['total_projects' => 0, 'total_users' => 0];
    }

    public function deleteUser($user_id) {
        $sql = "DELETE FROM User WHERE user_id = $user_id";
    
        return $this->db->insertquery($sql); 
    }
}