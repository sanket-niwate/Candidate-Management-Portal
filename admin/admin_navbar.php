<?php 
checkAdminLogin(); 
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

    .navbar {
        backdrop-filter: blur(6px);
        background: rgba(0, 0, 0, 0.6) !important;
    }
    </style>
</head>

<body>

    <!-- Navigation -->
    <nav class="navbar navbar-dark fixed-top px-4 navbar-expand-lg">
        <a class="navbar-brand fs-4 fw-bold text-white" href="admin_dashboard.php">Admin Dashboard</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="adminNav">

            <!-- PUSH LINKS TO RIGHT -->
            <ul class="navbar-nav ms-auto align-items-center">

                <!-- Home -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="admin_dashboard.php">Home</a>
                </li>
                <!-- User View -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="admin_manage_users.php">User View</a>
                </li>

                <!-- Manage Users -->
                <li class="nav-item">
                    <a class="nav-link text-white" href="admin_manage_entry.php">Manage Entries</a>
                </li>

                <!-- Logout -->
                <li class="nav-item ms-3">
                    <a href="admin_logout.php" class="btn btn-danger btn-sm">Logout</a>
                </li>
            </ul>

        </div>
    </nav>

</body>

</html>