<?php
session_start();
if ($_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit;
}

include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $modules = $_POST['modules'];
    $year = $_POST['year'];
    $semester = $_POST['semester'];

    // Retrieve the student ID based on the logged-in user
    $sql = "SELECT id FROM students WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($student_id);
    $stmt->fetch();
    $stmt->close();

    // Delete existing module selections for the student for the given year and semester
    $sql = "DELETE FROM student_modules WHERE student_id = ? AND year = ? AND semester = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $student_id, $year, $semester);
    $stmt->execute();
    $stmt->close();

    // Insert new module selections for the student
    $sql = "INSERT INTO student_modules (student_id, module_id, year, semester) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    foreach ($modules as $module_id) {
        $stmt->bind_param("iiii", $student_id, $module_id, $year, $semester);
        $stmt->execute();
    }
    $stmt->close();

    // Redirect back to the student dashboard or another appropriate page
    header("Location: student_dashboard.php");
    exit;
}
?>
