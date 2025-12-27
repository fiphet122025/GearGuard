-- =========================================
-- GearGuard : Maintenance Tracker Database
-- =========================================

CREATE DATABASE IF NOT EXISTS gearguard
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE gearguard;

-- =========================================
-- 1. Departments
-- =========================================
CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_name VARCHAR(100) NOT NULL UNIQUE
);

-- =========================================
-- 2. Users
-- =========================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','manager','technician','employee') NOT NULL,
    department_id INT NULL,
    avatar VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_users_department
        FOREIGN KEY (department_id)
        REFERENCES departments(id)
        ON DELETE SET NULL
);

-- =========================================
-- 3. Maintenance Teams
-- =========================================
CREATE TABLE maintenance_teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_name VARCHAR(100) NOT NULL UNIQUE
);

-- =========================================
-- 4. Maintenance Team Members
-- =========================================
CREATE TABLE maintenance_team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    user_id INT NOT NULL,

    CONSTRAINT fk_team_member_team
        FOREIGN KEY (team_id)
        REFERENCES maintenance_teams(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_team_member_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    UNIQUE KEY uniq_team_user (team_id, user_id)
);

-- =========================================
-- 5. Equipment
-- =========================================
CREATE TABLE equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipment_name VARCHAR(150) NOT NULL,
    serial_number VARCHAR(100) NOT NULL UNIQUE,
    purchase_date DATE,
    warranty_end_date DATE,
    location VARCHAR(150),
    department_id INT NOT NULL,
    assigned_user_id INT NULL,
    maintenance_team_id INT NOT NULL,
    is_scrapped TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_equipment_department
        FOREIGN KEY (department_id)
        REFERENCES departments(id),

    CONSTRAINT fk_equipment_user
        FOREIGN KEY (assigned_user_id)
        REFERENCES users(id)
        ON DELETE SET NULL,

    CONSTRAINT fk_equipment_team
        FOREIGN KEY (maintenance_team_id)
        REFERENCES maintenance_teams(id)
);

-- =========================================
-- 6. Maintenance Requests
-- =========================================
CREATE TABLE maintenance_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subject VARCHAR(255) NOT NULL,
    request_type ENUM('Corrective','Preventive') NOT NULL,
    equipment_id INT NOT NULL,
    maintenance_team_id INT NOT NULL,
    assigned_technician_id INT NULL,
    status ENUM('New','In Progress','Repaired','Scrap') DEFAULT 'New',
    scheduled_date DATE NULL,
    duration_hours DECIMAL(5,2) NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_request_equipment
        FOREIGN KEY (equipment_id)
        REFERENCES equipment(id),

    CONSTRAINT fk_request_team
        FOREIGN KEY (maintenance_team_id)
        REFERENCES maintenance_teams(id),

    CONSTRAINT fk_request_technician
        FOREIGN KEY (assigned_technician_id)
        REFERENCES users(id)
        ON DELETE SET NULL,

    CONSTRAINT fk_request_creator
        FOREIGN KEY (created_by)
        REFERENCES users(id),

    INDEX idx_status (status),
    INDEX idx_schedule (scheduled_date)
);

-- =========================================
-- 7. Request Logs (Audit Trail)
-- =========================================
CREATE TABLE request_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    note TEXT,
    old_status ENUM('New','In Progress','Repaired','Scrap') NULL,
    new_status ENUM('New','In Progress','Repaired','Scrap') NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_log_request
        FOREIGN KEY (request_id)
        REFERENCES maintenance_requests(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_log_user
        FOREIGN KEY (created_by)
        REFERENCES users(id)
);

-- =========================================
-- SAMPLE BASE DATA (OPTIONAL BUT RECOMMENDED)
-- =========================================

INSERT INTO departments (department_name) VALUES
('IT'),
('Production'),
('Maintenance');

INSERT INTO maintenance_teams (team_name) VALUES
('IT Support'),
('Mechanical'),
('Electrical');
