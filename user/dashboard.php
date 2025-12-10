<?php
session_start();
require_once "../includes/auth.php";
require_once "../includes/db.php";
require_once "../user/navbar.php";

checkLogin();

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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
    body {
        font-family: "Inter", sans-serif;
        background: linear-gradient(135deg, #f0f4ff, #d9e2ff);
        min-height: 100vh;
    }

    .dashboard-title {
        font-weight: 800;
        font-size: 2.2rem;
        color: #1f2937;
        margin-bottom: 40px;
        text-align: center;
    }

    .dashboard-card {
        border-radius: 20px;
        padding: 40px 25px;
        background: #ffffff;
        text-align: center;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
    }

    .dashboard-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
    }

    .dashboard-card h4 {
        font-weight: 700;
        margin-bottom: 10px;
        font-size: 1.5rem;
    }

    .dashboard-card p {
        color: #6b7280;
        font-size: 1rem;
    }

    .icon-box {
        width: 65px;
        height: 65px;
        border-radius: 15px;
        background: #e0e7ff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 30px;
        color: #4f46e5;
    }

    a.text-dark:hover {
        text-decoration: none;
    }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h3 class="dashboard-title">Welcome, <?php echo $fullname; ?></h3>
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