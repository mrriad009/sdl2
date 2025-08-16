<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
include 'db_connect.php';

// Check if export type is specified
$export_type = $_GET['type'] ?? '';
$format = $_GET['format'] ?? 'csv';

if (empty($export_type)) {
    die('Export type not specified');
}

// Set headers for file download
$filename = '';
$data = [];

switch ($export_type) {
    case 'all_students':
        $filename = 'all_students_' . date('Y-m-d');
        $sql = "SELECT s.id, s.name, s.email, s.department, 
                       21 AS total_classes, 
                       SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS present, 
                       SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) AS absent, 
                       FLOOR((SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / 21) * 100) AS percentage
                FROM students s
                LEFT JOIN attendance_record a ON s.id = a.student_id
                GROUP BY s.id, s.name, s.email, s.department
                ORDER BY s.id";
        
        $headers = ['Student ID', 'Name', 'Email', 'Department', 'Total Classes', 'Present', 'Absent', 'Attendance %'];
        break;
        
    case 'department':
        $department = $_GET['dept'] ?? '';
        if (empty($department)) {
            die('Department not specified');
        }
        
        $filename = strtolower(str_replace(' ', '_', $department)) . '_students_' . date('Y-m-d');
        $sql = "SELECT s.id, s.name, s.email, s.department, 
                       21 AS total_classes, 
                       SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS present, 
                       SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) AS absent, 
                       FLOOR((SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / 21) * 100) AS percentage
                FROM students s
                LEFT JOIN attendance_record a ON s.id = a.student_id
                WHERE s.department = ?
                GROUP BY s.id, s.name, s.email, s.department
                ORDER BY percentage DESC";
        
        $headers = ['Student ID', 'Name', 'Email', 'Department', 'Total Classes', 'Present', 'Absent', 'Attendance %'];
        break;
        
    case 'subject':
        $subject = $_GET['subj'] ?? '';
        if (empty($subject)) {
            die('Subject not specified');
        }
        
        $filename = strtolower(str_replace(' ', '_', $subject)) . '_attendance_' . date('Y-m-d');
        $sql = "SELECT s.id, s.name, s.department, a.subject,
                       COUNT(a.class_date) AS total_classes, 
                       SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS present, 
                       SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) AS absent, 
                       FLOOR((SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / COUNT(a.class_date)) * 100) AS percentage
                FROM students s
                JOIN attendance_record a ON s.id = a.student_id
                WHERE a.subject = ?
                GROUP BY s.id, s.name, s.department, a.subject
                ORDER BY percentage DESC";
        
        $headers = ['Student ID', 'Name', 'Department', 'Subject', 'Total Classes', 'Present', 'Absent', 'Attendance %'];
        break;
        
    case 'low_attendance':
        $filename = 'low_attendance_students_' . date('Y-m-d');
        $sql = "SELECT s.id, s.name, s.email, s.department, 
                       21 AS total_classes, 
                       SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS present, 
                       SUM(CASE WHEN a.status = 'Absent' THEN 1 ELSE 0 END) AS absent, 
                       FLOOR((SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / 21) * 100) AS percentage
                FROM students s
                LEFT JOIN attendance_record a ON s.id = a.student_id
                GROUP BY s.id, s.name, s.email, s.department
                HAVING percentage < 75
                ORDER BY percentage ASC";
        
        $headers = ['Student ID', 'Name', 'Email', 'Department', 'Total Classes', 'Present', 'Absent', 'Attendance %'];
        break;
        
    case 'daily_attendance':
        $date = $_GET['date'] ?? date('Y-m-d');
        $filename = 'daily_attendance_' . $date;
        $sql = "SELECT s.id, s.name, s.department, a.class_date, a.status, a.subject
                FROM students s
                JOIN attendance_record a ON s.id = a.student_id
                WHERE a.class_date = ?
                ORDER BY s.department, s.name";
        
        $headers = ['Student ID', 'Name', 'Department', 'Date', 'Status', 'Subject'];
        break;
        
    default:
        die('Invalid export type');
}

// Execute query
if (isset($department)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $department);
    $stmt->execute();
    $result = $stmt->get_result();
} elseif (isset($subject)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $subject);
    $stmt->execute();
    $result = $stmt->get_result();
} elseif (isset($date)) {
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}

// Fetch data
$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = array_values($row);
    }
}

// Export based on format
if ($format === 'csv') {
    // Set CSV headers
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
    
    // Create file pointer
    $output = fopen('php://output', 'w');
    
    // Add headers
    fputcsv($output, $headers);
    
    // Add data
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    
} elseif ($format === 'json') {
    // Set JSON headers
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="' . $filename . '.json"');
    
    // Create JSON structure
    $json_data = [
        'export_type' => $export_type,
        'export_date' => date('Y-m-d H:i:s'),
        'headers' => $headers,
        'data' => $data,
        'total_records' => count($data)
    ];
    
    echo json_encode($json_data, JSON_PRETTY_PRINT);
    
} elseif ($format === 'html') {
    // Set HTML headers
    header('Content-Type: text/html');
    
    echo '<!DOCTYPE html>';
    echo '<html><head>';
    echo '<title>' . ucfirst(str_replace('_', ' ', $export_type)) . ' Report</title>';
    echo '<style>';
    echo 'body { font-family: Arial, sans-serif; margin: 20px; }';
    echo 'table { border-collapse: collapse; width: 100%; }';
    echo 'th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }';
    echo 'th { background-color: #f2f2f2; }';
    echo 'tr:nth-child(even) { background-color: #f9f9f9; }';
    echo '.header { margin-bottom: 20px; }';
    echo '</style>';
    echo '</head><body>';
    
    echo '<div class="header">';
    echo '<h1>' . ucfirst(str_replace('_', ' ', $export_type)) . ' Report</h1>';
    echo '<p>Generated on: ' . date('Y-m-d H:i:s') . '</p>';
    echo '<p>Total Records: ' . count($data) . '</p>';
    echo '</div>';
    
    echo '<table>';
    echo '<thead><tr>';
    foreach ($headers as $header) {
        echo '<th>' . htmlspecialchars($header) . '</th>';
    }
    echo '</tr></thead>';
    
    echo '<tbody>';
    foreach ($data as $row) {
        echo '<tr>';
        foreach ($row as $cell) {
            echo '<td>' . htmlspecialchars($cell) . '</td>';
        }
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';