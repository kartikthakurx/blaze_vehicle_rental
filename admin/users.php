<?php
// users.php
require_once __DIR__ . '/../back/db.php';
require_once __DIR__ . '/../back/admin_auth.php';

$users = $pdo->query("SELECT id, fullname, email, created_at, is_admin FROM users ORDER BY created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Blaze Rental</title>
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
        .sidebar a.active { background: rgba(56, 189, 248, 0.15); color: var(--accent-color); }

        /* Main Content */
        .main { margin-left: 280px; padding: 50px; }
        .header h1 { font-size: 42px; font-weight: 700; margin-bottom: 35px; color: #0f172a; }

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

        .user-name { font-size: 21px; color: #000; font-weight: 500; display: block; }
        .user-email { color: #64748b; font-size: 16px; }

        /* Role Badges */
        .badge { 
            padding: 8px 18px; border-radius: 30px; font-size: 14px; 
            font-weight: 500; text-transform: uppercase; display: inline-block;
        }
        .badge-admin { background: #dbeafe; color: #1e40af; }
        .badge-user { background: #f1f5f9; color: #475569; }

        /* Action Buttons */
        .btn-action { 
            width: 45px; height: 45px; background: #f1f5f9; color: #1e293b; 
            display: inline-flex; align-items: center; justify-content: center; 
            border-radius: 12px; text-decoration: none; font-size: 20px; transition: 0.2s;
        }
        .btn-delete { background: #fff1f2; color: #e11d48; margin-left: 8px; }
        .btn-action:hover { transform: translateY(-2px); opacity: 0.9; }

    </style>
</head>
<body>
    <div class="sidebar">
        <h2><i class="fa-solid fa-car-rear"></i> Admin</h2>
        <a href="dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
        <a href="bookings.php"><i class="fa-solid fa-calendar-check"></i> Bookings</a>
        <a href="users.php" class="active"><i class="fa-solid fa-users"></i> Users</a>
        <a href="add_vehicle.php"><i class="fa-solid fa-plus-circle"></i> Add Vehicle</a>
        <a href="manage_vehicles.php"><i class="fa-solid fa-car"></i> Manage Fleet</a>
        <a href="messages.php"><i class="fa-solid fa-envelope"></i> Messages</a>
        <a href="../back/logout.php" style="margin-top: 50px; color: #f87171 !important;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a> 
    </div>

    <div class="main">
        <div class="header">
            <h1>Manage Users</h1>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User Details</th>
                        <th>Email</th>
                        <th>Joined Date</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td style="color: #64748b;">#<?= $user['id'] ?></td>
                        <td>
                            <span class="user-name"><?= htmlspecialchars($user['fullname']) ?></span>
                        </td>
                        <td><span class="user-email"><?= htmlspecialchars($user['email']) ?></span></td>
                        <td><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <?php if ($user['is_admin']): ?>
                                <span class="badge badge-admin">Admin</span>
                            <?php else: ?>
                                <span class="badge badge-user">User</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($user['id'] != $_SESSION['admin_id']): ?>
                                <a href="delete_user.php?id=<?= $user['id'] ?>" 
                                   class="btn-action btn-delete" 
                                   onclick="return confirm('Delete this user permanently?')">
                                   <i class="fa-solid fa-user-xmark"></i>
                                </a>
                            <?php else: ?>
                                <span style="font-size: 14px; color: #94a3b8; font-style: italic;">You</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>