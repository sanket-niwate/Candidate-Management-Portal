<?php
session_start();
require_once "../includes/db.php";

$error = "";

// Login Check
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role'] = $user['role'];
        header("Location: ../user/dashboard.php");
        exit;
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
    body {
        background: linear-gradient(135deg, #edf1f5, #ffffff, #eef3fa);
        font-family: "Poppins", sans-serif;
        height: 100vh;
    }

    .login-box {
        max-width: 450px;
        background: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(12px);
        margin: 90px auto;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.12);
        transition: 0.3s ease;
    }

    .login-box:hover {
        box-shadow: 0 15px 45px rgba(0, 0, 0, 0.18);
        transform: translateY(-4px);
    }

    h3 {
        font-weight: 600;
        color: #333;
    }

    label {
        font-weight: 500;
        color: #444;
    }

    .form-control {
        border-radius: 12px;
        padding: 11px 14px;
        border: 1px solid #d1d5db;
        background: #f9fafb;
    }

    .btn-login {
        background: #0d9488;
        color: white;
        border: none;
        border-radius: 12px;
        padding: 12px;
        font-weight: 600;
        width: 100%;
    }

    .btn-login:hover {
        background: #0b8277;
    }

    .btn-register {
        border-radius: 12px;
        padding: 12px;
        width: 100%;
        font-weight: 600;
    }

    .links {
        font-size: 14px;
        margin-top: 10px;
    }
    </style>
</head>

<body>

    <div class="login-box">

        <h3 class="text-center mb-4">Welcome Back</h3>

        <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="needs-validation" novalidate>

            <div class="mb-3">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required>
                <div class="invalid-feedback">Please enter your email.</div>
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
                <div class="invalid-feedback">Please enter your password.</div>
            </div>

            <button class="btn btn-login mt-2">Login</button>

            <div class="text-center links mt-3">
                Don't have an account?
                <a href="register.php" class="text-success fw-semibold">Create one</a>
            </div>

        </form>
    </div>

    <script>
    (function() {
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
    </script>

</body>

</html>