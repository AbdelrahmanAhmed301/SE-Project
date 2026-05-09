<?php
require_once 'controllers/DBcontrollers.php';

class ProjectModel {
    private $db;

    public function __construct() {
        $this->db = DBcontrollers::getInstance();
    }

    public function getAllProjects() {
        $sql = "SELECT project_id, title, status, budget FROM Projects";
        return $this->db->Select_query($sql);
    }

    public function updateProjectStatus($id, $status) {
        $sql = "UPDATE Projects SET status = '$status' WHERE project_id = $id";
        return $this->db->insertquery($sql);
    }
}