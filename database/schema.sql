-- Pokemon Management System Database Schema
-- Create database
CREATE DATABASE IF NOT EXISTS pokemon_management;
USE pokemon_management;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
);

-- Pokemon collection table
CREATE TABLE pokemon_collection (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    pokemon_id INT NOT NULL,
    pokemon_name VARCHAR(100) NOT NULL,
    pokemon_type1 VARCHAR(50),
    pokemon_type2 VARCHAR(50),
    pokemon_sprite VARCHAR(500),
    pokemon_abilities TEXT,
    nickname VARCHAR(100),
    level_caught INT DEFAULT 1,
    date_caught TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_pokemon_id (pokemon_id),
    INDEX idx_pokemon_name (pokemon_name),
    UNIQUE KEY unique_user_pokemon (user_id, pokemon_id)
);

-- Sessions table for better session management (optional but recommended)
CREATE TABLE user_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    session_id VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_session_id (session_id),
    INDEX idx_user_id (user_id),
    INDEX idx_expires (expires_at)
);

-- Insert sample data (optional)
-- INSERT INTO users (username, email, password_hash) VALUES 
-- ('admin', 'admin@pokemon.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');