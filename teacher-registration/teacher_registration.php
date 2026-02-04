<?php
include '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Capture Data
    $firstName = $conn->real_escape_string($_POST['firstName']);
    $lastName = $conn->real_escape_string($_POST['lastName']);
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $contact = $conn->real_escape_string($_POST['contactNumber']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $qualification = $conn->real_escape_string($_POST['qualifications']);
    $institution = $conn->real_escape_string($_POST['institution']);
    $city = $conn->real_escape_string($_POST['city']);
    $subjectName = $conn->real_escape_string($_POST['subject']);
    $password = $_POST['password'];

    // 2. Hash Password
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // 3. Resolve Subject ID
    $subject_id = 'NULL';
    if (!empty($subjectName)) {
        $res = $conn->query("SELECT subject_id FROM subjects WHERE subject_name = '$subjectName'");
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $subject_id = $row['subject_id'];
        }
    }

    // 4. Insert
    $sql = "INSERT INTO teachers (
        username, email, password, first_name, last_name, 
        contact_no, gender, qualification, institution, city, subject_id, created_at
    ) VALUES (
        '$username', '$email', '$hashed', '$firstName', '$lastName', 
        '$contact', '$gender', '$qualification', '$institution', '$city', $subject_id, NOW()
    )";

    if ($conn->query($sql) === TRUE) {
        // Success Page
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>Registration Successful</title>
            <meta http-equiv="refresh" content="3;url=../teacher-login/teacher-login.html">
            <style>
                body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #eef1f5; }
                .box { background: white; padding: 40px; border-radius: 15px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
                h1 { color: #2ecc71; }
                .spinner { margin: 20px auto; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; animation: spin 1s linear infinite; }
                @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
            </style>
        </head>
        <body>
            <div class="box">
                <h1>🎉 Registration Successful!</h1>
                <p>Teacher account created for <strong>'.$username.'</strong></p>
                <div class="spinner"></div>
                <p>Redirecting to Login...</p>
                <p><small><a href="../teacher-login/teacher-login.html">Click here if not redirected</a></small></p>
            </div>
        </body>
        </html>';
    } else {
        // Failure
        echo '<!DOCTYPE html>
        <html>
        <body>
            <div style="background: #fff0f0; border: 2px solid red; padding: 20px; font-family: monospace;">
                <h1 style="color: red;">Registration Failed</h1>
                <p><strong>Error:</strong> ' . $conn->error . '</p>
                <p><strong>Query:</strong> ' . $sql . '</p>
                <button onclick="window.history.back()">Go Back and Fix</button>
            </div>
        </body>
        </html>';
    }
}
?>
