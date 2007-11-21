-- phpMyAdmin SQL Dump
-- version 2.11.2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 21. November 2007 um 13:13
-- Server Version: 4.0.24
-- PHP-Version: 5.2.0-8+etch7~bpo.1


--
-- Datenbank: `ewebuki_mdebase`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `auth_content`
--

CREATE TABLE IF NOT EXISTS `auth_content` (
  `uid` int(11) NOT NULL default '0',
  `gid` int(11) NOT NULL default '0',
  `pid` int(11) NOT NULL default '0',
  `db` varchar(20) NOT NULL default '',
  `tname` varchar(50) NOT NULL default '',
  `ebene` text NOT NULL,
  `kategorie` text NOT NULL,
  PRIMARY KEY  (`uid`,`gid`,`pid`,`db`,`tname`)
) TYPE=MyISAM;

--
-- Daten für Tabelle `auth_content`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `auth_group`
--

CREATE TABLE IF NOT EXISTS `auth_group` (
  `gid` int(11) NOT NULL auto_increment,
  `ggroup` varchar(30) NOT NULL default '',
  `beschreibung` text NOT NULL,
  PRIMARY KEY  (`gid`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `auth_group`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `auth_level`
--

CREATE TABLE IF NOT EXISTS `auth_level` (
  `lid` int(11) NOT NULL auto_increment,
  `level` varchar(10) NOT NULL default '',
  `beschreibung` text NOT NULL,
  PRIMARY KEY  (`lid`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `auth_level`
--

INSERT INTO `auth_level` (`lid`, `level`, `beschreibung`) VALUES
(1, 'cms_edit', 'berechtigt zum bearbeiten der templates'),
(2, 'cms_admin', 'berechtigt zur administration');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `auth_member`
--

CREATE TABLE IF NOT EXISTS `auth_member` (
  `uid` int(11) NOT NULL default '0',
  `gid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`uid`,`gid`)
) TYPE=MyISAM;

--
-- Daten für Tabelle `auth_member`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `auth_priv`
--

CREATE TABLE IF NOT EXISTS `auth_priv` (
  `pid` int(11) NOT NULL default '0',
  `priv` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`pid`)
) TYPE=MyISAM;

--
-- Daten für Tabelle `auth_priv`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `auth_right`
--

CREATE TABLE IF NOT EXISTS `auth_right` (
  `uid` int(11) NOT NULL default '0',
  `lid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`uid`,`lid`)
) TYPE=MyISAM;

--
-- Daten für Tabelle `auth_right`
--

INSERT INTO `auth_right` (`uid`, `lid`) VALUES
(1, 1),
(1, 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `auth_special`
--

CREATE TABLE IF NOT EXISTS `auth_special` (
  `sid` int(11) NOT NULL auto_increment,
  `suid` int(11) NOT NULL default '0',
  `content` int(11) default '0',
  `sdb` varchar(20) NOT NULL default '',
  `stname` varchar(50) NOT NULL default '',
  `sebene` text,
  `skategorie` text,
  `sbeschreibung` text,
  PRIMARY KEY  (`sid`)
) TYPE=MyISAM AUTO_INCREMENT=1 ;

--
-- Daten für Tabelle `auth_special`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `auth_user`
--

CREATE TABLE IF NOT EXISTS `auth_user` (
  `uid` int(11) NOT NULL auto_increment,
  `nachname` varchar(40) NOT NULL default '',
  `vorname` varchar(40) NOT NULL default '',
  `email` varchar(60) NOT NULL default '',
  `username` varchar(20) NOT NULL default '',
  `pass` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`uid`),
  UNIQUE KEY `username` (`username`)
) TYPE=MyISAM PACK_KEYS=0 AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `auth_user`
--

INSERT INTO `auth_user` (`uid`, `nachname`, `vorname`, `email`, `username`, `pass`) VALUES
(1, '', '', '', 'ewebuki', 'WFffxluy26Lew');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `db_leer`
--

CREATE TABLE IF NOT EXISTS `db_leer` (
  `id` int(11) NOT NULL auto_increment,
  `field1` varchar(255) NOT NULL default '',
  `field2` text NOT NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `db_leer`
--

INSERT INTO `db_leer` (`id`, `field1`, `field2`) VALUES
(1, 'Erster Eintrag', 'Zweite Spalte'),
(2, 'Zweiter Eintrag', 'Zweite Spalte');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `site_file`
--

CREATE TABLE IF NOT EXISTS `site_file` (
  `fid` int(11) NOT NULL auto_increment,
  `frefid` int(11) NOT NULL default '0',
  `fuid` int(11) NOT NULL default '0',
  `fdid` int(11) NOT NULL default '0',
  `ftname` varchar(255) NOT NULL default '',
  `ffname` varchar(255) NOT NULL default '',
  `ffart` enum('gif','jpg','png','pdf','zip','odt','ods','odp','gz','bz2') NOT NULL default 'jpg',
  `fdesc` text NOT NULL,
  `funder` varchar(255) default NULL,
  `fhit` varchar(255) default NULL,
  `fdel` text,
  PRIMARY KEY  (`fid`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

--
-- Daten für Tabelle `site_file`
--

INSERT INTO `site_file` (`fid`, `frefid`, `fuid`, `fdid`, `ftname`, `ffname`, `ffart`, `fdesc`, `funder`, `fhit`, `fdel`) VALUES
(1, 0, 1, 0, '', 'ewebuki_160x67.png', 'png', 'eWeBuKi Logo Beschreibung', 'eWeBuKi Logo Unterschift', '', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `site_form`
--

CREATE TABLE IF NOT EXISTS `site_form` (
  `fid` int(11) NOT NULL auto_increment,
  `flabel` varchar(20) NOT NULL default '',
  `ftname` varchar(40) NOT NULL default '',
  `fsize` varchar(7) NOT NULL default '0',
  `fclass` varchar(30) NOT NULL default '',
  `fstyle` varchar(60) NOT NULL default '',
  `foption` enum('file','hidden','password','pgenum','readonly') default NULL,
  `frequired` enum('0','-1') NOT NULL default '0',
  `fcheck` text NOT NULL,
  PRIMARY KEY  (`fid`)
) TYPE=MyISAM AUTO_INCREMENT=11 ;

--
-- Daten für Tabelle `site_form`
--

INSERT INTO `site_form` (`fid`, `flabel`, `ftname`, `fsize`, `fclass`, `fstyle`, `foption`, `frequired`, `fcheck`) VALUES
(1, 'username', '210295197.modify', '0', '', '', NULL, '-1', ''),
(2, 'pass', '210295197.modify', '0', '', '', 'password', '-1', ''),
(3, 'pass', '852881080.modify', '0', '', '', 'password', '-1', ''),
(4, 'fid', '-939795212.modify', '0', 'hidden', '', 'hidden', '-1', ''),
(6, 'fdesc', '-939795212.modify', '25', '', '', NULL, '0', ''),
(7, 'funder', '-939795212.modify', '30', '', '', NULL, '0', ''),
(8, 'fhit', '-939795212.modify', '30', '', '', NULL, '0', ''),
(9, 'entry', '-555504947.add', '0', '', '', NULL, '-1', 'PREG:^[a-z_-.0-9]+$'),
(10, 'entry', '-555504947.edit', '0', '', '', NULL, '-1', 'PREG:^[a-z_-.0-9]+$');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `site_form_lang`
--

CREATE TABLE IF NOT EXISTS `site_form_lang` (
  `flid` int(11) NOT NULL auto_increment,
  `fid` int(11) NOT NULL default '0',
  `flang` varchar(5) NOT NULL default 'de',
  `fpgenum` text,
  `fwerte` varchar(255) NOT NULL default '',
  `ferror` varchar(255) NOT NULL default '',
  `fdberror` varchar(255) NOT NULL default '',
  `fchkerror` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`flid`)
) TYPE=MyISAM AUTO_INCREMENT=11 ;

--
-- Daten für Tabelle `site_form_lang`
--

INSERT INTO `site_form_lang` (`flid`, `fid`, `flang`, `fpgenum`, `fwerte`, `ferror`, `fdberror`, `fchkerror`) VALUES
(1, 1, 'de', NULL, '', 'Username darf nicht leer sein.', 'Username bereits vorhanden.', ''),
(2, 2, 'de', NULL, '', 'Passworte nicht identisch oder leer.', '', ''),
(3, 3, 'de', NULL, '', 'Passworte nicht identisch oder leer.', '', ''),
(9, 9, 'de', NULL, '', '', '', 'Ungültige Zeichen im Feld Eintrag.'),
(10, 10, 'de', NULL, '', '', '', 'Ungültige Zeichen im Feld Eintrag.');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `site_lock`
--

CREATE TABLE IF NOT EXISTS `site_lock` (
  `lang` varchar(5) NOT NULL default '',
  `label` varchar(20) NOT NULL default '',
  `tname` varchar(40) NOT NULL default '',
  `byalias` varchar(20) NOT NULL default '',
  `lockat` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`lang`,`label`,`tname`)
) TYPE=MyISAM;

--
-- Daten für Tabelle `site_lock`
--


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `site_menu`
--

CREATE TABLE IF NOT EXISTS `site_menu` (
  `mid` int(10) NOT NULL auto_increment,
  `refid` int(10) default '0',
  `entry` varchar(30) NOT NULL default '',
  `picture` varchar(128) default NULL,
  `sort` int(8) NOT NULL default '1000',
  `hide` enum('-1') default NULL,
  `level` varchar(10) default NULL,
  `mandatory` enum('-1') default NULL,
  `defaulttemplate` enum('default1','default2','default3','default4') NOT NULL default 'default1',
  PRIMARY KEY  (`mid`),
  UNIQUE KEY `DUPE` (`refid`,`entry`)
) TYPE=MyISAM PACK_KEYS=1 AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `site_menu`
--

INSERT INTO `site_menu` (`mid`, `refid`, `entry`, `picture`, `sort`, `hide`, `level`, `mandatory`, `defaulttemplate`) VALUES
(1, 0, 'demo', NULL, 10, NULL, NULL, NULL, 'default1'),
(2, 0, 'show', NULL, 20, NULL, NULL, NULL, 'default1'),
(3, 0, 'fehler', NULL, 30, NULL, NULL, NULL, 'default1'),
(4, 0, 'impressum', NULL, 40, NULL, NULL, NULL, 'default1');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `site_menu_lang`
--

CREATE TABLE IF NOT EXISTS `site_menu_lang` (
  `mlid` int(10) NOT NULL auto_increment,
  `mid` int(10) NOT NULL default '0',
  `lang` varchar(5) NOT NULL default 'de',
  `label` varchar(30) NOT NULL default '',
  `exturl` varchar(128) default NULL,
  PRIMARY KEY  (`mlid`)
) TYPE=MyISAM PACK_KEYS=1 AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `site_menu_lang`
--

INSERT INTO `site_menu_lang` (`mlid`, `mid`, `lang`, `label`, `exturl`) VALUES
(1, 1, 'de', 'Demo', NULL),
(2, 2, 'de', 'eWeBuKi Show', NULL),
(3, 3, 'de', '404', NULL),
(4, 4, 'de', 'Impressum', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `site_text`
--

CREATE TABLE IF NOT EXISTS `site_text` (
  `lang` varchar(5) NOT NULL default 'de',
  `label` varchar(20) NOT NULL default '',
  `tname` varchar(40) NOT NULL default '',
  `version` int(11) NOT NULL default '0',
  `ebene` text NOT NULL,
  `kategorie` text NOT NULL,
  `crc32` enum('-1','0') NOT NULL default '-1',
  `html` enum('-1','0') NOT NULL default '0',
  `content` text NOT NULL,
  `changed` datetime NOT NULL default '0000-00-00 00:00:00',
  `bysurname` varchar(40) NOT NULL default '',
  `byforename` varchar(40) NOT NULL default '',
  `byemail` varchar(60) NOT NULL default '',
  `byalias` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`lang`,`label`,`tname`,`version`)
) TYPE=MyISAM PACK_KEYS=1;

--
-- Daten für Tabelle `site_text`
--

INSERT INTO `site_text` (`lang`, `label`, `tname`, `version`, `ebene`, `kategorie`, `crc32`, `html`, `content`, `changed`, `bysurname`, `byforename`, `byemail`, `byalias`) VALUES
('de', 'abort', '-555504947.delete', 0, '/admin/menued', 'delete', '-1', '0', 'Abbrechen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'content', '-555504947.delete', 0, '/admin/menued', 'delete', '-1', '0', 'Inhalt', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'entry', '-555504947.delete', 0, '/admin/menued', 'delete', '-1', '0', 'Eintrag', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'error_menu', '-555504947.delete', 0, '/admin/menued', 'delete', '-1', '0', 'Fehler beim löschen des Menüeintrag', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'error_menu_lang', '-555504947.delete', 0, '/admin/menued', 'delete', '-1', '0', 'Fehler beim löschen der Sprache(n)', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'error_text', '-555504947.delete', 0, '/admin/menued', 'delete', '-1', '0', 'Fehler beim löschen des/r Text/e', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'inhalt', '-555504947.delete', 0, '/admin/menued', 'delete', '-1', '0', 'Den Menüpunkt "!#ausgaben_entry" wirklich löschen?', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'languages', '-555504947.delete', 0, '/admin/menued', 'delete', '-1', '0', 'Sprachen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'no_content', '-555504947.delete', 0, '/admin/menued', 'delete', '-1', '0', 'Kein Inhalt', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'send', '-555504947.delete', 0, '/admin/menued', 'delete', '-1', '0', 'Abschicken', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', '-555504947.delete', 0, '/admin/menued', 'delete', '-1', '0', 'Menü-Editor - Menüpunkt löschen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'abort', '-555504947.edit-multi', 0, '/admin/menued', 'edit', '-1', '0', 'Abbrechen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'add', '-555504947.edit-multi', 0, '/admin/menued', 'edit', '-1', '0', 'Neue Sprache hinzufügen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'basic', '-555504947.edit-multi', 0, '/admin/menued', 'add', '-1', '0', 'Allgemein', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'delete', '-555504947.edit-multi', 0, '/admin/menued', 'edit', '-1', '0', 'Diese Sprache löschen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'entry', '-555504947.edit-multi', 0, '/admin/menued', 'edit', '-1', '0', 'Eintrag', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'error_lang_add', '-555504947.edit-multi', 0, '/admin/menued', 'edit', '-1', '0', 'Diese Sprache ist bereits vorhanden.', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'error_lang_delete', '-555504947.edit-multi', 0, '/admin/menued', 'edit', '-1', '0', 'Die Entwickler Sprache kann nicht gelöscht werden.', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'error_result', '-555504947.edit-multi', 0, '/admin/menued', 'edit', '-1', '0', 'DB Fehler: ', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'extended', '-555504947.edit-multi', 0, '/admin/menued', 'edit', '-1', '0', 'Speziell', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'exturl', '-555504947.edit-multi', 0, '/admin/menued', 'edit', '-1', '0', 'externe Url', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'hide', '-555504947.edit-multi', 0, '/admin/menued', 'edit', '-1', '0', 'Deaktiviert', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'label', '-555504947.edit-multi', 0, '/admin/menued', 'edit', '-1', '0', 'Bezeichnung', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'lang', '-555504947.edit-multi', 0, '/admin/menued', 'edit', '-1', '0', 'Sprache', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'language', '-555504947.edit-multi', 0, '/admin/menued', 'edit', '-1', '0', 'Sprachen Verwaltung', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'level', '-555504947.edit-multi', 0, '/admin/menued', 'edit', '-1', '0', 'benötigter Level', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'madatory', '-555504947.edit-multi', 0, '/admin/menued', 'edit', '-1', '0', 'Erzwungen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'new_lang', '-555504947.edit-multi', 0, '/admin/menued', 'edit', '-1', '0', 'Neue Sprache', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'refid', '-555504947.edit-multi', 0, '/admin/menued', 'edit', '-1', '0', 'Ref. ID', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'reset', '-555504947.edit-multi', 0, '/admin/menued', 'edit', '-1', '0', 'Zurücksetzen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'send', '-555504947.edit-multi', 0, '/admin/menued', 'edit', '-1', '0', 'Abschicken', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'sort', '-555504947.edit-multi', 0, '/admin/menued', 'edit', '-1', '0', 'Sortierung', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'template', '-555504947.edit-multi', 0, '/admin/menued', 'edit', '-1', '0', 'Template', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', '-555504947.edit-multi', 0, '/admin/menued', 'edit', '-1', '0', 'Menü-Editor - Menüpunkt', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'abort', '-555504947.edit-single', 0, '/admin/menued', 'edit', '-1', '0', 'Abbrechen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'add', '-555504947.edit-single', 0, '/admin/menued', 'add', '-1', '0', 'Neue Sprache hinzufügen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'basic', '-555504947.edit-single', 0, '/admin/menued', 'add', '-1', '0', 'Allgemein', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'entry', '-555504947.edit-single', 0, '/admin/menued', 'add', '-1', '0', 'Eintrag', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'error_lang_add', '-555504947.edit-single', 0, '/admin/menued', 'edit', '-1', '0', 'Diese Sprache ist bereits vorhanden.', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'error_lang_delete', '-555504947.edit-single', 0, '/admin/menued', 'edit', '-1', '0', 'Die Entwickler Sprache kann nicht gelöscht werden.', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'error_result', '-555504947.edit-single', 0, '/admin/menued', 'add', '-1', '0', 'DB Fehler: ', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'extended', '-555504947.edit-single', 0, '/admin/menued', 'add', '-1', '0', 'Speziell', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'exturl', '-555504947.edit-single', 0, '/admin/menued', 'add', '-1', '0', 'ext. Url', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'hide', '-555504947.edit-single', 0, '/admin/menued', 'edit', '-1', '0', 'Versteckt', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'label', '-555504947.edit-single', 0, '/admin/menued', 'add', '-1', '0', 'Bezeichnung', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'lang', '-555504947.edit-single', 0, '/admin/menued', 'add', '-1', '0', 'Sprache', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'level', '-555504947.edit-single', 0, '/admin/menued', 'add', '-1', '0', 'benötigter Level', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'madatory', '-555504947.edit-single', 0, '/admin/menued', 'add', '-1', '0', 'Erzwungen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'new_lang', '-555504947.edit-single', 0, '/admin/menued', 'add', '-1', '0', 'Neue Sprache', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'refid', '-555504947.edit-single', 0, '/admin/menued', 'add', '-1', '0', 'Ref ID.', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'reset', '-555504947.edit-single', 0, '/admin/menued', 'edit', '-1', '0', 'Zurücksetzen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'send', '-555504947.edit-single', 0, '/admin/menued', 'edit', '-1', '0', 'Abschicken', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'sort', '-555504947.edit-single', 0, '/admin/menued', 'add', '-1', '0', 'Sortierung', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'template', '-555504947.edit-single', 0, '/admin/menued', 'add', '-1', '0', 'Template', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', '-555504947.edit-single', 0, '/admin/menued', 'edit', '-1', '0', 'Menü-Editor - Menüpunkt bearbeiten', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'button_desc_add', '-555504947.list', 0, '/admin/menued', 'list', '-1', '0', 'Unterpunkt hinzufügen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'button_desc_delete', '-555504947.list', 0, '/admin/menued', 'list', '-1', '0', 'Löschen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'button_desc_down', '-555504947.list', 0, '/admin/menued', 'list', '-1', '0', 'Sortierung - Nach unten', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'button_desc_edit', '-555504947.list', 0, '/admin/menued', 'list', '-1', '0', 'Bearbeiten', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'button_desc_move', '-555504947.list', 0, '/admin/menued', 'list', '-1', '0', 'Im Menü Baum verschieben', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'button_desc_up', '-555504947.list', 0, '/admin/menued', 'list', '-1', '0', 'Sortierung - Nach oben', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'disabled', '-555504947.list', 0, '/admin/menued', 'list', '-1', '0', 'Abgeschaltet', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'enabled', '-555504947.list', 0, '/admin/menued', 'list', '-1', '0', 'Eingeschaltet', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'error1', '-555504947.list', 0, '/admin/menued', 'list', '-1', '0', 'Menüpunkte mit Unterpunkten lassen sich nicht löschen.', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'extern', '-555504947.list', 0, '/admin/menued', 'list', '-1', '0', '(extern)', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', 'my', 0, '', 'my', '-1', '0', 'Modul Beispiel "my" einfach', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'renumber', '-555504947.list', 0, '/admin/menued', 'list', '-1', '0', 'Neu durchnummerieren', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', '-555504947.list', 0, '/admin/menued', 'list', '-1', '0', 'Menu-Editor - Übersicht', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'abort', '-555504947.move', 0, '/admin/menued', 'move', '-1', '0', 'Abbrechen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'entry', '-555504947.move', 0, '/admin/menued', 'move', '-1', '0', 'Eintrag', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'extern', '-555504947.move', 0, '/admin/menued', 'move', '-1', '0', '(extern)', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'reset', '-555504947.move', 0, '/admin/menued', 'move', '-1', '0', 'Zurücksetzen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'root', '-555504947.move', 0, '/admin/menued', 'move', '-1', '0', 'Ins Hauptmenü', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'send', '-555504947.move', 0, '/admin/menued', 'move', '-1', '0', 'Abschicken', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', '-555504947.move', 0, '/admin/menued', 'move', '-1', '0', 'Menü-Editor - Menüpunkt verschieben', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'send', '852881080.modify', 0, '/admin/passed', 'modify', '-1', '0', 'Abschicken', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'chkpass', '852881080.modify', 0, '/admin/passed', 'modify', '-1', '0', 'Wiederholung', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'inhalt', '852881080.modify', 0, '/admin/passed', 'modify', '-1', '0', 'Passwort ändern', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'newpass', '852881080.modify', 0, '/admin/passed', 'modify', '-1', '0', 'Neues', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'oldpass', '852881080.modify', 0, '/admin/passed', 'modify', '-1', '0', 'Altes', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', '852881080.modify', 0, '/admin/passed', 'modify', '-1', '0', 'Passwort Editor', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', 'auth', 0, '', 'index', '-1', '0', 'Überschrift', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'desc', 'auth.logout', 0, '', 'index', '-1', '0', 'Werkzeuge', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'fileed', 'auth.logout', 0, '', 'index', '-1', '0', 'Datei-Editor', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'leveled', 'auth.logout', 0, '', 'index', '-1', '0', 'Level-Editor', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'menued', 'auth.logout', 0, '', 'index', '-1', '0', 'Menü-Editor', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'nachher', 'auth.logout', 0, '', 'index', '-1', '0', 'ist angemeldet.', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'passed', 'auth.logout', 0, '', 'index', '-1', '0', 'Passwort-Editor', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'usered', 'auth.logout', 0, '', 'index', '-1', '0', 'User-Editor', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'vorher', 'auth.logout', 0, '', 'index', '-1', '0', 'Benutzer', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'abort', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Abbrechen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'add', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Datei einfügen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'b', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Fett', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'big', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Grösser als der Rest', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'br', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Erzwungener Umbruch', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'cent', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Zentriert', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'center', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Zentriert', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'cite', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Logisch: cite', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'col', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Tabellenspalte', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'db', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'DB', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'div', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Bereich', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'e', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Mail', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'em', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Logisch: emphatisch', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'email', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'eMail Link', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'file', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Datei', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'files', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Dateien', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'h1', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Überschrift Klasse 1', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'h2', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Überschrift Klasse 2', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'hl', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Spezielle Trennlinie', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'hr', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Trennlinie', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'i', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Kursiv', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'img', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Bild', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'imgb', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Bild mit Rahmen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'in', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Initial', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'label', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Marke', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'language', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Sprache', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'link', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Link', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'list', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Liste', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'm1', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Menü dieser Ebene', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'm2', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Menü der Unterebene', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'pre', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Vorformatiert', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'quote', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'In Anführungszeichen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'row', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Tabellenzeile', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 's', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Durchgestrichen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'save', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Speichern', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'small', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Kleiner als der Rest', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'sp', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Geschütztes Leerzeichen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'strong', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Logisch: strong', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'sub', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Tiefgestellt', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'sup', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Hochgestellt', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'tab', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Tabelle', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'tagselect', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Tag auswählen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'template', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Template', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'tt', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Dickengleich', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'u', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Unterstrichen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'up', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Zurück-Link', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'upload', 'cms.edit.cmstag', 0, '', 'index', '-1', '0', 'Hinaufladen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'inhalt', '404', 0, '', 'fehlt', '-1', '0', '[H1]Fehler 404 - Nicht gefunden.[/H1]\r\n\r\n[P]Die Uri !#ausgaben_404seite wurde nicht gefunden.\r\n\r\nLeider konnte das System nicht feststellen woher sie gekommen sind[/P].', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'modcol', 'global', 0, '/admin/leveled', 'list', '-1', '0', 'Funktionen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'error_dupe', '-555504947.edit-single', 0, '/admin/menued', 'add', '-1', '0', 'Der Eintrag ist bereits vorhanden.', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'inhalt', '404referer', 0, '', 'fehlt', '-1', '0', '[H1]Fehler 404 - Nicht gefunden.[/H1]\r\n\r\n[P]Die Uri: !#ausgaben_404seite wurde nicht gefunden.\r\n\r\nDie [LINK=!#ausgaben_404referer]Seite[/LINK] enthaelt einen falschen/alten Link.[/P]', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'error_dupe', '-555504947.edit-multi', 0, '/admin/menued', 'edit', '-1', '0', 'Der Eintrag ist bereits vorhanden.', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'error_dupe', '-555504947.move', 0, '/admin/menued', 'move', '-1', '0', 'In dieser Ebene existiert bereits ein Eintrag mit gleichem Namen.', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'logout', 'auth.login', 0, '', 'auth.login', '-1', '0', 'Abgemeldet', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'denied', 'auth.login', 0, '', 'auth.login', '-1', '0', 'Zugriff verweigert!', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'picture', '-555504947.edit-multi', 0, '/admin/menued', 'edit', '-1', '0', 'evt. Bild', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'picture', '-555504947.edit-single', 0, '/admin/menued', 'edit', '-1', '0', 'evt. Bild', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'reset', '852881080.modify', 0, '/admin/passed', 'modify', '-1', '0', 'Zurücksetzen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'abort', '852881080.modify', 0, '/admin/passed', 'modify', '-1', '0', 'Abbrechen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', '-840786483.list', 0, '/admin/menued', 'list', '-1', '0', 'Level-Editor - Übersicht', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', '-840786483.modify', 0, '/admin/menued', 'edit', '-1', '0', 'Level-Editor - Bearbeiten', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'level', '-840786483.modify', 0, '/admin/leveled', 'modify', '-1', '0', 'Bezeichnung', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'description', '-840786483.modify', 0, '/admin/leveled', 'modify', '-1', '0', 'Beschreibung', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'del', '-840786483.modify', 0, '/admin/leveled', 'edit', '-1', '0', 'Entfernen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'add', '-840786483.modify', 0, '/admin/leveled', 'edit', '-1', '0', 'Hinzufügen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'login', '210295197.list', 0, '/admin/usered', 'list', '-1', '0', 'Login', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'send', '-840786483.delete', 0, '/admin/leveled', 'modify', '-1', '0', 'Löschen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'abort', '-840786483.delete', 0, '/admin/leveled', 'modify', '-1', '0', 'Abbrechen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'frage', '-840786483.delete', 0, '/admin/leveled', 'modify', '-1', '0', 'Wollen Sie den Level "!#ausgaben_level" wirklich löschen?', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'level', '-840786483.details', 0, '/admin/leveled', 'details', '-1', '0', 'Bezeichnung', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'user', '-840786483.details', 0, '/admin/leveled', 'details', '-1', '0', 'Mitglieder', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'beschreibung', '-840786483.details', 0, '/admin/leveled', 'details', '-1', '0', 'Beschreibung', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'edit', '-840786483.details', 0, '/admin/leveled', 'details', '-1', '0', 'Bearbeiten', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'list', '-840786483.details', 0, '/admin/leveled', 'details', '-1', '0', 'Übersicht', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', '-840786483.details', 0, '/admin/leveled', 'details', '-1', '0', 'Level Editor - Eigenschaften', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', '-840786483.delete', 0, '/admin/leveled', 'modify', '-1', '0', 'Level-Editor - Löschen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', '210295197.list', 0, '/admin/usered', 'list', '-1', '0', 'User-Editor - Übersicht', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', '-939795212.list', 0, '/admin/fileed', 'list', '-1', '0', 'Datei-Editor - Übersicht', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'search', '-939795212.list', 0, '/admin/fileed', 'list', '-1', '0', 'Suche', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'gesamt', '-939795212.list', 0, '/admin/fileed', 'list', '-1', '0', 'Gesamt:', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'send', '-939795212.list', 0, '/admin/fileed', 'list', '-1', '0', 'Go', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'fileedit', '-939795212.list', 0, '/admin/fileed', 'list', '-1', '0', 'Bearbeiten', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'filedelete', '-939795212.list', 0, '/admin/fileed', 'list', '-1', '0', 'Löschen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ffname', '-939795212.modify', 0, '/admin', 'usered', '-1', '0', 'Dateiname', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'fdesc', '-939795212.modify', 0, '/admin', 'usered', '-1', '0', 'Bildbeschreibung', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'funder', '-939795212.modify', 0, '/admin', 'usered', '-1', '0', 'Bildunterschrift', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'fhit', '-939795212.modify', 0, '/admin', 'usered', '-1', '0', 'Schlagworte', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'upa', '-939795212.modify', 0, '/admin', 'usered', '-1', '0', 'Die aktuelle Datei durch', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'upb', '-939795212.modify', 0, '/admin', 'usered', '-1', '0', 'ersetzen.', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'send', '-939795212.modify', 0, '/admin', 'usered', '-1', '0', 'Abschicken', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'reset', '-939795212.modify', 0, '/admin', 'usered', '-1', '0', 'Zurücksetzen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'abort', '-939795212.modify', 0, '/admin', 'usered', '-1', '0', 'Abbrechen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'cmslink', '-939795212.list', 0, '/admin/fileed', 'list', '-1', '0', 'zum Content Editor', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'level', '-840786483.list', 0, '/admin/leveled', 'list', '-1', '0', 'Bezeichnung', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'beschreibung', '-840786483.list', 0, '/admin/leveled', 'list', '-1', '0', 'Beschreibung', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'delete', 'global', 0, '/admin/leveled', 'list', '-1', '0', 'Löschen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'edit', 'global', 0, '/admin/leveled', 'list', '-1', '0', 'Bearbeiten', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'back', 'global', 0, '/admin/leveled', 'details', '-1', '0', 'Zurück', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'members', '-840786483.delete', 0, '/admin/leveled', 'delete', '-1', '0', 'Mitglieder', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', '210295197.modify', 0, '/admin/usered', 'modify', '-1', '0', 'User-Editor - Bearbeiten', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'error_oldpass', '852881080.modify', 0, '/admin/passed', 'modify', '-1', '0', 'Das alte Passwort stimmt nicht!', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'error_chkpass', '852881080.modify', 0, '/admin/passed', 'modify', '-1', '0', 'Das Neue Passwort und die Wiederholung stimmen nicht überein!', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'nachname', '210295197.modify', 0, '/admin/usered', 'modify', '-1', '0', 'Nachname', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'vorname', '210295197.modify', 0, '/admin/usered', 'modify', '-1', '0', 'Vorname', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'email', '210295197.modify', 0, '/admin/usered', 'modify', '-1', '0', 'eMail', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'send', 'global', 0, '/admin/usered', 'edit', '-1', '0', 'Abschicken', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'frage', '210295197.delete', 0, '/admin/usered', 'modify', '-1', '0', 'Wollen Sie den User "!#ausgaben_username" wirklich löschen?', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'login', '210295197.details', 0, '/admin/usered', 'details', '-1', '0', 'Login', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', '210295197.delete', 0, '/admin/usered', 'modify', '-1', '0', 'User-Editor - Löschen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', '210295197.details', 0, '/admin/usered', 'details', '-1', '0', 'User-Editor - Eigenschaften', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'username', '210295197.modify', 0, '/admin/usered', 'modify', '-1', '0', 'Login', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'newpass', '210295197.modify', 0, '/admin/usered', 'modify', '-1', '0', 'Passwort', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'chkpass', '210295197.modify', 0, '/admin/usered', 'modify', '-1', '0', 'Wiederholung', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', 'base', 0, '', 'impressum', '-1', '0', 'Menu', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'copyright', 'base', 0, '', 'index', '-1', '0', 'eWeBuKi - Copyright 2003-2007', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'kekse', 'base', 0, '', 'impressum', '-1', '0', 'Kekse', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'bloged', 'auth.logout', 0, '/admin/passed', 'modify', '-1', '0', 'Blog-Editor', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'send', '-939795212.delete', 0, '/admin/menued', 'list', '-1', '0', 'Abschicken', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'abort', '-939795212.delete', 0, '/admin/menued', 'list', '-1', '0', 'Abbrechen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', '-939795212.delete', 0, '/admin/menued', 'delete', '-1', '0', 'Datei Editor - Datei löschen!', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'inhalt', '-939795212.delete', 0, '/admin/menued', 'delete', '-1', '0', 'Die Datei "!#ausgaben_ffname" wirklich löschen?', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', '-939795212.upload', 0, '/admin/menued', 'list', '-1', '0', 'Datei-Editor Upload', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'file', '-939795212.upload', 0, '/admin/menued', 'list', '-1', '0', 'Dateiauswahl', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'send', '-939795212.upload', 0, '/admin/menued', 'list', '-1', '0', 'Abschicken', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'reset', '-939795212.upload', 0, '/admin/menued', 'edit', '-1', '0', 'Zurücksetzen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'abort', '-939795212.upload', 0, '/admin/menued', 'edit', '-1', '0', 'Abbrechen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', '-939795212.modify', 0, '/admin/menued', 'add', '-1', '0', 'Datei-Editor - Datei Eigenschaften bearbeiten', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'answera', '-939795212.list', 0, '/admin/fileed', 'list', '-1', '0', 'Ihre Schnellsuche nach', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'answerb', '-939795212.list', 0, '/admin/fileed', 'list', '-1', '0', 'hat', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'answerc_no', '-939795212.list', 0, '/admin/fileed', 'list', '-1', '0', 'keine Einträge gefunden.', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'answerc_yes', '-939795212.list', 0, '/admin/fileed', 'list', '-1', '0', 'folgende Einträge gefunden.', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'next', '-939795212.list', 0, '/admin/fileed', 'list', '-1', '0', 'Vorherige Seite', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', '!krompi'),
('de', 'prev', '-939795212.list', 0, '/admin/fileed', 'list', '-1', '0', 'Nexte Seite', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'error1', '-939795212.list', 0, '/admin/fileed', 'list', '-1', '0', 'Bild wird bereits verwendet - Bearbeiten zeigt wo.', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'error2', '-939795212.list', 0, '/admin/fileed', 'list', '-1', '0', 'Bild kann nur vom Eigentümer gelöscht werden.', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'error_edit', '-939795212.modify', 0, '/admin/fileed', 'edit', '-1', '0', 'Bild kann nur vom Eigentümer bearbeitet werden.', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'references', '-939795212.modify', 0, '/admin/fileed', 'edit', '-1', '0', 'Ist enthalten in:', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'details', 'global', 0, '/admin/leveled', 'list', '-1', '0', 'Details', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'new', 'global', 0, '/admin/leveled', 'list', '-1', '0', 'Neuer Eintrag', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'special', '210295197.delete', 0, '/admin/usered', 'delete', '-1', '0', 'Spezial Rechte', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'reset', 'global', 0, '/admin/usered', 'edit', '-1', '0', 'Zurücksetzen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'abort', 'global', 0, '/admin/usered', 'edit', '-1', '0', 'Abbrechen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'del', '210295197.modify', 0, '/admin/usered', 'edit', '-1', '0', 'Nehmen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'actual', '210295197.modify', 0, '/admin/usered', 'edit', '-1', '0', 'Besitzt', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'avail', '210295197.modify', 0, '/admin/usered', 'edit', '-1', '0', 'Verfügbar', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'add', '210295197.modify', 0, '/admin/usered', 'edit', '-1', '0', 'Geben', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'actual', '-840786483.modify', 0, '/admin/leveled', 'edit', '-1', '0', 'Mitglieder', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'avail', '-840786483.modify', 0, '/admin/leveled', 'edit', '-1', '0', 'Verfügbar', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'surname', '210295197.list', 0, '/admin/usered', 'list', '-1', '0', 'Nachname', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'forename', '210295197.list', 0, '/admin/usered', 'list', '-1', '0', 'Vorname', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'right', '210295197.delete', 0, '/admin/usered', 'delete', '-1', '0', 'Rechte', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', '-2051315182.list', 0, '/admin/bloged', 'list', '-1', '0', 'Blog-Editor - Übersicht', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'anzahl', 'global', 0, '/admin/leveled', 'list', '-1', '0', 'Einträge: ', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'surname', '210295197.details', 0, '/admin/usered', 'details', '-1', '0', 'Nachname', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'forename', '210295197.details', 0, '/admin/usered', 'details', '-1', '0', 'Vorname', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'email', '210295197.details', 0, '/admin/usered', 'details', '-1', '0', 'E-Mail', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'level', '210295197.details', 0, '/admin/usered', 'details', '-1', '0', 'Rechte', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'field1', 'my', 0, '', 'my', '-1', '0', 'Feld 1', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'field2', 'my', 0, '', 'my', '-1', '0', 'Feld 2', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'inhalt', 'my', 0, '', 'my', '-1', '0', 'Beispiel für eine einfache Funktion.', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', '-1468826685.list', 0, '/dir/my', 'list', '-1', '0', 'Modul Beispiel "my" erweitert - Übersicht', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'field1', '-1468826685.list', 0, '/dir/my', 'list', '-1', '0', 'Feld 1', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', '-1468826685.modify', 0, '/dir/my', 'edit', '-1', '0', 'Modul Beispiel "my" erweitert - Bearbeiten', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'field1', '-1468826685.modify', 0, '/admin/leveled', 'list', '-1', '0', 'Feld 1', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'field2', '-1468826685.modify', 0, '/dir/my', 'edit', '-1', '0', 'Feld 2', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', '-1468826685.delete', 0, '/dir/my', 'delete', '-1', '0', 'Modul Beispiel "my" erweitert - Löschen', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'field1', '-1468826685.delete', 0, '/dir/my', 'delete', '-1', '0', 'Feld 1', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'field2', '-1468826685.delete', 0, '/dir/my', 'delete', '-1', '0', 'Feld 2', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', '-1468826685.details', 0, '/dir/my', 'details', '-1', '0', 'Modul Beispiel "my" erweitert - Details', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'field1', '-1468826685.details', 0, '/dir/my', 'details', '-1', '0', 'Feld 1', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'field2', '-1468826685.details', 0, '/dir/my', 'details', '-1', '0', 'Feld 2', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'inhalt', 'demo', 0, '', 'demo', '-1', '0', '\r\n\r\n', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'ueberschrift', '-102562964.list', 1, '/admin/grouped', 'list', '-1', '0', 'Gruppen-Editor - Übersicht', '2007-11-20 13:42:49', 'Ammon', 'Werner', 'chaot@chaos.de', 'chaot'),
('de', 'filelist', 'global', 1, '/admin/fileed', 'list', '-1', '0', 'Datei-Editor', '2007-11-20 13:49:49', 'Krompass', 'Mathias', 'nix@da.de', 'krompi'),
('de', 'filecompilation', 'global', 1, '/admin/fileed', 'list', '-1', '0', 'Galerien', '2007-11-20 13:50:00', 'Krompass', 'Mathias', 'nix@da.de', 'krompi'),
('de', 'fileupload', 'global', 1, '/admin/fileed', 'list', '-1', '0', 'Upload', '2007-11-20 13:50:14', 'Krompass', 'Mathias', 'nix@da.de', 'krompi'),
('de', 'group', '-939795212.list', 1, '/admin/fileed', 'list', '-1', '0', 'Gruppe', '2007-11-20 13:52:04', 'Krompass', 'Mathias', 'nix@da.de', 'krompi'),
('de', 'search', '-939795212.compilation', 1, '/admin/fileed', 'list', '-1', '0', 'Suche', '2007-11-20 13:52:56', 'Krompass', 'Mathias', 'nix@da.de', 'krompi'),
('de', 'compilation_search', '-939795212.compilation', 1, '/admin/fileed', 'compilation', '-1', '0', 'Galerien', '2007-11-20 13:53:16', 'Krompass', 'Mathias', 'nix@da.de', 'krompi'),
('de', 'compilation', '-939795212.compilation', 1, '/admin/fileed', 'compilation', '-1', '0', 'Galerie', '2007-11-20 13:54:09', 'Krompass', 'Mathias', 'nix@da.de', 'krompi'),
('de', 'of', '-939795212.compilation', 1, '/admin/fileed', 'list', '-1', '0', 'von', '2007-11-20 13:57:01', 'Krompass', 'Mathias', 'nix@da.de', 'krompi'),
('de', 'group', '-102562964.list', 1, '/admin/grouped', 'list', '-1', '0', 'Gruppe', '2007-11-20 13:58:38', 'Morhart', 'Günther', 'nix@da.de', 'buffy'),
('de', 'file_error0', 'global', 1, '/admin/fileed', 'upload', '-1', '0', 'Kein Fehler, Datei entspricht den Vorgaben', '2007-11-20 13:58:44', 'Krompass', 'Mathias', 'nix@da.de', 'krompi'),
('de', 'beschreibung', '-102562964.list', 1, '/admin/grouped', 'list', '-1', '0', 'Beschreibung', '2007-11-20 13:58:56', 'Morhart', 'Günther', 'nix@da.de', 'buffy'),
('de', 'file_error1', 'global', 1, '/admin/fileed', 'upload', '-1', '0', 'Die hochgeladene Datei überschreitet die Größenbeschränkung "upload_max_filesize" der php.ini!', '2007-11-20 13:59:05', 'Krompass', 'Mathias', 'nix@da.de', 'krompi'),
('de', 'file_error2', 'global', 1, '/admin/fileed', 'upload', '-1', '0', 'Die hochgeladene Datei überschreitet die im Formular festgelegte "MAX_FILE_SIZE"-Anweisung!', '2007-11-20 13:59:19', 'Krompass', 'Mathias', 'nix@da.de', 'krompi'),
('de', 'group', '-102562964.modify', 3, '/admin/grouped', 'add', '-1', '0', 'Gruppe', '2007-11-20 15:56:34', 'Morhart', 'Günther', 'nix@da.de', 'buffy'),
('de', 'inhalt', 'fehler', 0, '', 'fehler', '-1', '0', '[H1]404 Test[/H1]\r\n\r\n\r\n[P]Hiermit wird die 404 Fehlerseite angezeigt.\r\n\r\n[LINK=fehlt.html]404 Fehler mit Referer[/LINK]\r\n\r\nUm die zweite 404 Fehlermeldung (Referer unbekannt) sichtbar zu machen,\r\nin der Eingabezeile der obigen 404 Fehlermeldung einfach Enter drücken.[/P]', '2007-07-28 13:43:45', 'Ammon', 'Werner', 'chaot@chaos.de', 'chaot'),
('de', 'inhalt', 'impressum', 0, '', 'impressum', '-1', '0', '[H1]Impressum[/H1]\r\n\r\n\r\n[P]eWeBuKi - Copyright 2003-2007\r\nby [EMAIL=w.ammon(at)chaos.de]Werner Ammon[/EMAIL][/P]\r\n\r\n\r\n[H2]Weitere Infoseiten:[/H2]\r\n\r\n\r\n[P][LINK=http://www.ewebuki.de/]www.ewebuki.de[/LINK]\r\n[LINK=http://developer.berlios.de/projects/ewebuki/]developer.berlios.de/projects/ewebuki/[/LINK][/P]', '2007-09-14 22:16:17', 'Ammon', 'Werner', 'chaot@chaos.de', 'chaot'),
('de', 'inhalt', 'index', 0, '', 'index', '-1', '0', '[H1]Glückwunsch Ihr eWeBuKi läuft![/H1]\r\n\r\n[P]Um sich am System anzumelden benutzen Sie bitte folgende Daten:\r\n\r\nuser: ewebuki\r\npass: ewebuki[/P]\r\n\r\n[P=box][B]ACHTUNG:[/B] Passwort ändern nicht vergessen![/P]\r\n\r\n[P]Weitere Infoseiten:\r\n[LINK=http://www.ewebuki.de/]www.ewebuki.de[/LINK]\r\n[LINK=http://developer.berlios.de/projects/ewebuki/]developer.berlios.de/projects/ewebuki/[/LINK][/P]\r\n', '2007-09-14 22:16:39', 'Ammon', 'Werner', 'chaot@chaos.de', 'chaot');
INSERT INTO `site_text` (`lang`, `label`, `tname`, `version`, `ebene`, `kategorie`, `crc32`, `html`, `content`, `changed`, `bysurname`, `byforename`, `byemail`, `byalias`) VALUES
('de', 'inhalt', 'show', 0, '', 'show', '-1', '0', '[H1]eWeBuKi Show[/H1]\r\n\r\n\r\n[H2]Tabellen Positionen[/H2]\r\n\r\n[TAB=;300;1]\r\n[ROW]\r\n[COL]1,1[/COL]\r\n[COL=;;u]1,2[/COL]\r\n[COL=r]1,3\r\n\r\n\r\n[/COL]\r\n[/ROW][ROW]\r\n[COL=m]2,1[/COL]\r\n[COL=;;g]2,2[/COL]\r\n[COL=r;;m]2,3\r\n\r\n\r\n[/COL]\r\n[/ROW]\r\n[/TAB]\r\n\r\n\r\n[H2]Easy Template Links[/H2]\r\n\r\n[P]!#lnk_1\r\n!#lnk_2\r\n!#lnk_3[/P]\r\n\r\n[H2]Menu oberhalb (M1,mit Bez.)[/H2]\r\n[M1]nach oben[/M1]\r\n\r\n[H2]Menu oberhalb als Liste (M1=l,ohne Bez.)[/H2]\r\n[M1=l][/M1]\r\n\r\n[H2]Menu gleiche Ebene (M2,mit Bez.)[/H2]\r\n[M2]nach oben[/M2]\r\n\r\n[H2]Menu gleiche Ebene als Liste (M2=l,mit Bez.)[/H2]\r\n[M2=l][/M2]\r\n\r\n\r\n\r\n[H2]Tabellen Abstände[/H2]\r\n[P]Tabellen Abstände (abstand text - tabelle 1)[/P]\r\n\r\n\r\n[TAB=;300;1]\r\n[ROW]\r\n\r\n[COL=l;150]links oben\r\n[/COL]\r\n\r\n[COL=l;150]rechts oben\r\n[/COL]\r\n\r\n[/ROW]\r\n[/TAB]\r\n\r\n[TAB=;300;1]\r\n[ROW]\r\n\r\n[COL=l;150]links oben\r\n[/COL]\r\n\r\n[COL=l;150]rechts oben\r\n[/COL]\r\n\r\n[/ROW]\r\n[/TAB]\r\n\r\n\r\n[P]Tabellen Abstände (abstand text - tabelle 2)[/P]\r\n\r\n[IN]I[/IN]nitial fuer Texte\r\n\r\n[H1][B][EM]Bold EM Tag[/EM][/B] im H1 Tag[/H1]\r\n\r\nText zwischen Linien:\r\n[HL][/HL]\r\nHier kommt der Text.\r\n[HL][/HL]\r\n\r\n[H2]Bilder im Text[/H2]\r\n\r\n[P][IMG=/file/picture/small/img_1.png;l;;;20;;20]eWeBuKi Logo[/IMG]Weit hinten, hinter den Wortbergen, fern der Länder Vokalien und Konsonantien leben die Blindtexte. Abgeschieden wohnen Sie in Buchstabhausen an der Küste des Semantik, eines großen Sprachozeans. Ein kleines Bächlein namens Duden fließt durch ihren Ort und versorgt sie mit den nötigen Regelialien. Es ist ein paradiesmatisches Land, in dem einem gebratene Satzteile in den Mund fliegen. Nicht einmal von der allmächtigen Interpunktion werden die Blindtexte beherrscht – ein geradezu unorthographisches Leben.[/P]\r\n\r\n[H2]Mehrere Bilder rechts[/H2]\r\n\r\n[P]Bei mehreren Bildern rechts gibt es Abstand Probleme. Um das zu umgehen muss der Umlauf mit dem Tag BR=a angehalten werden.[/P]\r\n\r\n[IMGB=/file/picture/small/img_1.png;r;0;b]Logo[/IMGB]Text neben Bild 1[BR=a][/BR]\r\n\r\n[IMGB=/file/picture/small/img_1.png;r]Logo[/IMGB]Text neben Bild 2[BR=a][/BR]\r\n\r\n[P]Nicht nur Bilder sondern auch Text kann mit diesem Trick unter das Bild geschoben werden.[/P]\r\n[H1]ueberschrift h1[/H1]\r\n[H2]ueberschrift h2[/H2]\r\n[H3]ueberschrift h3[/H3]\r\n[H4]ueberschrift h4[/H4]\r\n[H5]ueberschrift h5[/H5]\r\n[H6]ueberschrift h6[/H6]\r\n\r\nAbsaetze mit css einstellen:\r\n[P]Im Absatz ist es Schoen[/P]\r\n\r\nDIV=class jeder css im Content:\r\n[DIV=anderst]Dieser Text ist schoener als der Rest[/DIV]', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'file_error3', 'global', 1, '/admin/fileed', 'upload', '-1', '0', 'Die Datei wurde nur teilweise hochgeladen!', '2007-11-20 13:59:40', 'Krompass', 'Mathias', 'nix@da.de', 'krompi'),
('de', 'file_error4', 'global', 1, '/admin/fileed', 'upload', '-1', '0', 'Es wurde keine Datei hochgeladen!', '2007-11-20 14:00:51', 'Krompass', 'Mathias', 'nix@da.de', 'krompi'),
('de', 'file_error6', 'global', 1, '/admin/fileed', 'upload', '-1', '0', 'Es ist kein temporäres Upload-Verzeichnis verfügbar!', '2007-11-20 14:01:32', 'Krompass', 'Mathias', 'nix@da.de', 'krompi'),
('de', 'file_error7', 'global', 1, '/admin/fileed', 'upload', '-1', '0', 'Es kann nicht auf die Platte geschrieben werden!', '2007-11-20 14:02:17', 'Krompass', 'Mathias', 'nix@da.de', 'krompi'),
('de', 'file_error8', 'global', 1, '/admin/fileed', 'upload', '-1', '0', 'Der Upload wurde von einer Erweiterung verhindert!', '2007-11-20 14:02:41', 'Krompass', 'Mathias', 'nix@da.de', 'krompi'),
('de', 'file_error10', 'global', 1, '/admin/fileed', 'upload', '-1', '0', 'Die Datei ist zu groß!', '2007-11-20 14:02:58', 'Krompass', 'Mathias', 'nix@da.de', 'krompi'),
('de', 'file_error11', 'global', 1, '/admin/fileed', 'upload', '-1', '0', 'Ungültiges Dateiformat!', '2007-11-20 14:03:31', 'Krompass', 'Mathias', 'nix@da.de', 'krompi'),
('de', 'file_error12', 'global', 1, '/admin/fileed', 'upload', '-1', '0', 'Die Datei ist schon vorhanden!', '2007-11-20 14:04:00', 'Krompass', 'Mathias', 'nix@da.de', 'krompi'),
('de', 'file_error13', 'global', 1, '/admin/fileed', 'upload', '-1', '0', 'Unbekannter Fehler. Eventuell ist die "post_max_size" der php.ini die Ursache. Bitte nicht weiter probieren!', '2007-11-20 14:04:19', 'Krompass', 'Mathias', 'nix@da.de', 'krompi'),
('de', 'file_error14', 'global', 1, '/admin/fileed', 'upload', '-1', '0', 'Es wird mindestens die PHP-Version 4.x.x benötigt!', '2007-11-20 14:04:33', 'Krompass', 'Mathias', 'nix@da.de', 'krompi'),
('de', 'avail', '-102562964.modify', 1, '/admin/grouped', 'add', '-1', '0', 'Verfügbar', '2007-11-20 14:08:07', 'Morhart', 'Günther', 'nix@da.de', 'buffy'),
('de', 'actual', '-102562964.modify', 1, '/admin/grouped', 'edit', '-1', '0', 'Mitglieder', '2007-11-20 14:10:20', 'Morhart', 'Günther', 'nix@da.de', 'buffy'),
('de', 'del', '-102562964.modify', 1, '/admin/grouped', 'edit', '-1', '0', 'Entfernen', '2007-11-20 14:11:54', 'Morhart', 'Günther', 'nix@da.de', 'buffy'),
('de', 'add', '-102562964.modify', 1, '/admin/grouped', 'edit', '-1', '0', 'Hinzufügen', '2007-11-20 14:12:06', 'Morhart', 'Günther', 'nix@da.de', 'buffy'),
('de', 'description', '-102562964.modify', 1, '/admin/grouped', 'edit', '-1', '0', 'Beschreibung', '2007-11-20 14:12:23', 'Morhart', 'Günther', 'nix@da.de', 'buffy'),
('de', 'frage', '-102562964.delete', 1, '/admin/grouped', 'delete', '-1', '0', 'Wollen Sie die Gruppe "!#ausgaben_group" wirklich löschen ?', '2007-11-20 15:54:47', 'Morhart', 'Günther', 'nix@da.de', 'buffy'),
('de', 'members', '-102562964.delete', 1, '/admin/grouped', 'delete', '-1', '0', 'Mitglieder', '2007-11-20 15:55:27', 'Morhart', 'Günther', 'nix@da.de', 'buffy'),
('de', 'user', '-102562964.details', 1, '/admin/grouped', 'details', '-1', '0', 'Mitglieder', '2007-11-20 15:55:47', 'Morhart', 'Günther', 'nix@da.de', 'buffy'),
('de', 'group', '-102562964.details', 1, '/admin/grouped', 'details', '-1', '0', 'Gruppe', '2007-11-20 15:56:02', 'Morhart', 'Günther', 'nix@da.de', 'buffy'),
('de', 'beschreibung', '-102562964.details', 1, '/admin/grouped', 'details', '-1', '0', 'Beschreibung', '2007-11-20 15:56:15', 'Morhart', 'Günther', 'nix@da.de', 'buffy'),
('de', 'ueberschrift', '-102562964.details', 1, '/admin/grouped', 'details', '-1', '0', 'Gruppen-Editor - Eigenschaften', '2007-11-20 15:57:35', 'Morhart', 'Günther', 'nix@da.de', 'buffy'),
('de', 'ueberschrift', '-102562964.delete', 1, '/admin/grouped', 'delete', '-1', '0', 'Gruppen-Editor - Löschen', '2007-11-20 15:58:21', 'Morhart', 'Günther', 'nix@da.de', 'buffy'),
('de', 'ueberschrift', '-102562964.modify', 1, '/admin/grouped', 'edit', '-1', '0', 'Gruppen-Editor - Bearbeiten', '2007-11-20 15:58:59', 'Morhart', 'Günther', 'nix@da.de', 'buffy'),
('de', 'error_dupe', '-102562964.modify', 1, '/admin/grouped', 'add', '-1', '0', 'Fehler: Es gibt bereits eine Gruppe mit diesem Namen !', '2007-11-21 11:04:51', 'Morhart', 'Günther', 'nix@da.de', 'buffy');
