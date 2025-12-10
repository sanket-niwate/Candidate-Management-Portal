<?php 
require_once '../includes/auth.php'; 
require_once '../includes/db.php'; 
require_once '../admin/admin_navbar.php';
checkAdminLogin(); 

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
    body {
        min-height: 100vh;
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        background-size: cover;
        font-family: "Poppins", sans-serif;
        padding-top: 70px;
    }



    .container-box {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(12px);
        padding: 25px;
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    }

    table {
        border-radius: 12px;
        overflow: hidden;
    }

    thead.table-dark {
        background: #000 !important;
    }

    tr:hover {
        background: rgba(255, 255, 255, 0.1) !important;
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
        <h3 class="dashboard-title">Users View</h3><br>
        <div class="container-box">

            <h3 class="text-white mb-3">
                All Users (<?php echo $users->num_rows; ?>)
            </h3>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover text-white">

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

                            <!-- PROFILE IMAGE -->
                            <td>
                                <?php 
                                $userImagePath = __DIR__ . '/../uploads/profile/' . $row['profile_image'];
                                if (!empty($row['profile_image']) && file_exists($userImagePath)): ?>
                                <img src="../uploads/profile/<?php echo htmlspecialchars($row['profile_image']); ?>"
                                    class="thumb"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <span class="text-light d-none">No Image</span>
                                <?php else: ?>
                                <span class="text-light">No Image</span>
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
    </div>

</body>

</html>