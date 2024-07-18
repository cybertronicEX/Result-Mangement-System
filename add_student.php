<?php
session_start();
if ($_SESSION['role'] != 'teacher') {
    header("Location: login.html");
    exit;
}

include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $student_name = $_POST['student_name'];
    $degree_id = $_POST['degree'];
    $enroll_year = $_POST['enroll_year'];
    $current_semester = $_POST['current_semester'];

    // Create user entry
    $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, 'student')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;

        // Create student entry
        $sql = "INSERT INTO students (user_id, degree_id, enroll_year, current_semester,student_name) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiis", $user_id, $degree_id, $enroll_year, $current_semester, $student_name);

        if ($stmt->execute()) {
            echo "<script>alert('Student added successfully!'); window.location.href = 'student_list.php';</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "'); window.location.href = 'student_list.php';</script>";
        }
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href = 'student_list.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
