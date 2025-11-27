<?php 
require_once '../includes/auth.php'; 
require_once '../includes/db.php'; 
//checkAdminLogin(); 

$userCount = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$adminCount = $conn->query("SELECT COUNT(*) as total FROM admins")->fetch_assoc()['total'];
$entryCount = $conn->query("SELECT COUNT(*) as total FROM candidate_entries")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark px-4">
        <a class="navbar-brand">Admin Dashboard</a>
        <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
    </nav>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card p-4 shadow text-center bg-primary text-white">
                    <h4>Total Users</h4>
                    <h1><?php echo $userCount; ?></h1>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-4 shadow text-center bg-success text-white">
                    <h4>Total Entries</h4>
                    <h1><?php echo $entryCount; ?></h1>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-4 shadow text-center bg-warning text-white">
                    <h4>Admins</h4>
                    <h1><?php echo $adminCount; ?></h1>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <a href="admin_manage_users.php" class="btn btn-primary btn-lg me-2">Users View</a>
            <a href="admin_manage_entry.php" class="btn btn-success btn-lg me-2">Manage Entries</a>
        </div>
    </div>
</body>
</html>
