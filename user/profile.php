<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";
require_once "../user/navbar.php";
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
$mobile = $entry['phone'] ?? 'Not updated';
$skills = $entry['skills'] ?? 'Not updated';

// Image Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_image']) && $entry) {
    $profile_image = $entry['profile_image'];

    if (!empty($_FILES['profile_image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedProfile)) {
            $error = "Profile image must be JPG/PNG!";
        } elseif ($_FILES['profile_image']['size'] > 2*1024*1024) {
            $error = "Max size 2MB!";
        } else {
            $folder = "../uploads/profile/";
            if (!is_dir($folder)) mkdir($folder, 0777, true);

            $newFile = time().'_'.uniqid().'.'.$ext;

            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $folder.$newFile)) {

                if (!empty($profile_image) && file_exists($folder.$profile_image)) {
                    unlink($folder.$profile_image);
                }

                $profile_image = $newFile;

                $stmt = $conn->prepare("UPDATE candidate_entries SET profile_image=? WHERE id=?");
                $stmt->bind_param("si", $profile_image, $entry['id']);

                if ($stmt->execute()) {
                    $success = "Image updated successfully!";
                    $entry['profile_image'] = $profile_image;
                } else {
                    $error = "Database update failed!";
                }
                $stmt->close();

            } else {
                $error = "Upload failed!";
            }
        }
    } else {
        $error = "Select an image!";
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

    <style>
    body {
        /* Background gradient matching navbar colors */
        background: #003239;
        color: #4f4f4f;
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
    }

    .container-box {
        max-width: 550px;
        margin-top: 50px;
    }

    .card-custom {
        background: #f4f4f4cc;
        /* Light grey with transparency */
        backdrop-filter: blur(10px);
        padding: 35px;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(0, 84, 97, 0.15);
        /* Teal shadow */
        transition: 0.3s ease;
    }

    .card-custom:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(1, 135, 144, 0.25);
        /* Hover shadow */
    }

    .profile-img {
        width: 130px;
        height: 130px;
        object-fit: cover;
        border-radius: 15px;
        border: 2px solid #00b7b5;
        /* Border color updated */
        box-shadow: 0 4px 8px rgba(0, 84, 97, 0.15);
    }

    .label-box {
        font-weight: 600;
        margin-bottom: 6px;
        color: #005461;
        /* Dark teal text */
    }

    .display-box {
        background: #e8fafa;
        /* Soft teal tint */
        border-radius: 10px;
        padding: 10px 14px;
        min-height: 40px;
        display: flex;
        align-items: center;
        word-wrap: break-word;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
        font-size: 0.95rem;
        color: #005461;
        /* Text color */
    }

    .btn-save {
        background: #018790;
        /* Button main color */
        border: none;
        padding: 10px;
        border-radius: 10px;
        font-weight: 600;
        color: white;
    }

    .btn-save:hover {
        background: #005461;
        /* Darker hover */
    }

    .btn-back {
        border-radius: 10px;
        padding: 10px;
        background: #00b7b5;
        color: white;
    }

    .btn-back:hover {
        background: #018790;
    }
    </style>

</head>

<body>

    <div class="container container-box">
        <div class="card-custom mx-auto">

            <h3 class="text-center mb-3 fw-bold">Update Entry Image</h3>

            <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($entry): ?>
            <form method="POST" enctype="multipart/form-data">

                <div class="text-center mb-3">
                    <label class="label-box">Current Image</label><br>
                    <img src="<?= !empty($entry['profile_image']) ? '../uploads/profile/'.htmlspecialchars($entry['profile_image']) : 'https://via.placeholder.com/130' ?>"
                        class="profile-img mb-2" alt="Profile Image">
                </div>

                <div class="mb-3">
                    <label class="label-box">Full Name</label>
                    <div class="display-box"><?= htmlspecialchars($fullname) ?></div>
                </div>

                <div class="mb-3">
                    <label class="label-box">Email</label>
                    <div class="display-box"><?= htmlspecialchars($email) ?></div>
                </div>

                <div class="mb-3">
                    <label class="label-box">Mobile</label>
                    <div class="display-box"><?= htmlspecialchars($mobile) ?></div>
                </div>

                <div class="mb-3">
                    <label class="label-box">Skills</label>
                    <div class="display-box"><?= htmlspecialchars($skills) ?></div>
                </div>


                <div class="mb-3">
                    <label class="label-box">Upload New Image (JPG/PNG, Max 2MB)</label>
                    <input type="file" name="profile_image" class="form-control rounded-3" required>
                </div>

                <button type="submit" class="btn btn-save text-white w-100" name="update_image">
                    Update Image
                </button>

            </form>

            <?php else: ?>
            <p class="text-muted text-center">No entry found to update.</p>
            <?php endif; ?>

            <a href="dashboard.php" class="btn btn-secondary btn-back mt-3 w-100">Back to Dashboard</a>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>