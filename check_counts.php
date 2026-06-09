<?php
include 'db_connect.php';
$res = $conn->query("SELECT s.subject_name, COUNT(q.question_id) as c FROM subjects s LEFT JOIN questions q ON s.subject_id = q.quiz_id GROUP BY s.subject_id");
echo "<pre>";
while($row = $res->fetch_assoc()) {
    echo $row['subject_name'] . ": " . $row['c'] . "\n";
}
echo "</pre>";
?>
