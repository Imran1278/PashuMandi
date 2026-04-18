<?php 
include '../db.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. DETERMINING VIEW CONTEXT
if(isset($_GET['id'])) {
    $view_id = mysqli_real_escape_string($conn, $_GET['id']);
    $is_owner = (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $view_id);
} else {
    if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
    $view_id = $_SESSION['user_id'];
    $is_owner = true;
}

$msg = "";
$error = "";

// 2. PROCESSING UPDATES (Owner Only)
if($is_owner) {
    if(isset($_POST['update_profile'])) {
        $name = mysqli_real_escape_string($conn, $_POST['full_name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        if(mysqli_query($conn, "UPDATE users SET full_name='$name', email='$email', phone='$phone', address='$address' WHERE id='$view_id'")) {
            $msg = "Profile updated successfully!";
        }
    }

    if(isset($_POST['update_password'])) {
        $np = $_POST['new_pass'];
        $cp = $_POST['conf_pass'];
        if($np === $cp && !empty($np)) {
            $hashed = password_hash($np, PASSWORD_DEFAULT);
            mysqli_query($conn, "UPDATE users SET password='$hashed' WHERE id='$view_id'");
            $msg = "Security settings updated!";
        } else { $error = "Passwords do not match!"; }
    }

    if(isset($_POST['update_pic']) && !empty($_FILES['new_pic']['name'])) {
        $fn = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $_FILES["new_pic"]["name"]);
        if(move_uploaded_file($_FILES["new_pic"]["tmp_name"], "../uploads/" . $fn)) {
            mysqli_query($conn, "UPDATE users SET profile_pic='$fn' WHERE id='$view_id'");
            $msg = "Avatar refreshed!";
        }
    }
}

// 3. FETCH DATA
$u_res = mysqli_query($conn, "SELECT * FROM users WHERE id='$view_id'");
if(mysqli_num_rows($u_res) == 0) { echo "User not found!"; exit(); }
$u = mysqli_fetch_assoc($u_res);
$user_ads_q = mysqli_query($conn, "SELECT * FROM animals WHERE user_id = '$view_id' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($u['full_name']) ?> | Dashboard</title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root { --p-teal: #14b8a6; --p-navy: #0f172a; --p-slate: #f8fafc; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f1f5f9; color: var(--p-navy); }
        
        .dashboard-wrapper { display: flex; min-height: 100vh; padding: 25px; gap: 25px; }
        
        /* Sidebar Styling */
        .glass-sidebar { width: 300px; background: var(--p-navy); border-radius: 40px; padding: 45px 25px; display: flex; flex-direction: column; box-shadow: 0 20px 50px rgba(15,23,42,0.2); }
        .sidebar-brand { font-size: 1.6rem; font-weight: 800; color: white; margin-bottom: 50px; letter-spacing: -1px; text-align: center; }
        .sidebar-brand span { color: var(--p-teal); }
        
        .nav-pill-custom { border: none; background: transparent; color: #94a3b8; padding: 16px 20px; border-radius: 20px; font-weight: 700; display: flex; align-items: center; gap: 15px; margin-bottom: 8px; transition: 0.3s; width: 100%; text-align: left; }
        .nav-pill-custom:hover { background: rgba(255,255,255,0.05); color: white; }
        .nav-pill-custom.active { background: var(--p-teal); color: white; box-shadow: 0 10px 20px rgba(20, 184, 166, 0.3); }

        /* Main Content Styling */
        .content-card { flex: 1; background: white; border-radius: 40px; padding: 50px; box-shadow: 0 10px 40px rgba(0,0,0,0.03); border: 1px solid rgba(255,255,255,0.8); }
        
        .profile-header { background: var(--p-slate); border-radius: 30px; padding: 30px; margin-bottom: 40px; display: flex; align-items: center; gap: 25px; border: 1px solid #e2e8f0; }
        .p-avatar-box { position: relative; width: 100px; height: 100px; }
        .p-avatar-box img { width: 100%; height: 100%; border-radius: 28px; object-fit: cover; border: 4px solid white; box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .edit-overlay { position: absolute; bottom: -5px; right: -5px; background: var(--p-teal); color: white; border: 3px solid white; width: 35px; height: 35px; border-radius: 12px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: 0.3s; }
        .edit-overlay:hover { transform: scale(1.1); }

        .form-control { border-radius: 18px; padding: 14px 20px; background: #f8fafc; border: 2px solid #f1f5f9; font-weight: 600; transition: 0.3s; }
        .form-control:focus { border-color: var(--p-teal); background: white; box-shadow: 0 0 0 5px rgba(20, 184, 166, 0.1); }
        
        .btn-prime { background: var(--p-navy); color: white; border-radius: 20px; padding: 16px; font-weight: 800; border: none; width: 100%; transition: 0.3s; }
        .btn-prime:hover { background: var(--p-teal); transform: translateY(-2px); box-shadow: 0 10px 20px rgba(20, 184, 166, 0.2); }

        .ad-row { background: #f8fafc; border-radius: 24px; padding: 18px; margin-bottom: 15px; border: 1px solid transparent; transition: 0.3s; display: flex; align-items: center; justify-content: space-between; }
        .ad-row:hover { border-color: var(--p-teal); background: white; transform: scale(1.01); box-shadow: 0 10px 30px rgba(0,0,0,0.04); }
        .ad-img { width: 65px; height: 65px; border-radius: 16px; object-fit: cover; }

        @media (max-width: 991px) {
            .dashboard-wrapper { flex-direction: column; padding: 15px; }
            .glass-sidebar { width: 100%; padding: 30px 20px; }
            .content-card { padding: 30px 20px; }
        }
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    <aside class="glass-sidebar">
        <div class="sidebar-brand">PASHU<span>MANDI</span></div>
        <div class="nav flex-column mb-auto">
            <button class="nav-pill-custom active" data-bs-toggle="pill" data-bs-target="#info">
                <i class="fa-solid fa-user-gear"></i> <?= $is_owner ? 'My Settings' : 'Seller Profile' ?>
            </button>
            <button class="nav-pill-custom" data-bs-toggle="pill" data-bs-target="#ads">
                <i class="fa-solid fa-paw"></i> Active Listings
            </button>
            <?php if($is_owner): ?>
            <button class="nav-pill-custom" data-bs-toggle="pill" data-bs-target="#security">
                <i class="fa-solid fa-shield-halved"></i> Security
            </button>
            <?php endif; ?>
        </div>
        
        <div class="mt-5 pt-4 border-top border-secondary border-opacity-10">
            <a href="../index.php" class="nav-pill-custom text-decoration-none mb-1"><i class="fa-solid fa-house"></i> Home</a>
            <?php if($is_owner): ?>
                <a href="logout.php" class="nav-pill-custom text-danger text-decoration-none"><i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out</a>
            <?php endif; ?>
        </div>
    </aside>

    <main class="content-card">
        <?php if($msg): ?> <div class="alert alert-success border-0 rounded-4 shadow-sm py-3 px-4 mb-4 fw-bold"><i class="fa-solid fa-circle-check me-2"></i><?= $msg ?></div> <?php endif; ?>
        <?php if($error): ?> <div class="alert alert-danger border-0 rounded-4 shadow-sm py-3 px-4 mb-4 fw-bold"><?= $error ?></div> <?php endif; ?>

        <div class="profile-header">
            <div class="p-avatar-box">
                <img src="../uploads/<?= !empty($u['profile_pic']) ? $u['profile_pic'] : 'default.png' ?>" alt="User">
                <?php if($is_owner): ?>
                <div class="edit-overlay" data-bs-toggle="modal" data-bs-target="#picModal"><i class="fa fa-camera-retro"></i></div>
                <?php endif; ?>
            </div>
            <div>
                <h2 class="fw-800 mb-1"><?= htmlspecialchars($u['full_name']) ?></h2>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge bg-teal-subtle text-success rounded-pill px-3 py-2 border border-success border-opacity-25 fw-800" style="font-size: 0.7rem;">TRUSTED SELLER</span>
                    <span class="text-muted small fw-bold"><i class="fa fa-calendar-check me-1"></i> Since <?= date('M Y', strtotime($u['created_at'])) ?></span>
                </div>
            </div>
        </div>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="info">
                <h5 class="fw-800 mb-4 px-2">Account Information</h5>
                <form method="POST">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-800 text-muted ms-1">Full Name</label>
                            <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($u['full_name']) ?>" <?= $is_owner ? '' : 'disabled' ?>>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-800 text-muted ms-1">Contact Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($u['email']) ?>" <?= $is_owner ? '' : 'disabled' ?>>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-800 text-muted ms-1">WhatsApp / Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($u['phone']) ?>" <?= $is_owner ? '' : 'disabled' ?>>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-800 text-muted ms-1">City / Location</label>
                            <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($u['address']) ?>" <?= $is_owner ? '' : 'disabled' ?>>
                        </div>
                        <?php if($is_owner): ?>
                        <div class="col-12 mt-4">
                            <button type="submit" name="update_profile" class="btn-prime">Save All Changes</button>
                        </div>
                        <?php else: ?>
                        <div class="col-12 mt-4">
                            <a href="https://wa.me/<?= $u['phone'] ?>" class="btn-prime text-decoration-none d-block text-center"><i class="fab fa-whatsapp me-2"></i> Contact Seller</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="tab-pane fade" id="ads">
                <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                    <h5 class="fw-800 mb-0">Listings Gallery</h5>
                    <span class="badge bg-dark rounded-pill px-3"><?= mysqli_num_rows($user_ads_q) ?> Items</span>
                </div>
                <?php if(mysqli_num_rows($user_ads_q) > 0): ?>
                    <?php while($ad = mysqli_fetch_assoc($user_ads_q)): ?>
                        <div class="ad-row">
                            <div class="d-flex align-items-center gap-3">
                                <img src="../uploads/<?= !empty($ad['image']) ? explode(',', $ad['image'])[0] : 'default.jpg' ?>" class="ad-img">
                                <div>
                                    <h6 class="mb-0 fw-800"><?= htmlspecialchars($ad['title']) ?></h6>
                                    <span class="fw-bold text-teal">Rs. <?= number_format($ad['price']) ?></span>
                                </div>
                            </div>
                            <a href="product_details.php?id=<?= $ad['id'] ?>" class="btn btn-light rounded-pill px-4 fw-bold border-0 shadow-sm">View</a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-center text-muted py-5">No active ads found.</p>
                <?php endif; ?>
            </div>

            <?php if($is_owner): ?>
            <div class="tab-pane fade" id="security">
                <h5 class="fw-800 mb-4 px-2">Access & Security</h5>
                <form method="POST" class="bg-light p-4 rounded-4 border">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-800">New Password</label>
                            <input type="password" name="new_pass" class="form-control" placeholder="••••••••" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-800">Confirm New Password</label>
                            <input type="password" name="conf_pass" class="form-control" placeholder="••••••••" required>
                        </div>
                        <div class="col-12"><button type="submit" name="update_password" class="btn-prime">Change Password</button></div>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<div class="modal fade" id="picModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 30px;">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-5 text-center">
                <h4 class="fw-800 mb-4">Update Photo</h4>
                <form method="POST" enctype="multipart/form-data">
                    <input type="file" name="new_pic" class="form-control mb-4" required>
                    <button type="submit" name="update_pic" class="btn-prime">Upload Image</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>