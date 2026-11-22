<?php
require_once __DIR__ . '/../back/db.php';
require_once __DIR__ . '/../back/admin_auth.php';

// If no ID is provided in the URL, send them back to the fleet page
if (!isset($_GET['id'])) {
    header("Location: manage_vehicles.php");
    exit;
}

$id = (int)$_GET['id'];
$success = false;

// Handle the Form Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['vehicle_name'];
    $engine = $_POST['engine_details'];
    $trans = $_POST['transmission'];
    $fuel = $_POST['fuel_type'];
    $power = $_POST['power_details'];
    $price = $_POST['price_per_hour'];
    $description = $_POST['description']; 

    // Helper function to handle multiple image uploads
    function handleUpload($fileKey, $currentPath) {
        if (!empty($_FILES[$fileKey]['name'])) {
            $filename = time() . "_" . $_FILES[$fileKey]['name'];
            $upload_path = "../assets/image/" . $filename;
            if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $upload_path)) {
                return "assets/image/" . $filename;
            }
        }
        return $currentPath; // Return old path if no new file uploaded
    }

    // Fetch current paths to preserve them if no new upload
    $stmt = $pdo->prepare("SELECT image_path, image2, image3, image4, display_image FROM vehicles WHERE id = ?");
    $stmt->execute([$id]);
    $current = $stmt->fetch();

    $display_img = handleUpload('display_image', $current['display_image']); // NEW: Cover Image
    $img1 = handleUpload('image_path', $current['image_path']); // Slider 1
    $img2 = handleUpload('image2', $current['image2']); // Slider 2
    $img3 = handleUpload('image3', $current['image3']); // Slider 3
    $img4 = handleUpload('image4', $current['image4']); // Slider 4

    // Update query with all fields
    $sql = "UPDATE vehicles SET 
            vehicle_name=?, engine_details=?, transmission=?, 
            fuel_type=?, power_details=?, price_per_hour=?, 
            image_path=?, image2=?, image3=?, image4=?, 
            description=?, display_image=? WHERE id=?";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $engine, $trans, $fuel, $power, $price, $img1, $img2, $img3, $img4, $description, $display_img, $id]);
    $success = true;
}

// Fetch the current vehicle data to pre-fill the form
$stmt = $pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
$stmt->execute([$id]);
$vehicle = $stmt->fetch();

if (!$vehicle) {
    header("Location: manage_vehicles.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Vehicle - Blaze Rental</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root { --sidebar-bg: #1e293b; --sidebar-hover: #334155; --accent-color: #38bdf8; --text-gray: #cbd5e1; --bg-light: #f1f5f9; --text-main: #1e293b; --primary-blue: #3b82f6; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', 'Segoe UI', sans-serif; background: var(--bg-light); color: var(--text-main); font-size: 18px; }
        
        .sidebar { position: fixed; left: 0; top: 0; width: 280px; height: 100vh; background: var(--sidebar-bg); color: white; padding: 30px 20px; z-index: 1000; }
        .sidebar h2 { font-size: 26px; margin-bottom: 40px; font-weight: 700; display: flex; align-items: center; gap: 12px; }
        .sidebar a { display: flex; align-items: center; gap: 15px; color: var(--text-gray); text-decoration: none; padding: 15px 20px; margin-bottom: 10px; border-radius: 12px; transition: 0.3s; font-size: 18px; }
        .sidebar a:hover, .sidebar a.active { background: var(--sidebar-hover); color: white; }
        .sidebar a.active { background: rgba(56, 189, 248, 0.15); color: var(--accent-color); }

        .main { margin-left: 280px; padding: 60px; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: flex-start; }
        .header { width: 100%; max-width: 1100px; margin-bottom: 30px; }
        .header h1 { font-size: 42px; font-weight: 700; color: #0f172a; }

        .form-container { background: white; padding: 50px; border-radius: 30px; box-shadow: 0 15px 40px rgba(0,0,0,0.06); width: 100%; max-width: 1100px; }
        .form-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; border-bottom: 2px solid #f1f5f9; padding-bottom: 25px; }
        .form-header h2 { font-size: 28px; color: #1e293b; }
        .badge { background: #dbeafe; color: #1e40af; padding: 8px 16px; border-radius: 10px; font-size: 15px; font-weight: 600; }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 35px; }
        .full-width { grid-column: span 2; }
        .field-group { display: flex; flex-direction: column; gap: 12px; }
        label { font-size: 17px; font-weight: 600; color: #475569; }
        input, select, textarea { padding: 18px; border: 2px solid #e2e8f0; border-radius: 15px; font-size: 18px; outline: none; transition: 0.2s; }
        textarea { resize: vertical; font-family: inherit; }
        input:focus, textarea:focus, select:focus { border-color: var(--primary-blue); box-shadow: 0 0 0 5px rgba(59, 130, 246, 0.1); }
        
        /* Preview Grid Styling */
        .preview-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-top: 10px; }
        .preview-card { background: #f8fafc; padding: 15px; border-radius: 20px; border: 1px solid #e2e8f0; text-align: center; }
        .preview-card img { width: 100%; height: 110px; object-fit: cover; border-radius: 12px; margin-bottom: 12px; border: 1px solid #ddd; }
        .preview-card input[type="file"] { padding: 5px; font-size: 12px; width: 100%; border-radius: 8px; }
        .no-img { height: 110px; background: #e2e8f0; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 14px; margin-bottom: 12px; }

        /* NEW: Display Image Styling */
        .upload-area { border: 2px dashed #93c5fd; padding: 30px; border-radius: 20px; background: #eff6ff; text-align: center; }
        .upload-area img { max-height: 200px; object-fit: contain; border-radius: 10px; margin-bottom: 15px; border: 2px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }

        .btn-save { background: var(--primary-blue); color: white; border: none; padding: 22px; border-radius: 15px; font-size: 20px; font-weight: 700; cursor: pointer; transition: 0.3s; width: 100%; margin-top: 30px; }
        .btn-save:hover { background: #1d4ed8; transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.15); }

        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }

        .section-title { grid-column: span 2; font-size: 20px; font-weight: 700; color: #0f172a; margin-top: 20px; padding-bottom: 10px; border-bottom: 1px dashed #cbd5e1; }
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
            <h1>Edit Vehicle</h1>
            <a href="manage_vehicles.php" style="color: #64748b; text-decoration: none;"><i class="fa-solid fa-arrow-left"></i> Back to Fleet</a>
        </div>

        <div class="form-container">
            <div class="form-header">
                <h2>Update Details: <?= htmlspecialchars($vehicle['vehicle_name']) ?></h2>
                <span class="badge"><i class="fa-solid fa-pen-to-square"></i> EDIT MODE</span>
            </div>

            <form action="edit_vehicle.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="field-group">
                        <label>Vehicle Name</label>
                        <input type="text" name="vehicle_name" value="<?= htmlspecialchars($vehicle['vehicle_name']) ?>" required>
                    </div>

                    <div class="field-group">
                        <label>Engine Details</label>
                        <input type="text" name="engine_details" value="<?= htmlspecialchars($vehicle['engine_details']) ?>" required>
                    </div>

                    <div class="field-group">
                        <label>Transmission Type</label>
                        <select name="transmission" required>
                            <option value="Automatic" <?= $vehicle['transmission'] == 'Automatic' ? 'selected' : '' ?>>Automatic</option>
                            <option value="Manual" <?= $vehicle['transmission'] == 'Manual' ? 'selected' : '' ?>>Manual</option>
                            <option value="Electric" <?= $vehicle['transmission'] == 'Both' ? 'selected' : '' ?>>Both</option>
                        </select>
                    </div>

                    <div class="field-group">
                        <label>Fuel Category</label>
                        <input type="text" name="fuel_type" value="<?= htmlspecialchars($vehicle['fuel_type']) ?>" required>
                    </div>

                    <div class="field-group">
                        <label>Engine Horsepower (BHP)</label>
                        <input type="text" name="power_details" value="<?= htmlspecialchars($vehicle['power_details']) ?>" required>
                    </div>

                    <div class="field-group">
                        <label>Rental Rate (₹ per Hour)</label>
                        <input type="number" name="price_per_hour" value="<?= htmlspecialchars($vehicle['price_per_hour']) ?>" required>
                    </div>

                    <div class="field-group full-width">
                        <label>Description</label>
                        <textarea name="description" rows="4" placeholder="Enter vehicle description..."><?= htmlspecialchars($vehicle['description'] ?? '') ?></textarea>
                    </div>

                    <div class="section-title">
                        <i class="fa-solid fa-image"></i> High-Resolution Cover Image (Shows on Fleet Page)
                    </div>
                    <div class="field-group full-width">
                        <div class="upload-area">
                            <?php if(!empty($vehicle['display_image'])): ?>
                                <img src="../<?= htmlspecialchars($vehicle['display_image']) ?>" alt="Cover Image">
                            <?php else: ?>
                                <i class="fa-solid fa-camera-retro" style="font-size: 40px; color: #3b82f6; margin-bottom: 15px; display: block;"></i>
                                <p style="color: #ef4444; font-weight: bold; margin-bottom: 10px;">No Cover Image Set!</p>
                            <?php endif; ?>
                            <input type="file" name="display_image" accept="image/*">
                        </div>
                    </div>

                    <div class="section-title">
                        <i class="fa-solid fa-images"></i> Details Modal Gallery (Shows when User clicks 'Details')
                    </div>
                    <div class="field-group full-width">
                        <div class="preview-grid">
                            <div class="preview-card">
                                <label style="font-size:13px;">Slider Image 1</label>
                                <?php if(!empty($vehicle['image_path'])): ?>
                                    <img src="../<?= htmlspecialchars($vehicle['image_path']) ?>">
                                <?php else: ?><div class="no-img">No Image</div><?php endif; ?>
                                <input type="file" name="image_path" accept="image/*">
                            </div>
                            <div class="preview-card">
                                <label style="font-size:13px;">Slider Image 2</label>
                                <?php if(!empty($vehicle['image2'])): ?>
                                    <img src="../<?= htmlspecialchars($vehicle['image2']) ?>">
                                <?php else: ?><div class="no-img">No Image</div><?php endif; ?>
                                <input type="file" name="image2" accept="image/*">
                            </div>
                            <div class="preview-card">
                                <label style="font-size:13px;">Slider Image 3</label>
                                <?php if(!empty($vehicle['image3'])): ?>
                                    <img src="../<?= htmlspecialchars($vehicle['image3']) ?>">
                                <?php else: ?><div class="no-img">No Image</div><?php endif; ?>
                                <input type="file" name="image3" accept="image/*">
                            </div>
                            <div class="preview-card">
                                <label style="font-size:13px;">Slider Image 4</label>
                                <?php if(!empty($vehicle['image4'])): ?>
                                    <img src="../<?= htmlspecialchars($vehicle['image4']) ?>">
                                <?php else: ?><div class="no-img">No Image</div><?php endif; ?>
                                <input type="file" name="image4" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <div class="full-width">
                        <button type="submit" class="btn-save">Update Vehicle Fleet</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if ($success): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const popup = document.createElement('div');
            popup.style.cssText = `position: fixed; top: 30px; right: 30px; background: #10b981; color: white; padding: 20px 35px; border-radius: 16px; box-shadow: 0 15px 35px rgba(16,185,129,0.3); z-index: 9999; display: flex; align-items: center; gap: 15px; animation: slideIn 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55) forwards;`;
            popup.innerHTML = `<i class="fa-solid fa-circle-check" style="font-size: 28px;"></i><div><strong style="display: block; font-size: 18px;">Fleet Updated!</strong><span style="font-size: 14px; opacity: 0.9;">Vehicle details and images saved.</span></div>`;
            document.body.appendChild(popup);
            setTimeout(() => { popup.style.animation = "fadeOut 0.6s forwards"; setTimeout(() => popup.remove(), 600); }, 3000);
        });
    </script>
    <?php endif; ?>
</body>
</html>