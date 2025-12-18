CREATE DATABASE IF NOT EXISTS keepMyPet
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE keepMyPet;

-- Suppression des tables (ordre inverse des dépendances)
DROP TABLE IF EXISTS Advertisement;
DROP TABLE IF EXISTS Historical;
DROP TABLE IF EXISTS Own;
DROP TABLE IF EXISTS Animal;
DROP TABLE IF EXISTS FAQ;
DROP TABLE IF EXISTS Legal_Notice;
DROP TABLE IF EXISTS CGU;
DROP TABLE IF EXISTS users;

-- =========================
-- TABLE FAQ
-- =========================
CREATE TABLE FAQ (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question VARCHAR(100) NOT NULL,
    answer VARCHAR(100) NOT NULL
);

-- =========================
-- TABLE CGU
-- =========================
CREATE TABLE CGU (
    id INT AUTO_INCREMENT PRIMARY KEY
);

-- =========================
-- TABLE LEGAL NOTICE
-- =========================
CREATE TABLE Legal_Notice (
    id INT AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(100) NOT NULL
);

-- =========================
-- TABLE USERS
-- =========================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mail VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(32) NOT NULL,
    first_name VARCHAR(32) NOT NULL,
    last_name VARCHAR(32) NOT NULL,
    role VARCHAR(10) NOT NULL DEFAULT 'user',
    gender ENUM('Homme', 'Femme') NOT NULL,
    note FLOAT NOT NULL,
    theme ENUM('white', 'dark') NOT NULL,
    lang ENUM('fr', 'eng') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- TABLE ANIMAL
-- =========================
CREATE TABLE Animal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(20) NOT NULL,
    race VARCHAR(20) NOT NULL,
    gender ENUM('male', 'female', 'femelle') NOT NULL,
    birthdate DATE NOT NULL,
    note FLOAT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =========================
-- TABLE OWN
-- =========================
CREATE TABLE Own (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    animal_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (animal_id) REFERENCES Animal(id) ON DELETE CASCADE
);

-- =========================
-- TABLE HISTORICAL
-- =========================
CREATE TABLE Historical (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    note FLOAT NOT NULL,
    comment VARCHAR(200) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- =========================
-- TABLE ADVERTISEMENT
-- =========================
CREATE TABLE Advertisement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    animal_id INT NOT NULL,
    title VARCHAR(30) NOT NULL,
    description TEXT NOT NULL,
    localisation TEXT NOT NULL,
    start_date DATE NOT NULL,
    start_hour INT NOT NULL,
    end_date DATE NOT NULL,
    end_hour INT NOT NULL,
    price FLOAT NOT NULL,
    type ENUM('gardiennage', 'promenade') NOT NULL,
    FOREIGN KEY (animal_id) REFERENCES Animal(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
