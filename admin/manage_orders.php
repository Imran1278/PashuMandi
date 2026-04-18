<?php 
include '../db.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Handle Status Update
if(isset($_POST['update_status'])) {
    $order_id = mysqli_real_escape_string($conn, $_POST['order_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);
    
    mysqli_query($conn, "UPDATE orders SET order_status='$new_status' WHERE id='$order_id'");
    header("Location: manage_orders.php?msg=updated");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Ledger | PashuMandi Admin</title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { 
            --p-blue: #3b82f6; 
            --p-dark: #0f172a; 
            --p-bg: #f1f5f9;
        }
        body { background: var(--p-bg); font-family: 'Plus Jakarta Sans', sans-serif; color: var(--p-dark); }
        
        .header-section { margin-bottom: 40px; }
        .ledger-card { 
            background: white; border-radius: 35px; border: none; 
            box-shadow: 0 20px 60px rgba(0,0,0,0.02); overflow: hidden;
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .table thead th { 
            background: #fafafa; text-transform: uppercase; font-size: 0.75rem; 
            letter-spacing: 1.2px; font-weight: 800; color: #94a3b8; padding: 25px 20px;
            border-bottom: 2px solid #f1f5f9;
        }
        
        .table tbody tr { transition: 0.3s; }
        .table tbody tr:hover { background-color: #f8fafc; }
        .table tbody td { padding: 22px 20px; vertical-align: middle; border-bottom: 1px solid #f1f5f9; }
        
        /* Status UI */
        .badge-status { 
            padding: 8px 16px; border-radius: 100px; font-size: 0.7rem; 
            font-weight: 800; display: inline-flex; align-items: center; gap: 6px;
        }
        .status-pending { background: #fffbeb; color: #b45309; }
        .status-completed { background: #f0fdf4; color: #15803d; }
        .status-cancelled { background: #fef2f2; color: #b91c1c; }

        .party-tag { 
            display: flex; align-items: center; gap: 8px; 
            background: #f8fafc; padding: 6px 12px; border-radius: 12px;
            margin-bottom: 6px; border: 1px solid #e2e8f0;
        }
        .party-tag i { font-size: 0.8rem; }
        
        .trx-id { 
            font-family: 'Monaco', monospace; background: #f1f5f9; 
            padding: 4px 10px; border-radius: 8px; font-size: 0.8rem; color: #475569;
        }

        .price-text { font-weight: 800; color: var(--p-dark); font-size: 1.05rem; }
        
        .status-select { 
            font-size: 0.8rem; font-weight: 700; border-radius: 12px; 
            padding: 8px 12px; border: 2px solid #f1f5f9; background: white;
            cursor: pointer; transition: 0.3s;
        }
        .status-select:focus { border-color: var(--p-blue); outline: none; }

        .btn-dash {
            background: white; border: 2px solid #e2e8f0; border-radius: 15px;
            padding: 10px 20px; font-weight: 700; transition: 0.3s;
        }
        .btn-dash:hover { background: var(--p-dark); color: white; border-color: var(--p-dark); }
    </style>
</head>
<body>

    <div class="container py-5">
        
        <div class="header-section d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-800 mb-1">Sales <span class="text-primary">Ledger</span></h2>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-dark rounded-pill">Admin Only</span>
                    <p class="text-muted small fw-600 mb-0">Livestock Transaction Management System</p>
                </div>
            </div>
            <a href="admin_panel.php" class="btn btn-dash text-decoration-none text-dark">
                <i class="fa-solid fa-house-chimney me-2"></i> Dashboard
            </a>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 fw-bold animate__animated animate__fadeIn">
                <i class="fa-solid fa-check-circle me-2"></i> Status Updated Successfully!
            </div>
        <?php endif; ?>

        <div class="ledger-card">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Trx ID</th>
                            <th>Livestock Item</th>
                            <th>Parties Involved</th>
                            <th>Total Amount</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql = "SELECT o.*, a.title as animal_name, b.full_name as buyer_name, s.full_name as seller_name 
                                FROM orders o 
                                JOIN animals a ON o.product_id = a.id 
                                JOIN users b ON o.user_id = b.id 
                                JOIN users s ON o.seller_id = s.id 
                                ORDER BY o.id DESC";
                                
                        $orders = mysqli_query($conn, $sql);
                        
                        if(mysqli_num_rows($orders) > 0):
                            while($o = mysqli_fetch_assoc($orders)): 
                                $status_class = 'status-' . strtolower($o['order_status']);
                        ?>
                        <tr>
                            <td class="ps-4">
                                <span class="trx-id">#<?= $o['id'] ?></span>
                            </td>
                            <td>
                                <div class="fw-800 text-dark"><?= htmlspecialchars($o['animal_name']) ?></div>
                                <div class="text-muted" style="font-size: 0.7rem;">Item ID: <?= $o['product_id'] ?></div>
                            </td>
                            <td>
                                <div class="party-tag">
                                    <i class="fa-solid fa-circle-up text-success"></i>
                                    <span class="small fw-700"><?= htmlspecialchars($o['seller_name']) ?></span>
                                </div>
                                <div class="party-tag">
                                    <i class="fa-solid fa-circle-down text-primary"></i>
                                    <span class="small fw-700 text-muted"><?= htmlspecialchars($o['buyer_name']) ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="price-text">Rs. <?= number_format($o['total_price']) ?></div>
                                <small class="text-muted fw-bold" style="font-size: 0.6rem;">PAID VIA CASH/MANDI</small>
                            </td>
                            <td>
                                <div class="fw-700 text-dark small"><?= date('M d, Y', strtotime($o['order_date'])) ?></div>
                                <div class="text-muted" style="font-size: 0.7rem;"><?= date('h:i A', strtotime($o['order_date'])) ?></div>
                            </td>
                            <td>
                                <span class="badge-status <?= $status_class ?>">
                                    <i class="fa-solid fa-circle"></i> <?= $o['order_status'] ?>
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <form method="POST">
                                    <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                    <select name="new_status" class="status-select" onchange="this.form.submit()">
                                        <option value="Pending" <?= $o['order_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="Completed" <?= $o['order_status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                        <option value="Cancelled" <?= $o['order_status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                    <input type="hidden" name="update_status">
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; 
                        else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" width="80" class="mb-3 opacity-25">
                                <h5 class="fw-800 text-muted">No Sales Data Available</h5>
                                <p class="small text-muted">Awaiting first successful livestock trade.</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>