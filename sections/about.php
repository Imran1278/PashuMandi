<?php
include 'db.php';
$fetch = mysqli_query($conn, "SELECT * FROM about_us LIMIT 1");
$data = mysqli_fetch_assoc($fetch);

// Null Handling - Pure logic is same
if (!$data) {
    $title = "Welcome to Our Mandi";
    $subtitle = "The Digital Future of Livestock";
    $desc = "Please update about section from admin panel to show your brand story here.";
    $img = "default-placeholder.jpg";
    $exp = "0";
    $clients = "0";
} else {
    $title = $data['title'];
    $subtitle = $data['subtitle'];
    $desc = $data['description'];
    $img = !empty($data['image']) ? "uploads/".$data['image'] : "default-placeholder.jpg";
    $exp = $data['exp_years'];
    $clients = $data['happy_clients'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | <?= htmlspecialchars($title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { 
            --accent-color: #0ea5e9; 
            --dark-navy: #0f172a;
            --soft-bg: #f8fafc;
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #fff; 
            color: var(--dark-navy);
            overflow-x: hidden;
        }

        /* Hero Text Outline Effect */
        .outline-text {
            -webkit-text-stroke: 1px #e2e8f0;
            color: transparent;
            font-size: 5rem;
            font-weight: 900;
            position: absolute;
            top: -40px;
            left: -20px;
            z-index: -1;
            opacity: 0.5;
        }

        /* Image Masking & Styling */
        .image-container {
            position: relative;
            padding: 20px;
        }

        .main-about-img {
            width: 100%;
            height: 650px;
            object-fit: cover;
            border-radius: 40px 150px 40px 40px;
            box-shadow: 0 50px 100px -20px rgba(15, 23, 42, 0.15);
            transition: 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .main-about-img:hover {
            border-radius: 40px;
            transform: scale(1.02);
        }

        /* Stat Cards Evolution */
        .stat-glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid #f1f5f9;
            border-radius: 24px;
            padding: 30px;
            transition: 0.4s;
            position: relative;
            overflow: hidden;
        }

        .stat-glass-card:hover {
            border-color: var(--accent-color);
            transform: translateY(-10px);
            box-shadow: 0 30px 60px -12px rgba(14, 165, 233, 0.15);
        }

        .stat-glass-card i {
            font-size: 2rem;
            color: var(--accent-color);
            margin-bottom: 15px;
            opacity: 0.2;
            position: absolute;
            right: 20px;
            top: 20px;
        }

        .accent-badge {
            display: inline-block;
            background: #f0f9ff;
            color: var(--accent-color);
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-size: 0.75rem;
            border: 1px solid #bae6fd;
        }

        .desc-text {
            font-size: 1.15rem;
            line-height: 1.8;
            color: #475569;
            border-left: 4px solid var(--accent-color);
            padding-left: 25px;
        }

        /* Floating Trust Tag */
        .trust-tag {
            background: var(--dark-navy);
            color: #fff;
            padding: 25px;
            border-radius: 24px;
            position: absolute;
            bottom: -30px;
            right: 20px;
            width: 280px;
            animation: float 4s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        @media (max-width: 991px) {
            .main-about-img { height: 450px; border-radius: 40px; }
            .outline-text { display: none; }
            .trust-tag { position: relative; width: 100%; right: 0; bottom: 0; margin-top: 20px; }
        }
    </style>
</head>
<body>

<section id="about"class="py-5 my-lg-5">
    <div class="container py-lg-5">
        <div class="row g-5 align-items-center">
            
            <div class="col-lg-6 order-2 order-lg-1">
                <div class="position-relative">
                    <span class="outline-text">ABOUT</span>
                    <div class="accent-badge mb-4">Established Marketplace</div>
                    <h1 class="display-3 fw-800 text-dark mb-4" style="letter-spacing: -2px;">
                        <?= htmlspecialchars($title) ?>
                    </h1>
                    <h5 class="text-primary fw-700 mb-4"><?= htmlspecialchars($subtitle) ?></h5>
                    
                    <p class="desc-text mb-5"><?= nl2br(htmlspecialchars($desc)) ?></p>

                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <div class="stat-glass-card">
                                <i class="fa-solid fa-award"></i>
                                <h2 class="fw-800 text-dark mb-1"><?= $exp ?>+</h2>
                                <span class="text-muted fw-bold small text-uppercase">Years of Excellence</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="stat-glass-card">
                                <i class="fa-solid fa-users-check"></i>
                                <h2 class="fw-800 text-dark mb-1"><?= $clients ?>+</h2>
                                <span class="text-muted fw-bold small text-uppercase">Satisfied Buyers</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-4">
                        <a href="index.php#contact" class="btn btn-dark rounded-pill px-5 py-3 fw-800 shadow-lg">
                            Get In Touch <i class="fa-solid fa-arrow-right-long ms-2 text-primary"></i>
                        </a>
                        <div class="d-none d-md-flex align-items-center gap-2">
                            <span class="fw-700 small">Follow Us:</span>
                            <a href="https://www.facebook.com" class="text-dark"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="https://www.instagram.com" class="text-dark ms-2"><i class="fa-brands fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 order-1 order-lg-2">
                <div class="image-container">
                    <img src="<?= $img ?>" class="main-about-img" alt="PashuMandi Journey">
                    
                    <div class="trust-tag shadow-2xl">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-wrap bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fa-solid fa-circle-check fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-800">Verified Trading</h6>
                                <p class="mb-0 small text-light opacity-75">Secure national network</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>

<div class="py-5"></div>

</body>
</html>