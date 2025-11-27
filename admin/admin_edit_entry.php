<?php 
require_once '../includes/auth.php'; 
require_once '../includes/db.php'; 
//checkAdminLogin(); 

$error = $success = '';
if (!isset($_GET['id'])) {
    header('Location: admin_manage_entry.php'); exit;
}
$entry_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM candidate_entries WHERE id = ?");
$stmt->bind_param("i", $entry_id);
$stmt->execute();
$result = $stmt->get_result();
$entry = $result->fetch_assoc();
$stmt->close();

if (!$entry) die('Entry not found!');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $skills = trim($_POST['skills']);
    $profile_image = $entry['profile_image'];
    $document = $entry['document'];

    if (empty($name) || empty($email) || empty($phone)) {
        $error = 'Name, Email, Phone required!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email!';
    } elseif (!preg_match('/[0-9]{10}/', $phone)) {
        $error = 'Phone must be 10 digits!';
    } else {
        // Profile image upload
        if (!empty($_FILES['profile_image']['name'])) {
            $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg','jpeg','png'])) {
                $error = 'Profile must be JPG/PNG!';
            } elseif ($_FILES['profile_image']['size'] > 2*1024*1024) {
                $error = 'Profile max 2MB!';
            } else {
                $folder = '../uploads/';
                $newFile = time() . '_' . basename($_FILES['profile_image']['name']);
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $folder . $newFile)) {
                    if (!empty($profile_image) && file_exists($folder . $profile_image)) {
                        unlink($folder . $profile_image);
                    }
                    $profile_image = $newFile;
                }
            }
        }

        // Document upload
        if (!empty($_FILES['document']['name']) && empty($error)) {
            $ext = strtolower(pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ['pdf','xls','xlsx'])) {
                $error = 'Document must be PDF/XLS/XLSX!';
            } elseif ($_FILES['document']['size'] > 2*1024*1024) {
                $error = 'Document max 2MB!';
            } else {
                $folder = '../uploads/';
                $newFile = time() . '_' . basename($_FILES['document']['name']);
                if (move_uploaded_file($_FILES['document']['tmp_name'], $folder . $newFile)) {
                    if (!empty($document) && file_exists($folder . $document)) {
                        unlink($folder . $document);
                    }
                    $document = $newFile;
                }
            }
        }

        if (empty($error)) {
            $stmt = $conn->prepare("UPDATE candidate_entries SET name=?, email=?, phone=?, skills=?, profile_image=?, document=? WHERE id=?");
            $stmt->bind_param("ssssssi", $name, $email, $phone, $skills, $profile_image, $document, $entry_id);
            if ($stmt->execute()) {
                $success = 'Entry updated successfully!';
                $entry['name'] = $name; $entry['email'] = $email; $entry['phone'] = $phone;
                $entry['skills'] = $skills; $entry['profile_image'] = $profile_image; $entry['document'] = $document;
            } else {
                $error = 'Update failed!';
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Entry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark px-4">
        <a href="admin_manage_entry.php" class="navbar-brand">← Back to Entries</a>
        <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
    </nav>
    <div class="container mt-4">
        <?php if($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>
        <?php if($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($entry['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($entry['email']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($entry['phone']); ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Skills</label>
                        <textarea name="skills" class="form-control" rows="4"><?php echo htmlspecialchars($entry['skills']); ?></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Profile Image</label>
                        <input type="file" name="profile_image" class="form-control" accept="image/*">
                        <?php if($entry['profile_image']): ?>
                            <div class="mt-2">
                                <img src="../uploads/<?php echo htmlspecialchars($entry['profile_image']); ?>" width="100" class="rounded border">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Document</label>
                        <input type="file" name="document" class="form-control" accept=".pdf,.xls,.xlsx">
                       
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Entry</button>
            <a href="admin_manage_entry.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
