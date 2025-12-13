<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";
require_once "../user/navbar.php";
checkLogin();

$user_id = $_SESSION['user_id'];
$error = $success = "";

// Allowed file types
$allowedProfile = ['jpg', 'jpeg', 'png'];
$allowedDocs    = ['pdf', 'xls', 'xlsx'];

/*-------------------------------------------
    REUSABLE FILE UPLOAD FUNCTION
-------------------------------------------*/
function uploadFile($file, $allowed, $folderName, $maxSizeMB = 2) {

    if (empty($file['name'])) return null;

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return "invalid";

    if ($file['size'] > ($maxSizeMB * 1024 * 1024)) return "size";

    $folder = "../uploads/$folderName/";
    if (!is_dir($folder)) mkdir($folder, 0777, true);

    $newName = time() . '_' . uniqid() . '.' . $ext;
    move_uploaded_file($file['tmp_name'], $folder . $newName);

    return $newName;
}

/*-------------------------------------------
    FORM SUBMIT
-------------------------------------------*/
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name   = trim($_POST['name']);
    $email  = trim($_POST['email']);
    $phone  = trim($_POST['phone']);
    $skills = trim($_POST['skills']);

    // Basic Validation
    if (!$name || !$email || !$phone) {
        $error = "Name, Email, and Phone are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email!";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = "Phone must be 10 digits!";
    }

    if (!$error) {
        // Upload Files
        $profile_image = uploadFile($_FILES['profile_image'], $allowedProfile, "profile");
        $document      = uploadFile($_FILES['document'], $allowedDocs, "documents");

        if ($profile_image === "invalid")     $error = "Profile must be JPG or PNG!";
        elseif ($profile_image === "size")    $error = "Profile must be less than 2MB!";
        elseif ($document === "invalid")      $error = "Document must be PDF/XLS/XLSX!";
        elseif ($document === "size")         $error = "Document must be less than 2MB!";
    }

    // Insert into DB
    if (!$error) {

        $stmt = $conn->prepare("INSERT INTO candidate_entries 
            (user_id, name, email, phone, skills, profile_image, document)
            VALUES (?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("issssss", $user_id, $name, $email, $phone, $skills, $profile_image, $document);

        $success = $stmt->execute() ? "Entry created successfully!" : "Database error!";
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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../user/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
    body {
        /* Linear gradient using ColorHunt palette: dark teal to teal */
        background: #003239;
        color: #4f4f4f;
        /* Base text color */
        font-family: 'Arial', sans-serif;
        margin: 0;
        padding: 0;
    }

    .form-card {
        border-radius: 20px;
        padding: 35px;
        background: linear-gradient(145deg, #f4f4f4, #e0f7f7);
        /* Soft gradient */
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08), 0 4px 10px rgba(0, 0, 0, 0.04);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .form-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12), 0 8px 20px rgba(0, 0, 0, 0.06);
    }

    label {
        font-weight: 600;
        margin-bottom: 5px;
        color: #005461;
        /* Dark teal for labels */
        display: block;
        font-size: 0.95rem;
    }

    .input-group-custom input,
    .input-group-custom textarea {
        border: 1.8px solid #018790;
        /* Teal border */
        padding: 12px 16px;
        border-radius: 12px;
        transition: all 0.3s ease;
        font-size: 15px;
        background: #ffffff;
        color: #4f4f4f;
        width: 100%;
    }

    .input-group-custom input::placeholder,
    .input-group-custom textarea::placeholder {
        color: #005461aa;
        /* Slightly transparent teal */
    }

    .input-group-custom input:focus,
    .input-group-custom textarea:focus {
        border-color: #00b7b5;
        box-shadow: 0 0 8px rgba(0, 183, 181, 0.35);
        outline: none;
        background: #f4f4f4;
        /* Slight highlight on focus */
    }

    #previewImage {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        border: 2px solid #018790;
        object-fit: cover;
        display: none;
        margin-top: 8px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    #previewImage.show {
        display: block;
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    </style>


</head>

<body>

    <div class="container mt-5" style="max-width:700px;">
        <div class="form-card">

            <h3 class="text-center mb-3 fw-bold">Create New Entry</h3>

            <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
            <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

            <form method="POST" enctype="multipart/form-data">

                <div class="input-group-custom mb-3">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter full name" required>
                </div>

                <div class="input-group-custom mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter email" required>
                </div>

                <div class="input-group-custom mb-3">
                    <label>Phone</label>
                    <input type="text" name="phone" maxlength="10" pattern="[0-9]{10}" class="form-control"
                        placeholder="10-digit number" required>
                </div>

                <div class="input-group-custom mb-3">
                    <label>Skills</label>
                    <textarea name="skills" class="form-control" rows="3" placeholder="Describe skills..."></textarea>
                </div>

                <div class="input-group-custom mb-3">
                    <label>Profile Picture (JPG/PNG)</label>
                    <input type="file" name="profile_image" class="form-control" id="profileInput" accept="image/*">

                    <img id="previewImage" src="#">
                </div>

                <div class="input-group-custom mb-3">
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

    <script>
    document.getElementById("profileInput").addEventListener("change", function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById("previewImage");

        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                preview.src = event.target.result;
                preview.style.display = "block";
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = "none";
            preview.src = "";
        }
    });
    </script>

</body>

</html>