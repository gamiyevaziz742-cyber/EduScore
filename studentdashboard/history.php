<?php
session_start();
include '../db_connect.php';

if (!isset($_SESSION['student_username'])) {
    header("Location: ../studentlogin/student-login.html");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch Attempts Grouped by Subject
// Match schema: result_id, student_id, quiz_id, total_attempts, correct_answers, incorrect_answers, status, taken_at
$sql = "SELECT qr.*, s.subject_name 
        FROM quiz_results qr 
        JOIN subjects s ON qr.quiz_id = s.subject_id 
        WHERE qr.student_id = ? 
        ORDER BY qr.taken_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$history_result = $stmt->get_result();

$history_data = [];
while ($row = $history_result->fetch_assoc()) {
    // Calculate score_percentage if not in DB
    if (!isset($row['score_percentage'])) {
        $row['score_percentage'] = ($row['total_attempts'] > 0) ? round(($row['correct_answers'] / $row['total_attempts']) * 100, 2) : 0;
    }
    $history_data[$row['subject_name']][] = $row;
}

// Helper to fetch details
function getDetails($conn, $result_id) {
    // Match schema: detail_id, result_id, question_text, user_answer, correct_answer, is_correct
    $sql = "SELECT * FROM quiz_history_details WHERE result_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $result_id);
    $stmt->execute();
    return $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Quiz History - Quizhub</title>
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
    
    .history-card {
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: var(--shadow);
        margin-bottom: 20px;
    }

    .subject-header {
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px;
        border-bottom: 1px solid #eee;
    }

    .subject-header:hover { background: #f8f9fa; }

    .attempt-list { display: none; padding-top: 20px; }
    .attempt-list.open { display: block; }

    .attempt-item {
        border-left: 4px solid var(--primary);
        background: #f8f9fa;
        margin-bottom: 15px;
        border-radius: 8px;
        overflow: hidden;
    }

    .attempt-summary {
        padding: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
    }

    .attempt-details {
        display: none;
        padding: 15px;
        background: white;
        border-top: 1px solid #eee;
    }
    
    .badge-score {
        background: var(--primary);
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
    }

    .q-row {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }
    .q-correct { color: #1dd1a1; font-weight: 600; }
    .q-wrong { color: #ff6b6b; font-weight: 600; }
  </style>
  <script>
      function toggleSubject(id) {
          document.getElementById('subject-' + id).classList.toggle('open');
      }
      function toggleAttempt(id) {
          const el = document.getElementById('attempt-' + id);
          el.style.display = el.style.display === 'block' ? 'none' : 'block';
      }
  </script>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-brand"><i class="fas fa-graduation-cap"></i> Quizhub</div>
    <div class="sidebar-menu">
        <a href="studentdashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="studentdashboard.php#subjects"><i class="fas fa-book"></i> My Subjects</a>
        <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
        <a href="student_quizzes.php"><i class="fas fa-chart-line"></i> Analytics</a>
        <a href="#" class="active"><i class="fas fa-history"></i> History</a>
        <a href="logout.php" style="margin-top: 50px; color: #ff6b6b;"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <h2 class="mb-4">Quiz History</h2>

    <?php if (empty($history_data)): ?>
        <div class="alert alert-info">No history found. Take a quiz to see it here!</div>
    <?php else: ?>
        <?php foreach ($history_data as $subject => $attempts): ?>
            <?php $safeSub = preg_replace('/[^a-zA-Z0-9]/', '', $subject); ?>
            <div class="history-card">
                <div class="subject-header" onclick="toggleSubject('<?php echo $safeSub; ?>')">
                    <h4 class="mb-0"><i class="fas fa-book mr-2"></i> <?php echo htmlspecialchars($subject); ?></h4>
                    <span class="badge badge-secondary"><?php echo count($attempts); ?> Attempts</span>
                </div>
                
                <div class="attempt-list" id="subject-<?php echo $safeSub; ?>">
                    <?php foreach ($attempts as $attempt): ?>
                        <div class="attempt-item">
                            <div class="attempt-summary" onclick="toggleAttempt(<?php echo $attempt['result_id']; ?>)">
                                <div>
                                    <strong>Date:</strong> <?php echo date('M d, Y h:i A', strtotime($attempt['taken_at'])); ?>
                                </div>
                                <div>
                                    <span class="badge-score">Score: <?php echo $attempt['correct_answers']; ?>/<?php echo $attempt['total_attempts']; ?></span>
                                    <i class="fas fa-chevron-down ml-2"></i>
                                </div>
                            </div>
                            <div class="attempt-details" id="attempt-<?php echo $attempt['result_id']; ?>">
                                <h6>Question Breakdown:</h6>
                                <?php 
                                    $details = getDetails($conn, $attempt['result_id']);
                                    if ($details->num_rows > 0):
                                        while($d = $details->fetch_assoc()):
                                ?>
                                    <div class="q-row">
                                        <div class="mb-1"><strong>Q:</strong> <?php echo htmlspecialchars($d['question_text']); ?></div>
                                        <div>
                                            Your Answer: 
                                            <span class="<?php echo $d['is_correct'] ? 'q-correct' : 'q-wrong'; ?>">
                                                <?php echo htmlspecialchars($d['user_answer']); ?>
                                            </span>
                                        </div>
                                        <div class="text-success small"><strong>Correct Answer:</strong> <?php echo htmlspecialchars($d['correct_answer']); ?></div>
                                    </div>
                                <?php 
                                        endwhile;
                                    else:
                                        echo "<p class='text-muted'>Detailed history not available for this attempt (Old data).</p>";
                                    endif;
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
