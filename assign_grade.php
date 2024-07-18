<?php
session_start();
if ($_SESSION['role'] != 'teacher') {
    header("Location: login.html");
    exit;
}

include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $module_id = $_POST['module_id'];
    $grade = $_POST['grade'];

    $sql = "INSERT INTO grades (student_id, module_id, grade) VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE grade = VALUES(grade)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $student_id, $module_id, $grade);

    if ($stmt->execute()) {
        echo "Grade assigned successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
