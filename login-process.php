<?php
session_start();

// Hardcoded users: username => password
$users = [
    "admin" => "admin123",
    "user1" => "pass123"
];

$username = trim($_POST['username']);
$password = trim($_POST['password']);

// Check credentials
if (isset($users[$username]) && $users[$username] === $password) {
    $_SESSION['logged_in'] = true;
    $_SESSION['username'] = $username;
    header("Location: Index.php");
    exit;
}

// Login failed
$_SESSION['error'] = "Invalid username or password";
header("Location: login.php");
exit;
?>
