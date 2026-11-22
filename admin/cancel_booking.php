<?php
session_start();
require_once 'db.php';

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $booking_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Sirf status update karenge, delete nahi!
    $sql = "UPDATE bookings SET status = 'CANCELLED BY USER' WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$booking_id, $user_id])) {
        header("Location: ../mybooking.php?msg=cancelled");
    } else {
        echo "Error updating record";
    }
}
?>