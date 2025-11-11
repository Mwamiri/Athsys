-- Quick Fix SQL Script for Demo Users
-- Run this in phpMyAdmin SQL tab

-- Delete existing demo users
DELETE FROM users WHERE email IN ('admin@example.com', 'coach@example.com', 'athlete@example.com');

-- Insert demo users with correct password hash (password123)
INSERT INTO users (email, password_hash, first_name, last_name, role, is_active) VALUES
('admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 'administrator', 1),
('coach@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Coach', 'coach', 1),
('athlete@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane', 'Runner', 'athlete', 1);

-- Verify the users
SELECT id, email, first_name, last_name, role, is_active FROM users;
