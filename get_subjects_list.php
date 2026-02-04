<?php
include 'db_connect.php';
$res = $conn->query("SELECT * FROM subjects");
while($row = $res->fetch_assoc()) {
    echo $row['subject_id'] . ": " . $row['subject_name'] . "\n";
}
?>
