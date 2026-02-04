<?php
// Include the database connection file
include('../db_connect.php');


// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Sanitize input
    $username = $conn->real_escape_string(trim($_POST['username']));
    $firstName = $conn->real_escape_string(trim($_POST['firstName']));
    $lastName = $conn->real_escape_string(trim($_POST['lastName']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $age = isset($_POST['age']) ? (int)$_POST['age'] : 0;
    $school = $conn->real_escape_string(trim($_POST['school']));
    $password = $_POST['password'];
    $retypePassword = $_POST['confirmPassword']; // Check name attribute in HTML

    // 2. Validate
    $errors = [];
    if ($password !== $retypePassword) {
        $errors[] = "Passwords do not match!";
    }

    // Check availability
    $check = $conn->query("SELECT * FROM students WHERE username='$username' OR email='$email'");
    if ($check->num_rows > 0) {
        $errors[] = "Username or Email already taken.";
    }

    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo "<script>alert('$error'); window.history.back();</script>";
        }
        exit();
    }

    // 3. Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // 4. Insert into Database
    // Note: Assuming 'age' and 'school' columns exist in 'students' table as per quizhub.sql
    $sql = "INSERT INTO students (username, first_name, last_name, email, age, school, password) 
            VALUES ('$username', '$firstName', '$lastName', '$email', '$age', '$school', '$hashedPassword')";

    if ($conn->query($sql) === TRUE) {
        $new_id = $conn->insert_id;
        echo "<div style='padding: 20px; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; font-family: sans-serif; text-align: center; margin-top: 50px;'>";
        echo "<h1>🎉 Registration Successful!</h1>";
        echo "<p>Welcome, <strong>$firstName</strong>. You will be redirected to the login page in 3 seconds...</p>";
        echo "<script>
            setTimeout(function() {
                window.location.href = '../studentlogin/student-login.html';
            }, 3000);
        </script>";
        echo "<p><a href='../studentlogin/student-login.html'>Click here if not redirected</a></p>";
        echo "</div>";
    } else {
        echo "<div style='color: red; padding: 20px;'>";
        echo "<h1>Registration Failed</h1>";
        echo "<p>Error: " . $conn->error . "</p>";
        echo "<p>SQL: $sql</p>";
        echo "</div>";
    }

    $conn->close();
}
?>
