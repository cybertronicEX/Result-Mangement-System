<?php
session_start();
if ($_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit;
}
include('navbar.php');
include('config.php');

$year = null;
$semester = null;
$modules = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['find_modules'])) {
    $year = $_POST['year'];
    $semester = $_POST['semester'];

    // Retrieve the degree ID based on the logged-in user
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT degree_id FROM students WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($degree_id);
    $stmt->fetch();
    $stmt->close();
    
    // Retrieve available modules based on the selected year and semester
    $sql = "SELECT id, module_name FROM modules WHERE degree_id = ? AND year = ? AND semester = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $degree_id, $year, $semester);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $modules[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Modules</title>
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
        form {
            background-color: #ffffff;
            border: 1px solid #0056b3;
            border-radius: 5px;
            padding: 20px;
            margin-top: 20px;
        }
        select, input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #0056b3;
            border-radius: 5px;
        }
        input[type="submit"] {
            background-color: #0056b3;
            color: white;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #003d80;
        }
        .modules-select {
            margin-top: 20px;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>
    <h2>Select Modules</h2>
    <div class="container">
        <form action="" method="POST">
            <label for="year">Year:</label>
            <select id="year" name="year" required>
                <option value="" disabled selected>Select Year</option>
                <option value="1" <?php if ($year == '1') echo 'selected'; ?>>Year 1</option>
                <option value="2" <?php if ($year == '2') echo 'selected'; ?>>Year 2</option>
                <option value="3" <?php if ($year == '3') echo 'selected'; ?>>Year 3</option>
                <option value="4" <?php if ($year == '4') echo 'selected'; ?>>Year 4</option>
            </select>

            <label for="semester">Semester:</label>
            <select id="semester" name="semester" required>
                <option value="" disabled selected>Select Semester</option>
                <option value="1" <?php if ($semester == '1') echo 'selected'; ?>>Semester 1</option>
                <option value="2" <?php if ($semester == '2') echo 'selected'; ?>>Semester 2</option>
            </select>
            <input type="submit" name="find_modules" value="Find Modules">
        </form>

        <?php if (!empty($modules)): ?>
            <div class="modules-select">
                <h3>Available Modules for Year <?php echo htmlspecialchars($year); ?>, Semester <?php echo htmlspecialchars($semester); ?></h3>
                <form action="select_modules_action.php" method="POST">
                    <input type="hidden" name="year" value="<?php echo htmlspecialchars($year); ?>">
                    <input type="hidden" name="semester" value="<?php echo htmlspecialchars($semester); ?>">
                    <label for="modules">Modules (Select up to 4):</label>
                    <select id="modules" name="modules[]" multiple required size="10">
                        <?php foreach ($modules as $module): ?>
                            <option value="<?php echo $module['id']; ?>"><?php echo $module['module_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="error" id="error-message" style="display: none;">You can select up to 4 modules only.</p>
                    <input type="submit" value="Select Modules">
                </form>
            </div>
        <?php endif; ?>
    </div>
    <script>
        document.querySelector('form[action="select_modules_action.php"]').addEventListener('submit', function(event) {
            var selectedModules = document.getElementById('modules').selectedOptions;
            var selectedModuleValues = Array.from(selectedModules).map(option => option.value);
            var uniqueModules = new Set(selectedModuleValues);

            // Check if the number of selected modules is greater than 4
            if (selectedModules.length > 4) {
                document.getElementById('error-message').textContent = 'You can select up to 4 modules only.';
                document.getElementById('error-message').style.display = 'block';
                event.preventDefault();
            } 
            // Check if there are duplicate modules
            else if (uniqueModules.size !== selectedModuleValues.length) {
                document.getElementById('error-message').textContent = 'Duplicate module selections are not allowed.';
                document.getElementById('error-message').style.display = 'block';
                event.preventDefault();
            } 
            else {
                document.getElementById('error-message').style.display = 'none';
            }
        });
    </script>

</body>
</html>
