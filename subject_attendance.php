<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set(\'display_errors\', 1);

// Database connection
$host = \'localhost\';
$username = \'root\';
$password = \'\';
$database = \'student_attendance\';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$subject = $_POST[\'subject\'] ?? \'\';

if ($subject) {
    // Query to get students\' attendance for the selected subject
    $sql = "SELECT s.id, s.name, s.email, s.department, a.subject, 21 AS total_classes, SUM(CASE WHEN a.status = \'Present\' THEN 1 ELSE 0 END) AS present, SUM(CASE WHEN a.status = \'Absent\' THEN 1 ELSE 0 END) AS absent, FLOOR((SUM(CASE WHEN a.status = \'Present\' THEN 1 ELSE 0 END) / 21) * 100) AS percentage FROM students s JOIN attendance_record a ON s.id = a.student_id WHERE a.subject = ? GROUP BY s.id, s.name, s.email, s.department, a.subject ORDER BY percentage DESC";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $subject);
        $stmt->execute();
        $stmt->bind_result($id, $name, $email, $department, $subject, $total_classes, $present, $absent, $percentage);
        $subject_results = [];
        while ($stmt->fetch()) {
            $subject_results[] = [
                \'id\' => $id,
                \'name\' => $name,
                \'email\' => $email,
                \'department\' => $department,
                \'subject\' => $subject,
                \'total_classes\' => $total_classes,
                \'present\' => $present,
                \'absent\' => $absent,
                \'percentage\' => $percentage
            ];
        }
        $stmt->close();
    } else {
        die("Error preparing statement: " . $conn->error);
    }
} else {
    die("No subject selected.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subject-wise Attendance</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #4ca1af;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1 );
            animation: fadeIn 0.5s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Subject-wise Attendance for <?php echo htmlspecialchars($subject); ?></h1>
        <?php if (!empty($subject_results)) : ?>
            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Subject</th>
                        <th>Present</th>
                        <th>Absent</th>
                        <th>Attendance %</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($subject_results as $row) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row[\'id\']); ?></td>
                        <td><?php echo htmlspecialchars($row[\'name\']); ?></td>
                        <td><?php echo htmlspecialchars($row[\'email\']); ?></td>
                        <td><?php echo htmlspecialchars($row[\'department\']); ?></td>
                        <td><?php echo htmlspecialchars($row[\'subject\']); ?></td>
                        <td><?php echo htmlspecialchars($row[\'present\']); ?></td>
                        <td><?php echo htmlspecialchars($row[\'absent\']); ?></td>
                        <td><?php echo htmlspecialchars($row[\'percentage\']); ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p class="text-danger">No attendance records found for the selected subject.</p>
        <?php endif; ?>
        <a href="index.php" class="btn btn-primary btn-block">Back to Home</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$page_title = "Subject Attendance";
include 'header.php';
$conn->close();
?>
