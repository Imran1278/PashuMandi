<?php 
include 'db.php';
$hero_query = mysqli_query($conn, "SELECT * FROM hero_section LIMIT 1");
$hr = mysqli_fetch_assoc($hero_query);

// Image Path Logic
$bg_img = !empty($hr['hero_image']) ? 'uploads/'.$hr['hero_image'] : 'assets/default-hero.jpg';
?>

<?php if($hr): ?>
<section class="hero-master">
    <div class="hero-bg-shape-1"></div>
    <div class="hero-bg-shape-2"></div>

    <div class="container position-relative" style="z-index: 2;">
        <div class="row min-vh-100 align-items-center py-5">
            
            <div class="col-lg-6 mb-5 mb-lg-0">
                <div class="hero-content animate__animated animate__fadeInUp">
                    <div class="badge-new mb-3">
                        <span class="badge-dot pulse"></span>
                        <span class="badge-text">Pakistan's #1 Livestock Market</span>
                    </div>
                    
                    <h1 class="hero-heading mb-4">
                        <?= htmlspecialchars($hr['heading']) ?>
                    </h1>
                    
                    <p class="hero-subtext mb-5">
                        <?= nl2br(htmlspecialchars($hr['sub_heading'])) ?>
                    </p>
                    
                    <div class="hero-btns d-flex flex-wrap gap-3">
                        <?php if($hr['btn_one_text']): ?>
                            <a href="./user/add_product.php" class="btn btn-hero-primary">
                                <span><?= htmlspecialchars($hr['btn_one_text']) ?></span>
                                <i class="fa-solid fa-arrow-right-long ms-2"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if($hr['btn_two_text']): ?>
                            <a href="./user/learnmore.php" class="btn btn-hero-outline">
                                <?= htmlspecialchars($hr['btn_two_text']) ?>
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex align-items-center mt-5 gap-4 opacity-75">
                        <div class="trust-item"><i class="fa-solid fa-shield-check text-primary me-2"></i> Verified Sellers</div>
                        <div class="trust-item"><i class="fa-solid fa-clock text-primary me-2"></i> 24/7 Support</div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="hero-visual animate__animated animate__zoomIn">
                    <div class="image-wrapper shadow-2xl">
                        <img src="<?= $bg_img ?>" class="main-visual-img" alt="Hero Visual">
                        
                        <div class="floating-card shadow-lg">
                            <div class="d-flex align-items-center gap-3">
                                <div class="icon-box bg-success text-white">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-800 text-dark">100% Secure</h6>
                                    <small class="text-muted">Verified Trading</small>
                                </div>
                            </div>
                        </div>

                        <div class="floating-card-2 shadow-lg">
                            <h4 class="mb-0 fw-900 text-primary">25k+</h4>
                            <p class="mb-0 small text-dark fw-bold">Active Ads</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
<?php endif; ?>

<style>
    :root {
        --h-primary: #0ea5e9;
        --h-dark: #0f172a;
        --h-slate: #64748b;
    }

    .hero-master { 
        background: #fcfdfe; 
        overflow: hidden; 
        position: relative; 
        display: flex;
        align-items: center;
    }

    /* Abstract Shapes */
    .hero-bg-shape-1 {
        position: absolute; top: -10%; right: -5%; width: 500px; height: 500px;
        background: radial-gradient(circle, rgba(14, 165, 233, 0.1) 0%, transparent 70%);
        border-radius: 50%;
    }
    .hero-bg-shape-2 {
        position: absolute; bottom: -10%; left: -5%; width: 400px; height: 400px;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.08) 0%, transparent 70%);
        border-radius: 50%;
    }

    /* Heading Styling */
    .hero-heading {
        font-size: clamp(2.5rem, 5vw, 4.5rem);
        font-weight: 900;
        color: var(--h-dark);
        line-height: 1.1;
        letter-spacing: -2px;
    }

    .hero-subtext {
        font-size: 1.2rem;
        color: var(--h-slate);
        max-width: 550px;
        line-height: 1.7;
    }

    /* Buttons Modern */
    .btn-hero-primary {
        background: var(--h-primary);
        color: white;
        padding: 18px 40px;
        border-radius: 50px;
        font-weight: 800;
        border: none;
        box-shadow: 0 10px 30px rgba(14, 165, 233, 0.3);
        transition: 0.3s;
    }
    .btn-hero-primary:hover {
        background: var(--h-dark);
        color: white;
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }

    .btn-hero-outline {
        padding: 18px 40px;
        border-radius: 50px;
        font-weight: 800;
        border: 2px solid #e2e8f0;
        color: var(--h-dark);
        transition: 0.3s;
    }
    .btn-hero-outline:hover {
        background: #fff;
        border-color: var(--h-primary);
        color: var(--h-primary);
    }

    /* Visual Side */
    .image-wrapper {
        position: relative;
        padding: 20px;
    }

    .main-visual-img {
        width: 100%;
        height: 550px;
        object-fit: cover;
        border-radius: 60px 20px 60px 20px;
        border: 10px solid white;
        box-shadow: 0 40px 100px -20px rgba(0,0,0,0.15);
    }

    /* Floating Cards */
    .floating-card {
        position: absolute;
        bottom: 50px;
        left: -30px;
        background: white;
        padding: 20px 25px;
        border-radius: 25px;
        z-index: 5;
        border-left: 5px solid #10b981;
        animation: floatY 4s ease-in-out infinite;
    }

    .floating-card-2 {
        position: absolute;
        top: 60px;
        right: -10px;
        background: white;
        padding: 20px;
        border-radius: 25px;
        text-align: center;
        z-index: 5;
        min-width: 120px;
        animation: floatY 5s ease-in-out infinite alternate;
    }

    .icon-box {
        width: 45px; height: 45px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
    }

    /* New Badge */
    .badge-new {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #f0f9ff;
        padding: 8px 16px;
        border-radius: 50px;
        border: 1px solid #bae6fd;
    }
    .badge-dot { width: 8px; height: 8px; border-radius: 50%; }
    .badge-dot.pulse { background: var(--h-primary); box-shadow: 0 0 0 rgba(14, 165, 233, 0.4); animation: dotPulse 2s infinite; }
    .badge-text { font-size: 0.85rem; font-weight: 800; color: var(--h-primary); text-transform: uppercase; letter-spacing: 1px; }

    /* Animations */
    @keyframes floatY {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }

    @keyframes dotPulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(14, 165, 233, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(14, 165, 233, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(14, 165, 233, 0); }
    }

    @media (max-width: 991px) {
        .hero-heading { font-size: 3rem; text-align: center; }
        .hero-subtext { text-align: center; margin-inline: auto; }
        .hero-btns { justify-content: center; }
        .main-visual-img { height: 400px; border-radius: 30px; }
        .floating-card, .floating-card-2 { display: none; }
    }
</style>