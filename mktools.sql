-- phpMyAdmin SQL Dump
-- version 2.10.2
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 21-01-2008 a las 10:16:30
-- Versión del servidor: 5.0.45
-- Versión de PHP: 5.2.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de datos: `mk_tools`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `computers`
--

CREATE TABLE `computers` (
`ip` varchar(11) NOT NULL default '',
`hostname` varchar(63) NOT NULL default '',
`displayname` varchar(63) NOT NULL default '',
`sort` int(10) unsigned NOT NULL default '0',
`status` char(1) NOT NULL default '',
`username` varchar(63) NOT NULL default '',
PRIMARY KEY (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcar la base de datos para la tabla `computers`
--

INSERT INTO `computers` (`ip`, `hostname`, `displayname`, `sort`, `status`, `username`) VALUES
('10.12.12.9', 'fimaster', 'Fimaster', 2, '1', 'YoannisRY'),
('10.12.13.40', 'mkestud-40', '40', 2, '1', 'gabreu');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configs`
--

CREATE TABLE `configs` (
`ip` varchar(11) NOT NULL default '',
`mk_dog_enabled` char(1) NOT NULL default '',
PRIMARY KEY (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Volcar la base de datos para la tabla `configs`
--

INSERT INTO `configs` (`ip`, `mk_dog_enabled`) VALUES
('10.12.13.1', '1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registries`
--

CREATE TABLE `registries` (
`id` int(11) NOT NULL auto_increment,
`root_key` varchar(63) NOT NULL default '',
`key` varchar(255) NOT NULL default '',
`string` varchar(63) NOT NULL default '',
`type` varchar(63) NOT NULL default '',
`value` varchar(255) NOT NULL default '',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Volcar la base de datos para la tabla `registries`
--

INSERT INTO `registries` (`id`, `root_key`, `key`, `string`, `type`, `value`) VALUES
(1, 'HKEY_LOCAL_MACHINE', '\\SOFTWARE\\Policies\\Microsoft\\Windows\\Safer\\CodeIdentifiers', 'DefaultLevel', 'dword', '00000000');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `technicians`
--

CREATE TABLE `technicians` (
`id` int(10) unsigned NOT NULL auto_increment,
`username` varchar(45) NOT NULL default '',
`password` varchar(45) NOT NULL default '',
`fullname` varchar(45) NOT NULL default '',
`email` varchar(45) NOT NULL default '',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Volcar la base de datos para la tabla `technicians`
--

INSERT INTO `technicians` (`id`, `username`, `password`, `fullname`, `email`) VALUES
(1, 'yosmy', '2197b3313c5eb6e584ead59c05cc86a4', '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `traces`
--

CREATE TABLE `traces` (
`id` bigint(20) NOT NULL auto_increment,
`trace` varchar(255) NOT NULL default '',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=52387 ;

--
-- Volcar la base de datos para la tabla `traces`
--

INSERT INTO `traces` (`id`, `trace`) VALUES
(52157, '10.12.13.40 - 8/30/2006 1:02:53 AM -'),
(52158, '10.12.13.40 - 8/30/2006 1:03:00 AM -'),
(52159, '10.12.13.40 - 8/30/2006 1:03:10 AM -'),
(52160, '10.12.13.40 - 8/30/2006 8:32:02 AM -'),
(52161, '10.12.13.40 - 8/30/2006 8:32:11 AM -'),
(52162, '10.12.13.40 - 8/30/2006 8:32:22 AM -'),
(52163, '10.12.13.40 - 8/30/2006 8:32:32 AM - uclv\\yosmany'),
(52164, '10.12.13.40 - 8/30/2006 8:32:41 AM -'),
(52165, '10.12.13.40 - 8/30/2006 8:32:53 AM - uclv\\yosmany'),
(52166, '10.12.13.40 - 8/30/2006 8:33:01 AM - uclv\\yosmany'),
(52167, '10.12.13.40 - 8/30/2006 8:33:11 AM - uclv\\yosmany'),
(52168, '10.12.13.40 - 8/30/2006 8:33:22 AM - uclv\\yosmany'),
(52169, '10.12.13.40 - 8/30/2006 8:33:31 AM -'),
(52170, '10.12.13.40 - 8/30/2006 8:33:41 AM -'),
(52171, '10.12.13.40 - 8/30/2006 8:33:51 AM -'),
(52172, '10.12.13.40 - 8/30/2006 8:34:01 AM -'),
(52173, '10.12.13.40 - 8/30/2006 8:34:11 AM -'),
(52174, '10.12.13.40 - 8/30/2006 8:34:21 AM -'),
(52175, '10.12.13.40 - 8/30/2006 8:34:31 AM -'),
(52176, '10.12.13.40 - 8/30/2006 8:34:41 AM -'),
(52177, '10.12.13.40 - 8/30/2006 8:34:51 AM -'),
(52178, '127.0.0.1 - 8/30/2006 8:35:01 AM - uclv\\yosmany - Without network'),
(52179, '127.0.0.1 - 8/30/2006 8:35:11 AM - uclv\\yosmany - Without network'),
(52180, '127.0.0.1 - 8/30/2006 8:35:21 AM - uclv\\yosmany - Without network'),
(52181, '127.0.0.1 - 8/30/2006 8:35:31 AM - uclv\\yosmany - Without network'),
(52182, '127.0.0.1 - 8/30/2006 8:35:41 AM - uclv\\yosmany - Without network'),
(52183, '10.12.13.40 - 8/30/2006 8:35:54 AM - uclv\\yosmany'),
(52184, '10.12.13.40 - 8/30/2006 8:36:01 AM -'),
(52185, '10.12.13.40 - 8/30/2006 8:36:11 AM -'),
(52186, '10.12.13.40 - 8/30/2006 8:36:21 AM -'),
(52187, '10.12.13.40 - 8/30/2006 8:36:31 AM -'),
(52188, '10.12.13.40 - 8/30/2006 8:36:41 AM -'),
(52189, '10.12.13.40 - 8/30/2006 8:36:51 AM -'),
(52190, '10.12.13.40 - 8/30/2006 8:37:01 AM -'),
(52191, '10.12.13.40 - 8/30/2006 8:37:11 AM -'),
(52192, '10.12.13.40 - 8/30/2006 8:37:21 AM -'),
(52193, '10.12.13.40 - 8/30/2006 8:37:31 AM -'),
(52194, '10.12.13.40 - 8/30/2006 8:37:41 AM -'),
(52195, '10.12.13.40 - 8/30/2006 8:37:51 AM -'),
(52196, '10.12.13.40 - 8/30/2006 8:38:01 AM -'),
(52197, '10.12.13.40 - 8/30/2006 8:38:11 AM - mkestud\\administrator'),
(52198, '10.12.13.40 - 8/30/2006 8:38:21 AM - mkestud\\administrator'),
(52199, '10.12.13.40 - 8/30/2006 8:38:31 AM - mkestud\\administrator'),
(52200, '10.12.13.40 - 8/30/2006 8:38:41 AM - mkestud\\administrator'),
(52201, '10.12.13.40 - 8/30/2006 8:38:51 AM - mkestud\\administrator'),
(52202, '10.12.13.40 - 8/30/2006 8:39:01 AM - mkestud\\administrator'),
(52203, '10.12.13.40 - 8/30/2006 8:39:11 AM - mkestud\\administrator'),
(52204, '10.12.13.40 - 8/30/2006 8:39:21 AM - mkestud\\administrator'),
(52205, '10.12.13.40 - 8/30/2006 8:39:31 AM - mkestud\\administrator'),
(52206, '10.12.13.40 - 8/30/2006 8:39:42 AM - mkestud\\administrator'),
(52207, '10.12.13.40 - 8/30/2006 8:39:51 AM - mkestud\\administrator'),
(52208, '10.12.13.40 - 8/30/2006 8:40:43 AM -'),
(52209, '10.12.13.40 - 8/30/2006 8:40:52 AM -'),
(52210, '10.12.13.40 - 8/30/2006 8:41:03 AM - mkestud\\administrator'),
(52211, '10.12.13.40 - 8/30/2006 8:41:13 AM - mkestud\\administrator'),
(52212, '10.12.13.40 - 8/30/2006 8:41:22 AM - mkestud\\administrator'),
(52213, '10.12.13.40 - 8/30/2006 8:41:32 AM - mkestud\\administrator'),
(52214, '10.12.13.40 - 8/30/2006 8:41:42 AM - mkestud\\administrator'),
(52215, '10.12.13.40 - 8/30/2006 8:41:52 AM - mkestud\\administrator'),
(52216, '10.12.13.40 - 8/30/2006 8:42:02 AM - mkestud\\administrator'),
(52217, '10.12.13.40 - 8/30/2006 8:42:12 AM - mkestud\\administrator'),
(52218, '10.12.13.40 - 8/30/2006 8:42:22 AM - mkestud\\administrator'),
(52219, '10.12.13.40 - 8/30/2006 8:42:32 AM - mkestud\\administrator'),
(52220, '10.12.13.40 - 8/30/2006 8:42:42 AM - mkestud\\administrator'),
(52221, '10.12.13.40 - 8/30/2006 8:42:52 AM - mkestud\\administrator'),
(52222, '10.12.13.40 - 8/30/2006 8:43:02 AM - mkestud\\administrator'),
(52223, '10.12.13.40 - 8/30/2006 8:43:13 AM - mkestud\\administrator'),
(52224, '10.12.13.40 - 8/30/2006 8:43:23 AM - mkestud\\administrator'),
(52225, '10.12.13.40 - 8/30/2006 8:43:33 AM - mkestud\\administrator'),
(52226, '10.12.13.40 - 8/30/2006 8:43:43 AM - mkestud\\administrator'),
(52227, '10.12.13.40 - 8/30/2006 8:43:53 AM - mkestud\\administrator'),
(52228, '10.12.13.40 - 8/30/2006 8:44:03 AM - mkestud\\administrator'),
(52229, '10.12.13.40 - 8/30/2006 8:44:13 AM - mkestud\\administrator'),
(52230, '10.12.13.40 - 8/30/2006 8:44:23 AM - mkestud\\administrator'),
(52231, '10.12.13.40 - 8/30/2006 8:44:33 AM - mkestud\\administrator'),
(52232, '10.12.13.40 - 8/30/2006 8:44:43 AM - mkestud\\administrator'),
(52233, '10.12.13.40 - 8/30/2006 8:44:53 AM - mkestud\\administrator'),
(52234, '10.12.13.40 - 8/30/2006 8:45:03 AM - mkestud\\administrator'),
(52235, '10.12.13.40 - 8/30/2006 8:45:13 AM - mkestud\\administrator'),
(52236, '10.12.13.40 - 8/30/2006 8:45:23 AM - mkestud\\administrator'),
(52237, '10.12.13.40 - 8/30/2006 8:45:33 AM - mkestud\\administrator'),
(52238, '10.12.13.40 - 8/30/2006 8:45:43 AM - mkestud\\administrator'),
(52239, '10.12.13.40 - 8/30/2006 8:45:53 AM - mkestud\\administrator'),
(52240, '10.12.13.40 - 8/30/2006 8:46:03 AM - mkestud\\administrator'),
(52241, '10.12.13.40 - 8/30/2006 8:46:13 AM - mkestud\\administrator'),
(52242, '10.12.13.40 - 8/30/2006 8:46:23 AM - mkestud\\administrator'),
(52243, '10.12.13.40 - 8/30/2006 8:46:33 AM - mkestud\\administrator'),
(52244, '10.12.13.40 - 8/30/2006 8:46:43 AM - mkestud\\administrator'),
(52245, '10.12.13.40 - 8/30/2006 8:46:53 AM - mkestud\\administrator'),
(52246, '10.12.13.40 - 8/30/2006 8:47:03 AM - mkestud\\administrator'),
(52247, '10.12.13.40 - 8/30/2006 8:47:13 AM - mkestud\\administrator'),
(52248, '10.12.13.40 - 8/30/2006 8:47:23 AM - mkestud\\administrator'),
(52249, '10.12.13.40 - 8/30/2006 8:47:33 AM - mkestud\\administrator'),
(52250, '10.12.13.40 - 8/30/2006 8:47:43 AM - mkestud\\administrator'),
(52251, '10.12.13.40 - 8/30/2006 8:47:53 AM - mkestud\\administrator'),
(52252, '10.12.13.40 - 8/30/2006 8:48:03 AM - mkestud\\administrator'),
(52253, '10.12.13.40 - 8/30/2006 8:48:13 AM - mkestud\\administrator'),
(52254, '10.12.13.40 - 8/30/2006 8:48:23 AM - mkestud\\administrator'),
(52255, '10.12.13.40 - 8/30/2006 8:48:33 AM - mkestud\\administrator'),
(52256, '10.12.13.40 - 8/30/2006 8:48:43 AM - mkestud\\administrator'),
(52257, '10.12.13.40 - 8/30/2006 8:48:53 AM - mkestud\\administrator'),
(52258, '10.12.13.40 - 8/30/2006 8:49:03 AM - mkestud\\administrator'),
(52259, '10.12.13.40 - 8/30/2006 8:49:13 AM - mkestud\\administrator'),
(52260, '10.12.13.40 - 8/30/2006 8:49:23 AM - mkestud\\administrator'),
(52261, '10.12.13.40 - 8/30/2006 8:49:33 AM - mkestud\\administrator'),
(52262, '10.12.13.40 - 8/30/2006 8:49:43 AM - mkestud\\administrator'),
(52263, '10.12.13.40 - 8/30/2006 8:49:53 AM - mkestud\\administrator'),
(52264, '10.12.13.40 - 8/30/2006 8:50:03 AM - mkestud\\administrator'),
(52265, '10.12.13.40 - 8/30/2006 8:50:13 AM - mkestud\\administrator'),
(52266, '10.12.13.40 - 8/30/2006 8:50:23 AM - mkestud\\administrator'),
(52267, '10.12.13.40 - 8/30/2006 8:50:33 AM - mkestud\\administrator'),
(52268, '10.12.13.40 - 8/30/2006 8:50:43 AM - mkestud\\administrator'),
(52269, '10.12.13.40 - 8/30/2006 8:50:53 AM - mkestud\\administrator'),
(52270, '10.12.13.40 - 8/30/2006 8:51:03 AM - mkestud\\administrator'),
(52271, '10.12.13.40 - 8/30/2006 8:51:13 AM - mkestud\\administrator'),
(52272, '10.12.13.40 - 8/30/2006 8:51:23 AM - mkestud\\administrator'),
(52273, '10.12.13.40 - 8/30/2006 8:51:33 AM - mkestud\\administrator'),
(52274, '10.12.13.40 - 8/30/2006 8:51:43 AM - mkestud\\administrator'),
(52275, '10.12.13.40 - 8/30/2006 8:51:53 AM - mkestud\\administrator'),
(52276, '10.12.13.40 - 8/30/2006 8:52:03 AM - mkestud\\administrator'),
(52277, '10.12.13.40 - 8/30/2006 8:52:13 AM - mkestud\\administrator'),
(52278, '10.12.13.40 - 8/30/2006 8:52:23 AM - mkestud\\administrator'),
(52279, '10.12.13.40 - 8/30/2006 8:52:33 AM - mkestud\\administrator'),
(52280, '10.12.13.40 - 8/30/2006 8:52:43 AM - mkestud\\administrator'),
(52281, '10.12.13.40 - 8/30/2006 8:52:53 AM - mkestud\\administrator'),
(52282, '10.12.13.40 - 8/30/2006 8:53:03 AM - mkestud\\administrator'),
(52283, '10.12.13.40 - 8/30/2006 8:53:13 AM - mkestud\\administrator'),
(52284, '10.12.13.40 - 8/30/2006 8:53:23 AM - mkestud\\administrator'),
(52285, '10.12.13.40 - 8/30/2006 8:54:39 AM - mkestud\\administrator'),
(52286, '10.12.13.40 - 8/30/2006 8:54:40 AM - mkestud\\administrator'),
(52287, '10.12.13.40 - 8/30/2006 8:54:40 AM - mkestud\\administrator'),
(52288, '10.12.13.40 - 8/30/2006 8:54:42 AM - mkestud\\administrator'),
(52289, '10.12.13.40 - 8/30/2006 8:54:52 AM - mkestud\\administrator'),
(52290, '10.12.13.40 - 8/30/2006 8:55:02 AM - mkestud\\administrator'),
(52291, '10.12.13.40 - 8/30/2006 8:55:12 AM - mkestud\\administrator'),
(52292, '10.12.13.40 - 8/30/2006 8:55:22 AM - mkestud\\administrator'),
(52293, '10.12.13.40 - 8/30/2006 8:55:32 AM - mkestud\\administrator'),
(52294, '10.12.13.40 - 8/30/2006 8:55:42 AM - mkestud\\administrator'),
(52295, '10.12.13.40 - 8/30/2006 8:55:52 AM - mkestud\\administrator'),
(52296, '10.12.13.40 - 8/30/2006 8:56:03 AM - mkestud\\administrator'),
(52297, '10.12.13.40 - 8/30/2006 8:57:08 AM - mkestud\\administrator'),
(52298, '10.12.13.40 - 8/30/2006 8:57:16 AM - mkestud\\administrator'),
(52299, '10.12.13.40 - 8/30/2006 8:57:26 AM - mkestud\\administrator'),
(52300, '10.12.13.40 - 8/30/2006 8:57:36 AM - mkestud\\administrator'),
(52301, '10.12.13.40 - 8/30/2006 8:57:46 AM - mkestud\\administrator'),
(52302, '10.12.13.40 - 8/30/2006 8:57:56 AM - mkestud\\administrator'),
(52303, '10.12.13.40 - 8/30/2006 8:58:06 AM - mkestud\\administrator'),
(52304, '10.12.13.40 - 8/30/2006 8:58:16 AM - mkestud\\administrator'),
(52305, '10.12.13.40 - 8/30/2006 8:58:26 AM - mkestud\\administrator'),
(52306, '10.12.13.40 - 8/30/2006 8:58:36 AM - mkestud\\administrator'),
(52307, '10.12.13.40 - 8/30/2006 8:58:46 AM - mkestud\\administrator'),
(52308, '10.12.13.40 - 8/30/2006 8:58:56 AM - mkestud\\administrator'),
(52309, '10.12.13.40 - 8/30/2006 8:59:06 AM - mkestud\\administrator'),
(52310, '10.12.13.40 - 8/30/2006 9:00:02 AM - mkestud\\administrator'),
(52311, '10.12.13.40 - 8/30/2006 9:00:03 AM - mkestud\\administrator'),
(52312, '10.12.13.40 - 8/30/2006 9:00:11 AM - mkestud\\administrator'),
(52313, '10.12.13.40 - 8/30/2006 9:00:21 AM - mkestud\\administrator'),
(52314, '10.12.13.40 - 8/30/2006 9:00:31 AM - mkestud\\administrator'),
(52315, '10.12.13.40 - 8/30/2006 9:00:41 AM - mkestud\\administrator'),
(52316, '10.12.13.40 - 8/30/2006 9:00:51 AM - mkestud\\administrator'),
(52317, '10.12.13.40 - 8/30/2006 9:01:01 AM - mkestud\\administrator'),
(52318, '10.12.13.40 - 8/30/2006 9:01:11 AM - mkestud\\administrator'),
(52319, '10.12.13.40 - 8/30/2006 9:01:21 AM - mkestud\\administrator'),
(52320, '10.12.13.40 - 8/30/2006 9:01:31 AM - mkestud\\administrator'),
(52321, '10.12.13.40 - 8/30/2006 9:01:41 AM - mkestud\\administrator'),
(52322, '10.12.13.40 - 8/30/2006 9:01:51 AM - mkestud\\administrator'),
(52323, '10.12.13.40 - 8/30/2006 9:02:01 AM - mkestud\\administrator'),
(52324, '10.12.13.40 - 8/30/2006 9:02:11 AM - mkestud\\administrator'),
(52325, '10.12.13.40 - 8/30/2006 9:02:21 AM - mkestud\\administrator'),
(52326, '10.12.13.40 - 8/30/2006 9:02:31 AM - mkestud\\administrator'),
(52327, '10.12.13.40 - 8/30/2006 9:02:41 AM - mkestud\\administrator'),
(52328, '10.12.13.40 - 8/30/2006 9:02:51 AM - mkestud\\administrator'),
(52329, '10.12.13.40 - 8/30/2006 9:03:01 AM - mkestud\\administrator'),
(52330, '10.12.13.40 - 8/30/2006 9:03:11 AM - mkestud\\administrator'),
(52331, '10.12.13.40 - 8/30/2006 9:03:21 AM - mkestud\\administrator'),
(52332, '10.12.13.40 - 8/30/2006 9:03:31 AM - mkestud\\administrator'),
(52333, '10.12.13.40 - 8/30/2006 9:03:41 AM - mkestud\\administrator'),
(52334, '10.12.13.40 - 8/30/2006 9:03:51 AM - mkestud\\administrator'),
(52335, '10.12.13.40 - 8/30/2006 9:04:01 AM - mkestud\\administrator'),
(52336, '10.12.13.40 - 8/30/2006 9:04:11 AM - mkestud\\administrator'),
(52337, '10.12.13.40 - 8/30/2006 9:04:21 AM - mkestud\\administrator'),
(52338, '10.12.13.40 - 8/30/2006 9:04:31 AM - mkestud\\administrator'),
(52339, '10.12.13.40 - 8/30/2006 9:04:41 AM - mkestud\\administrator'),
(52340, '10.12.13.40 - 8/30/2006 9:05:30 AM -'),
(52341, '10.12.13.40 - 8/30/2006 9:05:39 AM -'),
(52342, '10.12.13.40 - 8/30/2006 9:05:51 AM - mkestud\\administrator'),
(52343, '10.12.13.40 - 8/30/2006 9:05:59 AM - mkestud\\administrator'),
(52344, '10.12.13.40 - 8/30/2006 9:06:10 AM - mkestud\\administrator'),
(52345, '10.12.13.40 - 8/30/2006 9:06:19 AM - mkestud\\administrator'),
(52346, '10.12.13.40 - 8/30/2006 9:06:29 AM - mkestud\\administrator'),
(52347, '10.12.13.40 - 8/30/2006 9:06:39 AM - mkestud\\administrator'),
(52348, '10.12.13.40 - 8/30/2006 9:06:49 AM - mkestud\\administrator'),
(52349, '10.12.13.40 - 8/30/2006 9:06:59 AM - mkestud\\administrator'),
(52350, '10.12.13.40 - 8/30/2006 9:07:09 AM - mkestud\\administrator'),
(52351, '10.12.13.40 - 8/30/2006 9:07:19 AM - mkestud\\administrator'),
(52352, '10.12.13.40 - 8/30/2006 9:07:29 AM - mkestud\\administrator'),
(52353, '10.12.13.40 - 8/30/2006 9:07:39 AM - mkestud\\administrator'),
(52354, '10.12.13.40 - 8/30/2006 9:07:49 AM - mkestud\\administrator'),
(52355, '10.12.13.40 - 8/30/2006 9:07:59 AM - mkestud\\administrator'),
(52356, '10.12.13.40 - 8/30/2006 9:08:09 AM - mkestud\\administrator'),
(52357, '10.12.13.40 - 8/30/2006 9:08:19 AM - mkestud\\administrator'),
(52358, '10.12.13.40 - 8/30/2006 9:08:29 AM - mkestud\\administrator'),
(52359, '10.12.13.40 - 8/30/2006 9:08:39 AM - mkestud\\administrator'),
(52360, '10.12.13.40 - 8/30/2006 9:08:49 AM - mkestud\\administrator'),
(52361, '10.12.13.40 - 8/30/2006 9:08:59 AM - mkestud\\administrator'),
(52362, '10.12.13.40 - 8/30/2006 9:09:09 AM - mkestud\\administrator'),
(52363, '10.12.13.40 - 8/30/2006 9:09:20 AM - mkestud\\administrator'),
(52364, '10.12.13.40 - 8/30/2006 9:09:29 AM - mkestud\\administrator'),
(52365, '10.12.13.40 - 8/30/2006 9:09:39 AM - mkestud\\administrator'),
(52366, '10.12.13.40 - 8/30/2006 9:09:50 AM - mkestud\\administrator'),
(52367, '10.12.13.40 - 8/30/2006 9:09:59 AM - mkestud\\administrator'),
(52368, '10.12.13.40 - 8/30/2006 9:10:09 AM - mkestud\\administrator'),
(52369, '10.12.13.40 - 8/30/2006 9:10:20 AM - mkestud\\administrator'),
(52370, '10.12.13.40 - 8/30/2006 9:10:29 AM - mkestud\\administrator'),
(52371, '10.12.13.40 - 8/30/2006 9:10:39 AM - mkestud\\administrator'),
(52372, '10.12.13.40 - 8/30/2006 9:10:50 AM - mkestud\\administrator'),
(52373, '10.12.13.40 - 8/30/2006 9:10:59 AM - mkestud\\administrator'),
(52374, '10.12.13.40 - 8/30/2006 9:11:09 AM - mkestud\\administrator'),
(52375, '10.12.13.40 - 8/30/2006 9:11:20 AM - mkestud\\administrator'),
(52376, '10.12.13.40 - 8/30/2006 9:11:30 AM - mkestud\\administrator'),
(52377, '10.12.13.40 - 8/30/2006 9:11:40 AM - mkestud\\administrator'),
(52378, '10.12.13.40 - 8/30/2006 9:11:50 AM - mkestud\\administrator'),
(52379, '10.12.13.40 - 8/30/2006 9:12:00 AM - mkestud\\administrator'),
(52380, '10.12.13.40 - 8/30/2006 9:12:10 AM - mkestud\\administrator'),
(52381, '10.12.13.40 - 8/30/2006 9:12:20 AM - mkestud\\administrator'),
(52382, '10.12.13.40 - 8/30/2006 9:12:30 AM - mkestud\\administrator'),
(52383, '10.12.13.40 - 8/30/2006 9:12:40 AM - mkestud\\administrator'),
(52384, '10.12.13.40 - 8/30/2006 9:12:50 AM - mkestud\\administrator'),
(52385, '10.12.13.40 - 8/30/2006 9:13:00 AM - mkestud\\administrator'),
(52386, '10.12.13.40 - 8/30/2006 9:13:10 AM - mkestud\\administrator');
