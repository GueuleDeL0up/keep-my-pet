<?php
// MAMP MySQL connection settings
$host = "127.0.0.1"; // Use IP instead of localhost for MAMP
$port = 8889; // MAMP MySQL port
$dbname = "keepMyPet"; // Use underscore instead of hyphen (more compatible)
$username = "root";
$password = "root";

try {
    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed : " . $e->getMessage() . ". Check host={$host}, port={$port}, dbname={$dbname}");
}
