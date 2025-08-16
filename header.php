<?php
// Get current page name for navigation highlighting
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Student Attendance Management System</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php if (isset($extra_head)) echo $extra_head; ?>
</head>
<body>
    <div class="main-wrapper">
        <header class="header">
            <div class="container">
                <nav class="nav">
                    <a href="index.php" class="nav-brand">
                        📚 AttendanceHub
                    </a>
                    <ul class="nav-links">
                        <li><a href="index.php" class="nav-link <?php echo $current_page == 'index' ? 'active' : ''; ?>">Dashboard</a></li>
                        <li><a href="register.php" class="nav-link <?php echo $current_page == 'register' ? 'active' : ''; ?>">Register Student</a></li>
                        <li><a href="admin_panel.php" class="nav-link <?php echo $current_page == 'admin_panel' || $current_page == 'manage_attendance' || $current_page == 'view_attendance' ? 'active' : ''; ?>">Admin</a></li>
                    </ul>