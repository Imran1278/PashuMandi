<?php 
include '../db.php'; 
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// 1. Get Product ID from URL
if(!isset($_GET['id']) || empty($_GET['id'])) { 
    header("Location: ../index.php"); 
    exit(); 
}

$p_id = mysqli_real_escape_string($conn, $_GET['id']);
$u_id = $_SESSION['user_id'];

// 2. Fetch User Data
$user_res = mysqli_query($conn, "SELECT * FROM users WHERE id = '$u_id'");
$user_data = mysqli_fetch_assoc($user_res);

// 3. Fetch Animal & Seller Details
$sql = "SELECT a.*, u.full_name as seller_name, u.id as s_id 
        FROM animals a 
        JOIN users u ON a.user_id = u.id 
        WHERE a.id = '$p_id'";
$result = mysqli_query($conn, $sql);
$animal = mysqli_fetch_assoc($result);

if(!$animal) { 
    die("<div class='container mt-5 alert alert-danger'>Item not found! It might have been sold.</div>"); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | PashuMandi</title>
    <link rel="icon" href="../pics/icon.png">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root { 
            --p-navy: #0f172a; 
            --p-teal: #14b8a6; 
            --p-bg: #f8fafc; 
            --p-blue: #2563eb; 
        }
        
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--p-bg); color: var(--p-navy); }
        
        /* Premium Container */
        .checkout-wrapper { background: white; border-radius: 40px; overflow: hidden; box-shadow: 0 40px 100px -20px rgba(0,0,0,0.08); border: 1px solid rgba(0,0,0,0.05); }
        
        /* Stepper Styling */
        .checkout-stepper { background: #f1f5f9; padding: 20px 40px; display: flex; justify-content: center; gap: 40px; border-bottom: 1px solid #e2e8f0; }
        .step { display: flex; align-items: center; gap: 10px; color: #94a3b8; font-weight: 700; font-size: 14px; }
        .step.active { color: var(--p-blue); }
        .step-num { width: 28px; height: 28px; border-radius: 50%; background: #cbd5e1; color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; }
        .step.active .step-num { background: var(--p-blue); box-shadow: 0 0 0 5px rgba(37, 99, 235, 0.1); }

        /* Form Styling */
        .form-section { padding: 50px; }
        .input-label { font-weight: 800; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin-bottom: 10px; display: block; }
        .custom-input { border-radius: 18px; padding: 16px 20px; border: 2px solid #f1f5f9; background: #f8fafc; font-weight: 600; transition: 0.3s; }
        .custom-input:focus { border-color: var(--p-blue); background: white; box-shadow: none; }
        
        /* Order Summary Sticky */
        .summary-panel { background: #f8fafc; padding: 40px; border-left: 1px solid #e2e8f0; height: 100%; }
        .animal-preview { width: 100%; height: 200px; object-fit: cover; border-radius: 25px; margin-bottom: 20px; box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        
        .price-row { display: flex; justify-content: space-between; margin-bottom: 15px; font-weight: 600; color: #475569; }
        .total-row { display: flex; justify-content: space-between; margin-top: 25px; padding-top: 25px; border-top: 2px dashed #e2e8f0; }
        .grand-total { font-size: 32px; font-weight: 800; color: var(--p-navy); letter-spacing: -1px; }

        .btn-checkout { background: var(--p-navy); color: white; border-radius: 20px; padding: 20px; font-weight: 800; width: 100%; border: none; transition: 0.4s; font-size: 16px; margin-top: 20px; }
        .btn-checkout:hover { background: var(--p-teal); transform: translateY(-5px); box-shadow: 0 20px 40px rgba(20, 184, 166, 0.2); }

        .badge-verified { background: #dcfce7; color: #15803d; padding: 4px 12px; border-radius: 8px; font-size: 11px; font-weight: 800; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-xl-11">
            
            <div class="d-flex justify-content-between align-items-center mb-5">
                <div>
                    <h2 class="fw-800 mb-0">Secure <span class="text-primary">Checkout</span></h2>
                    <p class="text-muted mb-0">Complete your details to finalize the purchase.</p>
                </div>
                <a href="product_details.php?id=<?= $p_id ?>" class="btn btn-white border-0 shadow-sm rounded-pill px-4 fw-bold">
                    <i class="fa fa-times me-2"></i> Cancel
                </a>
            </div>

            <div class="checkout-wrapper">
                <div class="checkout-stepper d-none d-md-flex">
                    <div class="step"><div class="step-num">1</div> Selection</div>
                    <div class="step active"><div class="step-num">2</div> Shipping Info</div>
                    <div class="step"><div class="step-num">3</div> Confirmation</div>
                </div>

                <form action="process_order.php" method="POST">
                    <div class="row g-0">
                        <div class="col-lg-7">
                            <div class="form-section">
                                <h4 class="fw-800 mb-4">Delivery Details</h4>
                                
                                <div class="row">
                                    <div class="col-md-12 mb-4">
                                        <label class="input-label">Full Name</label>
                                        <input type="text" class="form-control custom-input" value="<?= htmlspecialchars($user_data['full_name'] ?? '') ?>" placeholder="Your Full Name" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-4">
                                        <label class="input-label">Mobile Number</label>
                                        <input type="text" name="buyer_phone" class="form-control custom-input" value="<?= $user_data['phone'] ?? '' ?>" placeholder="03xx xxxxxxx" required>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label class="input-label">City</label>
                                        <input type="text" name="buyer_city" class="form-control custom-input" value="<?= $user_data['city'] ?? '' ?>" placeholder="Your City" required>
                                    </div>

                                    <div class="col-md-12 mb-4">
                                        <label class="input-label">Shipping Address</label>
                                        <textarea name="buyer_address" class="form-control custom-input" rows="3" placeholder="Street, House No, Landmark..." required></textarea>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="alert bg-primary bg-opacity-10 border-0 rounded-4 p-4">
                                            <div class="d-flex gap-3">
                                                <i class="fa-solid fa-truck-clock text-primary fs-4"></i>
                                                <div>
                                                    <h6 class="fw-bold text-primary mb-1">Standard Delivery</h6>
                                                    <p class="small mb-0 text-muted fw-600">The seller typically contacts within 24 hours to arrange transportation of the livestock.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="summary-panel">
                                <h4 class="fw-800 mb-4">Purchase Summary</h4>
                                
                                <?php 
                                    $imgs = explode(',', $animal['image']);
                                    $thumb = !empty($imgs[0]) ? $imgs[0] : 'default.jpg';
                                ?>
                                <img src="../uploads/<?= $thumb ?>" class="animal-preview">
                                
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="fw-800 mb-0"><?= htmlspecialchars($animal['title']) ?></h5>
                                        <span class="badge-verified">Verified Listing</span>
                                    </div>
                                    <p class="text-muted small">Seller: <?= $animal['seller_name'] ?></p>
                                </div>

                                <div class="price-calculations">
                                    <div class="price-row">
                                        <span>Animal Price</span>
                                        <span>Rs. <?= number_format($animal['price']) ?></span>
                                    </div>
                                    <div class="price-row">
                                        <span>Service Fee</span>
                                        <span class="text-success">Rs. 0 (Free)</span>
                                    </div>
                                    <div class="price-row">
                                        <span>Transport</span>
                                        <span class="small text-muted">Negotiable</span>
                                    </div>

                                    <div class="total-row">
                                        <span class="fw-800 text-uppercase">Total Amount</span>
                                        <div class="text-end">
                                            <div class="grand-total">Rs. <?= number_format($animal['price']) ?></div>
                                            <small class="text-muted fw-bold">Inclusive of all taxes</small>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" name="product_id" value="<?= $animal['id'] ?>">
                                <input type="hidden" name="seller_id" value="<?= $animal['s_id'] ?>">
                                <input type="hidden" name="total_price" value="<?= $animal['price'] ?>">

                                <button type="submit" class="btn-checkout">
                                    <i class="fa fa-lock me-2"></i> PLACE SECURE ORDER
                                </button>

                                <div class="mt-4 text-center">
                                    <div class="d-flex justify-content-center gap-3 opacity-25 grayscale mb-2">
                                        <i class="fab fa-cc-visa fa-2x"></i>
                                        <i class="fab fa-cc-mastercard fa-2x"></i>
                                        <i class="fa fa-money-bill-transfer fa-2x"></i>
                                    </div>
                                    <p class="text-uppercase fw-bold text-muted small mb-0" style="font-size: 9px; letter-spacing: 1px;">
                                        Powered by PashuMandi Security
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="row mt-5 text-center g-4">
                <div class="col-md-4">
                    <i class="fa fa-shield-heart text-primary fs-3 mb-2"></i>
                    <h6 class="fw-800">100% Secure</h6>
                    <p class="small text-muted">Your money is safe with us until you get the delivery.</p>
                </div>
                <div class="col-md-4">
                    <i class="fa fa-truck text-primary fs-3 mb-2"></i>
                    <h6 class="fw-800">Verified Sellers</h6>
                    <p class="small text-muted">We only list livestock from trusted and verified farmers.</p>
                </div>
                <div class="col-md-4">
                    <i class="fa fa-headset text-primary fs-3 mb-2"></i>
                    <h6 class="fw-800">24/7 Support</h6>
                    <p class="small text-muted">Our team is here to help you with transportation and health checks.</p>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>