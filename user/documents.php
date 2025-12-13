<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";
checkLogin();

$user_id = $_SESSION['user_id'];

/*----------------------------------------------------------
    FETCH ALL DOCUMENTS FOR LOGGED-IN USER (Optimized)
----------------------------------------------------------*/
$stmt = $pdo->prepare("
    SELECT id, name, email, skills, document, created_at 
    FROM candidate_entries 
    WHERE user_id = ?
    ORDER BY id DESC
");
$stmt->execute([$user_id]);
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Documents - Candidate Portal</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../user/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
    /* === Body === */
    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(to bottom, #005461, #018790);
        color: #4f4f4f;
    }

    /* === Page Title === */
    .page-title {
        font-weight: 800;
        font-size: 2rem;
        color: #f4f4f4 !important;
        margin-bottom: 25px;
        text-align: center;
    }

    /* === Table Container === */
    .table-container {
        background: #f4f4f4;
        padding: 25px;
        border-radius: 18px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    /* === Table Styling === */
    .table.table-bordered th {
        background-color: #005461 !important;
        color: #f4f4f4 !important;
        text-align: center;
        font-weight: 600;
    }

    .table.table-bordered td {
        vertical-align: middle;
        font-size: 0.95rem;
    }

    /* === Table Row Hover === */
    .table.table-bordered tbody tr:hover {
        background: #e0f7f7 !important;
        box-shadow: 0 0 10px #00b7b5, 0 0 20px #018790 !important;
        transition: all 0.3s ease;
    }

    /* === Download Button === */
    a.btn-download {
        padding: 4px 12px !important;
        font-size: 0.85rem !important;
        background: #00b7b5 !important;
        color: #f4f4f4 !important;
        border: none !important;
        border-radius: 8px !important;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease !important;
    }

    a.btn-download:hover {
        background: #018790 !important;
        box-shadow: 0 0 10px #00b7b5, 0 0 20px #018790 !important;
        color: #f4f4f4 !important;
    }

    /* === No Documents Card === */
    .no-doc-card {
        background: #f4f4f4 !important;
        padding: 30px !important;
        border-radius: 18px !important;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08) !important;
        text-align: center !important;
        color: #005461 !important;
    }

    .no-doc-card h5 {
        font-weight: 700 !important;
        margin-bottom: 8px !important;
    }

    .no-doc-card p {
        color: #4f4f4f !important;
    }
    </style>


</head>

<body>



    <div class="container mt-5">

        <h3 class="page-title">Uploaded Documents</h3>

        <?php if (empty($documents)): ?>
        <div class="no-doc-card">
            <h5>No documents uploaded yet.</h5>
            <p class="text-muted">Upload a document from your dashboard.</p>
        </div>

        <?php else: ?>
        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Skills</th>
                            <th>Document</th>
                            <th>Uploaded On</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($documents as $i => $doc): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($doc['name']) ?></td>
                            <td><?= htmlspecialchars($doc['email']) ?></td>
                            <td><?= htmlspecialchars($doc['skills']) ?></td>

                            <td>
                                <?= $doc['document']
                                            ? htmlspecialchars($doc['document'])
                                            : "<span class='text-muted'>No File</span>" ?>
                            </td>

                            <td><?= date("d M Y", strtotime($doc['created_at'])) ?></td>

                            <td class="text-center">
                                <?php 
                                            $filePath = "../uploads/documents/" . $doc['document'];
                                            if (!empty($doc['document']) && file_exists($filePath)):
                                        ?>
                                <a href="<?= $filePath ?>" class="btn btn-success btn-sm btn-download"
                                    download>Download</a>
                                <?php else: ?>
                                <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>

                        </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>