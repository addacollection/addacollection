<?php
/**
 * Adda Collection - Centralized Database Configuration
 * Handles secure Aiven SSL connection.
 */

$host = 'mysql-7efca4b-addacollection.i.aivencloud.com';
$db   = 'defaultdb';
$user = 'avnadmin';
$pass = 'AVNS_h0ihm4NmXYmZcJ8ISQM';
$port = '13574';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    // SSL Configuration
    PDO::MYSQL_ATTR_SSL_CA       => __DIR__ . '/ca.pem',
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
];

try {
    // $pdo variable yahan global ban jayega jab tum ise require_once karoge
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Production mein error_log use karna behtar hai
    error_log("Database Connection Failed: " . $e->getMessage());
    die("Database Connection Error. Please try again later.");
}
?>