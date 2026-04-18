<?php 
include 'db.php'; 
// Fetching the latest contact settings
$query = mysqli_query($conn, "SELECT * FROM contact_settings WHERE id=1");
$data = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | PashuMandi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #0ea5e9; --dark: #0f172a; --bg-light: #f8fafc; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #fff; color: var(--dark); }
        
        /* Hero Section */
        .hero-contact { 
            background: radial-gradient(circle at top right, #f0f9ff, #fff);
            padding: 120px 0 80px 0; 
            border-radius: 0 0 60px 60px; 
            text-align: center; 
        }
        
        /* Contact Cards */
        .contact-card { 
            border-radius: 35px; padding: 40px; border: 1px solid #f1f5f9; 
            background: white; transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
            height: 100%; text-align: center;
        }
        .contact-card:hover { transform: translateY(-12px); box-shadow: 0 30px 60px rgba(0,0,0,0.05); border-color: var(--primary); }
        
        .icon-box { 
            width: 70px; height: 70px; background: var(--primary); color: white; 
            border-radius: 22px; display: flex; align-items: center; justify-content: center; 
            font-size: 1.8rem; margin: 0 auto 25px auto; 
        }

        /* Map & Info Box */
        .map-container { 
            border-radius: 40px; overflow: hidden; border: 8px solid white; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.08); height: 350px;
        }
        .map-container iframe { width: 100%; height: 100%; border: 0; }
        
        .info-glass { 
            background: var(--bg-light); border-radius: 30px; padding: 35px; 
            border: 1px solid #e2e8f0; 
        }

        /* Form Styling */
        .form-control { 
            border-radius: 18px; padding: 15px 20px; border: 1.5px solid #f1f5f9; 
            background: #fcfdfe; font-weight: 600;
        }
        .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1); }
        
        .social-btn {
            width: 50px; height: 50px; border-radius: 15px; display: inline-flex;
            align-items: center; justify-content: center; font-size: 1.3rem;
            margin-right: 10px; transition: 0.3s; color: white;
        }
        .social-btn:hover { transform: scale(1.1); color: white; }
    </style>
</head>
<body>

<section id="contact" class="hero-contact">
    <div class="container">
        <span class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 rounded-pill mb-3 fw-bold uppercase">Get In Touch</span>
        <h1 class="display-4 fw-800 mb-3">How can we <span class="text-primary">help you?</span></h1>
        <p class="text-muted mx-auto fw-600" style="max-width: 600px;">
            <?= htmlspecialchars($data['contact_tagline']) ?>
        </p>
    </div>
</section>

<div class="container" style="margin-top: -60px;">
    <div class="row g-4 justify-content-center">
        <div class="col-md-4">
            <div class="contact-card shadow-sm">
                <div class="icon-box"><i class="fa-solid fa-phone-volume"></i></div>
                <h5 class="fw-800 mb-2">Call Center</h5>
                <p class="text-muted small mb-3">Speak with our experts</p>
                <a href="tel:<?= $data['phone_number'] ?>" class="btn btn-light rounded-pill fw-800 px-4"><?= $data['phone_number'] ?></a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="contact-card shadow-sm">
                <div class="icon-box" style="background: #22c55e;"><i class="fa-brands fa-whatsapp"></i></div>
                <h5 class="fw-800 mb-2">WhatsApp Chat</h5>
                <p class="text-muted small mb-3">Average response: 5 mins</p>
                <a href="https://wa.me/<?= $data['whatsapp_number'] ?>" target="_blank" class="btn btn-success rounded-pill fw-800 px-4">Start Chat</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="contact-card shadow-sm">
                <div class="icon-box" style="background: var(--dark);"><i class="fa-solid fa-envelope-open-text"></i></div>
                <h5 class="fw-800 mb-2">Email Us</h5>
                <p class="text-muted small mb-3">For official inquiries</p>
                <a href="mailto:<?= $data['support_email'] ?>" class="btn btn-dark rounded-pill fw-800 px-4">Send Email</a>
            </div>
        </div>
    </div>
</div>

<section class="py-5 mt-5 mb-5">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <div class="mb-4">
                    <h2 class="fw-800 mb-3">Drop us a line</h2>
                    <p class="text-muted">Fill out the form below and we'll get back to you shortly.</p>
                </div>
                <form action="./user/process_contact.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small fw-bold mb-1">Full Name</label>
                            <input type="text" name="name" class="form-control" value="<?= isset($u['full_name']) ? htmlspecialchars($u['full_name']) : '' ?>" placeholder="John Doe" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold mb-1">Email Address</label>
                            <input type="email" name="email" class="form-control" value="<?= isset($u['email']) ? htmlspecialchars($u['email']) : '' ?>" placeholder="name@email.com" required>
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold mb-1">Subject</label>
                            <input type="text" name="subject" class="form-control" placeholder="Buying an animal...">
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold mb-1">Message</label>
                            <textarea name="message" class="form-control" rows="5" placeholder="Write your message here..." required></textarea>
                        </div>
                        <div class="col-12 pt-2">
                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 fw-800 shadow-lg w-100">
                                SEND MESSAGE <i class="fa-solid fa-paper-plane ms-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-lg-6">
                <div class="map-container mb-4">
                    <?php if(!empty($data['map_iframe'])): ?>
                        <?= $data['map_iframe'] ?>
                    <?php else: ?>
                        <div class="h-100 d-flex align-items-center justify-content-center bg-light text-muted">
                            <p><i class="fa-solid fa-map-pin me-2"></i>Map location not set.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="info-glass">
                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="text-primary fs-4"><i class="fa-solid fa-location-dot"></i></div>
                        <div>
                            <h6 class="fw-800 mb-1">Main Office</h6>
                            <p class="text-muted small mb-0"><?= nl2br(htmlspecialchars($data['office_address'])) ?></p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center gap-3">
                        <h6 class="fw-800 mb-0 me-2">Follow Us:</h6>
                        <a href="<?= $data['fb_link'] ?>" class="social-btn" style="background: #1877F2;"><i class="fab fa-facebook-f"></i></a>
                        <a href="<?= $data['insta_link'] ?>" class="social-btn" style="background: #E4405F;"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>