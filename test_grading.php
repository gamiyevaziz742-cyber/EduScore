<?php
session_start();
include 'db_connect.php';

// Simulate a student login if not already logged in
if (!isset($_SESSION['student_id'])) {
    $res = $conn->query("SELECT student_id, username FROM students LIMIT 1");
    if ($row = $res->fetch_assoc()) {
        $_SESSION['student_id'] = $row['student_id'];
        $_SESSION['student_username'] = $row['username'];
    } else {
        die("No students found in DB to simulate test.");
    }
}

$studentId = $_SESSION['student_id'];

// Get a subject and some questions
$subRes = $conn->query("SELECT subject_id FROM subjects LIMIT 1");
if (!$subRow = $subRes->fetch_assoc()) die("No subjects found.");
$subjectId = $subRow['subject_id'];

$qRes = $conn->query("SELECT question_id, correct_answer FROM questions WHERE quiz_id = $subjectId LIMIT 2");
$questions = [];
while ($q = $qRes->fetch_assoc()) $questions[] = $q;

if (count($questions) < 2) die("Need at least 2 questions in subject $subjectId for test.");

// Prepare POST data
$_POST['quiz_id'] = $subjectId;
$_POST['question_count'] = 2;
$_POST['time_limit'] = 10;

// Correct answer for Q1, Incorrect for Q2
$_POST['question_' . $questions[0]['question_id']] = 'A'; // Assuming A is correct for this test shim
$_POST['correct_' . $questions[0]['question_id']] = 'A';

$_POST['question_' . $questions[1]['question_id']] = 'B';
$_POST['correct_' . $questions[1]['question_id']] = 'C'; 

// Include the grading script
// Note: grade_quiz.php uses session_start and include '../db_connect.php'
// We'll mimic the environment
$_SERVER['REQUEST_METHOD'] = 'POST';
include 'studentdashboard/grade_quiz.php';

echo "\n--- Simulation Complete ---\n";
echo "Check output above for any errors.\n";
?>
