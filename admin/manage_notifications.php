<?php 
include '../db.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Security Check (Admin Only - Replace with your admin session logic)
// if(!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit(); }

// 1. Broadcast New Message
if(isset($_POST['broadcast'])) {
    $title = mysqli_real_escape_string($conn, $_POST['alert_title']);
    $content = mysqli_real_escape_string($conn, $_POST['alert_msg']);
    $type = mysqli_real_escape_string($conn, $_POST['alert_type']);
    
    if(!empty(trim($content))) {
        // user_id NULL matlab ye sab ke liye hai
        mysqli_query($conn, "INSERT INTO notifications (user_id, title, content, type, status) 
                            VALUES (NULL, '$title', '$content', '$type', 'active')");
        header("Location: manage_notifications.php?status=sent");
        exit();
    }
}

// 2. Actions (Delete or Toggle)
if(isset($_GET['action'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    if($_GET['action'] == 'delete') {
        mysqli_query($conn, "DELETE FROM notifications WHERE id='$id'");
    } elseif($_GET['action'] == 'toggle') {
        $curr = mysqli_query($conn, "SELECT status FROM notifications WHERE id='$id'");
        $row = mysqli_fetch_assoc($curr);
        $new_status = ($row['status'] == 'active') ? 'inactive' : 'active';
        mysqli_query($conn, "UPDATE notifications SET status='$new_status' WHERE id='$id'");
    }
    header("Location: manage_notifications.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Notifications | Admin</title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --p-blue: #38bdf8; --p-dark: #0f172a; --p-card: #1e293b; }
        body { background: var(--p-dark); font-family: 'Plus Jakarta Sans', sans-serif; color: #f8fafc; padding: 50px 0; }
        .notif-panel { background: var(--p-card); border-radius: 35px; border: 1px solid rgba(255,255,255,0.08); padding: 40px; box-shadow: 0 30px 60px rgba(0,0,0,0.4); }
        .form-control, .form-select { background: #0f172a; border: 2px solid #334155; color: white; padding: 12px 20px; border-radius: 15px; }
        .form-control:focus { background: #0f172a; color: white; border-color: var(--p-blue); box-shadow: none; }
        .history-card { background: rgba(15, 23, 42, 0.5); border-radius: 20px; padding: 20px; border: 1px solid rgba(255,255,255,0.05); }
        .status-badge { padding: 4px 10px; border-radius: 8px; font-size: 0.65rem; font-weight: 800; }
        .badge-active { background: rgba(34, 197, 94, 0.2); color: #4ade80; }
        .badge-inactive { background: rgba(239, 68, 68, 0.2); color: #f87171; }
        .btn-broadcast { background: linear-gradient(135deg, #0ea5e9, #38bdf8); border: none; font-weight: 800; }
    </style>
</head>
<body>
    <div class="container">
        <div class="notif-panel">
            <div class="text-center mb-5">
                <h2 class="fw-800">System <span class="text-info">Broadcast</span></h2>
                <p class="text-muted">Send alerts to all user dashboards.</p>
            </div>

            <div class="row g-5">
                <div class="col-md-5">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="small fw-bold text-info text-uppercase mb-2">Title</label>
                            <input type="text" name="alert_title" class="form-control" placeholder="e.g. Server Maintenance" required>
                        </div>
                        <div class="mb-3">
                            <label class="small fw-bold text-info text-uppercase mb-2">Category</label>
                            <select name="alert_type" class="form-select">
                                <option value="info">Info (Blue)</option>
                                <option value="success">Success (Green)</option>
                                <option value="warning">Warning (Yellow)</option>
                                <option value="danger">Danger (Red)</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="small fw-bold text-info text-uppercase mb-2">Message Content</label>
                            <textarea name="alert_msg" class="form-control" rows="4" placeholder="Type notification details..." required></textarea>
                        </div>
                        <button name="broadcast" class="btn btn-broadcast w-100 py-3 rounded-pill text-white">
                            PUSH ALERT <i class="fa-solid fa-bolt ms-2"></i>
                        </button>
                    </form>
                </div>

                <div class="col-md-7 border-start border-secondary border-opacity-25 ps-md-5">
                    <label class="small fw-bold text-info text-uppercase mb-3">Broadcast History</label>
                    <div class="history-card">
                        <table class="table table-dark table-borderless mb-0">
                            <thead>
                                <tr class="small text-muted border-bottom border-white border-opacity-10">
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $history = mysqli_query($conn, "SELECT * FROM notifications WHERE user_id IS NULL ORDER BY id DESC LIMIT 6");
                                while($h = mysqli_fetch_assoc($history)): ?>
                                <tr class="align-middle">
                                    <td class="small">
                                        <div class="text-truncate fw-bold" style="max-width: 200px;"><?= htmlspecialchars($h['title']) ?></div>
                                        <span class="text-muted" style="font-size: 0.65rem;"><?= date('M d, H:i', strtotime($h['created_at'])) ?></span>
                                    </td>
                                    <td>
                                        <span class="status-badge <?= $h['status'] == 'active' ? 'badge-active' : 'badge-inactive' ?>">
                                            <?= strtoupper($h['status']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="?action=toggle&id=<?= $h['id'] ?>" class="btn btn-sm text-info"><i class="fa fa-eye-slash"></i></a>
                                        <a href="?action=delete&id=<?= $h['id'] ?>" class="btn btn-sm text-danger" onclick="return confirm('Delete?')"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>