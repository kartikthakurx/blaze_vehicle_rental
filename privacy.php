<?php
// Start session to fetch the logged-in user's name
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Car Rental</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="/rentalproject/assets/css/style.css?v=1">
    <link rel="stylesheet" href="/rentalproject/assets/css/stylenew.css?v=1">
    
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
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
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
            box-sizing: border-box;
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
            transition: 0.3s;
        }
        
        .menu-btn:hover { background-color: #a70b16; }

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
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
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
           2. PRIVACY CONTENT STYLES
           ========================================================================== */
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            margin-top: 120px; /* Gives room for the fixed header */
        }
        .container h1, h2 {
            color: #17477a;
            margin-top: 20px;
        }
        .container p {
            line-height: 1.6;
            margin-top: 10px;
        }
        @media (max-width: 600px) {
            .container {
                padding: 15px;
            }
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
        <h1>Privacy Policy</h1>
 
        <h2>1. Information We Collect</h2>
        <p>We collect personal details such as your name, contact details, driver's license information, and payment details when you book a car rental with us.</p>
        
        <h2>2. How We Use Your Information</h2>
        <p>We use your information to process bookings, verify identity, improve customer service, and send promotional offers if you opt-in.</p>
        
        <h2>3. Data Protection</h2>
        <p>We implement security measures to protect your data from unauthorized access, alteration, disclosure, or destruction.</p>
        
        <h2>4. Sharing of Information</h2>
        <p>We do not sell or share your personal information with third parties, except when required by law or with trusted partners who assist in providing services.</p>
        
        <h2>5. Cookies and Tracking</h2>
        <p>Our website may use cookies to enhance user experience and gather analytics data.</p>
        
        <h2>6. Your Rights</h2>
        <p>You have the right to access, modify, or delete your personal data. Contact us at [blazevehiclerental@gmail.com] for any privacy-related requests.</p>
        
        <h2>7. Changes to Privacy Policy</h2>
        <p>We may update this policy from time to time. The latest version will always be available on our website.</p>
    </div>

    <footer class="car-footer">
        <div class="footer-container">
            <div class="footer-section">
                <ul>
                    <li><a href="refund.php"><i class="fas fa-angle-right"></i> Refund & Cancellations</a></li>
                    <li><a href="privacy.php"><i class="fas fa-angle-right"></i> Privacy & Policy</a></li>
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
    
    <script src="assets/js/index.js"></script>
    <script>
    // --- Navigation Dropdown JS ---
    function toggleMenu(e) {
        e.stopPropagation();
        const menu = document.getElementById('menuDropdown');
        menu.classList.toggle('open');
    }

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('menuDropdown');
        const menuBtn = document.querySelector('.menu-btn');
        if (menu && menu.classList.contains('open') && !menu.contains(event.target) && !menuBtn.contains(event.target)) {
            menu.classList.remove('open');
        }
    });
    </script>
</body>
</html>