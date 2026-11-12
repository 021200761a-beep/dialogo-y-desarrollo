<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/funciones.php';

requerirAdministrador();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php?mensaje=no_encontrado');
    exit;
}

/*
|--------------------------------------------------------------------------
| Verificar que el autor exista
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare(
    "SELECT id
     FROM autores
     WHERE id = ?
     LIMIT 1"
);

$stmt->execute([$id]);

$autor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$autor) {
    header('Location: index.php?mensaje=no_encontrado');
    exit;
}

/*
|--------------------------------------------------------------------------
| Verificar si el autor está siendo utilizado en reportajes
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare(
    "SELECT COUNT(*)
     FROM reportajes
     WHERE autor_id = ?"
);

$stmt->execute([$id]);

$cantidadReportajes = (int) $stmt->fetchColumn();

if ($cantidadReportajes > 0) {
    header('Location: index.php?mensaje=en_uso');
    exit;
}

/*
|--------------------------------------------------------------------------
| Eliminar autor
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->prepare(
        "DELETE FROM autores
         WHERE id = ?"
    );

    $stmt->execute([$id]);

    header('Location: index.php?mensaje=eliminado');
    exit;

} catch (PDOException $e) {

    header('Location: index.php?mensaje=error');
    exit;
}