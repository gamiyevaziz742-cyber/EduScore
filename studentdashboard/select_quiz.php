<?php
session_start();
include '../db_connect.php';

// Check login
if (!isset($_SESSION['student_username'])) {
    header("Location: ../studentlogin/student-login.html");
    exit();
}

// Get subject_id from URL parameter (dashboard बाट आएको)
if (!isset($_GET['subject_id'])) {
    header("Location: ../dashboard.php");
    exit();
}

$subject_id = $_GET['subject_id'];

// Fetch subject details and question count
$subject_query = "SELECT s.subject_id, s.subject_name, COUNT(q.question_id) as question_count 
                  FROM subjects s 
                  LEFT JOIN questions q ON s.subject_id = q.quiz_id 
                  WHERE s.subject_id = ? 
                  GROUP BY s.subject_id";
$stmt = $conn->prepare($subject_query);
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$subject_result = $stmt->get_result();

if ($subject_result->num_rows == 0) {
    header("Location: ../dashboard.php");
    exit();
}

$subject = $subject_result->fetch_assoc();
$totalQuestions = $subject['question_count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Quiz Duration - Quizhub</title>
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
            padding: 40px 20px;
        }
        
        .container-custom {
            max-width: 900px;
            margin: 0 auto;
        }
        
        .page-header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            margin-bottom: 30px;
            text-align: center;
        }
        
        .page-header h1 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .page-header p {
            color: #666;
            margin: 0;
        }
        
        .subject-info {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            margin-bottom: 30px;
            text-align: center;
            border-left: 5px solid #667eea;
        }
        
        .subject-info h3 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .subject-info p {
            color: #666;
            margin: 0;
            font-size: 1.1rem;
        }
        
        .selection-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
            padding: 40px;
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .option-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .quiz-option {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 30px 20px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            border: 3px solid transparent;
        }
        
        .quiz-option:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }
        
        .quiz-option.selected {
            border-color: #ffd700;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.5);
        }
        
        .quiz-option i {
            font-size: 2rem;
            margin-bottom: 15px;
        }
        
        .quiz-option h3 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .quiz-option p {
            margin: 0;
            opacity: 0.9;
        }
        
        .quiz-option small {
            display: block;
            margin-top: 10px;
            opacity: 0.8;
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            display: none;
        }
        
        .loading-spinner {
            width: 80px;
            height: 80px;
            border: 5px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            100% { transform: rotate(360deg); }
        }
        
        .loading-text {
            color: white;
            margin-top: 20px;
            font-size: 1.2rem;
            text-align: center;
        }
        
        .btn-back {
            background: #6c757d;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            position: absolute;
            top: 20px;
            left: 20px;
        }
        
        .btn-back:hover {
            background: #5a6268;
        }
        
        .no-questions {
            text-align: center;
            grid-column: 1/-1;
            padding: 40px;
        }
        
        .no-questions i {
            font-size: 3rem;
            color: #ffc107;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
        <div class="loading-text">Starting your quiz... Please wait</div>
    </div>

    <button class="btn-back" onclick="window.history.back()">
        <i class="fas fa-arrow-left"></i> Back
    </button>

    <div class="container-custom">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-clock"></i> Select Quiz Duration</h1>
            <p>Choose how long you want to practice</p>
        </div>

        <!-- Subject Info -->
        <div class="subject-info">
            <h3>Subject: <?= htmlspecialchars($subject['subject_name']) ?></h3>
            <p><?= $totalQuestions ?> questions available</p>
        </div>

        <!-- Selection Card -->
        <div class="selection-card">
            <h3 class="section-title">Choose Quiz Duration</h3>
            <div class="option-grid" id="options-container">
                <!-- Options will be dynamically inserted here -->
            </div>
        </div>
    </div>

    <script>
        const totalQuestions = <?= $totalQuestions ?>;
        const subjectId = <?= $subject_id ?>;
        let isProcessing = false;

        // Generate quiz options based on available questions
        function generateQuizOptions() {
            const optionsContainer = document.getElementById('options-container');
            optionsContainer.innerHTML = '';
            
            const options = [
                { questions: Math.min(50, totalQuestions), time: 30, icon: 'fa-fire', label: 'Full Practice' },
                { questions: Math.min(25, totalQuestions), time: 15, icon: 'fa-bolt', label: 'Quick Session' },
                { questions: Math.min(15, totalQuestions), time: 10, icon: 'fa-clock', label: 'Short Test' },
                { questions: Math.min(10, totalQuestions), time: 5, icon: 'fa-hourglass-half', label: 'Quick Test' }
            ];
            
            options.forEach(opt => {
                if (opt.questions > 0) {
                    const optionDiv = document.createElement('div');
                    optionDiv.className = 'quiz-option';
                    optionDiv.dataset.questions = opt.questions;
                    optionDiv.dataset.time = opt.time;
                    optionDiv.innerHTML = `
                        <i class="fas ${opt.icon}"></i>
                        <h3>${opt.questions}</h3>
                        <p>${opt.label}</p>
                        <small>${opt.time} minutes</small>
                    `;
                    
                    optionDiv.addEventListener('click', function() {
                        if (!isProcessing) {
                            selectAndStartQuiz(this);
                        }
                    });
                    
                    optionsContainer.appendChild(optionDiv);
                }
            });
            
            // If no questions available
            if (totalQuestions === 0) {
                optionsContainer.innerHTML = `
                    <div class="no-questions">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h3 style="color: #666;">No Questions Available</h3>
                        <p style="color: #999;">This subject doesn't have any questions yet.</p>
                        <button class="btn-back" onclick="window.history.back()" style="position: relative; top: 0; left: 0; margin-top: 20px;">
                            <i class="fas fa-arrow-left"></i> Go Back
                        </button>
                    </div>
                `;
            }
        }

        function selectAndStartQuiz(element) {
            if (isProcessing) return;
            
            isProcessing = true;
            
            // Show loading overlay
            document.getElementById('loadingOverlay').style.display = 'flex';
            
            // Remove previous selection
            document.querySelectorAll('.quiz-option').forEach(opt => opt.classList.remove('selected'));
            
            // Select this option
            element.classList.add('selected');
            
            const selectedOption = {
                questions: element.dataset.questions,
                time: element.dataset.time
            };
            
            // Wait for animation to complete, then start quiz
            setTimeout(() => {
                startQuiz(selectedOption);
            }, 500);
        }

        function startQuiz(selectedOption) {
            console.log('Starting quiz with:', {
                subject: subjectId,
                questions: selectedOption.questions,
                time: selectedOption.time
            });
            
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'fetch_questions.php';
            
            const subjectInput = document.createElement('input');
            subjectInput.type = 'hidden';
            subjectInput.name = 'subject';
            subjectInput.value = subjectId;
            
            const questionsInput = document.createElement('input');
            questionsInput.type = 'hidden';
            questionsInput.name = 'question_count';
            questionsInput.value = selectedOption.questions;
            
            const timeInput = document.createElement('input');
            timeInput.type = 'hidden';
            timeInput.name = 'time_limit';
            timeInput.value = selectedOption.time;
            
            form.appendChild(subjectInput);
            form.appendChild(questionsInput);
            form.appendChild(timeInput);
            
            console.log('Form inputs:', {
                subject: subjectInput.value,
                question_count: questionsInput.value,
                time_limit: timeInput.value
            });
            
            document.body.appendChild(form);
            console.log('Submitting form...');
            form.submit();
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', generateQuizOptions);
    </script>
</body>
</html>