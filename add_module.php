<?php
session_start();
if ($_SESSION['role'] != 'teacher') {
    header("Location: login.html");
    exit;
}

include('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['degree'], $_POST['year'], $_POST['semester'], $_POST['module_name'], $_POST['module_code'])) {
        $degree_id = $_POST['degree'];
        $year = $_POST['year'];
        $semester = $_POST['semester'];
        $module_name = $_POST['module_name'];
        $module_code = $_POST['module_code'];

        // Prepare SQL statement
        $sql = "INSERT INTO modules (degree_id, year, semester, module_name, module_code) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            // Handle prepare error
            die('SQL prepare error: ' . htmlspecialchars($conn->error));
        }

        // Bind parameters and execute query
        $stmt->bind_param("iiiss", $degree_id, $year, $semester, $module_name, $module_code);
        if ($stmt->execute()) {
            echo "<script>alert('Module added successfully!'); window.location.href = 'degrees.php';</script>";
        } else {
            echo "<script>alert('Error: " . htmlspecialchars($stmt->error) . "'); window.location.href = 'degrees.php';</script>";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "<script>alert('Error: Missing required form fields.'); window.location.href = 'degrees.php';</script>";
    }
}
?>
