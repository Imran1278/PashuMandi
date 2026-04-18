<?php 
include '../db.php'; 
session_start();

// // Security Check
// if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$msg = "";

if(isset($_POST['update_contact'])) {
    // Sanitizing inputs
    $email = mysqli_real_escape_string($conn, $_POST['support_email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $whatsapp = mysqli_real_escape_string($conn, $_POST['whatsapp_number']);
    $tagline = mysqli_real_escape_string($conn, $_POST['contact_tagline']);
    $address = mysqli_real_escape_string($conn, $_POST['office_address']);
    $map = mysqli_real_escape_string($conn, $_POST['map_iframe']);
    $fb = mysqli_real_escape_string($conn, $_POST['fb_link']);
    $insta = mysqli_real_escape_string($conn, $_POST['insta_link']);

    $sql = "UPDATE contact_settings SET 
            support_email='$email', 
            phone_number='$phone', 
            whatsapp_number='$whatsapp', 
            contact_tagline='$tagline',
            office_address='$address', 
            map_iframe='$map',
            fb_link='$fb',
            insta_link='$insta' 
            WHERE id=1";

    if(mysqli_query($conn, $sql)) {
        $msg = "Contact configuration updated successfully!";
    }
}

$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM contact_settings WHERE id=1"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Contact | Admin Panel</title>
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
            <h2 class="fw-800 m-0">Contact <span class="text-primary">Management</span></h2>
            <p class="text-muted small fw-600 mb-0">Control how users reach out to PashuMandi</p>
        </div>
        <a href="admin_panel.php" class="btn btn-outline-secondary rounded-pill px-4 fw-bold small">Back to Panel</a>
    </div>

    <?php if($msg): ?>
        <div class="alert alert-success shadow-sm py-3 mb-4">
            <i class="fa-solid fa-check-circle me-2"></i> <?= $msg ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="config-card">
        
        <div class="section-head"><i class="fa fa-headset"></i> Support Channels</div>
        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Support Email Address</label>
                <input type="email" name="support_email" class="form-control" value="<?= $data['support_email'] ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Primary Phone Number</label>
                <input type="text" name="phone_number" class="form-control" value="<?= $data['phone_number'] ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">WhatsApp Number (e.g. 923001234567)</label>
                <input type="text" name="whatsapp_number" class="form-control" value="<?= $data['whatsapp_number'] ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Page Sub-Heading/Tagline</label>
                <input type="text" name="contact_tagline" class="form-control" value="<?= $data['contact_tagline'] ?>">
            </div>
        </div>

        <div class="section-head"><i class="fa fa-share-nodes"></i> Social Links</div>
        <div class="row">
            <div class="col-md-6">
                <label class="form-label"><i class="fab fa-facebook text-primary"></i> Facebook Profile/Page URL</label>
                <input type="text" name="fb_link" class="form-control" value="<?= $data['fb_link'] ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label"><i class="fab fa-instagram text-danger"></i> Instagram Profile URL</label>
                <input type="text" name="insta_link" class="form-control" value="<?= $data['insta_link'] ?>">
            </div>
        </div>

        <div class="section-head"><i class="fa fa-map-location-dot"></i> Physical Location</div>
        <div class="row">
            <div class="col-12">
                <label class="form-label">Office/Mandi Address</label>
                <textarea name="office_address" class="form-control" rows="2"><?= $data['office_address'] ?></textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Google Map Embed (Iframe Code)</label>
                <textarea name="map_iframe" class="form-control" rows="4" placeholder="Paste <iframe> from Google Maps..."><?= $data['map_iframe'] ?></textarea>
                <p class="small text-muted mt-n2"><i class="fa fa-info-circle me-1"></i> Tip: Go to Google Maps -> Share -> Embed Map -> Copy HTML.</p>
            </div>
        </div>

        <div class="pt-4">
            <button type="submit" name="update_contact" class="btn-update shadow-lg">
                <i class="fa fa-save me-2"></i> SYNC CONTACT DATA
            </button>
        </div>
    </form>
</div>

</body>
</html>