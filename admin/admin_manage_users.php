<?php 
require_once '../includes/auth.php'; 
require_once '../includes/db.php'; 
require_once '../admin/admin_navbar.php';
checkAdminLogin(); 
$users = $conn->query("
    SELECT 
        users.*, 
        candidate_entries.profile_image AS entry_image,
        candidate_entries.phone AS candidate_phone
    FROM users
    LEFT JOIN candidate_entries 
        ON candidate_entries.user_id = users.id
    ORDER BY users.id DESC
");


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
        color: #fff;
    }

    /* Container Card */
    .container-box {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(12px);
        padding: 25px;
        border-radius: 16px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    }

    /* Dashboard Title */
    .dashboard-title {
        font-weight: 800;
        font-size: 2rem;
        color: #ffffff;
        margin-bottom: 40px;
        text-align: center;
    }

    /* Table Styling */
    table {
        border-radius: 12px;
        overflow: hidden;
        table-layout: fixed;
        /* important for responsive columns */
        width: 100%;
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

    /* Table columns */
    table th,
    table td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    table td {
        word-wrap: break-word;
        vertical-align: middle;
        font-size: 0.9rem;
    }

    /* Set min-width for specific columns */
    table th:nth-child(1),
    table td:nth-child(1) {
        width: 50px;
    }

    /* ID */
    table th:nth-child(2),
    table td:nth-child(2) {
        width: 70px;
    }

    /* Profile */
    table th:nth-child(3),
    table td:nth-child(3) {
        min-width: 120px;
    }

    /* Name */
    table th:nth-child(4),
    table td:nth-child(4) {
        min-width: 180px;
    }

    /* Email */
    table th:nth-child(5),
    table td:nth-child(5) {
        width: 120px;
    }

    /* Phone */
    table th:nth-child(6),
    table td:nth-child(6) {
        width: 90px;
    }

    /* Role */
    table th:nth-child(7),
    table td:nth-child(7) {
        width: 130px;
    }

    /* Created */

    /* Tooltip for full text */
    td[title] {
        cursor: pointer;
    }

    /* Responsive adjustments */
    @media (max-width: 992px) {

        table th,
        table td {
            font-size: 0.85rem;
        }

        .dashboard-title {
            font-size: 1.8rem;
        }
    }

    @media (max-width: 576px) {

        table th,
        table td {
            font-size: 0.75rem;
        }

        .dashboard-title {
            font-size: 1.5rem;
        }

        .thumb {
            width: 40px;
            height: 40px;
        }
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
    $imageFile = $row['entry_image']; 
    $imagePath = "../uploads/profile/" . $imageFile;

    if (!empty($imageFile) && file_exists($imagePath)): ?>
                                <img src="<?php echo $imagePath; ?>" class="thumb" alt="User Image">
                                <?php else: ?>
                                <span class="text-light">No Image</span>
                                <?php endif; ?>
                            </td>


                            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo $row['candidate_phone'] ?: '-'; ?></td>


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