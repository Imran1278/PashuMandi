<?php 
include '../db.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// // Security: Check if admin is logged in
// if(!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit(); }

$show_success = isset($_GET['msg']) && $_GET['msg'] == 'success';

// Fetch current hero settings
$hero_res = mysqli_query($conn, "SELECT * FROM hero_section LIMIT 1");
$hero = mysqli_fetch_assoc($hero_res);

if(isset($_POST['update_hero'])) {
    $heading = mysqli_real_escape_string($conn, $_POST['heading']);
    $sub_heading = mysqli_real_escape_string($conn, $_POST['sub_heading']);
    $btn1 = mysqli_real_escape_string($conn, $_POST['btn1']);
    $btn2 = mysqli_real_escape_string($conn, $_POST['btn2']);
    
    $image_name = $hero['hero_image'] ?? ''; 

    // Image Upload Logic
    if(!empty($_FILES['hero_img']['name'])) {
        $target_dir = "../uploads/"; 
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
        
        $new_image_name = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES["hero_img"]["name"]));
        $target_file = $target_dir . $new_image_name;
        
        if(move_uploaded_file($_FILES["hero_img"]["tmp_name"], $target_file)) {
            // Delete old file to save server space
            if(!empty($hero['hero_image']) && file_exists($target_dir . $hero['hero_image'])) {
                unlink($target_dir . $hero['hero_image']);
            }
            $image_name = $new_image_name;
        }
    }

    if($hero) {
        $q = "UPDATE hero_section SET heading='$heading', sub_heading='$sub_heading', btn_one_text='$btn1', btn_two_text='$btn2', hero_image='$image_name' WHERE id=".$hero['id'];
    } else {
        $q = "INSERT INTO hero_section (heading, sub_heading, btn_one_text, btn_two_text, hero_image) VALUES ('$heading', '$sub_heading', '$btn1', '$btn2', '$image_name')";
    }
    
    if(mysqli_query($conn, $q)) {
        header("Location: manage_hero.php?msg=success");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Hero | Admin Panel</title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --p-blue: #0ea5e9; --p-dark: #0f172a; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f1f5f9; color: var(--p-dark); }
        
        .hero-card { background: white; border-radius: 35px; border: none; box-shadow: 0 25px 60px rgba(0,0,0,0.05); padding: 40px; }
        
        /* Premium Image Preview */
        .img-container { 
            position: relative; width: 100%; border-radius: 25px; 
            overflow: hidden; background: #e2e8f0; border: 3px dashed #cbd5e1;
            aspect-ratio: 16/9; transition: 0.3s;
        }
        .img-container:hover { border-color: var(--p-blue); }
        .img-container img { width: 100%; height: 100%; object-fit: cover; }
        
        .custom-input { 
            border-radius: 15px; padding: 15px; border: 2px solid #f1f5f9; 
            background: #f8fafc; font-weight: 600; transition: 0.3s;
        }
        .custom-input:focus { border-color: var(--p-blue); background: #fff; box-shadow: none; }

        /* Button Preview Tag */
        .btn-tag { 
            display: inline-block; padding: 8px 18px; border-radius: 12px; 
            font-size: 0.75rem; font-weight: 800; text-transform: uppercase;
            background: #eff6ff; color: var(--p-blue); margin-bottom: 8px;
        }

        .success-toast { 
            position: fixed; top: 30px; right: 30px; background: #10b981; 
            color: white; padding: 18px 35px; border-radius: 20px; 
            box-shadow: 0 20px 40px rgba(16,185,129,0.25); z-index: 9999;
            font-weight: 700; display: flex; align-items: center; gap: 12px;
        }
    </style>
</head>
<body>

    <?php if($show_success): ?>
    <div class="success-toast animate__animated animate__slideInRight">
        <i class="fa-solid fa-wand-magic-sparkles"></i> Hero section updated successfully!
    </div>
    <script>setTimeout(() => { window.location.href='manage_hero.php'; }, 3000);</script>
    <?php endif; ?>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-xl-10">
                
                <div class="d-flex justify-content-between align-items-end mb-5 px-3">
                    <div>
                        <h2 class="fw-800 mb-1">Visual <span class="text-primary">Hero</span></h2>
                        <p class="text-muted small fw-600 mb-0">Control the landing experience of your users.</p>
                    </div>
                    <a href="admin_panel.php" class="btn btn-white shadow-sm rounded-pill px-4 fw-bold border">
                        <i class="fa fa-arrow-left me-2"></i> Dashboard
                    </a>
                </div>

                <div class="hero-card">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row g-5">
                            
                            <div class="col-md-5">
                                <label class="form-label fw-800 text-muted small text-uppercase mb-3">Hero Backdrop Image</label>
                                <div class="img-container mb-4 shadow-sm" id="imageWrapper">
                                    <?php if(!empty($hero['hero_image'])): ?>
                                        <img src="../uploads/<?= $hero['hero_image'] ?>" id="output">
                                    <?php else: ?>
                                        <div class="h-100 d-flex flex-column align-items-center justify-content-center text-muted">
                                            <i class="fa-regular fa-image fa-3x mb-3"></i>
                                            <span class="small fw-700">No Image Uploaded</span>
                                        </div>
                                        <img id="output" style="display:none">
                                    <?php endif; ?>
                                </div>
                                <input type="file" name="hero_img" class="form-control custom-input" onchange="loadFile(event)">
                                <div class="mt-3 p-3 bg-light rounded-4 border">
                                    <small class="text-muted d-block"><i class="fa fa-lightbulb me-2 text-warning"></i> <b>Pro Tip:</b> Use a dark or high-contrast image for better text readability.</small>
                                </div>
                            </div>

                            <div class="col-md-7">
                                <div class="mb-4">
                                    <label class="form-label fw-800 text-muted small text-uppercase">Main Headline</label>
                                    <input type="text" name="heading" class="form-control custom-input h4 fw-800" value="<?= htmlspecialchars($hero['heading'] ?? '') ?>" placeholder="Enter catchy headline..." required>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-800 text-muted small text-uppercase">Sub-Heading (Tagline)</label>
                                    <textarea name="sub_heading" class="form-control custom-input" rows="4" placeholder="Briefly describe your platform's mission..."><?= htmlspecialchars($hero['sub_heading'] ?? '') ?></textarea>
                                </div>

                                <div class="row g-3">
                                    <div class="col-6">
                                        <span class="btn-tag">Primary Button</span>
                                        <input type="text" name="btn1" class="form-control custom-input" value="<?= htmlspecialchars($hero['btn_one_text'] ?? 'Explore') ?>">
                                    </div>
                                    <div class="col-6">
                                        <span class="btn-tag">Secondary Button</span>
                                        <input type="text" name="btn2" class="form-control custom-input" value="<?= htmlspecialchars($hero['btn_two_text'] ?? 'Contact Us') ?>">
                                    </div>
                                </div>

                                <button type="submit" name="update_hero" class="btn btn-dark w-100 py-3 mt-5 rounded-pill fw-800 shadow-lg border-0 transition">
                                    <i class="fa-solid fa-cloud-arrow-up me-2"></i> UPDATE HERO SECTION
                                </button>
                            </div>

                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        var loadFile = function(event) {
            var output = document.getElementById('output');
            var wrapper = document.getElementById('imageWrapper');
            
            // If there's a placeholder div, remove it
            if(wrapper.querySelector('.h-100')) {
                wrapper.querySelector('.h-100').remove();
            }

            output.src = URL.createObjectURL(event.target.files[0]);
            output.style.display = "block";
            output.onload = function() {
                URL.revokeObjectURL(output.src)
            }
        };
    </script>
</body>
</html>