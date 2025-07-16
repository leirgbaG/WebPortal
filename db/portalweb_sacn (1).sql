-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 31-01-2025 a las 00:11:22
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
-- Base de datos: `portalweb_sacn`
--
CREATE DATABASE IF NOT EXISTS `portalweb_sacn` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `portalweb_sacn`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accion`
--

CREATE TABLE `accion` (
  `id_accion` int(11) NOT NULL,
  `fecha_act` date NOT NULL,
  `hora_act` time NOT NULL,
  `accion_name` varchar(100) NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `accion`
--

INSERT INTO `accion` (`id_accion`, `fecha_act`, `hora_act`, `accion_name`, `id_user`) VALUES
(1, '2025-01-29', '22:30:00', 'Crear Usuario', 1),
(2, '2025-01-29', '23:01:00', 'Publicar Noticia', 1),
(3, '2025-01-29', '23:07:00', 'Publicar Noticia', 1),
(4, '2025-01-29', '23:09:00', 'Publicar Noticia', 1),
(5, '2025-01-29', '23:10:00', 'Publicar Noticia', 1),
(6, '2025-01-29', '23:12:00', 'Publicar Noticia', 1),
(7, '2025-01-29', '23:13:00', 'Publicar Noticia', 1),
(8, '2025-01-29', '23:14:00', 'Publicar Noticia', 1),
(9, '2025-01-29', '23:15:00', 'Publicar Noticia', 1),
(10, '2025-01-29', '23:17:00', 'Publicar Noticia', 1),
(11, '2025-01-29', '23:23:00', 'Publicar Noticia', 1),
(12, '2025-01-29', '23:24:00', 'Publicar Noticia', 1),
(13, '2025-01-29', '23:26:00', 'Publicar Noticia', 1),
(14, '2025-01-29', '23:27:00', 'Publicar Noticia', 1),
(15, '2025-01-29', '23:28:00', 'Publicar Noticia', 1),
(16, '2025-01-29', '23:30:00', 'Publicar Noticia', 1),
(17, '2025-01-29', '23:32:00', 'Publicar Noticia', 1),
(18, '2025-01-30', '01:13:00', 'Publicar Libro', 1),
(19, '2025-01-30', '01:19:00', 'Publicar Libro', 1),
(20, '2025-01-30', '01:23:00', 'Publicar Libro', 1),
(21, '2025-01-30', '01:30:00', 'Publicar Libro', 1),
(22, '2025-01-30', '01:41:00', 'Publicar Libro', 1),
(23, '2025-01-30', '01:51:00', 'Publicar Libro', 1),
(24, '2025-01-30', '01:52:00', 'Publicar Libro', 1),
(25, '2025-01-30', '10:48:00', 'Publicar Noticia', 1),
(26, '2025-01-30', '11:24:00', 'Borrar Noticia', 1),
(27, '2025-01-30', '11:55:00', 'Borrar Noticia', 1),
(28, '2025-01-30', '12:03:00', 'Crear Usuario', 2),
(29, '2025-01-30', '12:04:00', 'Bloquear Usuario', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `autor`
--

CREATE TABLE `autor` (
  `id_autor` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `surname` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `autor`
--

INSERT INTO `autor` (`id_autor`, `name`, `surname`) VALUES
(1, 'Eduardo', 'González'),
(2, 'Yuliangel', 'Marcano'),
(3, 'Yulyannys', 'Cedeño'),
(4, 'Fabiana', 'Bouttó'),
(5, 'Departamento ', 'SACN');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `autor_documento`
--

CREATE TABLE `autor_documento` (
  `id_autordoc` int(11) NOT NULL,
  `id_autor` int(11) DEFAULT NULL,
  `id_doc` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `autor_documento`
--

INSERT INTO `autor_documento` (`id_autordoc`, `id_autor`, `id_doc`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 1, 2),
(5, 2, 2),
(6, 3, 2),
(7, 1, 3),
(8, 2, 3),
(9, 3, 3),
(10, 4, 4),
(11, 5, 5),
(12, 5, 6),
(13, 5, 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargo`
--

CREATE TABLE `cargo` (
  `id_cargo` int(11) NOT NULL,
  `name_cargo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cargo`
--

INSERT INTO `cargo` (`id_cargo`, `name_cargo`) VALUES
(1, 'Programador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clasificacion`
--

CREATE TABLE `clasificacion` (
  `id_clasf` int(11) NOT NULL,
  `name_clasf` varchar(50) NOT NULL,
  `estado_clasf` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clasificacion`
--

INSERT INTO `clasificacion` (`id_clasf`, `name_clasf`, `estado_clasf`) VALUES
(1, 'Proyecto Sociotecnológico', 0),
(2, 'Informe', 0),
(3, 'Organigrama', 0),
(4, 'Tríptico', 0),
(5, 'Pensum', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documento`
--

CREATE TABLE `documento` (
  `id_doc` int(11) NOT NULL,
  `ttle_doc` varchar(200) NOT NULL,
  `corp_doc` varchar(4000) NOT NULL,
  `docx` varchar(200) NOT NULL,
  `estado_doc` int(11) NOT NULL,
  `id_clasf` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `documento`
--

INSERT INTO `documento` (`id_doc`, `ttle_doc`, `corp_doc`, `docx`, `estado_doc`, `id_clasf`) VALUES
(1, 'Desarrollo De Un Portal Web DinÁmico Para El Departamento De Seguridad Alimentaria Y Cultura Nutricional De La Uptp “luis Mariano Rivera”,  NÚcleo CarÚpano.', 'A través del desarrollo e investigación de este Proyecto Sociotecnológico se descubrió una problemática en el Departamento de Seguridad Alimentaria y Cultura Nutricional de la UPTP “LMR”, definido como la divulgación manual de noticias y documentos, determinado por el uso de técnicas como Observación, Entrevistas y Encuestas. Debido a esto, se propone el desarrollo de un Portal Web a través de la metodología RUP, la cual consta de las Fases Inicio, Elaboración, Construcción y Transición; utilizando herramientas como HTML5, CSS3, JavaScript, PHP8 y MySQL. Esto permitió el desarrollo de un Portal Web con los módulos «Gestión de Noticias», «Gestión de Documentos» y «Gestión de Usuarios». Finalmente se desarrolló e implantó un plan de adiestramiento y un manual de usuario para los usuarios del sistema desarrollado. Esto permitió el correcto desarrollo e implantación de este Proyecto Sociotecnológico.', 'Upload/Documentos/1.pdf', 0, 1),
(2, 'Especificación De Requerimientos Del Software. Proyecto: Desarrollo De Un Portal Web Para El Departamento De Seguridad Alimentaria Y Cultura Nutricional, De La Uptp \"lmr\", Núcleo Carúpano', 'Se busca cumplir el desarrollo de un Portal Web para el Departamento de Seguridad Alimentaria y Cultura Nutricional, de la Universidad Politécnica Territorial de Paria “Luis Mariano Rivera”, Núcleo Carúpano. Será un portal web dinámico ya que cuenta con una base de datos que permita registrar los usuarios, subir contenido informativo, videos, noticias y libros relacionados al Departamento objeto de estudio.\r\nSe propone crear un espacio donde se interactúe con los usuarios, quienes se conforman por los docentes y coordinadores del departamento de Seguridad Alimentaria y Cultura Nutricional, ofreciendo a los estudiantes un acceso limitado al portal web. Los usuarios pueden realizar una serie de actividades como la descarga de documentos esenciales para el PNF, modificar o eliminar la información ya registrada de dichos documentos, registro e inicio de sesión de los usuarios, publicar noticias o información relevante', 'Upload/Documentos/2.pdf', 0, 1),
(3, 'Diccionario De Datos Del Proyecto Desarrollo De Un Portal Web DinÁmico Para El Departamento De Seguridad Alimentaria Y Cultura Nutricional De La Uptp “luis Mariano Rivera”,  NÚcleo CarÚpano.', 'Se realiza un sistema de base de datos para el portal web que funciona como periódico y biblioteca virtual, en el Departamento de Seguridad Alimentaria y Cultura Nutricional (SACN). El portal web es regido por los docentes y coordinadores del Departamento.', 'Upload/Documentos/3.pdf', 0, 1),
(4, 'Informe De Actividades', 'Resumen de las noticias publicadas en las redes.', 'Upload/Documentos/4.pdf', 0, 2),
(5, 'Organigrama Del Departamento De Seguridad Alimentaria Y Cultura Nutricional', 'Organigrama del Departamento de Seguridad Alimentaria y Cultura Nutricional', 'Upload/Documentos/5.pdf', 0, 3),
(6, 'Tríptico Informativo', 'El Programa Nacional de Formación en Seguridad Alimentaria y Cultura Nutricional, que se desarrolla en la Universidad Politécnica Territorial de Paria “Luis Mariano Rivera”, es el fruto de un trabajo conjunto entre el Ministerio del Poder Popular para la Alimentación, el Ministerio del Poder Popular para la Educación Universitaria, el Instituto Nacional de Nutrición, la Escuela Venezolana de Alimentación y Nutrición Bicentenario 5 de Julio y el Consejo Educativo Popular. Todos ellos se unieron con el objetivo de crear y llevar a cabo una propuesta que formara profesionales en área tan esencial para el desarrollo productivo de nuestro país. Los egresados están destinados a manejar la seguridad alimentaria, el estado nutricional de la población y la promoción de patrones de consumo saludables, seguros y soberanos. De esta manera, se busca asegurar el acceso oportuno a los alimentos y su adecuado procesamiento, teniendo en cuenta las particularidades del territorio y la preservación de nuestra rica herencia cultural relacionada con la comida, todo ello en línea con los principios y valores del concepto de \"Vivir Bien\".', 'Upload/Documentos/6.pdf', 0, 4),
(7, 'Pensum De Esutdios', 'PROGRAMA NACIONAL DE FORMACIÓN: LICENCIATURA EN SEGURIDAD ALIMENTARIA Y CULTURA NUTRICIONAL\r\nMALLA CURRICULAR: SEMESTRAL, VERSIÓN 1\r\nMENCIÓN ACADÉMICA: NO APLICA', 'Upload/Documentos/7.pdf', 0, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documento_palabraclave`
--

CREATE TABLE `documento_palabraclave` (
  `id_dockw` int(11) NOT NULL,
  `id_kw` int(11) NOT NULL,
  `id_doc` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `documento_palabraclave`
--

INSERT INTO `documento_palabraclave` (`id_dockw`, `id_kw`, `id_doc`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 1),
(5, 5, 1),
(6, 6, 1),
(7, 7, 1),
(8, 8, 1),
(9, 9, 1),
(10, 10, 1),
(11, 11, 1),
(12, 12, 1),
(13, 13, 2),
(14, 14, 3),
(15, 15, 4),
(16, 16, 4),
(17, 17, 4),
(18, 18, 5),
(19, 19, 6),
(20, 20, 6),
(21, 21, 7),
(22, 22, 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `multimedia`
--

CREATE TABLE `multimedia` (
  `id_media` int(11) NOT NULL,
  `media` varchar(200) DEFAULT NULL,
  `estado_media` tinyint(1) NOT NULL DEFAULT 0,
  `id_new` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `multimedia`
--

INSERT INTO `multimedia` (`id_media`, `media`, `estado_media`, `id_new`) VALUES
(1, 'Upload/Multimedia/1.mp4', 0, 1),
(2, 'Upload/Multimedia/2.mp4', 0, 2),
(3, 'Upload/Multimedia/3.mp4', 0, 3),
(4, 'Upload/Multimedia/4.mp4', 0, 4),
(5, 'Upload/Multimedia/5.png', 0, 5),
(6, 'Upload/Multimedia/6.png', 0, 5),
(7, 'Upload/Multimedia/7.png', 0, 6),
(8, 'Upload/Multimedia/8.jpg', 0, 7),
(9, 'Upload/Multimedia/9.jpg', 0, 7),
(10, 'Upload/Multimedia/10.jpg', 0, 7),
(11, 'Upload/Multimedia/11.jpg', 0, 7),
(12, 'Upload/Multimedia/12.png', 0, 7),
(13, 'Upload/Multimedia/13.jpg', 0, 8),
(14, 'Upload/Multimedia/14.jpg', 0, 8),
(15, 'Upload/Multimedia/15.jpg', 0, 9),
(16, 'Upload/Multimedia/16.jpg', 0, 9),
(17, 'Upload/Multimedia/17.jpg', 0, 9),
(18, 'Upload/Multimedia/18.jpg', 0, 9),
(19, 'Upload/Multimedia/19.jpg', 0, 9),
(20, 'Upload/Multimedia/20.jpg', 0, 9),
(21, 'Upload/Multimedia/21.png', 0, 10),
(22, 'Upload/Multimedia/22.jpg', 0, 11),
(23, 'Upload/Multimedia/23.jpg', 0, 11),
(24, 'Upload/Multimedia/24.jpg', 0, 11),
(25, 'Upload/Multimedia/25.jpg', 0, 12),
(26, 'Upload/Multimedia/26.jpg', 0, 12),
(27, 'Upload/Multimedia/27.jpg', 0, 13),
(28, 'Upload/Multimedia/28.jpg', 0, 13),
(29, 'Upload/Multimedia/29.png', 0, 13),
(30, 'Upload/Multimedia/30.jpg', 0, 13),
(31, 'Upload/Multimedia/31.jpg', 0, 13),
(32, 'Upload/Multimedia/32.jpg', 0, 13),
(33, 'Upload/Multimedia/33.jpg', 0, 13),
(34, 'Upload/Multimedia/34.png', 0, 14),
(35, 'Upload/Multimedia/35.png', 0, 14),
(36, 'Upload/Multimedia/36.png', 0, 14),
(37, 'Upload/Multimedia/37.png', 0, 14),
(38, 'Upload/Multimedia/38.jpg', 0, 15),
(39, 'Upload/Multimedia/39.jpg', 0, 16),
(40, 'Upload/Multimedia/40.jpg', 0, 16),
(41, 'Upload/Multimedia/41.jpg', 0, 17);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noticia`
--

CREATE TABLE `noticia` (
  `id_new` int(11) NOT NULL,
  `ttle_new` varchar(200) NOT NULL,
  `corp_new` varchar(4000) NOT NULL,
  `estado_new` tinyint(1) NOT NULL DEFAULT 1,
  `fecha_new` date NOT NULL,
  `hora_new` time NOT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `noticia`
--

INSERT INTO `noticia` (`id_new`, `ttle_new`, `corp_new`, `estado_new`, `fecha_new`, `hora_new`, `id_user`) VALUES
(1, 'Día de la Alimentación en Venezuela', 'Con el fin de promover una cultura alimentaria más saludable el PNF en Seguridad Alimentaria y Cultura Nutricional, llevó a cabo diversas actividades con motivo del \"Día de la Alimentación en Venezuela\", y en celebración de su aniversario N° 10. Con una grandiosa caminata, feria de la arepa nutricional y un panel de discusión con varios expertos, se unieron para sensibilizar a la población sobre la importancia de una alimentación orientada al B.E.S.O, es decir, balanceada, equilibrada, saludable y óptima, para una mejor calidad de vida.\r\n\r\n¡Comer es fundamental, nutrirse es un acto de amor propio!', 1, '2025-01-29', '23:01:00', 1),
(2, 'Estrategias que Promuevan una Alimentación Orientada al B.E.S.O. para una Mejor Vida', 'Panel de discusión \"Estrategias que Promuevan una Alimentación Orientada al B.E.S.O. para una Mejor Vida\"', 1, '2025-01-29', '23:07:00', 1),
(3, 'Día de la Alimentación en Venezuela', 'Actividades realizadas por el Día de la Alimentación en Venezuela, en la UPTP \"LMR\".', 1, '2025-01-29', '23:09:00', 1),
(4, 'Día de la Alimentación en Venezuela', 'Caminata por el Día de la Alimentación en Venezuela y actividades realizadas en la cancha techada de la UPTP \"LMR\".', 1, '2025-01-29', '23:10:00', 1),
(5, 'Día mundial de la actividad física.', 'Todos los 6 de abril de cada año, se conmemora a nivel global el Día Mundial de la Actividad Física. Esta celebración nace de una iniciativa de la Organización Mundial de la Salud (OMS) que, en 2002, y mediante una resolución, solicitó a sus estados miembro que fijaran una fecha especial para la promoción del ejercicio, también conocido como Move for Health (Muévete por la salud, en inglés), con el objetivo de conseguir un mejor estado de salud físico y mental, así como una mejor calidad de vida.\r\nPara la OMS, la actividad física significa “todos los movimientos que forman parte de la vida diaria, incluyendo el trabajo, la recreación, el ejercicio y las actividades deportivas”. Para esta organización, un nivel adecuado de actividad física regular en los adultos tiene amplios beneficios: reduce el riesgo de hipertensión, cardiopatía coronaria, ACV, diabetes, cáncer de mama y de colon, depresión; al tiempo que mejora la salud ósea y funcional, siendo un determinante clave del gasto energético, lo que lo hace fundamental para el equilibrio calórico y el control del peso.', 1, '2025-01-29', '23:12:00', 1),
(6, 'Día mundial de la salud.', 'El 7 de abril, celebramos el Día Mundial de la Salud, fecha conmemorativa de la fundación de la Organización Mundial de la Salud (OMS) en 1948. Este día simboliza el compromiso global con la promoción de la salud, la seguridad mundial y el apoyo a los más vulnerables, con el objetivo de que cada individuo, sin importar dónde se encuentre, pueda alcanzar el máximo nivel de salud y bienestar.\r\nCada año, se selecciona un lema específico con el objetivo de promover la conciencia y la acción en torno a un problema de salud particular.\r\nSegún el Consejo de la OMS sobre los Aspectos Económicos de la Salud para Todos, al menos 140 países reconocen la salud como un derecho humano en su constitución. Sin embargo, ninguno de ellos promulga ni aplica leyes que garanticen a sus poblaciones el derecho a acceder a los servicios de salud. Ello explica que al menos 4500 millones de personas (más de la mitad de la población mundial) no estuvieran plenamente cubiertas por servicios de salud esenciales en 2021.\r\nPara hacer frente a desafíos de este tipo, el lema del Día Mundial de la Salud 2024 es \"Mi salud, mi derecho\".\r\nEl lema de este año se eligió para defender el derecho de todas las personas, dondequiera que estén, a tener acceso a servicios de salud, a educación y a información de calidad, así como a agua potable, aire limpio, buena alimentación, vivienda de calidad, condiciones laborales y ambientales decentes, y a no ser discriminadas.\r\n¡Mi SALUD, MI DERECHO!', 1, '2025-01-29', '23:13:00', 1),
(7, 'Participación del PNF SACN, en las actividades de la semana aniversario de la U.P T.P \"Luis Mariano Rivera\".', 'El dia  9 de Abril el PNF de Seguridad Alimentaria y Cultura Nutricional participó en la semana universitaria de la UPTP Luis Mariano Rivera con su Stand representado por los alumnos del segundo trayecto acompañado de los profe Liliana y Luis Acosta y todo los demás docentes, obreros y administrativos de este emblemático PNF, en el Stand se preparó por parte de los estudiantes de este mencionado PNFSACN buñuelos de yuca, croquetas de yuca rellena con pescado guisado, azopado de mariscos y cazuela  de mariscos.', 1, '2025-01-29', '23:14:00', 1),
(8, 'Actividad para promover el PNF SACN', 'Se han llevado a cabo actividades en diferentes centros educativos con el fin de promocionar el PNF Seguridad Alimentaria y Cultura Nutricional. En esta oportunidad la Lic. Josefina Mendoza estuvo en la Unidad Educativa Bolivariana Petrica Reyes de  Quilarque y el Liceo Nacional Bolivariano Félix Melquíades Brito, Para cumplir con dicho propósito.\r\n🍊¡¡Los invitamos a formar parte del PNF SACN!!🍉', 1, '2025-01-29', '23:15:00', 1),
(9, 'Inicio de prácticas profesionales.', 'El día 07/05/2024 los estudiantes que están en el periodo de prácticas profesionales iniciaron esta importante etapa en la UEP \" Fray Bartolomé de Las Casas\", dónde los chicos pusieron de manifiesto los conocimientos adquiridos en su vida estudiantil, en lo relacionado con el Trompo de los Alimentos. Es importante resaltar que se contó con el apoyo de los docentes: Maribel Rodríguez, Milagros Lyon, Génesis Bastardo, Fabiana Bouttó y Steinbregner Rodríguez. De igual manera estuvieron presente los estudiantes de la sección 15. 🍉🍊🥑🍅🍍', 1, '2025-01-29', '23:17:00', 1),
(10, 'Clases magistrales', 'Desde el saber Experiencia de Integración Comunitaria en cumplimiento del Plan de Evaluación establecido, las secciones 16 y 17, en acompañamiento de sus facilitadoras la  Lcda. Milagros Lyon y  la Lcda. Neulis Serrano, llevaron a cabo los días 08 y 16 de mayo del presente año, las clases magistrales tituladas:\r\n1. Influencia de la cultura y la Sociedad en los Hábitos Alimentarios, a cargo Nutricionista Lcdo. Víctor Rodríguez. \r\n2. ¿Cómo el factor sociocultural influye en la cultura alimentaria? A cargo del Chef Internacional Lcdo. Luis Acosta. \r\nTodo esto con la finalidad de reforzar los conocimientos a los estudiantes de las secciones 15, 18 y 19 del PNF en Seguridad Alimentaria y Cultura Nutricional.', 1, '2025-01-29', '23:23:00', 1),
(11, 'Práctica gastronómica dirigida a futuros bachilleres del Liceo María de Vera.', 'El día 17/05/2024 en las instalaciones del PNFSACN, específicamente en el laboratorio de gastronomía, se llevó a cabo una práctica, la cual consistió en la elaboración de mortadela de pollo, siendo los participantes estudiantes aspirantes a bachilleres del liceo María de Vera de la comunidad de Charallave.', 1, '2025-01-29', '23:24:00', 1),
(12, 'Taller de formación a docentes.', 'Hoy viernes 17 de mayo se realizó un taller de Formación, dirigida a los docentes del PNF en SACN, para abordar temas relacionados con las Herramientas de Investigación acción Participativa (Matriz FODA, Árbol del Problema y de Objetivos).', 1, '2025-01-29', '23:26:00', 1),
(13, 'Práctica gastronomía típica de las regiones de Venezuela.', 'El día 21 del presente mes, se realizó una práctica gastronómica, en la cual los estudiantes de la sección \"15\", elaboraron patacones, uno de los platos autóctonos de la Región Zuliana. Con el fin de cumplir con la Unidad III Gastronomía típica de las regiones de Venezuela, correspondiente a la materia Diálogo entre Cultura Copular y Cultura Alimentaria. Dicha actividad estuvo a cargo de la coordinadora del saber Lic. Fabiana Bouttó, acompañados del profesor Lic. Luis Acosta.', 1, '2025-01-29', '23:27:00', 1),
(14, 'Participación del PNF SACN en la Expoferia \"Expo publi 2024\".', 'El día 22 de Mayo 2024. El PNF Seguridad Alimentaria y Cultura Nutricional, participó  en la Expoferia \"Expo publi 2024\" organizada por el departamento de mercadeo, específicamente por la carrera de publicidad, la cuál tuvo lugar en la UPTP \"Luis Mariano Rivera\", en las áreas de dicha carrera. Los estudiantes de la Sección 17, pertenecientes al trayecto II del PNF SACN, resaltaron con su arte culinario al preparar unas deliciosas tortillas de plátano y yuca, libres de gluten. Bajo la coordinación del profesor del saber \"Calidad e Inocuidad de los Alimentos\" Lcdo. Luis Acosta y la profesora Lcda. Olga Rendón. Felicitamos a los estudiantes por su destacada participación, logrando desenvolverse con seguridad al responder las inquietudes de las presentes relacionadas al consumo de alimentos sanos. Es importante mencionar, que los productos elaborados obtuvieron una notable aceptación por su agradable sabor y textura, captando la atención del público.', 1, '2025-01-29', '23:28:00', 1),
(15, 'Siembra de plantas de pimentón.', 'El día de hoy 29 de mayo, se realizó la siembra de plantas de pimentón en uno de los canteros del área productiva, que pertenece al PNF en Seguridad Alimentaria y Cultura Nutricional, con el asesoramiento del profesor Sergio Marcano, esta actividad es parte de la planificación del saber Determinantes sociales y ambientales que se imparte a la sección 15, semestre V del trayecto III.', 1, '2025-01-29', '23:30:00', 1),
(16, 'Actividades del saber Prácticas y Hábitos Saludables.', 'El Programa Nacional de Formación en Seguridad y Cultural Alimentaria y Cultura Nutricional sigue avanzando en pro de la excelencia educativa. En el mes de mayo los días 06 y 07 de 2024, los estudiantes de las secciones 16 y 17 de Saber: Prácticas y hábitos saludables, coordinado por la Licenciada María Fernanda Quintero, realizaron diversas actividades en el estacionamiento central de la UPTP \"Luis Mariano Rivera\" y en la cancha techada. Las actividades ejecutadas fueron las siguientes: Bailo terapia:  \"Bailando la vida es más saludable\". Charlas: \"Las frutas, el secreto para una vida saludable\" y \"El estrés y la salud mental\". Cartelera: Los beneficios de una alimentación saludable para la salud física y mental. Juegos Recreativos: las frutas un mundo de colores y sabores. Entrega de trípticos: \"Hábitos saludables\" y Volantes: Importancia de las frutas en la salud. De ese modo, incentivaron a la comunidad universitaria sobre la importancia de cultivar hábitos saludables para la prevención de enfermedades y lograr el bienestar físico, mental y social.', 1, '2025-01-29', '23:32:00', 1),
(17, 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Qui, aliquid omnis? Saepe rerum magni harum recusandae quisquam quaerat optio voluptatibus earum illum, laudantium ipsum suscipit nihil nisi i', 'sadsaasdasdsa', -1, '2025-01-30', '10:48:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `palabraclave`
--

CREATE TABLE `palabraclave` (
  `id_kw` int(11) NOT NULL,
  `name_kw` varchar(50) NOT NULL,
  `estado_pc` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `palabraclave`
--

INSERT INTO `palabraclave` (`id_kw`, `name_kw`, `estado_pc`) VALUES
(1, 'PortalWeb', 0),
(2, 'RUP', 0),
(3, 'HTML5', 0),
(4, 'CSS3', 0),
(5, 'JavaScript', 0),
(6, 'PHP8', 0),
(7, 'MySQL', 0),
(8, 'PeriódicoVirtual', 0),
(9, 'BibliotecaVirtual', 0),
(10, 'Observación', 0),
(11, 'Entrevista', 0),
(12, 'Encuesta', 0),
(13, 'DERS', 0),
(14, 'DiccionarioDeDatos', 0),
(15, 'Resumen', 0),
(16, 'Informe', 0),
(17, 'Comunicación', 0),
(18, 'Organigrama', 0),
(19, 'Tríptico', 0),
(20, 'Informativo', 0),
(21, 'Pensum', 0),
(22, 'SACN', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `peticion`
--

CREATE TABLE `peticion` (
  `id_pet` int(11) NOT NULL,
  `pet_ttle` varchar(200) NOT NULL,
  `pet_corp` varchar(4000) NOT NULL,
  `pet_fecha` date NOT NULL,
  `pet_estado` tinyint(1) NOT NULL,
  `pet_msg` varchar(400) DEFAULT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_user` int(11) NOT NULL,
  `usuario` varchar(16) NOT NULL,
  `contrasegna` varchar(65) NOT NULL,
  `nombre` varchar(20) NOT NULL,
  `apellido` varchar(20) NOT NULL,
  `id_cargo` int(11) NOT NULL DEFAULT 1,
  `tipo` tinyint(4) NOT NULL DEFAULT 1,
  `estado_user` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_user`, `usuario`, `contrasegna`, `nombre`, `apellido`, `id_cargo`, `tipo`, `estado_user`) VALUES
(1, 'gabigabs123', '$2y$10$.TT2r82d.DPHpPrKOvnQAuwzGYlB04IC2NZJfDxBGaTyhEXz78Nvm', 'Gabriel', 'González', 1, 2, 0),
(2, 'yulssdc12', '$2y$10$jlRLdWO6i89lb1IiNAYtk.FpN9plPYXyYa.eoTYNCcfjGaWPjbNrW', 'Yulss', 'Subero', 1, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_documento`
--

CREATE TABLE `usuario_documento` (
  `id_userdoc` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_doc` int(11) DEFAULT NULL,
  `fecha` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario_documento`
--

INSERT INTO `usuario_documento` (`id_userdoc`, `id_user`, `id_doc`, `fecha`) VALUES
(1, 1, 1, '2025-01-30'),
(2, 1, 1, '2025-01-30'),
(3, 1, 2, '2025-01-30'),
(4, 1, 2, '2025-01-30'),
(5, 1, 3, '2025-01-30'),
(6, 1, 3, '2025-01-30'),
(7, 1, 4, '2025-01-30'),
(8, 1, 4, '2025-01-30'),
(9, 1, 5, '2025-01-30'),
(10, 1, 5, '2025-01-30'),
(11, 1, 6, '2025-01-30'),
(12, 1, 6, '2025-01-30'),
(13, 1, 7, '2025-01-30'),
(14, 1, 7, '2025-01-30');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `accion`
--
ALTER TABLE `accion`
  ADD PRIMARY KEY (`id_accion`),
  ADD KEY `fk_useraact` (`id_user`);

--
-- Indices de la tabla `autor`
--
ALTER TABLE `autor`
  ADD PRIMARY KEY (`id_autor`);

--
-- Indices de la tabla `autor_documento`
--
ALTER TABLE `autor_documento`
  ADD PRIMARY KEY (`id_autordoc`),
  ADD KEY `fk_docaautor` (`id_doc`),
  ADD KEY `fk_autoradoc` (`id_autor`);

--
-- Indices de la tabla `cargo`
--
ALTER TABLE `cargo`
  ADD PRIMARY KEY (`id_cargo`),
  ADD UNIQUE KEY `name_cargo` (`name_cargo`);

--
-- Indices de la tabla `clasificacion`
--
ALTER TABLE `clasificacion`
  ADD PRIMARY KEY (`id_clasf`),
  ADD UNIQUE KEY `name_clasf` (`name_clasf`);

--
-- Indices de la tabla `documento`
--
ALTER TABLE `documento`
  ADD PRIMARY KEY (`id_doc`),
  ADD KEY `fk_clasfadoc` (`id_clasf`);

--
-- Indices de la tabla `documento_palabraclave`
--
ALTER TABLE `documento_palabraclave`
  ADD PRIMARY KEY (`id_dockw`),
  ADD KEY `fk_kwadoc` (`id_kw`),
  ADD KEY `fk_docakw` (`id_doc`);

--
-- Indices de la tabla `multimedia`
--
ALTER TABLE `multimedia`
  ADD PRIMARY KEY (`id_media`),
  ADD KEY `fk_medianew` (`id_new`);

--
-- Indices de la tabla `noticia`
--
ALTER TABLE `noticia`
  ADD PRIMARY KEY (`id_new`),
  ADD KEY `fk_usertonew` (`id_user`);

--
-- Indices de la tabla `palabraclave`
--
ALTER TABLE `palabraclave`
  ADD PRIMARY KEY (`id_kw`),
  ADD UNIQUE KEY `name_kw` (`name_kw`);

--
-- Indices de la tabla `peticion`
--
ALTER TABLE `peticion`
  ADD PRIMARY KEY (`id_pet`),
  ADD KEY `fk_userapet` (`id_user`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Indices de la tabla `usuario_documento`
--
ALTER TABLE `usuario_documento`
  ADD PRIMARY KEY (`id_userdoc`),
  ADD KEY `fk_usertodoc` (`id_user`),
  ADD KEY `fk_doctouser` (`id_doc`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `accion`
--
ALTER TABLE `accion`
  MODIFY `id_accion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `autor`
--
ALTER TABLE `autor`
  MODIFY `id_autor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `autor_documento`
--
ALTER TABLE `autor_documento`
  MODIFY `id_autordoc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `cargo`
--
ALTER TABLE `cargo`
  MODIFY `id_cargo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `clasificacion`
--
ALTER TABLE `clasificacion`
  MODIFY `id_clasf` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `documento`
--
ALTER TABLE `documento`
  MODIFY `id_doc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `documento_palabraclave`
--
ALTER TABLE `documento_palabraclave`
  MODIFY `id_dockw` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `multimedia`
--
ALTER TABLE `multimedia`
  MODIFY `id_media` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT de la tabla `noticia`
--
ALTER TABLE `noticia`
  MODIFY `id_new` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `palabraclave`
--
ALTER TABLE `palabraclave`
  MODIFY `id_kw` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `peticion`
--
ALTER TABLE `peticion`
  MODIFY `id_pet` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuario_documento`
--
ALTER TABLE `usuario_documento`
  MODIFY `id_userdoc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `accion`
--
ALTER TABLE `accion`
  ADD CONSTRAINT `fk_useraact` FOREIGN KEY (`id_user`) REFERENCES `usuario` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `autor_documento`
--
ALTER TABLE `autor_documento`
  ADD CONSTRAINT `fk_autoradoc` FOREIGN KEY (`id_autor`) REFERENCES `autor` (`id_autor`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_docaautor` FOREIGN KEY (`id_doc`) REFERENCES `documento` (`id_doc`) ON DELETE CASCADE;

--
-- Filtros para la tabla `documento`
--
ALTER TABLE `documento`
  ADD CONSTRAINT `fk_clasfadoc` FOREIGN KEY (`id_clasf`) REFERENCES `clasificacion` (`id_clasf`);

--
-- Filtros para la tabla `documento_palabraclave`
--
ALTER TABLE `documento_palabraclave`
  ADD CONSTRAINT `fk_docakw` FOREIGN KEY (`id_doc`) REFERENCES `documento` (`id_doc`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_kwadoc` FOREIGN KEY (`id_kw`) REFERENCES `palabraclave` (`id_kw`);

--
-- Filtros para la tabla `multimedia`
--
ALTER TABLE `multimedia`
  ADD CONSTRAINT `fk_medianew` FOREIGN KEY (`id_new`) REFERENCES `noticia` (`id_new`) ON DELETE CASCADE;

--
-- Filtros para la tabla `noticia`
--
ALTER TABLE `noticia`
  ADD CONSTRAINT `fk_usertonew` FOREIGN KEY (`id_user`) REFERENCES `usuario` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario_documento`
--
ALTER TABLE `usuario_documento`
  ADD CONSTRAINT `fk_doctouser` FOREIGN KEY (`id_doc`) REFERENCES `documento` (`id_doc`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_usertodoc` FOREIGN KEY (`id_user`) REFERENCES `usuario` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
