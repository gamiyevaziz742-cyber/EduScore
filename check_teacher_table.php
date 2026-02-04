<?php
include 'db_connect.php';
$res = $conn->query("SHOW CREATE TABLE teachers");
if($res) {
    echo "<pre>";
    print_r($res->fetch_assoc());
    echo "</pre>";
} else {
    echo "Error: " . $conn->error;
}
?>
