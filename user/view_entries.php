<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";

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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <style>
        body {
            background: #f4f6f9;
            font-family: 'Roboto', sans-serif;
        }
        h4 {
            font-weight: 600;
            color: #343a40;
            margin-top: 30px;
        }
        .table thead th {
            background-color: #343a40;
            color: #fff;
        }
        .table tbody tr:hover {
            background-color: #e9f5ff;
        }
        .btn-sm {
            font-size: 0.8rem;
        }
        .profile-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .container {
            padding-top: 20px;
        }
        .text-muted {
            font-size: 0.85rem;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Own Entries -->
    <h4>My Entries</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>Name</th><th>Email</th><th>Phone</th><th>Skills</th><th>Profile</th><th>Document</th><th>Actions</th>
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
                            <img src="../uploads/profile/<?= htmlspecialchars($entry['profile_image']) ?>" class="profile-img" alt="Profile"/>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($entry['document']): ?>
                            <a href="../public/download.php?file=<?= urlencode($entry['document']) ?>" class="btn btn-sm btn-success">Download</a>
                            <a href="../uploads/documents/<?= htmlspecialchars($entry['document']) ?>" target="_blank" class="btn btn-sm btn-success">View</a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_entry.php?id=<?= $entry['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="delete_entry.php?id=<?= $entry['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this entry?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Other Users' Entries -->
    <h4>Other Users' Entries (Read-only)</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>Name</th><th>Email</th><th>Phone</th><th>Skills</th><th>Profile</th><th>Document</th>
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
                            <img src="../uploads/profile/<?= htmlspecialchars($entry['profile_image']) ?>" class="profile-img" alt="Profile" />
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($entry['document'])): ?>
                            <?php if ($_SESSION['role'] === 'admin' || $entry['user_id'] == $_SESSION['user_id']): ?>
                                <a href="../download.php?id=<?= $entry['id'] ?>" class="btn btn-sm btn-info">Download</a>
                            <?php else: ?>
                                <span class="text-muted">Not allowed</span>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
