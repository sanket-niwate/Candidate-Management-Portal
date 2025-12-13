<?php
session_start();
require_once "../includes/auth.php";
require_once "../includes/db.php";
require_once "../user/navbar.php";
checkLogin();

$user_id  = $_SESSION['user_id'];
$fullname = $_SESSION['fullname'] ?? 'User';

/*----------------------------------------------------------
    FETCH LATEST PROFILE IMAGE (Optimized Version)
----------------------------------------------------------*/
$profileImage = "https://via.placeholder.com/40";

$stmt = $conn->prepare("
    SELECT profile_image 
    FROM candidate_entries 
    WHERE user_id = ? 
    ORDER BY id DESC 
    LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($imageFile);

if ($stmt->fetch() && $imageFile) {
    $imagePath = "../uploads/profile/$imageFile";
    if (file_exists($imagePath)) {
        $profileImage = $imagePath;
    }
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Candidate Portal</title>



    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../user/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
    body {
        /* Background gradient matching navbar colors */
        background: #003239;
        color: #4f4f4f;
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
    }

    /* Dashboard Title */
    .dashboard-title {
        font-weight: 800;
        color: #f4f4f4;
        /* Light color for contrast on dark background */
        text-align: center;
        margin-bottom: 40px;
        font-size: 2.3rem;
    }

    /* Responsive title sizes */
    @media (max-width: 992px) {
        .dashboard-title {
            font-size: 2rem;
            margin-bottom: 32px;
        }
    }

    @media (max-width: 768px) {
        .dashboard-title {
            font-size: 1.8rem;
            margin-bottom: 28px;
        }
    }

    @media (max-width: 320px) {
        .dashboard-title {
            font-size: 1.6rem;
            margin-bottom: 22px;
        }
    }

    /* Dashboard Cards */
    .dashboard-card {
        border-radius: 20px;
        padding: 35px 25px;
        background: #f4f4f4;
        /* Light card background */
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
    }

    /* Glow effect on hover */
    .dashboard-card:hover {
        transform: translateY(-8px);
        box-shadow:
            0 0 20px #00b7b5,
            /* Bright teal glow */
            0 0 30px #018790,
            /* Medium teal glow */
            0 18px 40px rgba(0, 0, 0, 0.12);
        /* Original shadow for depth */
    }


    /* Icon Box */
    .icon-box {
        width: 70px;
        height: 70px;
        border-radius: 17px;
        background: #00b7b5;
        /* Bright teal for icons matching palette */
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        margin: 0 auto 15px;
        color: #f4f4f4;
        /* Light icon color for contrast */
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .icon-box:hover {
        transform: scale(1.1);
        box-shadow: 0 8px 25px rgba(0, 183, 181, 0.35);
    }

    /* Card headings and text */
    .dashboard-card h4 {
        font-size: 1.4rem;
        font-weight: 700;
        margin-bottom: 8px;
        color: #005461;
        /* Dark teal headings for consistency */
    }

    .dashboard-card p {
        color: #4f4f4f;
        font-size: 0.95rem;
    }

    /* Links hover */
    a.text-dark:hover {
        text-decoration: none;
        color: #018790;
        /* Teal hover effect matching navbar */
    }
    </style>

</head>

<body>

    <div class="container mt-5">
        <h3 class="dashboard-title">Welcome, <?= htmlspecialchars($fullname) ?></h3>

        <div class="row g-4 justify-content-center">

            <!-- Create Entry -->
            <div class="col-md-4">
                <a href="create_entry.php" class="text-decoration-none text-dark">
                    <div class="dashboard-card">
                        <div class="icon-box">📄</div>
                        <h4>Create Entry</h4>
                        <p>Submit your personal/candidate information</p>
                    </div>
                </a>
            </div>

            <!-- My Documents -->
            <div class="col-md-4">
                <a href="view_entries.php" class="text-decoration-none text-dark">
                    <div class="dashboard-card">
                        <div class="icon-box">📁</div>
                        <h4>My Documents</h4>
                        <p>View and download your uploaded files</p>
                    </div>
                </a>
            </div>

            <!-- My Profile -->
            <div class="col-md-4">
                <a href="profile.php" class="text-decoration-none text-dark">
                    <div class="dashboard-card">
                        <div class="icon-box">👤</div>
                        <h4>My Profile</h4>
                        <p>View or update your personal details</p>
                    </div>
                </a>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>