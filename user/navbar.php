<?php
$user_id   = $_SESSION['user_id'];
$fullname  = $_SESSION['fullname'] ?? 'User';

/* Fetch Latest Profile Image */
$profileImage = "https://via.placeholder.com/40";

$query = "
    SELECT profile_image 
    FROM candidate_entries 
    WHERE user_id = ? 
    ORDER BY id DESC 
    LIMIT 1
";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $imageFile = $row['profile_image'];
        $imagePath = "../uploads/profile/" . $imageFile;

        if (!empty($imageFile) && file_exists($imagePath)) {
            $profileImage = $imagePath;
        }
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Candidate Portal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: #eef2f7;
        font-family: "Inter", sans-serif;
    }

    /* NAVBAR STYLES */
    .navbar {
        backdrop-filter: blur(16px);
        background: rgba(255, 255, 255, 0.65) !important;
        padding: 14px 28px;
        border-bottom: 1px solid #d1d5db;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.07);
    }

    .navbar-brand {
        font-weight: 800;
        font-size: 1.55rem;
        color: #1f2937 !important;
    }

    .nav-link {
        font-weight: 600;
        font-size: 0.98rem;
        color: #374151 !important;
        margin-right: 18px;
        border-radius: 10px;
        padding: 8px 14px;
        transition: 0.25s ease;
    }

    .nav-link:hover {
        background: #e5edff;
        color: #1d4ed8 !important;
    }

    .nav-link.active {
        background: #dbe8ff;
        color: #1d4ed8 !important;
        font-weight: 700;
    }

    /* PROFILE PIC */
    .profile-thumb {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid white;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.25);
    }

    .logout-btn {
        background: #ef4444;
        border: none;
        padding: 6px 14px;
        border-radius: 8px;
        font-weight: 600;
        transition: 0.25s ease;
    }

    .logout-btn:hover {
        background: #dc2626;
        transform: scale(1.03);
    }
    </style>
</head>

<body>

    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">Candidate Portal</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMenu">
                <!-- MENU ITEMS -->
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?php if(isset($active) && $active=='home') echo 'active'; ?>"
                            href="dashboard.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if(isset($active) && $active=='create') echo 'active'; ?>"
                            href="create_entry.php">Create Entry</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if(isset($active) && $active=='documents') echo 'active'; ?>"
                            href="view_entries.php">My Documents</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if(isset($active) && $active=='profile') echo 'active'; ?>"
                            href="profile.php">My Profile</a>
                    </li>
                </ul>

                <!-- PROFILE & LOGOUT -->
                <div class="d-flex align-items-center ms-3">
                    <img src="<?= htmlspecialchars($profileImage) ?>" class="profile-thumb me-2" alt="Profile">
                    <a href="../public/logout.php" class="logout-btn text-white text-decoration-none">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>