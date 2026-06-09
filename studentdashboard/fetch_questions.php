<?php
session_start();
include '../db_connect.php';

// Check login
if (!isset($_SESSION['student_username'])) {
    header("Location: ../studentlogin/student-login.html");
    exit();
}

// Debug: Check what was received
// Uncomment these lines to debug:
// echo "<pre>POST Data: "; print_r($_POST); echo "</pre>";
// exit();

// Get quiz parameters - check if they exist
if (!isset($_POST['subject']) || !isset($_POST['question_count']) || !isset($_POST['time_limit'])) {
    echo "<h3>Error: Invalid quiz parameters</h3>";
    echo "<p>Received POST data:</p>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    echo "<p><a href='select_quiz.php'>Go back to quiz selection</a></p>";
    exit();
}

$quiz_id = (int)$_POST['subject'];
$question_count = (int)$_POST['question_count'];
$time_limit = (int)$_POST['time_limit'];

// Fetch subject name
$subject_query = "SELECT subject_name FROM subjects WHERE subject_id = ?";
$stmt = $conn->prepare($subject_query);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$subject_result = $stmt->get_result();
$subject_name = $subject_result->fetch_assoc()['subject_name'] ?? 'Unknown Subject';
$stmt->close();

// Fetch random questions
$query = "SELECT question_id, question_text, option_a, option_b, option_c, option_d, correct_answer 
          FROM questions 
          WHERE quiz_id = ? 
          ORDER BY RAND() 
          LIMIT ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $quiz_id, $question_count);
$stmt->execute();
$result = $stmt->get_result();

$questions = [];
while ($row = $result->fetch_assoc()) {
    // Shuffle options for each question
    $options = [
        'A' => $row['option_a'],
        'B' => $row['option_b'],
        'C' => $row['option_c'],
        'D' => $row['option_d']
    ];
    
    // Find which key has the correct answer
    $correct_key = array_search($row['correct_answer'], $options);
    
    // Shuffle the options
    $values = array_values($options);
    shuffle($values);
    
    // Create new shuffled options array
    $shuffled_options = [
        'A' => $values[0],
        'B' => $values[1],
        'C' => $values[2],
        'D' => $values[3]
    ];
    
    // Find new key for correct answer
    $new_correct_key = array_search($row['correct_answer'], $shuffled_options);
    
    $questions[] = [
        'question_id' => $row['question_id'],
        'question_text' => $row['question_text'],
        'options' => $shuffled_options,
        'correct_answer' => $new_correct_key
    ];
}

$stmt->close();
$conn->close();

// Store quiz info in session for grading
$_SESSION['current_quiz'] = [
    'quiz_id' => $quiz_id,
    'question_count' => $question_count,
    'time_limit' => $time_limit
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz - <?= htmlspecialchars($subject_name) ?></title>
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
            padding: 20px;
        }
        
        .quiz-container {
            max-width: 900px;
            margin: 0 auto;
        }
        
        /* Header */
        .quiz-header {
            background: white;
            padding: 25px 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .quiz-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .quiz-title i {
            font-size: 2rem;
            color: #667eea;
        }
        
        .quiz-title h1 {
            font-size: 1.8rem;
            color: #333;
            margin: 0;
        }
        
        .quiz-info {
            text-align: right;
        }
        
        .quiz-info p {
            margin: 5px 0;
            color: #666;
        }
        
        /* Timer */
        .timer-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            margin-bottom: 25px;
            text-align: center;
        }
        
        .timer {
            font-size: 2.5rem;
            font-weight: 700;
            color: #667eea;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        
        .timer.warning {
            color: #ff6b6b;
            animation: pulse 1s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        /* Progress */
        .progress-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            margin-bottom: 25px;
        }
        
        .progress-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .progress-bar-container {
            height: 10px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            transition: width 0.3s;
        }
        
        /* Question Card */
        .question-card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            margin-bottom: 25px;
            display: none;
        }
        
        .question-card.active {
            display: block;
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .question-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 50px;
            display: inline-block;
            margin-bottom: 20px;
            font-weight: 600;
        }
        
        .question-text {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .options-container {
            display: grid;
            gap: 15px;
        }
        
        .option {
            background: #f8f9fa;
            border: 3px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .option:hover {
            border-color: #667eea;
            background: rgba(102, 126, 234, 0.05);
            transform: translateX(5px);
        }
        
        .option input[type="radio"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        
        .option label {
            cursor: pointer;
            flex: 1;
            font-size: 1.05rem;
            color: #333;
            margin: 0;
        }
        
        .option.selected {
            border-color: #667eea;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        }
        
        /* Navigation */
        .navigation {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .btn-prev {
            background: #6c757d;
            color: white;
        }
        
        .btn-prev:hover {
            background: #5a6268;
            transform: translateX(-3px);
        }
        
        .btn-next {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-next:hover {
            transform: translateX(3px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #1dd1a1 0%, #10ac84 100%);
            color: white;
        }
        
        .btn-submit:hover {
            box-shadow: 0 5px 15px rgba(29, 209, 161, 0.4);
        }
        
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* Question Navigator */
        .question-navigator {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            margin-bottom: 25px;
        }
        
        .navigator-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }
        
        .navigator-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(50px, 1fr));
            gap: 10px;
        }
        
        .nav-dot {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: 600;
            color: #666;
            transition: all 0.3s;
        }
        
        .nav-dot:hover {
            border-color: #667eea;
        }
        
        .nav-dot.answered {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: #667eea;
        }
        
        .nav-dot.current {
            border-color: #ffd700;
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
        }
    </style>
</head>
<body>
    <div class="quiz-container">
        <!-- Header -->
        <div class="quiz-header">
            <div class="quiz-title">
                <i class="fas fa-graduation-cap"></i>
                <h1><?= htmlspecialchars($subject_name) ?> Quiz</h1>
            </div>
            <div class="quiz-info">
                <p><strong><?= $question_count ?></strong> Questions</p>
                <p><strong><?= $time_limit ?></strong> Minutes</p>
            </div>
        </div>

        <!-- Timer -->
        <div class="timer-card">
            <div class="timer" id="timer">
                <i class="fas fa-clock"></i>
                <span id="timer-display">00:00</span>
            </div>
        </div>

        <!-- Progress -->
        <div class="progress-card">
            <div class="progress-info">
                <span><strong>Progress:</strong> <span id="progress-text">0 of <?= $question_count ?></span></span>
                <span id="progress-percent">0%</span>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar-fill" id="progress-bar"></div>
            </div>
        </div>

        <!-- Question Navigator -->
        <div class="question-navigator">
            <div class="navigator-title">Question Navigator</div>
            <div class="navigator-grid" id="navigator-grid">
                <!-- Will be populated by JavaScript -->
            </div>
        </div>

        <!-- Quiz Form -->
        <form id="quizForm" method="POST" action="grade_quiz.php">
            <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">
            <input type="hidden" name="question_count" value="<?= $question_count ?>">
            <input type="hidden" name="time_limit" value="<?= $time_limit ?>">
            
            <?php foreach ($questions as $index => $question): ?>
                <div class="question-card <?= $index === 0 ? 'active' : '' ?>" data-index="<?= $index ?>">
                    <div class="question-number">Question <?= $index + 1 ?> of <?= $question_count ?></div>
                    <div class="question-text"><?= htmlspecialchars($question['question_text']) ?></div>
                    
                    <div class="options-container">
                        <?php foreach ($question['options'] as $key => $value): ?>
                            <div class="option" onclick="selectOption(this)">
                                <input type="radio" 
                                       name="question_<?= $question['question_id'] ?>" 
                                       value="<?= $key ?>" 
                                       id="q<?= $question['question_id'] ?>_<?= $key ?>"
                                       onchange="markAnswered()">
                                <label for="q<?= $question['question_id'] ?>_<?= $key ?>">
                                    <strong><?= $key ?>:</strong> <?= htmlspecialchars($value) ?>
                                </label>
                                <!-- Add hidden input for option text -->
                                <input type="hidden" name="question_<?= $question['question_id'] ?>_text_<?= $key ?>" value="<?= htmlspecialchars($value) ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <input type="hidden" name="correct_<?= $question['question_id'] ?>" value="<?= $question['correct_answer'] ?>">
                </div>
            <?php endforeach; ?>
        </form>

        <!-- Navigation -->
        <div class="navigation">
            <button type="button" class="btn btn-prev" id="prev-btn" onclick="previousQuestion()">
                <i class="fas fa-arrow-left"></i> Previous
            </button>
            <button type="button" class="btn btn-next" id="next-btn" onclick="nextQuestion()">
                Next <i class="fas fa-arrow-right"></i>
            </button>
            <button type="button" class="btn btn-submit" id="submit-btn" onclick="submitQuiz()" style="display: none;">
                <i class="fas fa-check"></i> Submit Quiz
            </button>
        </div>
    </div>

    <script>
        const totalQuestions = <?= $question_count ?>;
        const timeLimit = <?= $time_limit * 60 ?>; // Convert to seconds
        let currentQuestion = 0;
        let timeLeft = timeLimit;
        let answeredQuestions = new Set();
        let isSubmitting = false;

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            initNavigator();
            startTimer();
            updateProgress();
            updateNavigation();
        });

        // Initialize question navigator
        function initNavigator() {
            const grid = document.getElementById('navigator-grid');
            for (let i = 0; i < totalQuestions; i++) {
                const dot = document.createElement('div');
                dot.className = 'nav-dot';
                if (i === 0) dot.classList.add('current');
                dot.textContent = i + 1;
                dot.onclick = () => goToQuestion(i);
                grid.appendChild(dot);
            }
        }

        // Timer
        function startTimer() {
            const timerDisplay = document.getElementById('timer-display');
            const timerElement = document.getElementById('timer');
            
            const interval = setInterval(() => {
                if (timeLeft <= 0) {
                    clearInterval(interval);
                    if (!isSubmitting) {
                        isSubmitting = true;
                        alert('Time is up! Submitting your quiz...');
                        document.getElementById('quizForm').submit();
                    }
                } else {
                    const minutes = Math.floor(timeLeft / 60);
                    const seconds = timeLeft % 60;
                    timerDisplay.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                    
                    // Warning when 2 minutes left
                    if (timeLeft <= 120) {
                        timerElement.classList.add('warning');
                    }
                    
                    timeLeft--;
                }
            }, 1000);
        }

        // Select option
        function selectOption(element) {
            const container = element.closest('.question-card');
            container.querySelectorAll('.option').forEach(opt => opt.classList.remove('selected'));
            element.classList.add('selected');
            
            const radio = element.querySelector('input[type="radio"]');
            radio.checked = true;
            
            markAnswered();
        }

        function markAnswered() {
            // Mark as answered
            answeredQuestions.add(currentQuestion);
            updateNavigator();
            updateProgress();
        }

        // Navigation
        function nextQuestion() {
            if (currentQuestion < totalQuestions - 1) {
                goToQuestion(currentQuestion + 1);
            }
        }

        function previousQuestion() {
            if (currentQuestion > 0) {
                goToQuestion(currentQuestion - 1);
            }
        }

        function goToQuestion(index) {
            document.querySelectorAll('.question-card').forEach(card => card.classList.remove('active'));
            document.querySelectorAll('.nav-dot').forEach(dot => dot.classList.remove('current'));
            
            currentQuestion = index;
            document.querySelector(`[data-index="${index}"]`).classList.add('active');
            document.querySelectorAll('.nav-dot')[index].classList.add('current');
            
            updateNavigation();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function updateNavigation() {
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            const submitBtn = document.getElementById('submit-btn');
            
            prevBtn.style.display = currentQuestion === 0 ? 'none' : 'inline-flex';
            
            if (currentQuestion === totalQuestions - 1) {
                nextBtn.style.display = 'none';
                submitBtn.style.display = 'inline-flex';
            } else {
                nextBtn.style.display = 'inline-flex';
                submitBtn.style.display = 'none';
            }
        }

        function updateNavigator() {
            const dots = document.querySelectorAll('.nav-dot');
            answeredQuestions.forEach(index => {
                dots[index].classList.add('answered');
            });
        }

        function updateProgress() {
            const answered = answeredQuestions.size;
            const percent = Math.round((answered / totalQuestions) * 100);
            
            document.getElementById('progress-text').textContent = `${answered} of ${totalQuestions}`;
            document.getElementById('progress-percent').textContent = `${percent}%`;
            document.getElementById('progress-bar').style.width = `${percent}%`;
        }

        function submitQuiz() {
            const unanswered = totalQuestions - answeredQuestions.size;
            if (unanswered > 0) {
                if (!confirm(`You have ${unanswered} unanswered question(s). Are you sure you want to submit?`)) {
                    return;
                }
            }
            
            if (!isSubmitting) {
                isSubmitting = true;
                document.getElementById('quizForm').submit();
            }
        }

        // Handle page unload - only show warning if not submitting
        window.addEventListener('beforeunload', function(e) {
            if (!isSubmitting && answeredQuestions.size > 0) {
                e.preventDefault();
                e.returnValue = 'Are you sure you want to leave? Your quiz progress may not be saved.';
                return 'Are you sure you want to leave? Your quiz progress may not be saved.';
            }
        });

        // Remove warning when form is submitted
        document.getElementById('quizForm').addEventListener('submit', function() {
            isSubmitting = true;
            window.removeEventListener('beforeunload', beforeunloadHandler);
        });
    </script>
</body>
</html>