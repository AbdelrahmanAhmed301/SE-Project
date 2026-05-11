<?php
session_start();

require_once "../../Models/user.php";

require_once "../../Controllers/authcontroller.php";

$err_msg="";

if(isset($_POST['email']) && isset($_POST['password'])){
    if(!empty($_POST['email']) && !empty($_POST['password'])){

        $user = new user();
        $auth = new authcontrollers();

        $user->email = $_POST['email'];
        $user->password_hash = $_POST['password'];

        if(!$auth->login($user)){
            $err_msg = $_SESSION["errmsg"];
        } else {

            if($_SESSION['user_roleid']==1){
                header("Location: ../../views/Admin/admin-dashboard.php");
                exit();
            }
            elseif($_SESSION['user_roleid']==2){
                header("Location: ../../views/Client/client-dashboard.php");
                exit();
            }
            else{
              
                header("Location: ../../views/Freelancer/freelancer-profile.php");
                exit();
            }
        }

    } else {
        $err_msg = "Please fill in all fields.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <link rel="stylesheet" href="../../public/assets/css/style-login.css">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;600&family=DM+Mono:wght@300;400&display=swap" rel="stylesheet"/>
<body>

  <div class="card">
    <p class="brand"></p>
    <h1>Welcome<br></h1>
    <p class="subtitle">Sign in to continue</p>

    <?php

    if($err_msg!=""){
      ?>
      <div class="alert-danger" role="alert">
        <h3><?php echo $err_msg?></h3>
    </div>
    <?php
    }


    ?>
    


    <form action="login.php" method="post">
      <div class="field">
        <label for="email">email</label>
        <input id="email" name="email" type="email" placeholder="Email address"/>
      </div>

      <div class="field">
        <label for="password">Password</label>
        <input id="password" name="password" type="password" placeholder="password"/>
      </div>

      <div class="options">
        <label class="checkbox-wrap">
          <input type="checkbox"/>
          <span>Remember</span>
        </label>
        <a class="forgot" href="#">Forgot password?</a>
      </div>

      <button class="btn" type="submit">
        <span>Sign In→</span>
      </button>
    </form>

    <p class="footer">No account? <a href="signup.php">Create one</a></p>
  </div>

</body>
</html>