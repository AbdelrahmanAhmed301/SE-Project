<?php
require_once '../../Models/notification.php';
require_once "../../Controllers/DBcontrollers.php";

class notifycontrollers{
    private $db;

    public function __construct() {
        $this->db = new DBcontrollers(); 
    }

public function notify_all_freelancers(notification $notifiy) {
    if (!$this->db->openconnection()) {
        return false;
    }

    
        $query = "INSERT INTO notification (user_id, title, msg, create_at) 
                VALUES ('$notifiy->user_id', 
                        '{$notifiy->title}', 
                        '{$notifiy->msg}', 
                        '{$notifiy->create_at}')";
        
        $result = $this->db->insertquery($query);
        if($result){
            return true;
        }
        else{
            return false;
        }


}
}

?>