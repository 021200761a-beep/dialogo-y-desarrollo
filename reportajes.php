<?php

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/funciones.php';

/*
|--------------------------------------------------------------------------
| Obtener reportajes
|--------------------------------------------------------------------------
| Mostramos primero los más recientes.
| Se relaciona con autores para obtener el nombre del autor.
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
    INNER JOIN autores a ON a.id = r.autor_id
    ORDER BY r.fecha_publicacion DESC, r.id DESC
";

$stmt = $pdo->query($sql);
$reportajes = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| Formatear fecha
|--------------------------------------------------------------------------
*/

function fechaReportaje(string $fecha): string
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

    $dia = date('d', $timestamp);
    $mes = $meses[(int) date('n', $timestamp)];
    $anio = date('Y', $timestamp);

    return $mes . ' ' . $dia . ', ' . $anio;
}


/*
|--------------------------------------------------------------------------
| Obtener nombre del autor
|--------------------------------------------------------------------------
*/

function nombreAutorReportaje(array $reportaje): string
{
    if (!empty($reportaje['es_nickname']) && !empty($reportaje['nickname'])) {
        return $reportaje['nickname'];
    }

    $nombre = trim(
        ($reportaje['autor_nombres'] ?? '') . ' ' .
        ($reportaje['autor_ap_paterno'] ?? '') . ' ' .
        ($reportaje['autor_ap_materno'] ?? '')
    );

    return $nombre !== '' ? $nombre : 'Autor';
}


/*
|--------------------------------------------------------------------------
| Obtener URL de la imagen
|--------------------------------------------------------------------------
*/

function imagenReportajePublica(?string $foto): string
{
    if (empty($foto)) {
        return 'assets/images/reportaje-18-08-26.jpg';
    }

    /*
     * Si la base de datos ya guarda la ruta completa,
     * la utilizamos directamente.
     */
    if (
        str_starts_with($foto, 'assets/') ||
        str_starts_with($foto, 'http://') ||
        str_starts_with($foto, 'https://')
    ) {
        return $foto;
    }

    /*
     * Si solamente se guardó el nombre del archivo,
     * buscamos en la carpeta de reportajes.
     */
    return 'assets/uploads/reportajes/' . ltrim($foto, '/');
}

?>
<!doctype html>
<html lang="es">

<head>

    <meta charset="utf-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>DDP Noticias - Diálogo y Desarrollo Perú</title>

    <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600&amp;subset=latin-ext,vietnamese"
          rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style-starter.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>

<!-- header -->
<header id="site-header" class="fixed-top">

    <div class="container">

        <nav class="navbar navbar-expand-lg stroke">

            <a class="navbar-brand" href="index.php">
                <img src="assets/images/logo.png"
                     alt="D&D Noticias"
                     title="D&D Noticias"
                     style="height:75px;" />
            </a>

            <button class="navbar-toggler collapsed bg-gradient"
                    type="button"
                    data-toggle="collapse"
                    data-target="#navbarTogglerDemo02"
                    aria-controls="navbarTogglerDemo02"
                    aria-expanded="false"
                    aria-label="Toggle navigation">

                <span class="navbar-toggler-icon fa icon-expand fa-bars"></span>
                <span class="navbar-toggler-icon fa icon-close fa-times"></span>

            </button>

            <div class="collapse navbar-collapse" id="navbarTogglerDemo02">

                <ul class="navbar-nav ml-auto">

                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            Inicio
                            <span class="sr-only">(current)</span>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="index.php#actualidad">
                            Actualidad
                        </a>
                    </li>

                    <li class="nav-item active">
                        <a class="nav-link" href="reportajes.php">
                            Reportajes
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="podcasts.php">
                            Podcast
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="boletines.php">
                            Boletín NTEP
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="alianzas.php">
                            Alianzas
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="sobre.php">
                            Sobre D&D
                        </a>
                    </li>

                    <li class="ml-2">
                        <a href="contacto.php"
                           class="btn btn-style btn-outline-secondary">
                            Contacto
                        </a>
                    </li>

                </ul>

            </div>

        </nav>

    </div>

</header>
<!-- //header -->


<!-- breadcrumb -->
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

                            <li class="active">
                                Reportajes
                            </li>

                        </ul>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>
<!-- //breadcrumb -->


<!-- grids block 5 -->
<div class="grids-block-5 py-5">

    <section class="py-lg-4 py-md-3">

        <div class="container">

            <div class="row">

                <?php if (empty($reportajes)): ?>

                    <div class="col-12 text-center py-5">

                        <h4>
                            No hay reportajes publicados.
                        </h4>

                        <p class="mt-3">
                            Próximamente encontrarás nuevos reportajes.
                        </p>

                    </div>

                <?php else: ?>

                    <?php foreach ($reportajes as $reportaje): ?>

                        <?php
                        $imagen = imagenReportajePublica(
                            $reportaje['foto_principal'] ?? null
                        );

                        $fecha = fechaReportaje(
                            $reportaje['fecha_publicacion']
                        );

                        $autor = nombreAutorReportaje($reportaje);

                        /*
                         * Por ahora enlazamos al ID del reportaje.
                         * En la siguiente parte podremos crear
                         * reportaje.php?id=... para mostrar
                         * el contenido completo.
                         */
                        $enlace = 'reportaje.php?id=' .
                            (int) $reportaje['id'];
                        ?>

                        <div class="col-lg-4 col-md-6 grids5-info mb-5">

                            <a href="<?= e($enlace) ?>"
                               class="d-block">

                                <img
                                    src="<?= e($imagen) ?>"
                                    alt="<?= e($reportaje['titulo']) ?>"
                                    class="img-fluid"
                                />

                            </a>

                            <div class="blog-info">

                                <h5>
                                    <?= e($fecha) ?>
                                </h5>

                                <h4>

                                    <a href="<?= e($enlace) ?>"
                                       class="d-block">

                                        <?= e($reportaje['titulo']) ?>

                                    </a>

                                </h4>

                                <a href="<?= e($enlace) ?>"
                                   class="btn mt-4 p-0">

                                    Leer
                                    <span class="fa fa-arrow-right"></span>

                                </a>

                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

        </div>

    </section>

</div>
<!-- // grids block 5 -->


<!-- footer block -->
<section class="w3l-footer-29-main py-5" id="footer">

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

                        <a target="_blank"
                           href="https://www.facebook.com/DialogoyDesarrolloPeru"
                           class="facebook">

                            <span class="fa fa-facebook-square"></span>

                        </a>

                        <a target="_blank"
                           href="https://www.tiktok.com/@dialogo.y.desarrollo"
                           class="twitter">

                            <img src="assets/images/tiktokp.png">

                        </a>

                        <a target="_blank"
                           href="https://www.instagram.com/dialogo.y.desarrollo/"
                           class="instagram">

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

                    <a target="_blank"
                       href="https://www.wsperu.info">
                        WebSolutions
                    </a>

                </p>

            </div>

        </div>

    </div>


    <!-- move top -->
    <button onclick="topFunction()"
            id="movetop"
            title="Go to top">

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
    <!-- /move top -->

</section>
<!-- //footer block -->


<!-- Template JavaScript -->

<script src="assets/js/jquery-3.3.1.min.js"></script>

<script src="assets/js/theme-change.js"></script>

<script src="assets/js/lightbox-plus-jquery.min.js"></script>

<script src="assets/js/easyResponsiveTabs.js"></script>

<script>

    $(document).ready(function () {

        $('#parentHorizontalTab').easyResponsiveTabs({

            type: 'default',
            width: 'auto',
            fit: true,
            tabidentify: 'hor_1',

            activate: function (event) {

                var $tab = $(this);
                var $info = $('#nested-tabInfo');
                var $name = $('span', $info);

                $name.text($tab.text());
                $info.show();

            }

        });

    });

</script>


<script src="assets/js/owl.carousel.js"></script>

<script>

    $(document).ready(function () {

        $('.owl-logos').owlCarousel({

            loop: true,
            margin: 0,
            nav: false,
            responsiveClass: true,
            autoplay: true,
            autoplayTimeout: 5000,
            autoplaySpeed: 1000,
            autoplayHoverPause: false,

            responsive: {

                0: {
                    items: 2,
                    nav: false
                },

                480: {
                    items: 2,
                    nav: false
                },

                568: {
                    items: 3,
                    nav: false
                },

                1000: {
                    items: 5,
                    nav: false
                }

            }

        });

    });

</script>


<script>

    $(document).ready(function () {

        $("#owl-demo1").owlCarousel({

            loop: true,
            margin: 20,
            responsiveClass: true,

            responsive: {

                0: {
                    items: 1,
                    nav: true
                },

                768: {
                    items: 2,
                    nav: false
                },

                1000: {
                    items: 3,
                    nav: true,
                    loop: false
                }

            }

        });

    });

</script>


<script>

    $(document).ready(function () {

        $('.owl-carousel').owlCarousel({

            loop: true,
            margin: 0,
            responsiveClass: true,

            responsive: {

                0: {
                    items: 1,
                    nav: true
                },

                400: {
                    items: 2,
                    nav: true,
                    margin: 20
                },

                768: {
                    items: 3,
                    nav: true,
                    margin: 20
                },

                1000: {
                    items: 4,
                    nav: true,
                    loop: true,
                    margin: 25
                }

            }

        });

    });

</script>


<script>

    $(function () {

        $('.navbar-toggler').click(function () {

            $('body').toggleClass('noscroll');

        });

    });

</script>


<script>

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


<script src="assets/js/bootstrap.min.js"></script>

</body>

</html>