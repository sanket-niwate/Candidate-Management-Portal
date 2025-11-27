<?php
session_start();
require_once "../includes/db.php";

$error = $success = "";

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
        // Check if email already exists
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
                $success = "Registration successful! <a href='login.php'>Login here</a>";
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
<style>
body {
    background: #f4f6f9;
}
.register-card {
    max-width: 450px;
    margin: 80px auto;
    padding: 30px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
}
</style>
</head>
<body>
<div class="register-card">
    <h3 class="mb-4 text-center">Register</h3>

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
            <div class="invalid-feedback">Enter full name.</div>
        </div>

        <div class="mb-3">
            <label>Email</label>
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
            <div class="invalid-feedback">Confirm password.</div>
        </div>

        <button class="btn btn-success w-100">Register</button>
        <a href="login.php" class="btn btn-secondary w-100 mt-2">Already have an account? Login</a>
    </form>
</div>

<script>
(function(){
    const forms=document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(function(form){
        form.addEventListener('submit',function(event){
            if(!form.checkValidity()){
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
