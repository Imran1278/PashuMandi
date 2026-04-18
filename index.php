<?php
    include 'db.php';
    if (session_status() === PHP_SESSION_NONE) { session_start(); }

    // Optional: Global Alert Logic (for login success/notifications)
    $alert = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PashuMandi</title>
    <link rel="icon" href="./pics/icon.png">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        :root {
            --pm-brand: #0f766e;
            --pm-secondary: #14b8a6;
            --pm-dark: #0f172a;
            --pm-light: #f8fafc;
            --pm-accent: #38bdf8;
            --pm-glass: rgba(255, 255, 255, 0.9);
            --pm-shadow: 0 20px 25px -5px rgba(0,0,0,0.05);
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #ffffff; 
            color: var(--pm-dark); 
            overflow-x: hidden;
            scroll-behavior: smooth; 
        }

        /* Smooth Section Transitions */
        .section-padding { padding: 80px 0; }
        
        /* Modern Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: var(--pm-brand); border-radius: 10px; }

        /* Global Button Style */
        .btn-premium { 
            padding: 12px 32px; 
            border-radius: 16px; 
            font-weight: 700; 
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
            border: none;
        }
        .btn-premium:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(15, 118, 110, 0.2); }

        /* Navigation Glassmorphism (Optional for header) */
        .sticky-nav {
            position: sticky; top: 0; z-index: 1000;
            background: var(--pm-glass); backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        /* Alert Styling */
        .floating-alert {
            position: fixed; bottom: 20px; right: 20px; z-index: 9999;
            animation: slideUp 0.5s ease;
        }
        @keyframes slideUp { from { transform: translateY(100px); } to { transform: translateY(0); } }
    </style>
</head>
<body>

    <?php if($alert): ?>
        <div class="floating-alert alert alert-dark bg-dark text-white border-0 shadow-lg rounded-4 py-3 px-4">
            <i class="fa fa-bell text-info me-2"></i> <?= htmlspecialchars($alert) ?>
            <button type="button" class="btn-close btn-close-white ms-3" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php 
        // Component-Based Loading
        // Tip: Ensure these files exist in your 'sections/' folder
        include 'sections/header.php'; 
        
        echo '<main>'; // Wrapper for SEO
            include 'sections/hero.php';
            include 'sections/about.php';
            
            // Adding a 'Container' for products to keep layout consistent
            echo '<section id="products" class="section-padding bg-light">';
                include 'sections/products.php';
            echo '</section>';
            
            include 'sections/contact.php';
        echo '</main>';

        include 'sections/footer.php';
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS Animations
        AOS.init({
            duration: 1000,
            once: true,
            easing: 'ease-in-out'
        });

        // Navbar Scroll Effect
        window.addEventListener('scroll', function() {
            const header = document.querySelector('nav'); // Replace with your header class/tag
            if (window.scrollY > 50) {
                header?.classList.add('sticky-nav');
            } else {
                header?.classList.remove('sticky-nav');
            }
        });


        
    </script>
</body>
</html>