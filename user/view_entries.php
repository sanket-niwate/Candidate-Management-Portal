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
        background: #002c32;
        background-image: linear-gradient(135deg, #00373f 0%, #001f23 100%);
        font-family: 'Poppins', sans-serif;
        color: #e6f7f8;
    }

    /* Heading */
    .section-title {
        font-weight: 700;
        margin: 30px 0 15px;
        font-size: 1.6rem;
        color: #003d46;
        /* darker shade of #005461 */
    }

    /* Card Container */
    .table-card {
        background: #fff;
        border-radius: 16px;
        padding: 25px;
        margin-bottom: 40px;
        border: 1px solid #e5e9ec;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
        transition: 0.3s ease-in-out;
    }


    .table-card:hover {
        box-shadow:
            0 0 12px rgba(0, 135, 144, 0.35),
            0 0 25px rgba(0, 84, 97, 0.25),
            0 8px 20px rgba(0, 0, 0, 0.15);
        transform: translateY(-3px);
    }



    /* Table Header */
    .table thead th {
        background: #005461;
        color: #ffffff;
        border: none;
        padding: 12px;
        font-size: 1rem;
        white-space: nowrap;
    }

    /* Table Body */
    .table tbody td {
        vertical-align: middle;
        word-break: break-word;
        white-space: normal;
        max-width: 230px;
        font-size: 0.95rem;
        color: #2f3b40;
    }

    /* Profile Image */
    .profile-img {
        width: 55px;
        height: 55px;
        border-radius: 10px;
        object-fit: cover;
        border: 2px solid #d9e3e6;
    }

    /* Buttons */
    .btn {
        border-radius: 8px !important;
        font-size: 0.85rem;
        padding: 6px 12px;
    }

    /* Empty State */
    .empty-text {
        color: #6b7a80;
        font-style: italic;
        padding: 15px;
        font-size: 0.95rem;
    }

    /* Hover Glow for Buttons */
    .btn-warning:hover,
    .btn-danger:hover,
    .btn-info:hover,
    .btn-success:hover {
        box-shadow: 0px 0px 12px rgba(0, 84, 97, 0.35);
    }

    /* Responsive */
    @media (max-width: 992px) {
        .section-title {
            font-size: 1.4rem;
        }

        .table thead th {
            font-size: 0.90rem;
        }

        .table tbody td {
            font-size: 0.88rem;
        }

        .profile-img {
            width: 45px;
            height: 45px;
        }
    }

    @media (max-width: 576px) {
        .section-title {
            font-size: 1.2rem;
        }

        .table thead th {
            font-size: 0.85rem;
        }

        .table tbody td {
            font-size: 0.85rem;
        }

        .profile-img {
            width: 40px;
            height: 40px;
        }
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
                            <td title="<?= htmlspecialchars($entry['skills']) ?>">
                                <?= htmlspecialchars($entry['skills']) ?>
                            </td>


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