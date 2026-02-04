<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connect.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully to " . $conn->host_info . "<br>";

echo "<h2>Subjects Check:</h2>";
$result = $conn->query("SELECT * FROM subjects");
if (!$result) {
    echo "Query Error: " . $conn->error;
} else {
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "ID: " . $row["subject_id"]. " - " . $row["subject_name"]. "<br>";
        }
    } else {
        echo "0 subjects found. Table is empty.<br>";
        // Seed Button
        echo '<form method="post"><button name="seed">Seed Data</button></form>';
    }
}

if (isset($_POST['seed'])) {
    $sql = "INSERT INTO subjects (subject_name, description) VALUES 
    ('Operating System', 'OS Concepts'),
    ('Database Management System', 'DBMS Concepts'),
    ('Scripting Language', 'JS, PHP, etc'),
    ('Numerical Method', 'Maths'),
    ('Software Engineering', 'SDLC')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Seeding successful! Refresh page.";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
