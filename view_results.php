<?php
session_start();
if ($_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit;
}
include('navbar.php');
include('config.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Results</title>
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
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #0056b3;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #0056b3;
            color: white;
        }
        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Your Grades</h2>
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Module</th>
                    <th>Grade</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $user_id = $_SESSION['user_id'];
                $sql = "SELECT m.module_name, COALESCE(g.grade, 'N/A') AS grade
                        FROM modules m
                        LEFT JOIN grades g ON m.id = g.module_id AND g.student_id = (SELECT id FROM students WHERE user_id = ?)
                        JOIN student_modules sm ON m.id = sm.module_id
                        WHERE sm.student_id = (SELECT id FROM students WHERE user_id = ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $user_id, $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . $row['module_name'] . "</td><td>" . $row['grade'] . "</td></tr>";
                }
                $stmt->close();
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
