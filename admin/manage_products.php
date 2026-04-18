<?php 
include '../db.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. DELETE LOGIC WITH FULL MEDIA CLEANUP
if(isset($_GET['del_prod'])) {
    $id = mysqli_real_escape_string($conn, $_GET['del_prod']);
    
    // Fetch all media before deleting record
    $media_res = mysqli_query($conn, "SELECT image, video FROM animals WHERE id='$id'");
    $media = mysqli_fetch_assoc($media_res);
    
    if($media) {
        $target_dir = "../uploads/";
        
        // Delete all images (split by comma)
        if(!empty($media['image'])) {
            $imgs = explode(",", $media['image']);
            foreach($imgs as $img) {
                $img_path = $target_dir . trim($img);
                if(file_exists($img_path) && !empty($img)) { unlink($img_path); }
            }
        }
        
        // Delete video
        if(!empty($media['video'])) {
            $vid_path = $target_dir . trim($media['video']);
            if(file_exists($vid_path)) { unlink($vid_path); }
        }
    }

    mysqli_query($conn, "DELETE FROM animals WHERE id='$id'");
    header("Location: manage_products.php?status=deleted");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Global Inventory | Admin</title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --p-teal: #14b8a6; --p-dark: #0f172a; --p-danger: #ef4444; }
        body { background: #f1f5f9; font-family: 'Plus Jakarta Sans', sans-serif; color: #334155; }
        
        .admin-card { 
            background: white; border-radius: 30px; border: none; overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03); height: 100%;
            display: flex; flex-direction: column; transition: 0.3s;
        }
        .admin-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }

        .media-preview { position: relative; height: 220px; background: #000; overflow: hidden; }
        .media-preview img { width: 100%; height: 100%; object-fit: cover; opacity: 0.9; }
        
        .media-count { 
            position: absolute; top: 15px; left: 15px; background: rgba(0,0,0,0.6); 
            backdrop-filter: blur(5px); color: white; padding: 4px 12px; 
            border-radius: 10px; font-size: 0.7rem; font-weight: 800;
        }

        .video-indicator {
            position: absolute; top: 15px; right: 15px; background: var(--p-danger);
            color: white; width: 30px; height: 30px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center; font-size: 0.8rem;
        }

        .price-badge {
            position: absolute; bottom: 15px; right: 15px; background: var(--p-teal);
            color: white; padding: 5px 15px; border-radius: 12px; font-weight: 800;
        }

        .content-box { padding: 25px; flex-grow: 1; }
        .seller-info { 
            background: #f8fafc; padding: 15px 25px; border-top: 1px solid #f1f5f9;
            display: flex; align-items: center; justify-content: space-between;
        }

        .btn-round {
            width: 40px; height: 40px; border-radius: 12px; display: flex;
            align-items: center; justify-content: center; transition: 0.3s;
            text-decoration: none;
        }
        .btn-view { background: #f1f5f9; color: var(--p-dark); }
        .btn-view:hover { background: var(--p-dark); color: white; }
        .btn-del { background: #fee2e2; color: var(--p-danger); }
        .btn-del:hover { background: var(--p-danger); color: white; }

        .stat-pill { background: white; padding: 8px 20px; border-radius: 100px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); font-weight: 800; font-size: 0.85rem; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5 gap-3">
        <div>
            <h2 class="fw-800 mb-0">Manage <span class="text-teal">Inventory</span></h2>
            <p class="text-muted fw-600 mb-0">Total listings currently live on PashuMandi</p>
        </div>
        <div class="d-flex gap-3">
            <div class="stat-pill text-teal border-start border-4 border-teal">
                <i class="fa fa-paw me-2"></i> <?= mysqli_num_rows(mysqli_query($conn,"SELECT id FROM animals")) ?> Animals
            </div>
            <a href="admin_panel.php" class="btn btn-dark rounded-pill px-4 fw-800">Back</a>
        </div>
    </div>

    <div class="row g-4">
        <?php 
        $sql = "SELECT a.*, u.full_name, u.phone FROM animals a 
                JOIN users u ON a.user_id = u.id 
                ORDER BY a.id DESC";
        $res = mysqli_query($conn, $sql);
        
        while($p = mysqli_fetch_assoc($res)): 
            $images = explode(',', $p['image']);
            $main_img = !empty($images[0]) ? $images[0] : 'default.jpg';
        ?>
        <div class="col-lg-4 col-md-6">
            <div class="admin-card">
                <div class="media-preview">
                    <img src="../uploads/<?= $main_img ?>" alt="animal">
                    
                    <div class="media-count">
                        <i class="fa fa-camera me-1"></i> <?= count($images) ?> Photos
                    </div>

                    <?php if(!empty($p['video'])): ?>
                    <div class="video-indicator" title="Video Included">
                        <i class="fa fa-play"></i>
                    </div>
                    <?php endif; ?>

                    <div class="price-badge">Rs. <?= number_format($p['price']) ?></div>
                </div>

                <div class="content-box">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="badge bg-light text-dark text-uppercase"><?= $p['category'] ?></span>
                        <span class="small text-muted fw-bold">ID: #<?= $p['id'] ?></span>
                    </div>
                    <h5 class="fw-800 mb-1 text-truncate"><?= htmlspecialchars($p['title']) ?></h5>
                    <p class="small text-muted mb-0"><i class="fa fa-map-marker-alt me-1 text-danger"></i> <?= $p['location'] ?></p>
                </div>

                <div class="seller-info">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-teal bg-opacity-10 text-teal p-2 rounded-3">
                            <i class="fa fa-user" style="font-size: 0.8rem;"></i>
                        </div>
                        <div>
                            <span class="d-block fw-800 small text-dark"><?= htmlspecialchars($p['full_name']) ?></span>
                            <span class="text-muted" style="font-size: 0.7rem;"><?= $p['phone'] ?></span>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <a href="../product_details.php?id=<?= $p['id'] ?>" target="_blank" class="btn-round btn-view" title="Preview">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a href="?del_prod=<?= $p['id'] ?>" class="btn-round btn-del" onclick="return confirm('Pura data (Photos, Video, Info) delete ho jayega. Sure?')" title="Delete">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>