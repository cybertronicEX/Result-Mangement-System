<?php
session_start();
if ($_SESSION['role'] != 'teacher') {
    header("Location: login.html");
    exit;
}

include('config.php');

$student_id = $_POST['student_id'];
$module_id = $_POST['module_id'];
$mid_marks = $_POST['mid_marks'];

$sql = "UPDATE grades SET mid_marks = ? WHERE student_id = ? AND module_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'SQL Error: ' . $conn->error]);
    exit;
}

$stmt->bind_param("sii", $mid_marks, $student_id, $module_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['status' => 'success', 'message' => 'Mid marks updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update mid marks']);
}

$stmt->close();
$conn->close();
?>
