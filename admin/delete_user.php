<?php
//delete_user.php
require_once __DIR__ . '/../back/db.php';
require_once __DIR__ . '/../back/admin_auth.php';

$id = $_GET['id'] ?? 0;

// Prevent deleting yourself
if ($id == $_SESSION['user_id']) {
    header("Location: users.php?error=Cannot+delete+yourself");
    exit;
}

$pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
header("Location: users.php?success=User+deleted");
