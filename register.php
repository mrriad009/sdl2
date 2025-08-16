<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set(\'display_errors\', 1);

// Database connection
include \'db_connect.php\';

// Initialize variables
$success_message = \'\';
$error_message = \'\';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST[\'id\']) ? (int)$_POST[\'id\'] : 0;
    $name = trim($_POST[\'name\']);
    $email = trim($_POST[\'email\']);
    $department = trim($_POST[\'department\']);
    $subject = NULL;

    // Validate inputs
    if ($id <= 0 || empty($name) || empty($email) || empty($department)) { 
        $error_message = "All fields are required and ID must be positive.";
    } else {
        // Check if id already exists
        $sql = "SELECT id FROM students WHERE id = ?";
        $stmt_check = $conn->prepare($sql);
        $stmt_check->bind_param("i", $id);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error_message = "Student ID already exists.";
        } else {
            $stmt_check->close();
            // Insert new student
            $sql = "INSERT INTO students (id, name, email, department) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isss", $id, $name, $email, $department);
            if ($stmt->execute()) {
                $success_message = "Student registered successfully!";
            } else {
                $error_message = "Error registering student.";
            }
            $stmt->close();
        }
    }
}

$page_title = "Register Student";
include 'header.php';
?>

<div class="fade-in">
    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <div class="card-header">
            <h1>👨‍🎓 Register New Student</h1>
            <p>Add a new student to the attendance management system</p>
        </div>
        
        <div class="card-body">
            <!-- Display Success or Error Message -->
            <?php if (!empty($success_message)) : ?>
                <div class="alert alert-success">
                    ✅ <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($error_message)) : ?>
                <div class="alert alert-error">
                    ❌ <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <!-- Registration Form -->
            <form method="POST" action="" id="registrationForm">
                <div class="form-group">
                    <label for="id" class="form-label">Student ID *</label>
                    <input type="number" name="id" id="id" class="form-input" 
                           placeholder="Enter unique student ID" 
                           value="<?php echo isset($id) ? htmlspecialchars($id) : \'\'; ?>" required>
                </div>

                <div class="form-group">
                    <label for="name" class="form-label">Full Name *</label>
                    <input type="text" name="name" id="name" class="form-input" 
                           placeholder="Enter student\'s full name" 
                           value="<?php echo isset($name) ? htmlspecialchars($name) : \'\'; ?>" required>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" name="email" id="email" class="form-input" 
                           placeholder="Enter student\'s email address" 
                           value="<?php echo isset($email) ? htmlspecialchars($email) : \'\'; ?>" required>
                </div>

                <div class="form-group">
                    <label for="department" class="form-label">Department *</label>
                    <select name="department" id="department" class="form-select" required>
                        <option value="">--Select Department--</option>
                        <option value="Computer Science" <?php echo (isset($department) && $department == \'Computer Science\') ? \'selected\' : \'\'; ?>>Computer Science</option>
                        <option value="Electrical Engineering" <?php echo (isset($department) && $department == \'Electrical Engineering\') ? \'selected\' : \'\'; ?>>Electrical Engineering</option>
                        <option value="Mechanical Engineering" <?php echo (isset($department) && $department == \'Mechanical Engineering\') ? \'selected\' : \'\'; ?>>Mechanical Engineering</option>
                    </select>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="btn btn-primary flex-1">
                        ➕ Register Student
                    </button>
                    <button type="reset" class="btn btn-outline">
                        🔄 Clear Form
                    </button>
                </div>
            </form>
        </div>
        
        <div class="card-footer">
            <div class="flex justify-between items-center">
                <a href="index.php" class="btn btn-secondary">
                    ← Back to Dashboard
                </a>
                <small class="text-gray-500">* Required fields</small>
            </div>
        </div>
    </div>

    <!-- Recent Registrations -->
    <div class="card mt-4" style="max-width: 800px; margin: 2rem auto 0;">
        <div class="card-header">
            <h3>📋 Recent Registrations</h3>
        </div>
        <div class="card-body">
            <?php
            // Get recent registrations
            $sql = "SELECT id, name, email, department FROM students ORDER BY id DESC LIMIT 5";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                echo \'<div class="table-wrapper">\';
                echo \'<table class="table">\';
                echo \'<thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Department</th><th>Action</th></tr></thead>\';
                echo \'<tbody>\';
                
                while ($row = $result->fetch_assoc()) {
                    echo \'<tr>\';
                    echo \'<td>\' . htmlspecialchars($row[\'id\']) . \'</td>\';
                    echo \'<td>\' . htmlspecialchars($row[\'name\']) . \'</td>\';
                    echo \'<td>\' . htmlspecialchars($row[\'email\']) . \'</td>\';
                    echo \'<td>\' . htmlspecialchars($row[\'department\']) . \'</td>\';
                    echo \'<td><a href="profile.php?id=\' . htmlspecialchars($row[\'id\']) . \'" class="btn btn-sm btn-primary">View</a></td>\';
                    echo \'</tr>\';
                }
                
                echo \'</tbody></table></div>\';
            } else {
                echo \'<p class="text-center text-gray-500">No students registered yet.</p>\';
            }
            ?>
        </div>
    </div>
</div>

<?php
$extra_scripts = \'\n<script>\ndocument.addEventListener("DOMContentLoaded", function() {\n    const form = document.getElementById("registrationForm");\n    \n    // Show success notification if registration was successful\n    \' . (!empty($success_message) ? \'showNotification("Student registered successfully!", "success");\' : \'\') . \'\n    \n    // Form validation enhancement\n    form.addEventListener("submit", function(e) {\n        const studentId = document.getElementById("id").value;\n        const name = document.getElementById("name").value;\n        const email = document.getElementById("email").value;\n        \n        // Additional validation\n        if (studentId <= 0) {\n            e.preventDefault();\n            showNotification("Student ID must be a positive number!", "error");\n            return;\n        }\n        \n        if (name.length < 2) {\n            e.preventDefault();\n            showNotification("Name must be at least 2 characters long!", "error");\n            return;\n        }\n        \n        // Email validation\n        const emailRegex = /^[^\\s@]+@[^\\s@]+\\.[^\\s@]+$/;\n        if (!emailRegex.test(email)) {\n            e.preventDefault();\n            showNotification("Please enter a valid email address!", "error");\n            return;\n        }\n    });\n    \n    // Auto-generate student ID suggestion\n    const idInput = document.getElementById("id");\n    if (!idInput.value) {\n        // Generate a random ID between 1000 and 9999\n        const suggestedId = Math.floor(Math.random() * 9000) + 1000;\n        idInput.placeholder = "Suggested ID: " + suggestedId;\n    }\n});\n</script>\n\';

include 'footer.php';
$conn->close();
?>

