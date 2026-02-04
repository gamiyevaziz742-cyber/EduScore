<?php
session_start();
include '../db_connect.php';

// Check login
if (!isset($_SESSION['student_username'])) {
    header("Location: ../studentlogin/student-login.html");
    exit();
}

// Initialize Stats
if (!isset($_SESSION['quiz_attempts'])) $_SESSION['quiz_attempts'] = 0;
if (!isset($_SESSION['correct_answers'])) $_SESSION['correct_answers'] = 0;
if (!isset($_SESSION['incorrect_answers'])) $_SESSION['incorrect_answers'] = 0;

// Fetch Stats from DB
$studentId = $_SESSION['student_id'];
$query = "SELECT total_attempts, total_correct, total_incorrect FROM student_dashboard_totals WHERE student_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $_SESSION['quiz_attempts'] = $row['total_attempts'];
    $_SESSION['correct_answers'] = $row['total_correct'];
    $_SESSION['incorrect_answers'] = $row['total_incorrect'];
}
$stmt->close();

// Fetch Subjects
$subjects_query = "SELECT * FROM subjects";
$subjects_result = $conn->query($subjects_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard - Quizhub</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
        --primary: #4e54c8;
        --secondary: #8f94fb;
        --accent: #ff6b6b;
        --success: #1dd1a1;
        --text: #2d3436;
        --text-light: #636e72;
        --bg: #f7f9fc;
        --white: #ffffff;
        --shadow: 0 10px 40px rgba(0,0,0,0.08);
        --glass: rgba(255, 255, 255, 0.95);
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--bg);
        color: var(--text);
        margin: 0;
        overflow-x: hidden;
    }

    /* Sidebar */
    .sidebar {
        width: 280px;
        height: 100vh;
        background: linear-gradient(135deg, var(--primary), var(--secondary));
        color: var(--white);
        position: fixed;
        left: 0;
        top: 0;
        padding: 30px;
        z-index: 100;
        box-shadow: 5px 0 25px rgba(0,0,0,0.1);
    }

    .sidebar-brand {
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 50px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .sidebar-menu a {
        display: flex;
        align-items: center;
        gap: 15px;
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        padding: 15px 20px;
        border-radius: 15px;
        margin-bottom: 10px;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .sidebar-menu a:hover, .sidebar-menu a.active {
        background: rgba(255,255,255,0.2);
        color: var(--white);
        transform: translateX(5px);
    }

    /* Main Content */
    .main-content {
        margin-left: 280px;
        padding: 40px;
    }

    /* Header */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 40px;
    }

    .welcome-text h2 {
        font-weight: 700;
        color: var(--text);
    }

    .welcome-text p {
        color: var(--text-light);
    }

    .user-profile {
        display: flex;
        align-items: center;
        gap: 15px;
        background: var(--white);
        padding: 10px 20px;
        border-radius: 50px;
        box-shadow: var(--shadow);
    }

    .avatar {
        width: 40px;
        height: 40px;
        background: var(--primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
    }

    /* Stats Cards */
    .stats-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        margin-bottom: 50px;
    }

    .stat-card {
        background: var(--white);
        padding: 30px;
        border-radius: 20px;
        box-shadow: var(--shadow);
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-card h3 {
        color: var(--text-light);
        font-size: 1rem;
        font-weight: 500;
        margin-bottom: 10px;
    }

    .stat-card .value {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--text);
    }

    .stat-card .icon {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 4rem;
        opacity: 0.1;
    }

    .icon-attempts { color: var(--primary); }
    .icon-correct { color: var(--success); }
    .icon-wrong { color: var(--accent); }

    /* Subjects Grid */
    .subjects-section {
        margin-bottom: 50px;
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .subjects-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 25px;
    }

    .subject-card {
        background: var(--white);
        padding: 25px;
        border-radius: 20px;
        box-shadow: var(--shadow);
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .subject-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 5px;
        background: linear-gradient(90deg, var(--primary), var(--secondary));
    }

    .subject-card:hover {
        transform: translateY(-5px);
        border-color: var(--primary);
    }

    .subject-icon {
        width: 60px;
        height: 60px;
        background: rgba(78, 84, 200, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        color: var(--primary);
        font-size: 1.5rem;
    }

    .subject-name {
        font-weight: 600;
        color: var(--text);
        margin-bottom: 5px;
    }

    .subject-desc {
        font-size: 0.8rem;
        color: var(--text-light);
    }

    /* Charts & Todo Layout */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
    }

    .panel {
        background: var(--white);
        padding: 30px;
        border-radius: 20px;
        box-shadow: var(--shadow);
    }

    /* Todo List */
    .todo-input-group {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .todo-input {
        flex: 1;
        padding: 12px 15px;
        border: 2px solid #eee;
        border-radius: 12px;
        outline: none;
        transition: border-color 0.3s;
    }

    .todo-input:focus {
        border-color: var(--primary);
    }

    .btn-add {
        background: var(--primary);
        color: white;
        border: none;
        padding: 0 20px;
        border-radius: 12px;
        cursor: pointer;
    }

    .todo-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #eee;
        color: var(--text);
    }

    .btn-delete {
        color: var(--accent);
        background: none;
        border: none;
        cursor: pointer;
    }

  </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-graduation-cap"></i>
            Quizhub
        </div>
        <div class="sidebar-menu">
            <a href="#" class="active"><i class="fas fa-th-large"></i> Dashboard</a>
            <a href="#subjects"><i class="fas fa-book"></i> My Subjects</a>
            <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
            <a href="student_quizzes.php"><i class="fas fa-chart-line"></i> Analytics</a>
            <a href="history.php"><i class="fas fa-history"></i> History</a>
            <a href="logout.php" style="margin-top: 50px; color: #ff6b6b;"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="welcome-text">
                <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['student_username']); ?>! 👋</h2>
                <p>Ready to learn something new today?</p>
            </div>
            <div class="user-profile">
                <div class="avatar">
                    <?php echo strtoupper(substr($_SESSION['student_username'], 0, 1)); ?>
                </div>
                <span><?php echo htmlspecialchars($_SESSION['student_username']); ?></span>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="stats-container">
            <div class="stat-card">
                <h3>Total Quiz Attempts</h3>
                <div class="value"><?php echo $_SESSION['quiz_attempts']; ?></div>
                <i class="fas fa-pen-fancy icon icon-attempts"></i>
            </div>
            <div class="stat-card">
                <h3>Correct Answers</h3>
                <div class="value"><?php echo $_SESSION['correct_answers']; ?></div>
                <i class="fas fa-check-circle icon icon-correct"></i>
            </div>
            <div class="stat-card">
                <h3>Needs Improvement</h3>
                <div class="value"><?php echo $_SESSION['incorrect_answers']; ?></div>
                <i class="fas fa-times-circle icon icon-wrong"></i>
            </div>
        </div>

        <!-- Subjects Grid -->
        <div class="subjects-section" id="subjects">
            <div class="section-title">
                <i class="fas fa-layer-group"></i>
                Available Subjects
            </div>

            <div class="subjects-grid">
                <?php
                if ($subjects_result->num_rows > 0) {
                    // Reset pointer ताकि फेरि loop गर्न सकियोस्
                    $subjects_result->data_seek(0);
                    
                    while($subject = $subjects_result->fetch_assoc()) {
                        // Generate a random icon or map styling based on ID if needed
                        echo '
                        <div class="subject-card" onclick="window.location.href=\'select_quiz.php?subject_id='.$subject['subject_id'].'\'">
                            <div class="subject-icon">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <div class="subject-name">'.htmlspecialchars($subject['subject_name']).'</div>
                            <div class="subject-desc">'.htmlspecialchars($subject['description']).'</div>
                        </div>';
                    }
                } else {
                    echo '<p>No subjects found.</p>';
                }
                ?>
            </div>
        </div>

        <!-- Graph & Todo -->
        <div class="dashboard-grid">
            <!-- Graph -->
            <div class="panel">
                <div class="section-title">
                    <i class="fas fa-chart-pie"></i> Performance Overview
                </div>
                <div style="height: 300px; position: relative;">
                    <canvas id="performanceGraph"></canvas>
                </div>
            </div>

            <!-- Todo -->
            <div class="panel">
                <div class="section-title">
                    <i class="fas fa-tasks"></i> Study Goals
                </div>
                <div class="todo-input-group">
                    <input type="text" id="todoInput" class="todo-input" placeholder="New goal...">
                    <button class="btn-add" onclick="addTodo()"><i class="fas fa-plus"></i></button>
                </div>
                <ul class="todo-list" id="todoList">
                    <li>Revise DBMS Normalization <button class="btn-delete" onclick="this.parentElement.remove()"><i class="fas fa-trash"></i></button></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Todo Functionality
        function addTodo() {
            const input = document.getElementById('todoInput');
            const list = document.getElementById('todoList');
            if (input.value.trim() === '') return;

            const li = document.createElement('li');
            li.innerHTML = `${input.value} <button class="btn-delete" onclick="this.parentElement.remove()"><i class="fas fa-trash"></i></button>`;
            list.appendChild(li);
            input.value = '';
        }

        // Chart
        const ctx = document.getElementById('performanceGraph').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Correct', 'Incorrect', 'Skipped'],
                datasets: [{
                    data: [
                        <?php echo $_SESSION['correct_answers']; ?>, 
                        <?php echo $_SESSION['incorrect_answers']; ?>,
                        <?php echo max(0, $_SESSION['quiz_attempts'] - ($_SESSION['correct_answers'] + $_SESSION['incorrect_answers'])); ?> 
                    ],
                    backgroundColor: ['#1dd1a1', '#ff6b6b', '#a4b0be'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    </script>
</body>
</html>