<?php
session_start();

// 1. Path Fix: Folder structure ke hisaab se
if (file_exists('back/db.php')) {
    require_once 'back/db.php';
} else {
    die("Database file nahi mili! Check karein ki 'back/db.php' sahi jagah par hai.");
}

// ==========================================================
// BACKGROUND AJAX REQUESTS (RATING & CANCEL BOOKING)
// ==========================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $booking_id = filter_var($_POST['booking_id'], FILTER_SANITIZE_NUMBER_INT);

    // --- Action 1: Save Rating ---
    if ($_POST['action'] == 'save_rating') {
        $rating = filter_var($_POST['rating'], FILTER_SANITIZE_NUMBER_INT);
        if ($rating >= 1 && $rating <= 5) {
            try {
                $sql = "UPDATE bookings SET user_rating = ? WHERE id = ? AND user_id = ?";
                $stmt = $pdo->prepare($sql);
                if ($stmt->execute([$rating, $booking_id, $user_id])) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Database update failed.']);
                }
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Database error.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid rating.']);
        }
        exit;
    }

    // --- Action 2: Cancel Booking ---
    if ($_POST['action'] == 'cancel_booking') {
        try {
            $sql = "UPDATE bookings SET status = 'CANCELLED BY USER' WHERE id = ? AND user_id = ?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$booking_id, $user_id])) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to cancel booking.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Database error.']);
        }
        exit;
    }
}
// ==========================================================


// 2. Check if logged in (NO REDIRECT, JUST CHECK)
$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;

$username = $_SESSION['username'] ?? 'Guest';
$user_initial = strtoupper(substr($username, 0, 1));

// 3. Database Query (Sirf tab chalegi agar user logged in hai)
$my_bookings = [];
if ($is_logged_in) {
    try {
        $sql = "SELECT b.*, MIN(v.image_path) as image_path 
                FROM bookings b 
                LEFT JOIN vehicles v ON b.vehicle_model = v.vehicle_name 
                WHERE b.user_id = ? 
                GROUP BY b.id 
                ORDER BY b.created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $my_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Luxury Rides - Blaze Rental</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #3b82f6; --accent: #60a5fa; --dark: #0f172a;
            --bg: #f1f5f9; --glass: rgba(255, 255, 255, 0.7);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: radial-gradient(circle at top right, #e2e8f0, #f8fafc); min-height: 100vh; color: var(--dark); }
        .wrapper { max-width: 1000px; margin: 50px auto; padding: 0 25px; }

        .top-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        .user-meta { display: flex; align-items: center; gap: 12px; background: white; padding: 8px 15px; border-radius: 50px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .user-avatar { width: 35px; height: 35px; background: var(--primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; }
        
        .dashboard-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 50px; }
        .dashboard-header h1 { font-size: 48px; font-weight: 800; letter-spacing: -2px; line-height: 1; }
        .dashboard-header h1 span { display: block; font-size: 18px; font-weight: 600; color: var(--primary); letter-spacing: 4px; text-transform: uppercase; margin-bottom: 10px; }

        /* --- NEW: Overlay & Blur Styles --- */
        .content-wrapper-relative {
            position: relative;
            border-radius: 35px;
            min-height: 400px;
        }
        
        .form-blurred {
            filter: blur(8px);
            opacity: 0.5;
            pointer-events: none;
            user-select: none;
        }

        .login-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            z-index: 10;
            background: rgba(241, 245, 249, 0.3);
            border-radius: 35px;
        }

        .overlay-box {
            background: white; padding: 40px; border-radius: 20px; text-align: center;
            box-shadow: 0 20px 50px rgba(0,0,0,0.15); transform: translateY(-10px);
        }
        .overlay-box i { font-size: 45px; color: #0f172a; margin-bottom: 15px; }
        .overlay-box h3 { font-size: 24px; font-weight: 800; color: #0f172a; margin-bottom: 10px; }
        .overlay-box p { color: #64748b; margin-bottom: 25px; }
        
        .btn-overlay-login {
            background: #c90e1c; color: white; padding: 12px 30px; border-radius: 12px;
            text-decoration: none; font-weight: 700; font-size: 16px; transition: 0.3s; display: inline-block;
        }
        .btn-overlay-login:hover { background: #0f172a; transform: translateY(-2px); color: white; }
        /* ---------------------------------- */

        .booking-card {
            background: var(--glass); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 35px; padding: 35px; margin-bottom: 35px; display: flex; gap: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08); transition: 0.5s; position: relative;
        }
        .booking-card:hover { transform: scale(1.02) translateY(-10px); background: rgba(255, 255, 255, 0.9); }
        
        .image-container { width: 300px; height: 200px; flex-shrink: 0; }
        .image-container img { width: 100%; height: 100%; object-fit: cover; border-radius: 25px; }

        .content { flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between; }
        .top-row { display: flex; justify-content: space-between; align-items: flex-start; }
        .top-row h2 { font-size: 30px; font-weight: 800; letter-spacing: -0.5px; }

        .status-tag { padding: 8px 20px; border-radius: 15px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
        .status-pending { background: #fffbeb; color: #d97706; border: 1px solid #fef3c7; }
        .status-completed { background: #f0fdf4; color: #16a34a; border: 1px solid #dcfce7; }
        .status-approved { background: #eff6ff; color: #2563eb; border: 1px solid #dbeafe; }
        .status-cancelled { background: #fef2f2; color: #ef4444; border: 1px solid #fee2e2; }

        .meta-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin: 20px 0; }
        .meta-item { display: flex; align-items: center; gap: 10px; font-size: 15px; color: #475569; font-weight: 500; }
        .meta-item i { width: 35px; height: 35px; background: white; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: var(--primary); }

        .action-row { display: flex; justify-content: space-between; align-items: center; padding-top: 20px; border-top: 1px solid rgba(0,0,0,0.05); }
        .price-display { font-size: 26px; font-weight: 800; }
        .price-display span { font-size: 14px; color: #94a3b8; font-weight: 400; }

        .btn-view { background: var(--dark); color: white; padding: 12px 25px; border-radius: 15px; border: none; font-weight: 600; cursor: pointer; transition: 0.3s; display: flex; align-items: center; gap: 10px; }
        .btn-view:hover { background: var(--primary); }
        .btn-view:disabled { cursor: not-allowed; }

        .star-rating { display: flex; align-items: center; gap: 5px; }
        .star-rating .star { color: #cbd5e1; font-size: 22px; cursor: pointer; transition: 0.2s; }
        .star-rating .star:hover, .star-rating .star.hover { color: #fca019; transform: scale(1.1); }
        .star-rating .star.fa-solid { color: #fca019; } 
        .rating-text { font-size: 13px; margin-left: 10px; color: #64748b; font-weight: 600; }

        @media (max-width: 850px) {
            .booking-card { flex-direction: column; gap: 25px; padding: 25px; }
            .image-container { width: 100%; height: 220px; }
        }
    </style>
</head>
<body>

    <div class="wrapper">
        <nav class="top-nav">
            <a href="index.php" style="text-decoration: none; font-weight: 600; color: var(--dark);"><i class="fa-solid fa-arrow-left"></i> Home</a>
            <div class="user-meta">
                <div class="user-avatar"><?= $user_initial ?></div>
                <span style="font-weight:700; font-size:14px;"><?= htmlspecialchars($username) ?></span>
                
                <?php if($is_logged_in): ?>
                    <a href="back/logout.php" style="color:#f87171; font-size:12px; font-weight:700; text-decoration:none; margin-left:10px;">Logout</a>
                <?php else: ?>
                    <a href="signin.html" style="color:#c90e1c; font-size:12px; font-weight:700; text-decoration:none; margin-left:10px;">Sign In</a>
                <?php endif; ?>
            </div>
        </nav>

        <header class="dashboard-header">
            <div>
                <h1><span>Activity</span>My Rides</h1>
            </div>
        </header>

        <div class="content-wrapper-relative">
            
            <?php if (!$is_logged_in): ?>
                <div class="login-overlay">
                    <div class="overlay-box">
                        <i class="fa-solid fa-lock"></i>
                        <h3>Login Required</h3>
                        <p>Please sign in to view and manage your rides.</p>
                        <a href="signin.html" class="btn-overlay-login">Sign In Now</a>
                    </div>
                </div>
            <?php endif; ?>

            <div class="booking-feed <?php echo !$is_logged_in ? 'form-blurred' : ''; ?>">
                
                <?php if (empty($my_bookings)): ?>
                    <div style="text-align: center; padding: 60px; background: white; border-radius: 35px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
                        <i class="fa-solid fa-car-side" style="font-size: 60px; color: #e2e8f0; margin-bottom: 20px;"></i>
                        <h2 style="color: #64748b;">No bookings found</h2>
                        <p style="margin-bottom: 30px; color: #94a3b8;">You haven't rented any luxury cars yet.</p>
                        <a href="vehicles.php" style="background: var(--primary); color: white; padding: 15px 30px; border-radius: 15px; text-decoration: none; font-weight: 700;">Explore Fleet</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($my_bookings as $booking): 
                        $raw_status = strtolower($booking['status']);
                        $status_class = (strpos($raw_status, 'cancel') !== false) ? 'status-cancelled' : 'status-' . $raw_status;
                    ?>
                    <div class="booking-card">
                        <div class="image-container">
                            <img src="<?= !empty($booking['image_path']) ? $booking['image_path'] : 'assets/image/default-car.jpg' ?>" alt="Vehicle">
                        </div>
                        <div class="content">
                            <div class="top-row">
                                <h2><?= htmlspecialchars($booking['vehicle_model']) ?></h2>
                                <span class="status-tag <?= $status_class ?>"><?= htmlspecialchars($booking['status']) ?></span>
                            </div>
                            
                                <div class="meta-grid">
                                    <div class="meta-item">
                                        <i class="fa-solid fa-calendar"></i> 
                                        <?= date('d M', strtotime($booking['pickup_date'])) ?> - <?= date('d M, Y', strtotime($booking['dropoff_date'])) ?>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fa-solid fa-clock"></i> 
                                        <?= $booking['total_hours'] ?> Hours Rental
                                    </div>
                                    
                                    <div class="meta-item">
                                        <i class="fa-solid fa-car"></i> 
                                        <?= isset($booking['quantity']) ? $booking['quantity'] : 1 ?> Vehicle(s) Booked
                                    </div>

                                    <?php if (isset($booking['need_driver']) && $booking['need_driver'] === 'Yes'): ?>
                                        <div class="meta-item" style="color: #10b981; font-weight: 700;">
                                            <i class="fa-solid fa-user-tie" style="color: #10b981; background: #ecfdf5;"></i> Driver Included
                                        </div>
                                    <?php else: ?>
                                        <div class="meta-item">
                                            <i class="fa-solid fa-shield-check"></i> Fully Insured
                                        </div>
                                    <?php endif; ?>
                                </div>

                            <div class="action-row">
                                <div class="price-display">₹<?= number_format($booking['total_amount']) ?> <span>/ total</span></div>
                                
                                <?php if (strtolower($booking['status']) == 'completed'): ?>
                                    <div class="star-rating" data-booking-id="<?= $booking['id'] ?>">
                                        <?php 
                                        $current_rating = isset($booking['user_rating']) ? $booking['user_rating'] : 0;
                                        for ($i = 1; $i <= 5; $i++): 
                                            $starClass = $i <= $current_rating ? 'fa-solid' : 'fa-regular';
                                        ?>
                                            <i class="<?= $starClass ?> fa-star star" data-value="<?= $i ?>"></i>
                                        <?php endfor; ?>
                                        <span class="rating-text" style="<?= $current_rating > 0 ? 'color: #16a34a;' : '' ?>">
                                            <?= $current_rating > 0 ? 'Thanks for rating!' : 'Rate this ride' ?>
                                        </span>
                                    </div>
                                <?php elseif (strpos(strtolower($booking['status']), 'cancel') !== false): ?>
                                    <button class="btn-view" style="background: #e2e8f0; color: #64748b;" disabled>
                                        <i class="fa-solid fa-ban"></i> Cancelled
                                    </button>
                                <?php else: ?>
                                    <button class="btn-view" style="background: #fee2e2; color: #ef4444;" onclick="cancelBooking(<?= $booking['id'] ?>, this)">
                                        <i class="fa-solid fa-trash-can"></i> Cancel Booking 
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
        </div>
    </div>

    <script>
    // --- 1. Cancel Booking Script (AJAX) ---
    function cancelBooking(bookingId, btnElement) {
        if (confirm("Kya aap waqai is booking ko cancel karna chahte hain?")) {
            
            const originalText = btnElement.innerHTML;
            btnElement.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
            btnElement.disabled = true;

            fetch('mybookings.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=cancel_booking&booking_id=${bookingId}`
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    btnElement.style.background = "#e2e8f0";
                    btnElement.style.color = "#64748b";
                    btnElement.innerHTML = '<i class="fa-solid fa-ban"></i> Cancelled';
                    
                    const cardContent = btnElement.closest('.content');
                    const statusTag = cardContent.querySelector('.status-tag');
                    statusTag.className = 'status-tag status-cancelled';
                    statusTag.innerText = 'CANCELLED BY USER';

                    alert("Booking successfully cancelled!");
                } else {
                    alert("Error: " + data.message);
                    btnElement.innerHTML = originalText;
                    btnElement.disabled = false;
                }
            })
            .catch(error => {
                alert("Error processing request!");
                btnElement.innerHTML = originalText;
                btnElement.disabled = false;
            });
        }
    }

    // --- 2. Star Rating Interactive Script ---
    document.addEventListener('DOMContentLoaded', function() {
        const starContainers = document.querySelectorAll('.star-rating');

        starContainers.forEach(container => {
            const stars = container.querySelectorAll('.star');
            const bookingId = container.dataset.bookingId;
            const textSpan = container.querySelector('.rating-text');

            stars.forEach(star => {
                star.addEventListener('mouseover', function() {
                    const value = this.dataset.value;
                    stars.forEach(s => {
                        if (s.dataset.value <= value) {
                            s.classList.remove('fa-regular');
                            s.classList.add('fa-solid');
                            s.style.color = "#fca019";
                        }
                    });
                });

                star.addEventListener('mouseout', function() {
                    const currentValue = container.dataset.currentRating || 0;
                    stars.forEach(s => {
                        if (s.dataset.value > currentValue) {
                            s.classList.remove('fa-solid');
                            s.classList.add('fa-regular');
                            s.style.color = "#cbd5e1";
                        }
                    });
                });

                star.addEventListener('click', function() {
                    const value = this.dataset.value;
                    
                    container.dataset.currentRating = value; 
                    stars.forEach(s => {
                        if (s.dataset.value <= value) {
                            s.classList.remove('fa-regular');
                            s.classList.add('fa-solid');
                            s.style.color = "#fca019";
                        } else {
                            s.classList.remove('fa-solid');
                            s.classList.add('fa-regular');
                            s.style.color = "#cbd5e1";
                        }
                    });
                    textSpan.innerText = "Saving...";
                    textSpan.style.color = "#fca019";
                    
                    fetch('mybookings.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `action=save_rating&booking_id=${bookingId}&rating=${value}`
                    })
                    .then(response => {
                        if (!response.ok) throw new Error("Network error");
                        return response.json();
                    })
                    .then(data => {
                        if(data.success) {
                            textSpan.innerText = "Thanks for rating!";
                            textSpan.style.color = "#16a34a"; 
                        } else {
                            textSpan.innerText = "Failed to save.";
                            textSpan.style.color = "#ef4444"; 
                        }
                    })
                    .catch(error => {
                        textSpan.innerText = "Error connecting!";
                        textSpan.style.color = "#ef4444";
                    });
                });
            });
        });
    });
    </script>

</body>
</html>