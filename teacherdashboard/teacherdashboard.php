<?php
session_start();
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    header("Location: ../teacher-login/teacher-login.html");
    exit();
}
require '../db_connect.php'; 

// Fetch Teacher Details
$teacher_id = $_SESSION['teacher_id'];
$teacher_username = $_SESSION['teacher_username'];
$subject_id = $_SESSION['subject_id'];

// Get Subject Name
$sub_name = "Not Assigned";
if($subject_id) {
    $s_query = $conn->query("SELECT subject_name FROM subjects WHERE subject_id = $subject_id");
    if($s_query->num_rows > 0) {
        $sub_name = $s_query->fetch_assoc()['subject_name'];
    }
}

// Get Question Count
$q_count = 0;
if($subject_id) {
    $q_query = $conn->query("SELECT COUNT(*) as count FROM questions WHERE quiz_id = $subject_id");
    if($q_query) {
        $q_count = $q_query->fetch_assoc()['count'];
    }
}

// Get Total Students
$stu_count = 0;
$stu_res = $conn->query("SELECT COUNT(*) as count FROM students");
if($stu_res) $stu_count = $stu_res->fetch_assoc()['count'];

// Fetch Questions for View-Only Section
$questions = [];
if($subject_id) {
    $q_res = $conn->query("SELECT question_text, option_a, option_b, option_c, option_d, correct_answer FROM questions WHERE quiz_id = $subject_id ORDER BY question_id DESC LIMIT 10");
    if($q_res) {
        while($row = $q_res->fetch_assoc()) {
            $questions[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - Quizhub</title>
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
        
        .user-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-info {
            text-align: right;
        }
        
        .user-info .username {
            font-weight: 600;
            color: #333;
            display: block;
        }
        
        .user-info .role {
            font-size: 0.85rem;
            color: #666;
        }
        
        .nav-buttons {
            display: flex;
            gap: 10px;
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
        }
        
        .btn-profile {
            background: #667eea;
            color: white;
        }
        
        .btn-profile:hover {
            background: #5568d3;
            color: white;
        }
        
        .btn-logout {
            background: #f5576c;
            color: white;
        }
        
        .btn-logout:hover {
            background: #e04455;
        }
        
        /* Main Container */
        .main-container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        /* Stats Cards */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }
        
        .stat-icon.purple {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .stat-icon.pink {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .stat-icon.blue {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        
        .stat-info h3 {
            margin: 0;
            font-size: 2rem;
            color: #333;
        }
        
        .stat-info p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        /* Action Buttons */
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .action-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            text-decoration: none;
            transition: 0.3s;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            text-decoration: none;
        }
        
        .action-card i {
            font-size: 3rem;
            margin-bottom: 15px;
        }
        
        .action-card.purple {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .action-card.pink {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .action-card h4 {
            margin: 0;
            font-weight: 600;
        }
        
        /* Questions Section */
        .questions-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .section-header h3 {
            margin: 0;
            color: #333;
            font-weight: 700;
        }
        
        .question-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
        }
        
        .question-text {
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            font-size: 1.05rem;
        }
        
        .options-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .option {
            background: white;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 0.9rem;
            color: #555;
        }
        
        .correct-answer {
            background: #d4edda;
            padding: 10px 15px;
            border-radius: 5px;
            color: #155724;
            font-weight: 600;
            display: inline-block;
        }
        
        .no-questions {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        .no-questions i {
            font-size: 4rem;
            margin-bottom: 15px;
            color: #ddd;
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <div class="top-nav">
        <div class="logo-section">
            <i class="fas fa-chalkboard-teacher"></i>
            <h2>Quizhub Teacher</h2>
        </div>
        <div class="user-section">
            <div class="user-info">
                <span class="username"><?php echo htmlspecialchars($teacher_username); ?></span>
                <span class="role"><?php echo htmlspecialchars($sub_name); ?> Teacher</span>
            </div>
            <div class="nav-buttons">
                <a href="profiles.php" class="nav-btn btn-profile">
                    <i class="fas fa-user"></i> Profile
                </a>
                <a href="logout.php" class="nav-btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Stats Row -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-book"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo htmlspecialchars($sub_name); ?></h3>
                    <p>My Subject</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon pink">
                    <i class="fas fa-question-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $q_count; ?></h3>
                    <p>Total Questions</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $stu_count; ?></h3>
                    <p>Total Students</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="questions.php" class="action-card purple">
                <i class="fas fa-plus-circle"></i>
                <h4>Manage Questions</h4>
                <p style="margin-top: 10px; font-size: 0.9rem;">Add, Edit, Delete Questions</p>
            </a>
            
            <a href="profiles.php" class="action-card pink">
                <i class="fas fa-user-cog"></i>
                <h4>My Profile</h4>
                <p style="margin-top: 10px; font-size: 0.9rem;">View & Edit Profile</p>
            </a>
        </div>

        <!-- Questions Section (View Only) -->
        <div class="questions-section">
            <div class="section-header">
                <h3><i class="fas fa-list"></i> My Questions (Latest 10)</h3>
                <span style="color: #999; font-size: 0.9rem;">Read-only view</span>
            </div>
            
            <?php if(count($questions) > 0): ?>
                <?php foreach($questions as $index => $q): ?>
                    <div class="question-item">
                        <div class="question-text">
                            <?php echo ($index + 1) . ". " . htmlspecialchars($q['question_text']); ?>
                        </div>
                        <div class="options-grid">
                            <div class="option"><strong>A:</strong> <?php echo htmlspecialchars($q['option_a']); ?></div>
                            <div class="option"><strong>B:</strong> <?php echo htmlspecialchars($q['option_b']); ?></div>
                            <div class="option"><strong>C:</strong> <?php echo htmlspecialchars($q['option_c']); ?></div>
                            <div class="option"><strong>D:</strong> <?php echo htmlspecialchars($q['option_d']); ?></div>
                        </div>
                        <div>
                            <span class="correct-answer">
                                <i class="fas fa-check-circle"></i> Correct Answer: <?php echo htmlspecialchars($q['correct_answer']); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-questions">
                    <i class="fas fa-inbox"></i>
                    <h4>No Questions Yet</h4>
                    <p>Click "Manage Questions" to add your first question</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>