<?php 
include '../db.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// // Security Check (Isay zaroor on rakhein)
// if(!isset($_SESSION['user_id'])) { header("Location: ../login.php"); exit(); }

// Delete Message Logic
if(isset($_GET['delete_id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM messages WHERE id='$id'");
    header("Location: manage_messages.php?status=deleted");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage User Chats | Admin Panel</title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root { --p-blue: #38bdf8; --p-dark: #0f172a; }
        body { background: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; color: var(--p-dark); }
        
        .msg-bubble { 
            background: white; border-radius: 28px; padding: 25px; 
            margin-bottom: 20px; border: 1px solid #e2e8f0;
            transition: all 0.3s ease; 
        }
        .msg-bubble:hover { border-color: var(--p-blue); box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        
        .user-tag { background: #f1f5f9; padding: 5px 12px; border-radius: 10px; font-weight: 700; font-size: 0.85rem; }
        .arrow-icon { color: #94a3b8; font-size: 0.8rem; margin: 0 10px; }
        .timestamp { font-size: 0.75rem; color: #94a3b8; font-weight: 600; }
        
        .message-content { 
            background: #f8fafc; padding: 15px 20px; border-radius: 15px; 
            color: #475569; margin-top: 15px; border-left: 4px solid var(--p-blue);
        }

        .btn-action { width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
    </style>
</head>
<body>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div>
                        <h2 class="fw-800 mb-1">User <span class="text-info">Conversations</span></h2>
                        <p class="text-muted small fw-600">Monitoring all direct messages between Buyers & Sellers.</p>
                    </div>
                    <a href="admin_panel.php" class="btn btn-white shadow-sm rounded-pill px-4 fw-bold border">
                        <i class="fa fa-arrow-left me-2"></i> Dashboard
                    </a>
                </div>

                <div class="messages-stack">
                    <?php 
                    // Naya SQL Join: Sender aur Receiver ke naam nikalne ke liye
                    $sql = "SELECT m.*, 
                            u1.full_name as sender_name, u1.email as sender_email,
                            u2.full_name as receiver_name
                            FROM messages m 
                            JOIN users u1 ON m.sender_id = u1.id 
                            JOIN users u2 ON m.receiver_id = u2.id 
                            ORDER BY m.id DESC";
                    
                    $msgs = mysqli_query($conn, $sql);
                    
                    if(mysqli_num_rows($msgs) > 0):
                        while($m = mysqli_fetch_assoc($msgs)): 
                    ?>
                    <div class="msg-bubble">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="user-tag text-primary"><i class="fa fa-user me-1"></i> <?= htmlspecialchars($m['sender_name']) ?></span>
                                <i class="fa fa-long-arrow-right arrow-icon"></i>
                                <span class="user-tag text-dark"><i class="fa fa-store me-1"></i> <?= htmlspecialchars($m['receiver_name']) ?></span>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span class="timestamp"><i class="fa-regular fa-clock me-1"></i> <?= date('M d, h:i A', strtotime($m['created_at'])) ?></span>
                                <a href="?delete_id=<?= $m['id'] ?>" class="btn-action bg-light text-danger border" onclick="return confirm('Delete this message log?')">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </div>
                        </div>

                        <div class="message-content">
                            <?= nl2br(htmlspecialchars($m['message'])) ?>
                        </div>
                    </div>
                    <?php endwhile; 
                    else: ?>
                    <div class="text-center py-5 opacity-50">
                        <i class="fa-regular fa-comments fa-4x mb-3 text-info"></i>
                        <h4 class="fw-800">No Conversations Yet</h4>
                        <p class="fw-600 text-muted">User chats will appear here once they start messaging.</p>
                    </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

</body>
</html>