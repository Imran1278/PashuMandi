<?php 
include '../db.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }



//$id = $_SESSION['id'];

// 1. Enhanced Delete Logic
if(isset($_GET['delete_user'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete_user']);
    
    // Prevent self-deletion
    if($id == $id) {
        header("Location: manage_users.php?msg=error_self");
    } else {
        mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
        header("Location: manage_users.php?msg=User Removed");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage User | Admin  Panel</title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --pm-primary: #38bdf8; --pm-dark: #0f172a; }
        body { background: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        .admin-card { 
            border: none; border-radius: 35px; 
            box-shadow: 0 20px 60px rgba(0,0,0,0.03); 
            background: white; overflow: hidden;
        }
        
        .user-row { transition: all 0.3s; }
        .user-row:hover { background: #f0f9ff; }
        
        .avatar-frame { 
            width: 55px; height: 55px; border-radius: 18px; 
            object-fit: cover; border: 3px solid #f1f5f9;
            background: #f1f5f9;
        }

        .badge-verified { background: #dcfce7; color: #166534; font-size: 0.65rem; font-weight: 800; border-radius: 10px; padding: 6px 12px; }
        .badge-role { background: #f1f5f9; color: #475569; font-size: 0.65rem; font-weight: 800; border-radius: 10px; padding: 6px 12px; text-transform: uppercase; }

        .search-input { 
            border-radius: 15px; border: 1px solid #e2e8f0; padding: 10px 20px; 
            background: #f8fafc; font-size: 0.9rem; width: 300px;
        }

        .btn-action {
            width: 38px; height: 38px; border-radius: 12px; 
            display: inline-flex; align-items: center; justify-content: center;
            transition: 0.2s; border: 1px solid #e2e8f0; background: white; color: #64748b;
        }
        .btn-action:hover { background: #fee2e2; color: #ef4444; border-color: #fecaca; }
        .btn-view:hover { background: #e0f2fe; color: #0ea5e9; border-color: #bae6fd; }
    </style>
</head>
<body>

    <div class="container-fluid p-lg-5 p-4">
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
            <div>
                <h2 class="fw-800 mb-1 text-dark">User <span class="text-primary">Directory</span></h2>
                <p class="text-muted small fw-600 mb-0">Managing <?= mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users")) ?> registered members.</p>
            </div>
            <div class="d-flex gap-3">
                <input type="text" class="search-input d-none d-md-block" placeholder="Search by name or email...">
                <a href="admin_panel.php" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm">
                    <i class="fa fa-house me-2"></i> Dashboard
                </a>
            </div>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-<?= ($_GET['msg'] == 'error_self') ? 'danger' : 'success' ?> border-0 rounded-4 py-3 shadow-sm mb-4">
                <i class="fa fa-info-circle me-2"></i> 
                <?= ($_GET['msg'] == 'error_self') ? "Access Denied: You cannot delete your own admin account." : htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>

        <div class="admin-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light border-bottom">
                        <tr class="text-muted small fw-800">
                            <th class="ps-5 py-4">MEMBER PROFILE</th>
                            <th>CONTACT DETAILS</th>
                            <th>REGION</th>
                            <th>ROLE / STATUS</th>
                            <th class="text-end pe-5">OPERATIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $res = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
                        if(mysqli_num_rows($res) > 0):
                            while($row = mysqli_fetch_assoc($res)): 
                                $is_self = ($row['id'] == $id);
                        ?>
                        <tr class="user-row <?= $is_self ? 'opacity-75' : '' ?>">
                            <td class="ps-5 py-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="position-relative">
                                        <img src="../uploads/<?= !empty($row['profile_pic']) ? $row['profile_pic'] : 'default-user.png' ?>" class="avatar-frame">
                                        <?php if($is_self): ?>
                                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary border border-white border-3">YOU</span>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="fw-800 text-dark"><?= htmlspecialchars($row['full_name']) ?></div>
                                        <small class="text-muted fw-700" style="font-size: 0.65rem;">UID: #<?= str_pad($row['id'], 5, '0', STR_PAD_LEFT) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small fw-700 text-dark"><?= htmlspecialchars($row['email']) ?></div>
                                <div class="small text-primary fw-600"><?= htmlspecialchars($row['phone']) ?></div>
                            </td>
                            <td>
                                <div class="small text-muted fw-600">
                                    <i class="fa-solid fa-location-dot text-danger opacity-50 me-1"></i> 
                                    <?= !empty($row['address']) ? htmlspecialchars($row['address']) : 'Location Not Set' ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge-role me-1"><?= $row['role'] ?? 'Member' ?></span>
                                <span class="badge-verified"><i class="fa fa-check-double me-1"></i> Verified</span>
                            </td>
                            <td class="text-end pe-5">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="../user/profile.php" class="btn-action btn-view" title="View Details"><i class="fa fa-eye"></i></a>
                                    
                                    <?php if(!$is_self): ?>
                                    <a href="?delete_user=<?= $row['id'] ?>" 
                                       onclick="return confirm('WARNING: Are you sure you want to permanently remove this user and all their listings?')" 
                                       class="btn-action" title="Remove User">
                                        <i class="fa-solid fa-user-xmark"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; 
                        else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted">No users found in the system.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>