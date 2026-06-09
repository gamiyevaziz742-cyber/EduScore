<?php
// Start the session
session_start();

// Include the database connection
include '../db_connect.php';

// Initialize counters
$correctCount = 0;
$incorrectCount = 0;
$unansweredCount = 0;
$questionsWithAnswers = [];

// Check if quiz was submitted
if (empty($_POST)) {
    header("Location: studentdashboard.php");
    exit();
}

$resultId = 0; // Initialize resultId for history linking


// Loop through submitted answers
foreach ($_POST as $key => $value) {
    if (preg_match('/^question_(\d+)$/', $key, $matches)) {
        $question_id = (int)$matches[1];
        $userAnswerOption = trim($value); // This is the option key (A, B, C, or D)

        // Fetch question from the database to get the text and original options
        $query = "SELECT question_text, option_a, option_b, option_c, option_d, correct_answer 
                  FROM questions WHERE question_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $question_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $question = $result->fetch_assoc();
        $stmt->close();

        if (!$question) continue;

        // The correct option key was sent from fetch_questions.php as correct_<id>
        $correctOptionKey = isset($_POST['correct_' . $question_id]) ? $_POST['correct_' . $question_id] : '';
        
        $isCorrect = 0;
        $userAnswerText = 'Not answered';

        if (!empty($userAnswerOption)) {
            // Find what text the user actually selected based on the SHUFFLED options in fetch_questions.php
            // We now send the option text as a hidden input: question_<id>_text_<KEY>
            $textParamName = 'question_' . $question_id . '_text_' . strtoupper($userAnswerOption);
            if (isset($_POST[$textParamName])) {
                $userAnswerText = trim($_POST[$textParamName]);
            } else {
                $userAnswerText = "Option " . $userAnswerOption; 
            }
            
            // Grading logic: compare option key (A, B, C, D) with the correct option key
            if (strtoupper($userAnswerOption) === strtoupper($correctOptionKey)) {
                $correctCount++;
                $isCorrect = 1;
            } else {
                $incorrectCount++;
            }
        } else {
            $unansweredCount++;
        }


        // Store question and answers for UI display
        $questionsWithAnswers[] = [
            'question_id' => $question_id,
            'question_text' => $question['question_text'],
            'user_answer' => $userAnswerText,
            'correct_answer' => trim($question['correct_answer']),
            'is_correct' => $isCorrect
        ];
    }
}

$totalQuestions = $correctCount + $incorrectCount + $unansweredCount;

// Save the results to the database
if (isset($_SESSION['student_id'])) {  
    $studentId = $_SESSION['student_id'];
    $quizId = isset($_POST['quiz_id']) ? (int)$_POST['quiz_id'] : 0;

    // First, update student_dashboard_totals
    $query = "INSERT INTO student_dashboard_totals (student_id, total_attempts, total_correct, total_incorrect, total_unanswered) 
              VALUES (?, 1, ?, ?, ?) 
              ON DUPLICATE KEY UPDATE total_attempts = total_attempts + 1, 
                                      total_correct = total_correct + ?, 
                                      total_incorrect = total_incorrect + ?, 
                                      total_unanswered = total_unanswered + ?";

    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        die("Error preparing student_dashboard_totals query: " . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("iiiiiii", $studentId, $correctCount, $incorrectCount, $unansweredCount, 
                                $correctCount, $incorrectCount, $unansweredCount);

    if (!$stmt->execute()) {
        die("Error executing student_dashboard_totals query: " . htmlspecialchars($stmt->error));
    }
    $stmt->close();

    // Now insert into quiz_results table
    if ($quizId > 0) {
        $status = ($totalQuestions > 0 && ($correctCount / $totalQuestions) >= 0.5) ? 'Pass' : 'Fail';
        
        // Match schema: result_id, student_id, quiz_id, total_attempts (questions), correct_answers, incorrect_answers, status, taken_at
        $query = "INSERT INTO quiz_results (student_id, quiz_id, total_attempts, correct_answers, incorrect_answers, status, taken_at) 
                  VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $conn->prepare($query);
        if ($stmt !== false) {
            $stmt->bind_param("iiiiis", $studentId, $quizId, $totalQuestions, $correctCount, $incorrectCount, $status);
            $stmt->execute();
            $resultId = $stmt->insert_id;
            $stmt->close();

            // Insert into quiz_history_details for each question
            if ($resultId > 0) {
                $detailQuery = "INSERT INTO quiz_history_details (result_id, question_text, user_answer, correct_answer, is_correct) 
                                VALUES (?, ?, ?, ?, ?)";
                $detailStmt = $conn->prepare($detailQuery);
                foreach ($questionsWithAnswers as $qwa) {
                    $detailStmt->bind_param("isssi", $resultId, $qwa['question_text'], $qwa['user_answer'], $qwa['correct_answer'], $qwa['is_correct']);
                    $detailStmt->execute();
                }
                $detailStmt->close();
            }
        }
    }
}

// Clear session data after quiz is graded
unset($_SESSION['current_quiz']);
unset($_SESSION['quiz_data']);

// Update session stats for dashboard
if (!isset($_SESSION['quiz_attempts'])) {
    $_SESSION['quiz_attempts'] = 0;
}
if (!isset($_SESSION['correct_answers'])) {
    $_SESSION['correct_answers'] = 0;
}
if (!isset($_SESSION['incorrect_answers'])) {
    $_SESSION['incorrect_answers'] = 0;
}

$_SESSION['quiz_attempts']++;
$_SESSION['correct_answers'] += $correctCount;
$_SESSION['incorrect_answers'] += $incorrectCount;

// Calculate percentage
$percentage = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100, 2) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results - EduScore</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #4e54c8;
            --secondary: #8f94fb;
            --success: #1dd1a1;
            --danger: #ff6b6b;
            --warning: #feca57;
            --dark: #2d3436;
            --light: #f7f9fc;
            --white: #ffffff;
            --shadow: 0 10px 40px rgba(0,0,0,0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }

        .results-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Header */
        .results-header {
            background: var(--white);
            padding: 30px;
            border-radius: 20px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .results-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        .header-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .result-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--success), #10ac84);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .result-icon i {
            font-size: 2.5rem;
            color: var(--white);
        }

        .results-header h1 {
            font-size: 2.2rem;
            color: var(--dark);
            margin-bottom: 10px;
        }

        .results-header p {
            color: #666;
            font-size: 1.1rem;
        }

        /* Main Stats */
        .stats-section {
            background: var(--white);
            padding: 40px;
            border-radius: 20px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--primary);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--light);
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 1.5rem;
            color: var(--white);
        }

        .icon-correct { background: var(--success); }
        .icon-incorrect { background: var(--danger); }
        .icon-unanswered { background: #a4b0be; }
        .icon-total { background: var(--primary); }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .stat-label {
            color: #666;
            font-size: 0.95rem;
        }

        /* Progress Ring */
        .progress-ring {
            width: 200px;
            height: 200px;
            margin: 0 auto 30px;
            position: relative;
        }

        .ring-bg {
            fill: none;
            stroke: #e9ecef;
            stroke-width: 15;
        }

        .ring-progress {
            fill: none;
            stroke-width: 15;
            stroke-linecap: round;
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
            transition: stroke-dashoffset 1s ease;
            stroke-dasharray: 565;
            stroke-dashoffset: <?= 565 - (565 * $percentage) / 100 ?>;
        }

        .progress-ring.percentage-pass .ring-progress { stroke: var(--success); }
        .progress-ring.percentage-fail .ring-progress { stroke: var(--danger); }

        .ring-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .ring-percentage {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--dark);
        }

        .ring-status {
            font-size: 1.2rem;
            font-weight: 600;
            color: <?= $percentage >= 50 ? 'var(--success)' : 'var(--danger)' ?>;
        }

        /* Question Review */
        .questions-section {
            background: var(--white);
            padding: 40px;
            border-radius: 20px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
        }

        .question-review {
            margin-bottom: 25px;
            padding: 25px;
            border-radius: 15px;
            background: var(--light);
            border-left: 5px solid;
            transition: transform 0.3s;
        }

        .question-review:hover {
            transform: translateX(5px);
        }

        .question-review.correct {
            border-left-color: var(--success);
            background: rgba(29, 209, 161, 0.05);
        }

        .question-review.incorrect {
            border-left-color: var(--danger);
            background: rgba(255, 107, 107, 0.05);
        }

        .question-review.unanswered {
            border-left-color: #a4b0be;
            background: rgba(164, 176, 190, 0.05);
        }

        .question-number {
            display: inline-block;
            background: var(--primary);
            color: var(--white);
            padding: 6px 15px;
            border-radius: 20px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .question-text {
            font-size: 1.1rem;
            color: var(--dark);
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .answer-comparison {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .answer-box {
            padding: 15px;
            border-radius: 10px;
            background: var(--white);
        }

        .answer-box h4 {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .answer-box p {
            font-size: 1rem;
            color: var(--dark);
            font-weight: 500;
        }

        .your-answer { border: 2px solid var(--danger); }
        .correct-answer-box { border: 2px solid var(--success); }

        /* Action Buttons */
        .actions-section {
            background: var(--white);
            padding: 30px;
            border-radius: 20px;
            box-shadow: var(--shadow);
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .btn {
            padding: 15px 40px;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .btn-dashboard {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: var(--white);
        }

        .btn-dashboard:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(78, 84, 200, 0.3);
        }

        .btn-restart {
            background: var(--light);
            color: var(--dark);
            border: 2px solid #e9ecef;
        }

        .btn-restart:hover {
            background: #e9ecef;
            transform: translateY(-3px);
        }

        /* Performance Message */
        .performance-message {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: var(--white);
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 30px;
        }

        .performance-message i {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .performance-message h3 {
            font-size: 1.8rem;
            margin-bottom: 15px;
        }

        .performance-message p {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .answer-comparison {
                grid-template-columns: 1fr;
            }
            
            .actions-section {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            body {
                padding: 20px 10px;
            }
            
            .results-header, .stats-section, .questions-section, .actions-section {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="results-container">
        <!-- Header -->
        <div class="results-header">
            <div class="header-content">
                <div class="result-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <h1>Quiz Completed!</h1>
                <p>You've finished the quiz. Here's how you performed</p>
            </div>
        </div>

        <!-- Performance Message -->
        <div class="performance-message">
            <i class="fas fa-<?= $percentage >= 50 ? 'check-circle' : 'exclamation-circle' ?>"></i>
            <h3>
                <?php if ($percentage >= 80): ?>
                    Outstanding Performance! 🎯
                <?php elseif ($percentage >= 60): ?>
                    Great Job! 👍
                <?php elseif ($percentage >= 50): ?>
                    You Passed! ✅
                <?php else: ?>
                    Keep Practicing! 💪
                <?php endif; ?>
            </h3>
            <p>
                <?php if ($percentage >= 80): ?>
                    You've demonstrated excellent understanding of the material. Keep up the fantastic work!
                <?php elseif ($percentage >= 60): ?>
                    Good effort! You have a solid grasp of the concepts with room for improvement.
                <?php elseif ($percentage >= 50): ?>
                    You've passed the quiz! Review the questions you missed to improve further.
                <?php else: ?>
                    Don't get discouraged! Review the material and try again. Practice makes perfect.
                <?php endif; ?>
            </p>
        </div>

        <!-- Main Stats -->
        <div class="stats-section">
            <h2 class="section-title">
                <i class="fas fa-chart-line"></i>
                Performance Summary
            </h2>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon icon-correct">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="stat-value"><?= $correctCount ?></div>
                    <div class="stat-label">Correct Answers</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon icon-incorrect">
                        <i class="fas fa-times"></i>
                    </div>
                    <div class="stat-value"><?= $incorrectCount ?></div>
                    <div class="stat-label">Incorrect Answers</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon icon-unanswered">
                        <i class="fas fa-question"></i>
                    </div>
                    <div class="stat-value"><?= $unansweredCount ?></div>
                    <div class="stat-label">Unanswered</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon icon-total">
                        <i class="fas fa-list-ol"></i>
                    </div>
                    <div class="stat-value"><?= $totalQuestions ?></div>
                    <div class="stat-label">Total Questions</div>
                </div>
            </div>
            
            <!-- Progress Ring -->
            <div class="progress-ring <?= $percentage >= 50 ? 'percentage-pass' : 'percentage-fail' ?>">
                <svg width="200" height="200">
                    <circle class="ring-bg" cx="100" cy="100" r="90"></circle>
                    <circle class="ring-progress" cx="100" cy="100" r="90"></circle>
                </svg>
                <div class="ring-center">
                    <div class="ring-percentage"><?= $percentage ?>%</div>
                    <div class="ring-status">
                        <?= $percentage >= 50 ? 'PASS' : 'FAIL' ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Question Review -->
        <div class="questions-section">
            <h2 class="section-title">
                <i class="fas fa-clipboard-check"></i>
                Question Review
            </h2>
            
            <?php foreach ($questionsWithAnswers as $index => $question): 
                $isCorrect = $question['user_answer'] === $question['correct_answer'];
                $isUnanswered = $question['user_answer'] === 'Not answered';
                $reviewClass = $isCorrect ? 'correct' : ($isUnanswered ? 'unanswered' : 'incorrect');
            ?>
                <div class="question-review <?= $reviewClass ?>">
                    <div class="question-number">Question <?= $index + 1 ?></div>
                    <div class="question-text"><?= htmlspecialchars($question['question_text']) ?></div>
                    
                    <div class="answer-comparison">
                        <div class="answer-box your-answer">
                            <h4>Your Answer</h4>
                            <p><?= htmlspecialchars($question['user_answer']) ?></p>
                            <?php if (!$isUnanswered && !$isCorrect): ?>
                                <div style="color: var(--danger); font-size: 0.9rem; margin-top: 5px;">
                                    <i class="fas fa-times"></i> Incorrect
                                </div>
                            <?php elseif ($isCorrect): ?>
                                <div style="color: var(--success); font-size: 0.9rem; margin-top: 5px;">
                                    <i class="fas fa-check"></i> Correct
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="answer-box correct-answer-box">
                            <h4>Correct Answer</h4>
                            <p><?= htmlspecialchars($question['correct_answer']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Action Buttons -->
        <div class="actions-section">
            <a href="studentdashboard.php" class="btn btn-dashboard">
                <i class="fas fa-th-large"></i>
                Back to Dashboard
            </a>
        </div>
    </div>

    <script>
        // Add animation to progress ring
        document.addEventListener('DOMContentLoaded', function() {
            const progressRing = document.querySelector('.progress-ring');
            const progressCircle = document.querySelector('.ring-progress');
            
            // Animate progress ring
            setTimeout(() => {
                progressRing.style.opacity = '1';
            }, 500);
            
            // Animate stats cards with delay
            document.querySelectorAll('.stat-card').forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, 300 * (index + 1));
            });
            
            // Animate question reviews with delay
            document.querySelectorAll('.question-review').forEach((review, index) => {
                setTimeout(() => {
                    review.style.opacity = '1';
                    review.style.transform = 'translateX(0)';
                }, 100 * (index + 1));
            });
        });
    </script>
</body>
</html>