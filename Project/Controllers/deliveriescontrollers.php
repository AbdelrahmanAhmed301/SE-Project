<?php

require_once "DBcontrollers.php";


class deliveriescontrollers {

    private $db;

    public function __construct() {
        $this->db = DBcontrollers::getInstance();
    }
    public function someAction($userId) {
        $db = DBcontrollers::getInstance();
        if (user::isBanned($userId, $db)) {
                die ("Error: Your account is restricted.");
        exit();
    }
}

    public function submit_work(deliveries $deliveries) {

        $query = "
        INSERT INTO deliveries
        (project_id, freelancer_id, message, file_path)

        VALUES
        (
            '$deliveries->project_id',
            '$deliveries->freelancer_id',
            '$deliveries->message',
            '$deliveries->file_path'
        )
        ";

        $result = $this->db->insertquery($query);

        if ($result) {
            return true;
        }

        return false;
    }

}
?>