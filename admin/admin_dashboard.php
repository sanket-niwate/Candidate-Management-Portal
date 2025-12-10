<?php 
require_once '../includes/auth.php'; 
require_once '../includes/db.php'; 
require_once '../admin/admin_navbar.php';
checkAdminLogin(); 

$userCount  = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$adminCount = $conn->query("SELECT COUNT(*) as total FROM admins")->fetch_assoc()['total'];
$entryCount = $conn->query("SELECT COUNT(*) as total FROM candidate_entries")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    body {
        min-height: 100vh;
        padding-top: 70px;
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        background-repeat: no-repeat;
        background-size: cover;
        font-family: "Poppins", sans-serif;
    }



    .stat-card {
        border-radius: 18px;
        padding: 30px;
        color: #fff;
        transition: 0.3s;
        box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.25);
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.35);
    }

    .btn-custom {
        font-size: 18px;
        padding: 12px 25px;
        border-radius: 10px;
        transition: 0.3s;
    }

    .btn-custom:hover {
        transform: scale(1.07);
    }
    </style>
</head>

<body>


    <!-- Dashboard Content -->
    <div class="container mt-4">

        <div class="row g-4">

            <!-- Users Card -->
            <div class="col-md-4">
                <div class="stat-card" style="background:#6a11cb;">
                    <h4 class="fw-bold">Total Users</h4>
                    <h1 class="display-4 fw-bold"><?php echo $userCount; ?></h1>
                </div>
            </div>

            <!-- Entries Card -->
            <div class="col-md-4">
                <div class="stat-card" style="background:#00b894;">
                    <h4 class="fw-bold">Total Entries</h4>
                    <h1 class="display-4 fw-bold"><?php echo $entryCount; ?></h1>
                </div>
            </div>

            <!-- Admins Card -->
            <div class="col-md-4">
                <div class="stat-card" style="background:#fdcb6e;">
                    <h4 class="fw-bold">Admins</h4>
                    <h1 class="display-4 fw-bold"><?php echo $adminCount; ?></h1>
                </div>
            </div>

        </div>

        <!-- Buttons -->
        <div class="mt-5">
            <a href="admin_manage_users.php" class="btn btn-primary btn-custom me-3">Users View</a>
            <a href="admin_manage_entry.php" class="btn btn-success btn-custom">Manage Entries</a>
        </div>

    </div>

</body>

</html>