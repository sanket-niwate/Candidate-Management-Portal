<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";
checkLogin();

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}
$entry_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Prepare MySQLi statement to delete own entry
$stmt = $conn->prepare("DELETE FROM candidate_entries WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $entry_id, $user_id);
$stmt->execute();
$stmt->close();

header("Location: dashboard.php");
exit;
?>
