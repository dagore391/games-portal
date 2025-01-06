-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-01-2025 a las 16:03:44
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `juegos404`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `achievement`
--

CREATE TABLE `achievement` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(500) NOT NULL,
  `value` varchar(32) NOT NULL,
  `published` timestamp NOT NULL DEFAULT current_timestamp(),
  `author` bigint(20) UNSIGNED NOT NULL,
  `game` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cheat`
--

CREATE TABLE `cheat` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL DEFAULT '',
  `published` timestamp NOT NULL DEFAULT current_timestamp(),
  `author` bigint(20) UNSIGNED NOT NULL,
  `game` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `company`
--

CREATE TABLE `company` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `country`
--

CREATE TABLE `country` (
  `id` int(10) UNSIGNED NOT NULL,
  `iso_code` char(3) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `country`
--

INSERT INTO `country` (`id`, `iso_code`, `name`) VALUES
(1, 'esp', 'España');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entry`
--

CREATE TABLE `entry` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category` enum('articles','faqs','news','previews','reviews','rumours') NOT NULL,
  `title` varchar(200) NOT NULL,
  `resume` varchar(500) NOT NULL DEFAULT '',
  `content` text NOT NULL DEFAULT '',
  `published` timestamp NOT NULL DEFAULT current_timestamp(),
  `author` bigint(20) UNSIGNED NOT NULL,
  `game` bigint(20) UNSIGNED DEFAULT NULL,
  `platform` int(10) UNSIGNED DEFAULT NULL,
  `score` tinyint(1) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `entry`
--

INSERT INTO `entry` (`id`, `category`, `title`, `resume`, `content`, `published`, `author`, `game`, `platform`, `score`) VALUES
(1, 'news', 'Saturn ya se encuentra a la venta en Estados Unidos', 'El ejecutivo asegura que 30000 unidades de su nueva máquina ya se están disponibles en los estantes de varias cadenas importantes', '<p><b>Tom Kalinske</b>, presidente de <b>Sega of America</b>, ha aprovechado su espacio en la <b>Electronic Entertainment Expo</b> para realizar un anuncio que ha sorprendido a la prensa internacional, <b>Saturn</b> ya está disponible. El ejecutivo ha asegurado que 30000 unidades de su nueva máquina ya se encuentran en los estantes de importantes cadenas como <b>Babbages</b>, <b>Electronics Boutique</b>, <b>Software Etc.</b> o <b>Toys \"R\" Us</b>, a un precio de $399 (sólo la consola) o de $449 junto con el CD de <b>Virtua Fighter</b>.</p> \r\n\r\n<blockquote><p>El consumidor se muere por una nueva experiencia ahora, no va a esperar otro año.</p><footer><cite>Tom Kalinske</cite></footer></blockquote>\r\n\r\n<p>Además, como era de esperar, la compañía también ha puesto a disposición de los consumidores una pequeña selección de juegos que incluye: <b>Clockwork Knight</b>, <b>Daytona USA</b>, <b>Panzer Dragoon</b>, <b>Pebble Beach Golf Links</b> y <b>Worldwide Soccer: Sega International Victory Goal Edition</b>, para que le puedan sacar el máximo partido a la nueva consola. Si todo va según lo previsto, <b>Sega</b> espera lanzar 600000 unidades en los <b>Estados Unidos</b> para estas Navidades y tiene proyectados un total de 3 millones para su primer año de vida en dicho territorio.</p>', '1995-05-11 19:00:00', 1, NULL, NULL, 0),
(2, 'news', 'Saturn llega el próximo mes de julio a nuestro país', '', '<p>Hasta la fecha, muchos medios especializados estaban convencidos de que <b>Saturn</b> vería la luz en el viejo continente el próximo 2 de septiembre, sin embargo, eso está a punto de cambiar. El 17 de junio de 1995, el Director General de <b>Sega Europa</b>, <b>Malcom Miller</b>, y su homónimo de <b>Sega España</b>, <b>José Ángel Sánchez</b>, celebraron una rueda de prensa en un céntrico hotel de <b>Madrid</b> en la que detallaron sus planes para la nueva consola de videojuegos. En ella se puso de manifiesto la intención de la compañía de vender alrededor de 45000 unidades en sus primeros 6 meses de vida en nuestro país, pero la sorpresa de la ceremonia llegó con el anuncio del día de su salida dentro de nuestras fronteras.</p><p>Al igual que sucedió con el anuncio para el mercado estadounidense, que como algunos recordaréis, tuvo lugar el pasado 11 de mayo durante el <b>E3</b> (<i>Electronic Entertainment Expo</i>) que se celebró en <b>Los Ángeles</b>, la máquina también ha visto adelantado su lanzamiento en territorio europeo y se pondrá a la venta en escasas semanas, concretamente el 7 de julio, a un precio recomendado de 79900 pesetas.</p>', '1995-06-17 19:00:00', 1, NULL, NULL, 0),
(3, 'articles', 'Especial de lanzamiento: ¡Sega Saturn a la venta en España!', '', '', '1995-07-07 19:00:00', 1, NULL, NULL, 0),
(4, 'news', 'Sega baja el precio de sus nuevos títulos en Estados Unidos', '', '', '1996-10-08 19:00:00', 1, NULL, NULL, 0),
(5, 'news', 'Tres juegos gratis con la compra de una Saturn en Estados Unidos', '', '', '1996-11-18 20:00:00', 1, NULL, NULL, 0),
(6, 'news', 'Las ventas de Saturn se disparan en Estados Unidos', '', '', '1996-12-03 20:00:00', 1, NULL, NULL, 0),
(7, 'news', 'Saturn «Action Pack» a la venta mañana en Europa', '', '', '1997-03-18 20:00:00', 1, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entry_comment`
--

CREATE TABLE `entry_comment` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `content` text DEFAULT NULL,
  `published` timestamp NOT NULL DEFAULT current_timestamp(),
  `entry` bigint(20) UNSIGNED NOT NULL,
  `author` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `forum`
--

CREATE TABLE `forum` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(500) NOT NULL,
  `category` int(10) UNSIGNED NOT NULL,
  `parent_forum` int(10) UNSIGNED DEFAULT NULL,
  `who_can_read` enum('GUEST','REGISTER','USER','WRITER','MODERATOR','MANAGER','ADMIN') NOT NULL DEFAULT 'REGISTER',
  `who_can_write` enum('GUEST','REGISTER','USER','WRITER','MODERATOR','MANAGER','ADMIN') NOT NULL DEFAULT 'REGISTER',
  `position` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `forum`
--

INSERT INTO `forum` (`id`, `title`, `description`, `category`, `parent_forum`, `who_can_read`, `who_can_write`, `position`) VALUES
(1, 'General', 'Descripción de prueba para este foro.', 1, NULL, 'GUEST', 'REGISTER', 1),
(2, 'Presentaciones', 'Descripción de prueba para este foro.', 1, NULL, 'GUEST', 'REGISTER', 2),
(3, 'Colecciones', 'Descripción de prueba para este foro.', 1, NULL, 'GUEST', 'REGISTER', 3),
(4, 'Android', 'Descripción de prueba para este foro.', 2, NULL, 'GUEST', 'REGISTER', 1),
(5, 'iOS', 'Descripción de prueba para este foro.', 2, NULL, 'GUEST', 'REGISTER', 2),
(6, 'Nintendo Switch', 'Descripción de prueba para este foro.', 2, NULL, 'GUEST', 'REGISTER', 3),
(7, 'PlayStation 5', 'Descripción de prueba para este foro.', 2, NULL, 'GUEST', 'REGISTER', 4),
(8, 'Xbox Series', 'Descripción de prueba para este foro.', 2, NULL, 'GUEST', 'REGISTER', 5),
(9, 'Mac OS', 'Descripción de prueba para este foro.', 2, NULL, 'GUEST', 'REGISTER', 6),
(10, 'Windows', 'Descripción de prueba para este foro.', 2, NULL, 'GUEST', 'REGISTER', 7),
(11, 'Hardware y presupuestos', 'Descripción de prueba para este foro.', 3, NULL, 'GUEST', 'REGISTER', 1),
(12, 'Teléfonos móviles', 'Descripción de prueba para este foro.', 3, NULL, 'GUEST', 'REGISTER', 2),
(13, 'Desarrollo de videojuegos', 'Descripción de prueba para este foro.', 3, NULL, 'GUEST', 'REGISTER', 3),
(14, 'Redes e Internet', 'Descripción de prueba para este foro.', 3, NULL, 'GUEST', 'REGISTER', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `forum_category`
--

CREATE TABLE `forum_category` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` varchar(500) NOT NULL,
  `position` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `forum_category`
--

INSERT INTO `forum_category` (`id`, `title`, `description`, `position`) VALUES
(1, 'Principal', 'Descripción de prueba', 1),
(2, 'Videojuegos', 'Descripción de prueba', 2),
(3, 'Informática y tecnología', 'Descripción de prueba', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `forum_post`
--

CREATE TABLE `forum_post` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL DEFAULT '',
  `published` timestamp NOT NULL DEFAULT current_timestamp(),
  `author` bigint(20) UNSIGNED NOT NULL,
  `forum` int(10) UNSIGNED NOT NULL,
  `topic` bigint(20) UNSIGNED DEFAULT NULL,
  `is_closed` bit(1) NOT NULL DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `game`
--

CREATE TABLE `game` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `gamegroup` bigint(20) UNSIGNED DEFAULT NULL,
  `platform` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `resume` text DEFAULT NULL,
  `release_day` tinyint(2) DEFAULT NULL,
  `release_month` tinyint(2) DEFAULT NULL,
  `release_year` smallint(4) DEFAULT NULL,
  `release_price` varchar(16) DEFAULT NULL,
  `sources` text NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `game`
--

INSERT INTO `game` (`id`, `gamegroup`, `platform`, `name`, `resume`, `release_day`, `release_month`, `release_year`, `release_price`, `sources`) VALUES
(1, NULL, 1, 'Tetris', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 3)'),
(2, NULL, 1, 'F-1 Race', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 4)'),
(3, NULL, 1, 'The Amazing Spider-Man', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 4)'),
(4, NULL, 1, 'Tennis', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 4)'),
(5, 45, 1, 'Nintendo World Cup', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 4)'),
(6, NULL, 1, 'Super Mario Land', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 4)'),
(7, NULL, 1, 'Dr. Mario', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 4)'),
(8, NULL, 1, 'Alleyway', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 4)'),
(9, NULL, 1, 'Golf', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 4)'),
(10, NULL, 1, 'Burai Fighter Deluxe', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 4)'),
(11, NULL, 1, 'Wizards & Warriors X: The Fortress of Fear', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 4)'),
(12, NULL, 1, 'Pinball: Revenge of the Gator', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 4)'),
(13, NULL, 1, 'Kwirk', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 4)'),
(14, NULL, 1, 'Gargoyle\'s Quest', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 4)'),
(15, NULL, 1, 'Dynablaster', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 4)'),
(16, NULL, 1, 'Solar Striker', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 4)'),
(17, NULL, 2, 'Stormlord', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 6)'),
(18, NULL, 2, 'Might and Magic: Gates to Another World', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 6)'),
(19, NULL, 2, 'M-1 Abrams Battle Tank', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 6)'),
(20, NULL, 2, 'Thunder Force III', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 7)'),
(21, 1, 2, 'Sonic The Hedgehog', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 8)'),
(22, 1, 3, 'Sonic The Hedgehog', NULL, NULL, 12, 1991, NULL, 'Revista Hobby Consolas Nº1 (página 8)'),
(23, 1, 4, 'Sonic The Hedgehog', NULL, NULL, 12, 1991, NULL, 'Revista Hobby Consolas Nº1 (página 8)'),
(24, NULL, 2, 'Centurion: Defender of Rome', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 8)'),
(25, NULL, 3, 'Back to the Future Part II', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 8)'),
(26, NULL, 2, 'Back to the Future Part III', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 8)'),
(27, 55, 2, 'Xenon 2: Megablast', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 8)'),
(28, NULL, 2, 'Speedball 2: Brutal Deluxe', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 8)'),
(29, 5, 3, 'Pac-Mania', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 9)'),
(30, 8, 3, 'Populous', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 9)'),
(31, NULL, 3, 'Shadow of the Beast', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 9)'),
(32, NULL, 5, 'The Simpsons: Bart vs. the Space Mutants', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 14)'),
(33, NULL, 1, 'Bart Simpson\'s Escape from Camp Deadly', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 16)'),
(34, NULL, 5, 'Blue Shadow', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 22)'),
(35, 3, 3, 'Forgotten Worlds', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 24)'),
(36, NULL, 5, 'Shadow Warriors', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 26)'),
(37, NULL, 5, 'Super Spike V\'Ball', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 28)'),
(38, 33, 1, 'Batman: The Video Game', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 30)'),
(39, 20, 3, 'G-LOC: Air Battle', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 31)'),
(40, NULL, 2, 'John Madden Football', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 32)'),
(41, 2, 1, 'DuckTales', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 34)'),
(42, NULL, 6, 'A.P.B.', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 35)'),
(43, NULL, 3, 'Spider-Man', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 36)'),
(44, NULL, 6, 'Pac-Land', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 38)'),
(45, 40, 1, 'Ghostbusters II', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 39)'),
(46, NULL, 3, 'Speedball', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 40)'),
(47, NULL, 6, 'Ninja Gaiden', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 42)'),
(48, 16, 4, 'Wonder Boy', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 43)'),
(49, 4, 4, 'Super Monaco GP', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 44)'),
(50, NULL, 1, 'Motocross Maniacs', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 45)'),
(51, 39, 2, 'Blockout', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 46)'),
(52, NULL, 4, 'Shinobi', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 56)'),
(53, NULL, 6, 'Checkered Flag', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 57)'),
(54, NULL, 1, 'Teenage Mutant Ninja Turtles: Fall of the Foot Clan', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 58)'),
(55, NULL, 3, 'After Burner', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 58)'),
(56, NULL, 5, 'Bad Dudes', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 58)'),
(57, 25, 5, 'Bubble Bobble', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 58)'),
(58, 22, 2, 'Castle of Illusion Starring Mickey Mouse', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 58)'),
(59, NULL, 2, 'After Burner II', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 60)'),
(60, NULL, 3, 'Bomber Raid', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 60)'),
(61, NULL, 6, 'Electrocop', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 60)'),
(62, 2, 5, 'DuckTales', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 60)'),
(63, 6, 3, 'Gain Ground', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 60)'),
(64, 32, 5, 'Double Dragon II: The Revenge', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 62)'),
(65, NULL, 3, 'Altered Beast', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 62)'),
(66, NULL, 1, 'Pipe Dream', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 62)'),
(67, 14, 2, 'Michael\'s Jackson Moonwalker', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 62)'),
(68, NULL, 3, 'Aztec Adventure', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 62)'),
(69, 13, 2, 'Ghouls\'n Ghosts', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 64)'),
(70, 7, 3, 'Golden Axe', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 64)'),
(71, 42, 2, 'Shadow Dancer: The Secret of Shinobi', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 64)'),
(72, NULL, 2, 'Truxton', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 64)'),
(73, NULL, 3, 'Rastan', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 64)'),
(74, NULL, 5, 'Super Mario Bros. 3', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 65)'),
(75, NULL, 3, 'Choplifter', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 66)'),
(76, 44, 3, 'Enduro Racer', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 66)'),
(77, NULL, 5, 'Probotector', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 66)'),
(78, NULL, 2, 'The Revenge of Shinobi', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 66)'),
(79, 48, 5, 'Solomon\'s Key', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 66)'),
(80, NULL, 2, 'Arnold Palmer Tournament Golf', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 66)'),
(81, 12, 2, 'Ghostbusters', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 67)'),
(82, NULL, 5, 'Rad Racer', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 67)'),
(83, 26, 3, 'Double Dragon', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 67)'),
(84, 3, 2, 'Forgotten Worlds', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 67)'),
(85, 4, 2, 'Super Monaco GP', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 67)'),
(86, NULL, 5, 'Solar Jetman: Hunt for the Golden Warpship', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 67)'),
(87, NULL, 5, 'Super Mario Bros.', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 68)'),
(88, 37, 6, 'Rygar', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 69)'),
(89, 11, 2, 'Dynamite Duke', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 69)'),
(90, NULL, 5, 'Donkey Kong', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 71)'),
(91, 5, 6, 'Pac-Mania', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 72)'),
(92, 21, 3, 'Joe Montana Football', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 73)'),
(93, NULL, 2, 'Arrow Flash', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 74)'),
(94, NULL, 5, 'Double Dribble', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 74)'),
(95, NULL, 5, 'Gradius', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 75)'),
(96, NULL, 2, 'Zoom!', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 76)'),
(97, NULL, 5, 'Ghosts\'n Goblins', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 78)'),
(98, NULL, 3, 'Shanghai', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 79)'),
(99, NULL, 5, 'Teenage Mutant Ninja Turtles', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 80)'),
(100, 6, 2, 'Gain Ground', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 81)'),
(101, 29, 3, 'R-Type', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 81)'),
(102, NULL, 5, 'Goal!', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 82)'),
(103, 28, 3, 'Paper Boy', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 82)'),
(104, NULL, 3, 'Gauntlet', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 86)'),
(105, NULL, 2, '688 Attack Sub', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(106, 54, 2, 'Alien Storm', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(107, NULL, 2, 'James \"Buster\" Douglas Knockout Boxing', NULL, NULL, NULL, NULL, '6990 ₧', 'Revista Hobby Consolas Nº1 (página 87)'),
(108, NULL, 2, 'Battle Squadron', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(109, NULL, 2, 'Bonanza Bros.', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(110, NULL, 2, 'Budokan: The Martial Spirit', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(111, 9, 2, 'Columns', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(112, NULL, 2, 'Cyberball', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(113, NULL, 2, 'DJ Boy', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(114, NULL, 2, 'Decap Attack', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(115, 10, 2, 'Dick Tracy', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(116, NULL, 2, 'ESWAT: City Under Siege', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(117, NULL, 2, 'The Faery Tale Adventure', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(118, NULL, 2, 'Fantasia', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(119, NULL, 2, 'Fatal Labyrinth', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(120, NULL, 2, 'Fire Shark', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(121, NULL, 2, 'Flicky', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(122, 7, 2, 'Golden Axe', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(123, 41, 2, 'Hard Drivin\'', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(124, NULL, 2, 'Hellfire', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(125, NULL, 2, 'James Pond: Underwater Agent', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(126, NULL, 2, 'Fatal Rewind', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(127, NULL, 2, 'King\'s Bounty: The Conqueror\'s Quest', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(128, 30, 2, 'Klax', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(129, NULL, 2, 'Lakers versus Celtics and the NBA Playoffs', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(130, NULL, 2, 'Last Battle', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(131, NULL, 2, 'PGA Tour Golf', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(132, NULL, 2, 'Phantasy Star II', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(133, NULL, 2, 'Phelios', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(134, 8, 2, 'Populous', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(135, 15, 2, 'Rambo III', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(136, NULL, 2, 'Road Rash', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(137, NULL, 2, 'Sagaia', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(138, NULL, 2, 'Space Harrier II', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(139, NULL, 2, 'Starflight', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(140, NULL, 2, 'Streets of Rage', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(141, 38, 2, 'Strider', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(142, NULL, 2, 'Super Hang-On', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(143, NULL, 2, 'Super League', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(144, NULL, 2, 'Super Real Basketball', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(145, NULL, 2, 'Super Thunder Blade', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(146, NULL, 2, 'Sword of Sodan', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(147, NULL, 2, 'Sword of Vermilion', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(148, NULL, 2, 'Thunder Force II', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(149, NULL, 2, 'Twin Hawk', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(150, 17, 2, 'Wonder Boy III: The Dragons Trap', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(151, 46, 2, 'World Cup Italia \'90', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(152, NULL, 2, 'Zany Golf', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(153, NULL, 3, 'Ace of Aces', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(154, NULL, 3, 'Action Fighter', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(155, NULL, 3, 'Alex Kidd in Shinobi World', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(156, NULL, 3, 'Alex Kidd: High-Tech World', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(157, NULL, 3, 'Alien Syndrome', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(158, NULL, 3, 'American Pro Football', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(159, NULL, 3, 'Basketball Nightmare', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(160, NULL, 3, 'Battle Out Run', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(161, NULL, 3, 'Black Belt', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(162, 43, 3, 'California Games', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(163, NULL, 3, 'Where in the World is Carmen Sandiego?', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(164, NULL, 3, 'Casino Games', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(165, 18, 3, 'Chase H.Q.', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(166, 9, 3, 'Columns', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(167, NULL, 3, 'The Cyber Shinobi', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(168, NULL, 3, 'Dead Angle', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(169, 10, 3, 'Dick Tracy', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(170, NULL, 3, 'Double Hawk', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(171, 11, 3, 'Dynamite Duke', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(172, NULL, 3, 'Dynamite Dux', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(173, NULL, 3, 'Cyber Police ESWAT', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(174, 44, 7, 'Enduro Racer', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(175, NULL, 3, 'F-16 Fighter', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(176, 19, 3, 'Fantasy Zone', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(177, NULL, 3, 'Galaxy Force', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(178, 12, 3, 'Ghostbusters', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(179, NULL, 3, 'Ghost house', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(180, 13, 3, 'Ghouls\'n Ghosts', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(181, NULL, 3, 'Global Defense', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(182, NULL, 3, 'Golfamania', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(183, NULL, 3, 'Golvellius: Valley of Doom', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(184, NULL, 3, 'Great Volleyball', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(185, 49, 3, 'Impossible Mission', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(186, NULL, 3, 'Danan: The Jungle Fighter', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(187, NULL, 3, 'Maze Hunter 3-D', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(188, 14, 3, 'Michael\'s Jackson Moonwalker', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(189, NULL, 3, 'My Hero', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(190, NULL, 3, 'Operation Wolf', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(191, 23, 3, 'Out Run', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(192, NULL, 3, 'Parlour Games', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(193, NULL, 3, 'Phantasy Star', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(194, NULL, 3, 'Pro Wrestling', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(195, 24, 3, 'Psychic World', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(196, NULL, 3, 'Psycho Fox', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(197, NULL, 3, 'Quartet', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(198, NULL, 3, 'R.C. Grand Prix', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(199, 15, 3, 'Rambo III', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(200, 31, 3, 'Rampage', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(201, NULL, 3, 'Rescue Mission', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(202, NULL, 3, 'Scramble Spirits', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(203, NULL, 3, 'Secret Command', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(204, NULL, 3, 'Slap Shot', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(205, NULL, 3, 'Space Harrier', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(206, NULL, 3, 'Submarine Attack', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(207, NULL, 3, 'Summer Games', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(208, 4, 3, 'Super Monaco GP', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(209, NULL, 3, 'Super Tennis', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(210, NULL, 3, 'Teddy Boy', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(211, NULL, 3, 'The Ninja', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(212, NULL, 3, 'Thunder Blade', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(213, NULL, 3, 'Transbot', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(214, NULL, 3, 'Vigilante', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(215, 16, 3, 'Wonder Boy', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(216, 17, 3, 'Wonder Boy III: The Dragons Trap', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(217, NULL, 3, 'Wonder Boy in Monster World', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(218, NULL, 3, 'World Grand Prix', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(219, NULL, 3, 'World Soccer', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(220, 9, 4, 'Columns', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(221, 18, 4, 'Chase H.Q.', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(222, NULL, 4, 'Dragon Crystal', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(223, NULL, 4, 'Factory Panic', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(224, 19, 4, 'Fantasy Zone', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(225, 20, 4, 'G-LOC: Air Battle', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(226, 21, 4, 'Joe Montana Football', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(227, 22, 3, 'Castle of Illusion Starring Mickey Mouse', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(228, 22, 4, 'Castle of Illusion Starring Mickey Mouse', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(229, 23, 4, 'Out Run', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(230, 27, 4, 'Pac-Man', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(231, 24, 4, 'Psychic World', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(232, NULL, 4, 'Putt & Putter', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(233, NULL, 4, 'Super Golf', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(234, NULL, 1, 'Amazing Penguin', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(235, 25, 1, 'Bubble Bobble', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(236, NULL, 1, 'Bugs Bunny: Crazy Castle', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(237, NULL, 1, 'Castlevania: The Adventure', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(238, 18, 1, 'Chase H.Q.', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(239, NULL, 1, 'The Chessmaster', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(240, 26, 1, 'Double Dragon', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(241, 52, 1, 'Dragon\'s Lair: The Legend', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(242, NULL, 1, 'Fist of the North Star: 10 Big Brawls for the King of the Universe', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(243, NULL, 1, 'Gremlins 2: The New Batch', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(244, NULL, 1, 'In Your Face', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(245, NULL, 1, 'Kung Fu Master', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(246, NULL, 1, 'Nemesis', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(247, NULL, 1, 'Ninja Boy', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(248, 27, 1, 'Pac-Man', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(249, 28, 1, 'Paper Boy', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(250, NULL, 1, 'Penguin Wars', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(251, NULL, 1, 'Qix', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(252, NULL, 1, 'The Hunt for Red October', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(253, NULL, 1, 'RoboCop', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(254, NULL, 1, 'Skate or Die: Bad\'n Rad', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(255, NULL, 1, 'Snoopy\'s Magic Show', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(256, NULL, 1, 'Balloon Kid', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(257, NULL, 1, 'Burger Time DELUXE', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(258, NULL, 1, 'Mickey Mouse', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(259, NULL, 1, 'Navy Seals', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(260, 29, 1, 'R-Type', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(261, NULL, 1, 'Spud\'s Adventure', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(262, NULL, 6, 'Chip\'s Challenge', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(263, NULL, 6, 'Gates of Zendocon', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(264, NULL, 6, 'Gauntlet: The Third Encounter', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(265, 30, 6, 'Klax', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(266, 28, 6, 'Paper Boy', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(267, 31, 6, 'Rampage', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(268, NULL, 6, 'Road Blasters', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(269, NULL, 6, 'Slime World', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(270, NULL, 6, 'Xenophobe', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 87)'),
(271, NULL, 3, 'Tennis Ace', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 89)'),
(272, NULL, 1, 'Choplifter II: Rescue Survive', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 90)'),
(273, 32, 7, 'Double Dragon II', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 91)'),
(274, 33, 5, 'Batman: The Video Game', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº1 (página 94)'),
(275, 34, 4, 'Space Harrier', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 7)'),
(276, NULL, 4, 'Halley Wars', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 7)'),
(277, NULL, 4, 'Slider', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 7)'),
(278, 35, 4, 'Ninja Gaiden', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 7)'),
(279, NULL, 4, 'Kinetic Connection', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 7)'),
(280, NULL, 4, 'Woody Pop', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 7)'),
(281, NULL, 4, 'Pengo', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 7)'),
(282, NULL, 2, 'QuackShot', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 7)'),
(283, NULL, 1, 'Double Dragon II', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 8)'),
(284, NULL, 5, 'Double Dragon III', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 8)'),
(285, NULL, 5, 'Trog', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 8)'),
(286, 36, 5, 'Terminator 2: Judgment Day', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 13)'),
(287, 36, 1, 'Terminator 2: Judgment Day', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 13)'),
(288, NULL, 5, 'Kung Fu', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 19)'),
(289, NULL, 5, 'Airwolf', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 19)'),
(290, NULL, 5, 'Super Mario Bros. 2', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 19)'),
(291, NULL, 5, 'Zelda II: The Adventure of Link', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 19)'),
(292, NULL, 5, 'Mega Man 2', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 19)'),
(293, 37, 5, 'Rygar', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 19)'),
(294, 38, 7, 'Strider', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 26)'),
(295, 39, 6, 'Blockout', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 34)'),
(296, NULL, 5, 'Star Wars', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 39)'),
(297, NULL, 2, 'EA Hockey', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 40)'),
(298, 53, 3, 'Super Kick Off', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 44)'),
(299, NULL, 6, 'Warbirds', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 46)'),
(300, NULL, 5, 'Jack Nicklaus', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº2 (página 48)'),
(301, NULL, 6, 'Blue Lightning', NULL, NULL, NULL, NULL, NULL, ''),
(302, NULL, 6, 'Ms. Pac-Man', NULL, NULL, NULL, NULL, NULL, ''),
(303, NULL, 5, 'Adventures of Lolo', NULL, NULL, NULL, NULL, NULL, ''),
(304, NULL, 5, 'Adventures of Lolo 2', NULL, NULL, NULL, NULL, NULL, ''),
(305, NULL, 5, 'Adventures of Lolo 3', NULL, NULL, NULL, NULL, NULL, ''),
(306, NULL, 5, 'The Goonies', NULL, NULL, NULL, NULL, NULL, ''),
(307, NULL, 5, 'The Goonies II', NULL, NULL, NULL, NULL, NULL, ''),
(308, NULL, 6, 'Robo-Squash', NULL, NULL, NULL, NULL, NULL, ''),
(309, NULL, 6, 'Zarlor Mercenary', NULL, NULL, NULL, NULL, NULL, ''),
(310, 38, 3, 'Strider', NULL, NULL, NULL, NULL, NULL, ''),
(311, 40, 5, 'Ghostbusters II', NULL, NULL, NULL, NULL, NULL, ''),
(312, NULL, 5, 'Top Gun', NULL, NULL, NULL, NULL, NULL, ''),
(313, NULL, 3, 'Indiana Jones and the Last Crusade: The Action Game', NULL, NULL, NULL, NULL, NULL, ''),
(314, NULL, 2, 'ToeJam & Earl', NULL, NULL, NULL, NULL, NULL, ''),
(315, NULL, 5, 'Gauntlet II', NULL, NULL, NULL, NULL, NULL, ''),
(316, 41, 6, 'Hard Drivin\'', NULL, NULL, NULL, NULL, NULL, ''),
(317, NULL, 3, 'World Class Leader Board', NULL, NULL, NULL, NULL, NULL, ''),
(318, NULL, 5, 'Days of Thunder', NULL, NULL, NULL, NULL, NULL, ''),
(319, NULL, 6, 'Turbo Sub', NULL, NULL, NULL, NULL, NULL, ''),
(320, NULL, 5, 'Blades of Steel', NULL, NULL, NULL, NULL, NULL, ''),
(321, NULL, 3, 'Fire & Forget II', NULL, NULL, NULL, NULL, NULL, ''),
(322, 23, 2, 'Out Run', NULL, NULL, NULL, NULL, NULL, ''),
(323, NULL, 5, 'Al Unser Jr.\'s Turbo Racing', NULL, NULL, NULL, NULL, NULL, ''),
(324, NULL, 3, 'Out Run Europa', NULL, NULL, NULL, NULL, NULL, ''),
(325, NULL, 5, 'WWF WrestleMania', NULL, NULL, NULL, NULL, NULL, ''),
(326, NULL, 6, 'S.T.U.N. Runner', NULL, NULL, NULL, NULL, NULL, ''),
(327, NULL, 1, 'WWF Superstars', NULL, NULL, NULL, NULL, NULL, ''),
(328, NULL, 2, 'Turrican', NULL, NULL, NULL, NULL, NULL, ''),
(329, 42, 3, 'Shadow Dancer: The Secret of Shinobi', NULL, NULL, NULL, NULL, NULL, ''),
(330, NULL, 6, 'Viking Child', NULL, NULL, NULL, NULL, NULL, ''),
(331, NULL, 5, 'Blaster Master', NULL, NULL, NULL, NULL, NULL, ''),
(332, 43, 6, 'California Games', NULL, NULL, NULL, NULL, NULL, ''),
(333, NULL, 5, 'Life Force', NULL, NULL, NULL, NULL, NULL, ''),
(334, NULL, 5, 'Hook', NULL, NULL, NULL, NULL, NULL, ''),
(335, 45, 5, 'Nintendo World Cup', NULL, NULL, NULL, NULL, NULL, ''),
(336, NULL, 5, 'Kick Off', NULL, NULL, NULL, NULL, NULL, ''),
(337, NULL, 5, 'Soccer', NULL, NULL, NULL, NULL, NULL, ''),
(338, 46, 3, 'World Cup Italia \'90', NULL, NULL, NULL, NULL, NULL, ''),
(339, NULL, 5, 'World Cup Soccer', NULL, NULL, NULL, NULL, NULL, ''),
(340, NULL, 5, 'Super Off Road', NULL, NULL, NULL, NULL, NULL, ''),
(341, NULL, 5, 'IronSword: Wizards & Warriors II', NULL, NULL, NULL, NULL, NULL, ''),
(342, 47, 2, 'Mercs', NULL, NULL, NULL, NULL, NULL, ''),
(343, 47, 3, 'Mercs', NULL, NULL, NULL, NULL, NULL, ''),
(344, 48, 1, 'Solomon\'s Key', NULL, NULL, NULL, NULL, NULL, ''),
(345, NULL, 6, 'Tournament Cyberball', NULL, NULL, NULL, NULL, NULL, ''),
(346, NULL, 1, 'NBA All-Star Challenge', NULL, NULL, NULL, NULL, NULL, ''),
(347, NULL, 6, 'Awesome Golf', NULL, NULL, NULL, NULL, NULL, ''),
(348, NULL, 5, 'Total Recall', NULL, NULL, NULL, NULL, NULL, ''),
(349, NULL, 3, 'Asterix', NULL, NULL, NULL, NULL, NULL, ''),
(350, NULL, 5, 'RoboCop 2', NULL, NULL, NULL, NULL, NULL, ''),
(351, NULL, 2, 'Crack Down', NULL, NULL, NULL, NULL, NULL, ''),
(352, NULL, 2, 'Technocop', NULL, NULL, NULL, NULL, NULL, ''),
(353, NULL, 5, 'Power Blade', NULL, NULL, NULL, NULL, NULL, ''),
(354, 49, 5, 'Impossible Mission', NULL, NULL, NULL, NULL, NULL, ''),
(355, 51, 3, 'The Lucky Dime Caper starring Donald Duck', NULL, NULL, NULL, NULL, NULL, ''),
(356, NULL, 6, 'Scrapyard Dog', NULL, NULL, NULL, NULL, NULL, ''),
(357, NULL, 2, 'Mario Lemieux Hockey', NULL, NULL, NULL, NULL, NULL, ''),
(358, NULL, 1, 'The Hunt for Red October', NULL, NULL, NULL, NULL, NULL, ''),
(359, NULL, 5, 'Captain Skyhawk', NULL, NULL, NULL, NULL, NULL, ''),
(360, NULL, 5, 'Snake Rattle N Roll', NULL, NULL, NULL, NULL, NULL, ''),
(361, NULL, 6, 'Basketbrawl', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 6)'),
(362, NULL, 2, 'Slaughter Sports', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 6)'),
(363, NULL, 1, 'Runes of Virtue', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 6)'),
(364, NULL, 1, 'The Rescue of Princess Blobette', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 7)'),
(365, 50, 3, 'Super Smash TV', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 8)'),
(366, 50, 2, 'Super Smash TV', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 8)'),
(367, NULL, 2, 'Blood Money', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 8)'),
(368, 36, 2, 'Terminator 2: Judgment Day', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 8)'),
(369, NULL, 5, 'The Little Mermaid', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 9)'),
(370, NULL, 1, 'Pit-Fighter', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 9)'),
(371, NULL, 2, 'The Inmortal', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 10)'),
(372, NULL, 2, 'Rings of Power', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 10)'),
(373, NULL, 5, 'Jackie Chan\'s Action Kung Fu', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 10)'),
(374, 30, 7, 'Klax', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 11)'),
(375, NULL, 2, 'Kid Chameleon', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 22)'),
(376, NULL, 5, 'The New Zealand Story', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 28)'),
(377, NULL, 2, 'James Pond II: Codename Robocod', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 34)'),
(378, NULL, 5, 'Bubble Bobble Part 2', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 40)'),
(379, NULL, 5, 'Willow', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 46)'),
(380, NULL, 1, 'Elevator Action', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 50)'),
(381, NULL, 6, 'Bill & Ted\'s Excellent Adventure', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 52)'),
(382, NULL, 5, 'Mega Man 3', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 62)'),
(383, 51, 4, 'The Lucky Dime Caper starring Donald Duck', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 64)'),
(384, NULL, 3, 'The Flintstones', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 70)'),
(385, NULL, 3, 'Laser Ghost', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 72)'),
(386, 43, 2, 'California Games', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 78)'),
(387, NULL, 1, 'Fortified Zone', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 88)'),
(388, NULL, 2, 'Joe Montana II: Sports Talk Football', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 90)'),
(389, 52, 5, 'Dragon\'s Lair: The Legend', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 92)'),
(390, NULL, 6, 'Xybots', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 94)'),
(391, 53, 1, 'Super Kick Off', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 98)'),
(392, 54, 3, 'Alien Storm', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 100)'),
(393, 55, 3, 'Xenon 2: Megablast', NULL, NULL, NULL, NULL, NULL, 'Revista Hobby Consolas Nº7 (página 161)');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `game_company`
--

CREATE TABLE `game_company` (
  `game` bigint(20) UNSIGNED NOT NULL,
  `company` int(10) UNSIGNED NOT NULL,
  `category` enum('PUBLISHER','DEVELOPER') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `game_metadata`
--

CREATE TABLE `game_metadata` (
  `game` bigint(20) UNSIGNED NOT NULL,
  `metadata` int(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `game_metadata`
--

INSERT INTO `game_metadata` (`game`, `metadata`) VALUES
(8, 3),
(20, 6),
(21, 3),
(21, 5),
(21, 7),
(22, 3),
(22, 5),
(22, 7),
(23, 3),
(23, 5),
(23, 7),
(42, 3),
(42, 4),
(55, 6),
(57, 3),
(57, 7),
(59, 6),
(65, 8),
(80, 10),
(105, 2),
(105, 4),
(106, 8),
(136, 4),
(136, 5),
(136, 13),
(145, 6),
(148, 6),
(153, 2),
(153, 3),
(154, 6),
(155, 7),
(156, 7),
(157, 9),
(158, 10),
(212, 6),
(234, 3),
(235, 3),
(235, 7),
(289, 6),
(296, 3),
(296, 7),
(303, 1),
(304, 1),
(305, 1),
(313, 3),
(313, 7),
(313, 11),
(318, 4),
(318, 5),
(323, 4),
(323, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `game_score`
--

CREATE TABLE `game_score` (
  `member` bigint(20) UNSIGNED NOT NULL,
  `game` bigint(20) UNSIGNED NOT NULL,
  `score` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `language`
--

CREATE TABLE `language` (
  `tag` char(5) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `language`
--

INSERT INTO `language` (`tag`, `name`) VALUES
('en_EN', 'English'),
('es_ES', 'Español');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login_history`
--

CREATE TABLE `login_history` (
  `login_ip` varbinary(8) NOT NULL,
  `login_member` varchar(32) NOT NULL,
  `login_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `login_result` bit(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `media`
--

CREATE TABLE `media` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` int(10) UNSIGNED NOT NULL,
  `country` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `media`
--

INSERT INTO `media` (`id`, `name`, `type`, `country`) VALUES
(1, 'Hobby Consolas', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `media_score`
--

CREATE TABLE `media_score` (
  `media` int(10) UNSIGNED NOT NULL,
  `game` bigint(20) UNSIGNED NOT NULL,
  `score` tinyint(1) UNSIGNED NOT NULL,
  `description` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `media_score`
--

INSERT INTO `media_score` (`media`, `game`, `score`, `description`) VALUES
(1, 4, 90, 'Revista Hobby Consolas Nº1'),
(1, 5, 65, 'Revista Hobby Consolas Nº5'),
(1, 6, 90, 'Revista Hobby Consolas Nº3'),
(1, 7, 74, 'Revista Hobby Consolas Nº2'),
(1, 8, 79, 'Revista Hobby Consolas Nº7'),
(1, 9, 80, 'Revista Hobby Consolas Nº1'),
(1, 10, 87, 'Revista Hobby Consolas Nº5'),
(1, 12, 88, 'Revista Hobby Consolas Nº4'),
(1, 14, 88, 'Revista Hobby Consolas Nº5'),
(1, 15, 61, 'Revista Hobby Consolas Nº7'),
(1, 16, 77, 'Revista Hobby Consolas Nº3'),
(1, 20, 95, 'Revista Hobby Consolas Nº2'),
(1, 21, 95, 'Revista Hobby Consolas Nº1'),
(1, 22, 93, 'Revista Hobby Consolas Nº2'),
(1, 23, 94, 'Revista Hobby Consolas Nº6'),
(1, 29, 88, 'Revista Hobby Consolas Nº1'),
(1, 30, 92, 'Revista Hobby Consolas Nº3'),
(1, 31, 90, 'Revista Hobby Consolas Nº5'),
(1, 32, 85, 'Revista Hobby Consolas Nº2'),
(1, 33, 83, 'Revista Hobby Consolas Nº2'),
(1, 34, 91, 'Revista Hobby Consolas Nº1'),
(1, 35, 88, 'Revista Hobby Consolas Nº1'),
(1, 36, 89, 'Revista Hobby Consolas Nº1'),
(1, 37, 84, 'Revista Hobby Consolas Nº1'),
(1, 38, 90, 'Revista Hobby Consolas Nº1'),
(1, 39, 75, 'Revista Hobby Consolas Nº1'),
(1, 40, 86, 'Revista Hobby Consolas Nº1'),
(1, 41, 86, 'Revista Hobby Consolas Nº1'),
(1, 42, 80, 'Revista Hobby Consolas Nº1'),
(1, 42, 83, 'Revista Hobby Consolas Nº6'),
(1, 43, 75, 'Revista Hobby Consolas Nº1'),
(1, 44, 84, 'Revista Hobby Consolas Nº1'),
(1, 45, 91, 'Revista Hobby Consolas Nº1'),
(1, 46, 76, 'Revista Hobby Consolas Nº1'),
(1, 47, 85, 'Revista Hobby Consolas Nº1'),
(1, 48, 89, 'Revista Hobby Consolas Nº1'),
(1, 49, 87, 'Revista Hobby Consolas Nº1'),
(1, 50, 70, 'Revista Hobby Consolas Nº1'),
(1, 51, 92, 'Revista Hobby Consolas Nº1'),
(1, 52, 92, 'Revista Hobby Consolas Nº1'),
(1, 53, 91, 'Revista Hobby Consolas Nº1'),
(1, 54, 85, 'Revista Hobby Consolas Nº1'),
(1, 57, 87, 'Revista Hobby Consolas Nº1'),
(1, 59, 92, 'Revista Hobby Consolas Nº1'),
(1, 61, 84, 'Revista Hobby Consolas Nº1'),
(1, 61, 86, 'Revista Hobby Consolas Nº6'),
(1, 64, 82, 'Revista Hobby Consolas Nº3'),
(1, 65, 45, 'Revista Hobby Consolas Nº6'),
(1, 67, 78, 'Revista Hobby Consolas Nº5'),
(1, 69, 93, 'Revista Hobby Consolas Nº5'),
(1, 71, 90, 'Revista Hobby Consolas Nº3'),
(1, 72, 94, 'Revista Hobby Consolas Nº1'),
(1, 73, 82, 'Revista Hobby Consolas Nº1'),
(1, 74, 93, 'Revista Hobby Consolas Nº2'),
(1, 76, 83, 'Revista Hobby Consolas Nº4'),
(1, 84, 79, 'Revista Hobby Consolas Nº4'),
(1, 85, 91, 'Revista Hobby Consolas Nº3'),
(1, 86, 55, 'Revista Hobby Consolas Nº7'),
(1, 87, 87, 'Revista Hobby Consolas Nº1'),
(1, 88, 85, 'Revista Hobby Consolas Nº1'),
(1, 89, 73, 'Revista Hobby Consolas Nº1'),
(1, 90, 80, 'Revista Hobby Consolas Nº1'),
(1, 92, 75, 'Revista Hobby Consolas Nº1'),
(1, 93, 85, 'Revista Hobby Consolas Nº1'),
(1, 94, 65, 'Revista Hobby Consolas Nº1'),
(1, 95, 85, 'Revista Hobby Consolas Nº1'),
(1, 96, 70, 'Revista Hobby Consolas Nº1'),
(1, 97, 70, 'Revista Hobby Consolas Nº1'),
(1, 98, 70, 'Revista Hobby Consolas Nº1'),
(1, 99, 86, 'Revista Hobby Consolas Nº1'),
(1, 100, 66, 'Revista Hobby Consolas Nº1'),
(1, 101, 85, 'Revista Hobby Consolas Nº1'),
(1, 102, 69, 'Revista Hobby Consolas Nº1'),
(1, 102, 60, 'Revista Hobby Consolas Nº5'),
(1, 103, 87, 'Revista Hobby Consolas Nº1'),
(1, 104, 88, 'Revista Hobby Consolas Nº1'),
(1, 107, 88, 'Revista Hobby Consolas Nº2'),
(1, 108, 83, 'Revista Hobby Consolas Nº2'),
(1, 109, 89, 'Revista Hobby Consolas Nº4'),
(1, 114, 85, 'Revista Hobby Consolas Nº5'),
(1, 115, 85, 'Revista Hobby Consolas Nº4'),
(1, 115, 79, 'Revista Hobby Consolas Nº6'),
(1, 116, 78, 'Revista Hobby Consolas Nº6'),
(1, 118, 94, 'Revista Hobby Consolas Nº3'),
(1, 122, 90, 'Revista Hobby Consolas Nº6'),
(1, 124, 94, 'Revista Hobby Consolas Nº7'),
(1, 126, 70, 'Revista Hobby Consolas Nº7'),
(1, 128, 86, 'Revista Hobby Consolas Nº7'),
(1, 136, 91, 'Revista Hobby Consolas Nº3'),
(1, 140, 90, 'Revista Hobby Consolas Nº2'),
(1, 140, 90, 'Revista Hobby Consolas Nº6'),
(1, 141, 83, 'Revista Hobby Consolas Nº6'),
(1, 145, 75, 'Revista Hobby Consolas Nº6'),
(1, 151, 83, 'Revista Hobby Consolas Nº5'),
(1, 152, 91, 'Revista Hobby Consolas Nº3'),
(1, 165, 65, 'Revista Hobby Consolas Nº6'),
(1, 168, 60, 'Revista Hobby Consolas Nº6'),
(1, 169, 60, 'Revista Hobby Consolas Nº6'),
(1, 173, 50, 'Revista Hobby Consolas Nº6'),
(1, 177, 75, 'Revista Hobby Consolas Nº2'),
(1, 180, 87, 'Revista Hobby Consolas Nº3'),
(1, 185, 63, 'Revista Hobby Consolas Nº6'),
(1, 186, 77, 'Revista Hobby Consolas Nº5'),
(1, 206, 68, 'Revista Hobby Consolas Nº2'),
(1, 207, 59, 'Revista Hobby Consolas Nº6'),
(1, 214, 71, 'Revista Hobby Consolas Nº5'),
(1, 219, 87, 'Revista Hobby Consolas Nº5'),
(1, 222, 78, 'Revista Hobby Consolas Nº2'),
(1, 223, 86, 'Revista Hobby Consolas Nº4'),
(1, 226, 83, 'Revista Hobby Consolas Nº5'),
(1, 228, 90, 'Revista Hobby Consolas Nº5'),
(1, 229, 86, 'Revista Hobby Consolas Nº3'),
(1, 231, 75, 'Revista Hobby Consolas Nº2'),
(1, 232, 70, 'Revista Hobby Consolas Nº5'),
(1, 237, 74, 'Revista Hobby Consolas Nº7'),
(1, 238, 59, 'Revista Hobby Consolas Nº6'),
(1, 240, 89, 'Revista Hobby Consolas Nº6'),
(1, 240, 80, 'Revista Hobby Consolas Nº7'),
(1, 241, 73, 'Revista Hobby Consolas Nº6'),
(1, 243, 90, 'Revista Hobby Consolas Nº2'),
(1, 249, 62, 'Revista Hobby Consolas Nº3'),
(1, 253, 84, 'Revista Hobby Consolas Nº4'),
(1, 253, 84, 'Revista Hobby Consolas Nº6'),
(1, 259, 82, 'Revista Hobby Consolas Nº7'),
(1, 262, 91, 'Revista Hobby Consolas Nº6'),
(1, 263, 86, 'Revista Hobby Consolas Nº2'),
(1, 264, 85, 'Revista Hobby Consolas Nº7'),
(1, 265, 90, 'Revista Hobby Consolas Nº5'),
(1, 266, 82, 'Revista Hobby Consolas Nº5'),
(1, 267, 80, 'Revista Hobby Consolas Nº3'),
(1, 268, 84, 'Revista Hobby Consolas Nº1'),
(1, 269, 80, 'Revista Hobby Consolas Nº3'),
(1, 271, 60, 'Revista Hobby Consolas Nº1'),
(1, 272, 86, 'Revista Hobby Consolas Nº3'),
(1, 275, 87, 'Revista Hobby Consolas Nº3'),
(1, 276, 84, 'Revista Hobby Consolas Nº6'),
(1, 277, 74, 'Revista Hobby Consolas Nº7'),
(1, 278, 82, 'Revista Hobby Consolas Nº7'),
(1, 280, 88, 'Revista Hobby Consolas Nº4'),
(1, 282, 94, 'Revista Hobby Consolas Nº5'),
(1, 289, 50, 'Revista Hobby Consolas Nº6'),
(1, 290, 88, 'Revista Hobby Consolas Nº5'),
(1, 295, 86, 'Revista Hobby Consolas Nº2'),
(1, 296, 96, 'Revista Hobby Consolas Nº4'),
(1, 297, 93, 'Revista Hobby Consolas Nº2'),
(1, 298, 90, 'Revista Hobby Consolas Nº2'),
(1, 298, 80, 'Revista Hobby Consolas Nº5'),
(1, 299, 93, 'Revista Hobby Consolas Nº2'),
(1, 300, 63, 'Revista Hobby Consolas Nº2'),
(1, 301, 93, 'Revista Hobby Consolas Nº3'),
(1, 310, 73, 'Revista Hobby Consolas Nº2'),
(1, 311, 88, 'Revista Hobby Consolas Nº2'),
(1, 312, 76, 'Revista Hobby Consolas Nº2'),
(1, 313, 95, 'Revista Hobby Consolas Nº2'),
(1, 314, 87, 'Revista Hobby Consolas Nº4'),
(1, 315, 92, 'Revista Hobby Consolas Nº3'),
(1, 316, 89, 'Revista Hobby Consolas Nº3'),
(1, 317, 88, 'Revista Hobby Consolas Nº3'),
(1, 318, 75, 'Revista Hobby Consolas Nº3'),
(1, 319, 86, 'Revista Hobby Consolas Nº3'),
(1, 320, 87, 'Revista Hobby Consolas Nº3'),
(1, 321, 68, 'Revista Hobby Consolas Nº3'),
(1, 322, 92, 'Revista Hobby Consolas Nº4'),
(1, 323, 91, 'Revista Hobby Consolas Nº4'),
(1, 324, 85, 'Revista Hobby Consolas Nº4'),
(1, 325, 82, 'Revista Hobby Consolas Nº4'),
(1, 326, 85, 'Revista Hobby Consolas Nº4'),
(1, 327, 90, 'Revista Hobby Consolas Nº4'),
(1, 328, 86, 'Revista Hobby Consolas Nº4'),
(1, 329, 73, 'Revista Hobby Consolas Nº4'),
(1, 330, 85, 'Revista Hobby Consolas Nº4'),
(1, 331, 91, 'Revista Hobby Consolas Nº4'),
(1, 332, 89, 'Revista Hobby Consolas Nº4'),
(1, 333, 90, 'Revista Hobby Consolas Nº4'),
(1, 335, 60, 'Revista Hobby Consolas Nº5'),
(1, 336, 80, 'Revista Hobby Consolas Nº6'),
(1, 337, 74, 'Revista Hobby Consolas Nº5'),
(1, 338, 72, 'Revista Hobby Consolas Nº5'),
(1, 339, 86, 'Revista Hobby Consolas Nº5'),
(1, 340, 85, 'Revista Hobby Consolas Nº5'),
(1, 341, 71, 'Revista Hobby Consolas Nº5'),
(1, 342, 90, 'Revista Hobby Consolas Nº5'),
(1, 343, 90, 'Revista Hobby Consolas Nº5'),
(1, 344, 88, 'Revista Hobby Consolas Nº5'),
(1, 345, 58, 'Revista Hobby Consolas Nº5'),
(1, 346, 87, 'Revista Hobby Consolas Nº5'),
(1, 347, 83, 'Revista Hobby Consolas Nº5'),
(1, 348, 77, 'Revista Hobby Consolas Nº5'),
(1, 349, 87, 'Revista Hobby Consolas Nº6'),
(1, 350, 84, 'Revista Hobby Consolas Nº6'),
(1, 351, 65, 'Revista Hobby Consolas Nº6'),
(1, 352, 70, 'Revista Hobby Consolas Nº6'),
(1, 353, 74, 'Revista Hobby Consolas Nº6'),
(1, 354, 88, 'Revista Hobby Consolas Nº6'),
(1, 355, 80, 'Revista Hobby Consolas Nº6'),
(1, 356, 87, 'Revista Hobby Consolas Nº6'),
(1, 357, 79, 'Revista Hobby Consolas Nº6'),
(1, 358, 69, 'Revista Hobby Consolas Nº6'),
(1, 359, 86, 'Revista Hobby Consolas Nº6'),
(1, 360, 89, 'Revista Hobby Consolas Nº6'),
(1, 375, 94, 'Revista Hobby Consolas Nº7'),
(1, 376, 93, 'Revista Hobby Consolas Nº7'),
(1, 377, 95, 'Revista Hobby Consolas Nº7'),
(1, 378, 87, 'Revista Hobby Consolas Nº7'),
(1, 379, 75, 'Revista Hobby Consolas Nº7'),
(1, 380, 70, 'Revista Hobby Consolas Nº7'),
(1, 381, 69, 'Revista Hobby Consolas Nº7'),
(1, 382, 93, 'Revista Hobby Consolas Nº7'),
(1, 383, 84, 'Revista Hobby Consolas Nº7'),
(1, 384, 75, 'Revista Hobby Consolas Nº7'),
(1, 385, 79, 'Revista Hobby Consolas Nº7'),
(1, 386, 82, 'Revista Hobby Consolas Nº7'),
(1, 387, 83, 'Revista Hobby Consolas Nº7'),
(1, 388, 77, 'Revista Hobby Consolas Nº7'),
(1, 389, 87, 'Revista Hobby Consolas Nº7'),
(1, 390, 66, 'Revista Hobby Consolas Nº7'),
(1, 391, 65, 'Revista Hobby Consolas Nº7'),
(1, 392, 62, 'Revista Hobby Consolas Nº7'),
(1, 393, 66, 'Revista Hobby Consolas Nº7');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `media_type`
--

CREATE TABLE `media_type` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `media_type`
--

INSERT INTO `media_type` (`id`, `name`) VALUES
(2, 'Página Web'),
(1, 'Revista');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `member`
--

CREATE TABLE `member` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(128) NOT NULL,
  `email` varchar(100) NOT NULL,
  `salt` varchar(16) NOT NULL,
  `language` char(5) NOT NULL DEFAULT 'es_ES',
  `myprofile_visivility` enum('ALL','FRIENDS','NONE') NOT NULL DEFAULT 'ALL',
  `myprofile_publish` enum('ALL','FRIENDS','NONE') NOT NULL DEFAULT 'ALL',
  `sendme_messages` enum('ALL','FRIENDS','NONE') NOT NULL DEFAULT 'ALL',
  `sendme_requests` bit(1) NOT NULL DEFAULT b'1',
  `inbox_max` tinyint(3) UNSIGNED NOT NULL DEFAULT 25,
  `comments_number` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `posts_number` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `account_state` enum('ACTIVED','BANNED','DEACTIVATED','LOCKED') NOT NULL DEFAULT 'DEACTIVATED',
  `account_group` enum('USER','WRITER','MODERATOR','MANAGER','ADMIN') NOT NULL DEFAULT 'USER',
  `account_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `activation_code` varchar(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `member`
--

INSERT INTO `member` (`id`, `username`, `password`, `email`, `salt`, `language`, `myprofile_visivility`, `myprofile_publish`, `sendme_messages`, `sendme_requests`, `inbox_max`, `comments_number`, `posts_number`, `account_state`, `account_group`, `account_creation`, `last_login`, `activation_code`) VALUES
(1, 'admin', 'b58942d6f806e7a9df2ab0b722128cd469f117e62ac6137117cc545438103f03', 'admin@admin.com', 'M61Qclk5B819dZ10', 'es_ES', 'FRIENDS', 'FRIENDS', 'FRIENDS', b'1', 25, 0, 0, 'ACTIVED', 'ADMIN', '2023-04-17 08:40:42', '2024-12-31 08:56:49', NULL),
(2, 'user1', 'b58942d6f806e7a9df2ab0b722128cd469f117e62ac6137117cc545438103f03', 'user1@user.com', 'M61Qclk5B819dZ10', 'es_ES', 'ALL', 'ALL', 'ALL', b'1', 25, 0, 0, 'ACTIVED', 'USER', '2023-04-20 11:31:09', '2023-04-20 11:31:09', NULL),
(3, 'user2', 'b58942d6f806e7a9df2ab0b722128cd469f117e62ac6137117cc545438103f03', 'user2@user.com', 'M61Qclk5B819dZ10', 'es_ES', 'ALL', 'ALL', 'ALL', b'1', 25, 0, 0, 'ACTIVED', 'USER', '2023-04-20 11:31:09', '2023-04-20 11:31:09', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `member_game`
--

CREATE TABLE `member_game` (
  `member` bigint(20) UNSIGNED NOT NULL,
  `game` bigint(20) UNSIGNED NOT NULL,
  `category` enum('COLLECTION','FAVORITE','WISHLIST') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `member_message`
--

CREATE TABLE `member_message` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `member_from` bigint(20) UNSIGNED NOT NULL,
  `member_to` bigint(20) UNSIGNED NOT NULL,
  `subject` varchar(100) NOT NULL,
  `message` text NOT NULL DEFAULT '',
  `send_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` bit(1) NOT NULL DEFAULT b'0',
  `is_read` bit(1) NOT NULL DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `member_relation`
--

CREATE TABLE `member_relation` (
  `member1` bigint(20) UNSIGNED NOT NULL,
  `member2` bigint(20) UNSIGNED NOT NULL,
  `state` enum('FRIENDSHIP','LOCKED','REQUEST') NOT NULL DEFAULT 'REQUEST',
  `relation_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `member_report`
--

CREATE TABLE `member_report` (
  `id` int(10) UNSIGNED NOT NULL,
  `message_id` bigint(20) UNSIGNED NOT NULL,
  `message_content` text NOT NULL,
  `manager_member` bigint(20) UNSIGNED DEFAULT NULL,
  `informer_member` bigint(20) UNSIGNED NOT NULL,
  `reported_member` bigint(20) UNSIGNED NOT NULL,
  `state` enum('APPROVED','PENDING','REJECTED') NOT NULL DEFAULT 'PENDING',
  `type` enum('COMMENT','POST') NOT NULL,
  `sending_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metadata`
--

CREATE TABLE `metadata` (
  `id` int(10) UNSIGNED NOT NULL,
  `value` varchar(200) NOT NULL,
  `group` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `metadata`
--

INSERT INTO `metadata` (`id`, `value`, `group`) VALUES
(3, 'Acción', 1),
(11, 'Aventuras', 1),
(8, 'Beat \'Em Up', 1),
(4, 'Conducción', 1),
(10, 'Deportes', 1),
(13, 'Lucha', 1),
(7, 'Plataformas', 1),
(1, 'Puzle', 1),
(12, 'Rail shooter', 1),
(9, 'Run and gun', 1),
(6, 'Shoot \'Em Up', 1),
(2, 'Simulador', 1),
(5, 'Velocidad', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metagroup`
--

CREATE TABLE `metagroup` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `tag` varchar(100) NOT NULL,
  `relevance` tinyint(2) UNSIGNED NOT NULL,
  `infotype` enum('TEXT','PNG') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `metagroup`
--

INSERT INTO `metagroup` (`id`, `name`, `tag`, `relevance`, `infotype`) VALUES
(1, 'Géneros', 'genres', 100, 'TEXT'),
(2, 'Número de jugadores', 'players', 25, 'TEXT'),
(3, 'Calificación de contenido', 'content-rating', 25, 'PNG'),
(4, 'Idioma de los textos', 'text-lang', 25, 'TEXT'),
(5, 'Idioma de las voces', 'sound-lang', 25, 'TEXT'),
(6, 'Temáticas', 'themes', 50, 'TEXT');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `page`
--

CREATE TABLE `page` (
  `path` varchar(200) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL DEFAULT '',
  `published` timestamp NOT NULL DEFAULT current_timestamp(),
  `author` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `page`
--

INSERT INTO `page` (`path`, `title`, `content`, `published`, `author`) VALUES
('cookies', 'Cookies', 'Página en construcción...', '2025-01-05 08:04:29', 1),
('policies', 'Políticas de privacidad', 'Página en construcción...', '2025-01-05 08:04:29', 1),
('rules', 'Normas del sitio', 'Página en construcción...', '2025-01-05 08:04:29', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `platform`
--

CREATE TABLE `platform` (
  `id` int(10) UNSIGNED NOT NULL,
  `tag` char(3) NOT NULL,
  `name` varchar(50) NOT NULL,
  `release_date` date DEFAULT NULL,
  `colour` varchar(20) DEFAULT NULL,
  `featured` bit(1) NOT NULL DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `platform`
--

INSERT INTO `platform` (`id`, `tag`, `name`, `release_date`, `colour`, `featured`) VALUES
(1, 'ngb', 'Game Boy', NULL, 'purple', b'1'),
(2, 'smd', 'Mega Drive', NULL, 'blue', b'1'),
(3, 'sms', 'Master System', NULL, 'blue', b'0'),
(4, 'sgg', 'Game Gear', NULL, 'blue', b'1'),
(5, 'nes', 'Nintendo Entertainment System', NULL, 'red', b'0'),
(6, 'lnx', 'Lynx', NULL, 'yellow', b'0'),
(7, 'arc', 'Arcade', NULL, 'orange', b'1'),
(8, 'sne', 'Super Nintendo', NULL, 'red', b'1'),
(9, 'tgx', 'Turbografx', NULL, 'yellow', b'1'),
(10, 'sng', 'Neo-Geo', NULL, 'yellow', b'1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `security_token`
--

CREATE TABLE `security_token` (
  `client_ip` varbinary(16) NOT NULL,
  `client_browser_hash` varchar(128) NOT NULL,
  `content_hash` varchar(128) NOT NULL,
  `expiration_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `request_page` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `visitor`
--

CREATE TABLE `visitor` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_ip` varbinary(16) NOT NULL,
  `client_browser_hash` varchar(128) NOT NULL,
  `visit_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `request_page` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `achievement`
--
ALTER TABLE `achievement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `achievement_fk-author` (`author`),
  ADD KEY `achievement_fk-game` (`game`);

--
-- Indices de la tabla `cheat`
--
ALTER TABLE `cheat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cheat_fk-author` (`author`),
  ADD KEY `cheat_fk-game` (`game`);

--
-- Indices de la tabla `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `company_uk-name` (`name`);

--
-- Indices de la tabla `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `country_uk-iso_code` (`iso_code`),
  ADD UNIQUE KEY `country_uk-name` (`name`);

--
-- Indices de la tabla `entry`
--
ALTER TABLE `entry`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entry_fk-author` (`author`),
  ADD KEY `entry_fk-game` (`game`),
  ADD KEY `entry_fk-platform` (`platform`);

--
-- Indices de la tabla `entry_comment`
--
ALTER TABLE `entry_comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entry_comment_fk-entry` (`entry`),
  ADD KEY `entry_comment_fk-author` (`author`);

--
-- Indices de la tabla `forum`
--
ALTER TABLE `forum`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `forum_uk-title-category` (`title`,`category`),
  ADD KEY `forum_fk-category` (`category`),
  ADD KEY `forum_fk-parent_forum` (`parent_forum`);

--
-- Indices de la tabla `forum_category`
--
ALTER TABLE `forum_category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `forum_category_uk-title` (`title`);

--
-- Indices de la tabla `forum_post`
--
ALTER TABLE `forum_post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `forum_post_fk-author` (`author`),
  ADD KEY `forum_post_fk-forum` (`forum`),
  ADD KEY `forum_post_fk-topic` (`topic`);

--
-- Indices de la tabla `game`
--
ALTER TABLE `game`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `game_uk-game-gamegroup-platform` (`gamegroup`,`platform`),
  ADD KEY `game_fk-platform` (`platform`);

--
-- Indices de la tabla `game_company`
--
ALTER TABLE `game_company`
  ADD PRIMARY KEY (`game`,`company`,`category`),
  ADD KEY `game_company_fk-company` (`company`);

--
-- Indices de la tabla `game_metadata`
--
ALTER TABLE `game_metadata`
  ADD PRIMARY KEY (`game`,`metadata`),
  ADD KEY `game_metadata_fk-metadata` (`metadata`);

--
-- Indices de la tabla `game_score`
--
ALTER TABLE `game_score`
  ADD PRIMARY KEY (`member`,`game`),
  ADD KEY `game_score_fk-game` (`game`);

--
-- Indices de la tabla `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`tag`),
  ADD UNIQUE KEY `language_uk-name` (`name`);

--
-- Indices de la tabla `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `media_type_uk-name` (`name`),
  ADD KEY `media_type_fk-type` (`type`),
  ADD KEY `media_type_fk-country` (`country`);

--
-- Indices de la tabla `media_score`
--
ALTER TABLE `media_score`
  ADD PRIMARY KEY (`media`,`game`,`description`) USING BTREE,
  ADD KEY `media_score_fk-game` (`game`);

--
-- Indices de la tabla `media_type`
--
ALTER TABLE `media_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `media_type_uk-name` (`name`);

--
-- Indices de la tabla `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `member_uk-username` (`username`),
  ADD UNIQUE KEY `member_uk-email` (`email`),
  ADD KEY `member_fk-language` (`language`);

--
-- Indices de la tabla `member_game`
--
ALTER TABLE `member_game`
  ADD PRIMARY KEY (`member`,`game`,`category`),
  ADD KEY `member_game_fk-game` (`game`);

--
-- Indices de la tabla `member_message`
--
ALTER TABLE `member_message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_message_fk-member_from` (`member_from`),
  ADD KEY `member_message_fk-member_to` (`member_to`);

--
-- Indices de la tabla `member_relation`
--
ALTER TABLE `member_relation`
  ADD PRIMARY KEY (`member1`,`member2`),
  ADD KEY `member_relation_fk-member2` (`member2`);

--
-- Indices de la tabla `member_report`
--
ALTER TABLE `member_report`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `member_report_uk-message_id-informer_member-reported_member-type` (`message_id`,`informer_member`,`reported_member`,`type`),
  ADD KEY `member_report_fk-manager_member` (`manager_member`),
  ADD KEY `member_report_fk-informer_member` (`informer_member`),
  ADD KEY `member_report_fk-reported_member` (`reported_member`);

--
-- Indices de la tabla `metadata`
--
ALTER TABLE `metadata`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `metadata_uk-value-group` (`value`,`group`),
  ADD KEY `metadata_fk-group` (`group`);

--
-- Indices de la tabla `metagroup`
--
ALTER TABLE `metagroup`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `metagroup_uk-name` (`name`),
  ADD UNIQUE KEY `metagroup_uk-tag` (`tag`);

--
-- Indices de la tabla `page`
--
ALTER TABLE `page`
  ADD PRIMARY KEY (`path`),
  ADD KEY `page_fk-author` (`author`);

--
-- Indices de la tabla `platform`
--
ALTER TABLE `platform`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `platform_uk-tag` (`tag`),
  ADD UNIQUE KEY `platform_uk-name` (`name`);

--
-- Indices de la tabla `security_token`
--
ALTER TABLE `security_token`
  ADD PRIMARY KEY (`client_ip`,`request_page`);

--
-- Indices de la tabla `visitor`
--
ALTER TABLE `visitor`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `achievement`
--
ALTER TABLE `achievement`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cheat`
--
ALTER TABLE `cheat`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `entry`
--
ALTER TABLE `entry`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `game`
--
ALTER TABLE `game`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=442;

--
-- AUTO_INCREMENT de la tabla `member_message`
--
ALTER TABLE `member_message`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `member_report`
--
ALTER TABLE `member_report`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `metadata`
--
ALTER TABLE `metadata`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `metagroup`
--
ALTER TABLE `metagroup`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `platform`
--
ALTER TABLE `platform`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `achievement`
--
ALTER TABLE `achievement`
  ADD CONSTRAINT `achievement_fk-author` FOREIGN KEY (`author`) REFERENCES `member` (`id`),
  ADD CONSTRAINT `achievement_fk-game` FOREIGN KEY (`game`) REFERENCES `game` (`id`);

--
-- Filtros para la tabla `cheat`
--
ALTER TABLE `cheat`
  ADD CONSTRAINT `cheat_fk-author` FOREIGN KEY (`author`) REFERENCES `member` (`id`),
  ADD CONSTRAINT `cheat_fk-game` FOREIGN KEY (`game`) REFERENCES `game` (`id`);

--
-- Filtros para la tabla `entry`
--
ALTER TABLE `entry`
  ADD CONSTRAINT `entry_fk-author` FOREIGN KEY (`author`) REFERENCES `member` (`id`),
  ADD CONSTRAINT `entry_fk-game` FOREIGN KEY (`game`) REFERENCES `game` (`id`),
  ADD CONSTRAINT `entry_fk-platform` FOREIGN KEY (`platform`) REFERENCES `platform` (`id`);

--
-- Filtros para la tabla `entry_comment`
--
ALTER TABLE `entry_comment`
  ADD CONSTRAINT `entry_comment_fk-author` FOREIGN KEY (`author`) REFERENCES `member` (`id`),
  ADD CONSTRAINT `entry_comment_fk-entry` FOREIGN KEY (`entry`) REFERENCES `entry` (`id`);

--
-- Filtros para la tabla `forum`
--
ALTER TABLE `forum`
  ADD CONSTRAINT `forum_fk-category` FOREIGN KEY (`category`) REFERENCES `forum_category` (`id`),
  ADD CONSTRAINT `forum_fk-parent_forum` FOREIGN KEY (`parent_forum`) REFERENCES `forum` (`id`);

--
-- Filtros para la tabla `forum_post`
--
ALTER TABLE `forum_post`
  ADD CONSTRAINT `forum_post_fk-author` FOREIGN KEY (`author`) REFERENCES `member` (`id`),
  ADD CONSTRAINT `forum_post_fk-forum` FOREIGN KEY (`forum`) REFERENCES `forum` (`id`),
  ADD CONSTRAINT `forum_post_fk-topic` FOREIGN KEY (`topic`) REFERENCES `forum_post` (`id`);

--
-- Filtros para la tabla `game`
--
ALTER TABLE `game`
  ADD CONSTRAINT `game_fk-platform` FOREIGN KEY (`platform`) REFERENCES `platform` (`id`);

--
-- Filtros para la tabla `game_company`
--
ALTER TABLE `game_company`
  ADD CONSTRAINT `game_company_fk-company` FOREIGN KEY (`company`) REFERENCES `company` (`id`),
  ADD CONSTRAINT `game_company_fk-game` FOREIGN KEY (`game`) REFERENCES `game` (`id`);

--
-- Filtros para la tabla `game_metadata`
--
ALTER TABLE `game_metadata`
  ADD CONSTRAINT `game_metadata_fk-metadata` FOREIGN KEY (`metadata`) REFERENCES `metadata` (`id`);

--
-- Filtros para la tabla `game_score`
--
ALTER TABLE `game_score`
  ADD CONSTRAINT `game_score_fk-game` FOREIGN KEY (`game`) REFERENCES `game` (`id`),
  ADD CONSTRAINT `game_score_fk-member` FOREIGN KEY (`member`) REFERENCES `member` (`id`);

--
-- Filtros para la tabla `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `media_type_fk-country` FOREIGN KEY (`country`) REFERENCES `country` (`id`),
  ADD CONSTRAINT `media_type_fk-type` FOREIGN KEY (`type`) REFERENCES `media_type` (`id`);

--
-- Filtros para la tabla `media_score`
--
ALTER TABLE `media_score`
  ADD CONSTRAINT `media_score_fk-game` FOREIGN KEY (`game`) REFERENCES `game` (`id`),
  ADD CONSTRAINT `media_score_fk-media` FOREIGN KEY (`media`) REFERENCES `media` (`id`);

--
-- Filtros para la tabla `member`
--
ALTER TABLE `member`
  ADD CONSTRAINT `member_fk-language` FOREIGN KEY (`language`) REFERENCES `language` (`tag`);

--
-- Filtros para la tabla `member_game`
--
ALTER TABLE `member_game`
  ADD CONSTRAINT `member_game_fk-game` FOREIGN KEY (`game`) REFERENCES `game` (`id`),
  ADD CONSTRAINT `member_game_fk-member` FOREIGN KEY (`member`) REFERENCES `member` (`id`);

--
-- Filtros para la tabla `member_message`
--
ALTER TABLE `member_message`
  ADD CONSTRAINT `member_message_fk-member_from` FOREIGN KEY (`member_from`) REFERENCES `member` (`id`),
  ADD CONSTRAINT `member_message_fk-member_to` FOREIGN KEY (`member_to`) REFERENCES `member` (`id`);

--
-- Filtros para la tabla `member_relation`
--
ALTER TABLE `member_relation`
  ADD CONSTRAINT `member_relation_fk-member1` FOREIGN KEY (`member1`) REFERENCES `member` (`id`),
  ADD CONSTRAINT `member_relation_fk-member2` FOREIGN KEY (`member2`) REFERENCES `member` (`id`);

--
-- Filtros para la tabla `member_report`
--
ALTER TABLE `member_report`
  ADD CONSTRAINT `member_report_fk-informer_member` FOREIGN KEY (`informer_member`) REFERENCES `member` (`id`),
  ADD CONSTRAINT `member_report_fk-manager_member` FOREIGN KEY (`manager_member`) REFERENCES `member` (`id`),
  ADD CONSTRAINT `member_report_fk-reported_member` FOREIGN KEY (`reported_member`) REFERENCES `member` (`id`);

--
-- Filtros para la tabla `metadata`
--
ALTER TABLE `metadata`
  ADD CONSTRAINT `metadata_fk-group` FOREIGN KEY (`group`) REFERENCES `metagroup` (`id`);

--
-- Filtros para la tabla `page`
--
ALTER TABLE `page`
  ADD CONSTRAINT `page_fk-author` FOREIGN KEY (`author`) REFERENCES `member` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
