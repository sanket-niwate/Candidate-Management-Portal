<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";
//checkLogin();

$user_id = $_SESSION['user_id'];
$error = $success = "";

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}
$entry_id = intval($_GET['id']);

// Fetch entry using MySQLi
$stmt = $conn->prepare("SELECT * FROM candidate_entries WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $entry_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$entry = $result->fetch_assoc();
$stmt->close();

if (!$entry) die("Access denied!");

$allowedProfile = ['jpg', 'jpeg', 'png'];
$allowedDocs = ['pdf', 'xls', 'xlsx'];

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
        // Profile image upload
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
                if (!empty($profile_image) && file_exists($folder . $profile_image)) unlink($folder . $profile_image);
                $profile_image = $newFile;
            }
        }

        // Document upload
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
                if (!empty($document) && file_exists($folder . $document)) unlink($folder . $document);
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
body { background: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
.card { border-radius: 12px; padding: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: 0.3s; background: #fff; }
.card:hover { transform: translateY(-3px); box-shadow: 0 6px 18px rgba(0,0,0,0.15); }
h3 { color: #333; margin-bottom: 20px; }
label { font-weight: 600; }
</style>
</head>
<body>
<div class="container mt-5" style="max-width:650px;">
    <div class="card">
        <h3>Edit Entry</h3>
        <?php if($success) echo "<div class='alert alert-success'>$success</div>"; ?>
        <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <form method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($entry['name']) ?>" required />
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($entry['email']) ?>" required />
            </div>
            <div class="mb-3">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($entry['phone']) ?>" required pattern="[0-9]{10}" />
            </div>
            <div class="mb-3">
                <label>Skills</label>
                <textarea name="skills" class="form-control"><?= htmlspecialchars($entry['skills']) ?></textarea>
            </div>
            <div class="mb-3">
                <label>Profile Picture (JPG/PNG)</label>
                <input type="file" name="profile_image" class="form-control" />
                <?php if ($entry['profile_image']): ?>
                    <img src="../uploads/profile/<?= htmlspecialchars($entry['profile_image']) ?>" width="100" class="mt-2 rounded border" alt="Profile Image" />
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label>Document (PDF/XLS/XLSX)</label>
                <input type="file" name="document" class="form-control" />
                <?php if ($entry['document']): ?>
                    <a href="../public/download.php?file=<?= urlencode($entry['document']) ?>" class="btn btn-sm btn-success mt-2">Download Existing</a>
                <?php endif; ?>
            </div>
            <div class="d-flex justify-content-between">
                <button class="btn btn-primary">Update</button>
                <a href="dashboard.php" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</div>
<script>
(function(){
const forms = document.querySelectorAll('.needs-validation');
Array.from(forms).forEach(function(form){
    form.addEventListener('submit',function(event){
        if(!form.checkValidity()){
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);
});
})();
</script>
</body>
</html>
