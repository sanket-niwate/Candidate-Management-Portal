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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
    * {
        font-family: 'Poppins', sans-serif !important;
    }

    /* NAVBAR STYLES */
    .navbar,
    .navbar a,
    .nav-link,
    .dropdown-item {
        letter-spacing: 0.3px;
    }

    .navbar {
        backdrop-filter: blur(16px);
        background: rgba(0, 84, 97, 0.85);
        /* Dark teal glass effect */
        padding: 14px 28px;
        border-bottom: 1px solid #00b7b5;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
    }

    .navbar-brand {
        font-weight: 800;
        font-size: 1.55rem;
        color: #f4f4f4 !important;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }

    .nav-link {
        font-weight: 600;
        font-size: 0.98rem;
        color: #f4f4f4 !important;
        margin-right: 18px;
        border-radius: 10px;
        padding: 8px 14px;
        transition: all 0.3s ease;
    }

    .nav-link:hover {
        background: linear-gradient(135deg, #00b7b5, #018790);
        color: #f4f4f4 !important;
        box-shadow: 0 4px 12px rgba(0, 183, 181, 0.3);
    }

    .nav-link.active {
        background: #018790;
        color: #f4f4f4 !important;
        font-weight: 700;
    }

    /* PROFILE PIC */
    .profile-thumb {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #f4f4f4;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.25);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .profile-thumb:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 15px rgba(0, 183, 181, 0.5);
    }

    /* LOGOUT BUTTON */
    .logout-btn {
        background: #ef4444;
        border: none;
        padding: 6px 14px;
        border-radius: 8px;
        font-weight: 600;
        color: #fff;
        transition: all 0.25s ease;
    }

    .logout-btn:hover {
        background: #dc2626;
        transform: scale(1.05);
    }

    /* TOGGLER BUTTON */
    .navbar-toggler {
        border: none;
        outline: none;
    }

    .navbar-toggler-icon {
        background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba(244,244,244,0.9)' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
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