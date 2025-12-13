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

    .dashboard-title {
        font-weight: 800;
        font-size: 2.2rem;
        margin-bottom: 30px;
        text-align: center;
    }

    .table-responsive {
        overflow-x: auto;
    }

    table {
        width: 100%;
        table-layout: auto;
        /* allow auto width for responsive columns */
    }

    .table th,
    .table td {
        vertical-align: middle;
        text-align: center;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    .table td.skills {
        max-width: 200px;
    }

    /* .profile-container {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    } */

    .thumb {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid rgba(0, 0, 0, 0.4);
        box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.25);
    }

    /* Document & Actions columns */
    /* .doc-actions,
    .actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        gap: 4px;
    }

    .doc-actions .btn,
    .actions .btn {
        white-space: nowrap;
        font-size: 12px;
        padding: 4px 6px;
    } */

    /* Smaller screens adjustments */
    @media (max-width: 768px) {

        .table th,
        .table td {
            font-size: 12px;
        }

        .table td.skills {
            max-width: 150px;
        }

        .thumb {
            width: 40px;
            height: 40px;
        }
    }

    tr:hover {
        background: rgba(255, 255, 255, 0.1);
        transition: 0.3s;
    }

    thead.table-dark {
        background: rgba(0, 0, 0, 0.8) !important;
    }
    </style>
</head>

<body>
    <div class="container mt-4">
        <h3 class="dashboard-title">Manage User Entries</h3>
        <div class="container-box">
            <?php if ($result->num_rows == 0): ?>
            <div class="alert alert-info text-dark">No entries found.</div>
            <?php else: ?>
            <div class="table-responsive">
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
                            <td><?= $e['id'] ?></td>
                            <td><?= htmlspecialchars($e['fullname']) ?></td>
                            <td><?= htmlspecialchars($e['name']) ?></td>
                            <td><?= htmlspecialchars($e['email']) ?></td>
                            <td><?= htmlspecialchars($e['phone']) ?></td>
                            <td class="skills"><?= htmlspecialchars($e['skills']) ?></td>

                            <!-- Profile Images -->
                            <td class="profile-container">
                                <?php 
                                $userImage = __DIR__ . '/../uploads/profile/' . $e['user_profile_image'];
                                $candidateImage = __DIR__ . '/../uploads/profile/' . $e['profile_image'];
                                if (!empty($e['user_profile_image']) && file_exists($userImage)): ?>
                                <img src="../uploads/profile/<?= htmlspecialchars($e['user_profile_image']) ?>"
                                    class="thumb" title="User: <?= $e['fullname'] ?>">
                                <?php endif; ?>
                                <?php if (!empty($e['profile_image']) && file_exists($candidateImage)): ?>
                                <img src="../uploads/profile/<?= htmlspecialchars($e['profile_image']) ?>" class="thumb"
                                    title="Candidate: <?= $e['name'] ?>">
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
                                <div class="d-flex flex-wrap justify-content-center gap-2">
                                    <?php if ($ext === 'pdf'): ?>
                                    <a href="<?= $docUrl ?>" target="_blank" class="btn btn-outline-primary btn-sm">👁️
                                        Preview</a>
                                    <?php endif; ?>
                                    <a href="<?= $docUrl ?>" download class="btn btn-outline-success btn-sm">⬇️
                                        Download</a>
                                </div>
                                <?php else: ?>
                                <span class="text-muted">No File</span>
                                <?php endif; ?>
                            </td>

                            <!-- Created Date -->
                            <td><?= date('M d, Y H:i', strtotime($e['created_at'])) ?></td>

                            <!-- Actions -->
                            <td class="actions">
                                <div class="d-flex flex-wrap justify-content-center gap-2">
                                    <a href="admin_edit_entry.php?id=<?= $e['id'] ?>"
                                        class="btn btn-warning btn-sm">Edit</a>
                                    <a href="admin_delete_entry.php?id=<?= $e['id'] ?>" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Delete entry for <?= htmlspecialchars($e['name']) ?>?')">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>