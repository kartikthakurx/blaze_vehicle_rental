<?php
require_once __DIR__ . '/../back/db.php';
require_once __DIR__ . '/../back/admin_auth.php';

// Handle Delete Logic
if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM vehicles WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: manage_vehicles.php?msg=deleted");
    exit;
}

$vehicles = $pdo->query("SELECT * FROM vehicles ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Fleet - Blaze Rental</title>
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
            border-radius: 12px; transition: 0.3s; font-size: 18px;
        }
        .sidebar a:hover, .sidebar a.active { background: var(--sidebar-hover); color: white; }
        /* This rule makes the 'active' link blue */
        .sidebar a.active { background: rgba(56, 189, 248, 0.15); color: var(--accent-color); }

        /* Main Content */
        .main { margin-left: 280px; padding: 50px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 35px; }
        .header h1 { font-size: 42px; font-weight: 700; color: #0f172a; }

        .btn-add { 
            background: var(--sidebar-bg); color: white; border: none; 
            padding: 15px 30px; border-radius: 12px; cursor: pointer; 
            font-size: 17px; text-decoration: none; font-weight: 600;
        }

        /* Table Container */
        .table-container { 
            background: white; padding: 35px; border-radius: 25px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow-x: auto;
        }
        table { width: 100%; border-collapse: collapse; min-width: 1000px; }
        
        th { 
            text-align: left; padding: 20px; color: #64748b; font-size: 15px; 
            text-transform: uppercase; border-bottom: 2px solid #f1f5f9; font-weight: 600;
        }
        
        td { 
            padding: 25px 20px; border-bottom: 1px solid #f1f5f9; 
            font-size: 19px; color: var(--text-main); 
        }

        .car-img { width: 120px; height: 75px; object-fit: cover; border-radius: 12px; background: #f1f5f9; }
        .car-name { font-size: 21px; color: #000; font-weight: 600; display: block; }
        .specs-text { color: #64748b; font-size: 16px; }

        .btn-action { 
            width: 45px; height: 45px; background: #f1f5f9; color: #1e293b; 
            display: inline-flex; align-items: center; justify-content: center; 
            border-radius: 12px; text-decoration: none; font-size: 20px; 
        }
        .btn-edit { background: #dbeafe; color: #1e40af; }
        .btn-delete { background: #fff1f2; color: #e11d48; margin-left: 8px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2><i class="fa-solid fa-car-rear"></i> Admin</h2>
        <a href="dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
        
        <a href="bookings.php"><i class="fa-solid fa-calendar-check"></i> Bookings</a>
        
        <a href="users.php"><i class="fa-solid fa-users"></i> Users</a>
        <a href="add_vehicle.php"><i class="fa-solid fa-plus-circle"></i> Add Vehicle</a>
        
        <a href="manage_vehicles.php" class="active"><i class="fa-solid fa-car"></i> Manage Fleet</a>
        
        <a href="messages.php"><i class="fa-solid fa-envelope"></i> Messages</a>
        <a href="../back/logout.php" style="margin-top: 50px; color: #f87171 !important;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a> 
    </div>

    <div class="main">
        <div class="header">
            <h1>Vehicle Fleet</h1>
            <a href="add_vehicle.php" class="btn-add"><i class="fa-solid fa-plus"></i> Add New Car</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>Vehicle Details</th>
                        <th>Specifications</th>
                        <th>Rent (₹/hr)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vehicles as $v): ?>
                    <tr>
                        <td>
                            <?php if(!empty($v['image'])): ?>
                                <img src="../uploads/<?= htmlspecialchars($v['image']) ?>" class="car-img" alt="car">
                            <?php else: ?>
                                <div class="car-img" style="display:flex; align-items:center; justify-content:center; background:#e2e8f0; color:#94a3b8;">
                                    <i class="fa-solid fa-car"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="car-name"><?= htmlspecialchars($v['vehicle_name']) ?></span>
                        </td>
                        <td>
                            <span class="specs-text"><?= htmlspecialchars($v['engine_details']) ?></span><br>
                            <small style="color:#94a3b8;"><?= htmlspecialchars($v['transmission']) ?></small>
                        </td>
                        <td style="font-weight: 700;">₹<?= number_format((float)$v['price_per_hour']) ?></td>
                        <td>
                            <a href="edit_vehicle.php?id=<?= $v['id'] ?>" class="btn-action btn-edit"><i class="fa-solid fa-pen"></i></a>
                            <a href="manage_vehicles.php?delete_id=<?= $v['id'] ?>" class="btn-action btn-delete" onclick="return confirm('Delete this vehicle?')">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>