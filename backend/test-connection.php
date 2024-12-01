<?php
$dsn = 'mysql:host=mysql-db;dbname=cinephoria;charset=utf8mb4';
$username = 'cinephoria';
$password = 'cinephoria';

try {
    $pdo = new PDO($dsn, $username, $password);
    echo "Connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
