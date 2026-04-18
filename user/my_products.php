<?php 
include '../db.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$u_id = $_SESSION['user_id'];
$msg = isset($_GET['msg']) ? $_GET['msg'] : "";

// --- DELETE LOGIC (Multiple Files Support) ---
if(isset($_GET['delete'])) {
    $p_id = mysqli_real_escape_string($conn, $_GET['delete']);
    $res = mysqli_query($conn, "SELECT image, video FROM animals WHERE id='$p_id' AND user_id='$u_id'");
    $data = mysqli_fetch_assoc($res);
    
    if($data) {
        // Delete all images (comma separated)
        if(!empty($data['image'])) {
            $imgs = explode(",", $data['image']);
            foreach($imgs as $img) {
                if(file_exists("../uploads/".$img)) { unlink("../uploads/".$img); }
            }
        }
        // Delete video
        if(!empty($data['video']) && file_exists("../uploads/".$data['video'])) {
            unlink("../uploads/".$data['video']);
        }
        
        mysqli_query($conn, "DELETE FROM animals WHERE id='$p_id' AND user_id='$u_id'");
        header("Location: my_products.php?msg=deleted");
        exit();
    }
}

// Fetch user listings
$products = mysqli_query($conn, "SELECT * FROM animals WHERE user_id='$u_id' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Inventory | PashuMandi</title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root { --p-teal: #14b8a6; --p-dark: #0f172a; --p-bg: #f8fafc; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--p-bg); color: #475569; }
        .dashboard-wrapper { max-width: 1200px; margin: 50px auto; padding: 0 20px; }
        .page-header { background: white; padding: 35px; border-radius: 30px; box-shadow: 0 10px 40px rgba(0,0,0,0.02); margin-bottom: 35px; position: relative; }
        .page-header::after { content: ''; position: absolute; left: 0; top: 25%; height: 50%; width: 5px; background: var(--p-teal); border-radius: 0 10px 10px 0; }
        .inventory-card { background: white; border-radius: 35px; box-shadow: 0 20px 60px rgba(0,0,0,0.03); overflow: hidden; }
        .table thead th { background: #f8fafc; padding: 22px; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; color: #94a3b8; border: none; }
        .table tbody td { padding: 25px 22px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
        .thumb-box { width: 85px; height: 85px; border-radius: 22px; overflow: hidden; border: 4px solid #f1f5f9; transition: 0.3s; position: relative; }
        .thumb-box img { width: 100%; height: 100%; object-fit: cover; }
        .video-badge { position: absolute; bottom: 5px; right: 5px; background: rgba(0,0,0,0.6); color: white; padding: 2px 6px; border-radius: 6px; font-size: 10px; }
        tr:hover .thumb-box { transform: scale(1.05) rotate(-2deg); border-color: var(--p-teal); }
        .prod-name { font-weight: 800; color: var(--p-dark); font-size: 1.05rem; text-decoration: none; }
        .status-pill { padding: 8px 16px; border-radius: 12px; font-weight: 800; font-size: 0.7rem; text-transform: uppercase; display: inline-flex; align-items: center; gap: 8px; }
        .live-pill { background: #f0fdf4; color: #16a34a; border: 1px solid #dcfce7; }
        .live-dot { width: 7px; height: 7px; background: #16a34a; border-radius: 50%; box-shadow: 0 0 8px #16a34a; animation: pulse 1.5s infinite; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.4; } 100% { opacity: 1; } }
        .btn-circle { width: 45px; height: 45px; border-radius: 16px; display: inline-flex; align-items: center; justify-content: center; background: #f8fafc; color: #64748b; border: 1px solid #e2e8f0; transition: 0.3s; text-decoration: none; }
        .btn-circle:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .btn-edit:hover { background: var(--p-teal); color: white; }
        .btn-delete:hover { background: #fff1f2; color: #ef4444; }
        .btn-add-new { background: var(--p-dark); color: white; border-radius: 20px; padding: 16px 32px; font-weight: 800; text-decoration: none; transition: 0.4s; }
        .btn-add-new:hover { background: var(--p-teal); color: white; transform: translateY(-2px); }
        .empty-state { padding: 100px 20px; text-align: center; }
    </style>
</head>
<body>

<div class="dashboard-wrapper">
    
    <div class="page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item small fw-bold"><a href="profile.php" class="text-decoration-none text-muted">Account</a></li>
                    <li class="breadcrumb-item small active fw-bold text-teal" style="color:var(--p-teal)">Inventory</li>
                </ol>
            </nav>
            <h2 class="fw-800 mb-1">My Livestock Ads</h2>
            <p class="text-muted small mb-0">Managing <span class="badge bg-light text-dark border fw-bold"><?= mysqli_num_rows($products) ?> listings</span></p>
        </div>
        <a href="add_product.php" class="btn-add-new">
            <i class="fa-solid fa-plus me-2"></i> Post New Ad
        </a>
    </div>

    <?php if($msg == "success"): ?>
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 p-3 fw-bold">
            <i class="fa-solid fa-circle-check me-2"></i> Animal listed successfully!
        </div>
    <?php elseif($msg == "deleted"): ?>
        <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 p-3 fw-bold">
            <i class="fa-solid fa-trash me-2"></i> Listing and files removed.
        </div>
    <?php endif; ?>

    <div class="inventory-card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Livestock Details</th>
                        <th>Category</th>
                        <th>Valuation</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Controls</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($products) > 0): ?>
                        <?php while($p = mysqli_fetch_assoc($products)): 
                            // Get first image from comma separated list
                            $all_imgs = explode(",", $p['image']);
                            $display_img = (!empty($all_imgs[0]) && file_exists("../uploads/".$all_imgs[0])) ? $all_imgs[0] : 'default.png';
                        ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-4">
                                    <div class="thumb-box">
                                        <img src="../uploads/<?= $display_img ?>" alt="item">
                                        <?php if(!empty($p['video'])): ?>
                                            <span class="video-badge"><i class="fa-solid fa-video"></i></span>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <a href="animal_details.php?id=<?= $p['id'] ?>" class="prod-name d-block"><?= htmlspecialchars($p['title']) ?></a>
                                        <div class="text-muted small mt-1">
                                            <i class="fa-regular fa-clock me-1"></i> <?= date('d M, Y', strtotime($p['created_at'])) ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-800 text-dark small mb-1"><?= htmlspecialchars($p['category']) ?></div>
                                <span class="badge bg-light text-muted border" style="font-size: 10px;"><?= htmlspecialchars($p['brand']) ?></span>
                            </td>
                            <td>
                                <div class="fw-800 text-dark">PKR <?= number_format($p['price']) ?></div>
                                <div class="text-muted" style="font-size: 11px;"><i class="fa-solid fa-location-dot me-1"></i> <?= htmlspecialchars($p['location']) ?></div>
                            </td>
                            <td>
                                <span class="status-pill live-pill">
                                    <span class="live-dot"></span> Live
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn-circle btn-edit" title="Edit">
                                        <i class="fa-solid fa-pen-nib"></i>
                                    </a>
                                    <a href="my_products.php?delete=<?= $p['id'] ?>" 
                                       class="btn-circle btn-delete" 
                                       onclick="return confirm('Permanently delete this ad and all images?')">
                                         <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <h5 class="fw-800">No Listings Yet</h5>
                                    <p class="text-muted small">To Sell Your Animals, Plz Click 'Post New Ad'</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>