<?php 
require_once '../includes/auth.php'; 
require_once '../includes/db.php'; 
//checkAdminLogin(); 

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
    .thumb {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #ddd;
        margin-right: 5px;
    }

    .profile-container {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .doc-preview {
        width: 60px;
        height: 80px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #ddd;
    }

    .doc-actions {
        white-space: nowrap;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-dark bg-dark px-4">
        <a href="admin_dashboard.php" class="navbar-brand">Manage Entries</a>
        <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
    </nav>
    <div class="container mt-4">
        <?php if ($result->num_rows == 0): ?>
        <div class="alert alert-info">No entries found.</div>
        <?php else: ?>
        <table class="table table-bordered table-striped">
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
                    <td><?php echo htmlspecialchars(substr($e['skills'], 0, 50)) . (strlen($e['skills']) > 50 ? '...' : ''); ?>
                    </td>

                    <!-- Profile Images (unchanged) -->
                    <td class="profile-container">
                        <?php 
                            $userImagePath = __DIR__ . '/../uploads/profile/' . $e['user_profile_image'];
                            if (!empty($e['user_profile_image']) && file_exists($userImagePath)): 
                            ?>
                        <img src="../uploads/profile/<?php echo htmlspecialchars($e['user_profile_image']); ?>"
                            class="thumb" alt="User Profile" title="User: <?php echo $e['fullname']; ?>">
                        <?php endif; ?>

                        <?php 
                            $candidateImagePath = __DIR__ . '/../uploads/profile/' . $e['profile_image'];
                            if (!empty($e['profile_image']) && file_exists($candidateImagePath)): 
                            ?>
                        <img src="../uploads/profile/<?php echo htmlspecialchars($e['profile_image']); ?>" class="thumb"
                            alt="Candidate Profile" title="Candidate: <?php echo $e['name']; ?>">
                        <?php elseif (empty($e['user_profile_image'])): ?>
                        <span class="text-muted">No Image</span>
                        <?php endif; ?>
                    </td>

                    <!-- FIXED DOCUMENT SECTION - uploads/documents/ -->
                    <td class="doc-actions">
                        <?php 
$docPath = __DIR__ . '/../uploads/documents/' . $e['document'];
$docUrl = '../uploads/documents/' . htmlspecialchars($e['document']);
$ext = strtolower(pathinfo($e['document'], PATHINFO_EXTENSION));

if (!empty($e['document']) && file_exists($docPath)): ?>
                        <div class="btn-group btn-group-sm" role="group">
                            <?php if ($ext === 'pdf'): ?>
                            <!-- Preview opens PDF in a new tab -->
                            <a href="<?php echo $docUrl; ?>" target="_blank" class="btn btn-outline-primary"
                                title="Preview PDF">
                                👁️ Preview
                            </a>
                            <?php endif; ?>
                            <!-- Download button -->
                            <a href="<?php echo $docUrl; ?>" download class="btn btn-outline-success"
                                title="Download Document">
                                ⬇️ Download
                            </a>
                        </div>
                        <?php else: ?>
                        <span class="text-muted">No File</span>
                        <?php endif; ?>

                    </td>

                    <td><?php echo date('M d, Y H:i', strtotime($e['created_at'])); ?></td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="admin_edit_entry.php?id=<?php echo $e['id']; ?>" class="btn btn-warning">Edit</a>
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

    <!-- Bootstrap JS  -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>