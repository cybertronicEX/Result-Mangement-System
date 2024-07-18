<?php
session_start();
if ($_SESSION['role'] != 'teacher') {
    header("Location: login.html");
    exit;
}

include('config.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Teacher Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        h2 {
            background-color: #007BFF;
            color: white;
            padding: 20px;
            width: 100%;
            text-align: center;
            margin: 0;
        }
        ul {
            list-style-type: none;
            padding: 0;
            margin: 20px;
            display: flex;
            justify-content: center;
            width: 100%;
        }
        li {
            margin: 0 10px;
        }
        a {
            text-decoration: none;
            color: white;
            padding: 15px 25px;
            display: inline-block;
            background-color: #007BFF;
            border-radius: 5px;
            transition: background-color 0.3s;
            width: 150px;
            text-align: center;
        }
        a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<h2>
    Welcome, <?php echo $_SESSION['username']; ?>
    <a href="logout.php" style="float: right; padding: 5px 5px; color: white; border-radius: 5px; text-decoration: none; margin-right: 10px; font-size: 16px;">Logout</a>
</h2>

    <ul>
        <li><a href="student_list.php">Student List</a></li>
        <li><a href="exam_results.php">Exam Results</a></li>
        <li><a href="degrees.php">Degrees</a></li>
    </ul>
</body>
</html>
