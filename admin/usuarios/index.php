<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/funciones.php';

requerirAdministrador();

$tituloPagina = 'Usuarios';
$baseUrl = '../../';

$mensaje = $_GET['mensaje'] ?? '';

/*
|--------------------------------------------------------------------------
| Obtener usuarios
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query(
    "SELECT
        id,
        nombres,
        ap_paterno,
        ap_materno,
        email,
        rol,
        created_at
     FROM usuarios
     ORDER BY id DESC"
);

$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
                <h1 class="h3 mb-1">Usuarios</h1>

                <p class="text-muted mb-0">
                    Administrar los usuarios del sistema
                </p>
            </div>

            <div>
                <a href="crear.php" class="btn btn-primary">
                    <i class="bi bi-person-plus me-1"></i>
                    Nuevo usuario
                </a>
            </div>

        </div>


        <!-- Mensajes -->
        <?php if ($mensaje === 'creado'): ?>

            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                El usuario se creó correctamente.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>
            </div>

        <?php elseif ($mensaje === 'editado'): ?>

            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                El usuario se actualizó correctamente.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>
            </div>

        <?php elseif ($mensaje === 'eliminado'): ?>

            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                El usuario se eliminó correctamente.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>
            </div>

        <?php elseif ($mensaje === 'no_encontrado'): ?>

            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-1"></i>
                El usuario solicitado no existe.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>
            </div>

        <?php elseif ($mensaje === 'no_eliminar_propio'): ?>

            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-1"></i>
                No puedes eliminar tu propio usuario mientras tienes la sesión iniciada.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                ></button>
            </div>

        <?php elseif ($mensaje === 'error'): ?>

            <div class="alert alert-danger alert-dismissible fade show" role="alert">
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
                                <th>ID</th>
                                <th>Nombre completo</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Fecha de creación</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php if (empty($usuarios)): ?>

                                <tr>
                                    <td
                                        colspan="6"
                                        class="text-center py-4 text-muted"
                                    >
                                        No hay usuarios registrados.
                                    </td>
                                </tr>

                            <?php else: ?>

                                <?php foreach ($usuarios as $usuario): ?>

                                    <?php
                                    $nombreCompleto = trim(
                                        $usuario['nombres'] . ' ' .
                                        $usuario['ap_paterno'] . ' ' .
                                        ($usuario['ap_materno'] ?? '')
                                    );
                                    ?>

                                    <tr>

                                        <td>
                                            <?= (int) $usuario['id'] ?>
                                        </td>

                                        <td>
                                            <strong>
                                                <?= e($nombreCompleto) ?>
                                            </strong>
                                        </td>

                                        <td>
                                            <?= e($usuario['email']) ?>
                                        </td>

                                        <td>

                                            <?php if ($usuario['rol'] === 'admin'): ?>

                                                <span class="badge text-bg-danger">
                                                    Administrador
                                                </span>

                                            <?php elseif ($usuario['rol'] === 'editor'): ?>

                                                <span class="badge text-bg-primary">
                                                    Editor
                                                </span>

                                            <?php else: ?>

                                                <span class="badge text-bg-secondary">
                                                    Redactor
                                                </span>

                                            <?php endif; ?>

                                        </td>

                                        <td>
                                            <?= e($usuario['created_at']) ?>
                                        </td>

                                        <td class="text-end">

                                            <div
                                                class="d-flex flex-column flex-sm-row justify-content-end gap-2"
                                            >

                                                <a
                                                    href="editar.php?id=<?= (int) $usuario['id'] ?>"
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="Editar usuario"
                                                >
                                                    <i class="bi bi-pencil"></i>

                                                    <span class="d-none d-md-inline">
                                                        Editar
                                                    </span>
                                                </a>

                                                <form
                                                    method="POST"
                                                    action="eliminar.php"
                                                    onsubmit="return confirm('¿Estás seguro de eliminar este usuario?');"
                                                >

                                                    <input
                                                        type="hidden"
                                                        name="id"
                                                        value="<?= (int) $usuario['id'] ?>"
                                                    >

                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-outline-danger w-100"
                                                        title="Eliminar usuario"
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