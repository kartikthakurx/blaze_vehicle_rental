<?php
// Optional: Start session to fetch the logged-in user's name
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include your database connection
require_once __DIR__ . '/back/db.php';

// Fetch all available vehicles from the database
$stmt = $pdo->query("SELECT * FROM vehicles WHERE status = 'available' ORDER BY created_at DESC");
$vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Vehicles - Blaze Vehicle Rental</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="/rentalproject/assets/css/style.css?v=1">
    <link rel="stylesheet" href="/rentalproject/assets/css/stylenew.css?v=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    /* ==========================================================================
    1. NEW NAVIGATION & HEADER STYLES
    ========================================================================== */
    :root {
        --primary: #c90e1c; 
        --brand-red: #c90e1c;
        --brand-orange: #fca019; 
        --dark-menu-bg: #1e1e1e;
        --text-on-dark: #fff;
    }

    body { 
        background-color: #f8f9fa; 
        font-family: 'Inter', sans-serif; 
        margin: 0;
        overflow-x: hidden; 
    }

    /* Transparent, Centered Header with Frosted Glass Effect */
    .header {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center; 
        padding: 25px 40px; 
        background-color: rgba(0, 0, 0, 0.7); 
        backdrop-filter: blur(10px); 
        -webkit-backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1); 
        z-index: 1000;
    }

    .header .logo a {
        font-size: 1.5rem;
        font-weight: bold;
        color: #fff; 
        text-decoration: none;
        margin: 0;
        line-height: 1; 
    }

    .menu-btn {
        background-color: var(--brand-red);
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 1.1rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 1001; 
        margin: 0; 
        line-height: 1; 
        transition: 0.3s;
    }
    
    .menu-btn:hover { background-color: #a70b16; }

    .menu-dropdown {
        position: fixed;
        top: 80px; 
        right: 40px;
        width: 280px;
        background-color: var(--dark-menu-bg);
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0px 10px 30px rgba(0,0,0,0.5);
        transform: translateY(-20px) scale(0.9);
        opacity: 0;
        visibility: hidden;
        transition: all 0.4s ease;
        z-index: 999;
        display: flex;
        flex-direction: column;
    }

    .menu-dropdown.open {
        transform: translateY(0) scale(1);
        opacity: 1;
        visibility: visible;
    }

    .menu-dropdown ul {
        list-style: none;
        padding: 0;
        margin: 0;
        width: 100%;
    }

    .menu-dropdown .nav-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 12px 15px;
        color: var(--text-on-dark);
        text-decoration: none !important;
        font-weight: 500;
        border-radius: 8px;
        transition: background-color 0.3s ease;
    }

    .menu-dropdown .nav-item i {
        font-size: 1.3rem;
        color: #ccc;
        min-width: 30px;
        text-align: center;
    }

    .menu-dropdown .nav-item:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .user-section {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-left: 15px;
        padding-right: 15px;
        width: 100%; 
        box-sizing: border-box; 
    }

    .welcome-text {
        color: #ccc;
        font-size: 0.9rem;
        line-height: 1.4;
    }

    .welcome-text .username {
        color: var(--brand-orange); 
        font-weight: bold;
        font-size: 1.1rem;
    }

    .logout-icon {
        color: var(--brand-red);
        font-size: 1.4rem;
        text-decoration: none;
        transition: transform 0.3s ease;
    }

    .logout-icon:hover {
        transform: translateX(4px); 
        color: #ff4d4d;
    }

    @media (max-width: 768px) {
        .header { padding: 15px 20px; }
        .menu-dropdown { right: 20px; width: calc(100% - 40px); }
    }

    /* ==========================================================================
    2. VEHICLE PAGE SPECIFIC STYLES
    ========================================================================== */
    .page-header { margin-top: 120px; text-align: center; margin-bottom: 50px; }
    .page-header h1 { color: #1e293b; font-weight: 800; font-size: 42px; }

    .booking-modal {
        display: none; 
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.7);
        backdrop-filter: blur(8px);
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .booking-modal-content {
        background: #ffffff;
        border-radius: 30px !important;
        padding: 45px !important;
        width: 95% !important; 
        max-width: 1250px !important; 
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    #carouselImages img {
        height: 500px !important; 
        width: 100% !important;
        object-fit: cover;
        border-radius: 20px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
        background-color: rgba(0,0,0,0.6);
        border-radius: 50%;
        width: 45px;
        height: 45px;
        background-size: 50%;
    }

    #detName {
        font-size: 40px !important;
        font-weight: 800 !important;
        color: #07003c !important;
        margin-bottom: 10px;
    }

    #detDesc {
        font-size: 17px !important;
        color: #64748b !important;
        line-height: 1.8;
        margin-bottom: 30px;
    }

    .spec-container .badge {
        padding: 12px 20px !important;
        border-radius: 12px !important;
        font-weight: 600 !important;
        font-size: 15px !important;
        background: #f1f5f9 !important;
        color: #334155 !important;
        border: 1px solid #e2e8f0 !important;
        margin-right: 8px;
        margin-bottom: 8px;
    }

    .p-3.rounded.border.bg-light {
        background: #f8fafc !important;
        border: 2px solid #f1f5f9 !important;
        border-radius: 20px !important;
        padding: 30px !important;
        margin-top: 30px;
    }

    .p-3 strong { font-size: 24px !important; color: #0f172a; }

    .close-modal {
        position: absolute;
        right: 25px;
        top: 20px;
        font-size: 35px;
        cursor: pointer;
        color: #94a3b8;
        transition: 0.3s;
        z-index: 10;
    }
    .close-modal:hover { color: #ef4444; transform: rotate(90deg); }

    #proceedBtn {
        background: linear-gradient(135deg, #023485 0%, #0f172a 100%) !important;
        color: white !important;
        border: none !important;
        padding: 20px !important;
        font-size: 18px !important;
        font-weight: 700 !important;
        border-radius: 15px !important;
        text-transform: uppercase;
        width: 100%;
        margin-top: 15px;
        transition: 0.4s;
    }
    #proceedBtn:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.2); }

    #selectedVehicleName {
        background-color: #f1f5f9 !important; 
        border: 1px solid #e2e8f0 !important; 
        color: #1e293b !important;             
        font-weight: 450 !important;       
        cursor: not-allowed;                
    }
</style>
</head>
<body>

    <header class="header">
        <div class="logo"><a href="index.php">Blaze Vehicle Rental</a></div>
        <button class="menu-btn" onclick="toggleMenu(event)">
            <i class="fas fa-bars"></i> Menu
        </button>
    </header>

    <nav id="menuDropdown" class="menu-dropdown">
        <ul class="nav-menu">
            <li><a href="index.php" class="nav-item"><i class="fa-solid fa-house"></i>Home</a></li>
            <li><a href="mybookings.php" class="nav-item"><i class="fa-solid fa-calendar-check"></i>My Bookings</a></li>
            <li><a href="vehicles.php" class="nav-item"><i class="fa-solid fa-car"></i>Our Vehicles</a></li>
            <li><a href="refund.php" class="nav-item"><i class="fa-solid fa-rotate-left"></i>Refund & Cancellation</a></li>
            <li><a href="privacy.php" class="nav-item"><i class="fa-solid fa-shield-halved"></i>Privacy & Policy</a></li>
            <li><a href="contact.php" class="nav-item"><i class="fa-solid fa-envelope"></i>Contact</a></li>
        </ul>
        
        <div class="user-section">
            <div class="welcome-text">
                Welcome,<br>
                <span class="username">
                    <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>
                </span>
            </div>
            <a href="back/logout.php" class="logout-icon" title="Logout">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>Explore Our Fleet</h1>
            <p>Choose from a wide range of well-maintained vehicles.</p>
        </div>

        <div class="car-container">
            <?php if (empty($vehicles)): ?>
                <p style="text-align: center; width: 100%;">No vehicles currently available. Please check back later!</p>
            <?php else: ?>
                <?php foreach ($vehicles as $car): ?>
                <div class="car-card">
                    <a href="javascript:void(0)" onclick="openBookingModal('<?= htmlspecialchars($car['vehicle_name']) ?>')">
                        <?php 
                            $cover_image = !empty($car['display_image']) ? $car['display_image'] : $car['image_path']; 
                        ?>
                        <img src="<?= htmlspecialchars($cover_image) ?>" alt="<?= htmlspecialchars($car['vehicle_name']) ?>">
                    </a>
                    
                    <div class="car-info">
                        <h2><?= htmlspecialchars($car['vehicle_name']) ?></h2>
                        
                        <ul class="features">
                            <li><i class="fa-solid fa-road"></i> <?= htmlspecialchars($car['engine_details']) ?></li>
                            <li><i class="fa-solid fa-cog"></i> <?= htmlspecialchars($car['transmission']) ?></li>
                            <li><i class="fa-solid fa-gas-pump"></i> <?= htmlspecialchars($car['fuel_type']) ?></li>
                            <li><i class="fa-solid fa-users"></i> <?= htmlspecialchars($car['power_details']) ?></li>
                        </ul>
                        
                        <div class="price"> <span><strong>₹<?= number_format($car['price_per_hour']) ?></strong> / hour</span></div>
                        
                        <div class="more-details ">
                            <button class="btn btn-primary book-now" 
                                onclick='openDetailsModal(<?= json_encode($car) ?>)'>
                                Details
                            </button>
                            
                            <button class="btn btn-primary book-now" 
                                onclick="openBookingModal('<?= htmlspecialchars($car['vehicle_name']) ?>', <?= $car['price_per_hour'] ?>)">
                                Book Now
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>


    <div id="detailsModal" class="booking-modal">
        <div class="booking-modal-content" style="max-width: 900px;">
            <span class="close-modal" onclick="closeDetailsModal()">&times;</span>
            
            <div class="row g-4">
                <div class="col-lg-7">
                    <div id="carCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner" id="carouselImages">
                            </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    </div>
                </div>

                <div class="col-lg-5">
                    <h2 id="detName" style="color: #17477a;"></h2>
                    <p id="detDesc" class="text-muted mb-3" style="font-size: 0.9rem;"></p>
                    
                    <div class="spec-container d-flex flex-wrap gap-2 mb-4">
                        <span class="badge bg-success-subtle text-success border border-success-subtle p-2"><i class="fa-solid fa-gas-pump"></i> <span id="detFuel"></span></span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle p-2"><i class="fa-solid fa-cog"></i> <span id="detTrans"></span></span>
                        <span class="badge bg-success-subtle text-success border border-success-subtle p-2"><i class="fa-solid fa-car"></i> <span id="detType"></span></span>
                    </div>

                    <div class="p-3 rounded border bg-light">
                        <div class="d-flex justify-content-between mb-1"><span>Rent/Hour:</span><strong id="detPrice"></strong></div>
                        <div class="d-flex justify-content-between mb-2"><span>Security Deposit:</span><strong>₹10,000</strong></div>
                        <button class="btn btn-primary w-100 mt-2" id="proceedBtn">Book This Vehicle</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="bookingModal" class="booking-modal">
        <div class="booking-modal-content">
            <span class="close-modal" onclick="closeBookingModal()">&times;</span>
            <h3 class="mb-4">Vehicle Rental Enquiry</h3>
            
            <form id="bookingForm" action="back/registerenquiry.php" method="post" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name</label>
                        <input name="fullname" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input name="phone" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Selected Vehicle</label>
                        <input type="text" id="selectedVehicleName" name="vehicle_model" class="form-control" style="background-color: #e9ecef; font-weight: bold;" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Number of Vehicles</label>
                        <input type="number" name="quantity" id="carQty" class="form-control" value="1" min="1" max="10" required onchange="updateCost()" oninput="updateCost()">
                    </div>
                    <div class="col-md-6 d-flex align-items-center">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" id="needDriver" name="need_driver" value="Yes" onchange="updateCost()" style="width: 3em; height: 1.5em; cursor: pointer;">
                            <label class="form-check-label ms-3 mt-1" for="needDriver" style="font-weight: 700; color: #0f172a; font-size: 16px;">
                                Add Professional Driver<br>
                                <small class="text-muted" style="font-weight: normal;">+₹500/hour per vehicle</small>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">DL Number</label>
                        <input type="text" class="form-control" name="dl_number" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Upload License</label>
                        <input type="file" class="form-control" name="license_file" accept="image/*,application/pdf" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Pickup Location</label>
                        <input name="pickup_location" class="form-control" required>  
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Pickup Date & Time</label>
                        <input type="datetime-local" name="pickup_datetime" id="pickup_dt" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Drop-off Date & Time</label>
                        <input type="datetime-local" name="dropoff_datetime" id="dropoff_dt" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" id="payMethod" class="form-select" required onchange="handlePaymentChange()">
                            <option value="" selected disabled>Select Payment Method</option>
                            <option value="Cash">Cash</option>
                            <option value="UPI">UPI</option>
                            <option value="Card">Credit/Debit/ATM Card</option>
                        </select>
                    </div>

                    <div class="col-12" id="upiSection" style="display:none;">
                        <label class="form-label">Enter UPI ID</label>
                        <input type="text" name="upi_id" class="form-control" placeholder="example@upi">
                    </div>

                    <div id="cardSection" style="display:none;" class="row">
                        <div class="col-md-6">
                            <label class="form-label">Card Number</label>
                            <input type="text" name="card_number" class="form-control" maxlength="16" placeholder="XXXX XXXX XXXX XXXX">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">CVV</label>
                            <input type="password" name="cvv" class="form-control" maxlength="3">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Expiry Date</label>
                            <input type="month" name="expiry_date" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div id="costSummary" class="alert alert-secondary mt-4" style="display:none; border-radius: 15px; border: 1px solid #cbd5e1; background: #f8fafc;">
                    <h5 class="mb-3 border-bottom pb-2" style="color: #0f172a;"><strong><i class="fa-solid fa-file-invoice-dollar"></i> Pricing Breakdown</strong></h5>
                    
                    <div class="d-flex justify-content-between mb-2"><span class="text-muted">Total Rental Time:</span> <strong id="hoursCount">0 Hours</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span class="text-muted">Number of Vehicles:</span> <strong id="qtyCount">1 Car(s)</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span class="text-muted">Base Vehicle Rent:</span> <strong id="baseRentCost">₹0</strong></div>
                    
                    <div class="d-flex justify-content-between mb-2 text-success" id="driverCostRow" style="display:none !important;">
                        <span>Driver Charge (₹500/hr/car):</span> <strong id="driverCost">₹0</strong>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2" style="color: #c90e1c;">
                        <span>Car Decoration Charges (₹2000/car):</span> <strong id="decoCost">₹0</strong>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3 text-warning-emphasis">
                        <span>Security Deposit (Refundable):</span> <strong id="secDeposit">₹0</strong>
                    </div>
                    
                    <div class="d-flex justify-content-between fs-5 pt-2 border-top">
                        <span style="color: #0f172a;"><strong>Total Payable Amount:</strong></span> 
                        <strong id="totalCost" style="color: #c90e1c; font-size: 24px;">₹0</strong>
                    </div>
                </div>

                <button class="btn btn-success mt-3 w-100" type="submit">Confirm Booking</button>
            </form>
        </div>
    </div>

    <script src="assets/js/index.js"></script>
    <script>
    function closeDetailsModal() {
        const dModal = document.getElementById('detailsModal');
        if (dModal) dModal.style.display = 'none';
    }

    function closeBookingModal() {
        const bModal = document.getElementById('bookingModal');
        if (bModal) {
            bModal.style.display = 'none';
            const form = document.getElementById('bookingForm');
            if(form) form.reset();
            document.getElementById('costSummary').style.display = 'none';
        }
    }

    function toggleMenu(e) {
        e.stopPropagation();
        const menu = document.getElementById('menuDropdown');
        menu.classList.toggle('open');
    }

    document.addEventListener('click', function(event) {
        const menu = document.getElementById('menuDropdown');
        const menuBtn = document.querySelector('.menu-btn');
        if (menu && menu.classList.contains('open') && !menu.contains(event.target) && !menuBtn.contains(event.target)) {
            menu.classList.remove('open');
        }
    });

    let currentVehiclePrice = 25000; 

    function openBookingModal(vehicleName, price) {
        const modal = document.getElementById('bookingModal');
        modal.style.display = 'flex';
        
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        
        const minDateTime = `${year}-${month}-${day}T${hours}:${minutes}`;
        
        document.getElementById('pickup_dt').setAttribute('min', minDateTime);
        document.getElementById('dropoff_dt').setAttribute('min', minDateTime);

        if(vehicleName) document.getElementById('selectedVehicleName').value = vehicleName;
        if(price) currentVehiclePrice = parseInt(price);

        updateCost();
    }

    const pickupEl = document.getElementById('pickup_dt');
    const dropoffEl = document.getElementById('dropoff_dt');
    const payEl = document.getElementById('payMethod');
    const box = document.getElementById('costSummary');

    function updateCost() {
        if (!pickupEl.value || !dropoffEl.value || !payEl.value) {
            box.style.display = 'none';
            return;
        }

        const start = new Date(pickupEl.value);
        const end = new Date(dropoffEl.value);
        let diffHrs = (end - start) / (1000 * 60 * 60);
        if (diffHrs <= 0) diffHrs = 1;
        diffHrs = Math.ceil(diffHrs); // Complete hours

        const qty = parseInt(document.getElementById('carQty').value) || 1;
        const needDriver = document.getElementById('needDriver').checked;

        const baseRent = diffHrs * currentVehiclePrice * qty;
        const driverCost = needDriver ? (diffHrs * 500 * qty) : 0;
        const decorationCost = 2000 * qty;
        const securityDeposit = 10000 * qty;

        const total = baseRent + driverCost + decorationCost + securityDeposit;

        document.getElementById('hoursCount').textContent = diffHrs + ' Hours';
        document.getElementById('qtyCount').textContent = qty + ' Car(s)';
        document.getElementById('baseRentCost').textContent = '₹' + baseRent.toLocaleString('en-IN');
        
        const driverRow = document.getElementById('driverCostRow');
        if(needDriver) {
            driverRow.style.setProperty('display', 'flex', 'important');
            document.getElementById('driverCost').textContent = '+ ₹' + driverCost.toLocaleString('en-IN');
        } else {
            driverRow.style.setProperty('display', 'none', 'important');
        }

        document.getElementById('decoCost').textContent = '+ ₹' + decorationCost.toLocaleString('en-IN');
        document.getElementById('secDeposit').textContent = '+ ₹' + securityDeposit.toLocaleString('en-IN');
        document.getElementById('totalCost').textContent = '₹' + total.toLocaleString('en-IN');

        box.style.display = 'block';
    }

    pickupEl.addEventListener('change', updateCost);
    dropoffEl.addEventListener('change', updateCost);
    payEl.addEventListener('change', updateCost);

    function openDetailsModal(car) {
        const dModal = document.getElementById('detailsModal');
        dModal.style.display = 'flex';

        document.getElementById('detName').innerText = car.vehicle_name;
        document.getElementById('detFuel').innerText = car.fuel_type;
        document.getElementById('detTrans').innerText = car.transmission;
        document.getElementById('detType').innerText = car.power_details;
        document.getElementById('detPrice').innerText = '₹' + parseInt(car.price_per_hour).toLocaleString('en-IN');
        document.getElementById('detDesc').innerText = car.description || "Premium vehicle with top-tier performance.";

        // Yahan sirf 4 andar wali photos hi slider mein aayengi
        const images = [car.image_path, car.image2, car.image3, car.image4].filter(src => src && src !== "");
        let innerHTML = "";
        
        images.forEach((src, index) => {
            innerHTML += `
                <div class="carousel-item ${index === 0 ? 'active' : ''}">
                    <img src="${src}" class="d-block w-100 rounded" style="height: 450px; object-fit: cover;">
                </div>`;
        });
        
        document.getElementById('carouselImages').innerHTML = innerHTML;

        var myCarouselEl = document.querySelector('#carCarousel');
        var carousel = new bootstrap.Carousel(myCarouselEl, {
            interval: 5000, 
            wrap: true,
            pause: 'hover'
        });

        document.getElementById('proceedBtn').onclick = function() {
            closeDetailsModal();
            openBookingModal(car.vehicle_name, car.price_per_hour);
        };
    }

    function handlePaymentChange() {
        const method = document.getElementById('payMethod').value;
        const upi = document.getElementById('upiSection');
        const card = document.getElementById('cardSection');

        upi.style.display = 'none';
        card.style.display = 'none';

        document.querySelector('[name="upi_id"]').required = false;
        document.querySelector('[name="card_number"]').required = false;
        document.querySelector('[name="cvv"]').required = false;
        document.querySelector('[name="expiry_date"]').required = false;

        if (method === "UPI") {
            upi.style.display = 'block';
            document.querySelector('[name="upi_id"]').required = true;
        } 
        else if (method === "Card") {
            card.style.display = 'flex';
            document.querySelector('[name="card_number"]').required = true;
            document.querySelector('[name="cvv"]').required = true;
            document.querySelector('[name="expiry_date"]').required = true;
        }

        updateCost();
    }

    window.onclick = function(event) {
        const bModal = document.getElementById('bookingModal');
        const dModal = document.getElementById('detailsModal');
        if (event.target == bModal) closeBookingModal();
        if (event.target == dModal) closeDetailsModal();
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>