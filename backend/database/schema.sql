-- Create Database
CREATE DATABASE IF NOT EXISTS suvaya_db;
USE suvaya_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Menu Items Table
CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2),
    image VARCHAR(255),
    kg DECIMAL(5, 2),
    available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Menu Packages Table (for items with packages)
CREATE TABLE IF NOT EXISTS menu_packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    menu_item_id INT NOT NULL,
    includes TEXT, -- JSON array of included items
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE
);

-- Baking Classes Table (for menu items that are classes)
CREATE TABLE IF NOT EXISTS baking_classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    menu_item_id INT NOT NULL,
    details TEXT,
    price DECIMAL(10, 2),
    duration INT, -- in minutes
    max_participants INT DEFAULT 10,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE
);

-- Events Table
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    date DATE NOT NULL,
    time VARCHAR(20) NOT NULL,
    location VARCHAR(200),
    description TEXT,
    price DECIMAL(10, 2),
    image VARCHAR(255),
    spots_available INT DEFAULT 10,
    event_type VARCHAR(50), -- 'class', 'private', 'group'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Reservations Table
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    date DATE NOT NULL,
    time VARCHAR(20) NOT NULL,
    guests INT DEFAULT 1,
    notes TEXT,
    status VARCHAR(20) DEFAULT 'pending', -- pending, confirmed, cancelled, completed
    deposit_paid BOOLEAN DEFAULT FALSE,
    deposit_amount DECIMAL(10, 2) DEFAULT 0.00,
    total_amount DECIMAL(10, 2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    deposit_paid BOOLEAN DEFAULT FALSE,
    deposit_amount DECIMAL(10, 2) DEFAULT 0.00,
    balance_due DECIMAL(10, 2) DEFAULT 0.00,
    status VARCHAR(20) DEFAULT 'pending', -- pending, confirmed, in_progress, completed, cancelled
    delivery_date DATE,
    delivery_address TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_item_id INT,
    item_name VARCHAR(100) NOT NULL,
    quantity INT DEFAULT 1,
    price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    notes TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE SET NULL
);

-- Bookings Table (for calendar bookings)
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL UNIQUE,
    booked BOOLEAN DEFAULT TRUE,
    booking_type VARCHAR(50), -- reservation, event, order
    reference_id INT, -- ID of the related reservation/event/order
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Contact Messages Table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status VARCHAR(20) DEFAULT 'new', -- new, read, responded
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO users (name, email, phone, password, is_admin) VALUES
('Admin', 'admin@suvaya.com', '+254700000000', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', TRUE)
ON DUPLICATE KEY UPDATE email=email;

-- Insert sample menu items
INSERT INTO menu_items (name, category, description, price, image, kg, available) VALUES
('Chocolate Cake', 'Cakes', 'Rich chocolate cake with chocolate frosting', 3500.00, '/public/Menu/chocolate-cake.jpg', 1.0, TRUE),
('Vanilla Sponge Cake', 'Cakes', 'Light and fluffy vanilla cake', 3000.00, '/public/Menu/vanilla-cake.jpg', 1.0, TRUE),
('Red Velvet Cake', 'Cakes', 'Classic red velvet with cream cheese frosting', 4000.00, '/public/Menu/red-velvet.jpg', 1.5, TRUE),
('Croissants', 'Pastries', 'Buttery French croissants', 150.00, '/public/Menu/croissant.jpg', NULL, TRUE),
('Cupcakes (6 pack)', 'Desserts', 'Assorted flavor cupcakes', 600.00, '/public/Menu/cupcakes.jpg', NULL, TRUE)
ON DUPLICATE KEY UPDATE name=name;

-- Insert sample events
INSERT INTO events (title, date, time, location, description, price, spots_available, event_type) VALUES
('Beginner Baking Class', '2025-12-15', '10:00 AM - 1:00 PM', 'Suvaya Bakery Studio', 'Learn the basics of baking', 3500.00, 8, 'class'),
('Advanced Cake Decoration', '2025-12-20', '2:00 PM - 5:00 PM', 'Suvaya Bakery Studio', 'Master frosting techniques', 5000.00, 6, 'class')
ON DUPLICATE KEY UPDATE title=title;
