<?php

require_once "DBcontrollers.php";

class notifycontrollers {
    private $db;

    public function __construct() {
        $this->db = DBcontrollers::getInstance();
    }

    public function notify_all_freelancers(notification $notifiy) {
        $query = "SELECT user_id FROM user WHERE role_id = 3";
        $freelancers = $this->db->Select_query($query);

        if ($freelancers) {
            foreach ($freelancers as $freelancer) {
                $user_id = $freelancer['user_id'];

                $checkQuery = "SELECT id FROM notification 
                            WHERE user_id = '$user_id' 
                            AND title = '" . addslashes($notifiy->title) . "' 
                            AND msg = '" . addslashes($notifiy->msg) . "' 
                            AND create_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)";
                
                $exists = $this->db->Select_query($checkQuery);
                if (!$exists) {
                    $insertQuery = "INSERT INTO notification (user_id, title, msg, create_at) 
                    VALUES ('$user_id', '" . addslashes($notifiy->title) . "', '" . addslashes($notifiy->msg) . "', NOW())";
                    $this->db->insertquery($insertQuery);
                }
            }
            return true;
        }
        return false;
    }

    public function get_user_notifications($user_id) {
        $query = "SELECT * FROM notification WHERE user_id = '$user_id'  create_at ";
        return $this->db->Select_query($query);
    }
    public function add_notification($user_id, $msg) {
        
        $query = "INSERT INTO notification (user_id, msg) 
                VALUES ('$user_id', '$msg')";
        
        return $this->db->insertquery($query);
    }

}
?>