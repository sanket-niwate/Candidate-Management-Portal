<?php
session_start();
require_once "../includes/auth.php";
require_once "../includes/db.php";

checkLogin(); // block direct access

$user_id = $_SESSION['user_id'];
$fullname = $_SESSION['fullname'] ?? 'User';

// Prepare MySQLi statement
$stmt = $conn->prepare("
    SELECT profile_image 
    FROM candidate_entries 
    WHERE user_id = ? 
    ORDER BY id DESC 
    LIMIT 1
");

if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
} else {
    $row = null;
}

$profile_path = "../uploads/profile/";

// Check if file exists
if ($row && !empty($row['profile_image']) && file_exists($profile_path . $row['profile_image'])) {
    $img_src = $profile_path . $row['profile_image'];
} else {
    // fallback image
    $img_src = "https://via.placeholder.com/40";
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Dashboard - Candidate Management Portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style> body { background: #f0f2f5; font-family: 'Roboto', sans-serif; } .navbar-brand { font-weight: 700; font-size: 1.4rem; } .dashboard-card { border-radius: 15px; padding: 30px 20px; transition: all 0.3s ease; background: #fff; box-shadow: 0 4px 12px rgba(0,0,0,0.1); } .dashboard-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.15); } .dashboard-card h4 { font-weight: 600; margin-bottom: 10px; color: #343a40; } .dashboard-card p { color: #6c757d; } .welcome-text { display: flex; align-items: center; gap: 10px; } .profile-thumb { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #fff; } a.text-dark:hover { text-decoration: none; color: #0d6efd; } @media (max-width: 767px){ .dashboard-card { padding: 20px 15px; } } </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
<div class="container-fluid">
    <span class="navbar-brand">Candidate Portal</span>
    <div class="d-flex align-items-center text-white">
        <img src="<?= htmlspecialchars($img_src) ?>" class="profile-thumb me-2" alt="Profile">
        <span>Welcome, <?= htmlspecialchars($fullname) ?></span>
        <a href="../public/logout.php" class="btn btn-danger btn-sm ms-3">Logout</a>
    </div>
</div>
</nav>


<div class="container mt-5">
    <h3 class="mb-4 fw-bold text-dark">User Dashboard</h3>
    <div class="row g-4">
        <div class="col-md-4">
            <a href="create_entry.php" class="text-decoration-none text-dark">
                <div class="dashboard-card text-center p-4 bg-white shadow-sm rounded">
                    <h4>Create Entry</h4>
                    <p>Submit your candidate information</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="view_entries.php" class="text-decoration-none text-dark">
                <div class="dashboard-card text-center p-4 bg-white shadow-sm rounded">
                    <h4>My Documents</h4>
                    <p>View or download uploaded files</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="profile.php" class="text-decoration-none text-dark">
                <div class="dashboard-card text-center p-4 bg-white shadow-sm rounded">
                    <h4>My Profile</h4>
                    <p>Update personal information</p>
                </div>
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
