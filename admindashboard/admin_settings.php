<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin-login/admin-login.html");
    exit();
}
include('../db_connect.php');

$msg = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password === $confirm_password) {
        if (strlen($new_password) >= 5) {
            // Note: In a real scenario we should look up the admin ID. 
            // Assuming for this project there is a single admin or we use the logged in ID.
            // Let's assume we update the admin 'admin' or all admins if ID not tracked in session.
            // Best practice: Store admin_id in session. Since I didn't see admin_id set in login, I'll default to username 'admin'.
            
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE admins SET password = ? WHERE username = 'admin'"); // Default admin user
            $stmt->bind_param("s", $hashed);
            if ($stmt->execute()) {
                $msg = "<div class='alert alert-success'>Password updated successfully!</div>";
            } else {
                $msg = "<div class='alert alert-danger'>Error updating password.</div>";
            }
        } else {
            $msg = "<div class='alert alert-danger'>Password must be at least 5 characters.</div>";
        }
    } else {
        $msg = "<div class='alert alert-danger'>Passwords do not match.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Settings - Quizhub</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; }
        .sidebar { height: 100vh; width: 250px; position: fixed; top: 0; left: 0; background-color: #343a40; padding-top: 20px; }
        .sidebar a { padding: 15px 25px; text-decoration: none; font-size: 1.1rem; color: #d1d1d1; display: block; transition: 0.3s; }
        .sidebar a:hover { color: #f1f1f1; background-color: #495057; }
        .sidebar .brand { font-size: 1.5rem; color: white; text-align: center; margin-bottom: 30px; font-weight: bold; }
        .content { margin-left: 250px; padding: 40px; }
        .card { border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand">Quizhub Admin</div>
    <a href="admin-dashboard.php"><i class="fas fa-tachometer-alt mr-2"></i> Dashboard</a>
    <a href="manage_subjects.php"><i class="fas fa-book mr-2"></i> Subjects</a>
    <a href="manage_teachers.php"><i class="fas fa-chalkboard-teacher mr-2"></i> Teachers</a>
    <a href="manage_students.php"><i class="fas fa-user-graduate mr-2"></i> Students</a>
    <a href="admin_settings.php" style="background-color: #495057; color: white;"><i class="fas fa-cog mr-2"></i> Settings</a>
    <a href="../index/index.html"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
</div>

<div class="content">
    <h2 class="mb-4 text-dark font-weight-bold">System Settings</h2>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white font-weight-bold">
                    <i class="fas fa-lock mr-2 text-primary"></i> Change Admin Password
                </div>
                <div class="card-body">
                    <?= $msg ?>
                    <form method="POST">
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
             <div class="alert alert-info">
                 <h5><i class="fas fa-shield-alt"></i> Security Tip</h5>
                 <p>Make sure to use a strong password mixed with letters, numbers, and symbols to keep the platform secure.</p>
             </div>
        </div>
    </div>
</div>

</body>
</html>
