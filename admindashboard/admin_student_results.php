<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin-login/admin-login.html");
    exit();
}
include('../db_connect.php');

// Fetch overall statistics
$total_students = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];
$total_quizzes_taken = $conn->query("SELECT COUNT(*) as count FROM quiz_results")->fetch_assoc()['count'];
$avg_score_query = $conn->query("SELECT AVG((correct_answers / total_attempts) * 100) as avg_score FROM quiz_results WHERE total_attempts > 0");
$avg_score = $avg_score_query->fetch_assoc()['avg_score'] ?? 0;

// Fetch subject-wise performance
$subject_performance = $conn->query("
    SELECT 
        sub.subject_name,
        COUNT(r.result_id) as attempts,
        AVG((r.correct_answers / r.total_attempts) * 100) as avg_score,
        SUM(CASE WHEN r.status = 'Pass' THEN 1 ELSE 0 END) as pass_count,
        SUM(CASE WHEN r.status = 'Fail' THEN 1 ELSE 0 END) as fail_count
    FROM subjects sub
    LEFT JOIN quiz_results r ON sub.subject_id = r.quiz_id
    WHERE r.total_attempts > 0
    GROUP BY sub.subject_id
    ORDER BY attempts DESC
");

// Fetch recent results
$recent_results = $conn->query("
    SELECT r.*, s.first_name, s.last_name, s.username, s.email, sub.subject_name 
    FROM quiz_results r 
    JOIN students s ON r.student_id = s.student_id 
    JOIN subjects sub ON r.quiz_id = sub.subject_id 
    ORDER BY r.taken_at DESC 
    LIMIT 20
");

// Fetch top performers
$top_performers = $conn->query("
    SELECT 
        s.student_id,
        s.first_name,
        s.last_name,
        s.username,
        COUNT(r.result_id) as total_quizzes,
        AVG((r.correct_answers / r.total_attempts) * 100) as avg_score,
        SUM(r.correct_answers) as total_correct,
        SUM(r.total_attempts) as total_questions
    FROM students s
    JOIN quiz_results r ON s.student_id = r.student_id
    WHERE r.total_attempts > 0
    GROUP BY s.student_id
    ORDER BY avg_score DESC
    LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Performance - Quizhub Admin</title>
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
            overflow-y: auto;
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
        .page-header {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        .stat-card h3 {
            font-size: 2.5rem;
            margin: 10px 0;
            font-weight: 700;
        }
        .stat-card p {
            color: #666;
            margin: 0;
        }
        .stat-card.blue { border-top: 4px solid #4facfe; }
        .stat-card.blue i { color: #4facfe; }
        .stat-card.blue h3 { color: #4facfe; }
        
        .stat-card.purple { border-top: 4px solid #667eea; }
        .stat-card.purple i { color: #667eea; }
        .stat-card.purple h3 { color: #667eea; }
        
        .stat-card.green { border-top: 4px solid #28a745; }
        .stat-card.green i { color: #28a745; }
        .stat-card.green h3 { color: #28a745; }
        
        .section-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }
        .section-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
        }
        .section-header h4 {
            margin: 0;
            font-weight: 600;
        }
        .table thead th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            border-top: none;
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        .badge-pass {
            background: #28a745;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
        }
        .badge-fail {
            background: #dc3545;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
        }
        .progress-bar-custom {
            height: 8px;
            border-radius: 10px;
            background: #e9ecef;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s;
        }
        .student-rank {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .rank-1 { background: linear-gradient(135deg, #FFD700, #FFA500); color: white; }
        .rank-2 { background: linear-gradient(135deg, #C0C0C0, #808080); color: white; }
        .rank-3 { background: linear-gradient(135deg, #CD7F32, #8B4513); color: white; }
        .rank-other { background: #e9ecef; color: #666; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand">Quizhub Admin</div>
    <a href="admin-dashboard.php"><i class="fas fa-tachometer-alt mr-2"></i> Dashboard</a>
    <a href="manage_subjects.php"><i class="fas fa-book mr-2"></i> Subjects</a>
    <a href="manage_teachers.php"><i class="fas fa-chalkboard-teacher mr-2"></i> Teachers</a>
    <a href="manage_students.php"><i class="fas fa-user-graduate mr-2"></i> Students</a>
    <a href="manage_questions.php"><i class="fas fa-question-circle mr-2"></i> Questions</a>
    <a href="admin_student_results.php" style="background-color: #495057; color: white;"><i class="fas fa-chart-line mr-2"></i> Student Performance</a>
    <a href="admin_settings.php"><i class="fas fa-cog mr-2"></i> Settings</a>
    <a href="../index/index.html"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
</div>

<div class="content">
    <!-- Page Header -->
    <div class="page-header">
        <h2 class="mb-2"><i class="fas fa-chart-line"></i> Student Performance Analytics</h2>
        <p class="text-muted mb-0">Comprehensive overview of student quiz performance and statistics</p>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <div class="stat-card blue">
            <i class="fas fa-user-graduate"></i>
            <h3><?php echo $total_students; ?></h3>
            <p>Total Students</p>
        </div>
        <div class="stat-card purple">
            <i class="fas fa-clipboard-list"></i>
            <h3><?php echo $total_quizzes_taken; ?></h3>
            <p>Quizzes Taken</p>
        </div>
        <div class="stat-card green">
            <i class="fas fa-percentage"></i>
            <h3><?php echo number_format($avg_score, 1); ?>%</h3>
            <p>Average Score</p>
        </div>
    </div>

    <!-- Subject-wise Performance -->
    <div class="section-card">
        <div class="section-header">
            <h4><i class="fas fa-book mr-2"></i> Subject-wise Performance</h4>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="pl-4">Subject</th>
                        <th>Total Attempts</th>
                        <th>Average Score</th>
                        <th>Pass Rate</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $subject_performance->fetch_assoc()): ?>
                        <?php 
                            $pass_rate = ($row['attempts'] > 0) ? ($row['pass_count'] / $row['attempts']) * 100 : 0;
                            $avg = $row['avg_score'] ?? 0;
                        ?>
                        <tr>
                            <td class="pl-4">
                                <strong><?= htmlspecialchars($row['subject_name']) ?></strong>
                            </td>
                            <td><?= $row['attempts'] ?></td>
                            <td><strong><?= number_format($avg, 1) ?>%</strong></td>
                            <td>
                                <span class="<?= ($pass_rate >= 50) ? 'badge-pass' : 'badge-fail' ?>">
                                    <?= number_format($pass_rate, 1) ?>%
                                </span>
                            </td>
                            <td style="width: 200px;">
                                <div class="progress-bar-custom">
                                    <div class="progress-fill" style="width: <?= $avg ?>%"></div>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if($subject_performance->num_rows == 0): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">No performance data available yet</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Performers -->
    <div class="section-card">
        <div class="section-header">
            <h4><i class="fas fa-trophy mr-2"></i> Top Performers</h4>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="pl-4">Rank</th>
                        <th>Student</th>
                        <th>Quizzes Taken</th>
                        <th>Average Score</th>
                        <th>Total Correct</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $rank = 1;
                    while($row = $top_performers->fetch_assoc()): 
                        $rank_class = $rank <= 3 ? "rank-$rank" : "rank-other";
                    ?>
                        <tr>
                            <td class="pl-4">
                                <div class="student-rank <?= $rank_class ?>">
                                    <?= $rank ?>
                                </div>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></strong><br>
                                <small class="text-muted"><?= htmlspecialchars($row['username']) ?></small>
                            </td>
                            <td><?= $row['total_quizzes'] ?></td>
                            <td>
                                <strong style="color: #667eea; font-size: 1.1rem;">
                                    <?= number_format($row['avg_score'], 1) ?>%
                                </strong>
                            </td>
                            <td>
                                <?= $row['total_correct'] ?> / <?= $row['total_questions'] ?>
                            </td>
                        </tr>
                    <?php 
                    $rank++;
                    endwhile; 
                    ?>
                    <?php if($top_performers->num_rows == 0): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">No student data available yet</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Quiz Results -->
    <div class="section-card">
        <div class="section-header">
            <h4><i class="fas fa-history mr-2"></i> Recent Quiz Results</h4>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="pl-4">Student</th>
                        <th>Subject</th>
                        <th>Score</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $recent_results->fetch_assoc()): ?>
                        <?php 
                            $score_percent = ($row['total_attempts'] > 0) ? ($row['correct_answers'] / $row['total_attempts']) * 100 : 0;
                        ?>
                        <tr>
                            <td class="pl-4">
                                <strong><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></strong><br>
                                <small class="text-muted"><?= htmlspecialchars($row['email']) ?></small>
                            </td>
                            <td>
                                <span class="badge badge-info"><?= htmlspecialchars($row['subject_name']) ?></span>
                            </td>
                            <td>
                                <strong><?= $row['correct_answers'] ?> / <?= $row['total_attempts'] ?></strong>
                                <small class="text-muted">(<?= number_format($score_percent, 1) ?>%)</small>
                            </td>
                            <td>
                                <span class="<?= ($row['status'] == 'Pass') ? 'badge-pass' : 'badge-fail' ?>">
                                    <?= $row['status'] ?>
                                </span>
                            </td>
                            <td class="text-muted">
                                <?= date('M d, Y H:i', strtotime($row['taken_at'])) ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if($recent_results->num_rows == 0): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted">No quiz results found yet</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
