<?php
require_once '../../Models/notification.php';
require_once "../../Controllers/DBcontrollers.php";

class notifycontrollers {
    private $db;

    public function __construct() {
        $this->db = new DBcontrollers();
    }

    public function notify_all_freelancers(notification $notifiy) {
        if (!$this->db->openconnection()) {
            return false;
        }

        // 1. جلب كل الـ user_id للمستقلين
        $query = "SELECT user_id FROM user WHERE role_id = 3";
        $freelancers = $this->db->Select_query($query);

        if ($freelancers) {
            foreach ($freelancers as $freelancer) {
                $user_id = $freelancer['user_id'];
                $created_at = date('Y-m-d H:i:s');

                // 2. التحقق من عدم وجود إشعار متطابق تماماً أرسل مؤخراً لمنع التكرار (Anti-Duplication Check)
                // نتحقق من العنوان والرسالة والمستخدم خلال آخر دقيقة مثلاً
                $checkQuery = "SELECT id FROM notification 
                            WHERE user_id = '$user_id' 
                            AND title = '$notifiy->title' 
                            AND msg = '$notifiy->msg' 
                            AND create_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)";
                
                $exists = $this->db->Select_query($checkQuery);

                if (!$exists) {
                    // 3. الإرسال فقط إذا لم يكن الإشعار موجوداً بالفعل
                    $insertQuery = "INSERT INTO notification (user_id, title, msg, create_at) 
                                    VALUES ('$user_id', '$notifiy->title', '$notifiy->msg', '$created_at')";
                    
                    $this->db->insertquery($insertQuery);
                }
            }
            return true;
        }
        
        return false;
    }
}
?>