<?php
session_start();
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') header("Location: ../admin/dashboard.php");
    else header("Location: ../user/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Candidate Management Portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: #f4f6f9;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.welcome-card {
    max-width: 500px;
    margin: 100px auto;
    padding: 40px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    text-align: center;
}
.welcome-card h1 {
    margin-bottom: 30px;
    font-weight: 600;
    color: #333;
}
.welcome-card .btn {
    width: 45%;
    margin: 5px;
    font-size: 1.1rem;
}
</style>
</head>
<body>

<div class="welcome-card">
    <h1>Candidate Management Portal</h1>
    <a href="login.php" class="btn btn-primary btn-lg">Login</a>
    <a href="register.php" class="btn btn-success btn-lg">Register</a>
</div>

</body>
</html>
