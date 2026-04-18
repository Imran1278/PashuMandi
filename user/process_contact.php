<?php
include '../db.php'; // Database connection file ka sahi path dein
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Data ko sanitize karna (Security ke liye)
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // Database mein insert karne ki query
    $query = "INSERT INTO contact_messages (name, email, subject, message) 
              VALUES ('$name', '$email', '$subject', '$message')";

    if (mysqli_query($conn, $query)) {
        // Success message ke saath wapas bhejein
        echo "<script>
                alert('Thank you! Your message has been sent successfully.');
                window.location.href = '../index.php#contact'; 
              </script>";
    } else {
        // Error handling
        echo "<script>
                alert('Error: Could not send your message. Please try again.');
                window.history.back();
              </script>";
    }

} else {
    // Agar koi direct access kare toh home par bhej dein
    header("Location: ../index.php");
    exit();
}
?>