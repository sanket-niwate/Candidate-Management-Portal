<?php
session_start();
require_once "../includes/db.php";

// Initialize error variable
$error = "";

// Handle login submission (you can adjust as needed)
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
        $error = "Invalid credentials!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
body {
    background: #f4f6f9;
}
.login-card {
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
<div class="login-card">
    <h3 class="mb-4 text-center">Login</h3>

    <?php if($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="needs-validation" novalidate>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required />
            <div class="invalid-feedback">Enter email.</div>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required />
            <div class="invalid-feedback">Enter password.</div>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <button class="btn btn-primary">Login</button>
            <a href="register.php" class="btn btn-success">Register</a>
        </div>
    </form>
</div>

<script>
(function(){
    const forms = document.querySelectorAll('.needs-validation');
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
