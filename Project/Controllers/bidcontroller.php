<?php
require_once '../../Models/project.php';
require_once "../../Controllers/DBcontrollers.php";

class bidcontroller{
    private $db;

    public function __construct() {
        $this->db = DBcontrollers::getInstance();
    }

    public function display_proposal(proposal $proposal){
        
            
            $query = "INSERT INTO proposal (bid_amount, Delivery_time, freelancer_id, cover_letter, project_id)
            VALUES ('$proposal->bid_amount', '$proposal->Delivery_time', '$proposal->freelancer_id', '$proposal->cover_letter', '$proposal->project_id')";
            $result = $this->db->insertquery($query);


        if(!$result){
            die("MySQL Error: " . mysqli_error($this->db->get_connection()));
        } else {
            return $result; 
        }

                }
    



}

?>