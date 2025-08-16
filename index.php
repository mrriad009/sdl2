<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
include 'db_connect.php';

// Initialize variables
$student_id = '';
$search_results = [];
$department = '';
$error_message = '';
$department_results = [];

// Get dashboard statistics
$stats = [];

// Total students
$sql = "SELECT COUNT(*) as total FROM students";
$result = $conn->query($sql);
$stats['total_students'] = $result->fetch_assoc()['total'];

// Average attendance
$sql = "SELECT AVG(percentage) as avg_attendance FROM (
    SELECT (SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / 21) * 100 AS percentage
    FROM students s
    JOIN attendance_record a ON s.id = a.student_id
    GROUP BY s.id
) as attendance_data";
$result = $conn->query($sql);
$stats['avg_attendance'] = round($result->fetch_assoc()['avg_attendance'], 1);

// Students with low attendance (< 75%)
$sql = "SELECT COUNT(*) as low_attendance FROM (
    SELECT s.id, (SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / 21) * 100 AS percentage
    FROM students s
    JOIN attendance_record a ON s.id = a.student_id
    GROUP BY s.id
    HAVING percentage < 75
) as low_attendance_data";
$result = $conn->query($sql);
$stats['low_attendance'] = $result->fetch_assoc()['low_attendance'];

// Today's classes
$today = date('Y-m-d');
$sql = "SELECT COUNT(DISTINCT student_id) as today_present FROM attendance_record WHERE class_date = ? AND status = 'Present'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();
$stats['today_present'] = $result->fetch_assoc()['today_present'];

// Handle student search by ID
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['student_id'])) {
    $student_id = $_POST['student_id'];

    $sql = "SELECT s.id, s.name, s.email, s.department, 
                   21 AS total_classes, 
                   SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS present, 
                   SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) AS absent, 
                   FLOOR((SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / 21) * 100) AS percentage
            FROM students s
            JOIN attendance_record a ON s.id = a.student_id
            WHERE s.id = ?
            GROUP BY s.id, s.name, s.email, s.department";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, $email, $department, $total_classes, $present, $absent, $percentage);
            $stmt->fetch();
            $search_results = [
                'id' => $id,
                'name' => $name,
                'email' => $email,
                'department' => $department,
                'total_classes' => $total_classes,
                'present' => $present,
                'absent' => $absent,
                'percentage' => $percentage
            ];
        } else {
            $error_message = "No student found with ID: " . htmlspecialchars($student_id);
        }
        $stmt->close();
    }
}

// Handle department filter
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['department'])) {
    $department = $_POST['department'];

    $sql = "SELECT s.id, s.name, s.email, s.department, 
                   21 AS total_classes, 
                   SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS present, 
                   SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) AS absent, 
                   FLOOR((SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / 21) * 100) AS percentage
            FROM students s
            JOIN attendance_record a ON s.id = a.student_id
            WHERE s.department = ?
            GROUP BY s.id, s.name, s.email, s.department
            ORDER BY percentage DESC";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $department);
        $stmt->execute();
        $stmt->bind_result($id, $name, $email, $department, $total_classes, $present, $absent, $percentage);
        while ($stmt->fetch()) {
            $department_results[] = [
                'id' => $id,
                'name' => $name,
                'email' => $email,
                'department' => $department,
                'total_classes' => $total_classes,
                'present' => $present,
                'absent' => $absent,
                'percentage' => $percentage
            ];
        }
        $stmt->close();
    }
}

$page_title = "Dashboard";
include 'header.php';
?>

<div class="fade-in">
    <!-- Dashboard Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['total_students']; ?></div>
            <div class="stat-label">Total Students</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['avg_attendance']; ?>%</div>
            <div class="stat-label">Average Attendance</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['low_attendance']; ?></div>
            <div class="stat-label">Low Attendance Alerts</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $stats['today_present']; ?></div>
            <div class="stat-label">Present Today</div>
        </div>
    </div>

    <!-- Main Dashboard Grid -->
    <div class="dashboard-grid">
        <!-- Student Search Card -->
        <div class="card">
            <div class="card-header">
                <h2>🔍 Student Search</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="student_id" class="form-label">Student ID</label>
                        <input type="text" name="student_id" id="student_id" class="form-input" 
                               placeholder="Enter Student ID" value="<?php echo htmlspecialchars($student_id); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-full">Search Student</button>
                </form>
            </div>
        </div>

        <!-- Department Filter Card -->
        <div class="card">
            <div class="card-header">
                <h2>🏢 Department Filter</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="department" class="form-label">Select Department</label>
                        <select name="department" id="department" class="form-select" required>
                            <option value="">--Select Department--</option>
                            <option value="Computer Science">Computer Science</option>
                            <option value="Electrical Engineering">Electrical Engineering</option>
                            <option value="Mechanical Engineering">Mechanical Engineering</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-full">View Department</button>
                </form>
            </div>
        </div>

        <!-- Subject Attendance Card -->
        <div class="card">
            <div class="card-header">
                <h2>📚 Subject Attendance</h2>
            </div>
            <div class="card-body">
                <form method="POST" action="subject_attendance.php">
                    <div class="form-group">
                        <label for="subject" class="form-label">Select Subject</label>
                        <select name="subject" id="subject" class="form-select" required>
                            <option value="">--Select Subject--</option>
                            <option value="Mathematics">Mathematics</option>
                            <option value="Physics">Physics</option>
                            <option value="Chemistry">Chemistry</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-full">View Attendance</button>
                </form>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="card">
            <div class="card-header">
                <h2>⚡ Quick Actions</h2>
            </div>
            <div class="card-body">
                <div class="flex flex-col gap-3">
                    <a href="register.php" class="btn btn-success">➕ Register New Student</a>
                    <a href="admin_panel.php" class="btn btn-secondary">👨‍💼 Admin Panel</a>
                    <button onclick="exportToCSV('attendance-table', 'attendance-report.csv')" class="btn btn-outline">📊 Export Report</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Results -->
    <?php if (!empty($search_results)) : ?>
        <div class="card mt-4">
            <div class="card-header">
                <h3>Search Results</h3>
            </div>
            <div class="card-body">
                <div class="table-wrapper">
                    <table class="table" id="search-results-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Total Classes</th>
                                <th>Present</th>
                                <th>Absent</th>
                                <th>Attendance</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo htmlspecialchars($search_results['id']); ?></td>
                                <td><?php echo htmlspecialchars($search_results['name']); ?></td>
                                <td><?php echo htmlspecialchars($search_results['department']); ?></td>
                                <td><?php echo htmlspecialchars($search_results['total_classes']); ?></td>
                                <td><?php echo htmlspecialchars($search_results['present']); ?></td>
                                <td><?php echo htmlspecialchars($search_results['absent']); ?></td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <div class="progress-bar" style="width: 100px;">
                                            <div class="progress-fill" style="width: <?php echo $search_results['percentage']; ?>%"></div>
                                        </div>
                                        <span><?php echo htmlspecialchars($search_results['percentage']); ?>%</span>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $percentage = $search_results['percentage'];
                                    if ($percentage < 75) {
                                        echo '<span class="status-badge status-danger">Low</span>';
                                    } elseif ($percentage > 90) {
                                        echo '<span class="status-badge status-success">Excellent</span>';
                                    } else {
                                        echo '<span class="status-badge status-warning">Good</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="profile.php?id=<?php echo htmlspecialchars($search_results['id']); ?>" class="btn btn-sm btn-primary">View Profile</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php elseif (isset($_POST['student_id']) && !empty($error_message)) : ?>
        <div class="alert alert-error mt-4">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <!-- Department Results -->
    <?php if (!empty($department_results)) : ?>
        <div class="card mt-4">
            <div class="card-header">
                <div class="flex justify-between items-center">
                    <h3>Students in <?php echo htmlspecialchars($department); ?></h3>
                    <button onclick="exportToCSV('department-table', '<?php echo strtolower(str_replace(' ', '-', $department)); ?>-attendance.csv')" class="btn btn-sm btn-outline">📊 Export</button>
                </div>
            </div>
            <div class="card-body">
                <div class="search-bar mb-4">
                    <input type="text" class="search-input" placeholder="Search students..." data-target="department-table">
                    <span class="search-icon">🔍</span>
                </div>
                <div class="table-wrapper">
                    <table class="table" id="department-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>ID</th>
                                <th>Total Classes</th>
                                <th>Present</th>
                                <th>Absent</th>
                                <th>Attendance</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($department_results as $student) : ?>
                                <tr>
                                    <td>
                                        <a href="profile.php?id=<?php echo htmlspecialchars($student['id']); ?>" class="text-primary">
                                            <?php echo htmlspecialchars($student['name']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($student['id']); ?></td>
                                    <td><?php echo htmlspecialchars($student['total_classes']); ?></td>
                                    <td><?php echo htmlspecialchars($student['present']); ?></td>
                                    <td><?php echo htmlspecialchars($student['absent']); ?></td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div class="progress-bar" style="width: 100px;">
                                                <div class="progress-fill" style="width: <?php echo $student['percentage']; ?>%"></div>
                                            </div>
                                            <span><?php echo htmlspecialchars($student['percentage']); ?>%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $percentage = $student['percentage'];
                                        if ($percentage < 75) {
                                            echo '<span class="status-badge status-danger">Low</span>';
                                        } elseif ($percentage > 90) {
                                            echo '<span class="status-badge status-success">Excellent</span>';
                                        } else {
                                            echo '<span class="status-badge status-warning">Good</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="profile.php?id=<?php echo htmlspecialchars($student['id']); ?>" class="btn btn-sm btn-primary">View Profile</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    // Search functionality for department table
    document.querySelectorAll('.search-input').forEach(input => {
        input.addEventListener('input', function() {
            const target = this.getAttribute('data-target');
            const filter = this.value.toLowerCase();
            const rows = document.querySelectorAll(`#${target} tbody tr`);

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                let match = false;

                cells.forEach(cell => {
                    if (cell.textContent.toLowerCase().includes(filter)) {
                        match = true;
                    }
                });

                if (match) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });

    // Export to CSV function
    function exportToCSV(tableId, filename) {
        const rows = document.querySelectorAll(`#${tableId} tr`);
        let csvContent = '';

        rows.forEach(row => {
            const cols = row.querySelectorAll('td, th');
            const rowData = [];
            cols.forEach(col => {
                rowData.push(col.innerText);
            });
            csvContent += rowData.join(',') + '\n';
        });

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.setAttribute('href', url);
        a.setAttribute('download', filename);
        a.click();
    }
</script>

<?php
include 'footer.php';
?>