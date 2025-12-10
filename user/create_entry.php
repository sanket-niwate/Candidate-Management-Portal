<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";
require_once "../user/navbar.php";

checkLogin();

$user_id = $_SESSION['user_id'];
$error = $success = "";

$allowedProfile = ['jpg','jpeg','png'];
$allowedDocs    = ['pdf','xls','xlsx'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name   = trim($_POST['name']);
    $email  = trim($_POST['email']);
    $phone  = trim($_POST['phone']);
    $skills = trim($_POST['skills']);

    $profile_image = null;
    $document = null;

    if (empty($name) || empty($email) || empty($phone)) {
        $error = "Name, Email, and Phone are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email!";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = "Phone must be 10 digits!";
    }

    if (empty($error) && !empty($_FILES['profile_image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedProfile)) {
            $error = "Profile must be JPG/PNG!";
        } elseif ($_FILES['profile_image']['size'] > 2*1024*1024) {
            $error = "Profile max 2MB!";
        } else {
            $folder = "../uploads/profile/";
            if(!is_dir($folder)) mkdir($folder,0777,true);

            $profile_image = time().'_'.uniqid().'.'.$ext;
            move_uploaded_file($_FILES['profile_image']['tmp_name'], $folder.$profile_image);
        }
    }

    if (empty($error) && !empty($_FILES['document']['name'])) {
        $ext = strtolower(pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedDocs)) {
            $error = "Document must be PDF/XLS/XLSX!";
        } elseif ($_FILES['document']['size'] > 2*1024*1024) {
            $error = "Document max 2MB!";
        } else {
            $folder = "../uploads/documents/";
            if(!is_dir($folder)) mkdir($folder,0777,true);

            $document = time().'_'.uniqid().'.'.$ext;
            move_uploaded_file($_FILES['document']['tmp_name'], $folder.$document);
        }
    }

    if (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO candidate_entries 
            (user_id, name, email, phone, skills, profile_image, document) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("issssss", $user_id, $name, $email, $phone, $skills, $profile_image, $document);

        if ($stmt->execute()) {
            $success = "Entry created successfully!";
        } else {
            $error = "Database error!";
        }

        $stmt->close();
    }

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Entry</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    body {
        background: #f4f6fa;
        font-family: "Inter", sans-serif;
    }

    .form-card {
        border-radius: 18px;
        padding: 35px;
        background: #fff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
        transition: 0.3s ease;
    }

    .form-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }

    h3 {
        font-weight: 700;
        margin-bottom: 20px;
    }

    label {
        font-weight: 600;
        margin-bottom: 5px;
    }

    footer {
        margin-top: 50px;
    }
    </style>

</head>

<body>

    <div class="container mt-5" style="max-width:700px;">
        <div class="form-card">

            <h3>Create New Entry</h3>

            <?php if($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <?php if($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control" pattern="[0-9]{10}" required>
                </div>

                <div class="mb-3">
                    <label>Skills</label>
                    <textarea name="skills" class="form-control" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label>Profile Picture (JPG/PNG)</label>
                    <input type="file" name="profile_image" class="form-control">
                </div>

                <div class="mb-3">
                    <label>Document (PDF/XLS/XLSX)</label>
                    <input type="file" name="document" class="form-control">
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <button class="btn btn-success px-4">Submit</button>
                    <a href="dashboard.php" class="btn btn-secondary px-4">Back</a>
                </div>

            </form>
        </div>
    </div>

    <!-- FOOTER
    <footer class="bg-dark text-white text-center py-3">
        <div class="container">
            <p class="mb-0" style="font-size: 0.9rem;">
                © <?= date("Y") ?> Candidate Portal. All Rights Reserved.
            </p>
            <p class="mb-0" style="font-size: 0.8rem; opacity: 0.7;">
                Designed with ❤️ for a smooth user experience.
            </p>
        </div>
    </footer> -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>