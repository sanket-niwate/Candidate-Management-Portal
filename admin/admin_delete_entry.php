<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
checkAdminLogin();

if (!isset($_GET['id'])) {
    header('Location: admin_manage_entry.php'); exit;
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("DELETE FROM candidate_entries WHERE id = ?");
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    header('Location: admin_manage_entry.php?deleted=1'); exit;
} else {
    die('Delete failed!');
}
?>
