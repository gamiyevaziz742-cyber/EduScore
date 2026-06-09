<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin-login/admin-login.html");
    exit();
}
include('../db_connect.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - EduScore</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar a {
            padding: 15px 25px;
            text-decoration: none;
            font-size: 1.1rem;
            color: #d1d1d1;
            display: block;
            transition: 0.3s;
        }
        .sidebar a:hover {
            color: #f1f1f1;
            background-color: #495057;
        }
        .sidebar .brand {
            font-size: 1.5rem;
            color: white;
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
        }
        .content {
            margin-left: 250px;
            padding: 40px;
        }
        .card-box {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }
        .card-box h3 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #4361ee;
        }
        .card-box p {
            color: #6c757d;
            font-size: 1.2rem;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="brand">EduScore Admin</div>
        <a href="admin-dashboard.php"><i class="fas fa-tachometer-alt mr-2"></i> Dashboard</a>
        <a href="manage_subjects.php"><i class="fas fa-book mr-2"></i> Subjects</a>
        <a href="manage_teachers.php"><i class="fas fa-chalkboard-teacher mr-2"></i> Teachers</a>
        <a href="manage_students.php"><i class="fas fa-user-graduate mr-2"></i> Students</a>
        <a href="manage_questions.php"><i class="fas fa-question-circle mr-2"></i> Questions</a>
        <a href="admin_student_results.php"><i class="fas fa-chart-line mr-2"></i> Results</a>
        <a href="admin_settings.php"><i class="fas fa-cog mr-2"></i> Settings</a>
        <a href="../index/index.html"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
    </div>

<?php
// Fetch Counts
$teacherCount = $conn->query("SELECT COUNT(*) as count FROM teachers")->fetch_assoc()['count'];
$studentCount = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];
$subjectCount = $conn->query("SELECT COUNT(*) as count FROM subjects")->fetch_assoc()['count'];
$questionCount = $conn->query("SELECT COUNT(*) as count FROM questions")->fetch_assoc()['count'];

// Fetch Recent Students
$recentStudents = $conn->query("SELECT * FROM students ORDER BY created_at DESC LIMIT 5");
?>

    <div class="content">
        <div class="header">
            <div>
                <h2 class="font-weight-bold text-dark mb-0">Dashboard Overview</h2>
                <p class="text-muted">Welcome back, Admin</p>
            </div>
            <div class="user-info text-right">
                <a href="admin_settings.php" class="text-dark" style="text-decoration: none;">
                    <i class="fas fa-user-circle fa-2x"></i>
                    <span class="d-block small mt-1">Settings</span>
                </a>
            </div>
        </div>

        <!-- Analytics Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="card-box bg-primary text-white text-left p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0 font-weight-bold text-white"><?= $subjectCount ?></h3>
                            <p class="mb-0 text-white-50">Subjects</p>
                        </div>
                        <i class="fas fa-book fa-3x text-white-50"></i>
                    </div>
                    <a href="manage_subjects.php" class="text-white small mt-3 d-block font-weight-bold" style="text-decoration: underline;">Manage Subjects &rarr;</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-box bg-success text-white text-left p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0 font-weight-bold text-white"><?= $teacherCount ?></h3>
                            <p class="mb-0 text-white-50">Teachers</p>
                        </div>
                        <i class="fas fa-chalkboard-teacher fa-3x text-white-50"></i>
                    </div>
                    <a href="manage_teachers.php" class="text-white small mt-3 d-block font-weight-bold" style="text-decoration: underline;">Manage Teachers &rarr;</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-box bg-warning text-white text-left p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0 font-weight-bold text-white"><?= $studentCount ?></h3>
                            <p class="mb-0 text-white-50">Students</p>
                        </div>
                        <i class="fas fa-user-graduate fa-3x text-white-50"></i>
                    </div>
                    <a href="manage_students.php" class="text-white small mt-3 d-block font-weight-bold" style="text-decoration: underline;">Manage Students &rarr;</a>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-box bg-info text-white text-left p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0 font-weight-bold text-white"><?= $questionCount ?></h3>
                            <p class="mb-0 text-white-50">Questions</p>
                        </div>
                        <i class="fas fa-question-circle fa-3x text-white-50"></i>
                    </div>
                    <span class="text-white small mt-3 d-block font-weight-bold">Total Question Bank</span>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity Section -->
        <div class="row mt-4">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white font-weight-bold border-0">
                        <i class="fas fa-clock mr-2 text-primary"></i> Recently Registered Students
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-top-0">Name</th>
                                    <th class="border-top-0">Email</th>
                                    <th class="border-top-0">Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($std = $recentStudents->fetch_assoc()): ?>
                                <tr>
                                    <td class="font-weight-bold"><?= htmlspecialchars($std['first_name'] . ' ' . $std['last_name']) ?></td>
                                    <td class="text-muted small"><?= htmlspecialchars($std['email']) ?></td>
                                    <td class="text-muted small"><?= date('M d, Y', strtotime($std['created_at'])) ?></td>
                                </tr>
                                <?php endwhile; ?>
                                <?php if($recentStudents->num_rows == 0): ?>
                                    <tr><td colspan="3" class="text-center text-muted">No recent activity.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white font-weight-bold border-0">
                        <i class="fas fa-bolt mr-2 text-warning"></i> Quick Actions
                    </div>
                    <div class="card-body">
                        <a href="manage_subjects.php" class="btn btn-outline-primary btn-block text-left mb-2">
                            <i class="fas fa-plus-circle mr-2"></i> Add New Subject
                        </a>
                        <a href="../teacher-registration/teacher-registration.html" target="_blank" class="btn btn-outline-success btn-block text-left mb-2">
                            <i class="fas fa-user-plus mr-2"></i> Register a Teacher Link
                        </a>
                        <a href="admin_settings.php" class="btn btn-outline-secondary btn-block text-left">
                            <i class="fas fa-cog mr-2"></i> System Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
