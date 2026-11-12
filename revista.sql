-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: sql206.infinityfree.com
-- Tiempo de generación: 12-11-2026 a las 11:28:56
-- Versión del servidor: 11.4.13-MariaDB
-- Versión de PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `if0_42984043_revista`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `autores`
--

CREATE TABLE `autores` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `ap_paterno` varchar(100) DEFAULT NULL,
  `ap_materno` varchar(100) DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `es_nickname` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `autores`
--

INSERT INTO `autores` (`id`, `nombres`, `ap_paterno`, `ap_materno`, `nickname`, `es_nickname`) VALUES
(1, 'autor 1', 'prueba', 'pr', 'autor_revista', 0),
(3, 'sfdvsad', 'sadfsadf', 'asdfsadfsa', 'lol', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `boletines`
--

CREATE TABLE `boletines` (
  `id` int(10) UNSIGNED NOT NULL,
  `numero_boletin` varchar(50) NOT NULL,
  `resumen` varchar(500) DEFAULT NULL,
  `foto_portada` varchar(255) DEFAULT NULL,
  `archivo_pdf` varchar(255) NOT NULL,
  `fecha_publicacion` date NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `boletines`
--

INSERT INTO `boletines` (`id`, `numero_boletin`, `resumen`, `foto_portada`, `archivo_pdf`, `fecha_publicacion`, `usuario_id`) VALUES
(2, '1', '-Promueven megaproyectos turísticos por S/ 2,400 mllns.\r\n\r\n-Invertirán S/ 9 millones en zonas rurales de Cusco.\r\n\r\n-Producción láctea se duplica en Cajamarca.', 'assets/uploads/boletines/boletin_portada_4a313e93ea178dd8a69d039906b3eb97.png', 'assets/uploads/boletines/boletin_pdf_53399b2e9cadf6cf41fef2da3288a19b.pdf', '2026-09-24', 2),
(3, '2', '', 'assets/uploads/boletines/boletin_portada_1305fd654036eb0c2613517c7d50d050.png', 'assets/uploads/boletines/boletin_pdf_20e38a3cffa0a5f4f9fb19d9779e3bd3.pdf', '2026-09-28', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noticias`
--

CREATE TABLE `noticias` (
  `id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `link_externo` varchar(500) DEFAULT NULL,
  `fecha_publicacion` date NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `noticias`
--

INSERT INTO `noticias` (`id`, `titulo`, `foto`, `link_externo`, `fecha_publicacion`, `usuario_id`) VALUES
(1, 'Impulsan talento local en Hualgayoc', 'assets/uploads/noticias/noticia_e903d3437bff83fb6ea05193a2f611c6.png', 'https://minart.pe/2025/11/07/gold-fields-y-empresas-locales-apuestan-por-el-talento-hualgayoquino-capacitando-a-pobladores-en-manejo-de-camiones-mineros-en-hualgayoc/?fbclid=IwY2xjawON3L1leHRuA2FlbQIxMABicmlkETFjbkNQNVZ0NVN4WVhmekpIc3J0YwZhcHBfaWQQMjIyMDM5MTc4ODIwMDg5MgABHpzyjGhuFRl3v4LIaB0ks6cftrL-zGT73DnNcsALEsgAQZRUufUc4iTcrLEc_aem_hYAx1MhXJflxvlIajOunzg', '2026-09-12', 2),
(3, 'Inauguran moderno colegio en Cerro Azul', 'assets/uploads/noticias/noticia_86569d9fab23e060184e57e48eb9e771.png', 'https://andina.pe/agencia/noticia-canete-inauguran-moderno-local-colegio-construido-inversion-s30-millones-1051936.aspx', '2026-09-28', 3),
(4, 'Megaproyecto de saneamiento en Juliaca', 'assets/uploads/noticias/noticia_1ede06805852998489bc559eb6ecb0c6.png', 'https://diarioelnoticiero.com/ministerio-de-vivienda-llego-a-juliaca-para-reafirmar-que-el-proyecto-de-agua-potable-y-alcantarillado-no-se-detiene-2/?fbclid=IwY2xjawON3QpleHRuA2FlbQIxMABicmlkETFjbkNQNVZ0NVN4WVhmekpIc3J0YwZhcHBfaWQQMjIyMDM5MTc4ODIwMDg5MgABHneyK2etohVG6KyvDXFJM_GtKA_gWI85gaZ5yLBuK66R0SW80sBUUdbwRCjt_aem_ByzW0vi0q7VetPB26LXGBg', '2026-09-28', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `podcasts`
--

CREATE TABLE `podcasts` (
  `id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `url_embed` varchar(500) NOT NULL,
  `fecha_publicacion` date NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `podcasts`
--

INSERT INTO `podcasts` (`id`, `titulo`, `url_embed`, `fecha_publicacion`, `usuario_id`) VALUES
(2, 'primera noticia prueba', 'https://www.youtube.com/watch?v=jlYH14uDAfU', '2026-09-24', 3),
(3, 'ALFREDO JALIFE: \"Trump siempre ha tenido Pickaxe Mountain en su punto de mira', 'https://www.youtube.com/watch?v=H_9dIGPEgOA', '2026-09-28', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportajes`
--

CREATE TABLE `reportajes` (
  `id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `resumen_corto` varchar(500) DEFAULT NULL,
  `desarrollo` longtext NOT NULL,
  `foto_principal` varchar(255) DEFAULT NULL,
  `pdf_adjunto` varchar(255) DEFAULT NULL,
  `fecha_publicacion` date NOT NULL,
  `es_destacado` tinyint(1) NOT NULL DEFAULT 0,
  `autor_id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reportajes`
--

INSERT INTO `reportajes` (`id`, `titulo`, `resumen_corto`, `desarrollo`, `foto_principal`, `pdf_adjunto`, `fecha_publicacion`, `es_destacado`, `autor_id`, `usuario_id`, `created_at`, `updated_at`) VALUES
(4, 'Consecuencias que tendría si Congreso y Ejecutivo aprueban otra prórroga del Reinfo', 'El Congreso y el Ejecutivo extenderían por un año más el proceso de formalización minera, pese a los cuestionamientos por sus escasos resultados y sus efectos negativos sobre el ambiente y la seguridad ciudadana.', 'Todo indica que el Registro Integral de Formalización Minera (Reinfo), que debería desaparecer el 31 de diciembre próximo, volverá a ser ampliado una vez más. Parece existir un acuerdo implícito entre el Congreso y el Ejecutivo para que este proceso de formalización, que no ha dado los resultados, continúe por lo menos un año más.\r\n\r\nEso se puede deducir de las posturas de varios congresistas de las cámaras de senadores y de diputados, que mantienen vínculos con organizaciones de mineros informales, como la Confemin, o han recibido a sus representantes en sus despachos. También de las declaraciones del ministro de Energía y Minas, Guillermo Shinno, quien no descartó una nueva prórroga hasta que se apruebe la nueva Ley MAPE.\r\n\r\nNo obstante, algunos congresistas, como Ruth Luque Ibarra, rechazaron la posibilidad de una nueva prórroga. Luque dijo, por ejemplo, que ampliar el Reinfo sería condenar a pueblos y líderes indígenas que en la actualidad viven en alto riesgo criminal y de contaminación ambiental. “Así que esa posible ampliación del Reinfo debe ser rechazada”, señaló.\r\n\r\nA esta posición se suman varios especialistas, quienes coinciden en que mantener este proceso, pese a sus escasos resultados, sería dañino para el medio ambiente y la seguridad de los peruanos. César Ipenza, especialista del Observatorio de Minería Ilegal (OMI), dijo que ampliarlo mantendría la impunidad de quienes usan el Reinfo para lavar oro de origen ilegal, acrecentaría la destrucción de la Amazonía e incrementaría la inseguridad ciudadana. En tanto, Iván Arenas, analista en temas mineros, consideró que hacerlo implicaría mantener la capacidad de degradación de la Amazonía y prolongar la ilegalidad.\r\n\r\nIpenza y Arenas sostienen que no se justifica otra ampliación. Quienes impulsan su extensión, en cambio, argumentan que los mineros no han logrado formalizarse y que el Reinfo debe continuar. “Pero ya vamos más de 24 años de un proceso de formalización. Si no han logrado avanzar o concretarlo en los últimos diez años, menos van a poder hacerlo en un año. Siguen utilizando este mecanismo para la impunidad, porque un minero con Reinfo no puede ser procesado por el delito de minería ilegal y puede seguir lavando oro de origen ilegal”, señaló Ipenza.\r\n\r\nEn esa misma línea, Arenas señaló que antes de hablar de una ampliación se debe hablar de depuración. “Hay 27 mil mineros con Reinfo. Debemos depurarlo y luego hablar de ampliación, pero una ampliación como la que quieren los mineros informales ya no es viable”, dijo. “Si sucede eso, el Gobierno sería cómplice de la ilegalidad minera. Todos sabemos que el Reinfo se utiliza para lavar producción ilegal”.\r\n\r\nAdemás, según Ipenza, una nueva prórroga permitirá que los mineros continúen operando en la informalidad y facilitará su expansión. “Vamos a seguir viendo lo que ya se ha visto. No va a cambiar nada o, más bien, se va a incrementar el nivel de daño. Cada día hay más mineros ilegales que utilizan distintos mecanismos, como el Reinfo, para lavar oro de origen ilegal”.\r\n\r\nPor ello, Arenas propuso que, para continuar con la formalización, se llegue primero “a un acuerdo con los informales para que se haga una depuración y luego recién se pueda hablar si es factible la ampliación. Primero lo primero”.\r\n\r\nEn la misma línea, el exministro de Energía y Minas Carlos Herrera Descalzi declaró a medios que ampliar este proceso solo prolongará la informalidad. Cuestionó también que durante todo ese tiempo no se haya intentado sacar adelante la Ley MAPE. De acuerdo con Herrera Descalzi, como no se hizo nada para resolver el problema, se termina extendiéndolo.', 'assets/uploads/reportajes/reportaje_f2706464be9a4eff0d40ef5784c50d8d.jpg', 'https://www.dialogoydesarrollo.com.pe/riesgos-prorroga-reinfo.pdf', '2026-09-24', 0, 1, 3, '2026-09-24 10:12:35', '2026-09-24 10:12:35'),
(5, 'Medidas que el nuevo gobierno debe tomar para frenar la minería ilegal', 'El Observatorio de Minería Ilegal señala que el desafío es contener esta economía ilícita. Plantea cerrar el Reinfo, fortalecer la persecución penal, eliminar el uso del mercurio, financiar una estrategia contra las mafias, etc.', 'El próximo gobierno tendrá poco margen para postergar decisiones sobre la minería ilegal. Para el Observatorio de Minería Ilegal (OMI), los primeros cien días del nuevo gobierno, el de Keiko Fujimori, serán decisivos para detener una actividad que, según esa institución, es la principal economía ilícita, porque mueve alrededor de 11 500 millones de dólares al año.\r\n\r\nEl especialista del OMI, César Ipenza, dijo anteriormente que el nuevo gobierno deberá asumir que la minería ilegal ya no es solo un problema ambiental, sino que financia otras actividades criminales y genera violencia.\r\n\r\nLa principal recomendación es poner fin al Registro Integral de Formalización Minera (Reinfo). Según el OMI, este mecanismo, creado para facilitar la formalización de pequeños productores, se convirtió en instrumento de miles de mineros para continuar extrayendo oro sin cumplir exigencias ambientales ni legales. Por eso el nuevo gobierno debe establecer un régimen transitorio que conduzca al cierre definitivo del registro.\r\n\r\nOtra prioridad consiste en eliminar el blindaje legal que hoy beneficia a quienes operan bajo el Reinfo. El año pasado, el Tribunal Constitucional recomendó retirar la exención de responsabilidad penal para los mineros inscritos en ese registro, pero esa recomendación no fue implementada. El Observatorio sostiene que el próximo gobierno debe establecer responsabilidades penales y civiles para quienes continúen explotando minerales en zonas prohibidas o incumplan las normas ambientales.\r\n\r\nLas propuestas también apuntan a fortalecer las instituciones encargadas de combatir la minería ilegal. El OMI advierte que la Fiscalía carece de presupuesto para ejecutar operativos de interdicción en territorios dominados por organizaciones criminales. Sin recursos, sostiene, cualquier estrategia contra estas redes resulta insuficiente.\r\n\r\nAsimismo, plantea reforzar los mecanismos anticorrupción dentro del Ministerio de Energía y Minas (Minem), especialmente en la Dirección General de Formalización Minera, que arrastra denuncias por presuntas irregularidades en la tramitación de expedientes. Hace poco el Minem aprobó su nueva Política Antisoborno mediante la Resolución Ministerial N. ° 232-2026, la que debería ser reforzada por el nuevo gobierno.\r\n\r\nEl OMI también propone aprobar una ley que elimine poco a poco el uso del mercurio hasta prohibirlo por completo en 2030. Y recomienda poner en funcionamiento el Sistema Interoperable de la Pequeña Minería y Minería Artesanal para mejorar la trazabilidad del oro y dificultar la comercialización de minerales de origen ilegal.\r\n\r\nOtra medida plantea prohibir mediante decreto supremo la actividad minera en los ríos amazónicos, donde la extracción aurífera provocado una grave contaminación ambiental.\r\n\r\nSin duda, el gran reto del próximo gobierno será contener el avance de la minería ilegal antes de que siga consolidándose como una de las principales amenazas para la seguridad, la gobernabilidad y el ambiente en el país. Las medidas están identificadas y la urgencia es evidente.', 'assets/uploads/reportajes/reportaje_3c2c23871f2abcf890aaf225b0ff41a9.jpg', 'https://www.dialogoydesarrollo.com.pe/medidas-contra-la-mineria-ilegal.pdf', '2026-09-24', 0, 1, 3, '2026-09-24 10:25:31', '2026-09-28 14:33:58'),
(7, 'Cómo evitar que el canon del boom minero termine en obras de poco impacto', 'El nuevo ciclo de altos precios de los minerales volverá a elevar las transferencias a regiones y municipios. El desafío de las autoridades que asumirán en 2027 será usar esos recursos para cerrar brechas y generar desarrollo.', 'Hasta julio de este año, los gobiernos regionales recibieron más de 2 500 millones de soles por canon y regalías mineras, mientras que los municipios superaron los 9 000 millones. Todo indica que el superciclo de los precios de los minerales seguirá elevando estas transferencias durante los próximos años.\r\n\r\nLas nuevas autoridades regionales y municipales, que asumirán en enero de 2027, administrarán más recursos. El reto será convertirlos en desarrollo sostenible. Luis Miguel Castilla Rubio, exministro de Economía y director ejecutivo de Videnza Instituto, sostiene que estos recursos deben formar parte de una estrategia regional de largo plazo que reduzca desigualdades y disminuya la dependencia de los precios de los minerales.\r\n\r\nPara Castilla existe una contradicción evidente entre los ingresos que genera la minería y los beneficios que percibe la población. \"Hay un divorcio muy grande entre los recursos transferidos por canon y regalías y el cierre efectivo de brechas sociales\", afirma.\r\n\r\nExplica que la amplia autonomía de los gobiernos subnacionales permite que muchos destinen el canon a proyectos alejados de las necesidades más urgentes. Así, mientras algunos municipios reciben cientos de millones de soles por actividad minera, todavía existen comunidades sin agua potable, alcantarillado, servicios de salud o infraestructura educativa adecuada.\r\n\r\nEn cambio, gran parte del presupuesto termina convertido en estadios, plazas, losas deportivas y otras obras de bajo impacto. A eso se suma la atomización del gasto. En lugar de concentrar recursos en proyectos que transformen una localidad, muchas municipalidades realizan pequeñas inversiones que consumen presupuesto sin mejorar las condiciones de vida de la población.\r\n\r\nEl riesgo del clientelismo\r\nCastilla también cuestiona el uso excesivo de la administración directa. Bajo esta modalidad, los gobiernos locales ejecutan las obras sin procesos de licitación y actúan como empresas constructoras al contratar directamente personal, maquinaria y servicios.\r\n\r\nDiversos estudios, señala Castilla, muestran que este mecanismo favorece el empleo temporal con fines políticos, alimenta redes de clientelismo y eleva los riesgos de corrupción. Para el exministro, el país también debe dejar atrás la idea de que el desarrollo depende únicamente del cemento. Carreteras, hospitales y colegios siguen siendo indispensables, pero ya no bastan.\r\n\r\nLas inversiones con mejores resultados sociales son las que mejoran la vida y fortalecen las capacidades de la población: agua potable y saneamiento, salud y nutrición infantil, educación de calidad, conectividad vial y digital, electrificación y otras que impulsen la producción regional y generen empleo privado.\r\n\r\nCastilla también propone ampliar el uso del canon a proyectos productivos que mejoren la competitividad regional y fortalezcan las cadenas agrícolas, industriales y de servicios, siempre que no generen gastos permanentes para el Estado. Advierte que sería un error utilizar estos recursos para financiar burocracia o crear nuevas plazas laborales. \"Los ingresos volátiles deben financiar inversiones temporales y no gastos permanentes\".\r\n\r\nSegún el economista, el nuevo ciclo de ingresos mineros exige revisar la Ley del Canon para que estos recursos ayuden al cierre de brechas y al desarrollo productivo, y fortalecer el planeamiento regional para impedir que cada nueva gestión abandone proyectos estratégicos y priorice obras de corto plazo.\r\n\r\nCastilla sostiene que el próximo quinquenio ofrece una oportunidad difícil de repetir. Si el canon se invierte con visión de largo plazo, se traducirá en mejor educación, salud y desarrollo productivo. Si no, la bonanza minera volverá a dejar miles de millones de soles en obras que no mejoran la vida de la población.', 'assets/uploads/reportajes/reportaje_ae0163b5b8fcafaf20a2e8a2522e840b.jpg', 'https://www.dialogoydesarrollo.com.pe/la-mineria-forma-genera-recursos.pdf', '2026-09-28', 0, 1, 3, '2026-09-28 14:35:58', '2026-09-28 14:35:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportajes_fotos`
--

CREATE TABLE `reportajes_fotos` (
  `id` int(10) UNSIGNED NOT NULL,
  `reportaje_id` int(10) UNSIGNED NOT NULL,
  `url_foto` varchar(255) NOT NULL,
  `orden` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `ap_paterno` varchar(100) NOT NULL,
  `ap_materno` varchar(100) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('admin','editor','redactor') NOT NULL DEFAULT 'redactor',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombres`, `ap_paterno`, `ap_materno`, `email`, `password_hash`, `rol`, `created_at`) VALUES
(2, 'Administrador', 'Principal', '', 'admin@revista.com', '$2y$10$Wxh1YSHkHXWtODZi3Eq3leWq.sRON0GrkKvGAmjBT56cQ.OHCoeTq', 'admin', '2026-09-11 15:42:13'),
(3, 'marcel', 'pere', 'flores', 'marcelo@gmail.com', '$2y$10$Qq3idFNqvM2aQQ8yQ1pOveflzUgOvysYtcqz6VKpeSPs3ISwyv7vy', 'admin', '2026-09-24 08:15:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `videos`
--

CREATE TABLE `videos` (
  `id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `url_embed` varchar(500) NOT NULL,
  `fecha_publicacion` date NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `videos`
--

INSERT INTO `videos` (`id`, `titulo`, `url_embed`, `fecha_publicacion`, `usuario_id`) VALUES
(1, 'nuevo video', 'https://www.youtube.com/watch?v=AzHRptk-_nI', '2026-09-24', 3);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `autores`
--
ALTER TABLE `autores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `boletines`
--
ALTER TABLE `boletines`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_boletines_numero` (`numero_boletin`),
  ADD KEY `fk_boletines_usuario` (`usuario_id`),
  ADD KEY `idx_boletines_fecha` (`fecha_publicacion`);

--
-- Indices de la tabla `noticias`
--
ALTER TABLE `noticias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_noticias_usuario` (`usuario_id`),
  ADD KEY `idx_noticias_fecha` (`fecha_publicacion`);

--
-- Indices de la tabla `podcasts`
--
ALTER TABLE `podcasts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_podcasts_usuario` (`usuario_id`),
  ADD KEY `idx_podcasts_fecha` (`fecha_publicacion`);

--
-- Indices de la tabla `reportajes`
--
ALTER TABLE `reportajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reportajes_autor` (`autor_id`),
  ADD KEY `fk_reportajes_usuario` (`usuario_id`),
  ADD KEY `idx_reportajes_fecha` (`fecha_publicacion`),
  ADD KEY `idx_reportajes_destacado` (`es_destacado`);

--
-- Indices de la tabla `reportajes_fotos`
--
ALTER TABLE `reportajes_fotos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reportajes_fotos_reportaje` (`reportaje_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_usuarios_email` (`email`);

--
-- Indices de la tabla `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_videos_usuario` (`usuario_id`),
  ADD KEY `idx_videos_fecha` (`fecha_publicacion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `autores`
--
ALTER TABLE `autores`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `boletines`
--
ALTER TABLE `boletines`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `noticias`
--
ALTER TABLE `noticias`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `podcasts`
--
ALTER TABLE `podcasts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `reportajes`
--
ALTER TABLE `reportajes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `reportajes_fotos`
--
ALTER TABLE `reportajes_fotos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `boletines`
--
ALTER TABLE `boletines`
  ADD CONSTRAINT `fk_boletines_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `noticias`
--
ALTER TABLE `noticias`
  ADD CONSTRAINT `fk_noticias_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `podcasts`
--
ALTER TABLE `podcasts`
  ADD CONSTRAINT `fk_podcasts_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `reportajes`
--
ALTER TABLE `reportajes`
  ADD CONSTRAINT `fk_reportajes_autor` FOREIGN KEY (`autor_id`) REFERENCES `autores` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reportajes_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `reportajes_fotos`
--
ALTER TABLE `reportajes_fotos`
  ADD CONSTRAINT `fk_reportajes_fotos_reportaje` FOREIGN KEY (`reportaje_id`) REFERENCES `reportajes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `fk_videos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
