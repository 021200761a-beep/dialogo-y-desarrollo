<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/funciones.php';

requerirAdministrador();

$tituloPagina = 'Nuevo podcast';
$baseUrl = '../../';

$errores = [];

$titulo = '';
$url_embed = '';
$fecha_publicacion = date('Y-m-d');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /*
     * Obtener datos del formulario.
     */
    $titulo = trim(
        $_POST['titulo'] ?? ''
    );

    $url_embed = trim(
        $_POST['url_embed'] ?? ''
    );

    $fecha_publicacion = trim(
        $_POST['fecha_publicacion'] ?? ''
    );


    /*
     * Validar título.
     */
    if ($titulo === '') {

        $errores[] =
            'El título del podcast es obligatorio.';
    }


    /*
     * Validar URL.
     */
    if ($url_embed === '') {

        $errores[] =
            'La URL del podcast es obligatoria.';

    } elseif (!filter_var(
        $url_embed,
        FILTER_VALIDATE_URL
    )) {

        $errores[] =
            'La URL del podcast no es válida.';
    }


    /*
     * Validar fecha.
     */
    if ($fecha_publicacion === '') {

        $errores[] =
            'La fecha de publicación es obligatoria.';

    } else {

        $fechaValida =
            DateTime::createFromFormat(
                'Y-m-d',
                $fecha_publicacion
            );

        if (
            !$fechaValida ||
            $fechaValida->format('Y-m-d') !==
            $fecha_publicacion
        ) {

            $errores[] =
                'La fecha de publicación no es válida.';
        }
    }


    /*
     * Guardar si no hay errores.
     */
    if (empty($errores)) {

        try {

            $usuarioId =
                (int) $_SESSION['usuario']['id'];


            $stmt = $pdo->prepare(
                "INSERT INTO podcasts (
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


            header(
                'Location: index.php?mensaje=creado'
            );

            exit;

        } catch (PDOException $e) {

            $errores[] =
                'No se pudo guardar el podcast.';
        }
    }
}


require_once __DIR__ . '/../../includes/admin-header.php';
require_once __DIR__ . '/../../includes/admin-sidebar.php';
?>

<main class="admin-main">

    <div class="container-fluid py-4">

        <!-- ENCABEZADO -->

        <div
            class="d-flex justify-content-between align-items-center mb-4"
        >

            <div>

                <h1 class="h3 mb-1">
                    Nuevo podcast
                </h1>

                <p class="text-muted mb-0">
                    Registrar un nuevo podcast
                </p>

            </div>

            <div>

                <a
                    href="index.php"
                    class="btn btn-outline-secondary"
                >
                    <i class="bi bi-arrow-left"></i>
                    Volver
                </a>

            </div>

        </div>


        <!-- ERRORES -->

        <?php if (!empty($errores)): ?>

            <div
                class="alert alert-danger"
                role="alert"
            >

                <strong>
                    No se pudo guardar el podcast:
                </strong>

                <ul class="mb-0 mt-2">

                    <?php foreach ($errores as $error): ?>

                        <li>
                            <?= e($error) ?>
                        </li>

                    <?php endforeach; ?>

                </ul>

            </div>

        <?php endif; ?>


        <!-- FORMULARIO -->

        <div class="card dashboard-card">

            <div class="card-body">

                <form method="POST">

                    <div class="row g-4">

                        <!-- TÍTULO -->

                        <div class="col-12">

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

                        <div class="col-12">

                            <label
                                for="url_embed"
                                class="form-label"
                            >
                                URL del podcast
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="url"
                                class="form-control"
                                id="url_embed"
                                name="url_embed"
                                value="<?= e($url_embed) ?>"
                                placeholder="https://..."
                                required
                            >

                            <div class="form-text">

                                Introduce la URL del contenido
                                del podcast.

                            </div>

                        </div>


                        <!-- FECHA -->

                        <div class="col-12 col-md-6">

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


                        <!-- BOTONES -->

                        <div class="col-12">

                            <hr>

                            <div
                                class="d-flex flex-wrap gap-2 justify-content-end"
                            >

                                <a
                                    href="index.php"
                                    class="btn btn-outline-secondary"
                                >
                                    Cancelar
                                </a>

                                <button
                                    type="submit"
                                    class="btn btn-primary"
                                >
                                    <i class="bi bi-save"></i>
                                    Guardar podcast
                                </button>

                            </div>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

</main>

<?php
require_once __DIR__ . '/../../includes/admin-footer.php';
?>