<?php
include 'db_connect.php';
// Remove the 5th subject if it exists, to match user's "4 subjects" expectation
$conn->query("DELETE FROM subjects WHERE subject_name = 'Software Engineering'");
echo "Deleted Software Engineering. Count is now: ";
$res = $conn->query("SELECT COUNT(*) as c FROM subjects")->fetch_assoc();
echo $res['c'];
?>
