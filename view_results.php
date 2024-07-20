<?php
session_start();
if ($_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit;
}
include('navbar.php');
include('config.php');

// Retrieve the student ID and degree ID based on the logged-in user
$user_id = $_SESSION['user_id'];
$sql = "SELECT s.id AS student_id, s.enroll_year, s.current_semester, d.id AS degree_id
        FROM students s
        JOIN degrees d ON s.degree_id = d.id
        WHERE s.user_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($student_id, $enroll_year, $current_semester, $degree_id);
$stmt->fetch();
$stmt->close();

// Grade points mapping
$grade_points = [
    'A+' => 4, 'A' => 4, 'A-' => 3.7, 'B+' => 3.3,
    'B' => 3, 'B-' => 2.7, 'C+' => 2.3, 'C' => 2,
    'C-' => 1.7, 'D+' => 1.3, 'D' => 1, 'E' => 0,
    'N/A' => 0 // Adding N/A to handle no grades
];

// Function to calculate GPA
function calculate_gpa($grades, $grade_points) {
    $total_points = 0;
    $num_modules = 0;

    foreach ($grades as $grade) {
        if (isset($grade_points[$grade])) {
            $total_points += $grade_points[$grade];
            $num_modules++;
        }
    }

    return $num_modules > 0 ? $total_points / $num_modules : 0;
}

// Retrieve and display GPAs
function display_results($conn, $student_id, $degree_id, $enroll_year, $current_semester, $grade_points) {
    $results = [];
    $cumulative_grades = [];

    // Retrieve results by year
    for ($year = 1; $year <= $enroll_year; $year++) {
        $sql = "SELECT m.module_name, COALESCE(g.grade, 'N/A') AS grade
                FROM modules m
                LEFT JOIN grades g ON m.id = g.module_id AND g.student_id = ?
                WHERE m.year = ? AND m.degree_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iii", $student_id, $year, $degree_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $grades = [];
        $modules = [];
        $all_grades_present = true;
        while ($row = $result->fetch_assoc()) {
            if ($row['grade'] == 'N/A') {
                $all_grades_present = false;
            }
            $grades[] = $row['grade'];
            $cumulative_grades[] = $row['grade'];
            $modules[] = $row;
        }
        $stmt->close();

        $yearly_gpa = $all_grades_present ? calculate_gpa($grades, $grade_points) : 'N/A';
        $results[$year] = [
            'data' => $modules,
            'gpa' => $yearly_gpa
        ];
    }

    // Calculate cumulative GPA
    $cumulative_gpa = calculate_gpa(array_filter($cumulative_grades, function($grade) use ($grade_points) {
        return isset($grade_points[$grade]);
    }), $grade_points);

    return [
        'results' => $results,
        'cumulative_gpa' => $cumulative_gpa
    ];
}

$results_data = display_results($conn, $student_id, $degree_id, $enroll_year, $current_semester, $grade_points);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Results</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #0056b3;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #0056b3;
            color: white;
        }
        tbody tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Results</h2>
    <div class="container">
        <h3>Cumulative GPA: <?php echo number_format($results_data['cumulative_gpa'], 2); ?></h3>

        <?php foreach ($results_data['results'] as $year => $data): ?>
            <h3>Year <?php echo $year; ?> GPA: <?php echo is_numeric($data['gpa']) ? number_format($data['gpa'], 2) : 'N/A'; ?></h3>
            <table>
                <thead>
                    <tr>
                        <th>Module</th>
                        <th>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['data'] as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['module_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['grade']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    </div>
</body>
</html>
