-- Blood Bank Management System Database
-- Created for Final Year College Project

CREATE DATABASE IF NOT EXISTS bloodbank;
USE bloodbank;

-- Admin table
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin
INSERT INTO admin (username, password, email) VALUES ('admin', 'admin123', 'admin@bloodbank.com');

-- Donors table
CREATE TABLE IF NOT EXISTS donors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    phone VARCHAR(15) NOT NULL,
    blood_group ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
    age INT NOT NULL,
    gender ENUM('Male','Female','Other') NOT NULL,
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    last_donation DATE,
    total_donations INT DEFAULT 0,
    status ENUM('Active','Inactive') DEFAULT 'Active',
    password VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Blood stock table
CREATE TABLE IF NOT EXISTS blood_stock (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blood_group ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL UNIQUE,
    units_available INT DEFAULT 0,
    units_used INT DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Initialize blood stock
INSERT INTO blood_stock (blood_group, units_available) VALUES
('A+', 15), ('A-', 8), ('B+', 20), ('B-', 5),
('AB+', 10), ('AB-', 3), ('O+', 25), ('O-', 7);

-- Blood requests table
CREATE TABLE IF NOT EXISTS blood_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_name VARCHAR(100) NOT NULL,
    patient_age INT,
    blood_group ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
    units_needed INT NOT NULL,
    hospital_name VARCHAR(150),
    contact_name VARCHAR(100),
    contact_phone VARCHAR(15) NOT NULL,
    contact_email VARCHAR(100),
    reason TEXT,
    urgency ENUM('Normal','Urgent','Critical') DEFAULT 'Normal',
    status ENUM('Pending','Approved','Rejected','Fulfilled') DEFAULT 'Pending',
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Donations table (records each donation)
CREATE TABLE IF NOT EXISTS donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    donor_id INT,
    donor_name VARCHAR(100),
    blood_group ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') NOT NULL,
    units INT DEFAULT 1,
    donation_date DATE NOT NULL,
    notes TEXT,
    FOREIGN KEY (donor_id) REFERENCES donors(id) ON DELETE SET NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Hospitals table
CREATE TABLE IF NOT EXISTS hospitals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(15),
    address TEXT,
    city VARCHAR(100),
    contact_person VARCHAR(100),
    status ENUM('Active','Inactive') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample donors
INSERT INTO donors (name, email, phone, blood_group, age, gender, city, state, last_donation, total_donations) VALUES
('Rahul Sharma', 'rahul@email.com', '9876543210', 'O+', 25, 'Male', 'Pune', 'Maharashtra', '2025-12-01', 3),
('Priya Patel', 'priya@email.com', '9876543211', 'A+', 28, 'Female', 'Mumbai', 'Maharashtra', '2025-11-15', 5),
('Amit Singh', 'amit@email.com', '9876543212', 'B+', 32, 'Male', 'Pune', 'Maharashtra', '2026-01-10', 2),
('Sneha Joshi', 'sneha@email.com', '9876543213', 'AB+', 24, 'Female', 'Nashik', 'Maharashtra', '2025-10-20', 1),
('Vikram Rao', 'vikram@email.com', '9876543214', 'O-', 35, 'Male', 'Pune', 'Maharashtra', '2025-09-05', 7),
('Meera Nair', 'meera@email.com', '9876543215', 'A-', 29, 'Female', 'Pune', 'Maharashtra', '2026-02-01', 4);

-- Sample requests
INSERT INTO blood_requests (patient_name, patient_age, blood_group, units_needed, hospital_name, contact_name, contact_phone, urgency, status) VALUES
('Suresh Kumar', 45, 'O+', 2, 'Ruby Hall Clinic', 'Raj Kumar', '9800000001', 'Urgent', 'Pending'),
('Anita Desai', 60, 'A+', 1, 'KEM Hospital', 'Ramesh Desai', '9800000002', 'Normal', 'Approved'),
('Baby Sharma', 5, 'B-', 3, 'Sassoon Hospital', 'Pooja Sharma', '9800000003', 'Critical', 'Pending');
