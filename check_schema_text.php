<?php
include 'db_connect.php';

function describeTable($conn, $tableName) {
    echo "Columns in $tableName:\n";
    $result = $conn->query("DESCRIBE $tableName");
    if($result) {
        while($row = $result->fetch_assoc()) {
            echo " - " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } else {
        echo "$tableName table not found.\n";
    }
    echo "\n";
}

describeTable($conn, 'quiz_results');
describeTable($conn, 'quiz_history_details');
describeTable($conn, 'student_dashboard_totals');
describeTable($conn, 'questions');
?>
