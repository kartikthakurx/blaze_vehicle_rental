<?php

session_start();
require __DIR__ . '/back/db.php';

function go($u){ header("Location: ./signin.html?$u"); exit; }

$fullname = trim($_POST['fullname'] ?? '');
$email    = trim($_POST['email'] ?? '');
$mobile   = trim($_POST['mobile'] ?? '');
$pass     = $_POST['password'] ?? '';
$confirm  = $_POST['confirmpassword'] ?? '';

if ($fullname==='' || $email==='' || $pass==='' || $confirm==='') { go('error=Fill+all+fields'); }
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { go('error=Invalid+email'); }
if ($pass !== $confirm) { go('error=Passwords+mismatch'); }

try {
  $q=$pdo->prepare("SELECT id FROM users WHERE email=?");
  $q->execute([$email]);
  if ($q->fetch()) { go('error=Email+already+registered'); }

  $hash = password_hash($pass, PASSWORD_DEFAULT);
  $ins=$pdo->prepare("INSERT INTO users(fullname,email,mobile,password_hash) VALUES(?,?,?,?)");
  $ins->execute([$fullname,$email,$mobile,$hash]);

  $_SESSION['user_id']  = (int)$pdo->lastInsertId();
  $_SESSION['fullname'] = $fullname;
  session_regenerate_id(true);

  go('index.php?signup=1');
} catch (Exception $e) {
  go('error=Server+error');
}
