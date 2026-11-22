<?php
// admin/bookings.php
require_once __DIR__ . '/../back/db.php';
require_once __DIR__ . '/../back/admin_auth.php';

// Get all bookings with search and filter
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';

$sql = "SELECT * FROM bookings WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (fullname LIKE ? OR email LIKE ? OR vehicle_model LIKE ? OR pickup_location LIKE ?)";
    $search_param = "%$search%";
    $params = [$search_param, $search_param, $search_param, $search_param];
}

if ($status_filter) {
    $sql .= " AND status = ?";
    $params[] = $status_filter;
}

$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Blaze Rental</title>
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
            font-weight: 400; /* Normal weight for everything */
        }

        /* Sidebar */
        .sidebar { 
            position: fixed; left: 0; top: 0; width: 280px; height: 100vh; 
            background: var(--sidebar-bg); color: white; padding: 30px 20px; 
            z-index: 1000;
        }
        .sidebar h2 { font-size: 26px; margin-bottom: 40px; font-weight: 700; display: flex; align-items: center; gap: 12px; }
        .sidebar a { 
            display: flex; align-items: center; gap: 15px; color: var(--text-gray); 
            text-decoration: none; padding: 15px 20px; margin-bottom: 10px; 
            border-radius: 12px; transition: 0.3s; font-size: 18px; font-weight: 400;
        }
        .sidebar a:hover, .sidebar a.active { background: var(--sidebar-hover); color: white; }
        .sidebar a.active { background: rgba(56, 189, 248, 0.15); color: var(--accent-color); }

        /* Main Content */
        .main { margin-left: 280px; padding: 50px; }
        .header h1 { font-size: 42px; font-weight: 700; margin-bottom: 35px; color: #0f172a; }

        /* Filters */
        .filters { 
            background: white; padding: 25px; border-radius: 20px; 
            margin-bottom: 40px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }
        .filters input, .filters select { 
            padding: 15px 20px; border: 2px solid #e2e8f0; border-radius: 12px; 
            font-size: 17px; min-width: 250px; outline: none; font-weight: 400;
        }
        .btn-filter { background: var(--sidebar-bg); color: white; border: none; padding: 15px 30px; border-radius: 12px; cursor: pointer; font-size: 17px; }

        /* Table Design */
        .table-container { 
            background: white; padding: 35px; border-radius: 25px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow-x: auto;
        }
        table { width: 100%; border-collapse: collapse; min-width: 1200px; }
        
        th { 
            text-align: left; padding: 20px; color: #64748b; font-size: 15px; 
            text-transform: uppercase; border-bottom: 2px solid #f1f5f9; font-weight: 600;
        }
        
        td { 
            padding: 25px 20px; border-bottom: 1px solid #f1f5f9; 
            font-size: 19px; color: var(--text-main); font-weight: 400; vertical-align: top;
        }

        .cust-name { font-size: 21px; color: #000; font-weight: 400; display: block; margin-bottom: 2px; }
        .loc-text { color: #3b82f6; display: flex; align-items: center; gap: 8px; font-size: 18px; }

        /* Status Pill */
        .status { 
            padding: 8px 18px; border-radius: 30px; font-size: 14px; 
            font-weight: 500; text-transform: uppercase; display: inline-block;
        }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #dcfce7; color: #166534; }
        .status-rejected { background: #fee2e2; color: #991b1b; }

        .btn-action { 
            width: 45px; height: 45px; background: #f1f5f9; color: #1e293b; 
            display: inline-flex; align-items: center; justify-content: center; 
            border-radius: 12px; text-decoration: none; font-size: 20px; 
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2><i class="fa-solid fa-car-rear"></i> Admin</h2>
        <a href="dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
        <a href="bookings.php" class="active"><i class="fa-solid fa-calendar-check"></i> Bookings</a>
        <a href="users.php"><i class="fa-solid fa-users"></i> Users</a>
        <a href="add_vehicle.php"><i class="fa-solid fa-plus-circle"></i> Add Vehicle</a>
        <a href="manage_vehicles.php" ><i class="fa-solid fa-car"></i> Manage Fleet</a>
        <a href="messages.php"><i class="fa-solid fa-envelope"></i> Messages</a>
        <a href="../back/logout.php" style="margin-top: 50px; color: #f87171 !important;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a> 
    </div>

    <div class="main">
        <div class="header">
            <h1>Manage Bookings</h1>
        </div>

        <div class="filters">
            <form method="GET">
                <input type="text" name="search" placeholder="Search customer, car or location..." value="<?= htmlspecialchars($search) ?>">
                <select name="status">
                    <option value="">All Statuses</option>
                    <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= $status_filter === 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="rejected" <?= $status_filter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                    <option value="cancelled by user" <?= $status_filter === 'cancelled by user' ? 'selected' : '' ?>>Cancelled by User</option>
                </select>
                <button type="submit" class="btn-filter">Apply Filter</button>
            </form>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Vehicle Details</th>
                        <th>Pickup Location</th>
                        <th>Timing</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td>#<?= $booking['id'] ?></td>
                        <td>
                            <span class="cust-name"><?= htmlspecialchars($booking['fullname']) ?></span>
                            <small style="color:#64748b; font-size: 14px;"><?= htmlspecialchars($booking['phone']) ?></small>
                        </td>
                        
                        <td>
                            <strong style="color: #0f172a; font-size: 20px; display: block;">
                                <?= htmlspecialchars($booking['vehicle_model']) ?>
                            </strong>
                            <div style="font-size: 15px; color: #64748b; margin-top: 5px;">
                                <i class="fa-solid fa-car-side"></i> Qty: <b><?= isset($booking['quantity']) ? $booking['quantity'] : 1 ?></b>
                            </div>
                            
                            <?php if (isset($booking['need_driver']) && $booking['need_driver'] === 'Yes'): ?>
                                <div style="display: inline-block; background: #ecfdf5; color: #10b981; padding: 4px 10px; border-radius: 8px; font-size: 13px; font-weight: 700; margin-top: 8px; border: 1px solid #a7f3d0;">
                                    <i class="fa-solid fa-user-tie"></i> Driver Required
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="loc-text">
                                <i class="fa-solid fa-location-dot"></i> 
                                <?= htmlspecialchars($booking['pickup_location'] ?? 'Not Set') ?>
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 16px; margin-bottom: 4px;"><span style="color:#10b981;">●</span> <?= date('M d, H:i', strtotime($booking['pickup_date'])) ?></div>
                            <div style="font-size: 16px; color: #94a3b8;"><span style="color:#ef4444;">●</span> <?= date('M d, H:i', strtotime($booking['dropoff_date'])) ?></div>
                        </td>
                        <td style="font-size: 20px; font-weight: 600;">₹<?= number_format((float)($booking['total_amount'] ?? 0), 0) ?></td>
                        <td>
                            <span class="status status-<?= htmlspecialchars($booking['status'] ?: 'pending') ?>">
                                <?= strtoupper($booking['status'] ?: 'pending') ?>
                            </span>
                        </td>
                        <td>
                            <a href="view_booking.php?id=<?= $booking['id'] ?>" class="btn-action"><i class="fa-solid fa-eye"></i></a>
                            <a href="delete_booking.php?id=<?= $booking['id'] ?>" class="btn-action" style="background:#fff1f2; color:#e11d48; margin-left:8px;" onclick="return confirm('Delete?')"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>