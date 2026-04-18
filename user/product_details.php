<?php 
include '../db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if(!isset($_GET['id'])) { header("Location: ../index.php"); exit(); }
$id = mysqli_real_escape_string($conn, $_GET['id']);

$sql = "SELECT a.*, u.full_name, u.profile_pic, u.phone, u.created_at as member_since, u.id as seller_id,
        (SELECT COUNT(*) FROM animals WHERE user_id = u.id) as total_products
        FROM animals a JOIN users u ON a.user_id = u.id WHERE a.id = '$id'";
$res = mysqli_query($conn, $sql);
$p = mysqli_fetch_assoc($res);

if(!$p) { header("Location: ../index.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($p['title']) ?> | PashuMandi</title>
    <link rel="icon" href="../pics/icon.png">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <style>
        :root { 
            --p-teal: #14b8a6;
            --p-dark: #0f172a;
            --p-slate: #f8fafc;
        }

        body { background-color: #f1f5f9; font-family: 'Plus Jakarta Sans', sans-serif; color: #334155; }

        /* Media Gallery Design */
        .media-section { background: white; border-radius: 35px; padding: 25px; box-shadow: 0 20px 50px rgba(0,0,0,0.05); }
        .main-img-container { border-radius: 25px; overflow: hidden; height: 450px; background: #eee; }
        .main-img-container img { width: 100%; height: 100%; object-fit: cover; }
        
        .thumb-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-top: 15px; }
        .thumb-item { height: 80px; border-radius: 15px; overflow: hidden; cursor: pointer; border: 2px solid transparent; transition: 0.3s; }
        .thumb-item:hover { border-color: var(--p-teal); }
        .thumb-item img { width: 100%; height: 100%; object-fit: cover; }

        .video-box { background: var(--p-dark); border-radius: 25px; overflow: hidden; margin-top: 30px; position: relative; }
        .video-label { position: absolute; top: 20px; left: 20px; background: rgba(20, 184, 166, 0.9); color: white; padding: 5px 15px; border-radius: 10px; font-weight: 700; z-index: 5; }

        /* Info Styling */
        .info-card { background: white; border-radius: 35px; padding: 40px; box-shadow: 0 20px 50px rgba(0,0,0,0.05); }
        .price-tag { font-size: 2.8rem; font-weight: 800; color: var(--p-teal); letter-spacing: -1px; }
        
        .spec-item { background: #f8fafc; padding: 15px; border-radius: 20px; border: 1px solid #e2e8f0; }
        .spec-label { font-size: 0.75rem; text-transform: uppercase; font-weight: 700; color: #94a3b8; display: block; }
        .spec-value { font-weight: 700; color: var(--p-dark); }

        /* Seller Card */
        .seller-card { background: var(--p-dark); color: white; border-radius: 35px; padding: 30px; position: sticky; top: 30px; }
        .seller-avatar { width: 70px; height: 70px; border-radius: 20px; object-fit: cover; border: 3px solid rgba(255,255,255,0.1); }
        
        .btn-action { border-radius: 18px; padding: 15px; font-weight: 700; transition: 0.3s; width: 100%; border: none; margin-bottom: 12px; }
        .btn-call { background: var(--p-teal); color: white; }
        .btn-wa { background: #25d366; color: white; }
        .btn-msg { background: rgba(255,255,255,0.1); color: white; }
        .btn-buy { background: white; color: var(--p-dark); font-size: 1.1rem; }

        .section-title { font-weight: 800; color: var(--p-dark); margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .section-title::after { content: ''; height: 3px; flex-grow: 1; background: #e2e8f0; border-radius: 10px; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="mb-4">
        <a href="../index.php" class="text-decoration-none text-muted fw-600">
            <i class="fa fa-chevron-left me-2"></i> Back to Marketplace
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            
            <div class="media-section mb-4">
                <h5 class="section-title"><i class="fa fa-image text-teal"></i> Photos</h5>
                <div class="main-img-container">
                    <?php 
                        $all_images = explode(',', $p['image']); 
                        $first_img = !empty($all_images[0]) ? $all_images[0] : 'default.jpg';
                    ?>
                    <img src="../uploads/<?= $first_img ?>" id="mainView" alt="Animal">
                </div>
                
                <?php if(count($all_images) > 1): ?>
                <div class="thumb-grid">
                    <?php foreach($all_images as $img): if(!empty($img)): ?>
                        <div class="thumb-item" onclick="document.getElementById('mainView').src='../uploads/<?= $img ?>'">
                            <img src="../uploads/<?= $img ?>">
                        </div>
                    <?php endif; endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <?php if(!empty($p['video'])): ?>
            <div class="media-section mb-4">
                <h5 class="section-title"><i class="fa fa-video text-danger"></i> Video Preview</h5>
                <div class="video-box">
                    <span class="video-label">LIVE VIDEO</span>
                    <video width="100%" controls style="max-height: 450px;">
                        <source src="../uploads/<?= $p['video'] ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>
            <?php endif; ?>

            <div class="info-card">
                <div class="mb-4">
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-bold mb-2">
                        <?= strtoupper($p['category']) ?>
                    </span>
                    <h1 class="fw-800 display-6"><?= htmlspecialchars($p['title']) ?></h1>
                    <p class="text-muted"><i class="fa fa-location-dot me-2"></i> <?= $p['location'] ?></p>
                    <div class="price-tag">Rs. <?= number_format($p['price']) ?></div>
                </div>

                <div class="row g-3 mb-5">
                    <div class="col-6 col-md-3">
                        <div class="spec-item">
                            <span class="spec-label">Breed</span>
                            <span class="spec-value"><?= !empty($p['brand']) ? $p['brand'] : 'N/A' ?></span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="spec-item">
                            <span class="spec-label">Purpose</span>
                            <span class="spec-value"><?= $p['sub_category'] ?></span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="spec-item">
                            <span class="spec-label">Status</span>
                            <span class="spec-value text-success"><?= strtoupper($p['status']) ?></span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="spec-item">
                            <span class="spec-label">Listed On</span>
                            <span class="spec-value"><?= date('d M, Y', strtotime($p['created_at'])) ?></span>
                        </div>
                    </div>
                </div>

                <h5 class="section-title">Description</h5>
                <p class="text-secondary lh-lg" style="white-space: pre-line; font-size: 1.05rem;">
                    <?= htmlspecialchars($p['description']) ?>
                </p>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="seller-card shadow-lg">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <img src="../uploads/<?= !empty($p['profile_pic']) ? $p['profile_pic'] : 'default_user.png' ?>" class="seller-avatar">
                    <div>
                        <h5 class="mb-0 fw-bold"><?= $p['full_name'] ?></h5>
                        <p class="small opacity-50 mb-0">Seller ID: #P-<?= $p['seller_id'] ?></p>
                    </div>
                </div>

                <div class="d-grid">
                    <?php if($p['show_phone'] != 0): ?>
                    <button class="btn-action btn-call" onclick="this.innerHTML='<i class=\'fa fa-phone\'></i> <?= $p['phone'] ?>'">
                        <i class="fa fa-phone me-2"></i> Show Phone Number
                    </button>
                    <?php endif; ?>

                    <a href="https://wa.me/<?= str_replace(['+', ' '], '', $p['phone']) ?>" target="_blank" class="btn-action btn-wa text-decoration-none text-center">
                        <i class="fab fa-whatsapp me-2"></i> Chat on WhatsApp
                    </a>

                    <a href="messages.php?receiver=<?= $p['seller_id'] ?>" class="btn-action btn-msg text-decoration-none text-center">
                        <i class="far fa-comment-dots me-2"></i> Message Directly
                    </a>

                    <hr class="my-4 opacity-25">

                    <form action="checkout.php" method="GET">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn-action btn-buy shadow-sm">
                            <i class="fa fa-shopping-cart me-2"></i> BUY SECURELY
                        </button>
                    </form>
                </div>

                <div class="mt-4 p-3 rounded-4 bg-white bg-opacity-10">
                    <p class="small mb-0 text-center"><i class="fa fa-shield-halved me-2"></i> Always inspect animal before paying.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>