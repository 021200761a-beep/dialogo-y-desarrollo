<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/funciones.php';

requerirAdministrador();

$tituloPagina = 'Editar Usuario';
$baseUrl = '../../';

$errores = [];

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php?mensaje=no_encontrado');
    exit;
}

/*
|--------------------------------------------------------------------------
| Obtener usuario
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare(
    "SELECT
        id,
        nombres,
        ap_paterno,
        ap_materno,
        email,
        password_hash,
        rol
     FROM usuarios
     WHERE id = ?
     LIMIT 1"
);

$stmt->execute([$id]);

$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    header('Location: index.php?mensaje=no_encontrado');
    exit;
}

/*
|--------------------------------------------------------------------------
| Valores iniciales
|--------------------------------------------------------------------------
*/

$nombres = $usuario['nombres'];
$ap_paterno = $usuario['ap_paterno'];
$ap_materno = $usuario['ap_materno'] ?? '';
$email = $usuario['email'];
$rol = $usuario['rol'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombres = trim($_POST['nombres'] ?? '');
    $ap_paterno = trim($_POST['ap_paterno'] ?? '');
    $ap_materno = trim($_POST['ap_materno'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirmacion = $_POST['password_confirmacion'] ?? '';
    $rol = $_POST['rol'] ?? 'redactor';

    /*
    |--------------------------------------------------------------------------
    | Validar nombres
    |--------------------------------------------------------------------------
    */

    if ($nombres === '') {

        $errores[] = 'El campo nombres es obligatorio.';

    } elseif (mb_strlen($nombres) > 100) {

        $errores[] = 'El campo nombres no puede superar los 100 caracteres.';
    }


    /*
    |--------------------------------------------------------------------------
    | Validar apellido paterno
    |--------------------------------------------------------------------------
    */

    if ($ap_paterno === '') {

        $errores[] = 'El apellido paterno es obligatorio.';

    } elseif (mb_strlen($ap_paterno) > 100) {

        $errores[] = 'El apellido paterno no puede superar los 100 caracteres.';
    }


    /*
    |--------------------------------------------------------------------------
    | Validar apellido materno
    |--------------------------------------------------------------------------
    */

    if (mb_strlen($ap_materno) > 100) {

        $errores[] = 'El apellido materno no puede superar los 100 caracteres.';
    }


    /*
    |--------------------------------------------------------------------------
    | Validar email
    |--------------------------------------------------------------------------
    */

    if ($email === '') {

        $errores[] = 'El email es obligatorio.';

    } elseif (mb_strlen($email) > 150) {

        $errores[] = 'El email no puede superar los 150 caracteres.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $errores[] = 'Debes ingresar un email válido.';
    }


    /*
    |--------------------------------------------------------------------------
    | Validar contraseña solamente si se desea cambiar
    |--------------------------------------------------------------------------
    */

    if ($password !== '' || $password_confirmacion !== '') {

        if (strlen($password) < 6) {
            $errores[] = 'La nueva contraseña debe tener al menos 6 caracteres.';
        }

        if ($password !== $password_confirmacion) {
            $errores[] = 'Las contraseñas no coinciden.';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Validar rol
    |--------------------------------------------------------------------------
    */

    $rolesPermitidos = [
        'admin',
        'editor',
        'redactor'
    ];

    if (!in_array($rol, $rolesPermitidos, true)) {

        $errores[] = 'El rol seleccionado no es válido.';
    }


    /*
    |--------------------------------------------------------------------------
    | Verificar email utilizado por otro usuario
    |--------------------------------------------------------------------------
    */

    if (empty($errores)) {

        $stmt = $pdo->prepare(
            "SELECT id
             FROM usuarios
             WHERE email = ?
             AND id <> ?
             LIMIT 1"
        );

        $stmt->execute([
            $email,
            $id
        ]);

        if ($stmt->fetch()) {

            $errores[] = 'Ya existe otro usuario registrado con ese email.';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Actualizar usuario
    |--------------------------------------------------------------------------
    */

    if (empty($errores)) {

        try {

            if ($password !== '') {

                $passwordHash = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $stmt = $pdo->prepare(
                    "UPDATE usuarios
                     SET
                        nombres = ?,
                        ap_paterno = ?,
                        ap_materno = ?,
                        email = ?,
                        password_hash = ?,
                        rol = ?
                     WHERE id = ?"
                );

                $stmt->execute([
                    $nombres,
                    $ap_paterno,
                    $ap_materno !== '' ? $ap_materno : null,
                    $email,
                    $passwordHash,
                    $rol,
                    $id
                ]);

            } else {

                $stmt = $pdo->prepare(
                    "UPDATE usuarios
                     SET
                        nombres = ?,
                        ap_paterno = ?,
                        ap_materno = ?,
                        email = ?,
                        rol = ?
                     WHERE id = ?"
                );

                $stmt->execute([
                    $nombres,
                    $ap_paterno,
                    $ap_materno !== '' ? $ap_materno : null,
                    $email,
                    $rol,
                    $id
                ]);
            }

            header('Location: index.php?mensaje=editado');
            exit;

        } catch (PDOException $e) {

            $errores[] = 'No se pudo actualizar el usuario. Inténtalo nuevamente.';
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
                    Editar usuario
                </h1>

                <p class="text-muted mb-0">
                    Modificar los datos del usuario
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
                    No se pudo actualizar el usuario:
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

                    <div class="row">

                        <!-- Nombres -->
                        <div class="col-12 col-md-6 mb-3">

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


                        <!-- Apellido paterno -->
                        <div class="col-12 col-md-6 mb-3">

                            <label
                                for="ap_paterno"
                                class="form-label"
                            >
                                Apellido paterno
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="ap_paterno"
                                name="ap_paterno"
                                value="<?= e($ap_paterno) ?>"
                                maxlength="100"
                                required
                            >

                        </div>


                        <!-- Apellido materno -->
                        <div class="col-12 col-md-6 mb-3">

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


                        <!-- Email -->
                        <div class="col-12 col-md-6 mb-3">

                            <label
                                for="email"
                                class="form-label"
                            >
                                Email
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="email"
                                class="form-control"
                                id="email"
                                name="email"
                                value="<?= e($email) ?>"
                                maxlength="150"
                                required
                            >

                        </div>


                        <!-- Nueva contraseña -->
                        <div class="col-12 col-md-6 mb-3">

                            <label
                                for="password"
                                class="form-label"
                            >
                                Nueva contraseña
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                minlength="6"
                            >

                            <div class="form-text">
                                Déjalo vacío si no deseas cambiar la contraseña.
                            </div>

                        </div>


                        <!-- Confirmación -->
                        <div class="col-12 col-md-6 mb-3">

                            <label
                                for="password_confirmacion"
                                class="form-label"
                            >
                                Confirmar nueva contraseña
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="password_confirmacion"
                                name="password_confirmacion"
                                minlength="6"
                            >

                        </div>


                        <!-- Rol -->
                        <div class="col-12 col-md-6 mb-4">

                            <label
                                for="rol"
                                class="form-label"
                            >
                                Rol
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                class="form-select"
                                id="rol"
                                name="rol"
                                required
                            >

                                <option
                                    value="admin"
                                    <?= $rol === 'admin' ? 'selected' : '' ?>
                                >
                                    Administrador
                                </option>

                                <option
                                    value="editor"
                                    <?= $rol === 'editor' ? 'selected' : '' ?>
                                >
                                    Editor
                                </option>

                                <option
                                    value="redactor"
                                    <?= $rol === 'redactor' ? 'selected' : '' ?>
                                >
                                    Redactor
                                </option>

                            </select>

                        </div>

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