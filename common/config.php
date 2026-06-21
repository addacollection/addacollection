<?php
// Aiven ki nayi details yahan daalo
define('DB_HOST', 'mysql-7efca4b-addacollection.i.aivencloud.com');
define('DB_USER', 'avnadmin');
define('DB_PASS', 'AVNS_h0ihm4NmXYmZcJ8ISQM'); // Aiven ka password yahan daalo
define('DB_NAME', 'defaultdb'); 
define('DB_PORT', '13574');

try {
    // Port ko string mein add kar diya hai
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}