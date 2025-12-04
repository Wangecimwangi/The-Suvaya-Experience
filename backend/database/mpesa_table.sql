-- M-Pesa Transactions Table
-- Run this SQL to add M-Pesa payment support to your database

USE suvaya_db;

-- Create M-Pesa transactions table
CREATE TABLE IF NOT EXISTS mpesa_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    merchant_request_id VARCHAR(100) UNIQUE,
    checkout_request_id VARCHAR(100) UNIQUE NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    account_reference VARCHAR(100),
    transaction_desc VARCHAR(255),
    result_code VARCHAR(10),
    result_desc TEXT,
    mpesa_receipt_number VARCHAR(100),
    transaction_date DATETIME,
    status ENUM('pending', 'completed', 'failed', 'timeout') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_checkout_request (checkout_request_id),
    INDEX idx_phone (phone_number),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
);

-- Add M-Pesa payment fields to reservations table
ALTER TABLE reservations
ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50) DEFAULT 'cash',
ADD COLUMN IF NOT EXISTS mpesa_receipt VARCHAR(100),
ADD INDEX idx_mpesa_receipt (mpesa_receipt);

-- Add M-Pesa payment fields to orders table
ALTER TABLE orders
ADD COLUMN IF NOT EXISTS payment_method VARCHAR(50) DEFAULT 'cash',
ADD COLUMN IF NOT EXISTS payment_status VARCHAR(50) DEFAULT 'pending',
ADD COLUMN IF NOT EXISTS mpesa_receipt VARCHAR(100),
ADD INDEX idx_mpesa_receipt (mpesa_receipt);

-- Show tables to confirm
SHOW TABLES;
SELECT 'M-Pesa tables created successfully!' as message;
