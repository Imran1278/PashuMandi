<?php 
include '../db.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$u_id = $_SESSION['user_id'];
$error = "";

// 1. FETCH CURRENT DATA
if(isset($_GET['id'])) {
    $p_id = mysqli_real_escape_string($conn, $_GET['id']);
    $res = mysqli_query($conn, "SELECT * FROM animals WHERE id='$p_id' AND user_id='$u_id'");
    $p = mysqli_fetch_assoc($res);

    if(!$p) { header("Location: my_products.php"); exit(); }
} else {
    header("Location: my_products.php");
    exit();
}

// 2. UPDATE LOGIC
if(isset($_POST['update_now'])) {
    $cat = mysqli_real_escape_string($conn, $_POST['category']);
    $sub_cat = mysqli_real_escape_string($conn, $_POST['sub_category']);
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $loc = mysqli_real_escape_string($conn, $_POST['location']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $show_num = isset($_POST['show_num']) ? (int)$_POST['show_num'] : 1;
    
    $target_dir = "../uploads/";
    $image_final_string = $p['image']; // Default: purani images
    $video_final = $p['video']; // Default: purani video

    // --- Handling Multiple New Images ---
    if(!empty($_FILES['images']['name'][0])) {
        $uploaded_images = [];
        foreach($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $img_name = time() . "_img_" . $key . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES["images"]["name"][$key]));
            if(move_uploaded_file($tmp_name, $target_dir . $img_name)) {
                $uploaded_images[] = $img_name;
            }
        }
        if(!empty($uploaded_images)) {
            // Purani images delete karein (optional logic, yahan hum override kar rahe hain)
            $old_imgs = explode(",", $p['image']);
            foreach($old_imgs as $old) { if(!empty($old) && file_exists($target_dir.$old)) unlink($target_dir.$old); }
            
            $image_final_string = implode(",", $uploaded_images);
        }
    }

    // --- Handling Video Update ---
    if(!empty($_FILES['video']['name'])) {
        $new_video = time() . "_vid_" . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES["video"]["name"]));
        if(move_uploaded_file($_FILES['video']['tmp_name'], $target_dir . $new_video)) {
            if(!empty($p['video']) && file_exists($target_dir.$p['video'])) unlink($target_dir.$p['video']);
            $video_final = $new_video;
        }
    }

    if(empty($error)) {
        $update_sql = "UPDATE animals SET 
                        title='$title', category='$cat', sub_category='$sub_cat', 
                        brand='$brand', price='$price', description='$desc', 
                        location='$loc', image='$image_final_string', video='$video_final', show_phone='$show_num' 
                       WHERE id='$p_id' AND user_id='$u_id'";

        if(mysqli_query($conn, $update_sql)) {
            echo "<script>window.location.href='my_products.php?msg=updated';</script>";
            exit();
        } else {
            $error = "Database Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Listing | PashuMandi</title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root { --p-teal: #14b8a6; --p-dark: #0f172a; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f1f5f9; color: #1e293b; }
        .edit-card { background: white; border-radius: 35px; box-shadow: 0 25px 70px rgba(0,0,0,0.04); padding: 40px; border:none; }
        .form-label { font-weight: 700; font-size: 0.85rem; text-transform: uppercase; color: #64748b; margin-bottom: 10px; }
        .form-control, .form-select { border-radius: 16px; padding: 14px 18px; border: 2px solid #f1f5f9; background: #f8fafc; font-weight: 600; }
        .current-media-box { background: #f8fafc; border-radius: 20px; padding: 15px; border: 2px solid #e2e8f0; }
        .preview-thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 12px; border: 2px solid white; box-shadow: 0 5px 10px rgba(0,0,0,0.1); }
        .btn-update { background: var(--p-dark); color: white; border-radius: 20px; padding: 18px; font-weight: 800; width: 100%; border: none; transition: 0.4s; }
        .btn-update:hover { background: var(--p-teal); transform: translateY(-2px); }
        .toggle-group { display: flex; background: #f1f5f9; padding: 5px; border-radius: 15px; width: fit-content; }
        .toggle-group input { display: none; }
        .toggle-group label { padding: 10px 20px; border-radius: 12px; cursor: pointer; font-weight: 700; transition: 0.3s; margin: 0; font-size: 0.85rem; }
        .toggle-group input:checked + label { background: white; color: var(--p-teal); }
    </style>
</head>
<body>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-800 mb-0">Edit Listing</h2>
                <a href="my_products.php" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm">Back</a>
            </div>

            <form method="POST" enctype="multipart/form-data" class="edit-card">
                
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Current Photos</label>
                        <div class="current-media-box d-flex flex-wrap gap-2">
                            <?php 
                            $imgs = explode(",", $p['image']);
                            foreach($imgs as $im): if(!empty($im)):
                            ?>
                                <img src="../uploads/<?= $im ?>" class="preview-thumb">
                            <?php endif; endforeach; ?>
                        </div>
                        <input type="file" name="images[]" class="form-control mt-2" multiple accept="image/*">
                        <small class="text-muted">Naye photos upload karne se purane delete ho jayenge.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Update Video (Optional)</label>
                        <div class="current-media-box">
                            <span class="small fw-bold"><?= !empty($p['video']) ? '<i class="fa fa-video me-1"></i> '.$p['video'] : 'No video uploaded' ?></span>
                        </div>
                        <input type="file" name="video" class="form-control mt-2" accept="video/*">
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select" required>
                            <option value="Cows" <?= $p['category'] == 'Cows' ? 'selected' : '' ?>>Cows (Gaye)</option>
                            <option value="Goats" <?= $p['category'] == 'Goats' ? 'selected' : '' ?>>Goats (Bakra)</option>
                            <option value="Buffalo" <?= $p['category'] == 'Buffalo' ? 'selected' : '' ?>>Buffalo (Bhains)</option>
                            <option value="Sheep" <?= $p['category'] == 'Sheep' ? 'selected' : '' ?>>Sheep (Dumba)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Breed / Nasal</label>
                        <input type="text" name="brand" class="form-control" value="<?= htmlspecialchars($p['brand']) ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Ad Title</label>
                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($p['title']) ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($p['description']) ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Price (PKR)</label>
                        <input type="number" name="price" class="form-control" value="<?= $p['price'] ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($p['location']) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Phone Visibility</label>
                        <div class="toggle-group mt-1">
                            <input type="radio" name="show_num" id="shY" value="1" <?= $p['show_phone'] == 1 ? 'checked' : '' ?>>
                            <label for="shY">Show</label>
                            <input type="radio" name="show_num" id="shN" value="0" <?= $p['show_phone'] == 0 ? 'checked' : '' ?>>
                            <label for="shN">Hide</label>
                        </div>
                    </div>
                </div>

                <button type="submit" name="update_now" class="btn-update mt-5">
                    <i class="fa-solid fa-save me-2"></i> UPDATE AD
                </button>
            </form>
        </div>
    </div>
</div>

</body>
</html>