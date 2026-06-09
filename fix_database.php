<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';

// 1. Create Subjects Table
$sql = "CREATE TABLE IF NOT EXISTS subjects (
    subject_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    subject_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'subjects' check/create OK.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// 2. Seed Subjects (Truncate first to avoid duplicates/mismatches)
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$conn->query("TRUNCATE TABLE subjects");
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

$subjects = [
    "Operating System",
    "Database Management System",
    "Scripting Language",
    "Numerical Method",
    "Software Engineering"
];

foreach ($subjects as $name) {
    $sql = "INSERT INTO subjects (subject_name, description) VALUES ('$name', 'Course content for $name')";
    if ($conn->query($sql) === TRUE) {
        echo "Inserted: $name<br>";
    } else {
        echo "Error: " . $conn->error . "<br>";
    }
}

// 3. Create Dashboard Totals
$sql = "CREATE TABLE IF NOT EXISTS student_dashboard_totals (
    stat_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    student_id INT(11) NOT NULL,
    total_attempts INT(11) DEFAULT 0,
    total_correct INT(11) DEFAULT 0,
    total_incorrect INT(11) DEFAULT 0,
    UNIQUE KEY (student_id)
)";
if ($conn->query($sql) === TRUE) {
    echo "Table 'student_dashboard_totals' check/create OK.<br>";
} else {
    echo "Error: " . $conn->error . "<br>";
}

echo "<h1>Database Fix Complete!</h1>";
?>
