<?php
// Optional: Start session at the very top if you plan to use $_SESSION['username'] later
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Rental | Blaze</title>
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
            --cream: #fff;
            --dark: #333;
        }

        body {
            margin: 0;
            font-family: var(--font-body);
            background-color: var(--cream);
            color: var(--dark);
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
            background-color: rgba(0, 0, 0, 0.6); 
            backdrop-filter: blur(8px); 
            -webkit-backdrop-filter: blur(8px);
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

        /* Menu Button */
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
        }

        /* Drop-down Menu Panel */
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
            /* Added Flex Column to force strictly vertical stacking */
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

        /* User Profile Section at bottom of menu */
        .user-section {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-left: 15px;
            padding-right: 15px;
            width: 100%; /* Ensures it fills the container horizontally */
            box-sizing: border-box; /* Stops padding from widening the element */
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
            .header {
                padding: 15px 20px;
            }
            .menu-dropdown {
                right: 20px;
                width: calc(100% - 40px);
            }
        }

        /* ==========================================================================
           2. PAGE CONTENT & CONTACT STYLES
           ========================================================================== */
        
        .page-content-wrapper {
            width: 100%;
            margin: 0;
            padding: 0;
        }

        .contact-homepage {
            background-color: #f8f9fa; 
        }

        .contact-card {
            border: none;
            border-top: 5px solid #001f3f; 
        }

        .contact-card .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #ddd;
        }

        .contact-card .form-control:focus {
            border-color: #fca019; 
            box-shadow: none;
        }

        .contact-card h2 {
            font-size: 2.5rem;
            letter-spacing: 1px;
        }

        /* --- NEW: Contact Form Blur Overlay Styles --- */
        .contact-wrapper {
            position: relative;
            border-radius: 20px;
            overflow: hidden; /* Overlay ko form area tak hi rakhne ke liye */
        }

        .form-blurred {
            filter: blur(5px);
            opacity: 0.6;
            pointer-events: none; /* Clicking disable karne ke liye */
            user-select: none;
        }

        .contact-login-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 10;
            background: rgba(255, 255, 255, 0.2);
        }

        .overlay-box {
            background: white;
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
            transform: translateY(-10px);
        }

        .overlay-box i {
            font-size: 45px;
            color: #001f3f;
            margin-bottom: 15px;
        }

        .overlay-box h3 {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 10px;
        }

        .overlay-box p {
            color: #64748b;
            margin-bottom: 25px;
        }

        .btn-overlay-login {
            background: #001f3f;
            color: white;
            padding: 12px 30px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 16px;
            transition: 0.3s;
            display: inline-block;
        }

        .btn-overlay-login:hover {
            background: #fca019;
            transform: translateY(-2px);
            color: #000;
        }
    </style>
</head>

<body>
    
    <header class="header">
        <div class="logo"><a href="index.php">Blaze Vehicle Rental</a></div>
        <button class="menu-btn" onclick="toggleMenu(event)">
            <i class="fas fa-bars"></i>
            Menu
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
            <?php if(isset($_SESSION['user_id'])): ?>
            <a href="back/logout.php" class="logout-icon" title="Logout">
                <i class="fa-solid fa-right-from-bracket"></i>
            </a>
            <?php else: ?>
            <a href="signin.html" class="logout-icon" title="Sign In" style="color: var(--brand-orange);">
                <i class="fa-solid fa-arrow-right-to-bracket"></i>
            </a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="page-content-wrapper">

        <section class="hero">
            <div class="hero-content">
                <h3>Drive Your Dream Car Today</h3>
                <h1>Your Ride, Your Way! <br> Affordable Car Rentals, Anytime, Anywhere.</h1>
                <p>
                    Choose from a wide range of well-maintained vehicles at the best prices.<br>Easy booking and hassle-free rides for every journey.
                </p>
            </div>
        </section>

        <section class="brands-section">
            <h2>Premium Brands</h2>
            <div class="slider-container">
                <div class="slider">
                    <div class="slide"><img src="assets/image/bugatti.png" alt="Bugatti"></div>
                    <div class="slide"><img src="assets/image/kia.png" alt="Jaguar"></div>
                    <div class="slide"><img src="assets/image/honda.png" alt="Honda"></div>
                    <div class="slide"><img src="assets/image/volvo.jpeg" alt="Chevrolet"></div>
                    
                    <div class="slide"><img src="assets/image/kwid.webp" alt="BMW"></div>
                    <div class="slide"><img src="assets/image/toyota.png" alt="Toyota"></div>
                    <div class="slide"><img src="assets/image/lexus.png" alt="Lexus"></div>
                    <div class="slide"><img src="assets/image/revolt.png" alt="Revolt"></div>
                    <div class="slide"><img src="assets/image/bugatti.png" alt="Bugatti"></div>
                    <div class="slide"><img src="assets/image/kia.png" alt="Jaguar"></div>
                    <div class="slide"><img src="assets/image/honda.png" alt="Honda"></div>
                    <div class="slide"><img src="assets/image/volvo.jpeg" alt="Chevrolet"></div>
                    
                    <div class="slide"><img src="assets/image/kwid.webp" alt="BMW"></div>
                    <div class="slide"><img src="assets/image/toyota.png" alt="Toyota"></div>
                    <div class="slide"><img src="assets/image/lexus.png" alt="Lexus"></div>
                    <div class="slide"><img src="assets/image/revolt.png" alt="Revolt"></div>
                </div>
            </div>
        </section>

        <section class="explore-banner-section" style="padding: 0; margin: 0; width: 100%;">
            <div style="position: relative; width: 100%; height: 600px; background-image: url('assets/image/back.jpg'); background-size: cover; background-position: center bottom; background-repeat: no-repeat;">
                <a href="vehicles.php" style="position: absolute; bottom: 12%; left: 50%; transform: translateX(-50%); z-index: 2; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 2px solid rgba(255, 255, 255, 0.5); color: #fff; font-size: 20px; font-weight: bold; padding: 12px 45px; border-radius: 50px; text-decoration: none; text-transform: uppercase; transition: 0.4s; letter-spacing: 1px;" 
                   onmouseover="this.style.background='rgba(252, 160, 25, 1)'; this.style.borderColor='#fca019'; this.style.color='#000'; this.style.transform='translateX(-50%) scale(1.05)';" 
                   onmouseout="this.style.background='rgba(255, 255, 255, 0.1)'; this.style.borderColor='rgba(255, 255, 255, 0.5)'; this.style.color='#fff'; this.style.transform='translateX(-50%) scale(1)';" >
                    Explore Now
                </a>
            </div>
        </section>

        <br><br><br><br>

        <div class="best-car-container">
            <div class="container01">
                <div class="video-section">
                    <img src="assets/image/car-4.png" alt="Car Detail">
                </div>
                <div class="content-section">
                    <h3><strong>Why Choose Our Car Rental Service</strong></h3>
                    <p>We provide well-maintained vehicles, transparent pricing, and 24/7 support to make your journey smooth and worry-free.</p>
                    <ul class="features">
                        <li>Wide Range of Vehicles – Cars, SUVs, Bikes & More</li>
                        <li>Affordable Pricing</li>
                        <li>24/7 Roadside Assistance</li>
                        <li>Free Pick-Up & Drop-Off</li>
                        <li>Easy Online Booking & Payment</li>
                    </ul>
                </div>
            </div>
        </div>

        <br><br><br><br>

        <div class="presting-car">
            <h2>HOW IT WORKS</h2>
            <h1>Easy Steps to Book Your Vehicle</h1>
            <div class="presting-item">
                <div class="presting"><img src="assets/image/carlocationsvg.png" alt="Sign In"><h3>Sign In</h3></div>
                <div class="presting"><img src="assets/image/carchoocesvg.png" alt="Choose"><h3>Choose Your Vehicle</h3></div>
                <div class="presting"><img src="assets/image/race-carssvg.svg" alt="Form"><h3>Fill Booking Form</h3></div>
                <div class="presting"><img src="assets/image/carkeysvg.jpg" alt="Journey"><h3>Begin Your Journey</h3></div>
            </div>
        </div>

        <br>

        <section class="py-5 contact-homepage">
            <div class="container">
                <div class="contact-wrapper mx-auto" style="max-width: 800px;">
                    
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <div class="contact-login-overlay">
                            <div class="overlay-box">
                                <i class="fa-solid fa-lock"></i>
                                <h3>Login Required</h3>
                                <p>Please sign in to send us a message.</p>
                                <a href="signin.html" class="btn-overlay-login">Sign In Now</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="contact-card shadow p-5 rounded-4 bg-white <?php echo !isset($_SESSION['user_id']) ? 'form-blurred' : ''; ?>">
                        <h2 class="text-center fw-bold mb-4">Contact Us</h2>
                        <form action="back/contact_submit.php" method="POST">
                            <div class="mb-3"><input type="text" name="fullname" class="form-control py-3" placeholder="Your Name" required></div>
                            <div class="mb-3"><input type="email" name="email" class="form-control py-3" placeholder="Your Email" required></div>
                            <div class="mb-3"><input type="text" name="phone" class="form-control py-3" placeholder="Phone Number" required></div>
                            <div class="mb-3"><input type="text" name="subject" class="form-control py-3" placeholder="Subject" required></div>
                            <div class="mb-3"><textarea name="message" class="form-control" rows="4" placeholder="Your Message" required></textarea></div>
                            <button type="submit" class="btn w-100 py-3 fw-bold" style="background: #001f3f; color: white;">Send Message</button>
                        </form>
                    </div>

                </div>
            </div>
        </section>

        <footer class="car-footer">
            <div class="footer-container">
                <div class="footer-section">
                    <p><i class="fa-solid fa-phone"></i> Phone: 8091295168</p>
                    <p><i class="fa-solid fa-envelope"></i> Email: blazevehiclerental@gmail.com</p>
                </div>
                <div class="footer-section">
                    <ul>
                        <li><a href="privacy.php"><i class="fas fa-angle-right"></i> Privacy & Policy</a></li>
                        <li><a href="refund.php"><i class="fas fa-angle-right"></i> Refund & Cancellations</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <ul>
                        <li><a href="index.php"><i class="fas fa-angle-right"></i> Home</a></li>
                        <li><a href="contact.php"><i class="fas fa-angle-right"></i> Contact</a></li>
                        <div class="social-links">
                            <li><a href="https://www.facebook.com" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                            <li><a href="https://www.twitter.com" target="_blank"><i class="fab fa-twitter"></i></a></li>
                            <li><a href="https://www.instagram.com" target="_blank"><i class="fab fa-instagram"></i></a></li>
                            <li><a href="https://www.linkedin.com" target="_blank"><i class="fab fa-linkedin"></i></a></li>
                        </div>
                    </ul>
                </div>
            </div>
        </footer>

    </div> 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/index.js"></script>
    
    <script>
        // Dropdown Menu Toggle 
        function toggleMenu(e) {
            e.stopPropagation();
            const menu = document.getElementById('menuDropdown');
            menu.classList.toggle('open');
        }

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('menuDropdown');
            const menuBtn = document.querySelector('.menu-btn');
            if (menu.classList.contains('open') && !menu.contains(event.target) && !menuBtn.contains(event.target)) {
                menu.classList.remove('open');
            }
        });
    </script>

    <div id="msgToast" class="toast align-items-center text-bg-success border-0 position-fixed top-0 end-0 m-3" role="alert" style="z-index:9999;">
        <div class="d-flex">
            <div class="toast-body">Message Sent Successfully!</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>

    <script>
    window.addEventListener('load', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('success')) {
            const toastElement = document.getElementById('msgToast');
            if (toastElement) {
                const toast = new bootstrap.Toast(toastElement);
                toast.show();
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        }
    });
    </script>

</body>
</html>