<?php

session_start();
require_once "../../Models/user.php";
require_once "../../Controllers/authcontroller.php";

$err_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ( !empty($_POST["Username"]) &&!empty($_POST["email"]) &&!empty($_POST["password"]) && !empty($_POST["role"])
    ) {

        $user = new user();
        $auth = new authcontrollers();

        $user->username = $_POST["Username"];
        $user->email = $_POST["email"];
        $user->password_hash = $_POST["password"];

        if ($_POST["role"] == "client") {
            $user->role_id = 2;
        } else {
            $user->role_id = 3;
        }

        if ($auth->register($user)) {

            if ($user->role_id == 2) {
                header("Location: ../../views/Client/client-dashboard.php");
                exit();
            } else {
                header("Location: ../../views/Freelancer/freelancer-dashboard.php");
                exit();
            }

        } else {
            echo "Registration failed";
        }

    } else {
        echo "Please fill all fields";
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
