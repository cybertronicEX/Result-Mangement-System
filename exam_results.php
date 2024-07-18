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
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Exam Results</title>
    <style>
        /* Styles for modal dialog */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #2a7bff;
        }
        button {
            padding: 5px 10px;
            cursor: pointer;
        }
    </style>
    <script>
        function showModal(studentId) {
            // Load modules and grades for the student
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById('moduleContent').innerHTML = this.responseText;
                    document.getElementById('viewModulesModal').style.display = 'block';
                }
            };
            xhttp.open("GET", "get_student_modules.php?student_id=" + studentId, true);
            xhttp.send();
        }

        function closeModal() {
            document.getElementById('viewModulesModal').style.display = 'none';
            document.getElementById('gradeModal').style.display = 'none';
        }

        function showGradeModal(studentId, moduleId) {
            document.getElementById('grade_student_id').value = studentId;
            document.getElementById('grade_module_id').value = moduleId;
            document.getElementById('gradeModal').style.display = 'block';
        }

        function submitGrade(event) {
            event.preventDefault();
            var form = event.target;

            var formData = new FormData(form);

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var response = JSON.parse(this.responseText);
                    alert(response.message);
                    if (response.status === 'success') {
                        window.location.href = 'exam_results.php';
                    }
                }
            };
            xhttp.open("POST", form.action, true);
            xhttp.send(formData);
        }
    </script>
</head>
<body>
    <h2>Exam Results</h2>

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
            <th>Username</th>
            <th>Student Name</th>
            <th>Degree</th>
            <th>Enroll Year</th>
            <th>Current Semester</th>
            <th>Actions</th>
        </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row['username'] . "</td>
                    <td>" . $row['student_name'] . "</td>
                    <td>" . $row['degree_name'] . "</td>
                    <td>" . $row['enroll_year'] . "</td>
                    <td>" . $row['current_semester'] . "</td>
                    <td><button onclick='showModal(" . $row['id'] . ")'>View Modules</button></td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "No students found.";
    }
    $conn->close();
    ?>

    <!-- Modal dialog for viewing modules -->
    <div id="viewModulesModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Modules and Grades</h3>
            <div id="moduleContent"></div>
        </div>
    </div>

    <!-- Modal dialog for adding/updating grade -->
    <div id="gradeModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Add/Update Grade</h3>
            <form action="assign_grade.php" method="POST" onsubmit="submitGrade(event)">
                <input type="hidden" id="grade_student_id" name="student_id">
                <input type="hidden" id="grade_module_id" name="module_id">
                <label for="grade">Grade:</label>
                <select id="grade" name="grade" required>
                    <option value="A+">A+</option>
                    <option value="A">A</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B">B</option>
                    <option value="B-">B-</option>
                    <option value="C+">C+</option>
                    <option value="C">C</option>
                    <option value="C-">C-</option>
                    <option value="D+">D+</option>
                    <option value="D">D</option>
                    <option value="E">E</option>
                    <option value="AB">AB</option>
                    <option value="MC">MC</option>
                </select>
                <input type="submit" value="Submit">
            </form>
        </div>
    </div>
</body>
</html>

