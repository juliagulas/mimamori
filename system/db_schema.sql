-- db_schema.sql
-- Mimamori Datenbank-Schema (ohne Benutzerdaten)
-- Für Installation auf neuen Systemen

-- Erstelle Datenbank (optional, falls Berechtigung vorhanden)
-- CREATE DATABASE IF NOT EXISTS mimamori CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE mimamori;

-- Tabelle für Mimamori-Typen
CREATE TABLE IF NOT EXISTS `mimamori_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `color_primary` varchar(7) NOT NULL,
  `color_secondary` varchar(7) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabelle für Benutzer
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `mimamori_type_id` int(11) DEFAULT 1,
  `happiness_level` int(11) DEFAULT 5,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_mimamori_type` (`mimamori_type_id`),
  CONSTRAINT `fk_mimamori_type` FOREIGN KEY (`mimamori_type_id`) REFERENCES `mimamori_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;