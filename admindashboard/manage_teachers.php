<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin-login/admin-login.html");
    exit();
}
include('../db_connect.php');

if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM teachers WHERE teacher_id = $id");
    header("Location: manage_teachers.php");
    exit();
}

$result = $conn->query("SELECT t.*, s.subject_name FROM teachers t LEFT JOIN subjects s ON t.subject_id = s.subject_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Teachers - Quizhub Admin</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8f9fa; }
        .sidebar { height: 100vh; width: 250px; position: fixed; top: 0; left: 0; background-color: #343a40; padding-top: 20px; }
        .sidebar a { padding: 15px 25px; text-decoration: none; font-size: 1.1rem; color: #d1d1d1; display: block; transition: 0.3s; }
        .sidebar a:hover { color: #f1f1f1; background-color: #495057; }
        .sidebar .brand { font-size: 1.5rem; color: white; text-align: center; margin-bottom: 30px; font-weight: bold; }
        .content { margin-left: 250px; padding: 40px; }
        .card { box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; border-radius: 10px; }
        .table thead th { border-top: none; background-color: #f1f3f5; color: #495057; }
        .badge-subject { background-color: #e9ecef; color: #495057; padding: 5px 10px; border-radius: 20px; font-size: 0.9rem; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand">Quizhub Admin</div>
    <a href="admin-dashboard.php"><i class="fas fa-tachometer-alt mr-2"></i> Dashboard</a>
    <a href="manage_subjects.php"><i class="fas fa-book mr-2"></i> Subjects</a>
    <a href="manage_teachers.php" style="background-color: #495057; color: white;"><i class="fas fa-chalkboard-teacher mr-2"></i> Teachers</a>
    <a href="manage_students.php"><i class="fas fa-user-graduate mr-2"></i> Students</a>
    <a href="../index/index.html"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
</div>

<div class="content">
    <h2 class="mb-4 text-dark font-weight-bold">Manage Teachers</h2>
    
    <div class="card">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="pl-4">Name</th>
                        <th>User / Email</th>
                        <th>Assigned Subject</th>
                        <th>Location</th>
                        <th class="text-right pr-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="pl-4">
                            <span class="font-weight-bold text-dark"><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></span><br>
                            <span class="text-muted small"><?= htmlspecialchars($row['qualification']) ?></span>
                        </td>
                        <td>
                            <div class="font-weight-bold"><?= htmlspecialchars($row['username']) ?></div>
                            <div class="text-muted small"><?= htmlspecialchars($row['email']) ?></div>
                        </td>
                        <td>
                            <span class="badge badge-subject">
                                <i class="fas fa-book mr-1"></i> <?= htmlspecialchars($row['subject_name'] ?? 'Unassigned') ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($row['city'] ?? 'N/A') ?></td>
                        <td class="text-right pr-4">
                            <a href="manage_teachers.php?delete=<?= $row['teacher_id'] ?>" 
                               class="btn btn-outline-danger btn-sm rounded-pill px-3" 
                               onclick="return confirm('Are you sure you want to remove this teacher?')">
                               <i class="fas fa-trash-alt"></i> Remove
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php if($result->num_rows == 0): ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">No teachers registered yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
