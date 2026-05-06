<?php
session_start();
require_once "../../Controllers/mark_read.php";

if (isset($_SESSION["userid"])) {
    $db = new DBcontrollers();
    $db->openconnection();
    $user_id = $_SESSION["userid"];
    
    $query = "UPDATE notifications SET is_read = 1 WHERE user_id = '$user_id' AND is_read = 0";
    $db->insertquery($query); 
}
?>