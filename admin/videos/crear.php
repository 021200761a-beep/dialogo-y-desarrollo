<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/funciones.php';

requerirAdministrador();

$tituloPagina = 'Nuevo Video';
$baseUrl = '../../';

$errores = [];

$titulo = '';
$url_embed = '';
$fecha_publicacion = date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titulo = trim($_POST['titulo'] ?? '');
    $url_embed = trim($_POST['url_embed'] ?? '');
    $fecha_publicacion = trim($_POST['fecha_publicacion'] ?? '');

    /*
    |--------------------------------------------------------------------------
    | Obtener usuario de la sesión
    |--------------------------------------------------------------------------
    */

    $usuarioSesion = $_SESSION['usuario'] ?? null;

    $usuarioId = null;

    if (is_array($usuarioSesion) && isset($usuarioSesion['id'])) {
        $usuarioId = (int) $usuarioSesion['id'];
    }

    if (!$usuarioId) {
        $errores[] = 'No se pudo identificar al usuario de la sesión.';
    }

    /*
    |--------------------------------------------------------------------------
    | Validar título
    |--------------------------------------------------------------------------
    */

    if ($titulo === '') {

        $errores[] = 'El título es obligatorio.';

    } elseif (mb_strlen($titulo) > 255) {

        $errores[] = 'El título no puede superar los 255 caracteres.';
    }

    /*
    |--------------------------------------------------------------------------
    | Validar URL / Embed
    |--------------------------------------------------------------------------
    */

    if ($url_embed === '') {

        $errores[] = 'La URL del video es obligatoria.';

    } elseif (mb_strlen($url_embed) > 500) {

        $errores[] = 'La URL del video no puede superar los 500 caracteres.';
    }

    /*
    |--------------------------------------------------------------------------
    | Validar fecha
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Guardar video
    |--------------------------------------------------------------------------
    */

    if (empty($errores)) {

        try {

            $stmt = $pdo->prepare(
                "INSERT INTO videos (
                    titulo,
                    url_embed,
                    fecha_publicacion,
                    usuario_id
                ) VALUES (?, ?, ?, ?)"
            );

            $stmt->execute([
                $titulo,
                $url_embed,
                $fecha_publicacion,
                $usuarioId
            ]);

            header('Location: index.php?mensaje=creado');
            exit;

        } catch (PDOException $e) {

            $errores[] = 'No se pudo crear el video. Inténtalo nuevamente.';
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

                <h1 class="h3 mb-1">
                    Nuevo video
                </h1>

                <p class="text-muted mb-0">
                    Registrar un nuevo video
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
                    No se pudo crear el video:
                </div>

                <ul class="mb-0">

                    <?php foreach ($errores as $error): ?>

                        <li>
                            <?= e($error) ?>
                        </li>

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


                    <!-- URL -->
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
                            Ingresa la URL o código embed que utilizará el video.
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
                            Crear video
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