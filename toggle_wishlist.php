<?php
include 'db.php';
session_start();

if(!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'login_required']);
    exit();
}

$u_id = $_SESSION['user_id'];
$a_id = mysqli_real_escape_string($conn, $_GET['animal_id']);

// Check if already in wishlist
$check = mysqli_query($conn, "SELECT id FROM wishlist WHERE user_id='$u_id' AND animal_id='$a_id'");

if(mysqli_num_rows($check) > 0) {
    // Remove if exists
    mysqli_query($conn, "DELETE FROM wishlist WHERE user_id='$u_id' AND animal_id='$a_id'");
    echo json_encode(['status' => 'removed']);
} else {
    // Add if not exists
    mysqli_query($conn, "INSERT INTO wishlist (user_id, animal_id) VALUES ('$u_id', '$a_id')");
    echo json_encode(['status' => 'added']);
}
?>