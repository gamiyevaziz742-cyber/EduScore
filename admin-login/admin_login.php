<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hardcoded credentials as requested
    if ($username === 'admin' && $password === '12345') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header("Location: ../admindashboard/admin-dashboard.php");
        exit();
    } else {
        echo "<script>alert('Invalid Username or Password'); window.location.href='admin-login.html';</script>";
    }
}
?>
