<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/funciones.php';

requerirAdministrador();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php?error=podcast_invalido');
    exit;
}

/*
|--------------------------------------------------------------------------
| Verificar que el podcast exista
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT id
    FROM podcasts
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$id]);

$podcast = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$podcast) {
    header('Location: index.php?error=podcast_no_encontrado');
    exit;
}

/*
|--------------------------------------------------------------------------
| Eliminar podcast
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->prepare("
        DELETE FROM podcasts
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    header('Location: index.php?mensaje=eliminado');
    exit;

} catch (PDOException $e) {

    header('Location: index.php?error=no_se_pudo_eliminar');
    exit;
}
