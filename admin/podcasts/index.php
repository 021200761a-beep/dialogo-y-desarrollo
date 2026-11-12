<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/funciones.php';

requerirAdministrador();

$tituloPagina = 'Podcasts';
$baseUrl = '../../';


/*
 * Obtener podcasts.
 */
$stmt = $pdo->query(
    "SELECT
        p.id,
        p.titulo,
        p.url_embed,
        p.fecha_publicacion,
        p.usuario_id,
        CONCAT(
            u.nombres,
            ' ',
            u.ap_paterno
        ) AS nombre_usuario
     FROM podcasts p
     LEFT JOIN usuarios u
        ON p.usuario_id = u.id
     ORDER BY p.id DESC"
);

$podcasts = $stmt->fetchAll();


/*
 * Mensaje.
 */
$mensaje = $_GET['mensaje'] ?? '';

?>

<?php
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
                    Podcasts
                </h1>

                <p class="text-muted mb-0">
                    Administrar los podcasts de D&D Noticias
                </p>

            </div>

            <div>

                <a
                    href="crear.php"
                    class="btn btn-primary"
                >
                    <i class="bi bi-plus-lg"></i>
                    Nuevo podcast
                </a>

            </div>

        </div>


        <!-- MENSAJES -->

        <?php if ($mensaje === 'creado'): ?>

            <div class="alert alert-success">
                El podcast se creó correctamente.
            </div>

        <?php elseif ($mensaje === 'editado'): ?>

            <div class="alert alert-success">
                El podcast se actualizó correctamente.
            </div>

        <?php elseif ($mensaje === 'eliminado'): ?>

            <div class="alert alert-success">
                El podcast se eliminó correctamente.
            </div>

        <?php elseif ($mensaje === 'no_encontrado'): ?>

            <div class="alert alert-warning">
                El podcast solicitado no existe.
            </div>

        <?php elseif ($mensaje === 'error'): ?>

            <div class="alert alert-danger">
                Ocurrió un error al realizar la operación.
            </div>

        <?php endif; ?>


        <!-- TABLA -->

        <div class="card dashboard-card">

            <div class="card-body">

                <div class="table-responsive">

                    <table
                        class="table table-hover align-middle mb-0"
                    >

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

                            <?php if (empty($podcasts)): ?>

                                <tr>

                                    <td
                                        colspan="6"
                                        class="text-center text-muted py-4"
                                    >
                                        No hay podcasts registrados.
                                    </td>

                                </tr>

                            <?php else: ?>

                                <?php foreach ($podcasts as $podcast): ?>

                                    <tr>

                                        <td>
                                            <?= (int) $podcast['id'] ?>
                                        </td>

                                        <td>
                                            <strong>
                                                <?= e($podcast['titulo']) ?>
                                            </strong>
                                        </td>

                                        <td>

                                            <?php if (!empty($podcast['url_embed'])): ?>

                                                <a
                                                    href="<?= e($podcast['url_embed']) ?>"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="btn btn-sm btn-outline-primary"
                                                >
                                                    <i class="bi bi-box-arrow-up-right"></i>
                                                    Abrir
                                                </a>

                                            <?php else: ?>

                                                <span class="text-muted">
                                                    Sin URL
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                        <td>
                                            <?= e($podcast['fecha_publicacion']) ?>
                                        </td>

                                        <td>
                                            <?= e(
                                                $podcast['nombre_usuario']
                                                ?? 'Sin usuario'
                                            ) ?>
                                        </td>

                                        <td class="text-end">

                                            <div
                                                class="d-flex justify-content-end gap-1"
                                            >

                                                <a
                                                    href="editar.php?id=<?= (int) $podcast['id'] ?>"
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="Editar"
                                                >
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                <form
                                                    method="POST"
                                                    action="eliminar.php"
                                                    class="d-inline"
                                                    onsubmit="return confirm('¿Estás seguro de eliminar este podcast?');"
                                                >

                                                    <input
                                                        type="hidden"
                                                        name="id"
                                                        value="<?= (int) $podcast['id'] ?>"
                                                    >

                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Eliminar"
                                                    >
                                                        <i class="bi bi-trash"></i>
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