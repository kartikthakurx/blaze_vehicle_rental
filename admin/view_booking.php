<?php
// admin/view_booking.php
require_once __DIR__ . '/../back/db.php';
require_once __DIR__ . '/../back/admin_auth.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $allowed    = ['pending','approved','rejected','completed'];
    $new_status = strtolower(trim($_POST['status'] ?? ''));
    $bid        = (int)($_POST['booking_id'] ?? 0);

    if ($bid === $id && in_array($new_status, $allowed, true)) {
        $update = $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        $update->execute([$new_status, $id]);
        header("Location: view_booking.php?id=$id&success=1");
        exit;
    }
}

$stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->execute([$id]);
$booking = $stmt->fetch();

if (!$booking) {
    header("Location: bookings.php?error=not_found");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Details #<?= $id ?> - Blaze Rental</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --accent-color: #38bdf8;
            --text-gray: #cbd5e1;
            --bg-light: #f1f5f9;
            --text-main: #1e293b; 
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Inter', 'Segoe UI', sans-serif; 
            background: var(--bg-light); 
            color: var(--text-main);
            font-size: 18px;
        }

        /* Sidebar - Exact match with dashboard */
        .sidebar { 
            position: fixed; left: 0; top: 0; width: 280px; height: 100vh; 
            background: var(--sidebar-bg); color: white; padding: 30px 20px; 
            z-index: 1000;
        }
        .sidebar h2 { font-size: 26px; margin-bottom: 40px; font-weight: 700; display: flex; align-items: center; gap: 12px; }
        .sidebar a { 
            display: flex; align-items: center; gap: 15px; color: var(--text-gray); 
            text-decoration: none; padding: 15px 20px; margin-bottom: 10px; 
            border-radius: 12px; transition: 0.3s; font-size: 18px;
        }
        .sidebar a:hover, .sidebar a.active { background: var(--sidebar-hover); color: white; }
        .sidebar a.active { background: rgba(56, 189, 248, 0.15); color: var(--accent-color); }

        /* Main Content */
        .main { margin-left: 280px; padding: 50px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px; }
        .header h1 { font-size: 42px; font-weight: 700; color: #0f172a; }

        .btn-back { 
            background: white; color: var(--text-main); border: 1px solid #e2e8f0; 
            padding: 12px 25px; border-radius: 12px; text-decoration: none; font-size: 16px; font-weight: 600;
        }

        .container-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px; }

        .card { 
            background: white; padding: 35px; border-radius: 25px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        .card h2 { font-size: 22px; margin-bottom: 25px; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px; }

        /* Details List */
        .info-group { margin-bottom: 20px; }
        .info-label { font-size: 14px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; display: block; margin-bottom: 5px; }
        .info-value { font-size: 19px; color: #1e293b; font-weight: 500; }

        /* Status Colors */
        .status-badge { padding: 6px 15px; border-radius: 20px; font-size: 14px; font-weight: 700; text-transform: uppercase; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #dcfce7; color: #166534; }
        .status-rejected { background: #fee2e2; color: #991b1b; }

        /* Form styling */
        select { 
            width: 100%; padding: 15px; border-radius: 12px; border: 2px solid #e2e8f0; 
            font-size: 16px; margin-bottom: 20px; outline: none;
        }
        .btn-update { 
            width: 100%; background: var(--sidebar-bg); color: white; border: none; 
            padding: 15px; border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s;
        }
        .btn-update:hover { background: #0f172a; transform: translateY(-2px); }

        .dl-link { 
            display: inline-flex; align-items: center; gap: 10px; color: #3b82f6; 
            text-decoration: none; font-weight: 600; margin-top: 10px;
        }

        /* NEW: Driver Alert Box */
        .driver-alert {
            background: #ecfdf5; border: 2px dashed #10b981; border-radius: 15px; 
            padding: 20px; margin-top: 20px; display: flex; align-items: center; gap: 20px;
        }
        .driver-alert i { font-size: 35px; color: #10b981; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2><i class="fa-solid fa-car-rear"></i> Admin</h2>
        <a href="dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
        <a href="bookings.php" class="active"><i class="fa-solid fa-calendar-check"></i> Bookings</a>
        <a href="users.php"><i class="fa-solid fa-users"></i> Users</a>
        <a href="add_vehicle.php"><i class="fa-solid fa-plus-circle"></i> Add Vehicle</a>
        <a href="manage_vehicles.php"><i class="fa-solid fa-car"></i> Manage Fleet</a>
        <a href="messages.php"><i class="fa-solid fa-envelope"></i> Messages</a>
        <a href="../back/logout.php" style="margin-top: 50px; color: #f87171 !important;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a> 
    </div>

    <div class="main">
        <div class="header">
            <h1>Booking Details</h1>
            <a href="bookings.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> Back to List</a>
        </div>

        <div class="container-grid">
            <div class="card">
                <h2><i class="fa-solid fa-circle-info"></i> Customer & Vehicle Information</h2>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="info-group">
                        <span class="info-label">Customer Name</span>
                        <div class="info-value"><?= htmlspecialchars($booking['fullname']) ?></div>
                    </div>
                    <div class="info-group">
                        <span class="info-label">Contact Number</span>
                        <div class="info-value"><?= htmlspecialchars($booking['phone']) ?></div>
                    </div>
                    <div class="info-group">
                        <span class="info-label">Email Address</span>
                        <div class="info-value"><?= htmlspecialchars($booking['email']) ?></div>
                    </div>
                    
                    <div class="info-group">
                        <span class="info-label">Vehicle Selected</span>
                        <div class="info-value" style="color: #3b82f6; font-weight: 700;">
                            <?= htmlspecialchars($booking['vehicle_model']) ?> 
                            <span style="color: #64748b; font-size: 15px; font-weight: 500;">
                                (Qty: <?= isset($booking['quantity']) ? $booking['quantity'] : 1 ?>)
                            </span>
                        </div>
                    </div>
                </div>

                <?php if (isset($booking['need_driver']) && $booking['need_driver'] === 'Yes'): ?>
                    <div class="driver-alert">
                        <i class="fa-solid fa-user-tie"></i>
                        <div>
                            <span style="display: block; font-weight: 800; color: #065f46; font-size: 20px;">PROFESSIONAL DRIVER REQUESTED</span>
                            <span style="color: #059669; font-size: 15px;">Customer has requested a professional driver for this booking. Please arrange accordingly.</span>
                        </div>
                    </div>
                <?php endif; ?>

                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px dashed #e2e8f0;">
                    <div class="info-group">
                        <span class="info-label">Pickup Location</span>
                        <div class="info-value"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($booking['pickup_location']) ?></div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 15px;">
                        <div class="info-group">
                            <span class="info-label">Pickup Date & Time</span>
                            <div class="info-value"><?= date('M d, Y - h:i A', strtotime($booking['pickup_date'])) ?></div>
                        </div>
                        <div class="info-group">
                            <span class="info-label">Dropoff Date & Time</span>
                            <div class="info-value"><?= date('M d, Y - h:i A', strtotime($booking['dropoff_date'])) ?></div>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px dashed #e2e8f0;">
                    <div class="info-group">
                        <span class="info-label">Driver's License (<?= htmlspecialchars($booking['dl_number']) ?>)</span>
                        <?php if ($booking['license_file']): ?>
                            <a href="../<?= htmlspecialchars($booking['license_file']) ?>" target="_blank" class="dl-link">
                                <i class="fa-solid fa-file-pdf"></i> View Uploaded Document
                            </a>
                        <?php else: ?>
                            <div class="info-value" style="color:#e11d48;">No document uploaded</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <h2><i class="fa-solid fa-wallet"></i> Status & Payment</h2>
                
                <div class="info-group" style="background: #f8fafc; padding: 20px; border-radius: 15px; margin-bottom: 30px;">
                    <span class="info-label">Total Amount</span>
                    <div class="info-value" style="font-size: 32px; color: #0f172a;">₹<?= number_format((float)($booking['total_amount'] ?? 0), 0) ?></div>
                    <small style="color:#64748b;">Method: <?= strtoupper(htmlspecialchars($booking['payment_method'])) ?></small>
                </div>

                <div class="info-group">
                    <span class="info-label">Current Status</span>
                    <span class="status-badge status-<?= htmlspecialchars($booking['status'] ?: 'pending') ?>">
                        <?= strtoupper($booking['status'] ?: 'pending') ?>
                    </span>
                </div>

                <form method="POST" style="margin-top: 40px;">
                    <span class="info-label">Update Booking Status</span>
                    <input type="hidden" name="booking_id" value="<?= (int)$booking['id'] ?>">
                    <select name="status">
                        <option value="pending" <?= ($booking['status'] === 'pending' ? 'selected' : '') ?>>Pending</option>
                        <option value="approved" <?= ($booking['status'] === 'approved' ? 'selected' : '') ?>>Approved</option>
                        <option value="rejected" <?= ($booking['status'] === 'rejected' ? 'selected' : '') ?>>Rejected</option>
                        <option value="completed" <?= ($booking['status'] === 'completed' ? 'selected' : '') ?>>Completed</option>
                    </select>
                    <button type="submit" class="btn-update">Apply Changes</button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>