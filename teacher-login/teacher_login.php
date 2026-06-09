<?php
session_start();
// Include the database connection file
require '../db_connect.php'; 

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize input
    $username = $conn->real_escape_string(trim($_POST['username']));
    $password = $_POST['password'];

    // Prepare and execute SQL statement
    // We select basic info to verify login
    $sql = "SELECT teacher_id, username, password, first_name, last_name, subject_id FROM teachers WHERE username = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // Verify password using password_verify (matches password_hash)
            if (password_verify($password, $row['password'])) {
                // Login Success
                // Store teacher info in session
                $_SESSION['teacher_logged_in'] = true;
                $_SESSION['teacher_id'] = $row['teacher_id'];
                $_SESSION['teacher_username'] = $row['username'];
                $_SESSION['teacher_name'] = $row['first_name'] . ' ' . $row['last_name'];
                $_SESSION['subject_id'] = $row['subject_id']; // Store subject for specialized access
                
                // Regenerate session ID for security
                session_regenerate_id(true);

                // Redirect to teacher dashboard
                header("Location: ../teacherdashboard/teacherdashboard.php");
                exit();
            } else {
                echo "<script>alert('Invalid password!'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert('Invalid username!'); window.history.back();</script>";
        }
        $stmt->close();
    } else {
        echo "Database error: " . $conn->error;
    }

    $conn->close();
}
?>
