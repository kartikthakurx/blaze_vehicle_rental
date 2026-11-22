<?php
// add_vehicle.php
require_once __DIR__ . '/../back/db.php';
require_once __DIR__ . '/../back/admin_auth.php';

$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['vehicle_name'];
    $engine = $_POST['engine_details'];
    $trans = $_POST['transmission'];
    $fuel = $_POST['fuel_type'];
    $power = $_POST['power_details'];
    $price = $_POST['price_per_hour'];
    $description = $_POST['description']; 

    // Image upload karne ke liye helper function
    function saveImage($fileKey) {
        if (!empty($_FILES[$fileKey]['name'])) {
            $filename = time() . "_" . $_FILES[$fileKey]['name'];
            if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], "../assets/image/" . $filename)) {
                return "assets/image/" . $filename;
            }
        }
        return null;
    }

    // IMPORTANT: Naya Logic for 5 Images
    // 1. Bahar dikhne wali photo (High Res / Cover)
    $display_img = saveImage('display_image'); 
    
    // 2. Andar slider mein dikhne wali 4 photos
    $img1 = saveImage('image_path'); 
    $img2 = saveImage('image2');
    $img3 = saveImage('image3');
    $img4 = saveImage('image4');

    // Make sure we have at least the cover photo and one slider photo
    if ($display_img && $img1) {
        $sql = "INSERT INTO vehicles (vehicle_name, engine_details, transmission, fuel_type, power_details, price_per_hour, image_path, image2, image3, image4, description, display_image, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'available')";
        
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$name, $engine, $trans, $fuel, $power, $price, $img1, $img2, $img3, $img4, $description, $display_img])) {
            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Vehicle - Blaze Rental</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --accent-color: #38bdf8;
            --text-gray: #cbd5e1;
            --bg-light: #f1f5f9;
            --text-main: #1e293b; 
            --primary-blue: #3b82f6;
            --success-green: #10b981;
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
        .sidebar a.active { background: rgba(56, 189, 248, 0.15); color: var(--accent-color); }

        /* Centering Logic */
        .main { 
            margin-left: 280px; 
            padding: 60px; 
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        .header {
            width: 100%;
            max-width: 1100px;
            margin-bottom: 30px;
        }
        .header h1 { font-size: 42px; font-weight: 700; color: #0f172a; }

        .form-container { 
            background: white; 
            padding: 50px; 
            border-radius: 30px; 
            box-shadow: 0 15px 40px rgba(0,0,0,0.06); 
            width: 100%;
            max-width: 1100px;
        }

        .form-header { 
            display: flex; justify-content: space-between; align-items: center; 
            margin-bottom: 40px; border-bottom: 2px solid #f1f5f9; padding-bottom: 25px;
        }
        .form-header h2 { font-size: 28px; color: #1e293b; }
        .badge { background: #dbeafe; color: #1e40af; padding: 8px 16px; border-radius: 10px; font-size: 15px; font-weight: 600; }

        /* Grid Layout */
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 35px; }
        .full-width { grid-column: span 2; }

        .field-group { display: flex; flex-direction: column; gap: 12px; }
        label { font-size: 17px; font-weight: 600; color: #475569; }
        
        input, select, textarea { 
            padding: 18px; 
            border: 2px solid #e2e8f0; border-radius: 15px; 
            font-size: 18px; outline: none; transition: 0.2s;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            width: 100%;
        }
        input:focus, select:focus, textarea:focus { border-color: var(--primary-blue); box-shadow: 0 0 0 5px rgba(59, 130, 246, 0.1); }

        textarea { resize: vertical; }

        .upload-area { 
            border: 2px dashed #cbd5e1; padding: 40px; border-radius: 20px; 
            background: #f8fafc; text-align: center; transition: 0.3s;
        }

        .btn-save { 
            background: var(--sidebar-bg); color: white; border: none; 
            padding: 22px; border-radius: 15px; font-size: 20px; font-weight: 700; 
            cursor: pointer; transition: 0.3s; width: 100%; margin-top: 30px;
            letter-spacing: 0.5px;
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-save:hover { background: #0f172a; transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.15); }
        .btn-save:disabled { background: #64748b; cursor: not-allowed; transform: none; }

        /* Popup Animation */
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        .section-title {
            grid-column: span 2;
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 20px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #cbd5e1;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h2><i class="fa-solid fa-car-rear"></i> Admin</h2>
        <a href="dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
        <a href="bookings.php"><i class="fa-solid fa-calendar-check"></i> Bookings</a>
        <a href="users.php"><i class="fa-solid fa-users"></i> Users</a>
        <a href="add_vehicle.php" class="active"><i class="fa-solid fa-plus-circle"></i> Add Vehicle</a>
        <a href="manage_vehicles.php"><i class="fa-solid fa-car"></i> Manage Fleet</a>
        <a href="messages.php"><i class="fa-solid fa-envelope"></i> Messages</a>
        <a href="../back/logout.php" style="margin-top: 50px; color: #f87171 !important;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a> 
    </div>

    <div class="main">
        <div class="header">
            <h1>Vehicle Management</h1>
        </div>

        <div class="form-container">
            <div class="form-header">
                <h2>Add a New Vehicle to Fleet</h2>
                <span class="badge"><i class="fa-solid fa-shield-car"></i> SECURE ENTRY</span>
            </div>

            <form id="vehicleForm" action="add_vehicle.php" method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="field-group">
                        <label>Vehicle Name</label>
                        <input type="text" name="vehicle_name" placeholder="e.g., Ferrari F8 Tributo" required>
                    </div>

                    <div class="field-group">
                        <label>Engine Details</label>
                        <input type="text" name="engine_details" placeholder="e.g., 3.9L V8 Twin-Turbo" required>
                    </div>

                    <div class="field-group">
                        <label>Transmission Type</label>
                        <select name="transmission" required>
                            <option value="Automatic">Automatic </option>
                            <option value="Manual">Manual </option>
                            <option value="Both">Both </option>
                        </select>
                    </div>

                    <div class="field-group">
                        <label>Fuel Category</label>
                        <input type="text" name="fuel_type" placeholder="e.g., Premium Petrol" required>
                    </div>

                    <div class="field-group">
                        <label>Engine Horsepower (BHP)</label>
                        <input type="text" name="power_details" placeholder="e.g., 710 bhp" required>
                    </div>

                    <div class="field-group">
                        <label>Rental Rate (₹ per Hour)</label>
                        <input type="number" name="price_per_hour" placeholder="Enter amount" required>
                    </div>

                    <div class="field-group full-width">
                        <label>Vehicle Description</label>
                        <textarea 
                            name="description" 
                            rows="4" 
                            placeholder="Type the full vehicle details here..."></textarea>
                    </div>

                    <div class="section-title">
                        <i class="fa-solid fa-image"></i> High-Resolution Cover Image (Shows on Fleet Page)
                    </div>

                    <div class="field-group full-width">
                        <div class="upload-area" style="background: #eff6ff; border-color: #93c5fd;">
                            <i class="fa-solid fa-camera-retro" style="font-size: 40px; color: #3b82f6; margin-bottom: 15px; display: block;"></i>
                            <input type="file" name="display_image" accept="image/*" required>
                            <p style="font-size: 15px; color: #64748b; margin-top: 15px;">This is the main display picture for the card. Supported formats: JPG, PNG, WEBP</p>
                        </div>
                    </div>

                    <div class="section-title">
                        <i class="fa-solid fa-images"></i> Details Modal Gallery (Shows when User clicks 'Details')
                    </div>

                    <div class="field-group">
                        <label>Slider Image 1 (Required)</label>
                        <input type="file" name="image_path" accept="image/*" required>
                    </div>

                    <div class="field-group">
                        <label>Slider Image 2 (Optional)</label>
                        <input type="file" name="image2" accept="image/*">
                    </div>
                    
                    <div class="field-group">
                        <label>Slider Image 3 (Optional)</label>
                        <input type="file" name="image3" accept="image/*">
                    </div>
                    
                    <div class="field-group">
                        <label>Slider Image 4 (Optional)</label>
                        <input type="file" name="image4" accept="image/*">
                    </div>

                    <div class="full-width">
                        <button type="submit" id="submitBtn" class="btn-save">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span id="btnText">Upload Vehicle to Catalog</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if ($success): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const popup = document.createElement('div');
            popup.style.cssText = `
                position: fixed; top: 30px; right: 30px; background: #10b981; color: white; 
                padding: 20px 35px; border-radius: 16px; box-shadow: 0 15px 35px rgba(16,185,129,0.3); 
                z-index: 9999; display: flex; align-items: center; gap: 15px; 
                animation: slideIn 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55) forwards;
            `;
            popup.innerHTML = `
                <i class="fa-solid fa-circle-check" style="font-size: 28px;"></i>
                <div>
                    <strong style="display: block; font-size: 18px;">Success!</strong>
                    <span style="font-size: 14px; opacity: 0.9;">New vehicle added to live fleet.</span>
                </div>
            `;
            document.body.appendChild(popup);

            setTimeout(() => {
                popup.style.animation = "fadeOut 0.6s forwards";
                setTimeout(() => popup.remove(), 600);
            }, 4000);
        });
    </script>
    <?php endif; ?>

    <script>
        // Loading State Logic
        const form = document.getElementById('vehicleForm');
        const btn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');

        form.onsubmit = function() {
            btn.disabled = true;
            btnText.innerHTML = "Uploading Fleet Data...";
            btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> ` + btn.innerHTML;
        };
    </script>

</body>
</html>