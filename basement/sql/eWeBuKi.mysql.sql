-- phpMyAdmin SQL Dump
-- version 2.10.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 28. Juli 2007 um 13:46
-- Server Version: 4.0.24
-- PHP-Version: 4.3.10-22

--
-- Datenbank: `ewebuki_mdesvn`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `auth_level`
--

CREATE TABLE `auth_level` (
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
-- Tabellenstruktur für Tabelle `auth_right`
--

CREATE TABLE `auth_right` (
  `rid` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `lid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`rid`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `auth_right`
--

INSERT INTO `auth_right` (`rid`, `uid`, `lid`) VALUES
(1, 1, 1),
(2, 1, 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `auth_special`
--

CREATE TABLE `auth_special` (
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

CREATE TABLE `auth_user` (
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

CREATE TABLE `db_leer` (
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

CREATE TABLE `site_file` (
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

CREATE TABLE `site_form` (
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
) TYPE=MyISAM AUTO_INCREMENT=9 ;

--
-- Daten für Tabelle `site_form`
--

INSERT INTO `site_form` (`fid`, `flabel`, `ftname`, `fsize`, `fclass`, `fstyle`, `foption`, `frequired`, `fcheck`) VALUES
(1, 'username', '210295197.modify', '0', '', '', NULL, '-1', ''),
(2, 'pass', '210295197.modify', '0', '', '', 'password', '-1', ''),
(3, 'pass', '852881080.modify', '0', '', '', 'password', '-1', ''),
(4, 'fid', '-939795212.modify', '0', '', '', 'hidden', '-1', ''),
(5, 'entry', '-555504947.edit', '0', '', '', NULL, '-1', 'PREG:^[a-z_.-0-9]+$'),
(6, 'fdesc', '-939795212.modify', '25', '', '', NULL, '0', ''),
(7, 'funder', '-939795212.modify', '30', '', '', NULL, '0', ''),
(8, 'fhit', '-939795212.modify', '30', '', '', NULL, '0', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `site_form_lang`
--

CREATE TABLE `site_form_lang` (
  `flid` int(11) NOT NULL auto_increment,
  `fid` int(11) NOT NULL default '0',
  `flang` varchar(5) NOT NULL default 'de',
  `fpgenum` text,
  `fwerte` varchar(255) NOT NULL default '',
  `ferror` varchar(255) NOT NULL default '',
  `fdberror` varchar(255) NOT NULL default '',
  `fchkerror` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`flid`)
) TYPE=MyISAM AUTO_INCREMENT=6 ;

--
-- Daten für Tabelle `site_form_lang`
--

INSERT INTO `site_form_lang` (`flid`, `fid`, `flang`, `fpgenum`, `fwerte`, `ferror`, `fdberror`, `fchkerror`) VALUES
(1, 1, 'de', NULL, '', 'Username darf nicht leer sein.', 'Username bereits vorhanden.', ''),
(2, 2, 'de', NULL, '', 'Passworte nicht identisch oder leer.', '', ''),
(3, 3, 'de', NULL, '', 'Passworte nicht identisch oder leer.', '', ''),
(5, 5, 'de', NULL, '', '', '', 'Ungültige Zeichen im Feld Eintrag.');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `site_menu`
--

CREATE TABLE `site_menu` (
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

CREATE TABLE `site_menu_lang` (
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

CREATE TABLE `site_text` (
  `lang` varchar(5) NOT NULL default 'de',
  `label` varchar(20) NOT NULL default '',
  `crc32` enum('0','-1') NOT NULL default '0',
  `tname` varchar(40) NOT NULL default '',
  `ebene` text NOT NULL,
  `kategorie` text NOT NULL,
  `html` enum('-1','0') NOT NULL default '0',
  `content` text NOT NULL,
  `changed` datetime NOT NULL default '0000-00-00 00:00:00',
  `bysurname` varchar(40) NOT NULL default '',
  `byforename` varchar(40) NOT NULL default '',
  `byemail` varchar(60) NOT NULL default '',
  `byalias` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`lang`,`label`,`tname`)
) TYPE=MyISAM PACK_KEYS=1;

--
-- Daten für Tabelle `site_text`
--

INSERT INTO `site_text` (`lang`, `label`, `crc32`, `tname`, `ebene`, `kategorie`, `html`, `content`, `changed`, `bysurname`, `byforename`, `byemail`, `byalias`) VALUES
('de', 'abort', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Abbrechen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'content', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Inhalt', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'entry', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Eintrag', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'error_menu', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Fehler beim löschen des Menüeintrag', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'error_menu_lang', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Fehler beim löschen der Sprache(n)', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'error_text', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Fehler beim löschen des/r Text/e', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'inhalt', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Den Menüpunkt "!#ausgaben_entry" wirklich löschen?', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'languages', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Sprachen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'no_content', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Kein Inhalt', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'send', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Abschicken', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ueberschrift', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Menü-Editor - Menüpunkt löschen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'abort', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Abbrechen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'add', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Neue Sprache hinzufügen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'basic', '-1', '-555504947.edit-multi', '/admin/menued', 'add', '0', 'Allgemein', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'delete', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Diese Sprache löschen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'entry', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Eintrag', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'error_lang_add', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Diese Sprache ist bereits vorhanden.', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'error_lang_delete', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Die Entwickler Sprache kann nicht gelöscht werden.', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'error_result', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'DB Fehler: ', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'extended', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Speziell', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'exturl', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'externe Url', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'hide', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Deaktiviert', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'label', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Bezeichnung', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'lang', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Sprache', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'language', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Sprachen Verwaltung', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'level', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'benötigter Level', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'madatory', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Erzwungen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'new_lang', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Neue Sprache', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'refid', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Ref. ID', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'reset', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Zurücksetzen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'send', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Abschicken', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'sort', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Sortierung', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'template', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Template', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ueberschrift', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Menü-Editor - Menüpunkt', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'abort', '-1', '-555504947.edit-single', '/admin/menued', 'edit', '0', 'Abbrechen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'add', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Neue Sprache hinzufügen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'basic', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Allgemein', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'entry', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Eintrag', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'error_lang_add', '-1', '-555504947.edit-single', '/admin/menued', 'edit', '0', 'Diese Sprache ist bereits vorhanden.', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'error_lang_delete', '-1', '-555504947.edit-single', '/admin/menued', 'edit', '0', 'Die Entwickler Sprache kann nicht gelöscht werden.', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'error_result', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'DB Fehler: ', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'extended', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Speziell', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'exturl', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'ext. Url', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'hide', '-1', '-555504947.edit-single', '/admin/menued', 'edit', '0', 'Versteckt', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'label', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Bezeichnung', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'lang', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Sprache', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'level', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'benötigter Level', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'madatory', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Erzwungen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'new_lang', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Neue Sprache', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'refid', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Ref ID.', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'reset', '-1', '-555504947.edit-single', '/admin/menued', 'edit', '0', 'Zurücksetzen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'send', '-1', '-555504947.edit-single', '/admin/menued', 'edit', '0', 'Abschicken', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'sort', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Sortierung', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'template', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Template', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ueberschrift', '-1', '-555504947.edit-single', '/admin/menued', 'edit', '0', 'Menü-Editor - Menüpunkt bearbeiten', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'button_desc_add', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Unterpunkt hinzufügen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'button_desc_delete', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Löschen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'button_desc_down', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Sortierung - Nach unten', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'button_desc_edit', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Bearbeiten', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'button_desc_move', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Im Menü Baum verschieben', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'button_desc_up', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Sortierung - Nach oben', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'disabled', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Abgeschaltet', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'enabled', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Eingeschaltet', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'error1', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Menüpunkte mit Unterpunkten lassen sich nicht löschen.', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'extern', '-1', '-555504947.list', '/admin/menued', 'list', '0', '(extern)', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'new', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Neuer Ast', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'renumber', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Neu durchnummerieren', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ueberschrift', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Menu-Editor - Übersicht', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'abort', '-1', '-555504947.move', '/admin/menued', 'move', '0', 'Abbrechen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'entry', '-1', '-555504947.move', '/admin/menued', 'move', '0', 'Eintrag', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'extern', '-1', '-555504947.move', '/admin/menued', 'move', '0', '(extern)', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'reset', '-1', '-555504947.move', '/admin/menued', 'move', '0', 'Zurücksetzen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'root', '-1', '-555504947.move', '/admin/menued', 'move', '0', 'Ins Hauptmenü', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'send', '-1', '-555504947.move', '/admin/menued', 'move', '0', 'Abschicken', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ueberschrift', '-1', '-555504947.move', '/admin/menued', 'move', '0', 'Menü-Editor - Menüpunkt verschieben', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'send', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Abschicken', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'chkpass', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Wiederholung', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'inhalt', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Passwort ändern', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'newpass', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Neues', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'oldpass', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Altes', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ueberschrift', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Passwort Editor', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ueberschrift', '-1', 'auth', '', 'index', '0', 'Überschrift', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'desc', '-1', 'auth.logout', '', 'index', '0', 'Werkzeuge', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'fileed', '-1', 'auth.logout', '', 'index', '0', 'Datei-Editor', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'leveled', '-1', 'auth.logout', '', 'index', '0', 'Level-Editor', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'menued', '-1', 'auth.logout', '', 'index', '0', 'Menü-Editor', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'nachher', '-1', 'auth.logout', '', 'index', '0', 'ist angemeldet.', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'passed', '-1', 'auth.logout', '', 'index', '0', 'Passwort-Editor', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'usered', '-1', 'auth.logout', '', 'index', '0', 'User-Editor', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'vorher', '-1', 'auth.logout', '', 'index', '0', 'Benutzer', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'abort', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Abbrechen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'add', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Datei einfügen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'b', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Fett', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'big', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Grösser als der Rest', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'br', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Erzwungener Umbruch', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'cent', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Zentriert', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'center', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Zentriert', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'cite', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Logisch: cite', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'col', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Tabellenspalte', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'db', '-1', 'cms.edit.cmstag', '', 'index', '0', 'DB', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'div', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Bereich', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'e', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Mail', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'em', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Logisch: emphatisch', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'email', '-1', 'cms.edit.cmstag', '', 'index', '0', 'eMail Link', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'file', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Datei', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'files', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Dateien', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'h1', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Überschrift Klasse 1', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'h2', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Überschrift Klasse 2', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'hl', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Spezielle Trennlinie', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'hr', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Trennlinie', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'i', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Kursiv', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'img', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Bild', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'imgb', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Bild mit Rahmen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'in', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Initial', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'label', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Marke', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'language', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Sprache', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'link', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Link', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'list', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Liste', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'm1', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Menü dieser Ebene', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'm2', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Menü der Unterebene', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'pre', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Vorformatiert', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'quote', '-1', 'cms.edit.cmstag', '', 'index', '0', 'In Anführungszeichen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'row', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Tabellenzeile', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 's', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Durchgestrichen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'save', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Speichern', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'small', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Kleiner als der Rest', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'sp', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Geschütztes Leerzeichen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'strong', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Logisch: strong', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'sub', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Tiefgestellt', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'sup', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Hochgestellt', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'tab', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Tabelle', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'tagselect', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Tag auswählen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'template', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Template', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'tt', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Dickengleich', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'u', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Unterstrichen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'up', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Zurück-Link', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'upload', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Hinaufladen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'inhalt', '-1', '404', '', 'fehlt', '0', '[H1]Fehler 404 - Nicht gefunden.[/H1]\r\n\r\n[P]Die Uri !#ausgaben_404seite wurde nicht gefunden.\r\n\r\nLeider konnte das System nicht feststellen woher sie gekommen sind[/P].', '2007-07-28 13:22:15', 'Ammon', 'Werner', 'chaot@chaos.de', 'chaot'),
('de', 'error_dupe', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Der Eintrag ist bereits vorhanden.', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'inhalt', '-1', '404referer', '', 'fehlt', '0', '[H1]Fehler 404 - Nicht gefunden.[/H1]\r\n\r\n[P]Die Uri: !#ausgaben_404seite wurde nicht gefunden.\r\n\r\nDie [LINK=!#ausgaben_404referer]Seite[/LINK] enthaelt einen falschen/alten Link.[/P]', '2007-07-28 13:21:52', 'Ammon', 'Werner', 'chaot@chaos.de', 'chaot'),
('de', 'error_dupe', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Der Eintrag ist bereits vorhanden.', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'error_dupe', '-1', '-555504947.move', '/admin/menued', 'move', '0', 'In dieser Ebene existiert bereits ein Eintrag mit gleichem Namen.', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'logout', '-1', 'auth.login', '', 'auth.login', '0', 'Abgemeldet', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'denied', '-1', 'auth.login', '', 'auth.login', '0', 'Zugriff verweigert!', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'picture', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'evt. Bild', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'picture', '-1', '-555504947.edit-single', '/admin/menued', 'edit', '0', 'evt. Bild', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'reset', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Zurücksetzen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'abort', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Abbrechen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ueberschrift', '-1', '-840786483.list', '/admin/menued', 'list', '0', 'Level-Editor - Übersicht', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ueberschrift', '-1', '-840786483.modify', '/admin/menued', 'edit', '0', 'Level-Editor - Bearbeiten', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'level', '-1', '-840786483.modify', '/admin/leveled', 'modify', '0', 'Bezeichnung', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'description', '-1', '-840786483.modify', '/admin/leveled', 'modify', '0', 'Beschreibung', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'del', '-1', '-840786483.modify', '/admin/leveled', 'modify', '0', 'entfernen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'add', '-1', '-840786483.modify', '/admin/leveled', 'modify', '0', 'hinzufügen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'send', '-1', '-840786483.modify', '/admin/leveled', 'modify', '0', 'Abschicken', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'reset', '-1', '-840786483.modify', '/admin/leveled', 'modify', '0', 'Zurücksetzen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'abort', '-1', '-840786483.modify', '/admin/leveled', 'modify', '0', 'Abbrechen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'send', '-1', '-840786483.delete', '/admin/leveled', 'modify', '0', 'Löschen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'abort', '-1', '-840786483.delete', '/admin/leveled', 'modify', '0', 'Abbrechen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'frage', '-1', '-840786483.delete', '/admin/leveled', 'modify', '0', 'Wollen Sie den Level "!#ausgaben_level" wirklich löschen?', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'level', '-1', '-840786483.details', '/admin/leveled', 'details', '0', 'Bezeichnung', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'user', '-1', '-840786483.details', '/admin/leveled', 'details', '0', 'Mitglieder', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'beschreibung', '-1', '-840786483.details', '/admin/leveled', 'details', '0', 'Beschreibung', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'edit', '-1', '-840786483.details', '/admin/leveled', 'details', '0', 'Bearbeiten', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'list', '-1', '-840786483.details', '/admin/leveled', 'details', '0', 'Übersicht', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ueberschrift', '-1', '-840786483.details', '/admin/leveled', 'details', '0', 'Level Editor - Eigenschaften', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ueberschrift', '-1', '-840786483.delete', '/admin/leveled', 'modify', '0', 'Level-Editor - Löschen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ueberschrift', '-1', '210295197.list', '/admin/usered', 'list', '0', 'User-Editor - Übersicht', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ueberschrift', '-1', '-939795212.list', '/admin/fileed', 'list', '0', 'Datei-Editor - Übersicht', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'search', '-1', '-939795212.list', '/admin/fileed', 'list', '0', 'Suche', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'gesamt', '-1', '-939795212.list', '/admin/fileed', 'list', '0', 'Gesamt:', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'send', '-1', '-939795212.list', '/admin/fileed', 'list', '0', 'Go', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'fileedit', '-1', '-939795212.list', '/admin/fileed', 'list', '0', 'Bearbeiten', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'filedelete', '-1', '-939795212.list', '/admin/fileed', 'list', '0', 'Löschen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ffname', '-1', '-939795212.modify', '/admin', 'usered', '0', 'Dateiname', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'fdesc', '-1', '-939795212.modify', '/admin', 'usered', '0', 'Bildbeschreibung', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'funder', '-1', '-939795212.modify', '/admin', 'usered', '0', 'Bildunterschrift', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'fhit', '-1', '-939795212.modify', '/admin', 'usered', '0', 'Schlagworte', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'upa', '-1', '-939795212.modify', '/admin', 'usered', '0', 'Die aktuelle Datei durch', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'upb', '-1', '-939795212.modify', '/admin', 'usered', '0', 'ersetzen.', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'send', '-1', '-939795212.modify', '/admin', 'usered', '0', 'Abschicken', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'reset', '-1', '-939795212.modify', '/admin', 'usered', '0', 'Zurücksetzen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'abort', '-1', '-939795212.modify', '/admin', 'usered', '0', 'Abbrechen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'cmslink', '-1', '-939795212.list', '/admin/fileed', 'list', '0', 'zum Content Editor', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'level', '-1', '-840786483.list', '/admin/leveled', 'list', '0', 'Bezeichnung', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'beschreibung', '-1', '-840786483.list', '/admin/leveled', 'list', '0', 'Beschreibung', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'modify', '-1', '-840786483.list', '/admin/leveled', 'list', '0', 'Bearbeiten', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'edit', '-1', '-840786483.list', '/admin/leveled', 'list', '0', 'Bearbeiten', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'delete', '-1', '-840786483.list', '/admin/leveled', 'list', '0', 'Löschen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'details', '-1', '-840786483.list', '/admin/leveled', 'list', '0', 'Eigenschaften', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'senden', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'Abschicken', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'reset', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'Zurücksetzen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'abort', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'Abbrechen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ueberschrift', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'User-Editor - Bearbeiten', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'error_oldpass', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Das alte Passwort stimmt nicht!', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'error_chkpass', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Das Neue Passwort und die Wiederholung stimmen nicht überein!', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'nachname', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'Nachname', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'vorname', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'Vorname', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'email', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'eMail', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'new', '-1', '-840786483.list', '/admin/leveled', 'list', '0', 'Neuer Level', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'new', '-1', '210295197.list', '/admin/usered', 'list', '0', 'Neuer User', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'frage', '-1', '210295197.delete', '/admin/usered', 'modify', '0', 'Wollen Sie den User "!#ausgaben_username" wirklich löschen?', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'delete', '-1', '210295197.delete', '/admin/usered', 'modify', '0', 'Delete', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'abort', '-1', '210295197.delete', '/admin/usered', 'modify', '0', 'Abbrechen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ueberschrift', '-1', '210295197.delete', '/admin/usered', 'modify', '0', 'User-Editor - Löschen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ueberschrift', '-1', '210295197.details', '/admin/usered', 'details', '0', 'User-Editor - Eigenschaften', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'username', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'Login', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'newpass', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'Passwort', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'chkpass', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'Wiederholung', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ueberschrift', '-1', 'base', '', 'impressum', '0', 'Menu', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'copyright', '-1', 'base', '', 'index', '0', 'eWeBuKi - Copyright 2003-2006', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'kekse', '-1', 'base', '', 'impressum', '0', 'Kekse', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'bloged', '-1', 'auth.logout', '/admin/passed', 'modify', '0', 'Blog-Editor', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'send', '-1', '-939795212.delete', '/admin/menued', 'list', '0', 'Abschicken', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'abort', '-1', '-939795212.delete', '/admin/menued', 'list', '0', 'Abbrechen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ueberschrift', '-1', '-939795212.delete', '/admin/menued', 'delete', '0', 'Datei Editor - Datei löschen!', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'inhalt', '-1', '-939795212.delete', '/admin/menued', 'delete', '0', 'Die Datei "!#ausgaben_ffname" wirklich löschen?', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ueberschrift', '-1', '-939795212.upload', '/admin/menued', 'list', '0', 'Datei-Editor Upload', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'file', '-1', '-939795212.upload', '/admin/menued', 'list', '0', 'Dateiauswahl', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'send', '-1', '-939795212.upload', '/admin/menued', 'list', '0', 'Abschicken', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'reset', '-1', '-939795212.upload', '/admin/menued', 'edit', '0', 'Zurücksetzen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'abort', '-1', '-939795212.upload', '/admin/menued', 'edit', '0', 'Abbrechen', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ueberschrift', '-1', '-939795212.modify', '/admin/menued', 'add', '0', 'Datei-Editor - Datei Eigenschaften bearbeiten', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'answera', '-1', '-939795212.list', '/admin/fileed', 'list', '0', 'Ihre Schnellsuche nach', '2006-09-26 12:18:44', '', '', '', 'ewebuki'),
('de', 'answerb', '-1', '-939795212.list', '/admin/fileed', 'list', '0', 'hat', '2006-09-26 12:18:58', '', '', '', 'ewebuki'),
('de', 'answerc_no', '-1', '-939795212.list', '/admin/fileed', 'list', '0', 'keine Einträge gefunden.', '2006-09-26 12:19:42', '', '', '', 'ewebuki'),
('de', 'answerc_yes', '-1', '-939795212.list', '/admin/fileed', 'list', '0', 'folgende Einträge gefunden.', '2006-09-26 12:20:01', '', '', '', 'ewebuki'),
('de', 'next', '-1', '-939795212.list', '/admin/fileed', 'list', '0', 'Vorherige Seite', '2006-09-26 12:22:25', '', '', '', 'ewebuki'),
('de', 'prev', '-1', '-939795212.list', '/admin/fileed', 'list', '0', 'Nexte Seite', '2006-09-26 12:22:35', '', '', '', 'ewebuki'),
('de', 'error1', '-1', '-939795212.list', '/admin/fileed', 'list', '0', 'Bild wird bereits verwendet - Bearbeiten zeigt wo.', '2006-10-06 20:07:05', '', '', '', 'ewebuki'),
('de', 'error2', '-1', '-939795212.list', '/admin/fileed', 'list', '0', 'Bild kann nur vom Eigentümer gelöscht werden.', '2006-10-06 20:22:05', '', '', '', 'ewebuki'),
('de', 'error_edit', '-1', '-939795212.modify', '/admin/fileed', 'edit', '0', 'Bild kann nur vom Eigentümer bearbeitet werden.', '2006-10-06 20:44:19', '', '', '', 'ewebuki'),
('de', 'references', '-1', '-939795212.modify', '/admin/fileed', 'edit', '0', 'Ist enthalten in:', '2006-10-06 19:59:07', '', '', '', 'ewebuki'),
('de', 'inhalt', '-1', 'demo', '', 'demo', '0', '[H1]Erstes Kapitel[/H1]\r\n\r\n[H2]Erster Absatz[/H2]\r\n\r\n[P]Weit hinten, hinter den Wortbergen, fern der Länder Vokalien und Konsonantien leben die Blindtexte. Abgeschieden wohnen Sie in Buchstabhausen an der Küste des Semantik, eines großen Sprachozeans. Ein kleines Bächlein namens Duden fließt durch ihren Ort und versorgt sie mit den nötigen Regelialien. Es ist ein paradiesmatisches Land, in dem einem gebratene Satzteile in den Mund fliegen. Nicht einmal von der allmächtigen Interpunktion werden die Blindtexte beherrscht – ein geradezu unorthographisches Leben.[/P]\r\n\r\n\r\n[H2]Zweiter Absatz[/H2]\r\n\r\n\r\n[P]Eines Tages aber beschloß eine kleine Zeile Blindtext, ihr Name war Lorem Ipsum, hinaus zu gehen in die weite Grammatik. Der große Oxmox riet ihr davon ab, da es dort wimmele von bösen Kommata, wilden Fragezeichen und hinterhältigen Semikoli, doch das Blindtextchen ließ sich nicht beirren. Es packte seine sieben Versalien, schob sich sein Initial in den Gürtel und machte sich auf den Weg.[/P]\r\n\r\n\r\n[H1]Zweites Kapitel[/H1]\r\n\r\n[H2]Erster Absatz[/H2]\r\n\r\n[P]Als es die ersten Hügel des Kursivgebirges erklommen hatte, warf es einen letzten Blick zurück auf die Skyline seiner Heimatstadt Buchstabhausen, die Headline von Alphabetdorf und die Subline seiner eigenen Straße, der Zeilengasse. Wehmütig lief ihm eine rethorische Frage über die Wange, dann setzte es seinen Weg fort.[/P]\r\n\r\n[P=box]Unterwegs traf es eine Copy. Die Copy warnte das Blindtextchen, da, wo sie herkäme wäre sie zigmal umgeschrieben worden und alles, was von ihrem Ursprung noch übrig wäre, sei das Wort "und" und das Blindtextchen solle umkehren und wieder in sein eigenes, sicheres Land zurückkehren.[/P]\r\n\r\n[H2]Dritter Absatz[/H2]\r\n\r\n[P]Doch alles Gutzureden konnte es nicht überzeugen und so dauerte es nicht lange, bis ihm ein paar heimtückische Werbetexter auflauerten, es mit Longe und Parole betrunken machten und es dann in ihre Agentur schleppten, wo sie es für ihre Projekte wieder und wieder mißbrauchten. Und wenn es nicht umgeschrieben wurde, dann benutzen Sie es immernoch.[/P]', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'inhalt', '-1', 'fehler', '', 'fehler', '0', '[H1]404 Test[/H1]\r\n\r\n\r\n[P]Hiermit wird die 404 Fehlerseite angezeigt.\r\n\r\n[LINK=fehlt.html]404 Fehler mit Referer[/LINK]\r\n\r\nUm die zweite 404 Fehlermeldung (Referer unbekannt) sichtbar zu machen,\r\nin der Eingabezeile der obigen 404 Fehlermeldung einfach Enter drücken.[/P]', '2007-07-28 13:43:45', 'Ammon', 'Werner', 'chaot@chaos.de', 'chaot'),
('de', 'inhalt', '-1', 'impressum', '', 'impressum', '0', '[H1]Impressum[/H1]\r\n\r\n\r\n[P]eWeBuKi - Copyright 2003-2007\r\nby [EMAIL=w.ammon(at)chaos.de]Werner Ammon[/EMAIL][/P]\r\n\r\n\r\n[H2]Weitere Infoseiten:[/H2]\r\n\r\n\r\n[P][LINK=http://developer.berlios.de/projects/ewebuki/]developer.berlios.de/projects/ewebuki/[/LINK]\r\n[LINK=http://www.chaos.de/ewebuki.html]www.chaos.de/ewebuki.html[/LINK][/P]', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'inhalt', '-1', 'index', '', 'index', '0', '[H1]Glückwunsch Ihr eWeBuKi läuft![/H1]\r\n\r\n[P]Um sich am System anzumelden benutzen Sie bitte folgende Daten:\r\n\r\nuser: ewebuki\r\npass: ewebuki[/P]\r\n\r\n[P=box][B]ACHTUNG:[/B] Passwort ändern nicht vergessen![/P]\r\n\r\n[P]Weitere Infoseiten:\r\n[LINK=http://developer.berlios.de/projects/ewebuki/]developer.berlios.de/projects/ewebuki/[/LINK]\r\n[LINK=http://www.chaos.de/ewebuki.html]www.chaos.de/ewebuki.html[/LINK][/P]', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki'),
('de', 'inhalt', '-1', 'show', '', 'show', '0', '[H1]eWeBuKi Show[/H1]\r\n\r\n\r\n[H2]Tabellen Positionen[/H2]\r\n\r\n[TAB=;300;1]\r\n[ROW]\r\n[COL]1,1[/COL]\r\n[COL=;;u]1,2[/COL]\r\n[COL=r]1,3\r\n\r\n\r\n[/COL]\r\n[/ROW][ROW]\r\n[COL=m]2,1[/COL]\r\n[COL=;;g]2,2[/COL]\r\n[COL=r;;m]2,3\r\n\r\n\r\n[/COL]\r\n[/ROW]\r\n[/TAB]\r\n\r\n\r\n[H2]Easy Template Links[/H2]\r\n\r\n[P]!#lnk_1\r\n!#lnk_2\r\n!#lnk_3[/P]\r\n\r\n[H2]Menu oberhalb (M1,mit Bez.)[/H2]\r\n[M1]nach oben[/M1]\r\n\r\n[H2]Menu oberhalb als Liste (M1=l,ohne Bez.)[/H2]\r\n[M1=l][/M1]\r\n\r\n[H2]Menu gleiche Ebene (M2,mit Bez.)[/H2]\r\n[M2]nach oben[/M2]\r\n\r\n[H2]Menu gleiche Ebene als Liste (M2=l,mit Bez.)[/H2]\r\n[M2=l][/M2]\r\n\r\n\r\n\r\n[H2]Tabellen Abstände[/H2]\r\n[P]Tabellen Abstände (abstand text - tabelle 1)[/P]\r\n\r\n\r\n[TAB=;300;1]\r\n[ROW]\r\n\r\n[COL=l;150]links oben\r\n[/COL]\r\n\r\n[COL=l;150]rechts oben\r\n[/COL]\r\n\r\n[/ROW]\r\n[/TAB]\r\n\r\n[TAB=;300;1]\r\n[ROW]\r\n\r\n[COL=l;150]links oben\r\n[/COL]\r\n\r\n[COL=l;150]rechts oben\r\n[/COL]\r\n\r\n[/ROW]\r\n[/TAB]\r\n\r\n\r\n[P]Tabellen Abstände (abstand text - tabelle 2)[/P]\r\n\r\n[IN]I[/IN]nitial fuer Texte\r\n\r\n[H1][B][EM]Bold EM Tag[/EM][/B] im H1 Tag[/H1]\r\n\r\nText zwischen Linien:\r\n[HL][/HL]\r\nHier kommt der Text.\r\n[HL][/HL]\r\n\r\n[H2]Bilder im Text[/H2]\r\n\r\n[P][IMG=/file/picture/small/img_1.png;l;;;20;;20]eWeBuKi Logo[/IMG]Weit hinten, hinter den Wortbergen, fern der Länder Vokalien und Konsonantien leben die Blindtexte. Abgeschieden wohnen Sie in Buchstabhausen an der Küste des Semantik, eines großen Sprachozeans. Ein kleines Bächlein namens Duden fließt durch ihren Ort und versorgt sie mit den nötigen Regelialien. Es ist ein paradiesmatisches Land, in dem einem gebratene Satzteile in den Mund fliegen. Nicht einmal von der allmächtigen Interpunktion werden die Blindtexte beherrscht – ein geradezu unorthographisches Leben.[/P]\r\n\r\n[H2]Mehrere Bilder rechts[/H2]\r\n\r\n[P]Bei mehreren Bildern rechts gibt es Abstand Probleme. Um das zu umgehen muss der Umlauf mit dem Tag BR=a angehalten werden.[/P]\r\n\r\n[IMGB=/file/picture/small/img_1.png;r;0;b]Logo[/IMGB]Text neben Bild 1[BR=a][/BR]\r\n\r\n[IMGB=/file/picture/small/img_1.png;r]Logo[/IMGB]Text neben Bild 2[BR=a][/BR]\r\n\r\n[P]Nicht nur Bilder sondern auch Text kann mit diesem Trick unter das Bild geschoben werden.[/P]\r\n[H1]ueberschrift h1[/H1]\r\n[H2]ueberschrift h2[/H2]\r\n[H3]ueberschrift h3[/H3]\r\n[H4]ueberschrift h4[/H4]\r\n[H5]ueberschrift h5[/H5]\r\n[H6]ueberschrift h6[/H6]\r\n\r\nAbsaetze mit css einstellen:\r\n[P]Im Absatz ist es Schoen[/P]\r\n\r\nDIV=class jeder css im Content:\r\n[DIV=anderst]Dieser Text ist schoener als der Rest[/DIV]', '0000-00-00 00:00:00', 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki');
