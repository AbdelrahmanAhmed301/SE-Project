<?php

class user {

public $userid ;
public $username;
public $email;
public $password_hash;
public $role_id;













public static function isBanned($userId, $db) {
    $sql = "SELECT account_status FROM user WHERE user_id = '$userId'";
    $result = $db->Select_query($sql);
    if ($result && isset($result[0])) {
        return ($result[0]['account_status'] === 'Banned');
    }
    return false;
}




}


?>