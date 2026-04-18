<?php 
include '../db.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$u_id = $_SESSION['user_id'];
$msg = "";

// Save Settings Logic
if(isset($_POST['save_settings'])) {
    $notif = isset($_POST['email_notif']) ? 1 : 0;
    $privacy = isset($_POST['public_profile']) ? 1 : 0;
    $marketing = isset($_POST['marketing_mails']) ? 1 : 0; // Added extra preference
    
    $update_sql = "UPDATE users SET email_notifications='$notif', public_profile='$privacy' WHERE id='$u_id'";
    if(mysqli_query($conn, $update_sql)) {
        $msg = "Preferences updated successfully!";
    }
}

// Fetch current user settings
$u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT email_notifications, public_profile FROM users WHERE id='$u_id'"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Preferences | PashuMandi</title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root { --p-indigo: #6366f1; --p-dark: #0f172a; --p-bg: #f8fafc; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--p-bg); color: var(--p-dark); }
        
        .settings-container { max-width: 700px; margin: 60px auto; }
        
        /* Back Button */
        .btn-back { background: white; border: none; width: 45px; height: 45px; border-radius: 15px; display: flex; align-items: center; justify-content: center; color: var(--p-dark); transition: 0.3s; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .btn-back:hover { transform: translateX(-5px); background: var(--p-indigo); color: white; }

        /* Card Styling */
        .settings-card { background: white; border-radius: 35px; padding: 45px; border: none; box-shadow: 0 20px 50px rgba(0,0,0,0.02); position: relative; overflow: hidden; }
        
        .section-label { font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 25px; display: block; border-left: 3px solid var(--p-indigo); padding-left: 12px; }

        /* Modern Switches */
        .form-check-input { width: 50px !important; height: 26px !important; cursor: pointer; border: 2px solid #e2e8f0; }
        .form-check-input:checked { background-color: var(--p-indigo); border-color: var(--p-indigo); }
        .form-check-label { cursor: pointer; }

        .setting-item { padding: 15px 0; border-bottom: 1px solid #f1f5f9; transition: 0.3s; }
        .setting-item:last-of-type { border-bottom: none; }
        
        /* Premium Button */
        .btn-save { background: var(--p-dark); color: white; border-radius: 20px; padding: 18px 40px; font-weight: 800; border: none; transition: 0.4s; width: 100%; letter-spacing: 0.5px; }
        .btn-save:hover { background: var(--p-indigo); transform: translateY(-3px); box-shadow: 0 15px 30px rgba(99, 102, 241, 0.3); }

        .security-note { background: #fff1f2; border: 1px solid #ffe4e6; border-radius: 20px; padding: 20px; margin-top: 30px; }
    </style>
</head>
<body>

<div class="container settings-container">
    <div class="d-flex align-items-center gap-3 mb-5 px-3">
        <a href="profile.php" class="btn-back"><i class="fa fa-arrow-left"></i></a>
        <div>
            <h2 class="fw-800 mb-0">Preferences</h2>
            <p class="text-muted small mb-0">Manage how you interact with Pulse Market</p>
        </div>
    </div>

    <?php if($msg): ?>
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 px-4 py-3 fw-bold">
            <i class="fa-solid fa-circle-check me-2"></i> <?= $msg ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="settings-card">
        <span class="section-label">General Notifications</span>
        
        <div class="setting-item d-flex justify-content-between align-items-center mb-3">
            <div style="max-width: 80%;">
                <h6 class="fw-800 mb-1">Email Notifications</h6>
                <p class="text-muted small mb-0">Get instant updates about new bids on your animals and price alerts.</p>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input shadow-none" type="checkbox" name="email_notif" <?= $u['email_notifications'] ? 'checked' : '' ?>>
            </div>
        </div>

        <div class="setting-item d-flex justify-content-between align-items-center mb-4">
            <div style="max-width: 80%;">
                <h6 class="fw-800 mb-1">Marketing Communication</h6>
                <p class="text-muted small mb-0">Receive monthly newsletters and exclusive livestock market trends.</p>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input shadow-none" type="checkbox" name="marketing_mails" checked>
            </div>
        </div>

        <span class="section-label mt-4">Privacy & Visibility</span>
        
        <div class="setting-item d-flex justify-content-between align-items-center mb-3">
            <div style="max-width: 80%;">
                <h6 class="fw-800 mb-1">Public Profile Visibility</h6>
                <p class="text-muted small mb-0">Allow buyers to see your collection and history when they search.</p>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input shadow-none" type="checkbox" name="public_profile" <?= $u['public_profile'] ? 'checked' : '' ?>>
            </div>
        </div>

        <div class="security-note">
            <div class="d-flex gap-3">
                <div class="text-danger fs-4"><i class="fa-solid fa-shield-halved"></i></div>
                <div>
                    <h6 class="fw-800 text-danger mb-1">Security Zone</h6>
                    <p class="text-muted small mb-0">Need to update your password or login methods? 
                        <a href="profile.php" class="text-danger fw-bold text-decoration-none">Go to Security Panel <i class="fa fa-arrow-right small"></i></a>
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <button type="submit" name="save_settings" class="btn-save">Save All Changes</button>
        </div>
    </form>

    <div class="text-center mt-5">
        <p class="text-muted small fw-bold opacity-50">Pulse Market Control Panel • Build v1.0.4</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>