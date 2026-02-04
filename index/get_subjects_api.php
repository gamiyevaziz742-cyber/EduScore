<?php
// get_subjects_api.php
// Returns JSON list of subjects
include('../db_connect.php');

$result = $conn->query("SELECT subject_id, subject_name FROM subjects ORDER BY subject_name ASC");

$subjects = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($subjects);
$conn->close();
?>
