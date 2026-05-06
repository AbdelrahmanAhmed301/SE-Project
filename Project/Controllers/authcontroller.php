<?php
require_once '../../Models/user.php';
require_once '../../Controllers/DBcontrollers.php';
use Dba\Connection;

class authcontrollers {
    private $db;

    public function __construct() {
        $this->db = new DBcontrollers(); 
    }

    public function login(user $user) {
        if($this->db->openconnection()){
            
            $query = "SELECT * FROM user WHERE email='$user->email' AND password_hash='$user->password_hash'";
            $result = $this->db->Select_query($query);

            if($result===false || count($result) == 0){
                // session_start();
                $_SESSION["errmsg"]="Invalid email or password";
                return false;
            } else {
                $_SESSION["userid"]=$result[0]["user_id"];
                $_SESSION["username"]=$result[0]["username"];
                $_SESSION["email"]=$result[0]["email"];
                $_SESSION["user_roleid"]=$result[0]["role_id"];
                return true;
            }
        }
    }

public function register(user $user){
    if($this->db->openconnection()){
        $query = "INSERT INTO user (username, email, password_hash, role_id)
                VALUES ('$user->username', '$user->email', '$user->password_hash', '$user->role_id')";
        
        $result = $this->db->insertquery($query);

        if($result){
            
            $res = $this->db->Select_query("SELECT user_id FROM user WHERE email='$user->email'");
            $_SESSION["userid"] = $res[0]["user_id"];
            $_SESSION["username"] = $user->username;
            $_SESSION["user_roleid"] = $user->role_id;
            // -------------------------
            return true;
        }
    }
    return false;
}

}
?>
