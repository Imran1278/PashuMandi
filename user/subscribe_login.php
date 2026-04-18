<?php
include '../db.php'; 
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['emailSubscribe'])) {
    
    $email = mysqli_real_escape_string($conn, $_POST['emailSubscribe']);
    $date = date('Y-m-d H:i:s');

    // 1. Check karein ke kya ye email 'users' table mein hai?
    $check_user = mysqli_query($conn, "SELECT id, full_name FROM users WHERE email = '$email'");
    
    if (mysqli_num_rows($check_user) > 0) {
        // CASE A: User pehle se registered hai -> AUTO LOGIN
        $user_data = mysqli_fetch_assoc($check_user);
        
        // --- YE LINES LOGIN KARWAYEIN GI ---
        $_SESSION['user_id'] = $user_data['id']; // Header isi ko check karta hai
        $_SESSION['full_name'] = $user_data['full_name'];
        
        echo "<script>
                alert('Welcome back, " . $user_data['full_name'] . "! You are now logged in.');
                window.location.href = '../index.php'; // Ab Header change ho jayega
              </script>";
              
    } else {
        // CASE B: User naya hai -> Newsletter mein save karein aur Register par bhejein
        $sub_query = "INSERT INTO newsletter (email, subscribed_at) VALUES ('$email', '$date') 
                      ON DUPLICATE KEY UPDATE subscribed_at = '$date'";
        mysqli_query($conn, $sub_query);

        echo "<script>
                alert('Thank you for subscribing! Please complete your registration.');
                window.location.href = '../user/register.php?email=" . urlencode($email) . "';
              </script>";
    }

} else {
    header("Location: ../index.php");
    exit();
}
?>