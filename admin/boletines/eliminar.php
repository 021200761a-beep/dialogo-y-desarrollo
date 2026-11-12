<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/funciones.php';
require_once __DIR__ . '/../../includes/uploads.php';

requerirAdministrador();


/*
 * Solo aceptar solicitudes POST.
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}


/*
 * Obtener y validar ID.
 */
$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id || $id <= 0) {
    header(
        'Location: index.php?mensaje=error'
    );
    exit;
}


/*
 * Buscar el boletín antes de eliminarlo.
 *
 * Necesitamos las rutas de la portada
 * y del PDF para poder eliminarlos
 * posteriormente.
 */
$stmt = $pdo->prepare(
    "SELECT
        id,
        foto_portada,
        archivo_pdf
     FROM boletines
     WHERE id = ?
     LIMIT 1"
);

$stmt->execute([$id]);

$boletin = $stmt->fetch();


/*
 * El boletín no existe.
 */
if (!$boletin) {
    header(
        'Location: index.php?mensaje=no_encontrado'
    );
    exit;
}


/*
 * Guardar las rutas antes de borrar
 * el registro.
 */
$fotoPortada = $boletin['foto_portada'];
$archivoPdf = $boletin['archivo_pdf'];


try {

    /*
     * Eliminar registro de MySQL.
     */
    $stmt = $pdo->prepare(
        "DELETE FROM boletines
         WHERE id = ?"
    );

    $stmt->execute([$id]);


    /*
     * Eliminar portada física.
     */
    if (!empty($fotoPortada)) {

        eliminarArchivoBoletin(
            $fotoPortada
        );
    }


    /*
     * Eliminar PDF físico.
     */
    if (!empty($archivoPdf)) {

        eliminarArchivoBoletin(
            $archivoPdf
        );
    }


    /*
     * Volver al listado.
     */
    header(
        'Location: index.php?mensaje=eliminado'
    );

    exit;

} catch (PDOException $e) {

    /*
     * No mostrar información interna
     * de la base de datos al usuario.
     */
    header(
        'Location: index.php?mensaje=error'
    );

    exit;
}