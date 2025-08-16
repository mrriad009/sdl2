<?php
include 'db_connect.php';

$student_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($student_id <= 0) {
    die("No student ID provided.");
}

// Fetch student and attendance data
$sql = "SELECT s.name, s.email, s.department, \n               21 AS total_classes, \n               SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS present, \n               SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) AS absent, \n               (SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / 21) * 100 AS percentage\n        FROM students s\n        JOIN attendance_record a ON s.id = a.student_id\n        WHERE s.id = ?";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $stmt->bind_result($name, $email, $department, $total_classes, $present, $absent, $percentage);
    $stmt->fetch();
    $stmt->close();
} else {
    die("Error retrieving student data.");
}

// Fetch dates where the student was present
$dates_present = [];
$sql_dates = "SELECT class_date FROM attendance_record WHERE student_id = ? AND status = 'Present'";
if ($stmt_dates = $conn->prepare($sql_dates)) {
    $stmt_dates->bind_param("i", $student_id);
    $stmt_dates->execute();
    $result_dates = $stmt_dates->get_result();
    while ($row = $result_dates->fetch_assoc()) {
        $dates_present[] = $row['class_date'];
    }
    $stmt_dates->close();
} else {
    die("Error retrieving attendance dates.");
}

$page_title = "Student Profile";
include 'header.php';
?>

<div class="container profile-container">
    <h2>Student Profile</h2>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
    <p><strong>Department:</strong> <?php echo htmlspecialchars($department); ?></p>

    <h3>Attendance Details</h3>
    <p><strong>Total Classes:</strong> <?php echo htmlspecialchars($total_classes); ?></p>
    <p><strong>Present:</strong> <?php echo htmlspecialchars($present); ?></p>
    <p><strong>Absent:</strong> <?php echo htmlspecialchars($absent); ?></p>
    <p><strong>Attendance Percentage:</strong> <?php echo htmlspecialchars($percentage); ?>%</p>

    <h3>Dates Present</h3>
    <ul>
        <?php foreach ($dates_present as $date): ?>
            <li><?php echo htmlspecialchars($date); ?></li>
        <?php endforeach; ?>
    </ul>

    <div class="buttons">
        <a href="javascript:history.back()">Back</a>
        <a href="index.php">Home</a>
    </div>
</div>

<?php include 'footer.php'; $conn->close(); ?>
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .container:hover {
            transform: translateY(-10px);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }
        h2, h3 {
            color: #333;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        p {
            line-height: 1.6;
            color: #555;
            margin: 10px 0;
        }
        .profile-container {
            text-align: left;
        }
        .profile-container p {
            margin: 10px 0;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .profile-container strong {
            color: #000;
        }
        .buttons {
            margin-top: 20px;
        }
        .buttons a {
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            padding: 10px 20px;
            border-radius: 5px;
            margin-right: 10px;
            transition: background-color 0.3s ease;
        }
        .buttons a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container profile-container">
        <h2>Student Profile</h2>
        <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
        <p><strong>Department:</strong> <?php echo htmlspecialchars($department); ?></p>

        <h3>Attendance Details</h3>
        <p><strong>Total Classes:</strong> <?php echo htmlspecialchars($total_classes); ?></p>
        <p><strong>Present:</strong> <?php echo htmlspecialchars($present); ?></p>
        <p><strong>Absent:</strong> <?php echo htmlspecialchars($absent); ?></p>
        <p><strong>Attendance Percentage:</strong> <?php echo htmlspecialchars($percentage); ?>%</p>

        <h3>Dates Present</h3>
        <ul>
            <?php foreach ($dates_present as $date): ?>
                <li><?php echo htmlspecialchars($date); ?></li>
            <?php endforeach; ?>
        </ul>

        <div class="buttons">
            <a href="javascript:history.back()">Back</a>
            <a href="index.php">Home</a>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
