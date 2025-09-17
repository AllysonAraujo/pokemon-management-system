-- Pokemon Management System Database Schema
-- Created for MySQL Workbench

-- Create database
CREATE DATABASE IF NOT EXISTS pokemon_management_system;
USE pokemon_management_system;

-- Users table for authentication
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
);

-- Pokemon collection table to store user's pokemon
CREATE TABLE pokemon_collection (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    pokemon_id INT NOT NULL,
    pokemon_name VARCHAR(100) NOT NULL,
    nickname VARCHAR(100),
    level INT DEFAULT 1,
    pokemon_sprite VARCHAR(500),
    pokemon_types JSON,
    pokemon_abilities JSON,
    caught_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_pokemon_id (pokemon_id),
    INDEX idx_pokemon_name (pokemon_name),
    INDEX idx_caught_date (caught_date)
);

-- Sessions table for session management
CREATE TABLE sessions (
    id VARCHAR(128) PRIMARY KEY,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at)
);

-- Sample data for testing
INSERT INTO users (username, email, password_hash) VALUES 
('admin', 'admin@pokemon.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); -- password: 'password'

-- Indexes for performance optimization
CREATE INDEX idx_pokemon_collection_user_pokemon ON pokemon_collection(user_id, pokemon_id);
CREATE INDEX idx_sessions_cleanup ON sessions(expires_at);