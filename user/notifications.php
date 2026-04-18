<?php 
include '../db.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$u_id = $_SESSION['user_id'];
$status_msg = "";

// 1. Mark All as Read
if(isset($_POST['mark_read'])) {
    mysqli_query($conn, "UPDATE notifications SET is_read=1 WHERE user_id='$u_id'");
    $status_msg = "All notifications marked as read!";
}

// 2. Delete Notification
if(isset($_GET['del'])) {
    $del_id = mysqli_real_escape_string($conn, $_GET['del']);
    // User sirf apne ya broadcast delete kar sakta hai (apne view se)
    mysqli_query($conn, "DELETE FROM notifications WHERE id='$del_id' AND (user_id='$u_id' OR user_id IS NULL)");
    header("Location: notifications.php");
    exit();
}

// Fetch Personal + Admin Broadcasts
$notif_q = mysqli_query($conn, "SELECT * FROM notifications 
                                WHERE (user_id='$u_id' OR user_id IS NULL) 
                                AND status='active' 
                                ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications | PashuMandi</title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --p-blue: #0ea5e9; --p-bg: #f8fafc; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--p-bg); color: #1e293b; }
        .notif-container { max-width: 800px; margin: 40px auto; }
        .header-box { background: white; padding: 25px 35px; border-radius: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .notif-card { background: white; border-radius: 22px; padding: 20px; margin-bottom: 15px; border: 1px solid #f1f5f9; display: flex; gap: 18px; transition: 0.3s; position: relative; }
        .notif-card.unread { background: #f0f9ff; border-left: 5px solid var(--p-blue); }
        .icon-circle { width: 55px; height: 55px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
        .type-info { background: #e0f2fe; color: #0ea5e9; }
        .type-success { background: #dcfce7; color: #15803d; }
        .type-warning { background: #fef9c3; color: #a16207; }
        .type-danger { background: #fee2e2; color: #b91c1c; }
        .unread-dot { width: 10px; height: 10px; background: var(--p-blue); border-radius: 50%; position: absolute; top: 20px; right: 20px; }
        .btn-mark-all { border-radius: 12px; padding: 10px 20px; font-weight: 700; background: var(--p-blue); color: white; border: none; }
    </style>
</head>
<body>
    <div class="container notif-container">
        <div class="header-box">
            <div>
                <h3 class="fw-800 mb-0">Notifications</h3>
                <p class="text-muted small mb-0">Stay updated with latest activity.</p>
            </div>
            <?php if(mysqli_num_rows($notif_q) > 0): ?>
            <form method="POST">
                <button type="submit" name="mark_read" class="btn-mark-all shadow-sm">Mark All Read</button>
            </form>
            <?php endif; ?>
        </div>

        <?php if($status_msg): ?>
            <div class="alert alert-info border-0 rounded-4 mb-4"><?= $status_msg ?></div>
        <?php endif; ?>

        <div class="notif-list">
            <?php if(mysqli_num_rows($notif_q) > 0): 
                while($n = mysqli_fetch_assoc($notif_q)): 
                    $type = $n['type'] ?? 'info';
                    $is_unread = ($n['user_id'] != NULL && $n['is_read'] == 0); // Broadcast are usually considered read-neutral or always visible
            ?>
            <div class="notif-card <?= $is_unread ? 'unread' : '' ?>">
                <div class="icon-circle type-<?= $type ?>">
                    <i class="fa-solid <?= ($type=='success'?'fa-check-circle':($type=='danger'?'fa-circle-xmark':'fa-bell')) ?>"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between">
                        <h6 class="fw-800 mb-1"><?= htmlspecialchars($n['title']) ?></h6>
                        <a href="?del=<?= $n['id'] ?>" class="text-muted opacity-50"><i class="fa-solid fa-xmark"></i></a>
                    </div>
                    <p class="text-muted small mb-1"><?= htmlspecialchars($n['content']) ?></p>
                    <div class="text-muted" style="font-size: 0.65rem;">
                        <i class="fa-regular fa-clock me-1"></i> <?= date('d M, h:i A', strtotime($n['created_at'])) ?>
                        <?= ($n['user_id'] == NULL) ? '<span class="badge bg-secondary ms-2">System Alert</span>' : '' ?>
                    </div>
                </div>
                <?php if($is_unread): ?><div class="unread-dot"></div><?php endif; ?>
            </div>
            <?php endwhile; else: ?>
                <div class="text-center py-5 opacity-50">
                    <i class="fa-solid fa-wind fa-3x mb-3"></i>
                    <h4>No Notifications</h4>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>