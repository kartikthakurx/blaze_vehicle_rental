<?php
// Start session to fetch the logged-in user's name
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$username = $_SESSION['username'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Rental Contact Form</title>
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
            margin: 0;
            background-color: #f8f9fa;
            overflow-x: hidden; 
        }

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
           2. BLUR OVERLAY & CONTACT FORM STYLES
           ========================================================================== */
        
        .main-contact {
            margin-top: 120px; 
            margin-bottom: 60px;
            display: flex;             /* Flexbox for perfect centering */
            justify-content: center;   /* Center horizontally */
            padding: 0 20px;           /* Padding for mobile screens */
        }

        .content-wrapper-relative {
            position: relative;
            width: 100%;               /* Take full width up to max-width */
            max-width: 700px;
            border-radius: 20px;
        }
        
        .form-blurred {
            filter: blur(6px);
            opacity: 0.6;
            pointer-events: none;
            user-select: none;
        }

        .login-overlay {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 100%;
            display: flex; flex-direction: column; justify-content: center; align-items: center;
            z-index: 10;
            background: rgba(248, 249, 250, 0.2);
            border-radius: 20px;
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

        .contact-container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            text-align: center;
        }
        .contact-container h2 { font-size: 32px; font-weight: 800; color: #0f172a; margin-bottom: 10px; }
        .contact-container p { color: #64748b; margin-bottom: 30px; }
        .contact-form input, .contact-form textarea {
            width: 100%; padding: 15px; margin-bottom: 15px; border: 2px solid #e2e8f0;
            border-radius: 10px; outline: none; transition: 0.3s; font-family: inherit;
            box-sizing: border-box;
        }
        .contact-form input:focus, .contact-form textarea:focus { border-color: #c90e1c; }
        .contact-form button {
            width: 100%; padding: 15px; background: #0f172a; color: white; border: none;
            border-radius: 10px; font-size: 18px; font-weight: 700; cursor: pointer; transition: 0.3s;
        }
        .contact-form button:hover { background: #c90e1c; }
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
                    <?php echo htmlspecialchars($username); ?>
                </span>
            </div>
            
            <?php if($is_logged_in): ?>
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

    <div class="main-contact">
        <div class="content-wrapper-relative">
            
            <?php if (!$is_logged_in): ?>
                <div class="login-overlay">
                    <div class="overlay-box">
                        <i class="fa-solid fa-lock"></i>
                        <h3>Login Required</h3>
                        <p>Please sign in to send us a message.</p>
                        <a href="signin.html" class="btn-overlay-login">Sign In Now</a>
                    </div>
                </div>
            <?php endif; ?>

            <div class="contact-container <?php echo !$is_logged_in ? 'form-blurred' : ''; ?>">
                <h2>Contact Us</h2>
                <p>Have a question? Fill out the form below and we will get back to you!</p>
                <form class="contact-form" method="POST" action="/rentalproject/back/contact_submit.php">
                    <input type="text" name="fullname" placeholder="Your Name" required>
                    <input type="email" name="email" placeholder="Your Email" required>
                    <input type="text" name="phone" placeholder="Phone" required>
                    <input type="text" name="subject" placeholder="Subject" required>
                    <textarea rows="5" name="message" placeholder="Your Message" required></textarea>
                    <button type="submit">Send Message</button>
                </form>
            </div>

        </div>
    </div>

    <footer class="car-footer">
        <div class="footer-container">
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

    <script src="assets/js/index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
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

    <div id="msgToast" class="toast align-items-center text-bg-success border-0 position-fixed top-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true" style="z-index:1080;">
        <div class="d-flex">
            <div class="toast-body" id="msgBody">Thanks for contacting us</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>

    <script>
        (function(){
            const p   = new URLSearchParams(location.search);
            const msg = p.get('success');
            if (!msg) return;

            document.getElementById('msgBody').textContent =
                decodeURIComponent(msg.replace(/\+/g,' ')) || 'Thanks for contacting us';

            const el = document.getElementById('msgToast');
            new bootstrap.Toast(el, { delay: 3500 }).show();

            if (history.replaceState) history.replaceState(null,'', location.pathname);
        })();
    </script>

</body>
</html>