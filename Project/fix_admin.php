<?php
// تأكد من صحة المسار لملف الـ DB
require_once "Controllers/DBcontrollers.php"; 

$db = DBcontrollers::getInstance();

// بيانات الأدمن الجديد
$username = "moaz_admin";
$email    = "admin@worknest.com";
$password = "123456"; // ده الباسورد اللي هتدخل بيه

// أهم خطوة: تشفير الباسورد عشان password_verify تفهمه
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// مسح أي أدمن قديم بنفس الإيميل عشان م يحصلش تضارب
$db->insertquery("DELETE FROM user WHERE email = '$email'");

$sql = "INSERT INTO user (username, email, password_hash, role_id, account_status) 
        VALUES ('$username', '$email', '$hashed_password', 1, 'Active')";

if($db->insertquery($sql)) {
    echo "<h1>Admin Created Successfully!</h1>";
    echo "<p><b>Email:</b> $email</p>";
    echo "<p><b>Password:</b> $password</p>";
    echo "<br><a href='views/Auth/login.php'>Go to Login Page</a>";
} else {
    echo " Error creating admin.";
}
?>