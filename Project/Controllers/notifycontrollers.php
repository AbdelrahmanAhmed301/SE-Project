<?php
require_once '../../Models/notification.php';
require_once "../../Controllers/DBcontrollers.php";

class notifycontrollers{
    private $db;

    public function __construct() {
        $this->db = new DBcontrollers(); 
    }

// تعديل في ملف notifycontrollers.php
public function notify_all_freelancers($title, $msg) {
    if (!$this->db->openconnection()) {
        return false;
    }

    $freelancers = $this->db->Select_query("SELECT user_id FROM user WHERE role = 'freelancer'");

    if ($freelancers) {
        foreach ($freelancers as $freelancer) {
            $user_id = $freelancer['user_id'];
            $created_at = date('Y-m-d H:i:s');

            $query = "INSERT INTO notification (user_id, title, msg, create_at) 
                    VALUES ('$user_id', '$title', '$msg', '$created_at')";
            $this->db->insertquery($query);
        }
        return true;
    }
    return false;
}
}

?>