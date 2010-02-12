
--
-- Tabellenstruktur für Tabelle `db_kontakt` entspricht dem kontakt formular
--

CREATE TABLE `db_kontakt` (
  `kid` int(11) NOT NULL AUTO_INCREMENT,
  `betreff` enum('Information','Sonstiges') default NULL,
  `firma` varchar(50) default NULL,
  `branche` varchar(50) default NULL,
  `ansprechpartner` varchar(50) NOT NULL default '',
  `strasse` varchar(50) NOT NULL default '',
  `plz` varchar(5) NOT NULL default '',
  `ort` varchar(50) NOT NULL default '',
  `telefon` varchar(50) NOT NULL default '',
  `fax` varchar(50) default NULL,
  `e-mail` varchar(50) NOT NULL default '',
  `mitteilung` text NOT NULL,
  `confirm` integer default 0,

  PRIMARY KEY  (`kid`)
) TYPE=MyISAM;

--
-- Tabellenstruktur für Tabelle `db_survey` test fuer ein abstimmungs-
-- formular
--

CREATE TABLE `db_survey` (
  `kid` int(11) NOT NULL AUTO_INCREMENT,
  `wert` enum('1','2','3','4','5','6') default NULL,
  `mitteilung` text NOT NULL,
  `confirm` integer default 0,
  PRIMARY KEY  (`kid`)
) TYPE=MyISAM;