<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/funciones.php';
require_once __DIR__ . '/../../includes/uploads.php';

requerirAdministrador();

$tituloPagina = 'Editar boletín';
$baseUrl = '../../';

$errores = [];

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

if (!$id || $id <= 0) {
    header('Location: index.php?mensaje=no_encontrado');
    exit;
}


/*
 * Buscar boletín.
 */
$stmt = $pdo->prepare(
    "SELECT
        id,
        numero_boletin,
        resumen,
        foto_portada,
        archivo_pdf,
        fecha_publicacion,
        usuario_id
     FROM boletines
     WHERE id = ?
     LIMIT 1"
);

$stmt->execute([$id]);

$boletin = $stmt->fetch();

if (!$boletin) {
    header('Location: index.php?mensaje=no_encontrado');
    exit;
}


/*
 * Valores actuales.
 */
$numero_boletin = $boletin['numero_boletin'];
$resumen = $boletin['resumen'];
$foto_portada = $boletin['foto_portada'];
$archivo_pdf = $boletin['archivo_pdf'];
$fecha_publicacion = $boletin['fecha_publicacion'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $numero_boletin = trim(
        $_POST['numero_boletin'] ?? ''
    );

    $resumen = trim(
        $_POST['resumen'] ?? ''
    );

    $fecha_publicacion = trim(
        $_POST['fecha_publicacion'] ?? ''
    );


    /*
     * Validar número.
     */
    if ($numero_boletin === '') {

        $errores[] =
            'El número de boletín es obligatorio.';
    }


    /*
     * Validar fecha.
     */
    if ($fecha_publicacion === '') {

        $errores[] =
            'La fecha de publicación es obligatoria.';
    }


    if ($fecha_publicacion !== '') {

        $fechaValida = DateTime::createFromFormat(
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
     * Comprobar número duplicado.
     *
     * Se excluye el boletín que estamos editando.
     */
    if ($numero_boletin !== '') {

        $stmt = $pdo->prepare(
            "SELECT id
             FROM boletines
             WHERE numero_boletin = ?
             AND id <> ?
             LIMIT 1"
        );

        $stmt->execute([
            $numero_boletin,
            $id
        ]);

        if ($stmt->fetch()) {

            $errores[] =
                'Ya existe otro boletín con ese número.';
        }
    }


    /*
     * Guardar las rutas originales.
     */
    $fotoOriginal = $boletin['foto_portada'];
    $pdfOriginal = $boletin['archivo_pdf'];

    /*
     * Nuevos archivos.
     */
    $nuevaFoto = null;
    $nuevoPdf = null;


    try {

        /*
         * Nueva portada.
         */
        if (
            isset($_FILES['foto_portada']) &&
            $_FILES['foto_portada']['error'] !==
            UPLOAD_ERR_NO_FILE
        ) {

            $nuevaFoto =
                subirPortadaBoletin(
                    $_FILES['foto_portada']
                );
        }


        /*
         * Nuevo PDF.
         */
        if (
            isset($_FILES['archivo_pdf']) &&
            $_FILES['archivo_pdf']['error'] !==
            UPLOAD_ERR_NO_FILE
        ) {

            $nuevoPdf =
                subirPdfBoletin(
                    $_FILES['archivo_pdf']
                );
        }


        /*
         * Si no hay errores, actualizar.
         */
        if (empty($errores)) {

            /*
             * Si no se subió una nueva portada,
             * conservar la anterior.
             */
            $fotoParaGuardar =
                $nuevaFoto !== null
                    ? $nuevaFoto
                    : $fotoOriginal;


            /*
             * Si no se subió un nuevo PDF,
             * conservar el anterior.
             */
            $pdfParaGuardar =
                $nuevoPdf !== null
                    ? $nuevoPdf
                    : $pdfOriginal;


            $stmt = $pdo->prepare(
                "UPDATE boletines
                 SET
                    numero_boletin = ?,
                    resumen = ?,
                    foto_portada = ?,
                    archivo_pdf = ?,
                    fecha_publicacion = ?
                 WHERE id = ?"
            );

            $stmt->execute([
                $numero_boletin,
                $resumen,
                $fotoParaGuardar,
                $pdfParaGuardar,
                $fecha_publicacion,
                $id
            ]);


            /*
             * Si se reemplazó la portada,
             * eliminar la portada anterior.
             */
            if (
                $nuevaFoto !== null &&
                $fotoOriginal !== $nuevaFoto
            ) {

                eliminarArchivoBoletin(
                    $fotoOriginal
                );
            }


            /*
             * Si se reemplazó el PDF,
             * eliminar el PDF anterior.
             */
            if (
                $nuevoPdf !== null &&
                $pdfOriginal !== $nuevoPdf
            ) {

                eliminarArchivoBoletin(
                    $pdfOriginal
                );
            }


            header(
                'Location: index.php?mensaje=editado'
            );

            exit;
        }

    } catch (PDOException $e) {

        /*
         * Si MySQL falla, eliminar archivos nuevos.
         */
        if ($nuevaFoto !== null) {

            eliminarArchivoBoletin(
                $nuevaFoto
            );
        }

        if ($nuevoPdf !== null) {

            eliminarArchivoBoletin(
                $nuevoPdf
            );
        }


        if (
            $e->errorInfo[0] === '23000'
        ) {

            $errores[] =
                'Ya existe un boletín con ese número.';

        } else {

            $errores[] =
                'No se pudo actualizar el boletín.';
        }

    } catch (RuntimeException $e) {

        /*
         * Si falla una subida,
         * eliminar cualquier archivo nuevo.
         */
        if ($nuevaFoto !== null) {

            eliminarArchivoBoletin(
                $nuevaFoto
            );
        }

        if ($nuevoPdf !== null) {

            eliminarArchivoBoletin(
                $nuevoPdf
            );
        }

        $errores[] =
            $e->getMessage();
    }
}


require_once __DIR__ . '/../../includes/admin-header.php';
require_once __DIR__ . '/../../includes/admin-sidebar.php';
?>

<main class="admin-main">

    <div class="container-fluid py-4">

        <!-- ENCABEZADO -->

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h1 class="h3 mb-1">
                    Editar boletín
                </h1>

                <p class="text-muted mb-0">
                    Modificar la información del boletín
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
                    No se pudo actualizar el boletín:
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

                <form
                    method="POST"
                    enctype="multipart/form-data"
                >

                    <div class="row g-4">

                        <!-- NÚMERO -->

                        <div class="col-12 col-md-6">

                            <label
                                for="numero_boletin"
                                class="form-label"
                            >
                                Número de boletín
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="numero_boletin"
                                name="numero_boletin"
                                value="<?= e($numero_boletin) ?>"
                                maxlength="100"
                                required
                            >

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


                        <!-- RESUMEN -->

                        <div class="col-12">

                            <label
                                for="resumen"
                                class="form-label"
                            >
                                Resumen
                            </label>

                            <textarea
                                class="form-control"
                                id="resumen"
                                name="resumen"
                                rows="5"
                            ><?= e($resumen) ?></textarea>

                        </div>


                        <!-- PORTADA ACTUAL -->

                        <div class="col-12 col-lg-6">

                            <label class="form-label">
                                Portada actual
                            </label>

                            <?php if (!empty($foto_portada)): ?>

                                <div class="mb-3">

                                    <img
                                        src="<?= e('../../' . $foto_portada) ?>"
                                        alt="Portada actual"
                                        class="img-thumbnail"
                                        style="
                                            max-width: 100%;
                                            max-height: 300px;
                                            object-fit: contain;
                                        "
                                    >

                                </div>

                            <?php else: ?>

                                <p class="text-muted">
                                    Este boletín no tiene portada.
                                </p>

                            <?php endif; ?>


                            <label
                                for="foto_portada"
                                class="form-label"
                            >
                                Cambiar portada
                            </label>

                            <input
                                type="file"
                                class="form-control"
                                id="foto_portada"
                                name="foto_portada"
                                accept="image/jpeg,image/png,image/webp,image/gif"
                            >

                            <div class="form-text">
                                Déjalo vacío para conservar
                                la portada actual.
                                JPG, PNG, WEBP o GIF.
                                Máximo 5 MB.
                            </div>

                            <div
                                id="vistaPortada"
                                class="mt-3"
                            ></div>

                        </div>


                        <!-- PDF ACTUAL -->

                        <div class="col-12 col-lg-6">

                            <label class="form-label">
                                PDF actual
                            </label>

                            <?php if (!empty($archivo_pdf)): ?>

                                <div class="mb-3">

                                    <a
                                        href="<?= e('../../' . $archivo_pdf) ?>"
                                        target="_blank"
                                        class="btn btn-outline-danger"
                                    >
                                        <i class="bi bi-file-earmark-pdf"></i>
                                        Ver PDF actual
                                    </a>

                                </div>

                            <?php else: ?>

                                <p class="text-muted">
                                    Este boletín no tiene PDF.
                                </p>

                            <?php endif; ?>


                            <label
                                for="archivo_pdf"
                                class="form-label"
                            >
                                Cambiar PDF
                            </label>

                            <input
                                type="file"
                                class="form-control"
                                id="archivo_pdf"
                                name="archivo_pdf"
                                accept="application/pdf,.pdf"
                            >

                            <div class="form-text">
                                Déjalo vacío para conservar
                                el PDF actual.
                                Solo PDF.
                                Máximo 10 MB.
                            </div>

                            <div
                                id="nombrePdf"
                                class="mt-3 text-muted"
                            ></div>

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
                                    Guardar cambios
                                </button>

                            </div>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

</main>


<script>
document.addEventListener(
    'DOMContentLoaded',
    function () {

        const inputPortada =
            document.getElementById(
                'foto_portada'
            );

        const vistaPortada =
            document.getElementById(
                'vistaPortada'
            );

        const inputPdf =
            document.getElementById(
                'archivo_pdf'
            );

        const nombrePdf =
            document.getElementById(
                'nombrePdf'
            );


        /*
         * Vista previa de nueva portada.
         */
        inputPortada.addEventListener(
            'change',
            function () {

                vistaPortada.innerHTML = '';

                const archivo =
                    this.files[0];

                if (!archivo) {
                    return;
                }

                const imagen =
                    document.createElement(
                        'img'
                    );

                imagen.src =
                    URL.createObjectURL(
                        archivo
                    );

                imagen.alt =
                    'Nueva portada';

                imagen.style.maxWidth =
                    '100%';

                imagen.style.maxHeight =
                    '300px';

                imagen.style.objectFit =
                    'contain';

                imagen.className =
                    'img-thumbnail';

                vistaPortada.appendChild(
                    imagen
                );
            }
        );


        /*
         * Mostrar nombre del nuevo PDF.
         */
        inputPdf.addEventListener(
            'change',
            function () {

                nombrePdf.textContent = '';

                const archivo =
                    this.files[0];

                if (!archivo) {
                    return;
                }

                nombrePdf.innerHTML =
                    '<i class="bi bi-file-earmark-pdf"></i> ' +
                    archivo.name;
            }
        );
    }
);
</script>

<?php
require_once __DIR__ . '/../../includes/admin-footer.php';
?>