<?php
session_start();
if ($_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit;
}
include('navbar.php');
include('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
    $modules = [];
    while ($row = $result->fetch_assoc()) {
        $modules[] = $row;
    }
    $stmt->close();
} else {
    $year = null;
    $semester = null;
    $modules = [];
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
    </style>
</head>
<body>
    <h2>Select Modules</h2>
    <div class="container">
        <form action="" method="POST">
            <label for="year">Year:</label>
            <select id="year" name="year" required>
                <option value="" disabled selected>Select Year</option>
                <option value="1">Year 1</option>
                <option value="2">Year 2</option>
                <option value="3">Year 3</option>
                <option value="4">Year 4</option>
            </select>

            <label for="semester">Semester:</label>
            <select id="semester" name="semester" required>
                <option value="" disabled selected>Select Semester</option>
                <option value="1">Semester 1</option>
                <option value="2">Semester 2</option>
            </select>
            <input type="submit" value="Find Modules">
        </form>

        <?php if (!empty($modules)): ?>
            <form action="select_modules_action.php" method="POST">
                <label for="modules">Modules:</label>
                <select id="modules" name="modules[]" multiple required>
                    <?php foreach ($modules as $module): ?>
                        <option value="<?php echo $module['id']; ?>"><?php echo $module['module_name']; ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="submit" value="Select Modules">
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
