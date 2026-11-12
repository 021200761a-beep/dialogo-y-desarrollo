<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/funciones.php';

requerirAdministrador();

$tituloPagina = 'Nuevo Usuario';
$baseUrl = '../../';

$errores = [];

$nombres = '';
$ap_paterno = '';
$ap_materno = '';
$email = '';
$rol = 'redactor';

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
    | Validar contraseña
    |--------------------------------------------------------------------------
    */

    if ($password === '') {

        $errores[] = 'La contraseña es obligatoria.';

    } elseif (strlen($password) < 6) {

        $errores[] = 'La contraseña debe tener al menos 6 caracteres.';
    }


    if ($password !== $password_confirmacion) {

        $errores[] = 'Las contraseñas no coinciden.';
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
    | Verificar email existente
    |--------------------------------------------------------------------------
    */

    if (empty($errores)) {

        $stmt = $pdo->prepare(
            "SELECT id
             FROM usuarios
             WHERE email = ?
             LIMIT 1"
        );

        $stmt->execute([$email]);

        if ($stmt->fetch()) {

            $errores[] = 'Ya existe un usuario registrado con ese email.';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Guardar usuario
    |--------------------------------------------------------------------------
    */

    if (empty($errores)) {

        try {

            $passwordHash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $pdo->prepare(
                "INSERT INTO usuarios (
                    nombres,
                    ap_paterno,
                    ap_materno,
                    email,
                    password_hash,
                    rol
                ) VALUES (?, ?, ?, ?, ?, ?)"
            );

            $stmt->execute([
                $nombres,
                $ap_paterno,
                $ap_materno !== '' ? $ap_materno : null,
                $email,
                $passwordHash,
                $rol
            ]);

            header('Location: index.php?mensaje=creado');
            exit;

        } catch (PDOException $e) {

            $errores[] = 'No se pudo crear el usuario. Inténtalo nuevamente.';
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
                <h1 class="h3 mb-1">Nuevo usuario</h1>

                <p class="text-muted mb-0">
                    Registrar un nuevo usuario del sistema
                </p>
            </div>

            <div>
                <a href="index.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>
                    Volver
                </a>
            </div>

        </div>


        <!-- Errores -->
        <?php if (!empty($errores)): ?>

            <div class="alert alert-danger">

                <div class="fw-semibold mb-2">
                    No se pudo crear el usuario:
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


                        <!-- Contraseña -->
                        <div class="col-12 col-md-6 mb-3">

                            <label
                                for="password"
                                class="form-label"
                            >
                                Contraseña
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="password"
                                name="password"
                                minlength="6"
                                required
                            >

                            <div class="form-text">
                                Mínimo 6 caracteres.
                            </div>

                        </div>


                        <!-- Confirmación -->
                        <div class="col-12 col-md-6 mb-3">

                            <label
                                for="password_confirmacion"
                                class="form-label"
                            >
                                Confirmar contraseña
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="password"
                                class="form-control"
                                id="password_confirmacion"
                                name="password_confirmacion"
                                minlength="6"
                                required
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
                            <i class="bi bi-person-plus me-1"></i>
                            Crear usuario
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