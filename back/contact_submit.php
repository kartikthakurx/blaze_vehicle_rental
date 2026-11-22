<?php
require __DIR__ . '/db.php';

// 1. Data collect karna
$fullname = trim($_POST['fullname'] ?? '');
$email    = trim($_POST['email'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$subject  = trim($_POST['subject'] ?? '');
$message  = trim($_POST['message'] ?? '');

// User kahaan se aaya hai uska path nikalna
$referrer = $_SERVER['HTTP_REFERER'] ?? '/rentalproject/index.php';

// 2. Validation (Check karna ki fields khali toh nahi hain)
if ($fullname === '' || $email === '' || $subject === '' || $message === '') {
    header("Location: " . strtok($referrer, '?') . "?error=Please+fill+all+required+fields");
    exit; // Yahan exit hona zaroori hai
}

try {
    // 3. Database mein save karna (Ye pehle hona chahiye)
    $stmt = $pdo->prepare(
        'INSERT INTO contact_messages (fullname, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([$fullname, $email, $phone, $subject, $message]);

    // 4. SUCCESS: Save hone ke BAAD redirect karna
    header("Location: " . strtok($referrer, '?') . "?success=Thanks+for+contacting+us");
    exit;

} catch (Exception $e) {
    // Error hone par redirect
    header("Location: " . strtok($referrer, '?') . "?error=Server+error");
    exit;
}
?>