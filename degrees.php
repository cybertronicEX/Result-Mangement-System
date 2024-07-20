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
    <title>Degrees</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script>
        // Function to show modal dialog
        function showModal() {
            document.getElementById('addModuleModal').style.display = 'block';
        }

        // Function to hide modal dialog
        function closeModal() {
            document.getElementById('addModuleModal').style.display = 'none';
        }

        function confirmDelete(moduleId) {
            if (confirm('Are you sure you want to delete this module?')) {
                window.location.href = 'delete_module.php?id=' + moduleId;
            }
        }
    </script>
</head>
<body>
    <h2>Degrees</h2>

    <h3>Existing Degrees:</h3>
    <ul>
        <?php
        $degrees_sql = "SELECT id, degree_name FROM degrees";
        $degrees_result = $conn->query($degrees_sql);
        while ($degree_row = $degrees_result->fetch_assoc()) {
            echo "<li>" . $degree_row['degree_name'] . "</li>";
        }
        ?>
    </ul>

    <!-- Button to open modal for adding module -->
    <button onclick="showModal()">Add Module</button>

    <!-- Modal dialog for adding module -->
    <div id="addModuleModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Add Module to Degree</h3>
            <form action="add_module.php" method="POST">
                <label for="degree">Select Degree:</label>
                <select id="degree" name="degree" required>
                    <?php
                    $degrees_sql = "SELECT id, degree_name FROM degrees";
                    $degrees_result = $conn->query($degrees_sql);
                    while ($degree_row = $degrees_result->fetch_assoc()) {
                        echo "<option value='" . $degree_row['id'] . "'>" . $degree_row['degree_name'] . "</option>";
                    }
                    ?>
                </select><br><br>
                <label for="year">Select Year:</label>
                <select id="year" name="year" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                </select><br><br>
                <label for="semester">Select Semester:</label>
                <select id="semester" name="semester" required>
                    <option value="1">1</option>
                    <option value="2">2</option>
                </select><br><br>
                <label for="module_name">Module Name:</label>
                <input type="text" id="module_name" name="module_name" required><br><br>
                <label for="module_code">Module Code:</label>
                <input type="text" name="module_code" id="module_code" required><br><br>
                <input type="submit" value="Add Module">
            </form>
        </div>
    </div>

    <h3>Modules by Degree:</h3>
    <?php
    // Fetch degrees and their modules
    $degrees_sql = "SELECT id, degree_name FROM degrees";
    $degrees_result = $conn->query($degrees_sql);

    while ($degree_row = $degrees_result->fetch_assoc()) {
        echo "<h4>Degree: " . $degree_row['degree_name'] . "</h4>";
        
        // Fetch modules for this degree
        $modules_sql = "SELECT id, module_name, module_code, year, semester FROM modules WHERE degree_id = " . $degree_row['id'];
        $modules_result = $conn->query($modules_sql);

        if ($modules_result->num_rows > 0) {
            echo "<table>";
            echo "<tr><th>Module Name</th><th>Module Code</th><th>Year</th><th>Semester</th><th>Actions</th></tr>";
            while ($module_row = $modules_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $module_row['module_name'] . "</td>";
                echo "<td>" . $module_row['module_code'] . "</td>";
                echo "<td>" . $module_row['year'] . "</td>";
                echo "<td>" . $module_row['semester'] . "</td>";
                echo "<td>
                        <button onclick=\"window.location.href='edit_module.php?id=" . $module_row['id'] . "'\">Edit</button>
                        <button onclick='confirmDelete(" . $module_row['id'] . ")'>Delete</button>
                      </td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No modules found for this degree.</p>";
        }
    }
    ?>
</body>
</html>
