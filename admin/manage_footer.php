<?php 
include '../db.php'; 
session_start();

// // Security Check
// if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$msg = "";

if(isset($_POST['update_footer'])) {
    // Sanitizing inputs
    $about = mysqli_real_escape_string($conn, $_POST['about_text']);
    $copy = mysqli_real_escape_string($conn, $_POST['copyright_text']);
    $fb = mysqli_real_escape_string($conn, $_POST['facebook_url']);
    $insta = mysqli_real_escape_string($conn, $_POST['instagram_url']);
    $twitter = mysqli_real_escape_string($conn, $_POST['twitter_url']);
    $whatsapp = mysqli_real_escape_string($conn, $_POST['whatsapp_url']);

    $sql = "UPDATE footer_settings SET 
            about_text='$about', 
            copyright_text='$copy', 
            facebook_url='$fb', 
            instagram_url='$insta',
            twitter_url='$twitter',
            linkedin_url='$whatsapp' 
            WHERE id=1";

    if(mysqli_query($conn, $sql)) {
        $msg = "Footer configuration synced successfully!";
    }
}

$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM footer_settings WHERE id=1"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Footer | Admin Panel</title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root { --p-blue: #0ea5e9; --p-dark: #0f172a; --p-bg: #f8fafc; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--p-bg); color: var(--p-dark); }
        
        .config-card { 
            background: white; border-radius: 35px; padding: 40px; 
            border: 1px solid #f1f5f9; box-shadow: 0 20px 40px rgba(0,0,0,0.03); 
        }
        
        .form-label { font-weight: 700; font-size: 0.85rem; color: #64748b; display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
        
        .form-control { 
            border-radius: 15px; padding: 12px 18px; border: 1.5px solid #f1f5f9; 
            background: #fcfdfe; font-weight: 600; margin-bottom: 20px; 
        }
        
        .form-control:focus { border-color: var(--p-blue); box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.05); }
        
        .section-head { 
            font-weight: 800; font-size: 1.1rem; color: var(--p-blue); 
            margin: 30px 0 20px 0; display: flex; align-items: center; gap: 10px; 
        }
        .section-head::after { content: ""; height: 2px; flex-grow: 1; background: #f1f5f9; }
        
        .btn-update { 
            background: var(--p-dark); color: white; border-radius: 18px; 
            padding: 15px; font-weight: 800; border: none; transition: 0.3s; width: 100%; 
        }
        
        .btn-update:hover { 
            background: var(--p-blue); transform: translateY(-3px); 
            box-shadow: 0 10px 20px rgba(14,165,233,0.2); 
        }

        .alert { border-radius: 20px; border: none; font-weight: 600; }
    </style>
</head>
<body>

<div class="container py-5" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-800 m-0">Footer <span class="text-primary">Branding</span></h2>
            <p class="text-muted small fw-600 mb-0">Customize your site's bottom section and social links</p>
        </div>
        <a href="admin_panel.php" class="btn btn-outline-secondary rounded-pill px-4 fw-bold small">Back</a>
    </div>

    <?php if($msg): ?>
        <div class="alert alert-success shadow-sm py-3 mb-4">
            <i class="fa-solid fa-circle-check me-2"></i> <?= $msg ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="config-card">
        
        <div class="section-head"><i class="fa fa-fingerprint"></i> Brand Identity</div>
        <div class="row">
            <div class="col-12">
                <label class="form-label">Footer "About Us" Bio</label>
                <textarea name="about_text" class="form-control" rows="3" placeholder="Briefly describe PashuMandi..."><?= $data['about_text'] ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Copyright Text</label>
                <input type="text" name="copyright_text" class="form-control" value="<?= $data['copyright_text'] ?>">
            </div>
        </div>

        <div class="section-head"><i class="fa fa-share-nodes"></i> Social Network Links</div>
        <div class="row">
            <div class="col-md-6">
                <label class="form-label"><i class="fab fa-facebook text-primary"></i> Facebook URL</label>
                <input type="text" name="facebook_url" class="form-control" value="<?= $data['facebook_url'] ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label"><i class="fab fa-instagram text-danger"></i> Instagram URL</label>
                <input type="text" name="instagram_url" class="form-control" value="<?= $data['instagram_url'] ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label"><i class="fab fa-x-twitter text-dark"></i> Twitter/X URL</label>
                <input type="text" name="twitter_url" class="form-control" value="<?= $data['twitter_url'] ?? '#' ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label"><i class="fab fa-whatsapp text-success"></i> WhatsApp Group/Link</label>
                <input type="text" name="whatsapp_url" class="form-control" value="<?= $data['linkedin_url'] ?? '#' ?>" placeholder="https://wa.me/...">
            </div>
        </div>

        <div class="pt-4">
            <button type="submit" name="update_footer" class="btn-update shadow-lg">
                <i class="fa fa-save me-2"></i> UPDATE FOOTER SETTINGS
            </button>
        </div>
    </form>
</div>

</body>
</html>