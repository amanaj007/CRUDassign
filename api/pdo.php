<?php
require_once 'config.php';

$dsn = getenv('DB_DSN');
$user = getenv('DB_USER');
$pass = getenv('DB_PASSWORD');

if (!$dsn) {
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: '3306';
    $name = getenv('DB_NAME') ?: 'resume';
    $dsn = "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4";
    $user = $user ?: (getenv('DB_USER') ?: 'root');
    $pass = $pass ?: (getenv('DB_PASSWORD') ?: '');
}

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log($e->getMessage());
    die('Unable to connect to the database.');
}
