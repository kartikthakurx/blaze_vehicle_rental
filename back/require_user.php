<?php
// require_user.php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: ../signin.html?error=Login+required");
  exit;
}
