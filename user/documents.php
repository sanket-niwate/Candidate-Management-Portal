<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";

checkLogin(); // Only logged-in users can access

$user_id = $_SESSION['user_id'];

// Fetch candidate entries/documents uploaded by this user
$stmt = $pdo->prepare("SELECT * FROM candidate_entries WHERE user_id = ?");
$stmt->execute([$user_id]);
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Documents - Candidate Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand">My Documents</span>
        <div class="d-flex">
            <span class="text-white me-3"><?= htmlspecialchars($_SESSION['fullname']) ?></span>
            <a href="../public/logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h3 class="mb-4">Uploaded Documents</h3>

    <?php if (count($documents) === 0): ?>
        <div class="alert alert-info">No documents uploaded yet.</div>
    <?php else: ?>
        <table class="table table-bordered bg-white shadow-sm">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Candidate Name</th>
                    <th>Email</th>
                    <th>Position</th>
                    <th>Document</th>
                    <th>Uploaded On</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($documents as $index => $doc): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($doc['fullname']) ?></td>
                    <td><?= htmlspecialchars($doc['email']) ?></td>
                    <td><?= htmlspecialchars($doc['position']) ?></td>
                    <td><?= htmlspecialchars($doc['document']) ?></td>
                    <td><?= date("d M Y", strtotime($doc['created_at'])) ?></td>
                    <td>
                        <?php if (!empty($doc['document']) && file_exists("../uploads/documents/" . $doc['document'])): ?>
                            <a href="../uploads/documents/<?= urlencode($doc['document']) ?>" class="btn btn-sm btn-success" download>Download</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
