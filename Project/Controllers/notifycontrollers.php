<?php
require_once '../../Models/notification.php';
require_once "../../Controllers/DBcontrollers.php";

class notifycontrollers{
    private $db;

    public function __construct() {
        $this->db = new DBcontrollers(); 
    }

// تعديل في ملف notifycontrollers.php
public function notify_all_freelancers(notification $notifiy) {
    if (!$this->db->openconnection()) {
        return false;
    }
    $query="SELECT user_id FROM user WHERE role_id = 3";
    $freelancers = $this->db->Select_query($query);

    if ($freelancers) {
        foreach ($freelancers as $freelancer) {
            $user_id = $freelancer['user_id'];
            $created_at = date('Y-m-d H:i:s');

            $query = "INSERT INTO notification (user_id, title, msg, create_at) 
                    VALUES ('$notifiy->user_id', '$user_id', '$notifiy->msg', '$created_at')";
            $this->db->insertquery($query);
        }
        return true;
    }
    return false;
}
}

?>