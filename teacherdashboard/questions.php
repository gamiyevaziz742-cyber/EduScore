<?php
session_start();
include('../db_connect.php');

// Security Check
if (!isset($_SESSION['teacher_logged_in']) || $_SESSION['teacher_logged_in'] !== true) {
    die("Access Denied. Please login as a teacher.");
}

// Get Subject ID from Session
if (!isset($_SESSION['subject_id']) || empty($_SESSION['subject_id'])) {
    die("Error: No subject assigned to your account. Please contact Admin.");
}

$quiz_id = $_SESSION['subject_id'];
$teacher_username = $_SESSION['teacher_username'];

// Fetch Subject Name
$subRes = $conn->query("SELECT subject_name FROM subjects WHERE subject_id = $quiz_id");
$subject_name = ($subRes->num_rows > 0) ? $subRes->fetch_assoc()['subject_name'] : "Unknown Subject";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add Question
    if (isset($_POST['add_question'])) {
        $question_text = mysqli_real_escape_string($conn, $_POST['question_text']);
        $option_a = mysqli_real_escape_string($conn, $_POST['option_a']);
        $option_b = mysqli_real_escape_string($conn, $_POST['option_b']);
        $option_c = mysqli_real_escape_string($conn, $_POST['option_c']);
        $option_d = mysqli_real_escape_string($conn, $_POST['option_d']);
        $correct_answer = mysqli_real_escape_string($conn, $_POST['correct_answer']);

        $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_answer) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $quiz_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer);
        
        if ($stmt->execute()) {
            $success_msg = "Question added successfully!";
        } else {
            $error_msg = "Error adding question: " . $stmt->error;
        }
        $stmt->close();
    }

    // Update Question
    if (isset($_POST['update_question'])) {
        $question_id = $_POST['question_id'];
        $question_text = mysqli_real_escape_string($conn, $_POST['question_text']);
        $option_a = mysqli_real_escape_string($conn, $_POST['option_a']);
        $option_b = mysqli_real_escape_string($conn, $_POST['option_b']);
        $option_c = mysqli_real_escape_string($conn, $_POST['option_c']);
        $option_d = mysqli_real_escape_string($conn, $_POST['option_d']);
        $correct_answer = mysqli_real_escape_string($conn, $_POST['correct_answer']);

        $checkStmt = $conn->prepare("SELECT quiz_id FROM questions WHERE question_id = ?");
        $checkStmt->bind_param("i", $question_id);
        $checkStmt->execute();
        $checkRes = $checkStmt->get_result();
        $qRow = $checkRes->fetch_assoc();
        
        if ($qRow && $qRow['quiz_id'] == $quiz_id) {
            $stmt = $conn->prepare("UPDATE questions SET question_text=?, option_a=?, option_b=?, option_c=?, option_d=?, correct_answer=? WHERE question_id=?");
            $stmt->bind_param("ssssssi", $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer, $question_id);
            
            if ($stmt->execute()) {
                $success_msg = "Question updated successfully!";
            } else {
                $error_msg = "Error updating question: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_msg = "Security Warning: You cannot edit questions from another subject!";
        }
        $checkStmt->close();
    }

    // Delete Question
    if (isset($_POST['delete_question'])) {
        $question_id = $_POST['question_id'];

        $checkStmt = $conn->prepare("SELECT quiz_id FROM questions WHERE question_id = ?");
        $checkStmt->bind_param("i", $question_id);
        $checkStmt->execute();
        $checkRes = $checkStmt->get_result();
        $qRow = $checkRes->fetch_assoc();

        if ($qRow && $qRow['quiz_id'] == $quiz_id) {
            $stmt = $conn->prepare("DELETE FROM questions WHERE question_id=?");
            $stmt->bind_param("i", $question_id);
            
            if ($stmt->execute()) {
                $success_msg = "Question deleted successfully!";
            } else {
                $error_msg = "Error deleting question: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_msg = "Security Warning: You cannot delete questions from another subject!";
        }
        $checkStmt->close();
    }
}

// Fetch all questions
$query = "SELECT * FROM questions WHERE quiz_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$result = $stmt->get_result();
$questions = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions - <?php echo htmlspecialchars($subject_name); ?></title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding-bottom: 50px;
        }
        
        /* Top Navigation */
        .top-nav {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo-section i {
            font-size: 2rem;
            color: #667eea;
        }
        
        .logo-section h2 {
            margin: 0;
            color: #333;
            font-weight: 700;
        }
        
        .nav-btn {
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
            background: #667eea;
            color: white;
        }
        
        .nav-btn:hover {
            background: #5568d3;
            color: white;
            text-decoration: none;
        }
        
        /* Main Container */
        .container-custom {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Page Header */
        .page-header {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            text-align: center;
        }
        
        .page-header h1 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .page-header p {
            color: #666;
            margin: 0;
        }
        
        /* Alert Messages */
        .alert-custom {
            border-radius: 10px;
            padding: 15px 20px;
            margin-bottom: 20px;
        }
        
        /* Add Question Form */
        .add-form-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .form-header h3 {
            margin: 0;
            color: #333;
        }
        
        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 12px 15px;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-add {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: 0.3s;
        }
        
        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        /* Questions List */
        .questions-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .question-item {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        
        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }
        
        .question-text {
            font-weight: 600;
            color: #333;
            font-size: 1.1rem;
            flex: 1;
        }
        
        .question-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-edit, .btn-delete {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: 0.3s;
        }
        
        .btn-edit {
            background: #4facfe;
            color: white;
        }
        
        .btn-edit:hover {
            background: #3a9be8;
        }
        
        .btn-delete {
            background: #f5576c;
            color: white;
        }
        
        .btn-delete:hover {
            background: #e04455;
        }
        
        .options-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .option {
            background: white;
            padding: 12px 15px;
            border-radius: 5px;
            font-size: 0.95rem;
            color: #555;
        }
        
        .correct-answer {
            background: #d4edda;
            padding: 10px 15px;
            border-radius: 5px;
            color: #155724;
            font-weight: 600;
            display: inline-block;
        }
        
        /* Modal Styling */
        .modal-content {
            border-radius: 15px;
        }
        
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        
        .no-questions {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        
        .no-questions i {
            font-size: 5rem;
            margin-bottom: 20px;
            color: #ddd;
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <div class="top-nav">
        <div class="logo-section">
            <i class="fas fa-question-circle"></i>
            <h2>Manage Questions</h2>
        </div>
        <a href="teacherdashboard.php" class="nav-btn">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="container-custom">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-book"></i> <?php echo htmlspecialchars($subject_name); ?></h1>
            <p>Manage your quiz questions - Add, Edit, or Delete</p>
        </div>

        <!-- Alert Messages -->
        <?php if(isset($success_msg)): ?>
            <div class="alert alert-success alert-custom">
                <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($error_msg)): ?>
            <div class="alert alert-danger alert-custom">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <!-- Add Question Form -->
        <div class="add-form-card">
            <div class="form-header">
                <h3><i class="fas fa-plus-circle"></i> Add New Question</h3>
            </div>
            <form method="post">
                <div class="form-group">
                    <label><strong>Question Text</strong></label>
                    <input type="text" name="question_text" class="form-control" placeholder="Enter your question" required>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Option A</strong></label>
                            <input type="text" name="option_a" class="form-control" placeholder="Option A" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Option B</strong></label>
                            <input type="text" name="option_b" class="form-control" placeholder="Option B" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Option C</strong></label>
                            <input type="text" name="option_c" class="form-control" placeholder="Option C" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Option D</strong></label>
                            <input type="text" name="option_d" class="form-control" placeholder="Option D" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label><strong>Correct Answer</strong></label>
                    <input type="text" name="correct_answer" class="form-control" placeholder="Enter the correct answer (must match one of the options)" required>
                </div>
                <button type="submit" name="add_question" class="btn-add">
                    <i class="fas fa-plus"></i> Add Question
                </button>
            </form>
        </div>

        <!-- Questions List -->
        <div class="questions-card">
            <div class="form-header">
                <h3><i class="fas fa-list"></i> All Questions (<?php echo count($questions); ?>)</h3>
            </div>
            
            <?php if (count($questions) > 0): ?>
                <?php foreach ($questions as $index => $question): ?>
                    <div class="question-item">
                        <div class="question-header">
                            <div class="question-text">
                                <?php echo ($index + 1) . ". " . htmlspecialchars($question['question_text']); ?>
                            </div>
                            <div class="question-actions">
                                <button type="button" class="btn-edit" data-toggle="modal" data-target="#updateModal<?php echo $question['question_id']; ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <form method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this question?');">
                                    <input type="hidden" name="question_id" value="<?php echo $question['question_id']; ?>">
                                    <button type="submit" name="delete_question" class="btn-delete">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="options-grid">
                            <div class="option"><strong>A:</strong> <?php echo htmlspecialchars($question['option_a']); ?></div>
                            <div class="option"><strong>B:</strong> <?php echo htmlspecialchars($question['option_b']); ?></div>
                            <div class="option"><strong>C:</strong> <?php echo htmlspecialchars($question['option_c']); ?></div>
                            <div class="option"><strong>D:</strong> <?php echo htmlspecialchars($question['option_d']); ?></div>
                        </div>
                        <div>
                            <span class="correct-answer">
                                <i class="fas fa-check-circle"></i> Correct: <?php echo htmlspecialchars($question['correct_answer']); ?>
                            </span>
                        </div>
                    </div>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="updateModal<?php echo $question['question_id']; ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Question</h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form method="post">
                                        <input type="hidden" name="question_id" value="<?php echo $question['question_id']; ?>">
                                        <div class="form-group">
                                            <label><strong>Question Text</strong></label>
                                            <input type="text" name="question_text" class="form-control" value="<?php echo htmlspecialchars($question['question_text']); ?>" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label><strong>Option A</strong></label>
                                                    <input type="text" name="option_a" class="form-control" value="<?php echo htmlspecialchars($question['option_a']); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label><strong>Option B</strong></label>
                                                    <input type="text" name="option_b" class="form-control" value="<?php echo htmlspecialchars($question['option_b']); ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label><strong>Option C</strong></label>
                                                    <input type="text" name="option_c" class="form-control" value="<?php echo htmlspecialchars($question['option_c']); ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label><strong>Option D</strong></label>
                                                    <input type="text" name="option_d" class="form-control" value="<?php echo htmlspecialchars($question['option_d']); ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label><strong>Correct Answer</strong></label>
                                            <input type="text" name="correct_answer" class="form-control" value="<?php echo htmlspecialchars($question['correct_answer']); ?>" required>
                                        </div>
                                        <button type="submit" name="update_question" class="btn-add">
                                            <i class="fas fa-save"></i> Update Question
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-questions">
                    <i class="fas fa-inbox"></i>
                    <h4>No Questions Yet</h4>
                    <p>Add your first question using the form above</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
