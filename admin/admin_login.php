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
        // Check connection object
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

                // Check admin found
                if ($admin) {
                    // Verify password hash
                    if (password_verify($password, $admin['password'])) {
                        $_SESSION['admin_id'] = $admin['id'];
                        $_SESSION['admin_name'] = $admin['name'];
                        $_SESSION['admin_email'] = $admin['email'];
                        header('Location: admin_dashboard.php');
                        exit;
                    } else {
                        $error = 'Invalid email or password.';
                    }
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
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <h3 class="mb-3 text-center">Admin Login</h3>

                <?php if ($error): ?>
                <div class="alert alert-danger py-2">
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>

                <form method="POST" class="border rounded p-3 bg-white">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>

                <p class="mt-3 text-muted small text-center">
                    Use admin email from <code>admins</code> table.
                </p>
            </div>
        </div>
    </div>
</body>

</html>