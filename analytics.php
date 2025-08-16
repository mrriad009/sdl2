<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
include 'db_connect.php';

// Get analytics data
$analytics = [];

// Department-wise statistics (fixed aggregation)
$sql = "SELECT s.department, COUNT(s.id) as total_students, ROUND(AVG(attendance_percentage),1) as avg_attendance FROM (SELECT s.id, s.department, (SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / 21) * 100 as attendance_percentage FROM students s LEFT JOIN attendance_record a ON s.id = a.student_id GROUP BY s.id, s.department) as sub GROUP BY sub.department ORDER BY avg_attendance DESC";
$result = $conn->query($sql);
$analytics['departments'] = [];
while ($row = $result->fetch_assoc()) {
    $analytics['departments'][] = $row;
}

// Subject-wise statistics
$sql = "SELECT a.subject, 
               COUNT(DISTINCT a.student_id) as total_students,
               AVG((SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / COUNT(a.class_date)) * 100) as avg_attendance
        FROM attendance_record a
        WHERE a.subject IS NOT NULL AND a.subject != ''
        GROUP BY a.subject
        ORDER BY avg_attendance DESC";
$result = $conn->query($sql);
$analytics['subjects'] = [];
while ($row = $result->fetch_assoc()) {
    $analytics['subjects'][] = $row;
}

// Weekly attendance trend (last 7 days)
$sql = "SELECT DATE(a.class_date) as date,
               COUNT(CASE WHEN a.status = 'Present' THEN 1 END) as present_count,
               COUNT(a.student_id) as total_count,
               (COUNT(CASE WHEN a.status = 'Present' THEN 1 END) / COUNT(a.student_id)) * 100 as attendance_rate
        FROM attendance_record a
        WHERE a.class_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(a.class_date)
        ORDER BY date DESC";
$result = $conn->query($sql);
$analytics['weekly_trend'] = [];
while ($row = $result->fetch_assoc()) {
    $analytics['weekly_trend'][] = $row;
}

// Top performers
$sql = "SELECT s.id, s.name, s.department,
               (SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / 21) * 100 as attendance_percentage
        FROM students s
        LEFT JOIN attendance_record a ON s.id = a.student_id
        GROUP BY s.id, s.name, s.department
        ORDER BY attendance_percentage DESC
        LIMIT 10";
$result = $conn->query($sql);
$analytics['top_performers'] = [];
while ($row = $result->fetch_assoc()) {
    $analytics['top_performers'][] = $row;
}

// Students needing attention (low attendance)
$sql = "SELECT s.id, s.name, s.department, s.email,
               (SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / 21) * 100 as attendance_percentage
        FROM students s
        LEFT JOIN attendance_record a ON s.id = a.student_id
        GROUP BY s.id, s.name, s.department, s.email
        HAVING attendance_percentage < 75
        ORDER BY attendance_percentage ASC
        LIMIT 10";
$result = $conn->query($sql);
$analytics['low_attendance'] = [];
while ($row = $result->fetch_assoc()) {
    $analytics['low_attendance'][] = $row;
}

$page_title = "Analytics Dashboard";
include 'header.php';
?>

<div class="fade-in">
    <!-- Page Header -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="flex justify-between items-center">
                <div>
                    <h1>📊 Analytics Dashboard</h1>
                    <p>Comprehensive attendance analytics and insights</p>
                </div>
                <div class="flex gap-2">
                    <a href="export.php?type=all_students&format=csv" class="btn btn-outline btn-sm">📥 Export All Data</a>
                    <a href="index.php" class="btn btn-secondary btn-sm">← Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="dashboard-grid mb-4">
        <!-- Department Performance Chart -->
        <div class="card">
            <div class="card-header">
                <h3>🏢 Department Performance</h3>
            </div>
            <div class="card-body">
                <canvas id="departmentChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Weekly Trend Chart -->
        <div class="card">
            <div class="card-header">
                <h3>📈 Weekly Attendance Trend</h3>
            </div>
            <div class="card-body">
                <canvas id="weeklyTrendChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Statistics Tables -->
    <div class="dashboard-grid">
        <!-- Department Statistics -->
        <div class="card">
            <div class="card-header">
                <div class="flex justify-between items-center">
                    <h3>🏢 Department Statistics</h3>
                    <a href="export.php?type=department&dept=all&format=csv" class="btn btn-outline btn-sm">📊 Export</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Department</th>
                                <th>Students</th>
                                <th>Avg Attendance</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($analytics['departments'] as $dept): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($dept['department']); ?></td>
                                    <td><?php echo $dept['total_students']; ?></td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div class="progress-bar" style="width: 80px;">
                                                <div class="progress-fill" style="width: <?php echo round($dept['avg_attendance']); ?>%"></div>
                                            </div>
                                            <span><?php echo round($dept['avg_attendance'], 1); ?>%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $avg = $dept['avg_attendance'];
                                        if ($avg >= 85) {
                                            echo '<span class="status-badge status-success">Excellent</span>';
                                        } elseif ($avg >= 75) {
                                            echo '<span class="status-badge status-warning">Good</span>';
                                        } else {
                                            echo '<span class="status-badge status-danger">Needs Attention</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Subject Statistics -->
        <div class="card">
            <div class="card-header">
                <div class="flex justify-between items-center">
                    <h3>📚 Subject Statistics</h3>
                    <a href="export.php?type=subject&subj=all&format=csv" class="btn btn-outline btn-sm">📊 Export</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Students</th>
                                <th>Avg Attendance</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($analytics['subjects'] as $subject): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($subject['subject']); ?></td>
                                    <td><?php echo $subject['total_students']; ?></td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div class="progress-bar" style="width: 80px;">
                                                <div class="progress-fill" style="width: <?php echo round($subject['avg_attendance']); ?>%"></div>
                                            </div>
                                            <span><?php echo round($subject['avg_attendance'], 1); ?>%</span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $avg = $subject['avg_attendance'];
                                        if ($avg >= 85) {
                                            echo '<span class="status-badge status-success">Excellent</span>';
                                        } elseif ($avg >= 75) {
                                            echo '<span class="status-badge status-warning">Good</span>';
                                        } else {
                                            echo '<span class="status-badge status-danger">Needs Attention</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Lists -->
    <div class="dashboard-grid mt-4">
        <!-- Top Performers -->
        <div class="card">
            <div class="card-header">
                <h3>🏆 Top Performers</h3>
            </div>
            <div class="card-body">
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Student</th>
                                <th>Department</th>
                                <th>Attendance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($analytics['top_performers'] as $index => $student): ?>
                                <tr>
                                    <td>
                                        <?php 
                                        $rank = $index + 1;
                                        if ($rank == 1) echo '🥇';
                                        elseif ($rank == 2) echo '🥈';
                                        elseif ($rank == 3) echo '🥉';
                                        else echo $rank;
                                        ?>
                                    </td>
                                    <td>
                                        <a href="profile.php?id=<?php echo $student['id']; ?>" class="text-primary">
                                            <?php echo htmlspecialchars($student['name']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($student['department']); ?></td>
                                    <td>
                                        <span class="status-badge status-success">
                                            <?php echo round($student['attendance_percentage'], 1); ?>%
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Students Needing Attention -->
        <div class="card">
            <div class="card-header">
                <div class="flex justify-between items-center">
                    <h3>⚠️ Students Needing Attention</h3>
                    <a href="export.php?type=low_attendance&format=csv" class="btn btn-outline btn-sm">📊 Export</a>
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($analytics['low_attendance'])): ?>
                    <div class="text-center p-4">
                        <p class="text-gray-500">🎉 Great! No students with low attendance.</p>
                    </div>
                <?php else: ?>
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Department</th>
                                    <th>Attendance</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($analytics['low_attendance'] as $student): ?>
                                    <tr>
                                        <td>
                                            <a href="profile.php?id=<?php echo $student['id']; ?>" class="text-primary">
                                                <?php echo htmlspecialchars($student['name']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo htmlspecialchars($student['department']); ?></td>
                                        <td>
                                            <span class="status-badge status-danger">
                                                <?php echo round($student['attendance_percentage'], 1); ?>%
                                            </span>
                                        </td>
                                        <td>
                                            <a href="mailto:<?php echo htmlspecialchars($student['email']); ?>" class="btn btn-sm btn-outline">
                                                📧 Contact
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
include 'footer.php';
$extra_scripts = '
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Department Performance Chart
    const deptCtx = document.getElementById("departmentChart").getContext("2d");
    new Chart(deptCtx, {
        type: "bar",
        data: {
            labels: [' . implode(',', array_map(function($d) { return '"' . $d['department'] . '"'; }, $analytics['departments'])) . '],
            datasets: [{
                label: "Average Attendance %",
                data: [' . implode(',', array_map(function($d) { return round($d['avg_attendance'], 1); }, $analytics['departments'])) . '],
                backgroundColor: ["#4f46e5", "#10b981", "#f59e0b", "#ef4444"],
                borderColor: ["#4338ca", "#059669", "#d97706", "#dc2626"],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + "%";
                        }
                    }
                }
            }
        }
    });

    // Weekly Trend Chart
    const weeklyCtx = document.getElementById("weeklyTrendChart").getContext("2d");
    new Chart(weeklyCtx, {
        type: "line",
        data: {
            labels: [' . implode(',', array_map(function($w) { return '"' . $w['date'] . '"'; }, $analytics['weekly_trend'])) . '],
            datasets: [{
                label: "Attendance Rate %",
                data: [' . implode(',', array_map(function($w) { return round($w['attendance_rate'], 1); }, $analytics['weekly_trend'])) . '],
                backgroundColor: "rgba(79, 70, 229, 0.2)",
                borderColor: "#4f46e5",
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + "%";
                        }
                    }
                }
            }
        }
    });
});
</script>
';
