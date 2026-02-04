<?php
include 'db_connect.php';

function describeTable($conn, $tableName) {
    echo "<h3>Columns in $tableName:</h3>";
    $result = $conn->query("DESCRIBE $tableName");
    if($result) {
        echo "<table border=1><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach($row as $cell) echo "<td>$cell</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "$tableName table not found.<br>";
    }
}

describeTable($conn, 'quiz_results');
describeTable($conn, 'quiz_history_details');
describeTable($conn, 'student_dashboard_totals');
describeTable($conn, 'questions');
?>
