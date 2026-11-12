<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/funciones.php';

requerirAdministrador();

// Obtener ID enviado por POST
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php?mensaje=no_encontrado');
    exit;
}


// Verificar que el video exista
$stmt = $pdo->prepare(
    "SELECT id
     FROM videos
     WHERE id = ?
     LIMIT 1"
);

$stmt->execute([$id]);

$video = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$video) {
    header('Location: index.php?mensaje=no_encontrado');
    exit;
}


// Eliminar video
try {

    $stmt = $pdo->prepare(
        "DELETE FROM videos
         WHERE id = ?"
    );

    $stmt->execute([$id]);

    header('Location: index.php?mensaje=eliminado');
    exit;

} catch (PDOException $e) {

    header('Location: index.php?mensaje=error');
    exit;
}