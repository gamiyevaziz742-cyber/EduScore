<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../admin-login/admin-login.html");
    exit();
}
include('../db_connect.php');

// Fetch all subjects with question counts
$subjects_query = "SELECT s.subject_id, s.subject_name, COUNT(q.question_id) as question_count 
                   FROM subjects s 
                   LEFT JOIN questions q ON s.subject_id = q.quiz_id 
                   GROUP BY s.subject_id 
                   ORDER BY s.subject_name";
$subjects_result = $conn->query($subjects_query);
$subjects = [];
while($row = $subjects_result->fetch_assoc()) {
    $subjects[] = $row;
}

// Filter Logic
$selected_subject = isset($_GET['subject_id']) ? intval($_GET['subject_id']) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Questions - Quizhub Admin</title>
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
        .subject-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }
        .subject-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .subject-header h4 {
            margin: 0;
            font-weight: 600;
        }
        .question-item {
            padding: 25px 30px;
            border-bottom: 1px solid #f0f0f0;
        }
        .question-item:last-child {
            border-bottom: none;
        }
        .question-text {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }
        .options-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }
        .option {
            background: #f8f9fa;
            padding: 12px 15px;
            border-radius: 5px;
            font-size: 0.95rem;
        }
        .option.correct {
            background: #d4edda;
            color: #155724;
            font-weight: 600;
            border-left: 4px solid #28a745;
        }
        .correct-answer-badge {
            background: #28a745;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-block;
        }
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card h3 {
            font-size: 2rem;
            margin: 10px 0;
            color: #667eea;
        }
        .stat-card p {
            color: #666;
            margin: 0;
        }
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .no-questions {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        .no-questions i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #ddd;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand">Quizhub Admin</div>
    <a href="admin-dashboard.php"><i class="fas fa-tachometer-alt mr-2"></i> Dashboard</a>
    <a href="manage_subjects.php"><i class="fas fa-book mr-2"></i> Subjects</a>
    <a href="manage_teachers.php"><i class="fas fa-chalkboard-teacher mr-2"></i> Teachers</a>
    <a href="manage_students.php"><i class="fas fa-user-graduate mr-2"></i> Students</a>
    <a href="manage_questions.php" style="background-color: #495057; color: white;"><i class="fas fa-question-circle mr-2"></i> Questions</a>
    <a href="admin_student_results.php"><i class="fas fa-chart-line mr-2"></i> Student Performance</a>
    <a href="admin_settings.php"><i class="fas fa-cog mr-2"></i> Settings</a>
    <a href="../index/index.html"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
</div>

<div class="content">
    <!-- Page Header -->
    <div class="page-header">
        <h2 class="mb-2"><i class="fas fa-question-circle"></i> Questions Bank</h2>
        <p class="text-muted mb-0">Review all questions created by teachers across all subjects</p>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
        <?php 
        $total_questions = 0;
        foreach($subjects as $sub) {
            $total_questions += $sub['question_count'];
        }
        ?>
        <div class="stat-card">
            <i class="fas fa-book" style="font-size: 2rem; color: #667eea;"></i>
            <h3><?php echo count($subjects); ?></h3>
            <p>Total Subjects</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-question-circle" style="font-size: 2rem; color: #f093fb;"></i>
            <h3><?php echo $total_questions; ?></h3>
            <p>Total Questions</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <form method="GET" class="form-inline">
            <label class="mr-3 font-weight-bold">Filter by Subject:</label>
            <select name="subject_id" class="form-control mr-2" onchange="this.form.submit()">
                <option value="0">All Subjects</option>
                <?php foreach($subjects as $sub): ?>
                    <option value="<?= $sub['subject_id'] ?>" <?= ($selected_subject == $sub['subject_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($sub['subject_name']) ?> (<?= $sub['question_count'] ?> questions)
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if($selected_subject > 0): ?>
                <a href="manage_questions.php" class="btn btn-secondary btn-sm">
                    <i class="fas fa-times"></i> Clear Filter
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Questions by Subject -->
    <?php
    if($selected_subject > 0) {
        // Show only selected subject
        $subjects_to_show = array_filter($subjects, function($s) use ($selected_subject) {
            return $s['subject_id'] == $selected_subject;
        });
    } else {
        // Show all subjects
        $subjects_to_show = $subjects;
    }

    foreach($subjects_to_show as $subject):
        // Fetch questions for this subject
        $subject_id = $subject['subject_id'];
        $questions_query = "SELECT * FROM questions WHERE quiz_id = $subject_id ORDER BY question_id DESC";
        $questions_result = $conn->query($questions_query);
    ?>
        <div class="subject-card">
            <div class="subject-header">
                <h4><i class="fas fa-book mr-2"></i> <?= htmlspecialchars($subject['subject_name']) ?></h4>
                <span class="badge badge-light"><?= $subject['question_count'] ?> Questions</span>
            </div>
            
            <?php if($questions_result->num_rows > 0): ?>
                <?php $q_num = 1; while($q = $questions_result->fetch_assoc()): ?>
                    <div class="question-item">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="question-text">
                                <?= $q_num ?>. <?= htmlspecialchars($q['question_text']) ?>
                            </div>
                            <span class="text-muted small">ID: <?= $q['question_id'] ?></span>
                        </div>
                        
                        <div class="options-grid">
                            <div class="option <?= ($q['correct_answer'] == $q['option_a']) ? 'correct' : '' ?>">
                                <strong>A:</strong> <?= htmlspecialchars($q['option_a']) ?>
                                <?= ($q['correct_answer'] == $q['option_a']) ? '<i class="fas fa-check-circle ml-2"></i>' : '' ?>
                            </div>
                            <div class="option <?= ($q['correct_answer'] == $q['option_b']) ? 'correct' : '' ?>">
                                <strong>B:</strong> <?= htmlspecialchars($q['option_b']) ?>
                                <?= ($q['correct_answer'] == $q['option_b']) ? '<i class="fas fa-check-circle ml-2"></i>' : '' ?>
                            </div>
                            <div class="option <?= ($q['correct_answer'] == $q['option_c']) ? 'correct' : '' ?>">
                                <strong>C:</strong> <?= htmlspecialchars($q['option_c']) ?>
                                <?= ($q['correct_answer'] == $q['option_c']) ? '<i class="fas fa-check-circle ml-2"></i>' : '' ?>
                            </div>
                            <div class="option <?= ($q['correct_answer'] == $q['option_d']) ? 'correct' : '' ?>">
                                <strong>D:</strong> <?= htmlspecialchars($q['option_d']) ?>
                                <?= ($q['correct_answer'] == $q['option_d']) ? '<i class="fas fa-check-circle ml-2"></i>' : '' ?>
                            </div>
                        </div>
                        
                        <div>
                            <span class="correct-answer-badge">
                                <i class="fas fa-check-circle"></i> Correct Answer: <?= htmlspecialchars($q['correct_answer']) ?>
                            </span>
                        </div>
                    </div>
                <?php $q_num++; endwhile; ?>
            <?php else: ?>
                <div class="no-questions">
                    <i class="fas fa-inbox"></i>
                    <h5>No Questions Yet</h5>
                    <p>Teachers haven't added questions for this subject</p>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>

    <?php if(count($subjects_to_show) == 0): ?>
        <div class="subject-card">
            <div class="no-questions">
                <i class="fas fa-exclamation-circle"></i>
                <h4>No Subjects Found</h4>
                <p>Please add subjects first</p>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
