<?php
session_start();
if ($_SESSION['role'] != 'teacher') {
    header("Location: login.html");
    exit;
}

include('navbar.php');
include('config.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student List</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script>
        // Function to show modal dialog
        function showModal() {
            document.getElementById('addStudentModal').style.display = 'block';
        }

        // Function to hide modal dialog
        function closeModal() {
            document.getElementById('addStudentModal').style.display = 'none';
        }
    </script>
</head>
<body>
    <h2>Student List</h2>

    <!-- Button to open modal -->
    <button onclick="showModal()">Add Student</button>

    <!-- Modal dialog for adding student -->
    <div id="addStudentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Add Student</h3>
            <form action="add_student.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required><br><br>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required><br><br>
                <label for="degree">Degree Program:</label>
                <select id="degree" name="degree" required>
                    <?php
                    $degrees_sql = "SELECT id, degree_name FROM degrees";
                    $degrees_result = $conn->query($degrees_sql);
                    while ($degree_row = $degrees_result->fetch_assoc()) {
                        echo "<option value='" . $degree_row['id'] . "'>" . $degree_row['degree_name'] . "</option>";
                    }
                    ?>
                </select><br><br>
                <label for="enroll_year">Enroll Year:</label>
                <select id="enroll_year" name="enroll_year" required>
                    <option value="1">Year 1</option>
                    <option value="2">Year 2</option>
                    <option value="3">Year 3</option>
                    <option value="4">Year 4</option>
                </select>
                <label for="current_semester">Current Semester:</label>
                <select id="current_semester" name="current_semester" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                </select><br><br>
                <label for="student_name">Student Name:</label>
                <input type="text" id="student_name" name="student_name" required><br><br>
                <input type="submit" value="Add Student">
            </form>
        </div>
    </div>

    <!-- Display student list -->
    <?php
    $sql = "SELECT s.id, u.username, d.degree_name, s.enroll_year, s.current_semester, s.student_name
            FROM students s
            INNER JOIN users u ON s.user_id = u.id
            INNER JOIN degrees d ON s.degree_id = d.id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>Student Username</th>
                    <th>Degree</th>
                    <th>Enroll Year</th>
                    <th>Current Semester</th>
                    <th>Student Name</th>
                    <th>Modules</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row['username'] . "</td>
                    <td>" . $row['degree_name'] . "</td>
                    <td>" . $row['enroll_year'] . "</td>
                    <td>" . $row['current_semester'] . "</td>
                    <td>" . $row['student_name'] . "</td>
                    <td>";
            // Retrieve modules for this student
            $student_id = $row['id'];
            $modules_sql = "SELECT m.module_name
                            FROM student_modules sm
                            INNER JOIN modules m ON sm.module_id = m.id
                            WHERE sm.student_id = $student_id";
            $modules_result = $conn->query($modules_sql);
            if ($modules_result->num_rows > 0) {
                while ($module_row = $modules_result->fetch_assoc()) {
                    echo $module_row['module_name'] . "<br>";
                }
            } else {
                echo "No modules selected.";
            }
            echo "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "No students found.";
    }
    $conn->close();
    ?>
</body>
</html>
