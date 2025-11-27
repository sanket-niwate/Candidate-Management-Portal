<?php
require_once "../includes/auth.php";
require_once "../includes/db.php";
checkLogin();

if (!isset($_GET['file'])) die("Invalid request!");
$file = basename($_GET['file']);

// Check in documents folder
$filepath = "../uploads/documents/$file";
if (!file_exists($filepath)) die("File not found!");

// Check ownership
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM candidate_entries WHERE user_id = ? AND document = ?");
$stmt->bind_param("is", $user_id, $file);
$stmt->execute();
$result = $stmt->get_result();
$entry = $result->fetch_assoc();
$stmt->close();
if (!$entry) die("Access denied!");

// Serve file for download
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filepath));
readfile($filepath);
exit;
?>
