<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/funciones.php';

requerirAdministrador();

$tituloPagina = 'Autores';
$baseUrl = '../../';

/*
|--------------------------------------------------------------------------
| Obtener autores
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query(
    "SELECT
        id,
        nombres,
        ap_paterno,
        ap_materno,
        nickname,
        es_nickname
     FROM autores
     ORDER BY id DESC"
);

$autores = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Mensaje
|--------------------------------------------------------------------------
*/

$mensaje = $_GET['mensaje'] ?? '';

?>

<?php
require_once __DIR__ . '/../../includes/admin-header.php';
require_once __DIR__ . '/../../includes/admin-sidebar.php';
?>

<main class="admin-main">

```
<div class="container-fluid py-4">

    <!-- ENCABEZADO -->

    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4"
    >

        <div>

            <h1 class="h3 mb-1">
                Autores
            </h1>

            <p class="text-muted mb-0">
                Administrar los autores de D&D Noticias
            </p>

        </div>

        <div>

            <a
                href="crear.php"
                class="btn btn-primary"
            >
                <i class="bi bi-plus-lg"></i>
                Nuevo autor
            </a>

        </div>

    </div>


    <!-- MENSAJES -->

    <?php if ($mensaje === 'creado'): ?>

        <div class="alert alert-success">
            El autor se creó correctamente.
        </div>

    <?php elseif ($mensaje === 'editado'): ?>

        <div class="alert alert-success">
            El autor se actualizó correctamente.
        </div>

    <?php elseif ($mensaje === 'eliminado'): ?>

        <div class="alert alert-success">
            El autor se eliminó correctamente.
        </div>

    <?php elseif ($mensaje === 'no_encontrado'): ?>

        <div class="alert alert-warning">
            El autor solicitado no existe.
        </div>

    <?php elseif ($mensaje === 'en_uso'): ?>

        <div class="alert alert-warning">
            No se puede eliminar este autor porque está asociado a uno o más reportajes.
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
                                Nombres
                            </th>

                            <th>
                                Apellido paterno
                            </th>

                            <th>
                                Apellido materno
                            </th>

                            <th>
                                Nickname
                            </th>

                            <th>
                                Tipo
                            </th>

                            <th class="text-end">
                                Acciones
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php if (empty($autores)): ?>

                            <tr>

                                <td
                                    colspan="7"
                                    class="text-center text-muted py-4"
                                >

                                    No hay autores registrados.

                                </td>

                            </tr>

                        <?php else: ?>

                            <?php foreach ($autores as $autor): ?>

                                <tr>

                                    <td>
                                        <?= (int) $autor['id'] ?>
                                    </td>

                                    <td>
                                        <?= e($autor['nombres']) ?>
                                    </td>

                                    <td>
                                        <?= e(
                                            $autor['ap_paterno']
                                            ?? ''
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= e(
                                            $autor['ap_materno']
                                            ?? ''
                                        ) ?>
                                    </td>

                                    <td>

                                        <?php if (!empty($autor['nickname'])): ?>

                                            <?= e($autor['nickname']) ?>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                —
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td>

                                        <?php if ((int) $autor['es_nickname'] === 1): ?>

                                            <span class="badge bg-primary">
                                                Nickname
                                            </span>

                                        <?php else: ?>

                                            <span class="badge bg-secondary">
                                                Nombre real
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td class="text-end">

                                        <div
                                            class="d-flex justify-content-end gap-1"
                                        >

                                            <!-- EDITAR -->

                                            <a
                                                href="editar.php?id=<?= (int) $autor['id'] ?>"
                                                class="btn btn-sm btn-outline-primary"
                                                title="Editar"
                                            >

                                                <i class="bi bi-pencil"></i>

                                            </a>


                                            <!-- ELIMINAR -->

                                            <form
                                                method="POST"
                                                action="eliminar.php"
                                                class="d-inline"
                                                onsubmit="return confirm('¿Estás seguro de eliminar este autor?');"
                                            >

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?= (int) $autor['id'] ?>"
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
```

</main>

<?php
require_once __DIR__ . '/../../includes/admin-footer.php';
?>
