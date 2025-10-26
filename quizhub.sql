-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 04, 2025 at 08:16 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quizhub`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(4, 'admin', 'sita@gmail.com', '$2y$10$ogQZllYCyx9zefLBBl/8d./2qvnBxSaktPrDhBiDfZE1wwOSuaC0u', '2024-12-08 12:01:27');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(7, 'Sirjana Basnet', 'sirjanab@gmail.com', 'Numerical Method', 'Numerical method not methods', '2025-02-24 04:44:08');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `question_id` int(11) NOT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `question_text` text NOT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) NOT NULL,
  `option_d` varchar(255) NOT NULL,
  `correct_answer` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`question_id`, `quiz_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`, `created_at`) VALUES
(43, 2, 'Which of following is the process of decomposing unsatisfactory relations by breaking up their attributes into smaller relations?', 'Neutralization', 'Breakdown', 'Decomposition ', 'Normalization', 'Normalization', '2024-10-07 11:37:01'),
(44, 2, 'Which of the following abstraction level hides the details of data types?', 'View level', 'Physcial level', 'Logical level', 'Abstract level', 'Logical level', '2024-10-07 11:42:21'),
(45, 2, 'In relational algebra, which of the following is used to select certain columns from relation?', 'Select', 'Project', 'Certain ', 'Join', 'Project', '2024-10-07 11:43:09'),
(46, 2, 'In ER diagram, which of the following relationship is used to recognize weak entity set?', 'Non-identifying Realtionship', 'Identifying Relationship', 'Weak Relationship', 'N-ary Relationship', 'Identifying Relationship', '2024-10-07 11:44:26'),
(47, 2, 'To use union, intersection operation between any two relations, they should be.......', 'Union Compatible ', 'Intersection Compatible', 'Both a and b', 'Derive relations', 'Union Compatible ', '2024-10-07 11:45:40'),
(48, 2, 'Two schedules are said to be ................. if the order of any two conflicting operations is the same in both schedules.', 'Conflict Serialization', 'Serial Schedule', 'Conflict Equivalent', 'Ordered Schedule ', 'Conflict Equivalent', '2024-10-07 11:47:10'),
(49, 2, 'Which of the following are the types of ordered indices?', 'Dense and Spares', 'Spares and Granular', 'Compact and Close', 'Desnse and Complex', 'Dense and Spares', '2024-10-07 11:48:19'),
(50, 2, 'Which of the following is used together with the select clause in SQL to uniquely list the values?', 'Unique', 'Once', 'Single', 'Distinct', 'Distinct', '2024-10-07 11:49:16'),
(51, 2, 'Which of the following SQL query is used to change the schema of an existing table in database?', 'Create', 'Update', 'Alter', 'Drop', 'Alter', '2024-10-07 11:50:09'),
(52, 2, '............... defines that an entity can be a member of at most one of the subclass of the specialization', 'Dis-jointness', ' Super class', 'Overlap ', 'Specific Subclass', 'Dis-jointness', '2024-10-07 11:51:29'),
(54, 5, 'Which granularity level of testing checks the behaviour of module cooperation?', 'Unit Testing ', 'Integration Testing', 'Acceptance testing ', 'Regresssion Testing', 'Integration Testing', '2024-10-07 11:57:11'),
(55, 5, 'user requirements are expressed as...... in extreme programming ', 'Implementation task', 'Functionalities', 'Scenarios', 'None of the above', 'Scenarios', '2024-10-07 11:57:56'),
(56, 5, 'In which phase of requirement elicitation process conflict are resloved?', 'Requirement Discovery', 'Requirement classifciation and organization ', 'Requirement Requirements prioritization and negotation.', 'Requirement Specification', 'Requirement Requirements prioritization and negotation.', '2024-10-07 12:00:42'),
(57, 5, 'Which of the following pattern is the basis of interaction management in many web-based system?', 'Layered pattern', 'MVC pattern ', 'Repository pattern', 'Generic pattern', 'MVC pattern ', '2024-10-07 12:03:27'),
(58, 5, 'Which of the following is not the principle of agile development?', 'Customer Involvement', 'Maintain Simplicity', 'Incremental Development', 'Version Control', 'Version Control', '2024-10-07 12:04:18'),
(59, 5, 'The relationship of data elements within a module is called.....', 'Modularity', 'Coupling', 'Cohesion', 'Granularity', 'Cohesion', '2024-10-07 12:07:56'),
(60, 5, 'Which of the following is concerned with making the system more maintainable?', 'Software maintenance', 'Configuation management', 'Software reengineering', 'Software refactoring', 'Software reengineering', '2024-10-07 12:09:24'),
(61, 5, 'Which of the following is not configuration management activity?', 'Change management', 'Release management', 'Version management', 'Risk management', 'Risk management', '2024-10-07 12:10:55'),
(62, 5, 'White box testing is sometimes called.', 'Functional testing', 'loop testing', 'Behavioral testing', 'Graph based testinhg', 'Behavioral testing', '2024-10-07 12:11:25'),
(81, 2, 'The database is said to be.......if various copies of the same data may no longer agree?', 'Inconsistent', 'specific', 'consistent', 'redundant', 'consistent', '2024-10-11 20:08:00'),
(82, 2, '....................captures data types, relationship and constraints on data.', 'Database instance', 'Database table', 'Database schema', 'Normalization', 'Database schema', '2024-10-11 20:08:36'),
(83, 2, 'In ER diagram, the derived attributes are represented using .......shape.', 'solid oval ', 'double oval', 'rectangle', 'dotted oval', 'dotted oval', '2024-10-11 20:09:28'),
(84, 2, 'If a function dependency (FD) X->Y holds, where Y is a subset of X, then it is called a..........', 'subset FD', 'trival FD', 'non-trival FD', 'least FD', 'non-trival FD', '2024-10-11 20:10:36'),
(85, 2, 'Which of the following is not binary operator in relational algebra?', 'Union', 'Natural Join', 'Project', 'Join', 'Project', '2024-10-11 20:11:10'),
(86, 2, 'Extended ER diagram uses the concept of ............', 'Inherence', 'Structure', 'Polymorphism', 'Object', 'Inherence', '2024-10-11 20:11:40'),
(88, 2, 'Which of following is not a conflicting operation set in a schedule of transcations T1 and T2?', 'W1(x), W2(x)', 'W1(x), W2(x)', 'R1(x)W2(x)', 'R1(x),R2(x)', 'R1(x),R2(x)', '2024-10-11 20:15:00'),
(89, 2, 'Database security is guaranteed if ................. are preserved.', 'Confidentiality, integrity, secrecy', 'confidentiality, integrity and availability', 'Availability, integrity, and accessibility', 'confidentiality, inegrity and accessibility', 'confidentiality, integrity and availability', '2024-10-11 20:26:07'),
(90, 2, '............ is an index where index entry appears for every search key,', 'Dense index', 'Spare index', 'Clustering index', 'Secondary Index', 'Dense index', '2024-10-11 20:26:50'),
(91, 5, 'Where is the prototyping model of software development well suited?', 'When requirements are well defined.', 'For projects with large development team.', 'When customer can not define requirement clearly.', 'None of the above', 'When customer can not define requirement clearly.', '2024-10-12 14:56:32'),
(92, 5, 'Which granularity level of testing checks the inteface mismatch error?', 'Unit Testing ', 'Integration Testing', 'Acceptance testing ', 'Regresssion Testing', 'Integration Testing', '2024-10-12 14:57:39'),
(93, 5, 'Scrum sprints', 'Is schedule', 'Is Duration', 'Should be tested so that it releases potentially shippable increments', 'Both ii and iii', 'Is Duration', '2024-10-12 14:59:02'),
(94, 5, 'Which is not the step of requirement engineering process?', 'Requirement Desgin', 'Feasibility study', 'Requirement Elicitation', 'Requirement Validation', 'Requirement Desgin', '2024-10-12 14:59:48'),
(95, 5, 'Which of the following pattern is the basis if the security is the critical non functional issue? ', 'Layered pattern', 'MVC pattern ', 'Repository pattern', 'Pipe and filter', 'Layered pattern', '2024-10-12 15:01:07'),
(96, 5, 'Which of the following is not concerned with UI desgin?', 'User and task analysis', 'Interface Construction', 'Cost Estimation', 'Interface validation', 'Cost Estimation', '2024-10-12 15:02:09'),
(97, 5, 'The relationship of data elements within a module is called.....', 'Modularity', 'Coupling', 'Cohesion', 'Granularity', 'Coupling', '2024-10-12 15:02:41'),
(98, 5, 'What are four dimensions of dependability?', 'Usability, Reliablitiy, Security, Flexibility', 'Availability, Reliability, Sustainability, Security', 'Availability, Reliability,  Security, Saftey', 'Security, Saftey, testability, Usability', 'Availability, Reliability,  Security, Saftey', '2024-10-12 15:04:52'),
(99, 5, 'Which of the following is configuration management activity?', 'People management', 'Resource management', 'Version management', 'Risk management', 'Version management', '2024-10-12 15:05:53'),
(100, 5, 'Black box testing is sometimes called.', 'Functional testing', 'loop testing', 'Behavioral testing', 'Graph based testing', 'Functional testing', '2024-10-12 15:06:27'),
(130, 5, 'What is the first step in the software development lifecycle?', 'System Design', 'Coding', 'System Testing', 'Preliminary Investigation and Analysis', 'Preliminary Investigation and Analysis', '2024-11-04 11:58:24'),
(131, 5, 'Which of the following word correctly summarized the importance of software Design?', 'Quality', 'Complexity', 'Efficiency', 'Accuracy', 'Quality', '2024-11-04 12:00:46'),
(132, 5, 'What does the study of existing system refer to?', 'Details of DFD', 'Feasibility Study', 'System Analysis', 'System Planning', 'System Analysis', '2024-11-04 12:02:26'),
(133, 5, 'What does a directed arc or line signify?', 'Data Flow', 'Data Process', 'Data Stores', 'None of the above', 'Data Flow', '2024-11-04 12:04:12'),
(134, 5, 'what is software?', 'set of programs', 'It\\\'s a subject', 'procedure of system analysis', 'system design process', 'set of programs', '2024-11-05 11:37:58'),
(142, 1, 'Which one of the following can cause a blunders in system?', 'Human Imperfection', 'Numerical Methods', 'Computing Methods', 'Measuring Methods', 'Human Imperfection', '2025-02-23 21:09:19'),
(143, 1, 'Which one of the following be possible solution of system of linear equations with equations: 2x-y=5 and 3x-3/2y=4.', 'Unique Solution', 'No solution', 'Infinite solution', 'III-condition', 'III-condition', '2025-02-23 21:10:32'),
(144, 1, 'In which method improved root x1 is calculated as, x0-f(x0)/f\\\'(x0)', 'Bisection method', 'Secant method', 'Newton-Raphson method', 'False Point method', 'Newton-Raphson method', '2025-02-23 21:11:24'),
(145, 1, 'Which one of the following is not an open end method(extrapolation method) of solving non-linear equations?', 'Bisection method', ' Newton-Raphson Method', 'Secant Method', 'Fixed Point Method', 'Fixed Point Method', '2025-02-23 21:12:10'),
(146, 1, 'Which one of the following is an iteration method of solving the system of linear equations?', 'Gauss-jordan method', 'Matrix Inverse Method', ' Traingular factorization method', 'Gauss-seidel method', 'Gauss-seidel method', '2025-02-23 21:14:01'),
(147, 1, 'Which of the following is a cubic splines with zero second derivatives at the end point?', 'Quadratic Splines', 'Natural Cubic Splines', ' Semi-Cubic Splines', 'No Splines', 'Natural Cubic Splines', '2025-02-23 21:14:37'),
(148, 1, 'Which one of the following is an Elliptic Equation?', 'Laplace Equation', 'Crank Nicolson Method', 'Bender-Schmidt Equation', 'Newton-Gregory Method', 'Laplace Equation', '2025-02-23 21:17:24'),
(149, 1, 'Which one of the following method is similar to two stage Runge-kutta method?', 'Tayler Series Method', 'Eulers Method', 'Picards Method', 'Heuns Method', 'Heuns Method', '2025-02-23 21:18:28'),
(150, 1, 'Which one of the following is the correct value of eigen values for 8x1-4x2=λx1 and 2x1+2x2=λx2?', '4,5', '6,4', '8,2', '4,2', '4,5', '2025-02-23 21:19:02'),
(151, 1, 'Which of the following is numerical error?', 'Missing Information', ' Data Error', 'Roundup Error', 'Converison Error', 'Roundup Error', '2025-02-23 21:19:39'),
(152, 1, 'Which of the following method is used solve the system of linear equation?', ' Bisection method', 'False Position method', 'Taylor Series Method', 'Matrix Inverse method', 'Matrix Inverse method', '2025-02-23 21:20:12'),
(153, 1, 'How many significant digits in the number 105.78904560?', '11', '3', '8', '10', '11', '2025-02-23 21:21:36'),
(154, 1, 'What will be the solution of equations x+y=3 and 2x + 2y=6?', 'Unique Solution', ' Infinite Solution', 'No Solution', 'III-condition', ' Infinite Solution', '2025-02-23 21:22:19'),
(155, 1, 'Which of the following formula is four-point formula to estimate integration?', 'Trapezoidal Rule', 'Simpsons 1/3 Rule', 'Simpsons 3/8 Rule', 'Booles Rule', 'Simpsons 3/8 Rule', '2025-02-23 21:22:51'),
(156, 1, 'When a differential equation is considered as an ordinary differential equation?', 'if it has one independent variable', 'if it has one dependent variable', 'more than one dependent variables', 'more than one independent variables', 'if it has one independent variable', '2025-02-23 21:23:39'),
(157, 4, 'Which of following function return 1 when output is successful?', 'echo()', 'print()', 'both', 'None', 'print()', '2025-02-24 07:35:49'),
(158, 4, 'Which Keyword is used to put a stop on inheritance?', 'stop', 'end', 'break', 'final', 'final', '2025-02-24 07:36:14'),
(159, 4, 'Which of the following jQuery method sets the html contents of an element?', 'html(val)', 'setHtml(val)', 'setInnerHtml(val)', 'None of the above', 'html(val)', '2025-02-24 07:36:51'),
(160, 4, 'Which one of the following statements instantiates the mysqli class?', 'mysqli=new mysqli()', '$mysqli= new mysqli()', ' $mysqli->new.mysqli()', 'mysqli->new.mysqli()', '$mysqli= new mysqli()', '2025-02-24 07:37:32'),
(161, 4, 'Which one of the following variable cannot be used inside a static method?', ' $this', '$get', '$set', ' $date', ' $this', '2025-02-24 07:37:59'),
(162, 4, 'When you want to store user data in a session use the .........Array.', '$_SESSION', 'SYS_SESSION', '$SEESION', '$SEESIOS', '$_SESSION', '2025-02-24 07:38:58'),
(163, 4, 'In your PHP application you nedd to open a file. You want the application to issue a warning and continue execution, in case the file is not found. The ideal function to be used is:', 'include()', 'require()', 'nowarm()', 'getFile(false)', 'include()', '2025-02-24 07:39:39'),
(164, 4, 'Variables/functions in PHP donot work directly with:', 'echo()', 'isset()', 'print()', 'All of the above', 'isset()', '2025-02-24 07:40:24'),
(165, 4, 'Which sign is used to access variable of variable in PHP?', '$$', '$', '#@', '$@', '$$', '2025-02-24 07:41:04'),
(166, 4, 'What are the advantages of Ajax?', 'Bandwidth utilization', ' More interactive', 'Speeder retrieval of data', 'All of these', 'All of these', '2025-02-24 07:41:39'),
(167, 4, '$.foo() is equivalent to?', ' javascript.foo();', 'document.foo();', 'jQuery.foo();', 'none of the above', 'jQuery.foo();', '2025-02-24 07:42:12'),
(168, 4, 'Which option will you select to find the unpublished articles of Joomla?', 'Component manager', 'Media manager', 'Article manager', 'None of the above', 'Article manager', '2025-02-24 07:42:48'),
(169, 4, 'Which of the following function is used to sort an array in descending order in PHP?', 'sort();', 'asort();', 'rsort();', 'dsort();', 'rsort();', '2025-02-24 07:43:23'),
(170, 4, 'Which two predefined variables are used to retrieve information from forms?', '$GET&$POST', '$_GET&$_POST', '$_GET&$_SET', 'GET & SET', '$_GET&$_POST', '2025-02-24 07:44:04'),
(171, 4, 'Which clause is used to modify the existing field of the table?', 'ALTER', 'FROM', 'SELECT', 'MODIFY', 'SELECT', '2025-02-24 07:44:47'),
(172, 4, 'In MVC architecture the model is referred to', 'shape of data', 'html content', 'collection of data', 'types of data', 'collection of data', '2025-02-24 07:45:15'),
(173, 4, 'Which key is used to link two table in MYSQL?', 'primary key', 'foreign key', 'both a and b', 'None of the above', 'foreign key', '2025-02-24 07:45:57'),
(174, 3, 'In UNIX, which system call creates the new process?', 'fork', 'create', 'new', 'None of the above', 'fork', '2025-02-24 07:51:53'),
(175, 3, 'Which of the following does  not contain process control block (pcb)?', 'code', 'bootstrap program', 'stack', 'data', 'bootstrap program', '2025-02-24 07:52:32'),
(176, 3, 'The test and set instruction in executed.', 'after a particular process', 'atomically', 'periodically', 'None of the above', 'atomically', '2025-02-24 07:53:09'),
(177, 3, 'How circular wait condition can be prevented?', 'by defining linear ordering of resource type', 'by resource grant on all or none basis', 'by using pipes', 'by using threads', 'by defining linear ordering of resource type', '2025-02-24 07:54:08'),
(178, 3, 'Which process can be affected by other process executing in the system?', 'cooperating process', 'init process', 'parent process', 'child process', 'cooperating process', '2025-02-24 07:54:51'),
(179, 3, 'External fragmentation will not occur when?', 'first fit is used', 'worst fit is used', 'best fit is used', 'no matter which algorithm is used, it will always occur', 'no matter which algorithm is used, it will always occur', '2025-02-24 07:55:34'),
(180, 3, 'What is compaction?', 'a technique for overcoming internal fragmentation', 'a technique for overcoming external fragmentation', 'a paging technique', 'a technique for over coming fatal error', 'a technique for overcoming external fragmentation', '2025-02-24 07:56:25'),
(181, 3, 'Why is one-time password safe?', 'it is easy to generated', 'it is different for every access', 'it cannot be shared', 'it is complex encrypted password', 'it is different for every access', '2025-02-24 07:57:09'),
(182, 3, 'In distributed system each processor has it own:', 'local memory', 'clock', 'both a and b', 'None of the above', 'local memory', '2025-02-24 07:57:47'),
(183, 3, 'How process on the remote system are identified:', 'host id', 'host name and identifier', 'identifier', 'process id', 'host name and identifier', '2025-02-24 07:58:15'),
(184, 3, 'Multiprogramming of computer system increases.......', 'cpu utilization', 'cost of computation', 'storage', 'memory', 'cpu utilization', '2025-02-24 08:00:38'),
(185, 3, 'To access the services of operating system, the interface is provided by', 'api', 'library', 'system call', 'assembly instruction', 'system call', '2025-02-24 08:01:19'),
(186, 3, 'which of the following method is used to prevent tread or process from accessing a single resource?', 'pcb', 'semaphore', 'job scheduler', 'non-contiguous memory allocation', 'semaphore', '2025-02-24 08:02:07'),
(187, 3, 'Bankers algorithm is used to...........', 'prevent deadlock', 'avoid deadlock', 'detect deadlock', 'recover from deadlock', 'prevent deadlock', '2025-02-24 08:02:46'),
(188, 3, 'The hardware mechanism that allows a device to notify the cpu is called', 'polling', 'interrupt', 'driver', 'controlling', 'interrupt', '2025-02-24 08:03:50'),
(189, 2, 'What is indexing?', 'It is a process of transactions', 'Data structure of indexes', 'saving the data in indexes', 'non of the above', 'Data structure of indexes', '2025-02-24 11:15:01');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `quiz_id` int(11) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `quiz_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`quiz_id`, `teacher_id`, `subject_id`, `quiz_name`, `created_at`) VALUES
(1, 1, 1, 'Numerical Methods Quiz', '2024-10-05 04:30:03'),
(2, 9, 2, 'Database Management System Quiz', '2024-10-06 05:31:56'),
(3, 18, 3, 'Operating System Quiz', '2024-10-06 05:40:44'),
(4, 20, 4, 'Scripting Language Quiz', '2024-10-06 05:40:44'),
(5, 15, 5, 'Software Engineering Quiz', '2024-10-06 05:40:44');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes_backup`
--

CREATE TABLE `quizzes_backup` (
  `quiz_id` int(11) NOT NULL DEFAULT 0,
  `teacher_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `quiz_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes_backup`
--

INSERT INTO `quizzes_backup` (`quiz_id`, `teacher_id`, `subject_id`, `quiz_name`, `created_at`) VALUES
(1, 1, 1, 'Operating Systems', '2024-10-05 04:30:03'),
(2, 1, 2, 'Database Operating System', '2024-10-06 05:31:56'),
(3, 1, 3, 'Scripting Languages', '2024-10-06 05:40:44'),
(4, 1, 4, 'Numerical Methods', '2024-10-06 05:40:44'),
(5, 1, 5, 'Software Engineering', '2024-10-06 05:40:44');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_history`
--

CREATE TABLE `quiz_history` (
  `history_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_answer` varchar(255) NOT NULL,
  `correct_answer` varchar(255) NOT NULL,
  `is_correct` tinyint(1) NOT NULL,
  `score` int(11) NOT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_history`
--

INSERT INTO `quiz_history` (`history_id`, `student_id`, `quiz_id`, `question_id`, `selected_answer`, `correct_answer`, `is_correct`, `score`, `attempted_at`) VALUES
(31, 7, 4, 166, 'Bandwidth utilization', 'All of these', 0, 0, '2025-02-24 02:27:29'),
(32, 7, 4, 168, 'Article manager', 'Article manager', 1, 1, '2025-02-24 02:27:29'),
(33, 7, 4, 164, 'All of the above', 'isset()', 0, 0, '2025-02-24 02:27:29'),
(34, 7, 4, 172, 'collection of data', 'collection of data', 1, 1, '2025-02-24 02:27:29'),
(35, 7, 4, 163, 'nowarm()', 'include()', 0, 0, '2025-02-24 02:27:29'),
(51, 10, 3, 183, 'host name and identifier', 'host name and identifier', 1, 1, '2025-02-24 04:52:18'),
(52, 10, 3, 180, 'a technique for overcoming internal fragmentation', 'a technique for overcoming external fragmentation', 0, 0, '2025-02-24 04:52:18'),
(53, 10, 3, 184, 'cpu utilization', 'cpu utilization', 1, 1, '2025-02-24 04:52:18'),
(54, 10, 3, 182, 'None of the above', 'local memory', 0, 0, '2025-02-24 04:52:18'),
(55, 10, 3, 181, 'it is different for every access', 'it is different for every access', 1, 1, '2025-02-24 04:52:18'),
(56, 10, 2, 51, 'Alter', 'Alter', 1, 1, '2025-02-24 04:57:58'),
(57, 10, 2, 49, 'Dense and Spares', 'Dense and Spares', 1, 1, '2025-02-24 04:57:58'),
(58, 10, 2, 90, 'Dense index', 'Dense index', 1, 1, '2025-02-24 04:57:58'),
(59, 10, 2, 44, 'Physcial level', 'Logical level', 0, 0, '2025-02-24 04:57:58'),
(60, 10, 1, 144, 'Newton-Raphson method', 'Newton-Raphson method', 1, 1, '2025-02-24 05:32:54'),
(61, 10, 1, 147, 'Natural Cubic Splines', 'Natural Cubic Splines', 1, 1, '2025-02-24 05:32:54'),
(62, 10, 1, 151, 'Roundup Error', 'Roundup Error', 1, 1, '2025-02-24 05:32:54'),
(63, 10, 1, 156, 'more than one dependent variables', 'if it has one independent variable', 0, 0, '2025-02-24 05:32:54'),
(64, 10, 1, 146, 'Gauss-seidel method', 'Gauss-seidel method', 1, 1, '2025-02-24 05:32:54'),
(65, 10, 5, 94, 'Feasibility study', 'Requirement Desgin', 0, 0, '2025-02-24 05:37:42'),
(66, 10, 5, 92, 'Integration Testing', 'Integration Testing', 1, 1, '2025-02-24 05:37:42'),
(67, 10, 5, 91, 'When customer can not define requirement clearly.', 'When customer can not define requirement clearly.', 1, 1, '2025-02-24 05:37:42'),
(68, 10, 5, 57, 'MVC pattern ', 'MVC pattern', 0, 0, '2025-02-24 05:37:42'),
(69, 10, 5, 131, 'Quality', 'Quality', 1, 1, '2025-02-24 05:37:42'),
(70, 10, 4, 170, '$GET&$POST', '$_GET&$_POST', 0, 0, '2025-02-24 05:41:58'),
(71, 10, 4, 162, '$_SESSION', '$_SESSION', 1, 1, '2025-02-24 05:41:58'),
(72, 10, 4, 160, 'mysqli=new mysqli()', '$mysqli= new mysqli()', 0, 0, '2025-02-24 05:41:58'),
(73, 10, 4, 168, 'Component manager', 'Article manager', 0, 0, '2025-02-24 05:41:58'),
(74, 10, 4, 165, '$', '$$', 0, 0, '2025-02-24 05:41:58'),
(75, 10, 3, 180, 'a technique for overcoming external fragmentation', 'a technique for overcoming external fragmentation', 1, 1, '2025-02-24 05:47:34'),
(76, 10, 3, 188, 'driver', 'interrupt', 0, 0, '2025-02-24 05:47:34'),
(77, 10, 3, 184, 'cpu utilization', 'cpu utilization', 1, 1, '2025-02-24 05:47:34'),
(78, 10, 3, 174, 'new', 'fork', 0, 0, '2025-02-24 05:47:34'),
(79, 10, 3, 186, 'job scheduler', 'semaphore', 0, 0, '2025-02-24 05:47:34'),
(80, 10, 3, 183, 'host name and identifier', 'host name and identifier', 1, 1, '2025-02-24 05:47:34'),
(81, 10, 3, 175, 'bootstrap program', 'bootstrap program', 1, 1, '2025-02-24 05:47:34'),
(82, 10, 3, 181, 'it is different for every access', 'it is different for every access', 1, 1, '2025-02-24 05:47:34'),
(83, 10, 3, 182, 'local memory', 'local memory', 1, 1, '2025-02-24 05:47:34'),
(84, 10, 3, 187, 'avoid deadlock', 'prevent deadlock', 0, 0, '2025-02-24 05:47:34'),
(85, 10, 3, 179, 'best fit is used', 'no matter which algorithm is used, it will always occur', 0, 0, '2025-02-24 05:47:34'),
(86, 10, 3, 185, 'system call', 'system call', 1, 1, '2025-02-24 05:47:34'),
(87, 10, 3, 178, 'child process', 'cooperating process', 0, 0, '2025-02-24 05:47:34'),
(88, 10, 3, 177, 'by resource grant on all or none basis', 'by defining linear ordering of resource type', 0, 0, '2025-02-24 05:47:34'),
(89, 10, 3, 176, 'periodically', 'atomically', 0, 0, '2025-02-24 05:47:34');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `username`, `email`, `first_name`, `last_name`, `password`, `profile_pic`) VALUES
(6, 'Sirjanab', 'sirjanab@gmail.com', 'Sirjana ', 'Basnyat', '$2y$10$o3ktyMwr.QXtdeNC7/WYlObffghx9oQMGTgL4WH4WhMVrzBwOYsYa', NULL),
(7, 'ishwor', 'sijankc542@gmail.com', 'Ishwor', 'khatri', '$2y$10$eM5sGNr.auWA5SYX3LvVt.J7uqZvuV90kowCLUYYs0T2Zq1HclDwK', NULL),
(8, 'ceejan', 'cijan10@gmail.com', 'cijan', 'khatri', '$2y$10$JAbV18M8L5WyCBuKt3flle2delg0cWthg1GrhbGe5Gb39sWO5LLWq', NULL),
(10, 'sirjana', 'sirjanabasnet@gmail.com', 'Sirjana', 'Basnet', '$2y$10$dW7K0ytZmZOPTBxGR74kgevqBBi.FBe72uhqtY2BeYIfyNSxXPvW2', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_dashboard_totals`
--

CREATE TABLE `student_dashboard_totals` (
  `student_id` int(11) NOT NULL,
  `total_attempts` int(11) DEFAULT 0,
  `total_correct` int(11) DEFAULT 0,
  `total_incorrect` int(11) DEFAULT 0,
  `total_unanswered` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_dashboard_totals`
--

INSERT INTO `student_dashboard_totals` (`student_id`, `total_attempts`, `total_correct`, `total_incorrect`, `total_unanswered`) VALUES
(7, 35, 57, 162, 0),
(8, 5, 3, 22, 0),
(10, 6, 21, 18, 0);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_name`) VALUES
(2, 'Database Management System'),
(1, 'Numerical Methods'),
(3, 'Operating System'),
(4, 'Scripting Language'),
(5, 'Software Engineering');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `teacher_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `contact_no` varchar(15) DEFAULT NULL,
  `qualification` varchar(100) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('pending','approved') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `username`, `email`, `first_name`, `last_name`, `contact_no`, `qualification`, `subject_id`, `password`, `status`) VALUES
(1, 'sijan', 'sijankc542@gmail.com', 'sijan', 'Khatri', '9865235689', 'Master', 1, '$2y$10$/lkDxYJ1VLMiaCdeHpc/XeKhGUNcd9Tjxs6PR399gbGOUQM5aGRVu', 'approved'),
(9, 'Sirjana', 'sirjana@gmail.com', 'sirjana', 'basnet', '9848251111', 'master', 2, '$2y$10$B9um1mMDXJTwJwWf22fwMea.HoKQOnbjuJJZ3CVg8.DIa.R7HPvpW', 'approved'),
(15, 'bijaya', 'bijaya52@gmail.com', 'Bijaya', 'Khatri', '9848251589', 'bacholer', 5, '$2y$10$Gp5zENBCo2wvBuyiiD1ObOWhiVpZzigXQt2UYYTgRxhAqUXyAtpKm', 'approved'),
(18, 'ramhari', 'ram@gmail.com', 'ramhari', 'khatri', '9848251111', 'Master', 3, '$2y$10$Z6H2b/cuPcUISgPqtGekJOOV1kr5InZPEEXBriYF6D/rEUlrW0BIS', 'approved'),
(20, 'ramkrishna', 'ramkrishna@gmail.com', 'ramkrishna', 'khatri', '9874563210', 'Master', 4, '$2y$10$X7AJDmdlNU1NPzfCQKYcP.q43NJjH7OtBVkeo0Pv9g04hmQ1mysDm', 'approved'),
(22, 'Hari', 'hari@gmail.com', 'Hari', 'Thapa', '9840267932', 'Bachleor', 3, '$2y$10$zRhu0z7xuGDxpwLcV1F9Jut46lXdwwzHY0LxzXE7SxNENLURo2z0m', 'approved');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `fk_quiz_id` (`quiz_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`quiz_id`),
  ADD KEY `fk_teacher_id` (`teacher_id`),
  ADD KEY `fk_subject_id` (`subject_id`);

--
-- Indexes for table `quiz_history`
--
ALTER TABLE `quiz_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `uq_username` (`username`),
  ADD UNIQUE KEY `uq_email` (`email`);

--
-- Indexes for table `student_dashboard_totals`
--
ALTER TABLE `student_dashboard_totals`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`),
  ADD UNIQUE KEY `uq_subject_name` (`subject_name`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teacher_id`),
  ADD UNIQUE KEY `uq_teacher_username` (`username`),
  ADD UNIQUE KEY `uq_teacher_email` (`email`),
  ADD KEY `subject_id` (`subject_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=190;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `quiz_history`
--
ALTER TABLE `quiz_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `fk_quiz_id` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `fk_subject_id` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_teacher_id` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`teacher_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quizzes_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_history`
--
ALTER TABLE `quiz_history`
  ADD CONSTRAINT `quiz_history_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `quiz_history_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`),
  ADD CONSTRAINT `quiz_history_ibfk_3` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`);

--
-- Constraints for table `student_dashboard_totals`
--
ALTER TABLE `student_dashboard_totals`
  ADD CONSTRAINT `student_dashboard_totals_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
