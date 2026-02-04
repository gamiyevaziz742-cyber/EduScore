<?php
include 'db_connect.php';
echo "--- Results ---\n";
$res = $conn->query("SELECT * FROM quiz_results ORDER BY taken_at DESC LIMIT 1");
print_r($res->fetch_assoc());
echo "\n--- Details ---\n";
$res = $conn->query("SELECT * FROM quiz_history_details ORDER BY detail_id DESC LIMIT 2");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
?>
