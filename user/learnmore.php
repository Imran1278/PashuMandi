<?php 
include '../db.php'; 
// Fetching dynamic data
$query = mysqli_query($conn, "SELECT * FROM site_details WHERE id=1");
$data = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learn More | PashuMandi </title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root { --p-blue: #0ea5e9; --p-dark: #0f172a; --p-silver: #f1f5f9; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; color: var(--p-dark); overflow-x: hidden; }
        
        /* Glassmorphism Header */
        .hero-bg { 
            background: radial-gradient(circle at top right, #e0f2fe, #fff);
            padding: 140px 0 100px 0;
            position: relative;
        }

        /* Section Titles */
        .section-tag { background: #e0f2fe; color: var(--p-blue); padding: 8px 20px; border-radius: 50px; font-weight: 800; font-size: 0.8rem; text-transform: uppercase; display: inline-block; margin-bottom: 20px; }

        /* Feature Grid */
        .grid-card {
            background: white; border: 1px solid #f1f5f9; border-radius: 40px; padding: 45px;
            transition: 0.5s cubic-bezier(0.2, 1, 0.3, 1); height: 100%;
        }
        .grid-card:hover { transform: translateY(-15px); box-shadow: 0 40px 80px rgba(0,0,0,0.07); border-color: var(--p-blue); }

        .icon-circle {
            width: 80px; height: 80px; border-radius: 25px; display: flex; align-items: center;
            justify-content: center; font-size: 2rem; margin-bottom: 30px;
        }

        /* Stats Section */
        .stat-banner { background: var(--p-dark); border-radius: 50px; padding: 60px; color: white; margin-top: -60px; position: relative; z-index: 10; }
        .stat-item h2 { font-size: 3.5rem; font-weight: 800; background: linear-gradient(to bottom, #fff, #64748b); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

        /* Step Section Styling */
        .step-card { border: none; background: #fff; border-radius: 30px; padding: 40px; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.02); height: 100%; }
        .step-number { position: absolute; top: -10px; right: 20px; font-size: 5rem; font-weight: 900; color: var(--p-blue); opacity: 0.1; }

        /* Support Section */
        .support-box { background: var(--p-silver); border-radius: 40px; padding: 60px; }
        
        /* Floating CTA */
        .cta-gradient { background: linear-gradient(135deg, var(--p-dark) 0%, #1e293b 100%); border-radius: 50px; padding: 80px 40px; }

        @media (max-width: 768px) {
            .hero-bg { padding: 100px 0 60px 0; }
            .stat-banner { border-radius: 30px; padding: 30px; margin-top: -30px; }
        }
    </style>
</head>
<body>

    <section class="hero-bg">
        <div class="container text-center">
            <span class="section-tag"><?= $data['section_title'] ?></span>
            <h1 class="display-3 fw-800 mb-4 px-lg-5"><?= $data['main_heading'] ?></h1>
            <p class="lead text-muted mx-auto mb-5" style="max-width: 800px;">
                <?= nl2br(htmlspecialchars($data['content'])) ?>
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="#how-it-works" class="btn btn-primary rounded-pill px-5 py-3 fw-800 shadow-lg">How it Works</a>
                <a href="../index.php#contact" class="btn btn-outline-dark rounded-pill px-5 py-3 fw-800">Get Help</a>
            </div>
        </div>
    </section>

    <div class="container" id="details">
        <div class="stat-banner shadow-2xl">
            <div class="row text-center g-4">
                <div class="col-md-4 stat-item">
                    <h2><?= $data['total_users'] ?></h2>
                    <p class="fw-bold opacity-50">Verified Farmers</p>
                </div>
                <div class="col-md-4 stat-item">
                    <h2><?= $data['total_animals'] ?></h2>
                    <p class="fw-bold opacity-50">Animals Listed</p>
                </div>
                <div class="col-md-4 stat-item">
                    <h2>24/7</h2>
                    <p class="fw-bold opacity-50">Online Trading</p>
                </div>
            </div>
        </div>
    </div>

    <section class="py-5 mt-5" id="how-it-works">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="section-tag">Process</span>
                <h2 class="fw-800">How PashuMandi Works</h2>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="step-card border">
                        <span class="step-number">01</span>
                        <h4 class="fw-800 mb-3 text-primary">Account</h4>
                        <p class="text-muted mb-0"><?= nl2br(htmlspecialchars($data['step_1_desc'])) ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-card border">
                        <span class="step-number">02</span>
                        <h4 class="fw-800 mb-3 text-primary">Listing</h4>
                        <p class="text-muted mb-0"><?= nl2br(htmlspecialchars($data['step_2_desc'])) ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step-card border">
                        <span class="step-number">03</span>
                        <h4 class="fw-800 mb-3 text-primary">Trading</h4>
                        <p class="text-muted mb-0"><?= nl2br(htmlspecialchars($data['step_3_desc'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="pb-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="grid-card">
                        <div class="icon-circle bg-primary bg-opacity-10 text-primary">
                            <i class="fa-solid fa-bullseye"></i>
                        </div>
                        <h3 class="fw-800">Our Strategic Mission</h3>
                        <p class="text-muted mb-0 leading-relaxed"><?= nl2br(htmlspecialchars($data['mission'])) ?></p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="grid-card">
                        <div class="icon-circle bg-dark bg-opacity-10 text-dark">
                            <i class="fa-solid fa-lightbulb"></i>
                        </div>
                        <h3 class="fw-800">Our Future Vision</h3>
                        <p class="text-muted mb-0 leading-relaxed"><?= nl2br(htmlspecialchars($data['vision'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="support-box container my-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <span class="section-tag">Core Value</span>
                <h2 class="fw-800 mb-4">Why choose our digital marketplace?</h2>
                <p class="text-muted mb-4">We are breaking traditional barriers to make livestock trading safe and profitable for everyone.</p>
                
                <div class="d-flex mb-4 gap-3">
                    <div class="text-primary fs-3"><i class="fa-solid fa-circle-check"></i></div>
                    <div>
                        <h6 class="fw-800 mb-1"><?= htmlspecialchars($data['feature_1_title']) ?></h6>
                        <p class="small text-muted mb-0"><?= htmlspecialchars($data['feature_1_desc']) ?></p>
                    </div>
                </div>
                <div class="d-flex mb-4 gap-3">
                    <div class="text-primary fs-3"><i class="fa-solid fa-circle-check"></i></div>
                    <div>
                        <h6 class="fw-800 mb-1"><?= htmlspecialchars($data['feature_2_title']) ?></h6>
                        <p class="small text-muted mb-0"><?= htmlspecialchars($data['feature_2_desc']) ?></p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-7">
                <div class="row g-4">
                    <div class="col-6"><div class="p-4 bg-white rounded-4 shadow-sm text-center"><i class="fa-solid fa-shield-cat text-primary fs-1 mb-3"></i><h6 class="fw-800">Animal Health</h6><p class="small text-muted mb-0">Verified Badges</p></div></div>
                    <div class="col-6"><div class="p-4 bg-white rounded-4 shadow-sm text-center"><i class="fa-solid fa-truck-fast text-primary fs-1 mb-3"></i><h6 class="fw-800">Logistics</h6><p class="small text-muted mb-0">Safe Transport</p></div></div>
                    <div class="col-6"><div class="p-4 bg-white rounded-4 shadow-sm text-center"><i class="fa-solid fa-comments-dollar text-primary fs-1 mb-3"></i><h6 class="fw-800">Instant Chat</h6><p class="small text-muted mb-0">Direct Deals</p></div></div>
                    <div class="col-6"><div class="p-4 bg-white rounded-4 shadow-sm text-center"><i class="fa-solid fa-camera-retro text-primary fs-1 mb-3"></i><h6 class="fw-800">HD Visuals</h6><p class="small text-muted mb-0">Live Proof</p></div></div>
                </div>
            </div>
        </div>
    </section>

    <section class="container py-5 mb-5">
        <div class="cta-gradient text-center text-white">
            <h2 class="display-5 fw-800 mb-4">Ready to grow your livestock business?</h2>
            <p class="opacity-75 mb-5 mx-auto" style="max-width: 600px;">Join thousands of farmers across Pakistan and start trading with confidence.</p>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <a href="../register.php" class="btn btn-primary rounded-pill px-5 py-3 fw-800">Register Now</a>
                <a href="../index.php" class="btn btn-outline-light rounded-pill px-5 py-3 fw-800">Explore Market</a>
            </div>
        </div>
    </section>

    <div class="text-center py-4 text-muted small fw-600">
        &copy; 2026 PashuMandi Platform • Digitizing Agriculture
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>