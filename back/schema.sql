-- 1. Create the database and switch to it
CREATE DATABASE IF NOT EXISTS vehiclerental_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE vehiclerental_db;

-- 2. Create the users table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  mobile VARCHAR(20),
  password_hash VARCHAR(255) NOT NULL,
  is_admin TINYINT(1) DEFAULT 0, -- Added so the admin panel works (0 = User, 1 = Admin)
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 3. Create the bookings table
CREATE TABLE IF NOT EXISTS bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  fullname VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  vehicle_model VARCHAR(120) NOT NULL,
  dl_number VARCHAR(50) NOT NULL,       -- Added for Driving License
  license_file VARCHAR(255) NOT NULL,   -- Added for Uploaded File
  pickup_location VARCHAR(150) NOT NULL,
  pickup_date DATETIME NOT NULL,        -- Changed to DATETIME for exact times
  dropoff_date DATETIME NOT NULL,       -- Changed to DATETIME for exact times
  total_hours INT NOT NULL,             -- Added for pricing calculation
  total_amount INT NOT NULL,            -- Added for total cost
  payment_method VARCHAR(40) NOT NULL,
  status VARCHAR(20) DEFAULT 'pending', -- Added for Admin Panel tracking
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_bookings_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE SET NULL
) ENGINE=InnoDB;

-- 4. Create the contact_messages table
CREATE TABLE IF NOT EXISTS contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  subject VARCHAR(200),
  message TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
select * from users;
select * from contact_messages;
select * from bookings;
select * from vehicles;
TRUNCATE TABLE contact_messages;
DELETE FROM users WHERE id = 3;
ALTER TABLE bookings ADD user_rating INT(11) DEFAULT 0;
ALTER TABLE vehicles ADD display_image VARCHAR(255) AFTER price_per_hour;

CREATE TABLE IF NOT EXISTS vehicles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  vehicle_name VARCHAR(150) NOT NULL,
  engine_details VARCHAR(50) NOT NULL,  -- e.g., "6496 cc" or "Electric"
  transmission VARCHAR(50) NOT NULL,    -- e.g., "Automatic" or "Manual"
  fuel_type VARCHAR(50) NOT NULL,       -- e.g., "Petrol" or "116 kWh"
  power_details VARCHAR(50) NOT NULL,   -- e.g., "788.52 bhp" or "473 km"
  price_per_hour INT NOT NULL DEFAULT 25000,
  image_path VARCHAR(255) NOT NULL,
  status VARCHAR(20) DEFAULT 'available',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Let's insert two of your actual vehicles so the page isn't blank when you test it!
INSERT INTO vehicles (vehicle_name, engine_details, transmission, fuel_type, power_details, price_per_hour, image_path) 
VALUES 
('Ferrari 812', '6496 cc', 'Automatic', 'Petrol', '788.52 bhp', 25000, 'assets/image/benz/main 1.avif'),
('Mercedes-Benz G-Class Electric', '473 km', 'Electric', '116 kWh', '579 bhp', 25000, 'assets/image/g class/main g1.avif');

-- Gallery images aur Description add karne ke liye
ALTER TABLE vehicles 
ADD COLUMN image2 VARCHAR(255) DEFAULT NULL AFTER image_path,
ADD COLUMN image3 VARCHAR(255) DEFAULT NULL AFTER image2,
ADD COLUMN image4 VARCHAR(255) DEFAULT NULL AFTER image3,
ADD COLUMN description TEXT DEFAULT NULL AFTER image4;

ALTER TABLE bookings 
ADD quantity INT(11) DEFAULT 1,
ADD need_driver VARCHAR(10) DEFAULT 'No';