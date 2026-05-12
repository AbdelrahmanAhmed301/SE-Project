<?php

session_start();

require_once "../../Models/user.php";
require_once "../../Controllers/authcontroller.php";
require_once "../../Controllers/FreelancerProfileController.php";
require_once "../../Controllers/DBcontrollers.php";

$err_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if (!empty($email) && !empty($password)) {

        $db = DBcontrollers::getInstance();
        $result = $db->Select_query("
            SELECT *
            FROM user
            WHERE email = '$email'
            LIMIT 1
        ");

        if (!empty($result)) {

            $user_data = $result[0];

           if (password_verify($password, $user_data['password_hash'])) {
    
    // فحص حالة الحساب قبل السماح بالدخول
    // إذا كان العمود غير موجود أو قيمته Banned سيتم منعه
    if (isset($user_data['account_status']) && $user_data['account_status'] === 'Banned') {
        $err_msg = "Your account has been suspended by the administrator.";
    } else {
        session_regenerate_id(true);

        $_SESSION['userid']      = $user_data['user_id'];
        $_SESSION['username']    = $user_data['username'];
        $_SESSION['user_roleid'] = $user_data['role_id'];

        // توجيه المستخدم حسب دوره
        if ($user_data['role_id'] == 1) {
            header("Location: ../../views/Admin/admin-dashboard.php");
            exit();
        } elseif ($user_data['role_id'] == 2) {
            header("Location: ../../views/Client/client-dashboard.php");
            exit();
        } elseif ($user_data['role_id'] == 3) {
            header("Location: ../../views/Freelancer/freelancer-dashboard.php");
            exit();
        }
    }
} else {
    $err_msg = "Incorrect password";
}
        } else {
            $err_msg = "Email does not exist";
        }
    } else {
        $err_msg = "Please fill in all fields";
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