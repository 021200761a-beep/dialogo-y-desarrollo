<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/funciones.php';

requerirAdministrador();

$tituloPagina = 'Editar Video';
$baseUrl = '../../';

$errores = [];

// Obtener ID
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php?mensaje=no_encontrado');
    exit;
}

// Buscar video
$stmt = $pdo->prepare(
    "SELECT
        id,
        titulo,
        url_embed,
        fecha_publicacion,
        usuario_id
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

// Valores iniciales
$titulo = $video['titulo'];
$url_embed = $video['url_embed'];
$fecha_publicacion = $video['fecha_publicacion'];


// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titulo = trim($_POST['titulo'] ?? '');
    $url_embed = trim($_POST['url_embed'] ?? '');
    $fecha_publicacion = trim($_POST['fecha_publicacion'] ?? '');

    // Validar título
    if ($titulo === '') {

        $errores[] = 'El título es obligatorio.';

    } elseif (mb_strlen($titulo) > 255) {

        $errores[] = 'El título no puede superar los 255 caracteres.';
    }


    // Validar URL / Embed
    if ($url_embed === '') {

        $errores[] = 'La URL del video es obligatoria.';

    } elseif (mb_strlen($url_embed) > 500) {

        $errores[] = 'La URL del video no puede superar los 500 caracteres.';
    }


    // Validar fecha
    if ($fecha_publicacion === '') {

        $errores[] = 'La fecha de publicación es obligatoria.';

    } else {

        $fechaValida = DateTime::createFromFormat(
            'Y-m-d',
            $fecha_publicacion
        );

        if (
            !$fechaValida ||
            $fechaValida->format('Y-m-d') !== $fecha_publicacion
        ) {
            $errores[] = 'La fecha de publicación no es válida.';
        }
    }


    // Actualizar
    if (empty($errores)) {

        try {

            $stmt = $pdo->prepare(
                "UPDATE videos
                 SET
                    titulo = ?,
                    url_embed = ?,
                    fecha_publicacion = ?
                 WHERE id = ?"
            );

            $stmt->execute([
                $titulo,
                $url_embed,
                $fecha_publicacion,
                $id
            ]);

            header('Location: index.php?mensaje=editado');
            exit;

        } catch (PDOException $e) {

            $errores[] = 'No se pudo actualizar el video. Inténtalo nuevamente.';
        }
    }
}

?>

<?php
require_once __DIR__ . '/../../includes/admin-header.php';
require_once __DIR__ . '/../../includes/admin-sidebar.php';
?>

<main class="admin-main">

    <div class="container-fluid py-4">

        <!-- Encabezado -->
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4"
        >

            <div>
                <h1 class="h3 mb-1">Editar video</h1>

                <p class="text-muted mb-0">
                    Modificar la información del video
                </p>
            </div>

            <div>
                <a
                    href="index.php"
                    class="btn btn-outline-secondary"
                >
                    <i class="bi bi-arrow-left me-1"></i>
                    Volver
                </a>
            </div>

        </div>


        <!-- Errores -->
        <?php if (!empty($errores)): ?>

            <div class="alert alert-danger">

                <div class="fw-semibold mb-2">
                    No se pudo actualizar el video:
                </div>

                <ul class="mb-0">

                    <?php foreach ($errores as $error): ?>

                        <li><?= e($error) ?></li>

                    <?php endforeach; ?>

                </ul>

            </div>

        <?php endif; ?>


        <!-- Formulario -->
        <div class="card dashboard-card">

            <div class="card-body">

                <form method="POST" action="">

                    <!-- Título -->
                    <div class="mb-3">

                        <label
                            for="titulo"
                            class="form-label"
                        >
                            Título
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="titulo"
                            name="titulo"
                            value="<?= e($titulo) ?>"
                            maxlength="255"
                            required
                        >

                    </div>


                    <!-- URL / Embed -->
                    <div class="mb-3">

                        <label
                            for="url_embed"
                            class="form-label"
                        >
                            URL / Embed
                            <span class="text-danger">*</span>
                        </label>

                        <textarea
                            class="form-control"
                            id="url_embed"
                            name="url_embed"
                            rows="4"
                            maxlength="500"
                            required
                        ><?= e($url_embed) ?></textarea>

                        <div class="form-text">
                            Puedes modificar la URL o el código embed del video.
                        </div>

                    </div>


                    <!-- Fecha -->
                    <div class="mb-4">

                        <label
                            for="fecha_publicacion"
                            class="form-label"
                        >
                            Fecha de publicación
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="date"
                            class="form-control"
                            id="fecha_publicacion"
                            name="fecha_publicacion"
                            value="<?= e($fecha_publicacion) ?>"
                            required
                        >

                    </div>


                    <!-- Botones -->
                    <div class="d-flex flex-column flex-sm-row gap-2">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i class="bi bi-save me-1"></i>
                            Guardar cambios
                        </button>

                        <a
                            href="index.php"
                            class="btn btn-outline-secondary"
                        >
                            <i class="bi bi-x-circle me-1"></i>
                            Cancelar
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </div>

</main>

<?php
require_once __DIR__ . '/../../includes/admin-footer.php';
?>