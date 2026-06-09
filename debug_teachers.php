<?php
// debug_teachers.php
include 'db_connect.php';

echo "<h1>Database Debugger</h1>";
echo "<p><strong>Connected to Database:</strong> " . (isset($dbname) ? $dbname : 'Unknown') . "</p>";
echo "<p><strong>Host Info:</strong> " . $conn->host_info . "</p>";

$sql = "SELECT * FROM teachers";
$result = $conn->query($sql);

echo "<h2>Teachers Table Data:</h2>";
if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Subject ID</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["teacher_id"] . "</td>";
        echo "<td>" . $row["username"] . "</td>";
        echo "<td>" . $row["email"] . "</td>";
        echo "<td>" . $row["subject_id"] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red;'>No teachers found in the table.</p>";
}

echo "<h2>Subjects Table Data:</h2>";
$res2 = $conn->query("SELECT * FROM subjects");
if ($res2->num_rows > 0) {
    while($r = $res2->fetch_assoc()) {
        echo $r['subject_id'] . ": " . $r['subject_name'] . "<br>";
    }
} else {
    echo "No subjects found.";
}

$conn->close();
?>
