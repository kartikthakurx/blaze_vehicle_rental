<?php
// admin/dashboard.php
require_once __DIR__ . '/../back/db.php';
require_once __DIR__ . '/../back/admin_auth.php';

// Stats (realtime from DB)
$total_users     = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_bookings  = (int)$pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$total_admins    = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE is_admin = 1")->fetchColumn();

// Recent bookings (Limited to 10)
$recent = $pdo->query("SELECT * FROM bookings ORDER BY created_at DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Blaze Rental</title>
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
            font-weight: 400; 
        }

        /* Sidebar - Matches Bookings Exact */
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
        .header h1 { font-size: 42px; font-weight: 700; margin-bottom: 10px; color: #0f172a; }
        .header p { font-size: 20px; color: #64748b; margin-bottom: 40px; }

        /* Stats Grid - Styled like Bookings cards */
        .stats-grid { 
            display: grid; 
            grid-template-columns: repeat(3, 1fr); 
            gap: 25px; 
            margin-bottom: 50px; 
        }
        .stat-card { 
            background: white; 
            padding: 35px; 
            border-radius: 25px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        .stat-card h3 { color: #64748b; font-size: 15px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; }
        .stat-card .number { font-size: 48px; font-weight: 700; color: #0f172a; }

        /* Table Design - Matches Bookings Exact */
        .table-container { 
            background: white; padding: 35px; border-radius: 25px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow-x: auto;
        }
        .table-container h2 { margin-bottom: 25px; font-size: 24px; font-weight: 700; }
        table { width: 100%; border-collapse: collapse; }
        
        th { 
            text-align: left; padding: 20px; color: #64748b; font-size: 15px; 
            text-transform: uppercase; border-bottom: 2px solid #f1f5f9; font-weight: 600;
        }
        
        td { 
            padding: 25px 20px; border-bottom: 1px solid #f1f5f9; 
            font-size: 19px; color: var(--text-main); 
        }

        /* Status Pill - Matches Bookings Exact */
        .status { 
            padding: 8px 18px; border-radius: 30px; font-size: 14px; 
            font-weight: 500; text-transform: uppercase; display: inline-block;
        }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #dcfce7; color: #166534; }
        .status-rejected { background: #fee2e2; color: #991b1b; }

        .btn-view { 
            color: #3b82f6; text-decoration: none; font-weight: 500; font-size: 18px;
        }
        .btn-view:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2><i class="fa-solid fa-car-rear"></i> Admin</h2>
        <a href="dashboard.php" class="active"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
        <a href="bookings.php"><i class="fa-solid fa-calendar-check"></i> Bookings</a>
        <a href="users.php"><i class="fa-solid fa-users"></i> Users</a>
        <a href="add_vehicle.php"><i class="fa-solid fa-plus-circle"></i> Add Vehicle</a>
        <a href="manage_vehicles.php"><i class="fa-solid fa-car"></i> Manage Fleet</a>
        <a href="messages.php"><i class="fa-solid fa-envelope"></i> Messages</a>
        <a href="../back/logout.php" style="margin-top: 50px; color: #f87171 !important;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a> 
    </div>

    <div class="main">
        <div class="header">
            <h1>Dashboard</h1>
            <p>Welcome back, <?= htmlspecialchars($_SESSION['fullname'] ?? 'Admin') ?></p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Users</h3>
                <div class="number"><?= $total_users ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Bookings</h3>
                <div class="number"><?= $total_bookings ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Admins</h3>
                <div class="number"><?= $total_admins ?></div>
            </div>
        </div>

        <div class="table-container">
            <h2>Recent Bookings</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Vehicle</th>
                        <th>Pickup Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent as $booking): ?>
                    <tr>
                        <td style="color: #64748b;">#<?= $booking['id'] ?></td>
                        <td style="font-weight: 500;"><?= htmlspecialchars($booking['fullname']) ?></td>
                        <td><?= htmlspecialchars($booking['vehicle_model']) ?></td>
                        <td><?= date('j M, Y', strtotime($booking['pickup_date'])) ?></td>
                        <td>
                            <span class="status status-<?= htmlspecialchars($booking['status'] ?: 'pending') ?>">
                                <?= strtoupper($booking['status'] ?: 'pending') ?>
                            </span>
                        </td>
                        <td>
                            <a href="view_booking.php?id=<?= $booking['id'] ?>" class="btn-view">Details</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>