<?php
session_start();

$secret_code = \'12345\'; // Define your secret code here
$error_message = \'\';

if ($_SERVER[\"REQUEST_METHOD\"] == \"POST\" && isset($_POST[\"secret_code\"])) {
    if ($_POST[\"secret_code\"] === $secret_code) {
        $_SESSION[\"authenticated\"] = true;
        header(\"Location: manage_attendance.php\");
        exit();
    } else {

        $error_message = \"Invalid secret code.\";
    }
}
?>

<?php
$page_title = "Admin Panel";
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Custom Styles */
        body {
            background-color: #4ca1af; /* Updated background color */
            color: #333;
            display: flex;
            justify-content: flex-start; /* Align to the left */
            align-items: center;
            min-height: 100vh;
            padding-left: 20px; /* Add padding to the left */
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
        <h1 class="text-center">Admin Panel</h1>
        <p class="text-center">Enter your Secret Code to access the Admin Panel</p>
        <form method="POST" action="">
            <div class="form-group">
                <input type="text" name="secret_code" class="form-control" placeholder="Enter Secret Code" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Submit</button>
        </form>
        <?php if (!empty($error_message)) : ?>
            <p class="text-danger text-center mt-3"><?php echo $error_message; ?></p>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php include 'footer.php'; ?>
