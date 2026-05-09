<?php

use Dba\Connection;

class DBcontrollers {
    private static $instance = null; 
    public $connection;

    private function __construct() {
        $this->openconnection();
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new DBcontrollers();
        }
        return self::$instance;
    }

    public function openconnection(){
        $this->connection = new mysqli("localhost", "root", "", "freelancer_platform", 3307);
        return $this->connection;
    }
    public function get_connection() {
    return $this->connection;
}


public function Select_query($qry){
    $result=$this->connection->query($qry);

    if($result){
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    else{
        echo "error in exection select ";
        return false;
    }
}
public function insertquery($qry){
    $result = $this->connection->query($qry);

    if($result){
        return true; 
    } else {
        echo "Error in execution insert";
        return false;
    }
}







}


?>