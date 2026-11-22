<?php
// back/registerenquiry.php
session_start();
require __DIR__ . '/db.php';

/**
 * Error Handling Function:
 * Agar koi galti ho toh ye stylish error message dikhayega
 */
function go($u){ 
    die("<div style='padding: 50px; text-align: center; font-family: sans-serif; background: #f8fafc; min-height: 100vh;'>
            <div style='background: white; padding: 40px; border-radius: 20px; display: inline-block; box-shadow: 0 10px 25px rgba(0,0,0,0.05);'>
                <h2 style='color: #ef4444; margin-bottom: 10px;'>Booking Alert!</h2>
                <h3 style='color: #475569; margin-bottom: 25px;'><b>" . htmlspecialchars($u) . "</b></h3>
                <a href='../vehicles.php' style='padding: 12px 25px; background: #3b82f6; color: white; text-decoration: none; border-radius: 10px; font-weight: 600;'>Go Back & Fix</a>
            </div>
         </div>"); 
}

// 1. Form Data Fetch & Sanitize
$fullname = trim($_POST['fullname'] ?? '');
$email    = trim($_POST['email'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$model    = trim($_POST['vehicle_model'] ?? $_POST['model'] ?? '');
$pickup_l = trim($_POST['pickup_location'] ?? '');
$pickup_dt = trim($_POST['pickup_datetime'] ?? '');
$dropoff_dt = trim($_POST['dropoff_datetime'] ?? '');
$pay      = trim($_POST['payment_method'] ?? '');
$dl_number= trim($_POST['dl_number'] ?? '');

// --- NEW DATA ADDED ---
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
$need_driver = (isset($_POST['need_driver']) && $_POST['need_driver'] === 'Yes') ? 'Yes' : 'No';
// ----------------------

// 2. Basic Validations
if ($fullname===''||$email===''||$phone===''||$model===''||$pickup_l===''||$pickup_dt===''||$dropoff_dt===''||$pay===''){
    go('Please fill all required fields before proceeding.');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)){ 
    go('The email address provided is invalid.'); 
}
if ($quantity < 1) {
    go('You must book at least 1 vehicle.');
}

// 3. Time Validation (Dropoff must be after Pickup)
$start_time = strtotime($pickup_dt);
$end_time = strtotime($dropoff_dt);

if ($end_time <= $start_time){ 
    go('Dropoff date/time must be after the pickup date/time.'); 
}

// 4. DL File Upload Logic
$license_path_db = null;
if (isset($_FILES['license_file']) && $_FILES['license_file']['error'] === UPLOAD_ERR_OK) {
    if ($_FILES['license_file']['size'] > 2 * 1024 * 1024) {
        go('File size must be less than 2MB.');
    }
    
    $ext = strtolower(pathinfo($_FILES['license_file']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','pdf'];
    
    if (!in_array($ext, $allowed)) {
        go('Only JPG, PNG or PDF files are allowed.');
    }
    
    $unique_name = "license_" . time() . "_" . rand(100,999) . "." . $ext;
    
    // Server path for moving file
    $destination = __DIR__ . '/../documents/' . $unique_name;

    if(move_uploaded_file($_FILES['license_file']['tmp_name'], $destination)) {
        // Database path for saving
        $license_path_db = 'documents/' . $unique_name;
    } else {
        go('Failed to save the license document on the server.');
    }
} else {
    go('Please upload a clear copy of your Driving License.');
}

// 5. Premium Price & Hours Calculation (Backend Security)
$total_hours = max(1, ceil(($end_time - $start_time) / 3600));

// Fetch vehicle price from DB
$stmtPrice = $pdo->prepare("SELECT price_per_hour FROM vehicles WHERE vehicle_name = ?");
$stmtPrice->execute([$model]);
$carData = $stmtPrice->fetch();

if (!$carData) {
    go('Selected vehicle not found. Please try again.');
}

$rent_per_hour = (int)$carData['price_per_hour'];

// --- NEW DETAILED CALCULATION LOGIC ---
$base_rent = $total_hours * $rent_per_hour * $quantity;
$security_deposit = 10000 * $quantity;
$decoration_cost = 2000 * $quantity;
$driver_cost = ($need_driver === 'Yes') ? ($total_hours * 500 * $quantity) : 0;

$total_amount = $base_rent + $security_deposit + $decoration_cost + $driver_cost;
// --------------------------------------

// SQL Format Dates
$pickup_sql = date('Y-m-d H:i:s', $start_time);
$dropoff_sql = date('Y-m-d H:i:s', $end_time);

// 6. Database Entry (Updated with new columns)
try {
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

    $sql = "INSERT INTO bookings (
                user_id, fullname, email, phone, vehicle_model, dl_number, license_file,
                pickup_location, pickup_date, dropoff_date,
                total_hours, total_amount,
                payment_method, status, created_at,
                quantity, need_driver 
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $user_id, 
        $fullname, 
        $email, 
        $phone, 
        $model, 
        $dl_number, 
        $license_path_db,
        $pickup_l, 
        $pickup_sql, 
        $dropoff_sql, 
        $total_hours, 
        $total_amount, 
        $pay, 
        'Pending', // Initial Status
        $quantity, // Naya Column
        $need_driver // Naya Column
    ]);

    // 7. Success Redirect (Pass params for success page UI)
    $redirect_url = "../success.php?name=" . urlencode($fullname) . 
                    "&model=" . urlencode($model) . 
                    "&pu=" . urlencode($pickup_sql) . 
                    "&do=" . urlencode($dropoff_sql) . 
                    "&hours=" . urlencode($total_hours) . 
                    "&total=" . urlencode($total_amount);
                    
    header("Location: " . $redirect_url);
    exit;

} catch(Exception $e) {
    go('System Error: ' . $e->getMessage());
}
?>