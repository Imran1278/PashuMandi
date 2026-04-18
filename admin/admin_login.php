<?php
session_start();
include '../db.php'; 

$msg = "";

if (isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_panel.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $checkAdmin = mysqli_query($conn, "SELECT * FROM admins LIMIT 1");
    
    if (mysqli_num_rows($checkAdmin) == 0) {
        $hashed_pass = password_hash($password, PASSWORD_BCRYPT);
        $insert = "INSERT INTO admins (full_name, email, password) VALUES ('$fullname', '$email', '$hashed_pass')";
        
        if (mysqli_query($conn, $insert)) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = mysqli_insert_id($conn);
            $_SESSION['admin_name'] = $fullname;
            header("Location: admin_panel.php");
            exit();
        }
    } else {
        $query = "SELECT * FROM admins WHERE email='$email' LIMIT 1";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);
        
        if ($row) {
            if (password_verify($password, $row['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $row['id'];
                $_SESSION['admin_name'] = $row['full_name'];
                header("Location: admin_panel.php");
                exit();
            } else {
                $msg = "<div class='alert-msg'><i class='fas fa-exclamation-circle me-2'></i>Security Key Mismatch! Access Denied.</div>";
            }
        } else {
            $msg = "<div class='alert-msg'><i class='fas fa-user-slash me-2'></i>Unauthorized! Admin record not found.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Access</title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { 
            --p-navy: #0f172a; 
            --p-teal: #14b8a6; 
            --p-slate: #64748b;
            --p-bg: #f8fafc;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--p-bg); 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            min-height: 100vh; 
            margin: 0; 
        }

        .auth-card { 
            width: 100%; 
            max-width: 440px; 
            background: white; 
            border-radius: 32px; 
            padding: 50px 40px; 
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08); 
            border: 1px solid #f1f5f9; 
        }

        .header { text-align: center; margin-bottom: 35px; }

        .icon-box { 
            width: 70px; 
            height: 70px; 
            background: var(--p-navy); 
            color: white; 
            border-radius: 22px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 28px; 
            margin: 0 auto 15px;
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.15);
        }

        h2 { 
            font-size: 28px; 
            font-weight: 800; 
            color: var(--p-navy); 
            letter-spacing: -1px; 
            margin-bottom: 8px;
        }

        .header p { color: var(--p-slate); font-size: 14px; font-weight: 500; }

        .alert-msg { 
            background: #fff1f2; 
            color: #e11d48; 
            padding: 14px; 
            border-radius: 16px; 
            font-size: 13.5px; 
            text-align: center; 
            margin-bottom: 25px; 
            font-weight: 700; 
            border: 1px solid #ffe4e6; 
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .form-group { margin-bottom: 22px; }

        label { 
            display: block; 
            font-size: 12px; 
            font-weight: 800; 
            color: var(--p-slate); 
            text-transform: uppercase; 
            margin-bottom: 10px; 
            margin-left: 5px; 
            letter-spacing: 0.5px;
        }

        .input-wrapper { position: relative; }

        .input-wrapper i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
            transition: 0.3s;
        }

        input { 
            width: 100%; 
            padding: 16px 16px 16px 52px; 
            border-radius: 18px; 
            border: 2px solid #f1f5f9; 
            background: #f8fafc; 
            font-size: 15px; 
            font-weight: 600; 
            color: var(--p-navy);
            outline: none; 
            transition: 0.3s all ease; 
        }

        input:focus { 
            border-color: var(--p-teal); 
            background: white; 
            box-shadow: 0 0 0 5px rgba(20, 184, 166, 0.08); 
        }

        input:focus + i { color: var(--p-teal); }

        .btn-submit { 
            width: 100%; 
            padding: 18px; 
            background: var(--p-navy); 
            color: white; 
            border: none; 
            border-radius: 20px; 
            font-weight: 800; 
            font-size: 16px;
            cursor: pointer; 
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            margin-top: 10px; 
            letter-spacing: 0.5px;
        }

        .btn-submit:hover { 
            background: var(--p-teal); 
            transform: translateY(-3px); 
            box-shadow: 0 15px 30px rgba(20, 184, 166, 0.2); 
        }

        .btn-submit:active { transform: translateY(-1px); }

        .back-link {
            text-align: center;
            margin-top: 25px;
        }

        .back-link a {
            text-decoration: none;
            color: #94a3b8;
            font-size: 14px;
            font-weight: 700;
            transition: 0.3s;
        }

        .back-link a:hover { color: var(--p-navy); }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="header">
        <div class="icon-box"><i class="fas fa-shield-halved"></i></div>
        <h2>Admin Access</h2>
        <p>PashuMandi Administration</p>
    </div>

    <?php if($msg) echo $msg; ?>

    <form method="POST">
        <div class="form-group">
            <label>Full Name</label>
            <div class="input-wrapper">
                <i class="fas fa-user-tie"></i>
                <input type="text" name="full_name" placeholder="Super Admin Name" required>
            </div>
        </div>
        
        <div class="form-group">
            <label>Admin Email</label>
            <div class="input-wrapper">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="admin@pulse.com" required>
            </div>
        </div>
        
        <div class="form-group">
            <label>Password</label>
            <div class="input-wrapper">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
        </div>
        
        <button type="submit" class="btn-submit">Verify & Log In</button>
    </form>

    <div class="back-link">
        <a href="../index.php"><i class="fas fa-arrow-left me-2"></i> Return to Main Site</a>
    </div>
</div>

</body>
</html>