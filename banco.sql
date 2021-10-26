# --------------------------------------------------------
# Host:                         127.0.0.1
# Server version:               5.5.8
# Server OS:                    Win32
# HeidiSQL version:             6.0.0.3603
# Date/time:                    2011-09-01 14:23:05
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

# Dumping database structure for auth
DROP DATABASE IF EXISTS `auth`;
CREATE DATABASE IF NOT EXISTS `auth` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `auth`;


# Dumping structure for table auth.banlist
DROP TABLE IF EXISTS `banlist`;
CREATE TABLE IF NOT EXISTS `banlist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `usuario` int(11) unsigned DEFAULT NULL,
  `grupo` int(11) unsigned DEFAULT NULL,
  `dominio` int(11) unsigned DEFAULT NULL,
  `fingerprint` varchar(50) DEFAULT NULL,
  `ipv4` varchar(11) DEFAULT NULL,
  `browser` varchar(25) DEFAULT NULL,
  `version` varchar(3) DEFAULT NULL,
  `so` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `browserversion` (`browser`,`version`),
  UNIQUE KEY `sistemaoperacional` (`so`),
  UNIQUE KEY `ipv4` (`ipv4`),
  UNIQUE KEY `fingerprint` (`fingerprint`),
  UNIQUE KEY `usuario_unique` (`usuario`),
  UNIQUE KEY `grupo_unique` (`grupo`),
  UNIQUE KEY `dominio_unique` (`dominio`),
  KEY `usuario` (`usuario`),
  KEY `grupo` (`grupo`),
  KEY `dominio` (`dominio`),
  CONSTRAINT `FK_banlist_dominio` FOREIGN KEY (`dominio`) REFERENCES `dominio` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_banlist_grupos` FOREIGN KEY (`grupo`) REFERENCES `grupos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_banlist_usuarios` FOREIGN KEY (`usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Dumping data for table auth.banlist: ~0 rows (approximately)
DELETE FROM `banlist`;
/*!40000 ALTER TABLE `banlist` DISABLE KEYS */;
/*!40000 ALTER TABLE `banlist` ENABLE KEYS */;


# Dumping structure for table auth.dominio
DROP TABLE IF EXISTS `dominio`;
CREATE TABLE IF NOT EXISTS `dominio` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(72) NOT NULL,
  `dominio` int(1) unsigned DEFAULT '0',
  `banido` int(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Dumping data for table auth.dominio: ~1 rows (approximately)
DELETE FROM `dominio`;
/*!40000 ALTER TABLE `dominio` DISABLE KEYS */;
INSERT INTO `dominio` (`id`, `nome`, `dominio`, `banido`) VALUES
	(1, 'Sistema Controlador de Acesso', 0, 0);
/*!40000 ALTER TABLE `dominio` ENABLE KEYS */;


# Dumping structure for table auth.eventos
DROP TABLE IF EXISTS `eventos`;
CREATE TABLE IF NOT EXISTS `eventos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sessao` int(11) unsigned NOT NULL,
  `acao` varchar(255) NOT NULL,
  `datahora` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sessao` (`sessao`),
  CONSTRAINT `FK_eventos_sessoes` FOREIGN KEY (`sessao`) REFERENCES `sessoes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Dumping data for table auth.eventos: ~453 rows (approximately)
DELETE FROM `eventos`;


# Dumping structure for table auth.grupos
DROP TABLE IF EXISTS `grupos`;
CREATE TABLE IF NOT EXISTS `grupos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(72) COLLATE utf8_unicode_ci NOT NULL,
  `dominio` int(11) unsigned NOT NULL,
  `banido` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome` (`nome`),
  KEY `dominio` (`dominio`),
  CONSTRAINT `FK_grupos_dominio` FOREIGN KEY (`dominio`) REFERENCES `dominio` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

# Dumping data for table auth.grupos: ~2 rows (approximately)
DELETE FROM `grupos`;
/*!40000 ALTER TABLE `grupos` DISABLE KEYS */;
INSERT INTO `grupos` (`id`, `nome`, `dominio`, `banido`) VALUES
	(1, 'Administradores', 1, 0),
	(2, 'UsuÃ¡rios', 1, 0);
/*!40000 ALTER TABLE `grupos` ENABLE KEYS */;


# Dumping structure for table auth.sessoes
DROP TABLE IF EXISTS `sessoes`;
CREATE TABLE IF NOT EXISTS `sessoes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `usuario` int(11) unsigned NOT NULL,
  `grupo` int(11) unsigned NOT NULL,
  `dominio` int(11) unsigned NOT NULL,
  `datahora` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ipv4` varchar(11) NOT NULL,
  `hostname` varchar(50) NOT NULL,
  `browser` varchar(25) NOT NULL,
  `version` varchar(3) NOT NULL,
  `so` varchar(25) NOT NULL,
  `fingerprint` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario` (`usuario`),
  KEY `grupo` (`grupo`),
  KEY `dominio` (`dominio`),
  CONSTRAINT `FK_sessoes_dominio` FOREIGN KEY (`dominio`) REFERENCES `dominio` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_sessoes_grupos` FOREIGN KEY (`grupo`) REFERENCES `grupos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_sessoes_usuarios` FOREIGN KEY (`usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Dumping data for table auth.sessoes: ~78 rows (approximately)
DELETE FROM `sessoes`;
/*!40000 ALTER TABLE `sessoes` DISABLE KEYS */;

# Dumping structure for table auth.usuarios
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(25) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fullname` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `encoded_password` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `grupo` int(11) unsigned NOT NULL DEFAULT '0',
  `dominio` int(11) unsigned NOT NULL DEFAULT '0',
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `banido` int(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`),
  UNIQUE KEY `email` (`email`),
  KEY `grupo` (`grupo`),
  KEY `dominio` (`dominio`),
  CONSTRAINT `usuario_dominio_fk` FOREIGN KEY (`dominio`) REFERENCES `dominio` (`id`) ON DELETE CASCADE,
  CONSTRAINT `usuario_grupo_fk` FOREIGN KEY (`grupo`) REFERENCES `grupos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DELETE FROM `usuarios`;

INSERT INTO `usuarios` (`id`, `login`, `fullname`, `email`, `encoded_password`, `grupo`, `dominio`, `expires`, `banido`) VALUES
	(1, 'root', 'Super User', 'giulianocf@gmail.com', 'dc1c3332a9cec838fe736405346212027362cd2e3569244518fca826927b7c3e6359e70b2a5084573810c942df6d2f969c842caf5c3411c5defe61c1f56b44fe', 1, 1, '2011-08-24 01:36:05', 0)
