<?php
require_once '../includes/db.php';
session_start();

$error = '';

// Redirect if already logged in
if (!empty($_SESSION['admin_id'])) {
    header('Location: admin_dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email === '' || $password === '') {
        $error = 'Email and password are required.';
    } else {
        if (!$conn) {
            $error = 'Database connection error.';
        } else {
            $stmt = $conn->prepare("SELECT id, name, email, password FROM admins WHERE email = ?");
            if (!$stmt) {
                $error = 'Database query error.';
            } else {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $res = $stmt->get_result();
                $admin = $res->fetch_assoc();
                $stmt->close();

                if ($admin && password_verify($password, $admin['password'])) {
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_name'] = $admin['name'];
                    $_SESSION['admin_email'] = $admin['email'];
                    header('Location: admin_dashboard.php');
                    exit;
                } else {
                    $error = 'Invalid email or password.';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
    body {
        min-height: 100vh;
        background: linear-gradient(135deg, #6a11cb, #2575fc);
        background-size: cover;
        background-position: center;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        font-family: "Poppins", sans-serif;
        /* OPTIONAL — uncomment below to add a background image */

        /* background: url('https://images.unsplash.com/photo-1503676260728-1c00da094a0b') no-repeat center center/cover; */
    }

    .login-card {
        background: rgba(255, 255, 255, 0.90);
        backdrop-filter: blur(6px);
        border-radius: 15px;
        padding: 25px;
        width: 360px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
    }

    .title-text {
        font-weight: 700;
        color: #333;
    }

    .btn-primary {
        background: #6a11cb;
        border: none;
    }

    .btn-primary:hover {
        background: #520fa3;
    }
    </style>
</head>

<body>

    <div class="login-card">

        <h3 class="text-center mb-3 title-text">Admin Login</h3>

        <?php if ($error): ?>
        <div class="alert alert-danger py-2">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-2">Login</button>
        </form>

        <p class="mt-3 text-muted small text-center">
            Use admin email from <code>Admins</code> table.
        </p>

    </div>

</body>

</html>