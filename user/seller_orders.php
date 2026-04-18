<?php 
include '../db.php';
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Security: Check if logged in
if(!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

$seller_id = $_SESSION['user_id'];

// 1. Status Update Logic
if(isset($_GET['action']) && isset($_GET['order_id'])) {
    $order_id = mysqli_real_escape_string($conn, $_GET['order_id']);
    $action = mysqli_real_escape_string($conn, $_GET['action']);
    
    $status = ($action == 'approve') ? "Approved" : "Rejected";
    $msg = ($action == 'approve') ? "Order Approved Successfully!" : "Order Rejected.";

    $update_query = "UPDATE orders SET order_status = '$status' WHERE id = '$order_id' AND seller_id = '$seller_id'";
    
    if(mysqli_query($conn, $update_query)) {
        echo "<script>alert('$msg'); window.location.href='seller_orders.php';</script>";
    }
}

// 2. Fetch Orders with Buyer & Product details
$sql = "SELECT o.*, a.title, a.image, u.full_name as buyer_name, u.phone as buyer_phone, u.profile_pic as buyer_img 
        FROM orders o 
        JOIN animals a ON o.product_id = a.id 
        JOIN users u ON o.user_id = u.id 
        WHERE o.seller_id = '$seller_id' 
        ORDER BY o.order_date DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Manager | PashuMandi</title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root { --p-teal: #14b8a6; --p-dark: #0f172a; --p-bg: #f1f5f9; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--p-bg); color: var(--p-dark); }
        
        /* Stats Cards */
        .stat-mini { background: white; border-radius: 20px; padding: 20px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
        .stat-icon { width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }

        /* Order Row Styling */
        .order-wrapper { background: white; border-radius: 25px; border: none; transition: 0.3s; margin-bottom: 15px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
        .order-wrapper:hover { transform: scale(1.01); box-shadow: 0 20px 40px rgba(0,0,0,0.05); }
        
        .product-preview { width: 80px; height: 80px; border-radius: 18px; object-fit: cover; }
        .buyer-avatar { width: 35px; height: 35px; border-radius: 10px; object-fit: cover; background: #eee; }

        .badge-status { padding: 8px 16px; border-radius: 100px; font-weight: 700; font-size: 11px; text-transform: uppercase; }
        .bg-pending { background: #fffbeb; color: #b45309; }
        .bg-approved { background: #f0fdf4; color: #15803d; }
        .bg-rejected { background: #fef2f2; color: #b91c1c; }
        
        .action-btn { border-radius: 14px; padding: 10px 20px; font-weight: 700; font-size: 0.85rem; transition: 0.3s; }
        .btn-confirm { background: var(--p-dark); color: white; border: none; }
        .btn-confirm:hover { background: var(--p-teal); }
        
        .table-head-text { font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row align-items-center mb-5">
        <div class="col-md-6">
            <h2 class="fw-800 mb-1">Sales <span class="text-teal">Dashboard</span></h2>
            <p class="text-muted fw-600">Track and manage your incoming livestock orders.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="../index.php" class="btn btn-white shadow-sm rounded-pill px-4 fw-bold border">
                <i class="fa fa-grid-2 me-2"></i> Dashboard
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-none d-md-flex row px-4 mb-3 table-head-text">
                <div class="col-md-4">Product Details</div>
                <div class="col-md-3">Buyer Information</div>
                <div class="col-md-2 text-center">Price & Status</div>
                <div class="col-md-3 text-end">Actions</div>
            </div>

            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($order = mysqli_fetch_assoc($result)): ?>
                    <div class="order-wrapper p-3 p-md-4">
                        <div class="row align-items-center">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="../uploads/<?= $order['image'] ?>" class="product-preview shadow-sm">
                                    <div>
                                        <h6 class="fw-800 mb-1 text-truncate" style="max-width: 200px;"><?= htmlspecialchars($order['title']) ?></h6>
                                        <div class="small text-muted fw-600">
                                            <i class="fa fa-calendar-alt me-1"></i> <?= date('d M, Y', strtotime($order['order_date'])) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-3 mb-3 mb-md-0">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="../uploads/<?= !empty($order['buyer_img']) ? $order['buyer_img'] : 'default_user.png' ?>" class="buyer-avatar">
                                    <div>
                                        <div class="fw-700 small"><?= htmlspecialchars($order['buyer_name']) ?></div>
                                        <a href="tel:<?= $order['buyer_phone'] ?>" class="text-decoration-none small fw-600 text-teal">
                                            <i class="fa fa-phone-alt me-1" style="font-size: 10px;"></i> <?= $order['buyer_phone'] ?>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 text-md-center mb-3 mb-md-0">
                                <div class="fw-800 text-dark mb-1">Rs. <?= number_format($order['total_price']) ?></div>
                                <span class="badge-status bg-<?= strtolower($order['order_status']) ?>">
                                    <i class="fa fa-circle me-1" style="font-size: 7px;"></i> <?= $order['order_status'] ?>
                                </span>
                            </div>

                            <div class="col-md-3 text-end">
                                <?php if($order['order_status'] == 'Pending'): ?>
                                    <div class="d-flex gap-2 justify-content-md-end">
                                        <a href="?action=approve&order_id=<?= $order['id'] ?>" class="action-btn btn-confirm text-decoration-none flex-grow-1 flex-md-grow-0 text-center">
                                            Approve
                                        </a>
                                        <a href="?action=reject&order_id=<?= $order['id'] ?>" class="action-btn btn-light border text-danger text-decoration-none">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small fw-700 px-3">
                                        <i class="fa fa-check-double me-1"></i> Processed
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-5 bg-white rounded-5 shadow-sm border mt-4">
                    <div class="mb-4">
                        <i class="fa-solid fa-receipt fa-4x opacity-10"></i>
                    </div>
                    <h5 class="fw-800 text-muted">No Sales Records Found</h5>
                    <p class="text-muted small">When buyers place orders for your livestock, they will appear here.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>