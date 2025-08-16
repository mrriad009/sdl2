<?php
session_start();

if (!isset($_SESSION["authenticated"]) || $_SESSION["authenticated"] !== true) {
    header("Location: admin_panel.php");
    exit();
}

// Ensure that the user is logged in and has a valid role (CR/Professor)
if (!isset($_SESSION["user"]) || !is_array($_SESSION["user"]) || !isset($_SESSION["user"]["role"]) || !in_array($_SESSION["user"]["role"], ["professor", "CR"])) {
    // Redirect to login page if not logged in or not authorized
    header("Location: login.php");
    exit();
}

// Retrieve user info from the session
$user = $_SESSION["user"];
$username = isset($user["name"]) && strtolower($user["name"]) !== "root" ? "Welcome, " . $user["name"] : "Welcome"; // Custom welcome message based on the user\"s name

// Database connection
include "db_connect.php";

// Initialize variables
$students = [];

// Fetch students from the database
$sql = "SELECT id, name FROM students";
if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
} else {
    echo "Error fetching student data: " . $conn->error;
}

// Process form submission to mark attendance
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $class_date = $_POST["class_date"];
    $status = $_POST["status"];
    $subject = $_POST["subject"];
    $student_ids = isset($_POST["student_ids"]) ? $_POST["student_ids"] : [];

    foreach ($student_ids as $student_id) {
        // Fetch total classes for the student
        $sql_total_classes = "SELECT total_classes, present, absent FROM attendance_record WHERE student_id = ? AND subject = ?";
        $stmt = $conn->prepare($sql_total_classes);
        $stmt->bind_param("is", $student_id, $subject);
        $stmt->execute();
        $stmt->bind_result($total_classes, $present, $absent);
        $stmt->fetch();
        $stmt->close();

        // Initialize present and absent if no record is found
        if ($total_classes === null) {
            $total_classes = 0;
            $present = 0;
            $absent = 0;
        }

        // Increment total classes
        $total_classes += 1;

        // Update attendance records dynamically
        if ($status == "Present") {
            $present += 1;
        } elseif ($status == "Absent") {
            $absent += 1;
        }

        // Calculate the percentage dynamically
        $percentage = ($present / $total_classes) * 100;

        // Check if an attendance record already exists for the student on the given date
        $sql_check = "SELECT id FROM attendance_record WHERE student_id = ? AND class_date = ? AND subject = ?";
        $stmt = $conn->prepare($sql_check);
        $stmt->bind_param("iss", $student_id, $class_date, $subject);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Update existing record
            $stmt->close();
            $stmt = $conn->prepare("UPDATE attendance_record SET status = ?, total_classes = ?, present = ?, absent = ?, percentage = ? WHERE student_id = ? AND class_date = ? AND subject = ?");
            $stmt->bind_param("siiiisis", $status, $total_classes, $present, $absent, $percentage, $student_id, $class_date, $subject);
        } else {
            // Insert new record
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO attendance_record (student_id, class_date, status, subject, total_classes, present, absent, percentage) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssiiii", $student_id, $class_date, $status, $subject, $total_classes, $present, $absent, $percentage);
        }

        if (!$stmt->execute()) {
            echo "Error marking attendance for student ID $student_id: " . $conn->error;
        }
        $stmt->close();
    }

    echo "<script>window.location.href='view_attendance.php?success=1';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Attendance</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #4ca1af; /* Updated background color */
            color: #333;
            margin: 0;
            padding: 0;
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
            width: 100%;
            max-width: 1100px; /* Updated container size */
            margin: 20px;
            animation: fadeIn 0.5s ease-in-out;
            position: relative;
        }

        h1 {
            text-align: center;
            margin-bottom: 2.5rem;
            font-size: 2rem;
            color: #333;
        }

        .top-right-button {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .btn {
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .btn-primary {
            background-color: #007bff;
            color: #fff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-success {
            background-color: #28a745;
            color: #fff;
            border: none;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .form-check-label {
            margin-left: 5px;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-control {
            border-radius: 5px;
            height: 40px; /* Shortened field height */
            width: 40%;
            border: 1px solid #ddd;
        }

        .form-check-input {
            margin-top: 0.3rem;
        }

        .select-students {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
        }

        .search-box {
            margin-bottom: 1rem;
            height: 40px; /* Shortened search box height */
            width: 40%;
        }

        .form-group label {
            font-weight: bold;
            color: #2c3e50; /* Updated label color */
            font-size: 1.1rem; /* Increased font size for labels */
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="top-right-button btn btn-primary">Home</a>
        <a href="view_attendance.php" class="top-right-button btn btn-primary" style="right: 120px;">View Records</a>
        <h1><?php echo $username; ?> - Manage Attendance</h1>
    
        <form method="POST" action="">
            <div class="form-group">
                <label for="class_date">Class Date:</label>
                <input type="date" name="class_date" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="status">Attendance Status:</label>
                <select name="status" class="form-control" required>
                    <option value="Present">Present</option>
                    <option value="Absent">Absent</option>
                    <option value="Excused">Excused</option>
                </select>
            </div>

            <div class="form-group">
                <label for="subject">Select Subject:</label>
                <select name="subject" class="form-control" required>
                    <option value="">--Select Subject--</option>
                    <option value="Mathematics">Mathematics</option>
                    <option value="Physics">Physics</option>
                    <option value="Chemistry">Chemistry</option>
                </select>
            </div>

            <div class="form-group">
                <label for="student_ids">Select Students:</label>  

                <input type="checkbox" id="select_all" onclick="toggleSelectAll()"> Select All  

                <input type="text" id="search" class="form-control search-box" placeholder="Search students...">
                <div class="select-students" id="student-list">
                    <?php foreach ($students as $student): ?>
                        <div class="form-check">
                            <input type="checkbox" name="student_ids[]" value="<?php echo $student["id"]; ?>" class="form-check-input">
                            <label class="form-check-label"><?php echo $student["name"]; ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-success">Submit Attendance</button>
        </form>

        <script>
            function toggleSelectAll() {
                var checkboxes = document.querySelectorAll('input[name="student_ids[]"]');
                var selectAllCheckbox = document.getElementById('select_all');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            }

            document.getElementById('search').addEventListener('input', function() {
                var searchValue = this.value.toLowerCase();
                var students = document.querySelectorAll('#student-list .form-check');
                students.forEach(function(student) {
                    var studentName = student.querySelector('.form-check-label').textContent.toLowerCase();
                    if (studentName.includes(searchValue)) {
                        student.style.display = '';
                    } else {
                        student.style.display = 'none';
                    }
                });
            });
        </script>

        <?php $conn->close(); ?>
    </div>
</body>
</html>

<?php
$page_title = "Manage Attendance";
include 'header.php';
include 'footer.php';
?>
