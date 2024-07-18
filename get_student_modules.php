<?php
include('config.php');

$student_id = $_GET['student_id'];

$sql = "SELECT m.module_name, g.grade, m.id AS module_id
        FROM student_modules sm
        INNER JOIN modules m ON sm.module_id = m.id
        LEFT JOIN grades g ON sm.student_id = g.student_id AND sm.module_id = g.module_id
        WHERE sm.student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>Module Name</th>
                <th>Grade</th>
                <th>Actions</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row['module_name'] . "</td>
                <td>" . ($row['grade'] ? $row['grade'] : 'Not graded') . "</td>
                <td><button onclick='showGradeModal(" . $student_id . ", " . $row['module_id'] . ")'>Add/Update Grade</button></td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No modules found for this student.";
}
$stmt->close();
$conn->close();
?>
