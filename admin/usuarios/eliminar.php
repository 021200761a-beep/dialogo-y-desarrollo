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
| Obtener usuario actual de la sesión
|--------------------------------------------------------------------------
*/

$usuarioSesion = $_SESSION['usuario'] ?? null;

$idUsuarioSesion = null;

if (is_array($usuarioSesion) && isset($usuarioSesion['id'])) {
    $idUsuarioSesion = (int) $usuarioSesion['id'];
}

/*
|--------------------------------------------------------------------------
| No permitir eliminar el propio usuario
|--------------------------------------------------------------------------
*/

if ($idUsuarioSesion !== null && $id === $idUsuarioSesion) {

    header('Location: index.php?mensaje=no_eliminar_propio');
    exit;
}

/*
|--------------------------------------------------------------------------
| Verificar que el usuario exista
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare(
    "SELECT id
     FROM usuarios
     WHERE id = ?
     LIMIT 1"
);

$stmt->execute([$id]);

$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {

    header('Location: index.php?mensaje=no_encontrado');
    exit;
}

/*
|--------------------------------------------------------------------------
| Verificar relaciones con otras tablas
|--------------------------------------------------------------------------
*/

$tablasRelacionadas = [
    'reportajes',
    'noticias',
    'boletines',
    'podcasts',
    'videos'
];

foreach ($tablasRelacionadas as $tabla) {

    $stmt = $pdo->prepare(
        "SELECT COUNT(*)
         FROM {$tabla}
         WHERE usuario_id = ?"
    );

    $stmt->execute([$id]);

    $cantidad = (int) $stmt->fetchColumn();

    if ($cantidad > 0) {

        header('Location: index.php?mensaje=en_uso');
        exit;
    }
}

/*
|--------------------------------------------------------------------------
| Eliminar usuario
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->prepare(
        "DELETE FROM usuarios
         WHERE id = ?"
    );

    $stmt->execute([$id]);

    header('Location: index.php?mensaje=eliminado');
    exit;

} catch (PDOException $e) {

    header('Location: index.php?mensaje=error');
    exit;
}