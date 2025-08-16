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

// Database connection
include "db_connect.php";

// Fetch updated attendance records with fixed total classes (21)
$search_query = isset($_POST["search_query"]) ? $_POST["search_query"] : "";
$subject_filter = isset($_POST["subject_filter"]) ? $_POST["subject_filter"] : "";
$attendance_sql = "SELECT students.id, students.name, \n                          21 AS total_classes, \n                          SUM(CASE WHEN attendance_record.status = \"Present\" THEN 1 ELSE 0 END) AS present, \n                          (SUM(CASE WHEN attendance_record.status = \"Present\" THEN 1 ELSE 0 END) / 21) * 100 AS percentage \n                   FROM attendance_record \n                   JOIN students ON attendance_record.student_id = students.id \n                   WHERE students.name LIKE ? AND (attendance_record.subject = ? OR ? = \"\")\n                   GROUP BY students.id, students.name";
$stmt = $conn->prepare($attendance_sql);
$search_param = "%" . $search_query . "%";
$stmt->bind_param("sss", $search_param, $subject_filter, $subject_filter);
$stmt->execute();
$attendance_result = $stmt->get_result();

// Filter present students for a specific day
$filter_date = isset($_POST["filter_date"]) ? $_POST["filter_date"] : null;
$present_students = [];
if ($filter_date) {
    $filter_sql = "SELECT students.id, students.name, attendance_record.class_date, attendance_record.status \n                   FROM attendance_record \n                   JOIN students ON attendance_record.student_id = students.id \n                   WHERE attendance_record.class_date = ? AND attendance_record.status = \"Present\"";
    $stmt = $conn->prepare($filter_sql);
    $stmt->bind_param("s", $filter_date);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $present_students[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Attendance</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Custom Styles */
        body {
            background-color: #4ca1af; /* Updated background color */
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: "Arial", sans-serif;
        }
        .container {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1 );
            animation: fadeIn 0.5s ease-in-out;
            max-width: 1100px;
            width: 100%;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .search-box, .date-box, .subject-box {
            max-width: 300px; /* Adjust the width of the search, date, and subject boxes */
        }
        .form-group label {
            font-weight: bold;
            color: #2c3e50; /* Updated label color */
        }
        .btn-primary {
            background-color: #4ca1af;
            border-color: #4ca1af;
        }
        .btn-primary:hover {
            background-color: #2c3e50;
            border-color: #2c3e50;
        }
        .btn-info {
            background-color: #3498db;
            border-color: #3498db;
        }
        .btn-info:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        .heading {
            font-size: 2.5rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }
        .table thead {
            background-color: #4ca1af;
            color: #fff;
        }
        .table tbody tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between mb-3">
            <a href="manage_attendance.php" class="btn btn-danger">Back</a>
            <a href="index.php" class="btn btn-primary">Home</a>
        </div>
        <h1 class="text-center heading">View Attendance Records</h1> <!-- Updated heading style -->
        <form method="POST" action="" class="mb-3">
            <div class="form-group">
                <label for="filter_date">Select Date to View Present Students:</label>
                <input type="date" name="filter_date" class="form-control date-box" required>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
        <?php if ($filter_date): ?>
            <h2 class="text-center">Present Students on <?php echo htmlspecialchars($filter_date); ?></h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Class Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($present_students as $student): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student["id"]); ?></td>
                            <td><?php echo htmlspecialchars($student["name"]); ?></td>
                            <td><?php echo htmlspecialchars($student["class_date"]); ?></td>
                            <td><?php echo htmlspecialchars($student["status"]); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <form method="POST" action="" class="mb-3">
            <div class="form-group">
                <label for="search_query">Search by Student Name:</label>
                <input type="text" name="search_query" class="form-control search-box" value="<?php echo htmlspecialchars($search_query); ?>">
            </div>
            <div class="form-group">
                <label for="subject_filter">Filter by Subject:</label>
                <select name="subject_filter" class="form-control subject-box">
                    <option value="">--Select Subject--</option>
                    <option value="Mathematics" <?php echo ($subject_filter == "Mathematics" ? "selected" : ""); ?>>Mathematics</option>
                    <option value="Physics" <?php echo ($subject_filter == "Physics" ? "selected" : ""); ?>>Physics</option>
                    <option value="Chemistry" <?php echo ($subject_filter == "Chemistry" ? "selected" : ""); ?>>Chemistry</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Total Classes</th>
                    <th>Present</th>
                    <th>Percentage</th>
                    <th>Action</th> <!-- New column for action buttons -->
                </tr>
            </thead>
            <tbody>
                <?php
                while ($attendance_row = $attendance_result->fetch_assoc()) {
                    echo "<tr>\n                            <td>\" . htmlspecialchars($attendance_row[\"id\"]) . \"</td>\n                            <td>\" . htmlspecialchars($attendance_row[\"name\"]) . \"</td>\n                            <td>\" . htmlspecialchars($attendance_row[\"total_classes\"]) . \"</td>\n                            <td>\" . htmlspecialchars($attendance_row[\"present\"]) . \"</td>\n                            <td>\" . htmlspecialchars(number_format($attendance_row[\"percentage\"], 2)) . \"%</td>\n                            <td><a href=\\\'profile.php?id=\\\' . htmlspecialchars($attendance_row[\"id\"]) . \"\\\' class=\\\'btn btn-info\\\'>View Profile</a></td> <!-- Updated button for viewing profile -->\n                          </tr>\";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
$page_title = "View Attendance";
include 'header.php';
$conn->close();
?>
