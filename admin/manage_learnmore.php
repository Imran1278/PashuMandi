<?php 
include '../db.php'; 
session_start();

// // Security Check (Ensure only admin can access)
// if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$msg = "";

if(isset($_POST['update_details'])) {
    // Sanitizing all inputs
    $title = mysqli_real_escape_string($conn, $_POST['section_title']);
    $heading = mysqli_real_escape_string($conn, $_POST['main_heading']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $mission = mysqli_real_escape_string($conn, $_POST['mission']);
    $vision = mysqli_real_escape_string($conn, $_POST['vision']);
    $users = mysqli_real_escape_string($conn, $_POST['total_users']);
    $animals = mysqli_real_escape_string($conn, $_POST['total_animals']);
    
    // New Fields
    $f1_t = mysqli_real_escape_string($conn, $_POST['feature_1_title']);
    $f1_d = mysqli_real_escape_string($conn, $_POST['feature_1_desc']);
    $f2_t = mysqli_real_escape_string($conn, $_POST['feature_2_title']);
    $f2_d = mysqli_real_escape_string($conn, $_POST['feature_2_desc']);
    $s1_d = mysqli_real_escape_string($conn, $_POST['step_1_desc']);
    $s2_d = mysqli_real_escape_string($conn, $_POST['step_2_desc']);
    $s3_d = mysqli_real_escape_string($conn, $_POST['step_3_desc']);

    $sql = "UPDATE site_details SET 
            section_title='$title', main_heading='$heading', content='$content', 
            mission='$mission', vision='$vision', total_users='$users', total_animals='$animals',
            feature_1_title='$f1_t', feature_1_desc='$f1_d', 
            feature_2_title='$f2_t', feature_2_desc='$f2_d',
            step_1_desc='$s1_d', step_2_desc='$s2_d', step_3_desc='$s3_d' 
            WHERE id=1";

    if(mysqli_query($conn, $sql)) {
        $msg = "All sections updated successfully!";
    }
}

$data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM site_details WHERE id=1"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Details | Admin Panel</title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root { --p-blue: #0ea5e9; --p-dark: #0f172a; --p-bg: #f8fafc; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--p-bg); color: var(--p-dark); }
        .config-card { background: white; border-radius: 35px; padding: 40px; border: 1px solid #f1f5f9; box-shadow: 0 20px 40px rgba(0,0,0,0.03); }
        .form-label { font-weight: 700; font-size: 0.85rem; color: #64748b; display: flex; align-items: center; gap: 8px; margin-bottom: 8px; }
        .form-control { border-radius: 15px; padding: 12px 18px; border: 1.5px solid #f1f5f9; background: #fcfdfe; font-weight: 600; margin-bottom: 20px; }
        .form-control:focus { border-color: var(--p-blue); box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.05); }
        .section-head { font-weight: 800; font-size: 1.1rem; color: var(--p-blue); margin: 30px 0 20px 0; display: flex; align-items: center; gap: 10px; }
        .section-head::after { content: ""; height: 2px; flex-grow: 1; background: #f1f5f9; }
        .btn-update { background: var(--p-dark); color: white; border-radius: 18px; padding: 15px 40px; font-weight: 800; border: none; transition: 0.3s; width: 100%; }
        .btn-update:hover { background: var(--p-blue); transform: translateY(-3px); box-shadow: 0 10px 20px rgba(14,165,233,0.2); }
    </style>
</head>
<body>

<div class="container py-5" style="max-width: 1000px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-800 m-0">Site <span class="text-primary">Customizer</span></h2>
        <a href="admin_panel.php" class="btn btn-outline-secondary rounded-pill px-4 fw-bold small">Back</a>
    </div>

    <?php if($msg): ?>
        <div class="alert alert-success border-0 rounded-4 shadow-sm py-3 mb-4"><i class="fa-solid fa-check-double me-2"></i> <?= $msg ?></div>
    <?php endif; ?>

    <form method="POST" class="config-card">
        
        <div class="section-head"><i class="fa fa-rocket"></i> Hero & Main Info</div>
        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Small Tagline</label>
                <input type="text" name="section_title" class="form-control" value="<?= $data['section_title'] ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Main Big Heading</label>
                <input type="text" name="main_heading" class="form-control" value="<?= $data['main_heading'] ?>">
            </div>
            <div class="col-12">
                <label class="form-label">Intro Description</label>
                <textarea name="content" class="form-control" rows="3"><?= $data['content'] ?></textarea>
            </div>
        </div>

        <div class="section-head"><i class="fa fa-eye"></i> Mission & Vision</div>
        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Our Mission Text</label>
                <textarea name="mission" class="form-control" rows="3"><?= $data['mission'] ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Our Vision Text</label>
                <textarea name="vision" class="form-control" rows="3"><?= $data['vision'] ?></textarea>
            </div>
        </div>

        <div class="section-head"><i class="fa fa-list-ol"></i> How It Works (Steps)</div>
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">Step 1: Account</label>
                <textarea name="step_1_desc" class="form-control"><?= $data['step_1_desc'] ?></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label">Step 2: Listing</label>
                <textarea name="step_2_desc" class="form-control"><?= $data['step_2_desc'] ?></textarea>
            </div>
            <div class="col-md-4">
                <label class="form-label">Step 3: Trading</label>
                <textarea name="step_3_desc" class="form-control"><?= $data['step_3_desc'] ?></textarea>
            </div>
        </div>

        <div class="section-head"><i class="fa fa-shield-halved"></i> Trust Features</div>
        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Feature 1 Title</label>
                <input type="text" name="feature_1_title" class="form-control" value="<?= $data['feature_1_title'] ?>">
                <label class="form-label">Feature 1 Description</label>
                <textarea name="feature_1_desc" class="form-control"><?= $data['feature_1_desc'] ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Feature 2 Title</label>
                <input type="text" name="feature_2_title" class="form-control" value="<?= $data['feature_2_title'] ?>">
                <label class="form-label">Feature 2 Description</label>
                <textarea name="feature_2_desc" class="form-control"><?= $data['feature_2_desc'] ?></textarea>
            </div>
        </div>

        <div class="section-head"><i class="fa fa-chart-line"></i> Live Counters</div>
        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Total Users Count</label>
                <input type="text" name="total_users" class="form-control" value="<?= $data['total_users'] ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Total Animals Count</label>
                <input type="text" name="total_animals" class="form-control" value="<?= $data['total_animals'] ?>">
            </div>
        </div>

        <div class="pt-4">
            <button type="submit" name="update_details" class="btn-update shadow-lg">PUSH UPDATES TO WEBSITE</button>
        </div>
    </form>
</div>

</body>
</html>