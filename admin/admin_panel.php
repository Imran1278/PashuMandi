<?php 
    include '../db.php';
    if (session_status() === PHP_SESSION_NONE) { session_start(); }
    
    // // Agar session variable set nahi hai, toh wapas login par bhej do
    // if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) 
    // {
    //     header("Location: admin_login.php");
    //     exit();
    // }

   

    // --- STAGE 2: DATA AGGREGATION ---
    // Using @ to suppress errors if tables don't exist yet
    $total_users = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users"));
    $total_products = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM animals"));
    
    // Orders aur Messages counts (Check if tables exist)
    $orders_q = mysqli_query($conn, "SHOW TABLES LIKE 'orders'");
    $total_orders = (mysqli_num_rows($orders_q) > 0) ? mysqli_num_rows(mysqli_query($conn, "SELECT id FROM orders")) : 0;
    
    $msg_q = mysqli_query($conn, "SHOW TABLES LIKE 'messages'");
    $total_messages = (mysqli_num_rows($msg_q) > 0) ? mysqli_num_rows(mysqli_query($conn, "SELECT id FROM messages")) : 0;
    $total_notifications = (mysqli_num_rows($msg_q) > 0) ? mysqli_num_rows(mysqli_query($conn, "SELECT id FROM notifications")) : 0;

    // Fetching 5 Recent Listings for the table
    $recent_listings = mysqli_query($conn, "SELECT animals.*, users.full_name as seller 
                                        FROM animals 
                                        JOIN users ON animals.user_id = users.id 
                                        ORDER BY animals.id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | PashuMandi</title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { 
            --sidebar-width: 280px;
            --primary-bg: #0f172a; 
            --accent: #0ea5e9; 
            --card-border: rgba(226, 232, 240, 0.8);
        }
        
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f1f5f9; color: #334155; }

        /* Sidebar Styling */
        .admin-sidebar { 
            width: var(--sidebar-width); 
            height: 100vh; 
            background: var(--primary-bg); 
            position: fixed; 
            left: 0; top: 0; 
            padding: 20px;
            color: #94a3b8;
            overflow-y: auto;
        }
        
        .brand-logo { color: white; font-weight: 800; font-size: 1.5rem; padding: 20px 10px; border-bottom: 1px solid rgba(255,255,255,0.05); margin-bottom: 30px; }
        .brand-logo span { color: var(--accent); }

        .nav-link { 
            display: flex; align-items: center; padding: 12px 15px; 
            color: #94a3b8; text-decoration: none; border-radius: 12px;
            margin-bottom: 5px; font-weight: 600; transition: 0.3s;
        }
        .nav-link i { width: 25px; font-size: 1.1rem; }
        .nav-link:hover, .nav-link.active { background: rgba(14, 165, 233, 0.1); color: white; }
        .nav-link.active i { color: var(--accent); }

        .nav-header { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1.2px; font-weight: 800; color: #475569; margin: 25px 0 10px 15px; }

        /* Main Content */
        .content-body { margin-left: var(--sidebar-width); padding: 40px; }
        
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        
        /* Dashboard Cards */
        .stat-card { 
            background: white; border-radius: 24px; padding: 24px; 
            border: 1px solid var(--card-border); transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.04); }
        .icon-circle { width: 54px; height: 54px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }

        /* Custom Table */
        .custom-table { background: white; border-radius: 24px; overflow: hidden; border: 1px solid var(--card-border); }
        .custom-table thead { background: #f8fafc; }
        .custom-table th { font-weight: 700; color: #64748b; border: none; padding: 20px; }
        .custom-table td { padding: 20px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; font-weight: 600; }
        
        .animal-img { width: 45px; height: 45px; border-radius: 12px; object-fit: cover; }
        .badge-status { padding: 6px 12px; border-radius: 10px; font-size: 0.75rem; font-weight: 800; }

        .btn-action { width: 35px; height: 35px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; transition: 0.2s; }
        
        @media (max-width: 992px) {
            .admin-sidebar { left: -100%; }
            .content-body { margin-left: 0; }
        }
    </style>
</head>
<body>

    <aside class="admin-sidebar">
        <div class="brand-logo">
            PASHU<span>MANDI</span>
        </div>
        
        <div class="nav-header">Overview</div>
        <a href="admin_panel.php" class="nav-link active"><i class="fa-solid fa-house-chimney"></i> Dashboard</a>
        <a href="manage_messages.php" class="nav-link"><i class="fa-solid fa-comment-dots"></i> Inquiries <span class="badge bg-danger ms-auto rounded-pill"><?= $total_messages ?></span></a>
        <a href="manage_notifications.php" class="nav-link"><i class="fa-solid fa-bell"></i> Notifications <span class="badge bg-danger ms-auto rounded-pill"><?= $total_notifications ?></span></a>

        <div class="nav-header">Frontend Control</div>
        <a href="manage_header.php" class="nav-link"><i class="fa-solid fa-window-maximize"></i> Navbar & Logo</a>
        <a href="manage_hero.php" class="nav-link"><i class="fa-solid fa-images"></i> Hero Sliders</a>
        <a href="manage_about.php" class="nav-link"><i class="fa-solid fa-file-lines"></i> About Content</a>
        <a href="manage_learnmore.php" class="nav-link"><i class="fas fa-list"></i> Details Content</a>
        <a href="manage_contact.php" class="nav-link"><i class="fa-solid fa-address-book"></i> Contact Information</a>
        <a href="manage_footer.php" class="nav-link"><i class="fa-solid fa-file-code"></i> Footer Management</a>

        <div class="nav-header">Management</div>
        <a href="manage_users.php" class="nav-link"><i class="fa-solid fa-user-gear"></i> Users Management</a>
        <a href="manage_products.php" class="nav-link"><i class="fa-solid fa-cow"></i> Animal Listings</a>
        <a href="manage_orders.php" class="nav-link"><i class="fa-solid fa-receipt"></i> Orders Tracking</a>

    </aside>

    <main class="content-body">
        
        <header class="top-bar">
            <div>
                <h2 class="fw-800 mb-1">PashuMandi Administration</h2>
                <p class="text-muted small fw-600 mb-0">
                    Welcome back, 
                    <span class="text-primary">
                        <?php echo isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : 'Super Admin'; ?>
                    </span>
                </p>
            </div>
            <div class="d-flex gap-3 align-items-center">
                <a href="../index.php" target="_blank" class="btn btn-white border rounded-pill px-4 fw-bold shadow-sm">
                    <i class="fa fa-eye me-2 text-primary"></i> Live Site
                </a>
                
                <a href="admin_logout.php" class="btn btn-danger border rounded-pill px-4 fw-bold shadow-sm">
                    <i class="fa fa-sign-out-alt me-2"></i> Logout
                </a>
            </div>
        </header>

        <div class="row g-4 mb-5">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle bg-primary bg-opacity-10 text-primary"><i class="fa-solid fa-users"></i></div>
                        <span class="text-success small fw-800">+12% <i class="fa fa-arrow-up"></i></span>
                    </div>
                    <h3 class="fw-800 mb-1"><?= number_format($total_users) ?></h3>
                    <p class="text-muted small fw-700 mb-0">Total Community Users</p>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle bg-warning bg-opacity-10 text-warning"><i class="fa-solid fa-cow"></i></div>
                        <span class="text-muted small fw-800">Live Ads</span>
                    </div>
                    <h3 class="fw-800 mb-1"><?= number_format($total_products) ?></h3>
                    <p class="text-muted small fw-700 mb-0">Active Livestock Ads</p>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle bg-success bg-opacity-10 text-success"><i class="fa-solid fa-bag-shopping"></i></div>
                        <span class="text-success small fw-800">Processed</span>
                    </div>
                    <h3 class="fw-800 mb-1"><?= number_format($total_orders) ?></h3>
                    <p class="text-muted small fw-700 mb-0">Successful Mandi Orders</p>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle bg-info bg-opacity-10 text-info"><i class="fa-solid fa-headset"></i></div>
                    </div>
                    <h3 class="fw-800 mb-1"><?= $total_messages ?></h3>
                    <p class="text-muted small fw-700 mb-0">Pending User Inquiries</p>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between mb-3">
                        <div class="icon-circle bg-info bg-opacity-10 text-info"><i class="fa-solid fa-bell"></i></div>
                    </div>
                    <h3 class="fw-800 mb-1"><?= $total_notifications ?></h3>
                    <p class="text-muted small fw-700 mb-0">Notifications Check</p>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-800 mb-0">Recent Livestock Submissions</h4>
                <a href="manage_products.php" class="btn btn-sm btn-light fw-800 px-3 border rounded-3">View All Listings</a>
            </div>

            <div class="custom-table shadow-sm">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Animal Details</th>
                            <th>Seller Name</th>
                            <th>Location</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($recent_listings) > 0): ?>
                            <?php while($row = mysqli_fetch_assoc($recent_listings)): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="../uploads/<?= $row['image'] ?>" class="animal-img shadow-sm">
                                        <div>
                                            <div class="fw-800 text-dark"><?= htmlspecialchars($row['title']) ?></div>
                                            <div class="small text-muted"><?= $row['category'] ?> • <?= $row['brand'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="fw-700"><?= htmlspecialchars($row['seller']) ?></span></td>
                                <td><i class="fa-solid fa-location-dot me-1 text-danger small"></i> <?= $row['location'] ?></td>
                                <td class="text-primary fw-800">Rs. <?= number_format($row['price']) ?></td>
                                <td><span class="badge-status bg-success bg-opacity-10 text-success">Approved</span></td>
                                <td>
                                    <a href="edit_product.php?id=<?= $row['id'] ?>" class="btn-action bg-light text-dark"><i class="fa-solid fa-pen-to-square"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="60" class="opacity-20 mb-3">
                                    <p class="text-muted small fw-600">No recent listings found in the database.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>