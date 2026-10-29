-- ==========================================
-- SmartCrop Database
-- ==========================================

USE smartcrop_db;


-- ==========================================
-- 1. Users Table
-- ==========================================

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mobile VARCHAR(15) NOT NULL,
    role ENUM('Farmer', 'Buyer', 'Admin') DEFAULT 'Farmer',
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- ==========================================
-- 2. Crop Advisory Table
-- ==========================================

CREATE TABLE crop_advisory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    soil_type VARCHAR(50) NOT NULL,
    temperature DECIMAL(5,2),
    soil_moisture DECIMAL(5,2),
    recommended_crop VARCHAR(100),
    advice TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE SET NULL
);


-- ==========================================
-- 3. Crop Health Table
-- ==========================================

CREATE TABLE crop_health (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    crop_name VARCHAR(100) NOT NULL,
    health_status VARCHAR(50),
    soil_moisture DECIMAL(5,2),
    risk_level VARCHAR(50),
    growth_stage VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE SET NULL
);


-- ==========================================
-- 4. Marketplace Table
-- ==========================================

CREATE TABLE marketplace (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT,
    product_name VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    price DECIMAL(10,2) NOT NULL,
    unit VARCHAR(30) DEFAULT 'Quintal',
    location VARCHAR(100),
    quality_rating DECIMAL(2,1),
    stock_status VARCHAR(50) DEFAULT 'In Stock',
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (seller_id)
    REFERENCES users(id)
    ON DELETE SET NULL
);


-- ==========================================
-- 5. Weather Alerts Table
-- ==========================================

CREATE TABLE weather_alerts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    location VARCHAR(100) NOT NULL,
    temperature DECIMAL(5,2),
    humidity DECIMAL(5,2),
    rainfall DECIMAL(6,2),
    wind_speed DECIMAL(6,2),
    alert_message TEXT,
    alert_type VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE SET NULL
);


-- ==========================================
-- 6. Settings Table
-- ==========================================

CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,

    notification_type VARCHAR(30) DEFAULT 'inapp',
    alert_frequency VARCHAR(30) DEFAULT 'instant',

    two_factor VARCHAR(20) DEFAULT 'disabled',

    language VARCHAR(10) DEFAULT 'en',

    area_unit VARCHAR(20) DEFAULT 'acre',
    weight_unit VARCHAR(20) DEFAULT 'quintal',
    currency VARCHAR(10) DEFAULT 'inr',

    profile_visibility VARCHAR(20) DEFAULT 'public',
    location_sharing VARCHAR(10) DEFAULT 'yes',
    third_party_access VARCHAR(20) DEFAULT 'blocked',

    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE CASCADE
);


-- ==========================================
-- 7. Support Requests Table
-- ==========================================

CREATE TABLE support_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    status VARCHAR(30) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
    REFERENCES users(id)
    ON DELETE SET NULL
);


-- ==========================================
-- 8. Transactions Table
-- ==========================================

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    buyer_id INT,
    seller_id INT,
    product_id INT,
    quantity DECIMAL(10,2),
    total_amount DECIMAL(12,2),
    transaction_status VARCHAR(30) DEFAULT 'Completed',
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (buyer_id)
    REFERENCES users(id)
    ON DELETE SET NULL,

    FOREIGN KEY (seller_id)
    REFERENCES users(id)
    ON DELETE SET NULL,

    FOREIGN KEY (product_id)
    REFERENCES marketplace(id)
    ON DELETE SET NULL
);


-- ==========================================
-- 9. Sample Users
-- ==========================================

INSERT INTO users
(full_name, email, mobile, role, password)
VALUES
('Dhananjay Wabale', 'dhananjaywabale5@gmail.com', '9561363034', 'Farmer', '123456'),
('Shubham Sonavane', 'shubham@smartcrop.com', '9000000001', 'Farmer', '123456'),
('Pratik Pagar', 'pratik@smartcrop.com', '9000000002', 'Buyer', '123456'),
('Krushna Burbane', 'krushna@smartcrop.com', '9000000003', 'Farmer', '123456');


-- ==========================================
-- 10. Sample Marketplace Products
-- ==========================================

INSERT INTO marketplace
(seller_id, product_name, category, price, unit, location, quality_rating, stock_status, description)
VALUES
(2, 'Wheat', 'Grains', 2400, 'Quintal', 'Kopargaon', 4.8, 'In Stock',
 'Fresh quality wheat'),

(3, 'Potato', 'Vegetables', 1800, 'Quintal', 'Shirdi', 4.5, 'In Stock',
 'Fresh potatoes'),

(2, 'Tomato', 'Vegetables', 2000, 'Quintal', 'Nashik', 4.7, 'In Stock',
 'Fresh tomatoes'),

(4, 'Onion', 'Vegetables', 2200, 'Quintal', 'Ahmednagar', 4.6, 'In Stock',
 'Good quality onions'),

(2, 'Mango', 'Fruits', 3500, 'Quintal', 'Nashik', 4.9, 'In Stock',
 'Fresh mangoes'),

(4, 'Rice', 'Grains', 2800, 'Quintal', 'Pune', 4.7, 'In Stock',
 'Premium quality rice');


-- ==========================================
-- 11. Sample Crop Health
-- ==========================================

INSERT INTO crop_health
(user_id, crop_name, health_status, soil_moisture, risk_level, growth_stage)
VALUES
(1, 'Wheat', 'Healthy', 68, 'Low Risk', 'Vegetative');


-- ==========================================
-- 12. Sample Weather Alert
-- ==========================================

INSERT INTO weather_alerts
(user_id, location, temperature, humidity, rainfall, wind_speed, alert_message, alert_type)
VALUES
(1, 'Kopargaon', 28, 65, 20, 12,
 'Weather conditions are normal.',
 'Information');


-- ==========================================
-- 13. Default Settings
-- ==========================================

INSERT INTO settings
(user_id)
VALUES
(1),
(2),
(3),
(4);


-- ==========================================
-- Database Setup Completed
-- ==========================================