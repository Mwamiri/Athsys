-- Athlete Results System Database Schema for MySQL
-- Run this in phpMyAdmin or MySQL command line

-- Create database (if not already created in cPanel)
-- CREATE DATABASE IF NOT EXISTS athlete_results CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE athlete_results;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    role ENUM('coach', 'athlete', 'administrator') NOT NULL DEFAULT 'athlete',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Teams table
CREATE TABLE IF NOT EXISTS teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    code VARCHAR(10) UNIQUE,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Athletes table
CREATE TABLE IF NOT EXISTS athletes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    date_of_birth DATE,
    gender ENUM('M', 'F', 'Other') NOT NULL,
    team_id INT,
    category VARCHAR(50),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE SET NULL,
    INDEX idx_name (last_name, first_name),
    INDEX idx_gender (gender),
    INDEX idx_team (team_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Events table
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    category ENUM('Track', 'Field', 'CrossCountry') NOT NULL,
    discipline VARCHAR(50),
    unit VARCHAR(20) NOT NULL,
    record_type ENUM('Time', 'Distance', 'Height', 'Score') NOT NULL,
    gender ENUM('M', 'F', 'Mixed'),
    age_category VARCHAR(50),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Competitions table
CREATE TABLE IF NOT EXISTS competitions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(300) NOT NULL,
    date DATE NOT NULL,
    location VARCHAR(200),
    type VARCHAR(50),
    status ENUM('Scheduled', 'InProgress', 'Completed') DEFAULT 'Scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_date (date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Competition Events junction table
CREATE TABLE IF NOT EXISTS competition_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    competition_id INT NOT NULL,
    event_id INT NOT NULL,
    scheduled_time DATETIME,
    status VARCHAR(20) DEFAULT 'Scheduled',
    FOREIGN KEY (competition_id) REFERENCES competitions(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    UNIQUE KEY unique_comp_event (competition_id, event_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Results table
CREATE TABLE IF NOT EXISTS results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    athlete_id INT NOT NULL,
    competition_id INT NOT NULL,
    event_id INT NOT NULL,
    performance VARCHAR(50) NOT NULL,
    performance_numeric DECIMAL(10,3),
    unit VARCHAR(20) NOT NULL,
    placement INT,
    is_personal_record TINYINT(1) DEFAULT 0,
    is_season_best TINYINT(1) DEFAULT 0,
    is_national_record TINYINT(1) DEFAULT 0,
    wind_speed DECIMAL(3,1),
    weather_conditions TEXT,
    course_info TEXT,
    recorded_by INT,
    recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (athlete_id) REFERENCES athletes(id) ON DELETE CASCADE,
    FOREIGN KEY (competition_id) REFERENCES competitions(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_athlete_event (athlete_id, event_id),
    INDEX idx_competition (competition_id),
    INDEX idx_performance (performance_numeric)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert demo users (password is 'password123' for all)
INSERT INTO users (email, password_hash, first_name, last_name, role) VALUES
('coach@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Coach', 'coach'),
('athlete@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane', 'Runner', 'athlete'),
('admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', 'administrator');

-- Insert sample teams
INSERT INTO teams (name, code) VALUES
('Athletics Kenya', 'AK'),
('Nairobi Club', 'NC'),
('Mombasa Athletics', 'MA');

-- Insert sample athletes
INSERT INTO athletes (first_name, last_name, date_of_birth, gender, category, team_id) VALUES
('John', 'Kipchoge', '1995-05-15', 'M', 'Senior', 1),
('Mary', 'Wanjiku', '1998-08-22', 'F', 'Senior', 2),
('David', 'Mutai', '2000-03-10', 'M', 'Junior', 1),
('Grace', 'Chebet', '1997-11-05', 'F', 'Senior', 3),
('Peter', 'Kamau', '1999-07-18', 'M', 'Senior', 2);

-- Insert sample events
INSERT INTO events (name, category, discipline, unit, record_type, gender) VALUES
('100m Sprint', 'Track', 'Sprint', 'seconds', 'Time', 'M'),
('100m Sprint', 'Track', 'Sprint', 'seconds', 'Time', 'F'),
('1500m', 'Track', 'Distance', 'seconds', 'Time', 'M'),
('1500m', 'Track', 'Distance', 'seconds', 'Time', 'F'),
('Long Jump', 'Field', 'Jump', 'meters', 'Distance', 'M'),
('Long Jump', 'Field', 'Jump', 'meters', 'Distance', 'F'),
('5km Cross Country', 'CrossCountry', 'CrossCountry', 'seconds', 'Time', 'M'),
('5km Cross Country', 'CrossCountry', 'CrossCountry', 'seconds', 'Time', 'F');

-- Insert sample competitions
INSERT INTO competitions (name, date, location, type, status) VALUES
('Kenya National Championships 2024', '2024-06-15', 'Nairobi', 'Track', 'Completed'),
('Cross Country Championships 2024', '2024-02-10', 'Eldoret', 'CrossCountry', 'Completed'),
('Regional Athletics Meet', '2024-04-20', 'Mombasa', 'Track', 'Completed');

-- Insert sample results
INSERT INTO results (athlete_id, competition_id, event_id, performance, performance_numeric, unit, placement, is_personal_record, is_season_best) VALUES
(1, 1, 1, '10.25', 10.25, 'seconds', 1, 1, 1),
(2, 1, 2, '11.85', 11.85, 'seconds', 1, 1, 1),
(3, 1, 3, '3:45.20', 225.20, 'seconds', 2, 0, 1),
(4, 3, 6, '6.45', 6.45, 'meters', 1, 1, 1),
(5, 3, 1, '10.67', 10.67, 'seconds', 3, 0, 1),
(1, 2, 7, '15:25', 925, 'seconds', 1, 1, 1),
(2, 2, 8, '17:45', 1065, 'seconds', 1, 1, 1);