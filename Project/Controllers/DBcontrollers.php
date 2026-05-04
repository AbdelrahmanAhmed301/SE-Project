<?php

use Dba\Connection;

class DBcontrollers {

public $db_host="localhost" ;
public $db_user="root";
public $db_password="";
public $db_name="freelancer_platform";
public $db_port = 3307;
public $connection;




public function openconnection(){
    $this->connection=new mysqli($this->db_host,$this->db_user,$this->db_password,$this->db_name,$this->db_port);

    if($this->connection->connect_errno){
        echo 'error in connection';
    }
    else{
        return true;
    }


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