<?php
session_start();
include '../db_connect.php';

// Check login
if (!isset($_SESSION['student_username'])) {
    header("Location: ../studentlogin/student-login.html");
    exit();
}

$student_id = $_SESSION['student_id'];
$success_msg = "";
$error_msg = "";

// Handle Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_first_name = $_POST['first_name'];
    $new_last_name = $_POST['last_name'];
    $new_email = $_POST['email'];
    $new_age = $_POST['age'];
    $new_school = $_POST['school'];

    // Profile Pic
    $profile_pic_sql = "";
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['profile_pic']['tmp_name'];
        $file_name = basename($_FILES['profile_pic']['name']);
        $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $new_name = "stu_" . $student_id . "_" . time() . "." . $ext;
            $dest = "../uploads/" . $new_name;
            if (!is_dir("../uploads")) mkdir("../uploads", 0777, true);
            if (move_uploaded_file($file_tmp, $dest)) {
                $profile_pic_sql = ", profile_pic='$new_name'";
            }
        }
    }

    $sql = "UPDATE students SET first_name=?, last_name=?, email=?, age=?, school=? $profile_pic_sql WHERE student_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisi", $new_first_name, $new_last_name, $new_email, $new_age, $new_school, $student_id);
    
    if ($stmt->execute()) {
        $success_msg = "Profile updated successfully!";
    } else {
        $error_msg = "Error updating profile.";
    }
}

// Fetch Data
$sql = "SELECT * FROM students WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile - EduScore</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
        --primary: #4e54c8;
        --secondary: #8f94fb;
        --text: #2d3436;
        --bg: #f7f9fc;
        --white: #ffffff;
        --shadow: 0 10px 40px rgba(0,0,0,0.08);
    }
    body { font-family: 'Poppins', sans-serif; background: var(--bg); color: var(--text); overflow-x: hidden; }
    
    .sidebar {
        width: 280px;
        height: 100vh;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: white;
        position: fixed;
        padding: 30px;
    }
    .sidebar-brand { font-size: 1.8rem; font-weight: 700; margin-bottom: 50px; display: flex; align-items: center; gap: 15px; }
    .sidebar-menu a { display: block; padding: 15px; color: rgba(255,255,255,0.8); text-decoration: none; border-radius: 15px; transition: 0.3s; }
    .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(255,255,255,0.2); color: white; }

    .main-content { margin-left: 280px; padding: 40px; }
    
    .profile-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: var(--shadow);
        max-width: 800px;
        margin: auto;
    }

    .profile-header {
        display: flex;
        align-items: center;
        gap: 30px;
        margin-bottom: 40px;
    }

    .profile-pic-wrapper {
        position: relative;
        width: 120px;
        height: 120px;
    }

    .profile-pic {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--secondary);
    }

    .camera-icon {
        position: absolute;
        bottom: 0;
        right: 0;
        background: var(--primary);
        color: white;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 2px solid white;
    }

    .form-group label {
        font-weight: 600;
        color: #636e72;
    }
    
    .form-control {
        border-radius: 10px;
        padding: 12px;
        border: 2px solid #eee;
    }
    
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: none;
    }

    .btn-save {
        background: var(--primary);
        color: white;
        padding: 12px 30px;
        border-radius: 50px;
        border: none;
        font-weight: 600;
        transition: 0.3s;
    }

    .btn-save:hover {
        background: var(--secondary);
        transform: translateY(-2px);
    }
  </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand"><i class="fas fa-graduation-cap"></i> EduScore</div>
    <div class="sidebar-menu">
        <a href="studentdashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="studentdashboard.php#subjects"><i class="fas fa-book"></i> My Subjects</a>
        <a href="#" class="active"><i class="fas fa-user"></i> My Profile</a>
        <a href="student_quizzes.php"><i class="fas fa-chart-line"></i> Analytics</a>
        <a href="history.php"><i class="fas fa-history"></i> History</a>
        <a href="logout.php" style="margin-top: 50px; color: #ff6b6b;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    
    <?php if ($success_msg): ?>
        <div class="alert alert-success"><?php echo $success_msg; ?></div>
    <?php endif; ?>
    
    <?php if ($error_msg): ?>
        <div class="alert alert-danger"><?php echo $error_msg; ?></div>
    <?php endif; ?>

    <div class="profile-card">
        <form method="POST" enctype="multipart/form-data">
            <div class="profile-header">
                <div class="profile-pic-wrapper">
                    <?php 
                        $pic = !empty($student['profile_pic']) ? "../uploads/".$student['profile_pic'] : "https://via.placeholder.com/150"; 
                    ?>
                    <img src="<?php echo $pic; ?>" class="profile-pic" id="preview">
                    <label for="upload" class="camera-icon"><i class="fas fa-camera"></i></label>
                    <input type="file" id="upload" name="profile_pic" style="display: none;" onchange="document.getElementById('preview').src = window.URL.createObjectURL(this.files[0])">
                </div>
                <div>
                    <h2><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h2>
                    <p class="text-muted">@<?php echo htmlspecialchars($student['username']); ?></p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
                </div>
                <div class="col-md-6 form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
                </div>
                <div class="col-md-6 form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($student['email']); ?>" required>
                </div>
                <div class="col-md-3 form-group">
                    <label>Age</label>
                    <input type="number" name="age" class="form-control" value="<?php echo htmlspecialchars($student['age']); ?>" required>
                </div>
                <div class="col-md-12 form-group">
                    <label>School / Institution</label>
                    <input type="text" name="school" class="form-control" value="<?php echo htmlspecialchars($student['school']); ?>" required>
                </div>
            </div>

            <div class="text-right mt-4">
                <button type="submit" class="btn btn-save">Save Changes</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
