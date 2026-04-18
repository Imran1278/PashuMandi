<?php
include '../db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// // Security: Sirf logged in user hi access kar sake (Admin check add kar sakte hain)
// if(!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit(); }

// Fetch current data
$get_about = mysqli_query($conn, "SELECT * FROM about_us LIMIT 1");
$row = mysqli_fetch_assoc($get_about);

if (isset($_POST['save_about'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $subtitle = mysqli_real_escape_string($conn, $_POST['subtitle']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $exp = mysqli_real_escape_string($conn, $_POST['exp_years']);
    $clients = mysqli_real_escape_string($conn, $_POST['happy_clients']);

    $img_name = $row['image'] ?? ''; // Default to existing

    // Image Upload Logic with cleanup
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../uploads/";
        $new_img_name = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES["image"]["name"]));
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $new_img_name)) {
            // Delete old image if it exists
            if (!empty($row['image']) && file_exists($target_dir . $row['image'])) {
                unlink($target_dir . $row['image']);
            }
            $img_name = $new_img_name;
        }
    }

    if ($row) {
        $sql = "UPDATE about_us SET title='$title', subtitle='$subtitle', description='$desc', exp_years='$exp', happy_clients='$clients', image='$img_name' WHERE id=" . $row['id'];
    } else {
        $sql = "INSERT INTO about_us (title, subtitle, description, exp_years, happy_clients, image) VALUES ('$title', '$subtitle', '$desc', '$exp', '$clients', '$img_name')";
    }

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('About Section Updated Successfully!'); window.location='manage_about.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage About | Admin Panel</title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --p-blue: #3b82f6; --p-slate: #f8fafc; --p-dark: #0f172a; }
        body { background: var(--p-slate); font-family: 'Plus Jakarta Sans', sans-serif; color: #334155; }
        
        .setup-card { background: #fff; border-radius: 30px; border: none; box-shadow: 0 20px 50px rgba(0,0,0,0.05); padding: 40px; }
        
        .form-label { font-weight: 700; color: #64748b; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 10px; }
        .form-control { border-radius: 15px; padding: 14px; border: 2px solid #f1f5f9; background: #f8fafc; font-weight: 600; transition: 0.3s; }
        .form-control:focus { border-color: var(--p-blue); background: #fff; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); }
        
        .preview-pane { background: #f1f5f9; border-radius: 20px; padding: 20px; text-align: center; border: 2px dashed #cbd5e1; }
        .img-preview { max-width: 100%; border-radius: 15px; box-shadow: 0 10px 20px rgba(0,0,0,0.1); height: 200px; object-fit: cover; }
        
        .stat-input-group { background: #eff6ff; padding: 20px; border-radius: 20px; border: 1px solid #dbeafe; }
        .btn-publish { background: var(--p-dark); color: white; border: none; border-radius: 18px; padding: 18px; font-weight: 800; transition: 0.4s; }
        .btn-publish:hover { background: var(--p-blue); transform: translateY(-3px); box-shadow: 0 15px 30px rgba(59, 130, 246, 0.3); }
    </style>
</head>
<body class="py-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-11 col-xl-10">
            
            <div class="d-flex justify-content-between align-items-center mb-4 px-3">
                <div>
                    <h2 class="fw-800 mb-1">Company <span class="text-primary">Profile</span></h2>
                    <p class="text-muted small fw-600">Update your story, mission, and achievements.</p>
                </div>
                <a href="admin_panel.php" class="btn btn-white shadow-sm rounded-pill px-4 fw-bold border">
                    <i class="fa fa-arrow-left me-2"></i> Dashboard
                </a>
            </div>

            <form method="POST" enctype="multipart/form-data" class="setup-card">
                <div class="row g-4">
                    
                    <div class="col-md-7">
                        <div class="mb-4">
                            <label class="form-label">Main Heading</label>
                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($row['title'] ?? '') ?>" placeholder="e.g. Revolutionizing the Livestock Industry" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Sub-Headline (Short Mission)</label>
                            <input type="text" name="subtitle" class="form-control" value="<?= htmlspecialchars($row['subtitle'] ?? '') ?>" placeholder="e.g. Quality and Trust at your doorstep.">
                        </div>

                        <div class="mb-0">
                            <label class="form-label">Our Story (Long Description)</label>
                            <textarea name="description" class="form-control" rows="8" placeholder="Describe your company history and goals..." required><?= htmlspecialchars($row['description'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <div class="mb-4">
                            <label class="form-label">Section Image</label>
                            <div class="preview-pane mb-3">
                                <img src="../uploads/<?= !empty($row['image']) ? $row['image'] : 'placeholder-about.jpg' ?>" id="aboutPreview" class="img-preview mb-3">
                                <input type="file" name="image" id="aboutInput" class="form-control form-control-sm">
                                <p class="small text-muted mt-2 mb-0">Recommended size: 800x600px</p>
                            </div>
                        </div>

                        <div class="stat-input-group">
                            <h6 class="fw-800 text-primary mb-3"><i class="fa fa-chart-line me-2"></i> Impact Counters</h6>
                            <div class="mb-3">
                                <label class="form-label text-dark">Years of Experience</label>
                                <input type="text" name="exp_years" class="form-control bg-white" value="<?= htmlspecialchars($row['exp_years'] ?? '') ?>" placeholder="e.g. 10+ Years">
                            </div>
                            <div class="mb-0">
                                <label class="form-label text-dark">Community Members</label>
                                <input type="text" name="happy_clients" class="form-control bg-white" value="<?= htmlspecialchars($row['happy_clients'] ?? '') ?>" placeholder="e.g. 50k+ Happy Farmers">
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-4 text-end">
                        <button type="submit" name="save_about" class="btn-publish w-100">
                            <i class="fa-solid fa-cloud-arrow-up me-2"></i> UPDATE ABOUT SECTION
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    // Live Image Preview
    const imgInput = document.getElementById('aboutInput');
    const preview = document.getElementById('aboutPreview');

    imgInput.onchange = evt => {
        const [file] = imgInput.files;
        if (file) {
            preview.src = URL.createObjectURL(file);
        }
    }
</script>

</body>
</html>