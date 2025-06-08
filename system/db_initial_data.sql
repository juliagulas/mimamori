-- db_initial_data.sql
-- Initiale Daten für Mimamori (nur System-Daten, keine Benutzer)

-- Mimamori-Typen einfügen
INSERT INTO `mimamori_types` (`id`, `name`, `color_primary`, `color_secondary`) VALUES
(1, 'Rosa', '#ff9a8b', '#ff6a88'),
(2, 'Blau', '#8bb5ff', '#668dcc'),
(3, 'Grün', '#9aff8b', '#66cc5a'),
(4, 'Lila', '#c38bff', '#9966cc');