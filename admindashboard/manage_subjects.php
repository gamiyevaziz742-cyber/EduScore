<?php
session_start();
// Security Check: Ensure Admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin-login/admin-login.html");
    exit();
}

include('../db_connect.php');

// Handle Add Subject
if (isset($_POST['add_subject'])) {
    $subject_name = $conn->real_escape_string(trim($_POST['subject_name']));
    $description = $conn->real_escape_string(trim($_POST['description']));
    
    if (!empty($subject_name)) {
        $sql = "INSERT INTO subjects (subject_name, description) VALUES ('$subject_name', '$description')";
        if ($conn->query($sql) === TRUE) {
            $success = "Subject added successfully!";
        } else {
            $error = "Error: " . $conn->error;
        }
    } else {
        $error = "Subject name cannot be empty.";
    }
}

// Handle Delete Subject
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM subjects WHERE subject_id = $id");
    header("Location: manage_subjects.php");
    exit();
}

// Fetch Subjects
$result = $conn->query("SELECT * FROM subjects");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Subjects - EduScore Admin</title>
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
        .card-header { background-color: #4361ee; color: white; border-radius: 10px 10px 0 0 !important; font-weight: bold; }
        .btn-primary { background-color: #4361ee; border: none; }
        .btn-primary:hover { background-color: #3f37c9; }
        .table thead th { border-top: none; background-color: #f1f3f5; color: #495057; }
        .table-hover tbody tr:hover { background-color: #f8f9fa; transform: scale(1.002); transition: all 0.2s; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand">EduScore Admin</div>
    <a href="admin-dashboard.php"><i class="fas fa-tachometer-alt mr-2"></i> Dashboard</a>
    <a href="manage_subjects.php" style="background-color: #495057; color: white;"><i class="fas fa-book mr-2"></i> Subjects</a>
    <a href="manage_teachers.php"><i class="fas fa-chalkboard-teacher mr-2"></i> Teachers</a>
    <a href="manage_students.php"><i class="fas fa-user-graduate mr-2"></i> Students</a>
    <a href="../index/index.html"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
</div>

<div class="content">
    <h2 class="mb-4 text-dark font-weight-bold">Manage Subjects</h2>
    
    <div class="row">
        <!-- Add Subject Form -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-plus-circle mr-2"></i> Add New Subject
                </div>
                <div class="card-body">
                    <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                    <?php if(isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label class="text-muted font-weight-bold">Subject Name</label>
                            <input type="text" name="subject_name" class="form-control" placeholder="e.g. Physics" required>
                        </div>
                        <div class="form-group">
                            <label class="text-muted font-weight-bold">Description</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Short description..."></textarea>
                        </div>
                        <button type="submit" name="add_subject" class="btn btn-primary btn-block py-2">Create Subject</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Subject List -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white text-dark border-bottom">
                    <i class="fas fa-list mt-1"></i> Existing Subjects
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="pl-4">Name</th>
                                <th>Description</th>
                                <th class="text-right pr-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="pl-4 font-weight-bold text-primary"><?php echo htmlspecialchars($row['subject_name']); ?></td>
                                        <td class="text-muted"><?php echo htmlspecialchars($row['description']); ?></td>
                                        <td class="text-right pr-4">
                                            <a href="manage_subjects.php?delete=<?php echo $row['subject_id']; ?>" 
                                               class="btn btn-outline-danger btn-sm rounded-pill px-3"
                                               onclick="return confirm('Wait! Deleting this subject will also delete ALL questions and results linked to it. Are you sure?');">
                                               <i class="fas fa-trash-alt"></i> Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="text-center py-5 text-muted">No subjects found. Add one to get started!</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
