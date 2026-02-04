-- Database: quizhub
CREATE DATABASE IF NOT EXISTS quizhub;
USE quizhub;

-- ==========================================
-- 1. ADMINS TABLE
-- Stores admin credentials
-- ==========================================
CREATE TABLE IF NOT EXISTS admins (
    admin_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Ensure this stores the HASHED password
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- 2. SUBJECTS TABLE
-- Stores the list of available subjects (Math, Science, etc.)
-- Admin can add/delete rows here.
-- ==========================================
CREATE TABLE IF NOT EXISTS subjects (
    subject_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    subject_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- 3. TEACHERS TABLE
-- Aligned with teacher-registration.html
-- ==========================================
CREATE TABLE IF NOT EXISTS teachers (
    teacher_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    
    -- Login Info
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    
    -- Personal Info
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    contact_no VARCHAR(20),      -- Matches 'contactNumber' from form
    gender VARCHAR(15),          -- Matches 'gender' from form
    
    -- Professional Info
    qualification VARCHAR(100),  -- Matches 'qualifications' from form
    institution VARCHAR(100),    -- Matches 'institution' from form
    city VARCHAR(50),            -- Matches 'city' from form
    
    -- Role / Assignment
    subject_id INT(11),          -- The subject this teacher is responsible for
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Link to Subjects Table
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id) ON DELETE SET NULL
);

-- ==========================================
-- 4. STUDENTS TABLE
-- Aligned with student-registration.html
-- ==========================================
CREATE TABLE IF NOT EXISTS students (
    student_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    
    -- Login Info
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    
    -- Personal Info
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    age INT(3),                  -- Matches 'age' from form
    
    -- Academic Info
    school VARCHAR(100),         -- Matches 'school' from form
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- 5. QUESTIONS TABLE
-- Stores quiz questions linked to a Subject
-- ==========================================
CREATE TABLE IF NOT EXISTS questions (
    question_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    quiz_id INT(11) NOT NULL,    -- Maps to subject_id (e.g., Math ID)
    
    question_text TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_answer VARCHAR(255) NOT NULL, -- Should store the value (e.g., "Paris") or key ("A") depending on logic
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (quiz_id) REFERENCES subjects(subject_id) ON DELETE CASCADE
);

-- ==========================================
-- 6. STUDENT DASHBOARD TOTALS
-- Stores aggregate stats for the dashboard display
-- ==========================================
CREATE TABLE IF NOT EXISTS student_dashboard_totals (
    stat_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    student_id INT(11) NOT NULL,
    
    total_attempts INT(11) DEFAULT 0,
    total_correct INT(11) DEFAULT 0,
    total_incorrect INT(11) DEFAULT 0,
    total_unanswered INT(11) DEFAULT 0,
    
    UNIQUE KEY (student_id),
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE
);

-- ==========================================
-- 7. QUIZ RESULTS
-- Stores the summary of a single quiz attempt (e.g., score, status)
-- ==========================================
CREATE TABLE IF NOT EXISTS quiz_results (
    result_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    student_id INT(11) NOT NULL,
    quiz_id INT(11) NOT NULL,    -- Subject/Quiz taken
    
    total_attempts INT(11) NOT NULL, -- Total questions in the quiz
    correct_answers INT(11) NOT NULL,
    incorrect_answers INT(11) NOT NULL,
    status VARCHAR(10) DEFAULT 'Fail', -- Pass/Fail
    
    taken_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (quiz_id) REFERENCES subjects(subject_id) ON DELETE CASCADE
);

-- ==========================================
-- 8. STUDENT ANSWERS (NEW)
-- Stores detailed history of every answer chosen
-- ==========================================
CREATE TABLE IF NOT EXISTS student_answers (
    answer_id INT(11) AUTO_INCREMENT PRIMARY KEY,
    result_id INT(11) NOT NULL,
    student_id INT(11) NOT NULL,
    question_id INT(11) NOT NULL,
    
    student_choice VARCHAR(255), -- The option the student selected
    is_correct BOOLEAN,          -- 1 if correct, 0 if wrong
    
    FOREIGN KEY (result_id) REFERENCES quiz_results(result_id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(question_id) ON DELETE CASCADE
);

-- ==========================================
-- DEFAULT DATA
-- ==========================================

-- Default Subjects
INSERT IGNORE INTO subjects (subject_name, description) VALUES 
('Mathematics', 'Algebra, Geometry, and Calculus'),
('Science', 'Physics, Chemistry, and Biology'),
('English', 'Grammar, Literature, and Vocabulary'),
('History', 'World History and Civilizations'),
('Computer Science', 'Programming, Algorithms, and Systems');

-- Note regarding Admin:
-- Use the Admin Registration page or insert manually using a bcrypt hash generator.
-- Example: INSERT INTO admins (username, password) VALUES ('admin', '$2y$10$...');
