<?php
include '../db.php';
session_start();

if(isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $res = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if(mysqli_num_rows($res) > 0) {
        $user = mysqli_fetch_assoc($res);
        if(password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            header("Location: ../index.php");
            exit();
        } else { $error = "Invalid password!"; }
    } else { $error = "Account not found!"; }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Welcome Back</title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f4f7fa; min-height: 100vh; display: flex; align-items: center; }
        .auth-card { background: white; border-radius: 35px; padding: 50px; box-shadow: 0 30px 60px rgba(0,0,0,0.08); border: 1px solid rgba(0,0,0,0.02); width: 100%; max-width: 420px; margin: auto; }
        .form-control { border-radius: 14px; padding: 14px; border: 1px solid #eef2f6; background: #fcfdfe; }
        .btn-auth { background: #0ea5e9; color: white; border-radius: 14px; padding: 16px; font-weight: 800; border: none; transition: 0.3s; }
        .btn-auth:hover { background: #0f172a; transform: translateY(-3px); }
    </style>
</head>
<body>
    <div class="container">
        <div class="auth-card">
            <div class="text-center mb-5">
                <h2 class="fw-800">Welcome Back</h2>
                <p class="text-muted">Enter your credentials to continue.</p>
            </div>
            
            <?php if(isset($_GET['msg'])): ?>
                <div class="alert alert-success border-0 rounded-4 py-3 small text-center">Account Ready! Please Sign In.</div>
            <?php endif; ?>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger border-0 rounded-4 py-3 small text-center"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="small fw-bold text-muted mb-2">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                </div>
                <div class="mb-4">
                    <label class="small fw-bold text-muted mb-2">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" name="login" class="btn-auth w-100 mb-4">Sign In</button>
                <p class="text-center small mb-0 text-muted">New here? <a href="register.php" class="text-primary fw-bold text-decoration-none">Create Account</a></p>
            </form>
        </div>
    </div>
</body>
</html>