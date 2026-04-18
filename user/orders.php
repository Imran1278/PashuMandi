<?php 
include '../db.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$u_id = $_SESSION['user_id'];

// Fetch Purchases (Items I bought)
$purchases_sql = "SELECT o.*, a.title, a.image, s.full_name as seller_name 
                  FROM orders o 
                  JOIN animals a ON o.product_id = a.id 
                  JOIN users s ON o.seller_id = s.id
                  WHERE o.user_id = '$u_id' ORDER BY o.order_date DESC";
$my_purchases = mysqli_query($conn, $purchases_sql);

// Fetch Sales (My items bought by others)
$sales_sql = "SELECT o.*, a.title, a.image, b.full_name as buyer_name 
              FROM orders o 
              JOIN animals a ON o.product_id = a.id 
              JOIN users b ON o.user_id = b.id
              WHERE o.seller_id = '$u_id' ORDER BY o.order_date DESC";
$my_sales = mysqli_query($conn, $sales_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History | PashuMandi</title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root { 
            --p-primary: #6366f1; 
            --p-success: #10b981; 
            --p-bg: #f8fafc; 
            --p-dark: #0f172a; 
            --p-card-shadow: 0 10px 30px rgba(0,0,0,0.04);
        }
        
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--p-bg); color: var(--p-dark); }
        
        /* Dashboard Header */
        .dash-header { background: white; border-bottom: 1px solid #e2e8f0; padding: 40px 0; margin-bottom: 40px; }
        
        /* Modern Tabs */
        .nav-pills-custom { background: #f1f5f9; padding: 6px; border-radius: 20px; display: inline-flex; }
        .nav-pills-custom .nav-link { border-radius: 15px; padding: 12px 30px; font-weight: 700; color: #64748b; border: none; transition: 0.3s; }
        .nav-pills-custom .nav-link.active { background: white; color: var(--p-primary); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }

        /* Order Rows */
        .activity-card { background: white; border-radius: 24px; border: 1px solid #f1f5f9; padding: 20px; margin-bottom: 16px; transition: 0.4s; }
        .activity-card:hover { transform: translateY(-3px); box-shadow: 0 20px 40px rgba(0,0,0,0.06); border-color: var(--p-primary); }
        
        .item-thumb { width: 70px; height: 70px; border-radius: 18px; object-fit: cover; }
        
        .order-title { font-weight: 800; font-size: 1.1rem; color: var(--p-dark); margin-bottom: 4px; }
        .order-subtitle { font-size: 0.85rem; color: #64748b; font-weight: 600; }
        
        /* Status Badges */
        .badge-status { padding: 8px 16px; border-radius: 12px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; }
        .status-completed { background: #ecfdf5; color: #059669; }
        .status-pending { background: #fffbeb; color: #d97706; }
        .status-cancelled { background: #fef2f2; color: #dc2626; }

        .price-label { font-size: 1.2rem; font-weight: 800; color: var(--p-dark); }
        .text-income { color: var(--p-success) !important; }

        .empty-illustration { padding: 60px 0; opacity: 0.6; }

        @media (max-width: 768px) {
            .activity-card { text-align: center; flex-direction: column; gap: 15px; }
            .activity-card .text-end { text-align: center !important; }
        }
    </style>
</head>
<body>

<div class="dash-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-7">
                <h2 class="fw-800 mb-1">Market Activity</h2>
                <p class="text-muted mb-0">Manage your livestock purchases and recent sales in one place.</p>
            </div>
            <div class="col-md-5 text-md-end mt-3 mt-md-0">
                <div class="nav nav-pills nav-pills-custom" id="v-pills-tab" role="tablist">
                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#purchases" type="button">
                        <i class="fa-solid fa-bag-shopping me-2"></i>Purchases
                    </button>
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#sales" type="button">
                        <i class="fa-solid fa-chart-line me-2"></i>Sales
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    <div class="tab-content" id="v-pills-tabContent">
        
        <div class="tab-pane fade show active" id="purchases" role="tabpanel">
            <?php if(mysqli_num_rows($my_purchases) > 0): ?>
                <div class="row justify-content-center">
                    <div class="col-xl-10">
                        <?php while($row = mysqli_fetch_assoc($my_purchases)): ?>
                        <div class="activity-card d-flex align-items-center justify-content-between flex-wrap">
                            <div class="d-flex align-items-center gap-3">
                                <img src="../uploads/<?= !empty($row['image']) ? $row['image'] : 'default.jpg' ?>" class="item-thumb">
                                <div>
                                    <div class="order-title"><?= htmlspecialchars($row['title']) ?></div>
                                    <div class="order-subtitle">
                                        <span class="me-2"><i class="fa fa-user me-1"></i> Seller: <?= htmlspecialchars($row['seller_name']) ?></span>
                                        <span><i class="fa fa-calendar me-1"></i> <?= date('M d, Y', strtotime($row['order_date'])) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end mt-2 mt-md-0">
                                <div class="price-label mb-1">Rs. <?= number_format($row['total_price']) ?></div>
                                <span class="badge-status status-<?= strtolower($row['order_status']) ?>">
                                    <?= $row['order_status'] ?>
                                </span>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center empty-illustration">
                    <i class="fa-solid fa-shopping-basket fa-4x mb-3 text-muted"></i>
                    <h5 class="fw-bold">No Purchases Found</h5>
                    <p>When you buy an animal, it will appear here.</p>
                    <a href="../index.php" class="btn btn-primary rounded-pill px-4 mt-2">Explore Market</a>
                </div>
            <?php endif; ?>
        </div>

        <div class="tab-pane fade" id="sales" role="tabpanel">
            <?php if(mysqli_num_rows($my_sales) > 0): ?>
                <div class="row justify-content-center">
                    <div class="col-xl-10">
                        <?php while($row = mysqli_fetch_assoc($my_sales)): ?>
                        <div class="activity-card d-flex align-items-center justify-content-between flex-wrap">
                            <div class="d-flex align-items-center gap-3">
                                <img src="../uploads/<?= !empty($row['image']) ? $row['image'] : 'default.jpg' ?>" class="item-thumb">
                                <div>
                                    <div class="order-title"><?= htmlspecialchars($row['title']) ?></div>
                                    <div class="order-subtitle">
                                        <span class="text-primary me-2 fw-bold">Buyer: <?= htmlspecialchars($row['buyer_name']) ?></span>
                                        <span><i class="fa fa-clock me-1"></i> <?= date('d M, H:i', strtotime($row['order_date'])) ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end mt-2 mt-md-0">
                                <div class="price-label text-income mb-1">+Rs. <?= number_format($row['total_price']) ?></div>
                                <span class="badge-status status-<?= strtolower($row['order_status']) ?>">
                                    <?= $row['order_status'] ?>
                                </span>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center empty-illustration">
                    <i class="fa-solid fa-vault fa-4x mb-3 text-muted"></i>
                    <h5 class="fw-bold">No Sales Yet</h5>
                    <p>List your animals for sale to see transactions here.</p>
                    <a href="add_product.php" class="btn btn-success rounded-pill px-4 mt-2">Start Selling</a>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>