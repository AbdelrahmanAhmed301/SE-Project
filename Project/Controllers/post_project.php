<?php
require_once '../../Models/project.php';
require_once "../../Controllers/DBcontrollers.php";
class post_project{
    private $db;

    public function __construct() {
        $this->db = new DBcontrollers(); 
    }

    public function post_project(project $project){
        if($this->db->openconnection()){
            
            $query = "INSERT INTO projects (title, description, milestones, budget, client_id)
            VALUES ('$project->title', '$project->description', '$project->milestones', '$project->budget', '$project->client_id')";
            $result = $this->db->insertquery($query);


        if($result){
            return true;
        } else {
            return false; 
        }

        }
    



}

public function get_total_spent($client_id){
    if($this->db->openconnection()){
        
        $qry = "SELECT SUM(budget) as total 
                FROM projects 
                WHERE client_id = '$client_id'";
                
        $result = $this->db->Select_query($qry);

        return $result[0]['total'] ?? 0;
    }
}
}
?>