<?php
session_start();
require_once "../../Models/user.php";
require_once "../../Controllers/authcontroller.php";
require_once "../../Controllers/DBcontrollers.php";

$err_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["Username"] ?? '');
    $email    = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');
    $role     = trim($_POST["role"] ?? '');
    if (
        !empty($username) &&
        !empty($email) &&
        !empty($password) &&
        !empty($role)
    ) {

        $db = DBcontrollers::getInstance();

        $check_email = $db->Select_query("
            SELECT email
            FROM user
            WHERE email = '$email'
        ");

        if (!empty($check_email)) {

            $err_msg = "Email already exists";

        } else {

            $user = new user();
            $auth = new authcontrollers();

            $user->username = htmlspecialchars($username);
            $user->email = htmlspecialchars($email);

            $user->password_hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            if ($role == "client") {

                $user->role_id = 2;

            } else {

                $user->role_id = 3;
            }

            if ($auth->register($user)) {

                session_regenerate_id(true);

                $new_user = $db->Select_query("
                    SELECT *
                    FROM user
                    WHERE email = '$email'
                    LIMIT 1
                ");

                if (!empty($new_user)) {

                    $_SESSION["userid"] = $new_user[0]["user_id"];
                    $_SESSION["username"] = $new_user[0]["username"];
                    $_SESSION["user_roleid"] = $new_user[0]["role_id"];

                    if ($new_user[0]["role_id"] == 2) {

                        header("Location: ../../views/Client/client-dashboard.php");
                        exit();
                    }

                    // FREELANCER
                    elseif ($new_user[0]["role_id"] == 3) {

                        header("Location: ../../views/onboarding/onboarding.php");
                        exit();
                    }
                }

            } else {

                $err_msg = "Registration failed";
            }
        }

    } else {

        $err_msg = "Please fill all fields";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
<title>SignUp Form</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!-- Custom Theme files -->
<link href="css/style.css" rel="stylesheet" type="text/css" media="all" />
<!-- //Custom Theme files -->
<!-- web font -->
<link href="//fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,700,700i" rel="stylesheet">
<link rel="stylesheet" href="../../public/assets/css/style_signup.css">
<!-- //web font -->
</head>
<body>
	<!-- main -->
	<div class="main-w3layouts wrapper">
		<h1> SignUp Form</h1>
		<div class="main-agileinfo">
			<div class="agileits-top">
				<form action="signup.php" method="post">
					<input class="text" type="text" name="Username" placeholder="Username" required="">
					<input class="text email" type="email" name="email" placeholder="Email" required="">
					<input class="text" type="password" name="password" placeholder="Password" required="">
					<select name="role" id="role">
						<option value="freelancer">freelancer</option>
					    <option value="client">client</option>
					</select>
					
					<div class="wthree-text">
						<label class="anim">
							<input type="checkbox" class="checkbox">
							<span>I Agree To The Terms & Conditions</span>
						</label>
						<div class="clear"> </div>
					</div>
					<input type="submit" value="SIGNUP">
				</form>
				<p>Don't have an Account? <a href="http://localhost/Project/views/Auth/login.php"> Login Now!</a></p>
			</div>
		</div>
		<!-- copyright -->
		<div class="colorlibcopy-agile">
			<p>© 2018 Colorlib Signup Form. All rights reserved | Design by <a href="https://colorlib.com/" target="_blank">Colorlib</a></p>
		</div>
		<!-- //copyright -->
		<ul class="colorlib-bubbles">
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
			<li></li>
		</ul>
	</div>
</body>
</html>
    
</body>

</html>
