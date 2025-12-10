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
        background: url('https://images.unsplash.com/photo-1521790361543-f645cf042ec0?auto=format&fit=crop&w=1350&q=80') no-repeat center center/cover;
        min-height: 100vh;
        backdrop-filter: blur(4px);
        font-family: "Poppins", sans-serif;
    }

    .welcome-card {
        max-width: 500px;
        margin: 120px auto;
        padding: 40px;
        background: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(12px);
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
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