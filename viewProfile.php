<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body style="background-color: #4ca1af;">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h2>Profile</h2>
            </div>
            <div class="card-body">
                <!-- Profile details will be displayed here -->
                <p><strong>Name:</strong> John Doe</p>
                <p><strong>Email:</strong> john.doe@example.com</p>
                <p><strong>Department:</strong> Computer Science</p>
                <p><strong>Date:</strong> <?php echo date(\'Y-m-d\' ); ?></p>
                <h3>Attendance Records</h3>
                <ul>
                    <?php
                    // Database connection
                    $conn = new mysqli(\'localhost\', \'root\', \'\', \'student_attendance\');
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Fetch attendance records for the student
                    $student_id = isset($_GET[\'id\']) ? (int)$_GET[\'id\'] : 0;
                    if ($student_id <= 0) {
                        die("No student ID provided.");
                    }
                    $sql = "SELECT s.name, s.email, s.department, 21 AS total_classes, SUM(CASE WHEN a.status = \'Present\' THEN 1 ELSE 0 END) AS present, SUM(CASE WHEN a.status = \'Absent\' THEN 1 ELSE 0 END) AS absent, (SUM(CASE WHEN a.status = \'Present\' THEN 1 ELSE 0 END) / 21) * 100 AS percentage FROM students s JOIN attendance_record a ON s.id = a.student_id WHERE s.id = ?";
                    if ($stmt = $conn->prepare($sql)) {
                        $stmt->bind_param("i", $student_id);
                        $stmt->execute();
                        $stmt->bind_result($name, $email, $department, $total_classes, $present, $absent, $percentage);
                        $stmt->fetch();
                        $stmt->close();
                    } else {
                        die("Error retrieving student data.");
                    }
                    ?>
                </ul>
            </div>
            <div class="card-footer text-right">
                <a href="index.php" class="btn btn-primary">Back to Home</a>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
