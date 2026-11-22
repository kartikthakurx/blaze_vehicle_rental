<?php
// back/login.php
session_start();
require __DIR__ . '/db.php';

function backToLogin($msg){ header("Location: ../signin.html?$msg"); exit; }

$email = trim($_POST['email'] ?? '');
$pass  = $_POST['password'] ?? '';

if ($email === '' || $pass === '') { backToLogin('error=Enter+email+and+password'); }

try {
    // 1) Get user with is_admin
    $st = $pdo->prepare("SELECT id, fullname, password_hash, is_admin FROM users WHERE email = ?");
    $st->execute([$email]);
    $u = $st->fetch(PDO::FETCH_ASSOC);

    // 2) Verify
    if (!$u || !password_verify($pass, $u['password_hash'])) {
        backToLogin('error=Invalid+credentials');
    }

    // 3) Set session
    session_regenerate_id(true);
    
    // --- YAHAN FIX HAI: Dono ke raste alag kar diye ---
    if ((int)$u['is_admin'] === 1) {
        // Admin ke liye 'admin_id'
        $_SESSION['admin_id'] = (int)$u['id'];
        $_SESSION['admin_name'] = $u['fullname'] ?? ''; 
        header('Location: /rentalproject/admin/dashboard.php');
    } else {
        // Normal User ke liye 'user_id'
        $_SESSION['user_id']  = (int)$u['id'];
        $_SESSION['username'] = $u['fullname'] ?? ''; 
        header('Location: /rentalproject/index.php?login=1'); 
    }
    exit;
} catch (Exception $e) {
    backToLogin('error=Server+error');
}
?>