<?php
include '../db.php';
session_start();

if(!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $buyer_id    = $_SESSION['user_id'];
    $product_id  = mysqli_real_escape_string($conn, $_POST['product_id']);
    $seller_id   = mysqli_real_escape_string($conn, $_POST['seller_id']);
    $total_price = mysqli_real_escape_string($conn, $_POST['total_price']);
    
    // Additional Info from Checkout
    $phone       = mysqli_real_escape_string($conn, $_POST['buyer_phone']);
    $address     = mysqli_real_escape_string($conn, $_POST['buyer_address']);
    $status      = "Pending";

    // Insert Order
    $sql = "INSERT INTO orders (user_id, seller_id, product_id, order_status, total_price, order_date) 
            VALUES ('$buyer_id', '$seller_id', '$product_id', '$status', '$total_price', NOW())";

    if (mysqli_query($conn, $sql)) {
        // Professional Alert then Redirect
        echo "<script>
                alert('Success! Your order has been placed successfully.');
                window.location.href = 'orders.php?status=placed';
              </script>";
        exit();
    } else {
        // Error handling in a clean way
        $error = mysqli_real_escape_string($conn, mysqli_error($conn));
        echo "<script>
                alert('Error: Unable to place order. " . $error . "');
                window.history.back();
              </script>";
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>