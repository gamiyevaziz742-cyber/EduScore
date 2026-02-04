<?php
session_start();
include '../db_connect.php';

// Check login
if (!isset($_SESSION['student_username'])) {
    header("Location: ../studentlogin/student-login.html");
    exit();
}

$student_id = $_SESSION['student_id'];
$stats = null;

// Fetch Real Stats
$stmt = $conn->prepare("SELECT * FROM student_dashboard_totals WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $stats = $result->fetch_assoc();
} else {
    // Default 0 stats if not found
    $stats = [
        'total_attempts' => 0,
        'total_correct' => 0,
        'total_incorrect' => 0
    ];
}

// Calculate Accuracy
$accuracy = $stats['total_attempts'] > 0 
    ? round(($stats['total_correct'] / $stats['total_attempts']) * 100, 1) 
    : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Analytics - Quizhub</title>
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    
    .analytics-card {
        background: white;
        padding: 30px;
        border-radius: 20px;
        box-shadow: var(--shadow);
        text-align: center;
        height: 100%;
        transition: transform 0.3s;
    }
    .analytics-card:hover { transform: translateY(-5px); }
    
    .analytics-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 1.5rem;
    }
    .icon-blue { background: rgba(78, 84, 200, 0.1); color: var(--primary); }
    .icon-green { background: rgba(29, 209, 161, 0.1); color: #1dd1a1; }
    .icon-red { background: rgba(255, 107, 107, 0.1); color: #ff6b6b; }
    .icon-purp { background: rgba(162, 155, 254, 0.1); color: #a29bfe; }

    .big-number { font-size: 2.5rem; font-weight: 700; color: var(--text); }
    .text-muted { color: #636e72 !important; }

    .chart-container {
        background: white;
        padding: 30px;
        border-radius: 20px;
        box-shadow: var(--shadow);
        margin-top: 30px;
    }
  </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand"><i class="fas fa-graduation-cap"></i> Quizhub</div>
    <div class="sidebar-menu">
        <a href="studentdashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="studentdashboard.php#subjects"><i class="fas fa-book"></i> My Subjects</a>
        <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
        <a href="#" class="active"><i class="fas fa-chart-line"></i> Analytics</a>
        <a href="history.php"><i class="fas fa-history"></i> History</a>
        <a href="logout.php" style="margin-top: 50px; color: #ff6b6b;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <h2 class="mb-4 font-weight-bold">Performance Analytics</h2>
    
    <div class="row">
        <!-- Attempts -->
        <div class="col-md-3">
            <div class="analytics-card">
                <div class="analytics-icon icon-blue"><i class="fas fa-history"></i></div>
                <div class="big-number"><?php echo $stats['total_attempts']; ?></div>
                <div class="text-muted">Total Quiz Attempts</div>
            </div>
        </div>

        <!-- Correct -->
        <div class="col-md-3">
            <div class="analytics-card">
                <div class="analytics-icon icon-green"><i class="fas fa-check"></i></div>
                <div class="big-number"><?php echo $stats['total_correct']; ?></div>
                <div class="text-muted">Correct Answers</div>
            </div>
        </div>

        <!-- Incorrect -->
        <div class="col-md-3">
            <div class="analytics-card">
                <div class="analytics-icon icon-red"><i class="fas fa-times"></i></div>
                <div class="big-number"><?php echo $stats['total_incorrect']; ?></div>
                <div class="text-muted">Incorrect Answers</div>
            </div>
        </div>
        
        <!-- Accuracy -->
        <div class="col-md-3">
            <div class="analytics-card">
                <div class="analytics-icon icon-purp"><i class="fas fa-percentage"></i></div>
                <div class="big-number"><?php echo $accuracy; ?>%</div>
                <div class="text-muted">Overall Accuracy</div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mt-4">
        <div class="col-md-8">
            <div class="chart-container">
                <h4>Performance Breakdown</h4>
                <div style="height: 300px; position: relative;">
                    <canvas id="mainChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="chart-container">
                <h4>Accuracy Ratio</h4>
                <div style="height: 300px; position: relative; padding-top: 20px;">
                    <canvas id="doughnutChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Bar Chart
    const ctx1 = document.getElementById('mainChart').getContext('2d');
    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: ['Attempts', 'Correct', 'Incorrect'],
            datasets: [{
                label: 'Questions',
                data: [<?php echo $stats['total_attempts']; ?>, <?php echo $stats['total_correct']; ?>, <?php echo $stats['total_incorrect']; ?>],
                backgroundColor: ['#4e54c8', '#1dd1a1', '#ff6b6b'],
                borderRadius: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { display: false } }
        }
    });

    // Doughnut Chart
    const ctx2 = document.getElementById('doughnutChart').getContext('2d');
    new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['Correct', 'Incorrect'],
            datasets: [{
                data: [<?php echo $stats['total_correct']; ?>, <?php echo $stats['total_incorrect']; ?>],
                backgroundColor: ['#1dd1a1', '#ff6b6b'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: { legend: { position: 'bottom' } }
        }
    });
</script>

</body>
</html>
