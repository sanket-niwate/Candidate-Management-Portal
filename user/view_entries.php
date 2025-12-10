<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";
require_once "../user/navbar.php";

checkLogin();
$user_id = $_SESSION['user_id'];

// Fetch own entries
$stmtOwn = $conn->prepare("SELECT * FROM candidate_entries WHERE user_id=? ORDER BY created_at DESC");
$stmtOwn->bind_param("i", $user_id);
$stmtOwn->execute();
$resultOwn = $stmtOwn->get_result();
$ownEntries = $resultOwn->fetch_all(MYSQLI_ASSOC);
$stmtOwn->close();

// Fetch other users' entries
$stmtOther = $conn->prepare("SELECT * FROM candidate_entries WHERE user_id != ? ORDER BY created_at DESC");
$stmtOther->bind_param("i", $user_id);
$stmtOther->execute();
$resultOther = $stmtOther->get_result();
$otherEntries = $resultOther->fetch_all(MYSQLI_ASSOC);
$stmtOther->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>User Dashboard - Entries</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />

    <style>
    body {
        background: #eef2f7;
        font-family: 'Inter', sans-serif;
    }

    .section-title {
        font-weight: 700;
        margin: 30px 0 15px;
        font-size: 1.4rem;
        color: #2d3436;
    }

    .table-card {
        background: #fff;
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 40px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: 0.3s ease;
    }

    .table-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 22px rgba(0, 0, 0, 0.12);
    }

    .table thead th {
        background: #2d3436;
        color: white;
        border: none;
        padding: 12px;
        font-size: 0.9rem;
    }

    .table tbody tr {
        vertical-align: middle;
        transition: 0.2s;
    }

    .table tbody tr:hover {
        background: #f1f8ff;
    }

    .profile-img {
        width: 55px;
        height: 55px;
        border-radius: 10px;
        object-fit: cover;
        border: 2px solid #e5e7eb;
    }

    .btn {
        border-radius: 8px !important;
    }

    .btn-warning {
        background: #ffb74d;
        border: none;
    }

    .btn-danger {
        background: #ff5252;
        border: none;
    }

    .btn-success {
        background: #66bb6a;
        border: none;
    }

    .btn-info {
        background: #42a5f5;
        border: none;
    }

    .empty-text {
        color: #666;
        font-style: italic;
        padding: 15px;
    }
    </style>
</head>

<body>

    <div class="container">

        <!-- MY ENTRIES -->
        <div class="table-card mt-4">
            <h4 class="section-title">My Entries</h4>

            <?php if (count($ownEntries) === 0): ?>
            <p class="empty-text">No entries found. Create your first entry!</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Skills</th>
                            <th>Profile</th>
                            <th>Document</th>
                            <th style="width:150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($ownEntries as $entry): ?>
                        <tr>
                            <td><?= htmlspecialchars($entry['name']) ?></td>
                            <td><?= htmlspecialchars($entry['email']) ?></td>
                            <td><?= htmlspecialchars($entry['phone']) ?></td>
                            <td><?= htmlspecialchars($entry['skills']) ?></td>

                            <td>
                                <?php if($entry['profile_image']): ?>
                                <img src="../uploads/profile/<?= htmlspecialchars($entry['profile_image']) ?>"
                                    class="profile-img" />
                                <?php else: ?>
                                <span class="text-muted">No image</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if($entry['document']): ?>
                                <a href="../public/download.php?file=<?= urlencode($entry['document']) ?>"
                                    class="btn btn-sm btn-success">Download</a>
                                <a href="../uploads/documents/<?= htmlspecialchars($entry['document']) ?>"
                                    target="_blank" class="btn btn-sm btn-info mt-1">View</a>
                                <?php else: ?>
                                <span class="text-muted">No document</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <a href="edit_entry.php?id=<?= $entry['id'] ?>" class="btn btn-sm btn-warning">Edit</a>

                                <a href="delete_entry.php?id=<?= $entry['id'] ?>" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Delete this entry?')">Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- OTHER USERS -->
        <div class="table-card">
            <h4 class="section-title">Other Users' Entries (Read-Only)</h4>

            <?php if (count($otherEntries) === 0): ?>
            <p class="empty-text">No entries from other users.</p>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Skills</th>
                            <th>Profile</th>
                            <th>Document</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($otherEntries as $entry): ?>
                        <tr>
                            <td><?= htmlspecialchars($entry['name']) ?></td>
                            <td><?= htmlspecialchars($entry['email']) ?></td>
                            <td><?= htmlspecialchars($entry['phone']) ?></td>
                            <td><?= htmlspecialchars($entry['skills']) ?></td>

                            <td>
                                <?php if($entry['profile_image']): ?>
                                <img src="../uploads/profile/<?= htmlspecialchars($entry['profile_image']) ?>"
                                    class="profile-img" />
                                <?php else: ?>
                                <span class="text-muted">No image</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if (!empty($entry['document'])): ?>
                                <span class="text-muted">Restricted</span>
                                <?php else: ?>
                                <span class="text-muted">No document</span>
                                <?php endif; ?>
                            </td>

                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>