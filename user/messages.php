<?php 
include '../db.php'; 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$u_id = $_SESSION['user_id'];
$active_chat = isset($_GET['receiver']) ? mysqli_real_escape_string($conn, $_GET['receiver']) : null;

// 1. SEND MESSAGE LOGIC
if(isset($_POST['send_msg']) && !empty(trim($_POST['message_text'])) && $active_chat) {
    $msg_text = mysqli_real_escape_string($conn, $_POST['message_text']);
    mysqli_query($conn, "INSERT INTO messages (sender_id, receiver_id, message, is_read) VALUES ('$u_id', '$active_chat', '$msg_text', 0)");
    header("Location: messages.php?receiver=$active_chat");
    exit();
}

// 2. MARK AS READ
if($active_chat) {
    mysqli_query($conn, "UPDATE messages SET is_read=1 WHERE receiver_id='$u_id' AND sender_id='$active_chat'");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages | PashuMandi </title>
    <link rel="icon" href="../pics/icon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root { --p-blue: #0ea5e9; --p-dark: #0f172a; --p-bg: #f8fafc; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--p-bg); height: 100vh; overflow: hidden; }
        .chat-app { height: 90vh; margin: 20px auto; background: white; border-radius: 35px; display: flex; box-shadow: 0 30px 60px rgba(0,0,0,0.08); overflow: hidden; border: 1px solid #eef2f6; }
        .chat-sidebar { width: 380px; border-right: 1px solid #f1f5f9; display: flex; flex-direction: column; background: #fff; }
        .sidebar-header { padding: 30px; border-bottom: 1px solid #f1f5f9; }
        .chat-list { flex: 1; overflow-y: auto; }
        .chat-user { padding: 18px 25px; display: flex; align-items: center; gap: 15px; cursor: pointer; transition: 0.3s; text-decoration: none; color: inherit; position: relative; }
        .chat-user:hover { background: #f8fafc; }
        .chat-user.active { background: #f0f9ff; }
        .chat-user.active::after { content: ''; position: absolute; left: 0; top: 15%; height: 70%; width: 4px; background: var(--p-blue); border-radius: 0 10px 10px 0; }
        .user-img { width: 55px; height: 55px; border-radius: 18px; object-fit: cover; background: #f1f5f9; border: 2px solid #fff; }
        .chat-window { flex: 1; display: flex; flex-direction: column; background: #fff; }
        .window-header { padding: 20px 35px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; }
        .message-area { flex: 1; padding: 35px; overflow-y: auto; background: #fcfdfe; display: flex; flex-direction: column; gap: 12px; }
        .bubble { max-width: 65%; padding: 14px 20px; border-radius: 22px; font-size: 0.92rem; font-weight: 500; position: relative; }
        .bubble.sent { align-self: flex-end; background: var(--p-blue); color: white; border-bottom-right-radius: 4px; }
        .bubble.received { align-self: flex-start; background: #f1f5f9; color: var(--p-dark); border-bottom-left-radius: 4px; }
        .msg-time { font-size: 0.65rem; margin-top: 6px; opacity: 0.8; display: block; }
        .input-area { padding: 25px 35px; border-top: 1px solid #f1f5f9; }
        .input-box { background: #f8fafc; border-radius: 20px; padding: 8px 12px; border: 1px solid #eef2f6; }
        .input-box input { flex: 1; border: none; background: transparent; padding: 12px; outline: none; font-weight: 600; }
        .btn-send { background: var(--p-blue); color: white; border: none; width: 48px; height: 48px; border-radius: 16px; transition: 0.3s; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    </style>
</head>
<body>

<div class="container py-4">
    <div class="chat-app">
        
        <div class="chat-sidebar">
            <div class="sidebar-header d-flex justify-content-between align-items-center">
                <h4 class="fw-800 mb-0">Inbox</h4>
                <a href="../index.php" class="text-muted"><i class="fa fa-house"></i></a>
            </div>
            <div class="chat-list">
                <?php 
                $list_q = "SELECT DISTINCT 
                            CASE WHEN sender_id = '$u_id' THEN receiver_id ELSE sender_id END as contact_id 
                           FROM messages WHERE sender_id = '$u_id' OR receiver_id = '$u_id'";
                $list_res = mysqli_query($conn, $list_q);
                
                while($row = mysqli_fetch_assoc($list_res)):
                    $contact_id = $row['contact_id'];
                    $c_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT full_name, profile_pic FROM users WHERE id='$contact_id'"));
                    $unread = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM messages WHERE sender_id='$contact_id' AND receiver_id='$u_id' AND is_read=0"));
                ?>
                <a href="messages.php?receiver=<?= $contact_id ?>" class="chat-user <?= ($active_chat == $contact_id) ? 'active' : '' ?>">
                    <img src="../uploads/<?= !empty($c_info['profile_pic']) ? $c_info['profile_pic'] : 'default-user.png' ?>" class="user-img">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <h6 class="mb-0 fw-700"><?= htmlspecialchars($c_info['full_name'] ?? 'User') ?></h6>
                            <?php if($unread > 0): ?>
                                <span class="badge rounded-pill bg-primary" style="font-size: 0.6rem;"><?= $unread ?></span>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted fw-600" style="font-size: 0.75rem;">Chat active</small>
                    </div>
                </a>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="chat-window">
            <?php if($active_chat): 
                $receiver_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT full_name, profile_pic FROM users WHERE id='$active_chat'"));
            ?>
                <div class="window-header">
                    <div class="d-flex align-items-center gap-3">
                        <img src="../uploads/<?= !empty($receiver_info['profile_pic']) ? $receiver_info['profile_pic'] : 'default-user.png' ?>" class="user-img" style="width:45px; height:45px;">
                        <div>
                            <h6 class="fw-800 mb-0"><?= htmlspecialchars($receiver_info['full_name'] ?? 'User') ?></h6>
                            <small class="text-success fw-700" style="font-size: 0.7rem;"><i class="fa fa-circle me-1"></i> Online</small>
                        </div>
                    </div>
                </div>

                <div class="message-area" id="chatBox">
                    <?php 
                    $msgs = mysqli_query($conn, "SELECT * FROM messages WHERE (sender_id='$u_id' AND receiver_id='$active_chat') OR (sender_id='$active_chat' AND receiver_id='$u_id') ORDER BY created_at ASC");
                    while($m = mysqli_fetch_assoc($msgs)):
                        $side = ($m['sender_id'] == $u_id) ? 'sent' : 'received';
                    ?>
                        <div class="bubble <?= $side ?>">
                            <?= htmlspecialchars($m['message']) ?>
                            <span class="msg-time">
                                <?= date('h:i A', strtotime($m['created_at'])) ?>
                                <?php if($side == 'sent'): ?>
                                    <i class="fa-solid fa-check-double ms-1 <?= ($m['is_read'] == 1) ? 'text-white' : 'opacity-50' ?>"></i>
                                <?php endif; ?>
                            </span>
                        </div>
                    <?php endwhile; ?>
                </div>

                <div class="input-area">
                    <form method="POST" class="d-flex gap-2">
                        <div class="input-box d-flex flex-grow-1">
                            <input type="text" id="msgInput" name="message_text" placeholder="Type your response..." autocomplete="off" required>
                        </div>
                        <button type="submit" name="send_msg" class="btn-send">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="h-100 d-flex align-items-center justify-content-center text-center">
                    <div class="p-5">
                        <div class="mb-4 text-primary opacity-10" style="font-size: 120px;"><i class="fa-solid fa-comments"></i></div>
                        <h3 class="fw-800">Your Inbox</h3>
                        <p class="text-muted mx-auto" style="max-width: 300px;">Select a conversation to start chatting.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
    // 1. Auto Scroll to bottom
    const chatBox = document.getElementById("chatBox");
    if(chatBox) { chatBox.scrollTop = chatBox.scrollHeight; }

    // 2. Intelligent Auto-Refresh Fix
    let isTyping = false;
    const msgInput = document.getElementById("msgInput");

    if(msgInput) {
        msgInput.addEventListener('focus', () => { isTyping = true; });
        msgInput.addEventListener('blur', () => { isTyping = false; });
    }

    <?php if($active_chat): ?>
    setInterval(function(){
        // Agar user type nahi kar raha, tabhi refresh karega
        if(!isTyping) {
            location.reload();
        }
    }, 7000); // 7 seconds refresh time
    <?php endif; ?>
</script>

</body>
</html>