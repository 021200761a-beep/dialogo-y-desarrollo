<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/funciones.php';

requerirAdministrador();

$tituloPagina = 'Videos';
$baseUrl = '../../';

$mensaje = $_GET['mensaje'] ?? '';

/*
|--------------------------------------------------------------------------
| Obtener videos
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query(
    "SELECT
        v.id,
        v.titulo,
        v.url_embed,
        v.fecha_publicacion,
        v.usuario_id,
        CONCAT(
            u.nombres,
            ' ',
            u.ap_paterno
        ) AS nombre_usuario
     FROM videos v
     LEFT JOIN usuarios u
        ON v.usuario_id = u.id
     ORDER BY v.id DESC"
);

$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                    Videos
                </h1>

                <p class="text-muted mb-0">
                    Administrar los videos publicados
                </p>

            </div>

            <div>

                <a
                    href="crear.php"
                    class="btn btn-primary"
                >
                    <i class="bi bi-plus-circle me-1"></i>
                    Nuevo video
                </a>

            </div>

        </div>


        <!-- Mensajes -->
        <?php if ($mensaje === 'creado'): ?>

            <div
                class="alert alert-success alert-dismissible fade show"
                role="alert"
            >
                <i class="bi bi-check-circle me-1"></i>
                El video se creó correctamente.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>
            </div>

        <?php elseif ($mensaje === 'editado'): ?>

            <div
                class="alert alert-success alert-dismissible fade show"
                role="alert"
            >
                <i class="bi bi-check-circle me-1"></i>
                El video se actualizó correctamente.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>
            </div>

        <?php elseif ($mensaje === 'eliminado'): ?>

            <div
                class="alert alert-success alert-dismissible fade show"
                role="alert"
            >
                <i class="bi bi-check-circle me-1"></i>
                El video se eliminó correctamente.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>
            </div>

        <?php elseif ($mensaje === 'no_encontrado'): ?>

            <div
                class="alert alert-warning alert-dismissible fade show"
                role="alert"
            >
                <i class="bi bi-exclamation-triangle me-1"></i>
                El video solicitado no existe.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>
            </div>

        <?php elseif ($mensaje === 'error'): ?>

            <div
                class="alert alert-danger alert-dismissible fade show"
                role="alert"
            >
                <i class="bi bi-x-circle me-1"></i>
                Ocurrió un error al realizar la operación.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>
            </div>

        <?php endif; ?>


        <!-- Tabla -->
        <div class="card dashboard-card">

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead>

                            <tr>

                                <th>
                                    ID
                                </th>

                                <th>
                                    Título
                                </th>

                                <th>
                                    URL / Embed
                                </th>

                                <th>
                                    Fecha
                                </th>

                                <th>
                                    Usuario
                                </th>

                                <th class="text-end">
                                    Acciones
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php if (empty($videos)): ?>

                                <tr>

                                    <td
                                        colspan="6"
                                        class="text-center py-4 text-muted"
                                    >
                                        No hay videos registrados.
                                    </td>

                                </tr>

                            <?php else: ?>

                                <?php foreach ($videos as $video): ?>

                                    <tr>

                                        <td>
                                            <?= (int) $video['id'] ?>
                                        </td>


                                        <td>

                                            <strong>
                                                <?= e($video['titulo']) ?>
                                            </strong>

                                        </td>


                                        <td>

                                            <span
                                                class="d-inline-block text-truncate"
                                                style="max-width: 300px;"
                                                title="<?= e($video['url_embed']) ?>"
                                            >
                                                <?= e($video['url_embed']) ?>
                                            </span>

                                        </td>


                                        <td>
                                            <?= e($video['fecha_publicacion']) ?>
                                        </td>


                                        <td>
                                            <?= e($video['nombre_usuario'] ?? 'Sin usuario') ?>
                                        </td>


                                        <td class="text-end">

                                            <div
                                                class="d-flex flex-column flex-sm-row justify-content-end gap-2"
                                            >

                                                <!-- Editar -->
                                                <a
                                                    href="editar.php?id=<?= (int) $video['id'] ?>"
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="Editar video"
                                                >

                                                    <i class="bi bi-pencil"></i>

                                                    <span class="d-none d-md-inline">
                                                        Editar
                                                    </span>

                                                </a>


                                                <!-- Eliminar -->
                                                <form
                                                    method="POST"
                                                    action="eliminar.php"
                                                    onsubmit="return confirm('¿Estás seguro de eliminar este video?');"
                                                >

                                                    <input
                                                        type="hidden"
                                                        name="id"
                                                        value="<?= (int) $video['id'] ?>"
                                                    >

                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-outline-danger w-100"
                                                        title="Eliminar video"
                                                    >

                                                        <i class="bi bi-trash"></i>

                                                        <span class="d-none d-md-inline">
                                                            Eliminar
                                                        </span>

                                                    </button>

                                                </form>

                                            </div>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</main>

<?php
require_once __DIR__ . '/../../includes/admin-footer.php';
?>