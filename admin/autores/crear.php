<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/funciones.php';

requerirAdministrador();

$tituloPagina = 'Nuevo Autor';
$baseUrl = '../../';

$errores = [];

/*
|--------------------------------------------------------------------------
| Valores iniciales
|--------------------------------------------------------------------------
*/

$nombres = '';
$ap_paterno = '';
$ap_materno = '';
$nickname = '';
$es_nickname = 0;

/*
|--------------------------------------------------------------------------
| Procesar formulario
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombres = trim($_POST['nombres'] ?? '');
    $ap_paterno = trim($_POST['ap_paterno'] ?? '');
    $ap_materno = trim($_POST['ap_materno'] ?? '');
    $nickname = trim($_POST['nickname'] ?? '');

    $es_nickname = isset($_POST['es_nickname']) ? 1 : 0;


    /*
    |--------------------------------------------------------------------------
    | Validaciones
    |--------------------------------------------------------------------------
    */

    if ($nombres === '') {

        $errores[] = 'El campo nombres es obligatorio.';

    } elseif (mb_strlen($nombres) > 100) {

        $errores[] = 'El campo nombres no puede superar los 100 caracteres.';

    }


    if (mb_strlen($ap_paterno) > 100) {

        $errores[] = 'El apellido paterno no puede superar los 100 caracteres.';

    }


    if (mb_strlen($ap_materno) > 100) {

        $errores[] = 'El apellido materno no puede superar los 100 caracteres.';

    }


    if (mb_strlen($nickname) > 100) {

        $errores[] = 'El nickname no puede superar los 100 caracteres.';

    }


    /*
    |--------------------------------------------------------------------------
    | Validación del nickname
    |--------------------------------------------------------------------------
    */

    if ($es_nickname === 1 && $nickname === '') {

        $errores[] = 'Debes ingresar un nickname cuando se selecciona esta opción.';

    }


    /*
    |--------------------------------------------------------------------------
    | Crear autor
    |--------------------------------------------------------------------------
    */

    if (empty($errores)) {

        try {

            $stmt = $pdo->prepare(
                "INSERT INTO autores (
                    nombres,
                    ap_paterno,
                    ap_materno,
                    nickname,
                    es_nickname
                ) VALUES (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?
                )"
            );

            $stmt->execute([
                $nombres,
                $ap_paterno !== '' ? $ap_paterno : null,
                $ap_materno !== '' ? $ap_materno : null,
                $nickname !== '' ? $nickname : null,
                $es_nickname
            ]);

            header('Location: index.php?mensaje=creado');
            exit;

        } catch (PDOException $e) {

            $errores[] = 'No se pudo crear el autor. Inténtalo nuevamente.';
        }
    }
}

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
                Nuevo autor
            </h1>

            <p class="text-muted mb-0">
                Registrar un nuevo autor para D&D Noticias
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

        <div class="alert alert-danger">

            <div class="fw-semibold mb-2">
                No se pudo guardar el autor:
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


    <!-- FORMULARIO -->

    <div class="card dashboard-card">

        <div class="card-body">

            <form
                method="POST"
                action=""
            >

                <!-- NOMBRES -->

                <div class="mb-3">

                    <label
                        for="nombres"
                        class="form-label"
                    >
                        Nombres
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="nombres"
                        name="nombres"
                        value="<?= e($nombres) ?>"
                        maxlength="100"
                        required
                    >

                </div>


                <!-- APELLIDO PATERNO -->

                <div class="mb-3">

                    <label
                        for="ap_paterno"
                        class="form-label"
                    >
                        Apellido paterno
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="ap_paterno"
                        name="ap_paterno"
                        value="<?= e($ap_paterno) ?>"
                        maxlength="100"
                    >

                </div>


                <!-- APELLIDO MATERNO -->

                <div class="mb-3">

                    <label
                        for="ap_materno"
                        class="form-label"
                    >
                        Apellido materno
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="ap_materno"
                        name="ap_materno"
                        value="<?= e($ap_materno) ?>"
                        maxlength="100"
                    >

                </div>


                <!-- NICKNAME -->

                <div class="mb-3">

                    <label
                        for="nickname"
                        class="form-label"
                    >
                        Nickname
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="nickname"
                        name="nickname"
                        value="<?= e($nickname) ?>"
                        maxlength="100"
                    >

                    <div class="form-text">
                        Puedes dejar este campo vacío si el autor utiliza su nombre real.
                    </div>

                </div>


                <!-- ES NICKNAME -->

                <div class="form-check mb-4">

                    <input
                        type="checkbox"
                        class="form-check-input"
                        id="es_nickname"
                        name="es_nickname"
                        value="1"
                        <?= $es_nickname === 1 ? 'checked' : '' ?>
                    >

                    <label
                        class="form-check-label"
                        for="es_nickname"
                    >
                        Mostrar este autor mediante su nickname
                    </label>

                </div>


                <!-- BOTONES -->

                <div class="d-flex flex-column flex-sm-row gap-2">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >

                        <i class="bi bi-save me-1"></i>
                        Guardar autor

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
```

</main>

<?php
require_once __DIR__ . '/../../includes/admin-footer.php';
?>
