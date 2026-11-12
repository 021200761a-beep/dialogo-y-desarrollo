<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/funciones.php';


/*
|--------------------------------------------------------------------------
| Obtener ID del reportaje
|--------------------------------------------------------------------------
*/

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(404);
    exit('Reportaje no encontrado.');
}


/*
|--------------------------------------------------------------------------
| Obtener reportaje
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        r.id,
        r.titulo,
        r.resumen_corto,
        r.desarrollo,
        r.foto_principal,
        r.pdf_adjunto,
        r.fecha_publicacion,
        r.es_destacado,

        a.nombres AS autor_nombres,
        a.ap_paterno AS autor_ap_paterno,
        a.ap_materno AS autor_ap_materno,
        a.nickname,
        a.es_nickname

    FROM reportajes r

    INNER JOIN autores a
        ON a.id = r.autor_id

    WHERE r.id = :id

    LIMIT 1
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':id' => $id
]);

$reportaje = $stmt->fetch(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| Si no existe
|--------------------------------------------------------------------------
*/

if (!$reportaje) {
    http_response_code(404);
    exit('Reportaje no encontrado.');
}


/*
|--------------------------------------------------------------------------
| Formatear fecha
|--------------------------------------------------------------------------
*/

function fechaReportajeDetalle(string $fecha): string
{
    $meses = [
        1 => 'Ene',
        2 => 'Feb',
        3 => 'Mar',
        4 => 'Abr',
        5 => 'May',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Ago',
        9 => 'Set',
        10 => 'Oct',
        11 => 'Nov',
        12 => 'Dic'
    ];

    $timestamp = strtotime($fecha);

    if (!$timestamp) {
        return $fecha;
    }

    return
        $meses[(int) date('n', $timestamp)]
        . ' '
        . date('d', $timestamp)
        . ', '
        . date('Y', $timestamp);
}


/*
|--------------------------------------------------------------------------
| Nombre del autor
|--------------------------------------------------------------------------
*/

function autorReportajeDetalle(array $reportaje): string
{
    if (
        !empty($reportaje['es_nickname'])
        && !empty($reportaje['nickname'])
    ) {
        return $reportaje['nickname'];
    }

    $nombre = trim(
        ($reportaje['autor_nombres'] ?? '')
        . ' '
        . ($reportaje['autor_ap_paterno'] ?? '')
        . ' '
        . ($reportaje['autor_ap_materno'] ?? '')
    );

    return $nombre !== '' ? $nombre : 'Autor';
}


/*
|--------------------------------------------------------------------------
| Imagen principal
|--------------------------------------------------------------------------
*/

function imagenReportajeDetalle(?string $foto): string
{
    if (empty($foto)) {
        return 'assets/images/reportaje-18-08-26.jpg';
    }

    if (
        str_starts_with($foto, 'assets/')
        || str_starts_with($foto, 'http://')
        || str_starts_with($foto, 'https://')
    ) {
        return $foto;
    }

    return 'assets/uploads/reportajes/' . ltrim($foto, '/');
}


/*
|--------------------------------------------------------------------------
| Datos preparados
|--------------------------------------------------------------------------
*/

$imagen = imagenReportajeDetalle(
    $reportaje['foto_principal'] ?? null
);

$fecha = fechaReportajeDetalle(
    $reportaje['fecha_publicacion']
);

$autor = autorReportajeDetalle($reportaje);


/*
|--------------------------------------------------------------------------
| Obtener últimos reportajes
|--------------------------------------------------------------------------
*/

$sqlUltimos = "
    SELECT
        id,
        titulo,
        fecha_publicacion

    FROM reportajes

    WHERE id <> :id

    ORDER BY fecha_publicacion DESC, id DESC

    LIMIT 3
";

$stmtUltimos = $pdo->prepare($sqlUltimos);

$stmtUltimos->execute([
    ':id' => $id
]);

$ultimosReportajes = $stmtUltimos->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| Obtener reportaje anterior
|--------------------------------------------------------------------------
*/

$sqlAnterior = "
    SELECT
        id,
        titulo
    FROM reportajes
    WHERE
        fecha_publicacion < :fecha_anterior
        OR (
            fecha_publicacion = :fecha_igual
            AND id < :id
        )
    ORDER BY fecha_publicacion DESC, id DESC
    LIMIT 1
";

$stmtAnterior = $pdo->prepare($sqlAnterior);

$stmtAnterior->execute([
    ':fecha_anterior' => $reportaje['fecha_publicacion'],
    ':fecha_igual' => $reportaje['fecha_publicacion'],
    ':id' => $id
]);

$reportajeAnterior = $stmtAnterior->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Obtener reportaje siguiente
|--------------------------------------------------------------------------
*/

$sqlSiguiente = "
    SELECT
        id,
        titulo
    FROM reportajes
    WHERE
        fecha_publicacion > :fecha_siguiente
        OR (
            fecha_publicacion = :fecha_igual_siguiente
            AND id > :id_siguiente
        )
    ORDER BY fecha_publicacion ASC, id ASC
    LIMIT 1
";

$stmtSiguiente = $pdo->prepare($sqlSiguiente);

$stmtSiguiente->execute([
    ':fecha_siguiente' => $reportaje['fecha_publicacion'],
    ':fecha_igual_siguiente' => $reportaje['fecha_publicacion'],
    ':id_siguiente' => $id
]);

$reportajeSiguiente = $stmtSiguiente->fetch(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| PDF
|--------------------------------------------------------------------------
*/

$pdf = trim($reportaje['pdf_adjunto'] ?? '');

?>

<!doctype html>

<html lang="es">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no"
    >

    <title>
        <?= e($reportaje['titulo']) ?> | D&D Noticias
    </title>

    <link
        href="https://fonts.googleapis.com/css?family=Cabin:400,500,600&amp;subset=latin-ext,vietnamese"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="assets/css/style-starter.css"
    >
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>


<!-- =========================================================
     HEADER
========================================================= -->

<header id="site-header" class="fixed-top">

    <div class="container">

        <nav class="navbar navbar-expand-lg stroke">

            <a
                class="navbar-brand"
                href="index.php"
            >

                <img
                    src="assets/images/logo.png"
                    alt="D&D Noticias"
                    title="D&D Noticias"
                    style="height:75px;"
                >

            </a>


            <button
                class="navbar-toggler collapsed bg-gradient"
                type="button"
                data-toggle="collapse"
                data-target="#navbarTogglerDemo02"
                aria-controls="navbarTogglerDemo02"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >

                <span class="navbar-toggler-icon fa icon-expand fa-bars"></span>

                <span class="navbar-toggler-icon fa icon-close fa-times"></span>

            </button>


            <div
                class="collapse navbar-collapse"
                id="navbarTogglerDemo02"
            >

                <ul class="navbar-nav ml-auto">


                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="index.php"
                        >
                            Inicio
                        </a>

                    </li>


                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="index.php#actualidad"
                        >
                            Actualidad
                        </a>

                    </li>


                    <li class="nav-item active">

                        <a
                            class="nav-link"
                            href="reportajes.php"
                        >
                            Reportajes
                        </a>

                    </li>


                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="podcasts.php"
                        >
                            Podcast
                        </a>

                    </li>


                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="boletines.php"
                        >
                            Boletín NTEP
                        </a>

                    </li>


                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="alianzas.php"
                        >
                            Alianzas
                        </a>

                    </li>


                    <li class="nav-item">

                        <a
                            class="nav-link"
                            href="sobre.php"
                        >
                            Sobre D&D
                        </a>

                    </li>


                    <li class="ml-2">

                        <a
                            href="contacto.php"
                            class="btn btn-style btn-outline-secondary"
                        >
                            Contacto
                        </a>

                    </li>


                </ul>

            </div>

        </nav>

    </div>

</header>


<!-- =========================================================
     BREADCRUMB
========================================================= -->

<section class="breadcrumb-area py-sm-5 py-4">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <div class="breadcrumb-contents">

                    <h2 class="title-big">
                        Reportajes
                    </h2>


                    <div class="breadcrumb">

                        <ul>

                            <li>
                                <a href="index.php">
                                    Inicio
                                </a>
                            </li>


                            <li>

                                <a href="reportajes.php">
                                    Reportajes
                                </a>

                            </li>


                            <li class="active">

                                <?= e($reportaje['titulo']) ?>

                            </li>

                        </ul>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =========================================================
     REPORTAJE
========================================================= -->

<section class="w3l-blog mt-lg-5">

    <div class="text-element-9 py-5 mt-lg-5">

        <div class="container py-lg-3">

            <div class="row grid-text-9">


                <!-- =================================================
                     CONTENIDO PRINCIPAL
                ================================================== -->

                <div class="col-lg-8">

                    <div class="blog-single-post">


                        <!-- TÍTULO -->

                        <div class="post-content">

                            <h2 class="title-single mb-3">

                                <?= e($reportaje['titulo']) ?>

                            </h2>

                        </div>


                        <!-- AUTOR Y FECHA -->

                        <div class="blo-singl mb-4">

                            <ul class="blog-single-author-date d-flex align-items-center">

                                <li>
                                    Por
                                    <?= e($autor) ?>
                                </li>

                                <li>
                                    <?= e($fecha) ?>
                                </li>

                            </ul>

                        </div>


                        <!-- IMAGEN -->

                        <div class="single-post-image mb-4 text-center">

                            <?php if ($pdf !== ''): ?>

                                <a
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    href="<?= e($pdf) ?>"
                                >

                                    <img
                                        src="<?= e($imagen) ?>"
                                        class="img-fluid w-100 radius-image"
                                        alt="<?= e($reportaje['titulo']) ?>"
                                    >

                                    <br>

                                    Clic en la imagen para ver el documento completo

                                </a>

                            <?php else: ?>

                                <img
                                    src="<?= e($imagen) ?>"
                                    class="img-fluid w-100 radius-image"
                                    alt="<?= e($reportaje['titulo']) ?>"
                                >

                            <?php endif; ?>

                        </div>


                        <!-- =================================================
                             RESUMEN
                        ================================================== -->

                        <?php if (!empty($reportaje['resumen_corto'])): ?>

                            <blockquote class="blockquote my-5">

                                <q class="mb-3 d-block">

                                    <?= e($reportaje['resumen_corto']) ?>

                                </q>

                            </blockquote>

                        <?php endif; ?>


                        <!-- =================================================
                             DESARROLLO
                        ================================================== -->

                        <div class="single-post-content">

                            <?php

                            /*
                             * El administrador guarda el desarrollo
                             * como texto desde un textarea.
                             *
                             * e() protege el contenido HTML.
                             * nl2br() convierte los saltos de línea
                             * en párrafos visuales.
                             */

                            $desarrollo = trim(
                                $reportaje['desarrollo'] ?? ''
                            );

                            $lineas = preg_split(
                                "/\R{2,}/",
                                $desarrollo
                            );

                            foreach ($lineas as $parrafo):

                                $parrafo = trim($parrafo);

                                if ($parrafo === '') {
                                    continue;
                                }

                            ?>

                                <p
                                    align="justify"
                                    class="mb-4"
                                >

                                    <?= nl2br(e($parrafo)) ?>

                                </p>

                            <?php endforeach; ?>

                        </div>


                        <!-- =================================================
                             NAVEGACIÓN
                        ================================================== -->

                        <nav class="post-navigation row mb-5 py-4">


                            <!-- ANTERIOR -->

                            <div class="post-prev col-md-6 pr-sm-5">

                                <?php if ($reportajeAnterior): ?>

                                    <span class="nav-title">

                                        <span class="fa fa-arrow-left mr-2"></span>

                                        Anterior

                                    </span>


                                    <a
                                        href="reportaje.php?id=<?= (int) $reportajeAnterior['id'] ?>"
                                        rel="prev"
                                        class="posts-view posts-view-left"
                                    >

                                        <label>

                                            <?= e(
                                                $reportajeAnterior['titulo']
                                            ) ?>

                                        </label>

                                    </a>

                                <?php else: ?>

                                    <span class="nav-title">

                                        <span class="fa fa-arrow-left mr-2"></span>

                                        <a href="reportajes.php">
                                            Reportajes
                                        </a>

                                    </span>

                                <?php endif; ?>

                            </div>


                            <!-- SIGUIENTE -->

                            <div class="post-next col-md-6 pl-sm-5 text-right">

                                <?php if ($reportajeSiguiente): ?>

                                    <span class="nav-title">

                                        Siguiente

                                        <span class="fa fa-arrow-right ml-2"></span>

                                    </span>


                                    <a
                                        href="reportaje.php?id=<?= (int) $reportajeSiguiente['id'] ?>"
                                        rel="next"
                                        class="posts-view posts-view-right"
                                    >

                                        <label>

                                            <?= e(
                                                $reportajeSiguiente['titulo']
                                            ) ?>

                                        </label>

                                    </a>

                                <?php endif; ?>

                            </div>


                        </nav>


                    </div>

                </div>


                <!-- =================================================
                     BARRA LATERAL
                ================================================== -->

                <div class="col-lg-4 left-text-9 mt-lg-0 mt-5 pl-lg-4">


                    <!-- ÚLTIMOS REPORTAJES -->

                    <div class="left-top-9 mt-5 pt-sm-3">

                        <h6 class="heading-small-text-9 mb-3">

                            Últimos reportajes

                        </h6>


                        <?php if (!empty($ultimosReportajes)): ?>

                            <?php foreach ($ultimosReportajes as $ultimo): ?>

                                <a
                                    href="reportaje.php?id=<?= (int) $ultimo['id'] ?>"
                                    class="p-post d-block py-2"
                                >

                                    <h6 class="text-left-inner-9">

                                        <?= e($ultimo['titulo']) ?>

                                    </h6>


                                    <span class="sub-inner-text-9">

                                        <?= e(
                                            fechaReportajeDetalle(
                                                $ultimo['fecha_publicacion']
                                            )
                                        ) ?>

                                    </span>

                                </a>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <p>
                                No hay otros reportajes publicados.
                            </p>

                        <?php endif; ?>

                    </div>


                    <!-- =================================================
                         ARCHIVOS
                    ================================================== -->

                    <div class="categories mt-5 pt-sm-3">

                        <h6 class="heading-small-text-9">
                            Archivos
                        </h6>


                        <ul>

                            <?php

                            $sqlArchivos = "
                                SELECT
                                    YEAR(fecha_publicacion) AS anio,
                                    MONTH(fecha_publicacion) AS mes,
                                    COUNT(*) AS cantidad

                                FROM reportajes

                                GROUP BY
                                    YEAR(fecha_publicacion),
                                    MONTH(fecha_publicacion)

                                ORDER BY
                                    anio DESC,
                                    mes DESC
                            ";

                            $stmtArchivos = $pdo->query($sqlArchivos);

                            $archivos = $stmtArchivos->fetchAll(
                                PDO::FETCH_ASSOC
                            );


                            $mesesArchivos = [
                                1 => 'Enero',
                                2 => 'Febrero',
                                3 => 'Marzo',
                                4 => 'Abril',
                                5 => 'Mayo',
                                6 => 'Junio',
                                7 => 'Julio',
                                8 => 'Agosto',
                                9 => 'Setiembre',
                                10 => 'Octubre',
                                11 => 'Noviembre',
                                12 => 'Diciembre'
                            ];

                            ?>


                            <?php if (!empty($archivos)): ?>

                                <?php foreach ($archivos as $archivo): ?>

                                    <li>

                                        <a
                                            href="reportajes.php?mes=<?= (int) $archivo['mes'] ?>&anio=<?= (int) $archivo['anio'] ?>"
                                        >

                                            <?= e(
                                                $mesesArchivos[
                                                    (int) $archivo['mes']
                                                ]
                                            ) ?>

                                            <?= (int) $archivo['anio'] ?>

                                        </a>

                                    </li>

                                <?php endforeach; ?>

                            <?php else: ?>

                                <li>
                                    No hay archivos disponibles.
                                </li>

                            <?php endif; ?>

                        </ul>

                    </div>


                </div>


            </div>

        </div>

    </div>

</section>


<!-- =========================================================
     FOOTER
========================================================= -->

<section
    class="w3l-footer-29-main py-5"
    id="footer"
>

    <div class="footer-29 py-md-3">

        <div class="container">

            <div class="row footer-top-29">


                <div class="col-lg-6 col-md-6 footer-list-29 footer-1">

                    <h6 class="footer-title-29">
                        Quiénes Somos
                    </h6>


                    <p>

                        Somos un espacio de periodismo independiente que busca
                        visibilizar las acciones de diálogo en el país desde
                        una mirada constructiva.

                    </p>


                    <div class="main-social-footer-29">


                        <a
                            target="_blank"
                            href="https://www.facebook.com/DialogoyDesarrolloPeru"
                            class="facebook"
                        >

                            <span class="fa fa-facebook-square"></span>

                        </a>


                        <a
                            target="_blank"
                            href="https://www.tiktok.com/@dialogo.y.desarrollo"
                            class="twitter"
                        >

                            <img src="assets/images/tiktokp.png">

                        </a>


                        <a
                            target="_blank"
                            href="https://www.instagram.com/dialogo.y.desarrollo/"
                            class="instagram"
                        >

                            <span class="fa fa-instagram"></span>

                        </a>


                    </div>

                </div>


                <div class="col-lg-3 col-md-6 footer-list-29 footer-2 mt-md-0 mt-5">

                    <ul>

                        <h6 class="footer-title-29">
                            Contenido
                        </h6>


                        <li>
                            <a href="noticias.php">
                                Noticias
                            </a>
                        </li>


                        <li>
                            <a href="videos.php">
                                Videos
                            </a>
                        </li>


                        <li>
                            <a href="podcasts.php">
                                Podcast
                            </a>
                        </li>


                    </ul>

                </div>


                <div class="col-lg-3 col-md-6 mt-lg-0 mt-5 footer-list-29 footer-3">

                    <div class="properties">

                        <h6 class="footer-title-29">
                            Contacto
                        </h6>


                        <ul>

                            <li>

                                <a href="contacto.php">

                                    info@dialogoydesarrollo.com.pe

                                </a>

                            </li>

                        </ul>

                    </div>

                </div>


            </div>


            <div class="bottom-copies text-center">

                <p class="copy-footer-29">

                    © 2026 Diálogo y Desarrollo Perú.
                    All rights reserved |

                    Designed by

                    <a
                        target="_blank"
                        href="https://www.wsperu.info"
                    >
                        WebSolutions
                    </a>

                </p>

            </div>


        </div>

    </div>


    <!-- move top -->

    <button
        onclick="topFunction()"
        id="movetop"
        title="Go to top"
    >

        <span class="fa fa-angle-up"></span>

    </button>


    <script>

        window.onscroll = function () {
            scrollFunction()
        };


        function scrollFunction() {

            if (
                document.body.scrollTop > 20 ||
                document.documentElement.scrollTop > 20
            ) {

                document.getElementById("movetop").style.display = "block";

            } else {

                document.getElementById("movetop").style.display = "none";

            }

        }


        function topFunction() {

            document.body.scrollTop = 0;
            document.documentElement.scrollTop = 0;

        }

    </script>

</section>


<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script src="assets/js/jquery-3.3.1.min.js"></script>

<script src="assets/js/theme-change.js"></script>

<script src="assets/js/lightbox-plus-jquery.min.js"></script>

<script src="assets/js/easyResponsiveTabs.js"></script>

<script src="assets/js/owl.carousel.js"></script>

<script src="assets/js/jquery.magnific-popup.min.js"></script>

<script src="assets/js/bootstrap.min.js"></script>


<script>

    $(function () {

        $('.navbar-toggler').click(function () {

            $('body').toggleClass('noscroll');

        });

    });


    $(window).on("scroll", function () {

        var scroll = $(window).scrollTop();

        if (scroll >= 80) {

            $("#site-header").addClass("nav-fixed");

        } else {

            $("#site-header").removeClass("nav-fixed");

        }

    });


    $(".navbar-toggler").on("click", function () {

        $("header").toggleClass("active");

    });


    $(document).on("ready", function () {

        if ($(window).width() > 991) {

            $("header").removeClass("active");

        }


        $(window).on("resize", function () {

            if ($(window).width() > 991) {

                $("header").removeClass("active");

            }

        });

    });

</script>


</body>

</html>