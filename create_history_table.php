<?php
include 'db_connect.php';

// Create quiz_history_details table
$sql = "CREATE TABLE IF NOT EXISTS quiz_history_details (
    detail_id INT AUTO_INCREMENT PRIMARY KEY,
    result_id INT NOT NULL,
    question_text TEXT NOT NULL,
    user_answer VARCHAR(255),
    correct_answer VARCHAR(255),
    is_correct TINYINT(1),
    FOREIGN KEY (result_id) REFERENCES quiz_results(result_id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'quiz_history_details' created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}
?>
