<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['student_id'])) {
    $res = $conn->query("SELECT student_id, username FROM students LIMIT 1");
    if ($row = $res->fetch_assoc()) {
        $_SESSION['student_id'] = $row['student_id'];
        $_SESSION['student_username'] = $row['username'];
    } else {
        die("No students found.");
    }
}

$studentId = $_SESSION['student_id'];
$subRes = $conn->query("SELECT subject_id FROM subjects LIMIT 1");
$subjectId = $subRes->fetch_assoc()['subject_id'];
$qRes = $conn->query("SELECT question_id FROM questions WHERE quiz_id = $subjectId LIMIT 1");
$questionId = $qRes->fetch_assoc()['question_id'];

// Mock the POST data including the new text fields
$_POST = [
    'quiz_id' => $subjectId,
    'question_count' => 1,
    'time_limit' => 10,
    'question_' . $questionId => 'D',
    'correct_' . $questionId => 'D',
    'question_' . $questionId . '_text_D' => 'Even'
];

$_SERVER['REQUEST_METHOD'] = 'POST';
include 'studentdashboard/grade_quiz.php';

echo "\n--- Verification ---\n";
$resId = $conn->insert_id;
$detailRes = $conn->query("SELECT * FROM quiz_history_details WHERE result_id = $resultId ORDER BY detail_id DESC LIMIT 1");
$detail = $detailRes->fetch_assoc();
echo "Stored Answer: " . $detail['user_answer'] . "\n";
echo "Correct: " . $detail['is_correct'] . "\n";
?>
