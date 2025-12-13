<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";
require_once "../user/navbar.php";

$user_id = $_SESSION['user_id'];
$error = $success = "";

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$entry_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM candidate_entries WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $entry_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$entry = $result->fetch_assoc();
$stmt->close();

if (!$entry) die("Access denied!");

// Allowed files
$allowedProfile = ['jpg', 'jpeg', 'png'];
$allowedDocs = ['pdf', 'xls', 'xlsx'];

// Handle update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $skills = trim($_POST['skills']);

    $profile_image = $entry['profile_image'];
    $document = $entry['document'];

    if (empty($name) || empty($email) || empty($phone)) {
        $error = "Name, Email, Phone required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email!";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = "Phone must be 10 digits!";
    } else {

        /* ------- PROFILE IMAGE ------- */
        if (!empty($_FILES['profile_image']['name'])) {
            $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowedProfile)) {
                $error = "Profile must be JPG/PNG!";
            } elseif ($_FILES['profile_image']['size'] > 2 * 1024 * 1024) {
                $error = "Profile max 2MB!";
            } else {
                $folder = "../uploads/profile/";
                $newFile = time() . '_' . basename($_FILES['profile_image']['name']);
                move_uploaded_file($_FILES['profile_image']['tmp_name'], $folder . $newFile);

                if (!empty($profile_image) && file_exists($folder . $profile_image))
                    unlink($folder . $profile_image);

                $profile_image = $newFile;
            }
        }

        /* ------- DOCUMENT ------- */
        if (!empty($_FILES['document']['name'])) {
            $ext = strtolower(pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowedDocs)) {
                $error = "Document must be PDF/XLS/XLSX!";
            } elseif ($_FILES['document']['size'] > 2 * 1024 * 1024) {
                $error = "Document max 2MB!";
            } else {
                $folder = "../uploads/documents/";
                $newFile = time() . '_' . basename($_FILES['document']['name']);
                move_uploaded_file($_FILES['document']['tmp_name'], $folder . $newFile);

                if (!empty($document) && file_exists($folder . $document))
                    unlink($folder . $document);

                $document = $newFile;
            }
        }
    }

    if (empty($error)) {
        $stmt = $conn->prepare("UPDATE candidate_entries SET name=?, email=?, phone=?, skills=?, profile_image=?, document=? WHERE id=? AND user_id=?");
        $stmt->bind_param("ssssssii", $name, $email, $phone, $skills, $profile_image, $document, $entry_id, $user_id);

        if ($stmt->execute()) {
            $success = "Entry updated successfully!";
            $entry['profile_image'] = $profile_image;
            $entry['document'] = $document;
        } else {
            $error = "Failed to update!";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Entry</title>
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

    .edit-card {
        background: #f4f4f4;
        padding: 35px;
        border-radius: 20px;
        box-shadow: 0 6px 20px rgba(0, 84, 97, 0.18);
        transition: 0.25s ease;
    }

    .edit-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 28px rgba(1, 135, 144, 0.35);
    }

    h3 {
        font-weight: 700;
        color: #005461;
        margin-bottom: 25px;
    }

    label {
        font-weight: 600;
        color: #005461;
    }

    .input-group-custom input,
    .input-group-custom textarea {
        border: 1.8px solid #00b7b5;
        padding: 10px 14px;
        border-radius: 10px;
        transition: 0.3s;
        font-size: 15px;
        background: #f4f4f4;
        color: #005461;
    }

    .input-group-custom input:focus,
    .input-group-custom textarea:focus {
        border-color: #018790;
        box-shadow: 0 0 6px rgba(0, 183, 181, 0.45);
    }

    .input-group-custom label {
        font-weight: 600;
        margin-bottom: 6px;
        color: #005461;
    }

    .input-group-custom input::placeholder,
    .input-group-custom textarea::placeholder {
        color: #7aaeb1;
        font-size: 14px;
    }

    .form-control {
        border-radius: 12px;
        padding: 10px 14px;
    }

    .btn-primary {
        background: #018790;
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 600;
        border: none;
    }

    .btn-primary:hover {
        background: #005461;
    }

    .btn-secondary {
        background: #00b7b5;
        border-radius: 10px;
        font-weight: 600;
        border: none;
        color: #fff;
    }

    .btn-secondary:hover {
        background: #018790;
    }

    .btn-success {
        background: #00b7b5;
        border: none;
    }

    .btn-success:hover {
        background: #018790;
    }

    .preview-img,
    #previewImage,
    #existingImage {
        border-radius: 12px;
        margin-top: 10px;
        border: 2px solid #00b7b5;
    }

    .alert-success {
        background: #e6fffa;
        border-left: 5px solid #00b7b5;
        color: #005461;
        font-weight: 500;
    }

    .alert-danger {
        background: #ffe5e5;
        border-left: 5px solid #ff6b6b;
        color: #b30000;
    }
    </style>

</head>

<body>

    <div class="container mt-5" style="max-width: 720px;">
        <div class="edit-card">

            <h3>Edit Entry</h3>

            <?php if($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <?php if($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>

                <div class="input-group-custom">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($entry['name']) ?>"
                        placeholder="Enter your full name" required>
                </div>

                <div class="input-group-custom">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control"
                        value="<?= htmlspecialchars($entry['email']) ?>" placeholder="Enter your email address"
                        required>
                </div>

                <div class="input-group-custom">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control"
                        value="<?= htmlspecialchars($entry['phone']) ?>" maxlength="10" pattern="[0-9]{10}"
                        placeholder="Enter 10-digit phone number" required>
                </div>

                <div class="input-group-custom">
                    <label>Skills</label>
                    <textarea name="skills" class="form-control" rows="3"
                        placeholder="Write your skills here..."><?= htmlspecialchars($entry['skills']) ?></textarea>
                </div>

                <div class="input-group-custom">
                    <label>Profile Image (JPG/PNG)</label>
                    <input type="file" name="profile_image" class="form-control" id="profileInput" accept="image/*">

                    <?php if (!empty($entry['profile_image'])): ?>
                    <img id="existingImage" src="../uploads/profile/<?= htmlspecialchars($entry['profile_image']) ?>"
                        width="80" height="80" style="border-radius:10px; object-fit:cover; margin-top:10px;">
                    <?php endif; ?>

                    <!-- New Preview -->
                    <img id="previewImage" src="#" style="width:80px; height:80px; border-radius:10px; object-fit:cover;
            display:none; margin-top:10px; border:2px solid #ddd;">
                </div>

                <div class="input-group-custom">
                    <label>Document (PDF/XLS/XLSX)</label>
                    <input type="file" name="document" class="form-control">

                    <?php if (!empty($entry['document'])): ?>
                    <a href="../public/download.php?file=<?= urlencode($entry['document']) ?>"
                        class="btn btn-success btn-sm mt-2">Download Current</a>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button class="btn btn-primary px-4">Update</button>
                    <a href="dashboard.php" class="btn btn-secondary px-4">Back</a>
                </div>

            </form>



        </div>
    </div>

    <script>
    (function() {
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
    </script>

    <script>
    document.getElementById("profileInput").addEventListener("change", function(event) {
        const file = event.target.files[0];
        const preview = document.getElementById("previewImage");
        const existing = document.getElementById("existingImage");

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.style.display = "block";
                preview.src = e.target.result;
                if (existing) existing.style.display = "none";
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = "none";
            preview.src = "";
            if (existing) existing.style.display = "block";
        }
    });
    </script>



</body>

</html>