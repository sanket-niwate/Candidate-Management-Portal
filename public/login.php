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
        background: linear-gradient(135deg, #005461, #018790, #00b7b5);
        font-family: "Poppins", sans-serif;
        height: 100vh;
    }

    .login-box {
        max-width: 450px;
        background: rgba(244, 244, 244, 0.75);
        /* #f4f4f4 */
        backdrop-filter: blur(12px);
        margin: 90px auto;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 12px 35px rgba(0, 84, 97, 0.25);
        transition: 0.3s ease;
    }

    .login-box:hover {
        box-shadow: 0 15px 45px rgba(1, 135, 144, 0.35);
        transform: translateY(-4px);
    }

    h3 {
        font-weight: 600;
        color: #005461;
    }

    label {
        font-weight: 500;
        color: #005461;
    }

    .form-control {
        border-radius: 12px;
        padding: 11px 14px;
        border: 1px solid #00b7b5;
        background: #f4f4f4;
        color: #005461;
    }

    .form-control:focus {
        border-color: #018790;
        box-shadow: 0 0 6px rgba(0, 183, 181, 0.5);
    }

    .btn-login {
        background: #018790;
        color: white;
        border: none;
        border-radius: 12px;
        padding: 12px;
        font-weight: 600;
        width: 100%;
    }

    .btn-login:hover {
        background: #005461;
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
        color: #005461;
    }

    .links a {
        color: #00b7b5 !important;
    }

    .links a:hover {
        color: #018790 !important;
    }

    .alert-danger {
        background: #ffe5e5;
        border-left: 4px solid #ff6b6b;
        color: #b30000;
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