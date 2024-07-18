<?php
session_start();
if ($_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit;
}

include('config.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        .container {
            width: 80%;
            margin: 0 auto;
        }
        h2 {
            background-color: #0056b3;
            color: white;
            padding: 10px;
            margin: 0;
            position: relative;
        }
        .logout-button {
            position: absolute;
            right: 20px;
            top: 10px;
            background-color: #0056b3;
            padding: 5px 10px;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
        }
        .options {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }
        .option {
            background-color: #ffffff;
            border: 1px solid #0056b3;
            border-radius: 5px;
            padding: 20px;
            width: 45%;
        }
        button {
            background-color: #0056b3;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }
        button:hover {
            background-color: #003d80;
        }
    </style>
</head>
<body>
    <h2>
        Welcome, <?php echo $_SESSION['username']; ?>
        <a href="logout.php" class="logout-button">Logout</a>
    </h2>
    <div class="container">
        <div class="options">
            <div class="option">
                <h3>Select Modules</h3>
                <button onclick="location.href='select_modules.php'">Select Modules</button>
            </div>
            <div class="option">
                <h3>View Results</h3>
                <button onclick="location.href='view_results.php'">View Results</button>
            </div>
        </div>
    </div>
</body>
</html>
