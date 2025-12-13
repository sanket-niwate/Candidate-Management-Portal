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
        background: linear-gradient(135deg, #005461, #018790);
        min-height: 100vh;
        font-family: "Poppins", sans-serif;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .welcome-card {
        max-width: 500px;
        padding: 40px;
        background: rgba(244, 244, 244, 0.85);
        /* #f4f4f4 */
        backdrop-filter: blur(12px);
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0, 84, 97, 0.25);
        text-align: center;
    }

    .welcome-card h1 {
        margin-bottom: 30px;
        font-weight: 600;
        color: #005461;
    }

    .welcome-card .btn-primary {
        background: #018790;
        border: none;
        width: 45%;
        margin: 5px;
        font-size: 1.1rem;
    }

    .welcome-card .btn-primary:hover {
        background: #005461;
    }

    .welcome-card .btn-success {
        background: #00b7b5;
        border: none;
        width: 45%;
        margin: 5px;
        font-size: 1.1rem;
    }

    .welcome-card .btn-success:hover {
        background: #018790;
        color: #fff;
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