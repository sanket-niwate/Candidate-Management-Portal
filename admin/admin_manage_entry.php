<?php 
require_once '../includes/auth.php'; 
require_once '../includes/db.php'; 
require_once '../admin/admin_navbar.php';
checkAdminLogin(); 

$sql = "SELECT ce.*, u.fullname, u.profile_image as user_profile_image 
        FROM candidate_entries ce 
        JOIN users u ON ce.user_id = u.id 
        ORDER BY ce.id DESC";
$result = $conn->query($sql);

if (!$result) {
    die('Database error: ' . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Entries</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        min-height: 100vh;
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        font-family: 'Poppins', sans-serif;
        padding-top: 70px;
        color: #fff;
    }


    .container-box {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(12px);
        padding: 25px;
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    }

    .table {
        border-radius: 12px;
        overflow: hidden;
    }

    thead.table-dark {
        background: rgba(0, 0, 0, 0.8) !important;
    }

    tr:hover {
        background: rgba(255, 255, 255, 0.1);
        transition: 0.3s;
    }

    .thumb {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid rgba(255, 255, 255, 0.4);
        box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.25);
    }

    .profile-container {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .doc-actions .btn {
        margin-right: 3px;
    }

    .dashboard-title {
        font-weight: 800;
        font-size: 2.2rem;
        color: #ffffffff;
        margin-bottom: 40px;
        text-align: center;
    }
    </style>
</head>

<body>


    <div class="container mt-4">
        <h3 class="dashboard-title">Manage User Entries</h3><br>
        <div class="container-box">
            <?php if ($result->num_rows == 0): ?>
            <div class="alert alert-info text-dark">No entries found.</div>
            <?php else: ?>
            <table class="table table-bordered table-striped table-hover text-white">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Skills</th>
                        <th>Profile Images</th>
                        <th>Document</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($e = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $e['id']; ?></td>
                        <td><?php echo htmlspecialchars($e['fullname']); ?></td>
                        <td><?php echo htmlspecialchars($e['name']); ?></td>
                        <td><?php echo htmlspecialchars($e['email']); ?></td>
                        <td><?php echo htmlspecialchars($e['phone']); ?></td>
                        <td><?php echo htmlspecialchars(substr($e['skills'], 0, 50)) . (strlen($e['skills'])>50?'...':''); ?>
                        </td>

                        <!-- Profile Images -->
                        <td class="profile-container">
                            <?php 
                        $userImagePath = __DIR__ . '/../uploads/profile/' . $e['user_profile_image'];
                        if (!empty($e['user_profile_image']) && file_exists($userImagePath)): ?>
                            <img src="../uploads/profile/<?php echo htmlspecialchars($e['user_profile_image']); ?>"
                                class="thumb" title="User: <?php echo $e['fullname']; ?>">
                            <?php endif; ?>

                            <?php 
                        $candidateImagePath = __DIR__ . '/../uploads/profile/' . $e['profile_image'];
                        if (!empty($e['profile_image']) && file_exists($candidateImagePath)): ?>
                            <img src="../uploads/profile/<?php echo htmlspecialchars($e['profile_image']); ?>"
                                class="thumb" title="Candidate: <?php echo $e['name']; ?>">
                            <?php elseif (empty($e['user_profile_image'])): ?>
                            <span class="text-muted">No Image</span>
                            <?php endif; ?>
                        </td>

                        <!-- Document -->
                        <td class="doc-actions">
                            <?php 
                        $docPath = __DIR__ . '/../uploads/documents/' . $e['document'];
                        $docUrl = '../uploads/documents/' . htmlspecialchars($e['document']);
                        $ext = strtolower(pathinfo($e['document'], PATHINFO_EXTENSION));
                        if (!empty($e['document']) && file_exists($docPath)): ?>
                            <div class="btn-group btn-group-sm" role="group">
                                <?php if ($ext === 'pdf'): ?>
                                <a href="<?php echo $docUrl; ?>" target="_blank" class="btn btn-outline-primary"
                                    title="Preview PDF">👁️ Preview</a>
                                <?php endif; ?>
                                <a href="<?php echo $docUrl; ?>" download class="btn btn-outline-success"
                                    title="Download">⬇️ Download</a>
                            </div>
                            <?php else: ?>
                            <span class="text-muted">No File</span>
                            <?php endif; ?>
                        </td>

                        <td><?php echo date('M d, Y H:i', strtotime($e['created_at'])); ?></td>

                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="admin_edit_entry.php?id=<?php echo $e['id']; ?>"
                                    class="btn btn-warning">Edit</a>
                                <a href="admin_delete_entry.php?id=<?php echo $e['id']; ?>" class="btn btn-danger"
                                    onclick="return confirm('Delete entry for <?php echo htmlspecialchars($e['name']); ?>?')">Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>