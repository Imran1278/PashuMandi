<?php 
// Database connection
include 'db.php'; 

$f_query = mysqli_query($conn, "SELECT * FROM footer_settings WHERE id=1");
$f_data = mysqli_fetch_assoc($f_query);
?>

<style>
    /* Footer Main Container */
    .main-footer { 
        background: #fff; 
        padding: 90px 0 30px 0; 
        border-top: 1px solid #f1f5f9; 
        margin-top: 60px; 
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    
    .footer-logo { 
        font-size: 26px; 
        font-weight: 800; 
        color: #0f172a; 
        margin-bottom: 20px; 
        display: block; 
        text-decoration: none; 
    }
    
    .footer-text { 
        color: #64748b; 
        font-size: 14.5px; 
        line-height: 1.8; 
        margin-bottom: 25px; 
        font-weight: 500;
    }
    
    .footer-title { 
        font-weight: 800; 
        font-size: 16px; 
        margin-bottom: 25px; 
        color: #0f172a; 
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .footer-links { list-style: none; padding: 0; }
    .footer-links li { margin-bottom: 14px; }
    .footer-links a { 
        color: #64748b; 
        text-decoration: none; 
        font-size: 14.5px; 
        transition: 0.3s; 
        font-weight: 600; 
    }
    .footer-links a:hover { color: #0ea5e9; padding-left: 8px; }

    /* Social Icons Styling */
    .social-icons a { 
        width: 45px; 
        height: 45px; 
        background: #f8fafc; 
        color: #64748b; 
        display: inline-flex; 
        align-items: center; 
        justify-content: center; 
        border-radius: 14px; 
        margin-right: 12px; 
        transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
        text-decoration: none;
        border: 1px solid #f1f5f9;
    }
    .social-icons a:hover { 
        background: #0ea5e9; 
        color: #fff; 
        transform: translateY(-6px); 
        box-shadow: 0 10px 20px rgba(14, 165, 233, 0.2);
    }

    /* Bottom Bar */
    .bottom-bar { 
        border-top: 1px solid #f1f5f9; 
        padding-top: 30px; 
        margin-top: 60px; 
    }
    
    .copyright { 
        color: #94a3b8; 
        font-size: 13.5px; 
        font-weight: 600; 
    }

    /* Faded Admin Access Button */
    .admin-access {
        color: #cbd5e1; 
        font-size: 12.5px; 
        text-decoration: none; 
        transition: 0.3s; 
        opacity: 0.4; 
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .admin-access:hover { 
        opacity: 1; 
        color: #64748b; 
    }

    /* Newsletter Input */
    .footer-newsletter .form-control {
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        height: 55px;
        font-weight: 600;
    }
</style>

<footer class="main-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <a href="index.php" class="footer-logo">Pashu<span class="text-primary">Mandi</span></a>
                <p class="footer-text">
                    <?= htmlspecialchars($f_data['about_text']) ?>
                </p>
                <div class="social-icons">
                    <a href="<?= $f_data['facebook_url'] ?>" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="<?= $f_data['instagram_url'] ?>" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="<?= $f_data['twitter_url'] ?>" title="Twitter/X"><i class="fab fa-twitter"></i></a>
                    <a href="<?= $f_data['linkedin_url'] ?>" title="WhatsApp Group"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            
            <div class="col-lg-2 col-md-4 ps-lg-5">
                <h6 class="footer-title">Quick Links</h6>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="./user/animals.php">Browse Mandi</a></li>
                    <li><a href="index.php#about">Our Story</a></li>
                    <li><a href="index.php#contact">Support</a></li>
                </ul>
            </div>

            <div class="col-lg-2 col-md-4">
                    <h6 class="footer-title">Livestock</h6>
                <ul class="footer-links">
                    <li><a href="./user/animals.php?cat=Cows %26 Bulls">Cows & Bulls</a></li>
                    <li><a href="./user/animals.php?cat=Goats %26 Sheep">Goats & Sheep</a></li>
                    <li><a href="./user/animals.php?cat=Buffaloes">Buffaloes</a></li>
                    <li><a href="./user/animals.php?cat=Fodder %26 Feed">Fodder & Feed</a></li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-4 text-lg-end position-relative">
    <div class="nl-container" style="background: var(--p-blue); padding: 30px; border-radius: 30px; color: white; position: relative; overflow: hidden;">
        
        <div class="nl-content text-start">
            <span style="background: rgba(255,255,255,0.2); padding: 5px 12px; border-radius: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                NEWSLETTER
            </span>
            
            <h4 class="fw-800 mt-3 mb-1" style="line-height: 1.2;">Subscribe Our Newsletter</h4>
            <p class="small opacity-75 mb-4">Get the latest livestock rates & arrivals.</p>
            
            <form action="./user/subscribe_login.php" method="POST">
                <div class="input-group mb-2" style="background: white; padding: 5px; border-radius: 18px;">
                    <input type="email" name="emailSubscribe" class="form-control border-0 px-3 shadow-none" placeholder="Enter Email Address" required style="font-size: 14px;">
                    <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold" style="font-size: 13px;">
                        Sign Up <i class="fa fa-paper-plane ms-1"></i>
                    </button>
                </div>
            </form>
            <small class="opacity-50" style="font-size: 10px;">* We never spam your inbox.</small>
        </div>

        <?php if(!empty($f_settings['newsletter_img'])): ?>
            <img src="../uploads/<?= $f_settings['newsletter_img'] ?>" class="nl-floating-img" 
                 style="position: absolute; right: -20px; bottom: -20px; height: 120px; opacity: 0.3; pointer-events: none;">
        <?php endif; ?>
    </div>
</div>
        </div>

        <div class="bottom-bar d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="copyright">
                <?= htmlspecialchars($f_data['copyright_text']) ?>
            </div>
            <div>
                <a href="./admin/admin_login.php" target="_blank" class="admin-access">
                    <i class="fa-solid fa-lock"></i>
                    <span>ADMIN PORTAL</span>
                </a>
            </div>
        </div>
    </div>
</footer>