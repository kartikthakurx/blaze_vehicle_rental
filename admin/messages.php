<?php
require_once __DIR__ . '/../back/db.php'; // Ensure db connection is included
require_once __DIR__ . '/../back/admin_auth.php';

// Delete Logic
if (isset($_POST['delete_id'])) {
    $id = (int)$_POST['delete_id'];
    if ($id > 0) {
        $stmt = $pdo->prepare('DELETE FROM contact_messages WHERE id = ?');
        $stmt->execute([$id]);
    }
    header('Location: /RENTALPROJECT/admin/messages.php'); 
    exit;
}

$rows = $pdo->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Messages - Blaze Rental</title>
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

        /* Table container */
        .table-container { 
            background: white; padding: 35px; border-radius: 25px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow-x: auto;
        }
        table { width: 100%; border-collapse: collapse; min-width: 1100px; }
        
        th { 
            text-align: left; padding: 20px; color: #64748b; font-size: 15px; 
            text-transform: uppercase; border-bottom: 2px solid #f1f5f9; font-weight: 600;
        }
        
        td { 
            padding: 25px 20px; border-bottom: 1px solid #f1f5f9; 
            font-size: 17px; color: var(--text-main); vertical-align: top;
        }

        .sender-name { font-size: 19px; color: #000; font-weight: 600; display: block; }
        .sender-email { color: #3b82f6; font-size: 15px; text-decoration: none; }
        
        /* UPDATED: Message text wrapping rules */
        .msg-text { 
            color: #475569; 
            line-height: 1.6; 
            max-width: 400px; 
            overflow-wrap: break-word; 
            word-wrap: break-word; 
            word-break: break-all; 
            white-space: normal;
        }
        
        .subject-badge { font-weight: 600; color: #0f172a; display: block; margin-bottom: 5px; }

        /* Action Buttons */
        .btn-delete { 
            width: 45px; height: 45px; background: #fff1f2; color: #e11d48; 
            border: none; display: inline-flex; align-items: center; justify-content: center; 
            border-radius: 12px; cursor: pointer; font-size: 18px; transition: 0.2s;
        }
        .btn-delete:hover { background: #ffe4e6; transform: scale(1.05); }

        /* Empty State */
        .empty-state { text-align: center; padding: 50px; color: #94a3b8; }
        .empty-state i { font-size: 64px; margin-bottom: 20px; display: block; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2><i class="fa-solid fa-car-rear"></i> Admin</h2>
        <a href="dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
        <a href="bookings.php"><i class="fa-solid fa-calendar-check"></i> Bookings</a>
        <a href="users.php"><i class="fa-solid fa-users"></i> Users</a>
        <a href="add_vehicle.php"><i class="fa-solid fa-plus-circle"></i> Add Vehicle</a>
        <a href="manage_vehicles.php" ><i class="fa-solid fa-car"></i> Manage Fleet</a>
        <a href="messages.php" class="active"><i class="fa-solid fa-envelope"></i> Messages</a>
        <a href="../back/logout.php" style="margin-top: 50px; color: #f87171 !important;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a> 
    </div>

    <div class="main">
        <div class="header">
            <h1>Contact Messages</h1>
        </div>

        <div class="table-container">
            <?php if (empty($rows)): ?>
                <div class="empty-state">
                    <i class="fa-solid fa-inbox"></i>
                    <p>No messages received yet.</p>
                </div>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Sender</th>
                        <th>Subject & Message</th>
                        <th>Received At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                    <tr>
                        <td style="color: #64748b;">#<?= (int)$r['id'] ?></td>
                        <td>
                            <span class="sender-name"><?= htmlspecialchars($r['fullname']) ?></span>
                            <a href="mailto:<?= htmlspecialchars($r['email']) ?>" class="sender-email"><?= htmlspecialchars($r['email']) ?></a>
                            <div style="font-size: 14px; color: #94a3b8; margin-top: 5px;"><?= htmlspecialchars($r['phone'] ?? '') ?></div>
                        </td>
                        <td>
                            <span class="subject-badge"><?= htmlspecialchars($r['subject']) ?></span>
                            <div class="msg-text"><?= nl2br(htmlspecialchars($r['message'])) ?></div>
                        </td>
                        <td style="white-space: nowrap; font-size: 15px; color: #64748b;">
                            <?= date('M d, Y', strtotime($r['created_at'])) ?><br>
                            <?= date('H:i A', strtotime($r['created_at'])) ?>
                        </td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Permanently delete this message?')">
                                <input type="hidden" name="delete_id" value="<?= (int)$r['id'] ?>">
                                <button type="submit" class="btn-delete">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>