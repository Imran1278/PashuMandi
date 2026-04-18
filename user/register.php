<?php
include '../db.php';
session_start();

if(isset($_POST['register'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    // Image Handling
    $profile_pic = 'default_user.png'; // Default image
    if(!empty($_FILES['profile_pic']['name'])) {
        $target_dir = "../uploads/";
        $profile_pic = time() . "_" . basename($_FILES["profile_pic"]["name"]);
        move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_dir . $profile_pic);
    }

    $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if(mysqli_num_rows($check) > 0) {
        $error = "Email already registered!";
    } else {
        $q = "INSERT INTO users (full_name, email, phone, address, password, profile_pic) 
              VALUES ('$full_name', '$email', '$phone', '$address', '$password', '$profile_pic')";
        if(mysqli_query($conn, $q)) {
            header("Location: login.php?msg=registered");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Account | Join Our Marketplace</title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f4f7fa; min-height: 100vh; display: flex; align-items: center; padding: 40px 0; }
        .auth-card { background: white; border-radius: 35px; padding: 50px; box-shadow: 0 30px 60px rgba(0,0,0,0.08); border: 1px solid rgba(0,0,0,0.02); width: 100%; max-width: 550px; margin: auto; }
        .form-control { border-radius: 14px; padding: 14px; border: 1px solid #eef2f6; background: #fcfdfe; font-size: 0.95rem; }
        .form-control:focus { box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1); border-color: #0ea5e9; }
        .btn-auth { background: #0ea5e9; color: white; border-radius: 14px; padding: 16px; font-weight: 800; border: none; transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .btn-auth:hover { background: #0f172a; transform: translateY(-3px); box-shadow: 0 10px 20px rgba(14, 165, 233, 0.2); }
    </style>
</head>
<body>
    <div class="container">
        <div class="auth-card">
            <div class="text-center mb-5">
                <h2 class="fw-800 text-dark">Join Us</h2>
                <p class="text-muted">Set up your professional profile in minutes.</p>
            </div>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger border-0 rounded-4 py-3 mb-4 small"><i class="fa-solid fa-triangle-exclamation me-2"></i> <?= $error ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="small fw-bold text-muted mb-2">Full Name</label>
                        <input type="text" name="full_name" class="form-control" placeholder="John Doe" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="small fw-bold text-muted mb-2">Phone</label>
                        <input type="text" name="phone" class="form-control" placeholder="03XXXXXXXXX" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold text-muted mb-2">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold text-muted mb-2">Address</label>
                    <input type="text" name="address" class="form-control" placeholder="City, Street, Country" required>
                </div>
                <div class="mb-3">
                    <label class="small fw-bold text-muted mb-2">Profile Picture</label>
                    <input type="file" name="profile_pic" class="form-control" accept="image/*">
                </div>
                <div class="mb-4">
                    <label class="small fw-bold text-muted mb-2">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" name="register" class="btn-auth w-100 mb-4">Create Account</button>
                <p class="text-center small mb-0 text-muted">Already registered? <a href="login.php" class="text-primary fw-bold text-decoration-none">Login Here</a></p>
            </form>
        </div>
    </div>
</body>
</html>