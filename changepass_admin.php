<?php
require_once 'includes/db.php';

$admin_email = 'admin@example.com';
$admin_name = 'Admin User';
$admin_password = 'admin12345';  // Change this password

// Hash the password
$hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT IGNORE INTO admins (name, email, password) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $admin_name, $admin_email, $hashed_password);

if ($stmt->execute()) {
    echo "Admin created successfully!<br>";
    echo "Email: $admin_email<br>";
    echo "Password: $admin_password<br>";
} else {
    echo "Admin already exists or error occurred.";
}

$stmt->close();
?>



<?php
require_once 'includes/db.php';
$result = $conn->query("SELECT id, name, email FROM admins");
while ($row = $result->fetch_assoc()) {
    echo "ID: {$row['id']}, Name: {$row['name']}, Email: {$row['email']}<br>";
}
?>