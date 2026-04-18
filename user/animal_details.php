<?php 
include '../db.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. ID Check (Security)
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: animals.php");
    exit();
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// 2. Fetch Animal Data
$query = "SELECT * FROM animals WHERE id = '$id'";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Database Error (Animals): " . mysqli_error($conn));
}

$animal = mysqli_fetch_assoc($result);

if(!$animal) {
    echo "<div style='text-align:center; padding:100px; font-family:sans-serif;'>
            <h3>Janwar ki maloomat nahi milin!</h3>
            <a href='animals.php' style='color:#0ea5e9;'>Market wapas jayein</a>
          </div>";
    exit();
}

// 3. Fetch Seller Data
$seller_id = $animal['user_id'];
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$seller_id'");
$u_data = mysqli_fetch_assoc($user_query);

$seller_name = $u_data['full_name'] ?? 'Verified Seller';
$seller_phone = $u_data['phone'] ?? 'No Number';
$seller_pic = $u_data['profile_pic'] ?? '';
$whatsapp_number = preg_replace('/[^0-9]/', '', $seller_phone);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($animal['title']) ?> | PashuMandi</title>
    <link rel="icon" href="../pics/icon.png">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root { --p-blue: #0ea5e9; --p-dark: #0f172a; --p-teal: #14b8a6; --p-bg: #f8fafc; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #fff; color: var(--p-dark); }
        
        /* Media Gallery Section */
        .media-holder { background: white; border-radius: 35px; padding: 20px; border: 1px solid #f1f5f9; box-shadow: 0 15px 45px rgba(0,0,0,0.03); }
        .main-display { width: 100%; height: 500px; border-radius: 25px; object-fit: cover; background: #eee; cursor: zoom-in; }
        
        .thumbnail-strip { display: flex; gap: 12px; margin-top: 15px; overflow-x: auto; padding-bottom: 10px; }
        .thumb-box { width: 80px; height: 80px; border-radius: 15px; overflow: hidden; cursor: pointer; flex-shrink: 0; border: 2px solid transparent; transition: 0.3s; }
        .thumb-box:hover { border-color: var(--p-blue); }
        .thumb-box img { width: 100%; height: 100%; object-fit: cover; }

        .video-container { border-radius: 30px; overflow: hidden; background: #000; margin-top: 25px; position: relative; }
        .video-tag { position: absolute; top: 15px; left: 15px; background: var(--p-blue); color: white; padding: 4px 12px; border-radius: 8px; font-size: 0.7rem; font-weight: 800; z-index: 2; }

        /* Pricing & Details */
        .price-hero { font-size: 3rem; font-weight: 800; color: var(--p-blue); letter-spacing: -1.5px; }
        .info-card { background: var(--p-bg); border-radius: 25px; padding: 25px; border: 1px solid #e2e8f0; }
        .spec-item { display: flex; flex-direction: column; gap: 4px; }
        .spec-label { font-size: 0.7rem; color: #94a3b8; font-weight: 800; text-transform: uppercase; }
        .spec-value { font-weight: 700; color: var(--p-dark); }

        /* Seller & Sidebar */
        .seller-box { background: var(--p-dark); color: white; border-radius: 30px; padding: 30px; position: sticky; top: 20px; }
        .seller-avatar { width: 65px; height: 65px; border-radius: 20px; border: 2px solid rgba(255,255,255,0.2); }
        
        .action-btn { border-radius: 18px; padding: 15px; font-weight: 800; transition: 0.4s; display: flex; align-items: center; justify-content: center; gap: 10px; border: none; width: 100%; margin-bottom: 12px; text-decoration: none; }
        .btn-call { background: var(--p-blue); color: white; }
        .btn-wa { background: #25d366; color: white; }
        .btn-msg { background: rgba(255,255,255,0.1); color: white; }
        .btn-buy { background: white; color: var(--p-dark); margin-top: 15px; }
        
        @media (max-width: 768px) { .main-display { height: 350px; } .price-hero { font-size: 2.2rem; } }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="animals.php" class="text-decoration-none text-muted fw-bold">
            <i class="fa fa-chevron-left me-2"></i> Back to Mandi
        </a>
        <button class="btn btn-light rounded-pill px-3 shadow-sm"><i class="fa fa-share-nodes"></i></button>
    </div>

    <div class="row g-5">
        <div class="col-lg-7">
            <div class="media-holder">
                <?php 
                    $all_imgs = explode(',', $animal['image']);
                    $main_img = !empty($all_imgs[0]) ? "../uploads/".$all_imgs[0] : "https://via.placeholder.com/800";
                ?>
                <img src="<?= $main_img ?>" class="main-display" id="mainImage" alt="Animal">
                
                <?php if(count($all_imgs) > 1): ?>
                <div class="thumbnail-strip">
                    <?php foreach($all_imgs as $m_img): if(!empty($m_img)): ?>
                        <div class="thumb-box" onclick="document.getElementById('mainImage').src='../uploads/<?= $m_img ?>'">
                            <img src="../uploads/<?= $m_img ?>">
                        </div>
                    <?php endif; endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <?php if(!empty($animal['video'])): ?>
            <div class="video-container">
                <span class="video-tag"><i class="fa fa-play me-1"></i> VIDEO TOUR</span>
                <video width="100%" controls style="max-height: 450px;">
                    <source src="../uploads/<?= $animal['video'] ?>" type="video/mp4">
                    Your browser does not support video.
                </video>
            </div>
            <?php endif; ?>

            <div class="mt-5 px-3">
                <h4 class="fw-800 mb-4 border-start border-4 border-primary ps-3">Janwar ki Tafseel</h4>
                <p class="text-muted fs-5" style="line-height: 1.8; white-space: pre-line;">
                    <?= htmlspecialchars($animal['description']) ?>
                </p>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="mb-4">
                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill mb-3 fw-bold">
                    <?= strtoupper($animal['category']) ?>
                </span>
                <h1 class="fw-800 mb-1"><?= htmlspecialchars($animal['title']) ?></h1>
                <p class="text-muted fw-600"><i class="fa fa-map-marker-alt text-danger me-1"></i> <?= $animal['location'] ?></p>
                <div class="price-hero">Rs. <?= number_format($animal['price']) ?></div>
            </div>

            <div class="info-card mb-4">
                <div class="row g-4 text-center">
                    <div class="col-6 border-end">
                        <div class="spec-item">
                            <span class="spec-label">Breed / Nasal</span>
                            <span class="spec-value"><?= !empty($animal['brand']) ? $animal['brand'] : 'Asli' ?></span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="spec-item">
                            <span class="spec-label">Purpose</span>
                            <span class="spec-value"><?= $animal['sub_category'] ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="seller-box shadow-lg">
                <div class="d-flex align-items-center mb-4">
                    <?php $avatar = !empty($seller_pic) ? "../uploads/".$seller_pic : "https://ui-avatars.com/api/?name=".urlencode($seller_name)."&background=0ea5e9&color=fff"; ?>
                    <img src="<?= $avatar ?>" class="seller-avatar" alt="Seller">
                    <div class="ms-3">
                        <h6 class="fw-800 mb-0"><?= $seller_name ?></h6>
                        <small class="opacity-50 fw-bold">Verified PashuMandi Seller</small>
                    </div>
                </div>

                <div class="actions">
                    <?php if($animal['show_phone'] == 1): ?>
                        <a href="tel:<?= $seller_phone ?>" class="action-btn btn-call">
                            <i class="fa fa-phone"></i> CALL SELLER
                        </a>
                        <a href="https://wa.me/<?= $whatsapp_number ?>?text=Asalam-o-Alaikum, I'm interested in: <?= urlencode($animal['title']) ?>" target="_blank" class="action-btn btn-wa">
                            <i class="fab fa-whatsapp"></i> WHATSAPP
                        </a>
                    <?php endif; ?>
                    
                    <a href="messages.php?receiver=<?= $seller_id ?>" class="action-btn btn-msg">
                        <i class="fa-regular fa-comments"></i> PRIVATE MESSAGE
                    </a>

                    <form action="checkout.php" method="GET">
                        <input type="hidden" name="id" value="<?= $animal['id'] ?>">
                        <button type="submit" class="action-btn btn-buy">
                            <i class="fa fa-shopping-bag me-2"></i> BUY NOW (SECURE)
                        </button>
                    </form>
                </div>

                <div class="mt-4 pt-3 border-top border-secondary text-center">
                    <p class="small opacity-50 mb-0"><i class="fa fa-shield-halved"></i> Payment after inspection is recommended.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>