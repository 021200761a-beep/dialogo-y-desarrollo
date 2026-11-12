<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';


/*
 * Si ya existe una sesión de administrador,
 * no tiene sentido mostrar nuevamente el login.
 */
if (esAdministrador()) {
    header('Location: admin/dashboard.php');
    exit;
}


$error = '';


/*
 * Procesar formulario.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';


    /*
     * Validación básica.
     */
    if ($email === '' || $password === '') {

        $error = 'Debes ingresar el correo y la contraseña.';

    } else {

        try {

            /*
             * Buscar usuario por correo.
             */
            $sql = "
                SELECT
                    id,
                    nombres,
                    ap_paterno,
                    ap_materno,
                    email,
                    password_hash,
                    rol
                FROM usuarios
                WHERE email = :email
                LIMIT 1
            ";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':email' => $email
            ]);

            $usuario = $stmt->fetch();


            /*
             * Comprobar contraseña.
             */
            if (
                $usuario &&
                password_verify($password, $usuario['password_hash'])
            ) {

                /*
                 * Solamente permitimos entrar
                 * al panel si el usuario es admin.
                 */
                if ($usuario['rol'] !== 'admin') {

                    $error = 'No tienes permisos de administrador.';

                } else {

                    /*
                     * Regenerar ID de sesión
                     * después de autenticarse.
                     */
                    session_regenerate_id(true);


                    /*
                     * Guardar solamente los datos
                     * necesarios en la sesión.
                     */
                    $_SESSION['usuario'] = [
                        'id' => $usuario['id'],
                        'nombres' => $usuario['nombres'],
                        'ap_paterno' => $usuario['ap_paterno'],
                        'ap_materno' => $usuario['ap_materno'],
                        'email' => $usuario['email'],
                        'rol' => $usuario['rol']
                    ];


                    /*
                     * Enviar al panel.
                     */
                    header('Location: admin/dashboard.php');
                    exit;
                }

            } else {

                $error = 'Correo o contraseña incorrectos.';
            }

        } catch (PDOException $e) {

            $error = 'Ocurrió un error al procesar el inicio de sesión.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Iniciar sesión - D&D Noticias</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #1e1e2f 0%, #2b2b45 60%, #3a2a4d 100%);
            color: #f1f1f1;
        }

        h1 {
            margin: 0 0 24px;
            font-size: 2rem;
            letter-spacing: 1px;
            text-align: center;
            color: #ffd166;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.6);
        }

        /* Mensaje de error */
        body > div {
            width: 100%;
            max-width: 380px;
            margin-bottom: 16px;
            padding: 12px 16px;
            border-radius: 8px;
            background: rgba(220, 53, 69, 0.15);
            border: 1px solid #dc3545;
            color: #ff8a95;
            font-size: 0.9rem;
            text-align: center;
        }

        form {
            width: 100%;
            max-width: 380px;
            padding: 32px 28px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(6px);
        }

        form > div {
            margin-bottom: 4px;
        }

        label {
            display: inline-block;
            margin-bottom: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            color: #cfcfe0;
            text-transform: uppercase;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            font-size: 1rem;
            color: #f1f1f1;
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 8px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        input[type="email"]::placeholder,
        input[type="password"]::placeholder {
            color: #8a8aa0;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #ffd166;
            background: rgba(0, 0, 0, 0.5);
            box-shadow: 0 0 0 3px rgba(255, 209, 102, 0.2);
        }

        button[type="submit"] {
            width: 100%;
            margin-top: 18px;
            padding: 13px 16px;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: #1e1e2f;
            background: linear-gradient(135deg, #ffd166, #f4a261);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.15s, box-shadow 0.2s, filter 0.2s;
        }

        button[type="submit"]:hover {
            filter: brightness(1.08);
            box-shadow: 0 8px 20px rgba(255, 209, 102, 0.35);
            transform: translateY(-1px);
        }

        button[type="submit"]:active {
            transform: translateY(0);
            box-shadow: 0 4px 12px rgba(255, 209, 102, 0.25);
        }

        /* Enlace de volver */
        body > a {
            margin-top: 22px;
            font-size: 0.9rem;
            color: #9aa0ff;
            text-decoration: none;
            border-bottom: 1px solid transparent;
            transition: color 0.2s, border-color 0.2s;
        }

        body > a:hover {
            color: #ffd166;
            border-bottom-color: #ffd166;
        }

        /* Los <br> sobrantes no rompen el layout */
        form br {
            display: none;
        }
    </style>

</head>

<body>

    <h1>Iniciar sesión</h1>

    <?php if ($error !== ''): ?>

        <div>
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>


    <form method="POST" action="">

        <div>

            <label for="email">
                Correo electrónico
            </label>

            <br>

            <input
                type="email"
                id="email"
                name="email"
                required
                autocomplete="email"
            >

        </div>

        <br>

        <div>

            <label for="password">
                Contraseña
            </label>

            <br>

            <input
                type="password"
                id="password"
                name="password"
                required
                autocomplete="current-password"
            >

        </div>

        <br>

        <button type="submit">
            Iniciar sesión
        </button>

    </form>

    <br>

    <a href="index.php">
        Volver a la página principal
    </a>

</body>

</html>