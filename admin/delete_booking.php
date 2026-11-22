<?php
//delete_booking.php
require_once __DIR__ . '/../back/db.php';
require_once __DIR__ . '/../back/admin_auth.php';

$id = $_GET['id'] ?? 0;
$pdo->prepare("DELETE FROM bookings WHERE id = ?")->execute([$id]);
header("Location: bookings.php?success=Booking+deleted");
