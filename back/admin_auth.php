<?php
// back/admin_auth.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db.php';

// CHANGE: Ab ye sirf 'admin_id' check karega
if (empty($_SESSION['admin_id'])) {
    header('Location: ../signin.html?error=Login+required');
    exit;
}

$stmt = $pdo->prepare('SELECT is_admin, fullname FROM users WHERE id = ?');
// CHANGE: Database mein bhi 'admin_id' bhejenge
$stmt->execute([$_SESSION['admin_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || (int)$user['is_admin'] !== 1) {
    header('Location: ../index.php?error=Access+denied');
    exit;
}

if (!isset($_SESSION['admin_name']) && !empty($user['fullname'])) {
    $_SESSION['admin_name'] = $user['fullname'];
}
?>