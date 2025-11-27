<?php 
require_once '../includes/auth.php'; 
require_once '../includes/db.php'; 
//checkAdminLogin(); 

$users = $conn->query("SELECT * FROM users ORDER BY id DESC");
if (!$users) {
    die('Database error: ' . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .thumb {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #ddd;
    }
    </style>
</head>

<body>
    <nav class="navbar navbar-dark bg-dark px-4">
        <a href="admin_dashboard.php" class="navbar-brand">Users View</a>
        <a href="../logout.php" class="btn btn-danger btn-sm">Logout</a>
    </nav>
    <div class="container mt-4">
        <h3>All Users (<?php echo $users->num_rows; ?>)</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Created</th>

                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>

                        <!-- FIXED PROFILE COLUMN - CORRECT STRUCTURE -->
                        <td>
                            <?php 
                            $userImagePath = __DIR__ . '/../uploads/profile/' . $row['profile_image'];
                            if (!empty($row['profile_image']) && file_exists($userImagePath)): 
                            ?>
                            <img src="../uploads/profile/<?php echo htmlspecialchars($row['profile_image']); ?>"
                                class="thumb" alt="Profile Image"
                                title="<?php echo htmlspecialchars($row['fullname']); ?>"
                                onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <span class="text-muted d-none">No Image</span>
                            <?php else: ?>
                            <span class="text-muted">No Image</span>
                            <?php endif; ?>
                        </td>

                        <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo $row['phone'] ?: '-'; ?></td>
                        <td>
                            <span class="badge bg-<?php echo $row['role']=='admin' ? 'danger' : 'primary'; ?>">
                                <?php echo ucfirst($row['role']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>

                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>