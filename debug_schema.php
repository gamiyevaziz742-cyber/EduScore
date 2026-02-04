<?php
include 'db_connect.php';
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    echo $row[0] . "<br>";
}
echo "<hr>";
echo "Columns in teachers:<br>";
$result = $conn->query("DESCRIBE teachers");
if($result) {
    echo "<table border=1><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        foreach($row as $cell) echo "<td>$cell</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "teachers table not found.";
}
?>
