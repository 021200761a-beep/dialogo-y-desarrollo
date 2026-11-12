<?php

$host = 'sql206.infinityfree.com';
$dbname = 'if0_42984043_revista';
$username = 'if0_42984043';
$password = 'pgDkMXDpfrtx98';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    echo "Conexión exitosa";

} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}