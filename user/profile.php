<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";
checkLogin();

$user_id = $_SESSION['user_id'];
$error = $success = "";

// Fetch user
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) die("User not found.");

// Fetch latest entry
$stmt2 = $conn->prepare("SELECT * FROM candidate_entries WHERE user_id = ? ORDER BY id DESC LIMIT 1");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$entry = $result2->fetch_assoc();
$stmt2->close();

$allowedProfile = ['jpg','jpeg','png'];

// Extract values
$fullname = $user['fullname'];
$email = $user['email'];
$mobile = $entry['mobile'] ?? 'Not updated';
$skills = $entry['skills'] ?? 'Not updated';

// Handle entry image update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_image']) && $entry) {
    $profile_image = $entry['profile_image'];

    if (!empty($_FILES['profile_image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedProfile)) {
            $error = "Profile image must be JPG/PNG!";
        } elseif ($_FILES['profile_image']['size'] > 2*1024*1024) {
            $error = "Profile image max 2MB!";
        } else {
            $folder = "../uploads/profile/";
            if (!is_dir($folder)) mkdir($folder, 0777, true);

            $newFile = time().'_'.uniqid().'.'.$ext;
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $folder.$newFile)) {
                if (!empty($profile_image) && file_exists($folder.$profile_image)) unlink($folder.$profile_image);
                $profile_image = $newFile;

                $stmt = $conn->prepare("UPDATE candidate_entries SET profile_image=? WHERE id=?");
                $stmt->bind_param("si", $profile_image, $entry['id']);
                if ($stmt->execute()) {
                    $success = "Entry image updated successfully!";
                    $entry['profile_image'] = $profile_image;
                } else {
                    $error = "Database update failed!";
                }
                $stmt->close();
            } else {
                $error = "Failed to upload image!";
            }
        }
    } else {
        $error = "Please choose an image to upload!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Update Entry Image</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
<style>
    body {
        background: #f4f6f9;
        font-family: 'Roboto', sans-serif;
    }
    .card-container {
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        transition: 0.3s;
    }
    .card-container:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    h3 {
        color: #343a40;
        margin-bottom: 20px;
    }
    .btn-success {
        background-color: #198754;
        border: none;
    }
    .btn-success:hover {
        background-color: #157347;
    }
    .profile-img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
</style>
</head>
<body>

<div class="container mt-5">
    <div class="card-container mx-auto" style="max-width:500px;">
        <h3>Update Entry Image</h3>

        <?php if($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if($entry): ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3 text-center">
                <label class="form-label">Current Entry Image:</label><br>
                <img src="<?= !empty($entry['profile_image']) ? '../uploads/profile/'.htmlspecialchars($entry['profile_image']) : 'https://via.placeholder.com/120' ?>" class="profile-img mb-2" alt="Profile Image">
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold">Full Name:</label>
                <p class="form-control bg-light"><?= htmlspecialchars($fullname) ?></p>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Email:</label>
                <p class="form-control bg-light"><?= htmlspecialchars($email) ?></p>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Mobile:</label>
                <p class="form-control bg-light"><?= htmlspecialchars($mobile) ?></p>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Skills:</label>
                <p class="form-control bg-light"><?= htmlspecialchars($skills) ?></p>
            </div>

            <div class="mb-3">
                <label class="form-label">Upload New Image (JPG/PNG, Max 2MB):</label>
                <input type="file" name="profile_image" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success w-100" name="update_image">Update Image</button>
        </form>
        <?php else: ?>
            <p class="text-muted text-center">No entry found to update.</p>
        <?php endif; ?>

        <a href="dashboard.php" class="btn btn-secondary mt-3 w-100">Back</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
