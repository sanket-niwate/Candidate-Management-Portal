<?php
require_once 'includes/db.php';

// New admin details - Change these as needed
$admin_email = 'admin@example.com';
$admin_name = 'Admin User';
$admin_password = 'admin12345';  // Change this password before running

// Hash the password securely
$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

if ($conn) {
    // Prepare SQL statement to insert new admin (ignore if email already exists)
    $stmt = $conn->prepare("INSERT IGNORE INTO admins (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $admin_name, $admin_email, $hashed_password);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "Admin created successfully!<br>";
            echo "Email: $admin_email<br>";
            echo "Password: $admin_password<br>";
        } else {
            echo "Admin with this email already exists.<br>";
        }
    } else {
        echo "Error occurred: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Database connection error.";
}
?>