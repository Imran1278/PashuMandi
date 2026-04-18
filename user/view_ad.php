<?php 
include '../db.php'; 
session_start();

// Get Ad ID
if(!isset($_GET['id'])) { header("Location: index.php"); exit(); }
$ad_id = mysqli_real_escape_string($conn, $_GET['id']);

// Fetch Ad Details with Seller Info
$query = "SELECT a.*, u.full_name, u.phone, u.address as loaction 
          FROM animals a 
          JOIN users u ON a.user_id = u.id 
          WHERE a.id = '$ad_id'";
$res = mysqli_query($conn, $query);
$ad = mysqli_fetch_assoc($res);

if(!$ad) { echo "Ad not found!"; exit(); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($ad['title']) ?> | Admin View</title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --pm-blue: #0ea5e9; --pm-dark: #0f172a; --pm-teal: #14b8a6; }
        body { background: #f1f5f9; font-family: 'Plus Jakarta Sans', sans-serif; color: var(--pm-dark); }
        
        /* Media Section Design */
        .media-card { background: white; border-radius: 35px; padding: 25px; box-shadow: 0 20px 50px rgba(0,0,0,0.05); border: none; }
        .main-img-view { width: 100%; height: 450px; border-radius: 25px; object-fit: cover; background: #eee; }
        
        .thumb-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-top: 15px; }
        .thumb-box { height: 70px; border-radius: 12px; overflow: hidden; cursor: pointer; border: 2px solid transparent; transition: 0.3s; }
        .thumb-box:hover { border-color: var(--pm-blue); }
        .thumb-box img { width: 100%; height: 100%; object-fit: cover; }

        .video-wrapper { background: #000; border-radius: 25px; overflow: hidden; margin-top: 25px; position: relative; }
        .video-badge { position: absolute; top: 15px; left: 15px; background: var(--pm-teal); color: white; padding: 4px 12px; border-radius: 8px; font-size: 0.75rem; font-weight: 800; z-index: 2; }

        /* Information Styling */
        .details-card { background: white; border-radius: 35px; padding: 40px; border: none; box-shadow: 0 20px 50px rgba(0,0,0,0.05); }
        .price-text { font-size: 2.5rem; font-weight: 800; color: var(--pm-teal); }
        
        .feature-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px; margin: 25px 0; }
        .feature-item { background: #f8fafc; padding: 15px; border-radius: 20px; text-align: center; border: 1px solid #e2e8f0; }
        .feature-item i { color: var(--pm-blue); font-size: 1.2rem; margin-bottom: 5px; display: block; }
        .feature-item span { font-size: 0.7rem; color: #64748b; text-transform: uppercase; font-weight: 700; display: block; }
        .feature-item b { font-size: 0.9rem; color: var(--pm-dark); }

        .seller-info-box { background: var(--pm-dark); color: white; border-radius: 30px; padding: 30px; margin-top: 30px; }
        .btn-call { background: var(--pm-blue); color: white; border-radius: 18px; padding: 15px; font-weight: 800; width: 100%; border: none; transition: 0.3s; }
        .btn-call:hover { background: white; color: var(--pm-dark); transform: translateY(-3px); }

        .section-label { font-weight: 800; font-size: 0.9rem; color: var(--pm-blue); text-transform: uppercase; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        .section-label::after { content: ''; flex-grow: 1; height: 2px; background: #e2e8f0; }
    </style>
</head>
<body>

    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="../admin/manage_products.php" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm">
                <i class="fa fa-arrow-left me-2"></i> Back to Inventory
            </a>
            <span class="badge bg-white text-dark shadow-sm rounded-pill px-3 py-2 fw-bold">Admin Preview Mode</span>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="media-card mb-4">
                    <div class="section-label">Photos Gallery</div>
                    <?php 
                        $images = explode(',', $ad['image']); 
                        $display_img = !empty($images[0]) ? $images[0] : 'default.jpg';
                    ?>
                    <img src="../uploads/<?= $display_img ?>" id="mainImage" class="main-img-view" alt="Main Photo">
                    
                    <?php if(count($images) > 1): ?>
                    <div class="thumb-grid">
                        <?php foreach($images as $img): if(!empty($img)): ?>
                            <div class="thumb-box" onclick="document.getElementById('mainImage').src='../uploads/<?= $img ?>'">
                                <img src="../uploads/<?= $img ?>">
                            </div>
                        <?php endif; endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if(!empty($ad['video'])): ?>
                <div class="media-card">
                    <div class="section-label">Video Preview</div>
                    <div class="video-wrapper">
                        <span class="video-badge"><i class="fa fa-play me-1"></i> VIDEO ATTACHED</span>
                        <video width="100%" controls style="max-height: 400px;">
                            <source src="../uploads/<?= $ad['video'] ?>" type="video/mp4">
                        </video>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-5">
                <div class="details-card shadow-sm">
                    <div class="mb-4">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-2 fw-800">
                            <?= strtoupper($ad['category']) ?>
                        </span>
                        <h1 class="fw-800 h2 mb-2"><?= htmlspecialchars($ad['title']) ?></h1>
                        <div class="price-text">Rs. <?= number_format($ad['price']) ?></div>
                    </div>

                    <div class="feature-grid">
                        <div class="feature-item">
                            <i class="fa fa-paw"></i>
                            <span>Breed</span>
                            <b><?= !empty($ad['brand']) ? $ad['brand'] : 'General' ?></b>
                        </div>
                        <div class="feature-item">
                            <i class="fa fa-map-marker-alt"></i>
                            <span>Location</span>
                            <b><?= !empty($ad['location']) ? $ad['location'] : 'N/A' ?></b>
                        </div>
                        <div class="feature-item">
                            <i class="fa fa-calendar-alt"></i>
                            <span>Listed</span>
                            <b><?= date('d M, Y', strtotime($ad['created_at'])) ?></b>
                        </div>
                    </div>

                    <div class="section-label">Description</div>
                    <p class="text-muted lh-lg mb-5" style="white-space: pre-line;">
                        <?= htmlspecialchars($ad['description']) ?>
                    </p>

                    <div class="seller-info-box">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="bg-white bg-opacity-10 p-3 rounded-circle">
                                <i class="fa fa-user-shield text-info"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-800">Seller Identity</h6>
                                <p class="mb-0 text-info small fw-700"><?= htmlspecialchars($ad['full_name']) ?></p>
                            </div>
                        </div>
                        
                        <a href="tel:<?= $ad['phone'] ?>" class="btn btn-call shadow-lg text-decoration-none d-flex align-items-center justify-content-center">
                            <i class="fa fa-phone-volume me-2"></i> CALL <?= $ad['phone'] ?>
                        </a>
                        
                        <div class="text-center mt-3">
                            <small class="opacity-50 fw-600" style="font-size: 0.7rem;">Verified Listing - Secure Mandi Database</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>