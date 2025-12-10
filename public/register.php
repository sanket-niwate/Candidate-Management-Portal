<?php
session_start();
require_once "../includes/db.php";

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'C:\xampp\htdocs\Candidate Management Portal\public\PHPMailer-7.0.1\src\Exception.php';
require 'C:\xampp\htdocs\Candidate Management Portal\public\PHPMailer-7.0.1\src\PHPMailer.php';
require 'C:\xampp\htdocs\Candidate Management Portal\public\PHPMailer-7.0.1\src\SMTP.php';

$mail_config = [
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'username' => 'sanketnivate2k18@gmail.com',
    'password' => 'uxyq tdwf ibtf bdjn',
    'from_email' => 'sanketnivate2k18@gmail.com',
    'from_name' => 'Candidate Portal'
];

$error = $success = "";

function sendWelcomeEmail($email, $fullname, $config) {
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host       = $config['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['username'];
        $mail->Password   = $config['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = $config['port'];

        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($email, $fullname);

        $mail->isHTML(true);
        $mail->Subject = 'Welcome to Candidate Portal!';
        $mail->Body    = "
            <h2>Hello $fullname,</h2>
            <p>Thank you for registering!</p>
            <p>Your account is ready. 
            <a href='http://localhost/Candidate Management Portal/public/login.php'>Login here</a></p>
            <hr><p>Best regards,<br>Candidate Portal Team</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email failed: {$mail->ErrorInfo}");
        return false;
    }
}

// Registration logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (empty($fullname) || empty($email) || empty($password)) {
        $error = "All fields are required!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email!";
    } elseif ($password != $confirm) {
        $error = "Passwords do not match!";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Email already exists!";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->bind_param("sss", $fullname, $email, $hash);

            if ($stmt->execute()) {
                if (sendWelcomeEmail($email, $fullname, $mail_config)) {
                    $success = "Registration successful! Welcome email sent. <a href='login.php'>Login here</a>";
                } else {
                    $success = "Registration successful but email failed! <a href='login.php'>Login here</a>";
                }
            } else {
                $error = "Registration failed!";
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
    body {
        background: linear-gradient(135deg, #edf1f5, #ffffff, #eef3fa);
        font-family: "Poppins", sans-serif;
        height: 100vh;
    }

    .register-box {
        max-width: 480px;
        background: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(12px);
        margin: 60px auto;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.12);
        transition: 0.3s ease;
    }

    .register-box:hover {
        box-shadow: 0 15px 45px rgba(0, 0, 0, 0.18);
        transform: translateY(-4px);
    }

    h3 {
        font-weight: 600;
        color: #333;
    }

    label {
        font-weight: 500;
    }

    .form-control {
        border-radius: 12px;
        padding: 11px 14px;
        background: #f9fafb;
        border: 1px solid #d1d5db;
    }

    .btn-register {
        background: #0d9488;
        color: white;
        border-radius: 12px;
        padding: 12px;
        font-weight: 600;
        width: 100%;
    }

    .btn-register:hover {
        background: #0d9488;
    }

    .btn-login {
        border-radius: 12px;
        padding: 12px;
        width: 100%;
        font-weight: 600;
    }
    </style>
</head>

<body>

    <div class="register-box">

        <h3 class="text-center mb-4">Create Account</h3>

        <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="needs-validation" novalidate>

            <div class="mb-3">
                <label>Full Name</label>
                <input type="text" name="fullname" class="form-control" required>
                <div class="invalid-feedback">Enter your full name.</div>
            </div>

            <div class="mb-3">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control" required>
                <div class="invalid-feedback">Enter a valid email.</div>
            </div>

            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
                <div class="invalid-feedback">Enter password.</div>
            </div>

            <div class="mb-3">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
                <div class="invalid-feedback">Confirm your password.</div>
            </div>

            <button class="btn btn-register mt-2">Register</button>

            <a href="login.php" class="btn btn-secondary w-100 mt-3">Already have an account? Login</a>

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