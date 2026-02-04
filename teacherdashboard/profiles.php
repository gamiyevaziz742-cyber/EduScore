<?php
session_start();
require '../db_connect.php';

// Check if the teacher is logged in
if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../teacher-login/teacher-login.html");
    exit();
}

// Get the teacher's ID from the session
$teacher_id = $_SESSION['teacher_id'];

// Fetch teacher profile data including subject
$sql = "SELECT t.username, t.email, t.first_name, t.last_name, t.contact_no, t.qualification, t.institution, t.city, t.gender, s.subject_name 
        FROM teachers t 
        LEFT JOIN subjects s ON t.subject_id = s.subject_id 
        WHERE t.teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $teacher = $result->fetch_assoc();
} else {
    echo "Profile information not found.";
    exit();
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Profile - Quizhub</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        /* Top Navigation */
        .top-nav {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo-section i {
            font-size: 2rem;
            color: #667eea;
        }
        
        .logo-section h2 {
            margin: 0;
            color: #333;
            font-weight: 700;
        }
        
        .nav-btn {
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
            background: #667eea;
            color: white;
        }
        
        .nav-btn:hover {
            background: #5568d3;
            color: white;
            text-decoration: none;
        }
        
        /* Profile Container */
        .profile-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .profile-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .profile-avatar i {
            font-size: 4rem;
            color: #667eea;
        }
        
        .profile-header h2 {
            margin: 0;
            font-weight: 700;
        }
        
        .profile-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
        }
        
        .profile-body {
            padding: 40px 30px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .info-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #667eea;
        }
        
        .info-label {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-value {
            font-size: 1.1rem;
            color: #333;
            font-weight: 600;
        }
        
        .full-width {
            grid-column: 1 / -1;
        }
        
        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        
        .btn-custom {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-edit {
            background: #4facfe;
            color: white;
        }
        
        .btn-edit:hover {
            background: #3a9be8;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 172, 254, 0.4);
        }
        
        .btn-dashboard {
            background: #667eea;
            color: white;
        }
        
        .btn-dashboard:hover {
            background: #5568d3;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            text-decoration: none;
        }
        
        /* Edit Form */
        .edit-form {
            display: none;
            margin-top: 30px;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .edit-form.active {
            display: block;
        }
        
        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 12px 15px;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-update {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: 0.3s;
        }
        
        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        @media (max-width: 768px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <div class="top-nav">
        <div class="logo-section">
            <i class="fas fa-user-circle"></i>
            <h2>My Profile</h2>
        </div>
        <a href="teacherdashboard.php" class="nav-btn">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="profile-container">
        <div class="profile-card">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <h2><?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?></h2>
                <p><?php echo htmlspecialchars($teacher['subject_name'] ?? 'No Subject'); ?> Teacher</p>
            </div>

            <!-- Profile Body -->
            <div class="profile-body">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Username</div>
                        <div class="info-value"><?php echo htmlspecialchars($teacher['username']); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?php echo htmlspecialchars($teacher['email']); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">First Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($teacher['first_name']); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Last Name</div>
                        <div class="info-value"><?php echo htmlspecialchars($teacher['last_name']); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Contact Number</div>
                        <div class="info-value"><?php echo htmlspecialchars($teacher['contact_no'] ?? 'Not provided'); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Gender</div>
                        <div class="info-value"><?php echo htmlspecialchars($teacher['gender'] ?? 'Not specified'); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Qualification</div>
                        <div class="info-value"><?php echo htmlspecialchars($teacher['qualification'] ?? 'Not provided'); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Subject</div>
                        <div class="info-value"><?php echo htmlspecialchars($teacher['subject_name'] ?? 'Not assigned'); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">Institution</div>
                        <div class="info-value"><?php echo htmlspecialchars($teacher['institution'] ?? 'Not provided'); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">City</div>
                        <div class="info-value"><?php echo htmlspecialchars($teacher['city'] ?? 'Not provided'); ?></div>
                    </div>
                </div>

                <div class="button-group">
                    <button id="editProfileButton" class="btn-custom btn-edit">
                        <i class="fas fa-edit"></i> Edit Profile
                    </button>
                    <a href="teacherdashboard.php" class="btn-custom btn-dashboard">
                        <i class="fas fa-tachometer-alt"></i> Return to Dashboard
                    </a>
                </div>

                <!-- Edit Form -->
                <div class="edit-form" id="editForm">
                    <h3 style="margin-bottom: 25px; color: #333;">
                        <i class="fas fa-edit"></i> Edit Profile Information
                    </h3>
                    <form action="update_profile.php" method="POST">
                        <input type="hidden" name="teacher_id" value="<?php echo $teacher_id; ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($teacher['username']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($teacher['email']); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($teacher['first_name']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($teacher['last_name']); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Contact Number</label>
                                    <input type="text" name="contact_no" class="form-control" value="<?php echo htmlspecialchars($teacher['contact_no']); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Qualification</label>
                                    <input type="text" name="qualification" class="form-control" value="<?php echo htmlspecialchars($teacher['qualification']); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Institution</label>
                                    <input type="text" name="institution" class="form-control" value="<?php echo htmlspecialchars($teacher['institution']); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($teacher['city']); ?>">
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-update">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        document.getElementById('editProfileButton').addEventListener('click', function() {
            const editForm = document.getElementById('editForm');
            editForm.classList.toggle('active');
            
            if (editForm.classList.contains('active')) {
                this.innerHTML = '<i class="fas fa-times"></i> Cancel Edit';
                this.style.background = '#f5576c';
            } else {
                this.innerHTML = '<i class="fas fa-edit"></i> Edit Profile';
                this.style.background = '#4facfe';
            }
        });
    </script>
</body>
</html>
