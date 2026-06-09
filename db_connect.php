<?php
$servername = "localhost";
$username = "quizuser";
$password = "123456";
$dbname = "quizhub";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
