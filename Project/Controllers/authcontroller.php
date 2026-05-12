<?php
require_once '../../Models/user.php';
require_once '../../Controllers/DBcontrollers.php';
use Dba\Connection;

class authcontrollers {
    private $db;

    public function __construct() {
        $this->db = DBcontrollers::getInstance(); 
    }

    public function login(user $user) {
    if($this->db = DBcontrollers::getInstance()){
        $query = "SELECT * FROM user WHERE email='$user->email' LIMIT 1";
        $result = $this->db->Select_query($query);

        if($result && count($result) > 0){
            $user_data = $result[0];
            if(password_verify($user->password_hash, $user_data['password_hash'])) {
                $_SESSION["userid"]=$user_data["user_id"];
                $_SESSION["username"]=$user_data["username"];
                $_SESSION["email"]=$user_data["email"];
                $_SESSION["user_roleid"]=$user_data["role_id"];
                return true;
            }
        }
        
        $_SESSION["errmsg"]="Invalid email or password";
        return false;
    }
}
public function register(user $user){
    if($this->db = DBcontrollers::getInstance()){
        $query = "INSERT INTO user (username, email, password_hash, role_id)
                VALUES ('$user->username', '$user->email', '$user->password_hash', '$user->role_id')";
        
        $result = $this->db->insertquery($query);

        if($result){
            
            $res = $this->db->Select_query("SELECT user_id FROM user WHERE email='$user->email'");
            $_SESSION["userid"] = $res[0]["user_id"];
            $_SESSION["username"] = $user->username;
            $_SESSION["user_roleid"] = $user->role_id;
        
            return true;
        }
    }
    return false;
}

}
?>
