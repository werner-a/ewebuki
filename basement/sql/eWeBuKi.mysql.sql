-- phpMyAdmin SQL Dump
-- version 2.6.0-pl2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Erstellungszeit: 13. November 2004 um 20:13
-- Server Version: 3.23.49
-- PHP-Version: 4.1.2
-- 
-- Datenbank: `eWeBuKi_mdecvs`
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

REPLACE INTO `auth_level` VALUES (1, 'cms_edit', 'berechtigt zum bearbeiten der templates'),
(2, 'cms_admin', 'berechtigt zur administration');

-- --------------------------------------------------------

-- 
-- Tabellenstruktur für Tabelle `auth_right`
-- 

CREATE TABLE `auth_right` (
  `rid` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `lid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`rid`),
  UNIQUE KEY `rid` (`rid`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

-- 
-- Daten für Tabelle `auth_right`
-- 

REPLACE INTO `auth_right` VALUES (1, 1, 1),
(2, 1, 2);

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
  UNIQUE KEY `uid` (`uid`),
  UNIQUE KEY `username` (`username`)
) TYPE=MyISAM PACK_KEYS=0 AUTO_INCREMENT=2 ;

-- 
-- Daten für Tabelle `auth_user`
-- 

REPLACE INTO `auth_user` VALUES (1, '', '', '', 'ewebuki', 'WFffxluy26Lew');

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
  `ffart` enum('jpg','png','pdf') NOT NULL default 'jpg',
  `fdesc` varchar(255) NOT NULL default '',
  `funder` varchar(255) default NULL,
  `fhit` varchar(255) default NULL,
  `fdel` text,
  PRIMARY KEY  (`fid`)
) TYPE=MyISAM AUTO_INCREMENT=2 ;

-- 
-- Daten für Tabelle `site_file`
-- 

REPLACE INTO `site_file` VALUES (1, 0, 1, 0, '', 'ewebuki_160x67.png', 'png', 'eWeBuKi Logo', '', '', NULL);

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
  `fcheck` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`fid`)
) TYPE=MyISAM AUTO_INCREMENT=5 ;

-- 
-- Daten für Tabelle `site_form`
-- 

REPLACE INTO `site_form` VALUES (1, 'username', '210295197.modify', '0', '', '', NULL, '-1', ''),
(2, 'pass', '210295197.modify', '0', '', '', 'password', '-1', ''),
(3, 'pass', '852881080.modify', '0', '', '', 'password', '-1', ''),
(4, 'fid', '-939795212.describe', '0', '', '', 'hidden', '-1', '');

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
  PRIMARY KEY  (`flid`)
) TYPE=MyISAM AUTO_INCREMENT=4 ;

-- 
-- Daten für Tabelle `site_form_lang`
-- 

REPLACE INTO `site_form_lang` VALUES (1, 1, 'de', NULL, '', 'Username darf nicht leer sein.', 'Username bereits vorhanden.'),
(2, 2, 'de', NULL, '', 'Passworte nicht identisch oder leer.', ''),
(3, 3, 'de', NULL, '', 'Passworte nicht identisch oder leer.', '');

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
) TYPE=MyISAM PACK_KEYS=1 AUTO_INCREMENT=6 ;

-- 
-- Daten für Tabelle `site_menu`
-- 

REPLACE INTO `site_menu` VALUES (1, 0, 'demo', NULL, 10, NULL, NULL, NULL, 'default1'),
(2, 1, 'test1', NULL, 10, NULL, NULL, NULL, 'default1'),
(3, 1, 'test2', NULL, 20, NULL, NULL, NULL, 'default1'),
(4, 0, 'show', NULL, 20, NULL, NULL, NULL, 'default1'),
(5, 0, 'impressum', NULL, 30, NULL, NULL, NULL, 'default1');

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
) TYPE=MyISAM PACK_KEYS=1 AUTO_INCREMENT=6 ;

-- 
-- Daten für Tabelle `site_menu_lang`
-- 

REPLACE INTO `site_menu_lang` VALUES (1, 1, 'de', 'Demo', NULL),
(2, 2, 'de', 'Test 1', NULL),
(3, 3, 'de', 'Test 2', NULL),
(4, 4, 'de', 'eWeBuKi Show', NULL),
(5, 5, 'de', 'Impressum', NULL);

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
  PRIMARY KEY  (`lang`,`label`,`tname`)
) TYPE=MyISAM PACK_KEYS=1;

-- 
-- Daten für Tabelle `site_text`
-- 

REPLACE INTO `site_text` VALUES ('de', 'abort', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Abbrechen'),
('de', 'content', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Inhalt'),
('de', 'entry', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Eintrag'),
('de', 'error_menu', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Fehler beim löschen des Menüeintrag'),
('de', 'error_menu_lang', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Fehler beim löschen der Sprache(n)'),
('de', 'error_text', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Fehler beim löschen des/r Text/e'),
('de', 'inhalt', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Den Menüpunkt "!#ausgaben_entry" wirklich löschen?'),
('de', 'languages', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Sprachen'),
('de', 'no_content', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Kein Inhalt'),
('de', 'send', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Abschicken'),
('de', 'ueberschrift', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Menü-Editor - Menüpunkt löschen'),
('de', 'abort', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Abbrechen'),
('de', 'add', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Neue Sprache hinzufügen'),
('de', 'basic', '-1', '-555504947.edit-multi', '/admin/menued', 'add', '0', 'Allgemein'),
('de', 'delete', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Diese Sprache löschen'),
('de', 'entry', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Eintrag'),
('de', 'error_lang_add', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Diese Sprache ist bereits vorhanden.'),
('de', 'error_lang_delete', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Die Entwickler Sprache kann nicht gelöscht werden.'),
('de', 'error_result', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'DB Fehler: '),
('de', 'extended', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Speziell'),
('de', 'exturl', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'externe Url'),
('de', 'hide', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Deaktiviert'),
('de', 'label', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Bezeichnung'),
('de', 'lang', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Sprache'),
('de', 'language', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Sprachen Verwaltung'),
('de', 'level', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'benötigter Level'),
('de', 'madatory', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Erzwungen'),
('de', 'new_lang', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Neue Sprache'),
('de', 'refid', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Ref. ID'),
('de', 'reset', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Zurücksetzen'),
('de', 'send', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Abschicken'),
('de', 'sort', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Sortierung'),
('de', 'template', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Template'),
('de', 'ueberschrift', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Menü-Editor - Menüpunkt'),
('de', 'abort', '-1', '-555504947.edit-single', '/admin/menued', 'edit', '0', 'Abbrechen'),
('de', 'add', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Neue Sprache hinzufügen'),
('de', 'basic', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Allgemein'),
('de', 'entry', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Eintrag'),
('de', 'error_lang_add', '-1', '-555504947.edit-single', '/admin/menued', 'edit', '0', 'Diese Sprache ist bereits vorhanden.'),
('de', 'error_lang_delete', '-1', '-555504947.edit-single', '/admin/menued', 'edit', '0', 'Die Entwickler Sprache kann nicht gelöscht werden.'),
('de', 'error_result', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'DB Fehler: '),
('de', 'extended', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Speziell'),
('de', 'exturl', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'ext. Url'),
('de', 'hide', '-1', '-555504947.edit-single', '/admin/menued', 'edit', '0', 'Versteckt'),
('de', 'label', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Bezeichnung'),
('de', 'lang', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Sprache'),
('de', 'level', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'benötigter Level'),
('de', 'madatory', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Erzwungen'),
('de', 'new_lang', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Neue Sprache'),
('de', 'refid', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Ref ID.'),
('de', 'reset', '-1', '-555504947.edit-single', '/admin/menued', 'edit', '0', 'Zurücksetzen'),
('de', 'send', '-1', '-555504947.edit-single', '/admin/menued', 'edit', '0', 'Abschicken'),
('de', 'sort', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Sortierung'),
('de', 'template', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Template'),
('de', 'ueberschrift', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Menü-Editor - Menüpunkt'),
('de', 'button_desc_add', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Unterpunkt hinzufügen'),
('de', 'button_desc_delete', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Löschen'),
('de', 'button_desc_down', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Sortierung - Nach unten'),
('de', 'button_desc_edit', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Bearbeiten'),
('de', 'button_desc_move', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Im Menü Baum verschieben'),
('de', 'button_desc_up', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Sortierung - Nach oben'),
('de', 'disabled', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Abgeschaltet'),
('de', 'enabled', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Eingeschaltet'),
('de', 'error1', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Menüpunkte mit Unterpunkten lassen sich nicht löschen.'),
('de', 'extern', '-1', '-555504947.list', '/admin/menued', 'list', '0', '(extern)'),
('de', 'inhalt', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Soll hier ein beschreibender Text rein?'),
('de', 'new', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Neuer Ast'),
('de', 'renumber', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Neu durchnummerieren'),
('de', 'ueberschrift', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Menu-Editor - Übersicht'),
('de', 'abort', '-1', '-555504947.move', '/admin/menued', 'move', '0', 'Abbrechen'),
('de', 'entry', '-1', '-555504947.move', '/admin/menued', 'move', '0', 'Eintrag'),
('de', 'extern', '-1', '-555504947.move', '/admin/menued', 'move', '0', '(extern)'),
('de', 'reset', '-1', '-555504947.move', '/admin/menued', 'move', '0', 'Zurücksetzen'),
('de', 'root', '-1', '-555504947.move', '/admin/menued', 'move', '0', 'Ins Hauptmenü'),
('de', 'send', '-1', '-555504947.move', '/admin/menued', 'move', '0', 'Abschicken'),
('de', 'ueberschrift', '-1', '-555504947.move', '/admin/menued', 'move', '0', 'Menü-Editor - Menüpunkt verschieben'),
('de', 'send', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Abschicken'),
('de', 'chkpass', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Wiederholung'),
('de', 'inhalt', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Passwort ändern'),
('de', 'newpass', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Neues'),
('de', 'oldpass', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Altes'),
('de', 'ueberschrift', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Passwort Editor'),
('de', 'ueberschrift', '-1', 'auth', '', 'index', '0', 'Überschrift'),
('de', 'desc', '-1', 'auth.logout', '', 'index', '0', 'Werkzeuge'),
('de', 'fileed', '-1', 'auth.logout', '', 'index', '0', 'Datei-Editor'),
('de', 'leveled', '-1', 'auth.logout', '', 'index', '0', 'Level-Editor'),
('de', 'menued', '-1', 'auth.logout', '', 'index', '0', 'Menü-Editor'),
('de', 'nachher', '-1', 'auth.logout', '', 'index', '0', 'ist angemeldet.'),
('de', 'passed', '-1', 'auth.logout', '', 'index', '0', 'Passwort-Editor'),
('de', 'usered', '-1', 'auth.logout', '', 'index', '0', 'User-Editor'),
('de', 'vorher', '-1', 'auth.logout', '', 'index', '0', 'Benutzer'),
('de', 'abort', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Abbrechen'),
('de', 'add', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Datei einfügen'),
('de', 'b', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Fett'),
('de', 'big', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Grösser als der Rest'),
('de', 'br', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Erzwungener Umbruch'),
('de', 'cent', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Zentriert'),
('de', 'center', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Zentriert'),
('de', 'cite', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Logisch: cite'),
('de', 'col', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Tabellenspalte'),
('de', 'db', '-1', 'cms.edit.cmstag', '', 'index', '0', 'DB'),
('de', 'div', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Bereich'),
('de', 'e', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Mail'),
('de', 'em', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Logisch: emphatisch'),
('de', 'email', '-1', 'cms.edit.cmstag', '', 'index', '0', 'eMail Link'),
('de', 'file', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Datei'),
('de', 'files', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Dateien'),
('de', 'h1', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Überschrift Klasse 1'),
('de', 'h2', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Überschrift Klasse 2'),
('de', 'hl', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Spezielle Trennlinie'),
('de', 'hr', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Trennlinie'),
('de', 'i', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Kursiv'),
('de', 'img', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Bild'),
('de', 'imgb', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Bild mit Rahmen'),
('de', 'in', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Initial'),
('de', 'label', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Marke'),
('de', 'language', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Sprache'),
('de', 'link', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Link'),
('de', 'list', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Liste'),
('de', 'm1', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Menü dieser Ebene'),
('de', 'm2', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Menü der Unterebene'),
('de', 'pre', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Vorformatiert'),
('de', 'quote', '-1', 'cms.edit.cmstag', '', 'index', '0', 'In Anführungszeichen'),
('de', 'row', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Tabellenzeile'),
('de', 's', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Durchgestrichen'),
('de', 'save', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Speichern'),
('de', 'small', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Kleiner als der Rest'),
('de', 'sp', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Geschütztes Leerzeichen'),
('de', 'strong', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Logisch: strong'),
('de', 'sub', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Tiefgestellt'),
('de', 'sup', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Hochgestellt'),
('de', 'tab', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Tabelle'),
('de', 'tagselect', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Tag auswählen'),
('de', 'template', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Template'),
('de', 'tt', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Dickengleich'),
('de', 'u', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Unterstrichen'),
('de', 'up', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Zurück-Link'),
('de', 'upload', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Hinaufladen'),
('de', 'inhalt', '-1', '404', '', 'indi', '0', 'Die Uri !#ausgaben_404seite wurde nicht gefunden.\r\n\r\nLeider konnte das System nicht feststellen woher sie gekommen sind.'),
('de', 'ueberschrift', '-1', '404', '', 'indi', '0', 'Fehler 404 - Nicht gefunden.'),
('de', 'error_dupe', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Der Eintrag ist bereits vorhanden.'),
('de', 'ueberschrift', '-1', '404referer', '', 'test3', '0', 'Fehler 404 - Nicht gefunden.'),
('de', 'inhalt', '-1', '404referer', '', 'test3', '0', 'Die Uri: !#ausgaben_404seite wurde nicht gefunden.\r\n\r\nDie [LINK=!#ausgaben_404referer]Seite[/LINK] enthaelt einen falschen/alten Link.'),
('de', 'error_dupe', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Der Eintrag ist bereits vorhanden.'),
('de', 'error_dupe', '-1', '-555504947.move', '/admin/menued', 'move', '0', 'In dieser Ebene existiert bereits ein Eintrag mit gleichem Namen.'),
('de', 'logout', '-1', 'auth.login', '', 'auth.login', '0', 'Abgemeldet'),
('de', 'denied', '-1', 'auth.login', '', 'auth.login', '0', 'Zugriff verweigert!'),
('de', 'picture', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'evt. Bild'),
('de', 'picture', '-1', '-555504947.edit-single', '/admin/menued', 'edit', '0', 'evt. Bild'),
('de', 'reset', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Zurücksetzen'),
('de', 'abort', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Abbrechen'),
('de', 'ueberschrift', '-1', '-840786483.list', '/admin/menued', 'list', '0', 'Level-Editor - Übersicht'),
('de', 'ueberschrift', '-1', '-840786483.modify', '/admin/menued', 'edit', '0', 'Level-Editor - Bearbeiten'),
('de', 'level', '-1', '-840786483.modify', '/admin/leveled', 'modify', '0', 'Bezeichnung'),
('de', 'description', '-1', '-840786483.modify', '/admin/leveled', 'modify', '0', 'Beschreibung'),
('de', 'del', '-1', '-840786483.modify', '/admin/leveled', 'modify', '0', 'entfernen'),
('de', 'add', '-1', '-840786483.modify', '/admin/leveled', 'modify', '0', 'hinzufügen'),
('de', 'send', '-1', '-840786483.modify', '/admin/leveled', 'modify', '0', 'Abschicken'),
('de', 'reset', '-1', '-840786483.modify', '/admin/leveled', 'modify', '0', 'Zurücksetzen'),
('de', 'abort', '-1', '-840786483.modify', '/admin/leveled', 'modify', '0', 'Abbrechen'),
('de', 'send', '-1', '-840786483.delete', '/admin/leveled', 'modify', '0', 'Löschen'),
('de', 'abort', '-1', '-840786483.delete', '/admin/leveled', 'modify', '0', 'Abbrechen'),
('de', 'frage', '-1', '-840786483.delete', '/admin/leveled', 'modify', '0', 'Wollen Sie den Level "!#ausgaben_level" wirklich löschen?'),
('de', 'level', '-1', '-840786483.details', '/admin/leveled', 'details', '0', 'Bezeichnung'),
('de', 'user', '-1', '-840786483.details', '/admin/leveled', 'details', '0', 'Mitglieder'),
('de', 'beschreibung', '-1', '-840786483.details', '/admin/leveled', 'details', '0', 'Beschreibung'),
('de', 'edit', '-1', '-840786483.details', '/admin/leveled', 'details', '0', 'Bearbeiten'),
('de', 'list', '-1', '-840786483.details', '/admin/leveled', 'details', '0', 'Übersicht'),
('de', 'ueberschrift', '-1', '-840786483.details', '/admin/leveled', 'details', '0', 'Level Editor - Eigenschaften'),
('de', 'ueberschrift', '-1', '-840786483.delete', '/admin/leveled', 'modify', '0', 'Level-Editor - Löschen'),
('de', 'ueberschrift', '-1', '210295197.list', '/admin/usered', 'list', '0', 'User-Editor - Übersicht'),
('de', 'ueberschrift', '-1', '-939795212.list', '/admin', 'usered', '0', 'Datei-Editor - Übersicht'),
('de', 'search', '-1', '-939795212.list', '/admin', 'usered', '0', 'Suche'),
('de', 'gesamt', '-1', '-939795212.list', '/admin', 'usered', '0', 'Gesamt:'),
('de', 'send', '-1', '-939795212.list', '/admin', 'usered', '0', 'Go'),
('de', 'describe', '-1', '-939795212.list', '/admin', 'usered', '0', 'Bearbeiten'),
('de', 'delete1', '-1', '-939795212.list', '/admin', 'usered', '0', 'Löschen'),
('de', 'ffname', '-1', '-939795212.describe', '/admin', 'usered', '0', 'Dateiname'),
('de', 'fdesc', '-1', '-939795212.describe', '/admin', 'usered', '0', 'Bildbeschreibung'),
('de', 'funder', '-1', '-939795212.describe', '/admin', 'usered', '0', 'Bildunterschrift'),
('de', 'fhit', '-1', '-939795212.describe', '/admin', 'usered', '0', 'Schlagworte'),
('de', 'upa', '-1', '-939795212.describe', '/admin', 'usered', '0', 'Die aktuelle Datei durch'),
('de', 'upb', '-1', '-939795212.describe', '/admin', 'usered', '0', 'ersetzen.'),
('de', 'send', '-1', '-939795212.describe', '/admin', 'usered', '0', 'Abschicken'),
('de', 'reset', '-1', '-939795212.describe', '/admin', 'usered', '0', 'Zurücksetzen'),
('de', 'abort', '-1', '-939795212.describe', '/admin', 'usered', '0', 'Abbrechen'),
('de', 'inhalt', '-1', 'impressum', '', 'impressum', '0', 'eWeBuKi - Copyright 2003, 2004\r\nby [EMAIL=w.ammon@chaos.de]Werner Ammon[/EMAIL]'),
('de', 'send_image', '-1', '-939795212.list', '', 'impressum', '0', 'zum Content Editor'),
('de', 'delete2', '-1', '-939795212.list', '', 'impressum', '0', 'Alle Löschen'),
('de', 'level', '-1', '-840786483.list', '/admin/leveled', 'list', '0', 'Bezeichnung'),
('de', 'beschreibung', '-1', '-840786483.list', '/admin/leveled', 'list', '0', 'Beschreibung'),
('de', 'modify', '-1', '-840786483.list', '/admin/leveled', 'list', '0', 'Bearbeiten'),
('de', 'edit', '-1', '-840786483.list', '/admin/leveled', 'list', '0', 'Bearbeiten'),
('de', 'delete', '-1', '-840786483.list', '/admin/leveled', 'list', '0', 'Löschen'),
('de', 'details', '-1', '-840786483.list', '/admin/leveled', 'list', '0', 'Eigenschaften'),
('de', 'senden', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'Abschicken'),
('de', 'reset', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'Zurücksetzen'),
('de', 'abort', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'Abbrechen'),
('de', 'ueberschrift', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'User-Editor - Bearbeiten'),
('de', 'error_oldpass', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Das alte Passwort stimmt nicht!'),
('de', 'error_chkpass', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Das Neue Passwort und die Wiederholung stimmen nicht überein!'),
('de', 'nachname', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'Nachname'),
('de', 'vorname', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'Vorname'),
('de', 'email', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'eMail'),
('de', 'new', '-1', '-840786483.list', '/admin/leveled', 'list', '0', 'Neuer Level'),
('de', 'new', '-1', '210295197.list', '/admin/usered', 'list', '0', 'Neuer User'),
('de', 'frage', '-1', '210295197.delete', '/admin/usered', 'modify', '0', 'Wollen Sie den User "!#ausgaben_username" wirklich löschen?'),
('de', 'delete', '-1', '210295197.delete', '/admin/usered', 'modify', '0', 'Delete'),
('de', 'abort', '-1', '210295197.delete', '/admin/usered', 'modify', '0', 'Abbrechen'),
('de', 'ueberschrift', '-1', '210295197.delete', '/admin/usered', 'modify', '0', 'User-Editor - Löschen'),
('de', 'ueberschrift', '-1', '210295197.details', '/admin/usered', 'details', '0', 'User-Editor - Eigenschaften'),
('de', 'username', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'Login'),
('de', 'newpass', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'Passwort'),
('de', 'chkpass', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'Wiederholung'),
('de', 'ueberschrift', '-1', 'index', '', 'index', '0', 'Menü'),
('de', 'copyright', '-1', 'index', '', 'impressum', '0', 'eWeBuKi - Copyright 2003, 2004'),
('de', 'kekse', '-1', 'index', '', 'impressum', '0', 'Kekse'),
('de', 'ueberschrift', '-1', 'show', '', 'show', '0', 'eWeBuKi Show'),
('de', 'inhalt', '-1', 'show', '', 'show', '0', 'Tabellen Positionen:\r\n[TAB=;300;1]\r\n[ROW]\r\n[COL]1,1\r\n\r\n\r\n[/COL]\r\n[COL=;;u]1,2\r\n[/COL]\r\n[COL=r]1,3[/COL]\r\n[/ROW][ROW]\r\n[COL=m]2,1[/COL]\r\n[COL=;;g]2,2[/COL]\r\n[COL=r;;m]2,3\r\n\r\n\r\n[/COL]\r\n[/ROW]\r\n[/TAB]\r\n\r\n\r\nEasy Template Links:\r\n!#lnk_0\r\n!#lnk_1\r\n!#lnk_2\r\n!#lnk_3\r\n\r\nMenu oberhalb (M1,mit Bez.):\r\n[M1]nach oben[/M1]\r\n\r\nMenu oberhalb als Liste (M1=l,ohne Bez.);\r\n[M1=l][/M1]\r\n\r\nMenu gleiche Ebene (M2,mit Bez.)\r\n[M2]nach oben[/M2]\r\n\r\nMenu gleiche Ebene als Liste (M2=l,mit Bez.)\r\n[M2=l][/M2]\r\n\r\nTabellen Abstände (abstand text - tabelle 1)\r\n[TAB=;300;1]\r\n[ROW]\r\n[COL=l;150]links oben\r\n[/COL]\r\n[COL=l;150]rechts oben\r\n[/COL]\r\n[/ROW]\r\n[/TAB]\r\n[TAB=;300;1]\r\n[ROW]\r\n[COL=l;150]links oben\r\n[/COL]\r\n[COL=l;150]rechts oben\r\n[/COL]\r\n[/ROW]\r\n[/TAB]\r\nTabellen Abstände (abstand text - tabelle 2)\r\n\r\n[IN]I[/IN]nitial fuer Texte\r\n\r\n[H1][B][EM]Bold EM Tag[/EM][/B] im H1 Tag[/H1]\r\n\r\n\r\n\r\n\r\nText zwischen Linien:\r\n[HL][/HL]\r\nHier kommt der Text.\r\n[HL][/HL]\r\n\r\nWeit hinten, hinter den Wortbergen, fern der Länder Vokalien und Konsonantien leben die Blindtexte. Abgeschieden wohnen Sie[IMG=/file/picture/small/img_1.png;l;;;20;;20]eWeBuKi Logo[/IMG] in Buchstabhausen an der Küste des Semantik, eines großen Sprachozeans. Ein kleines Bächlein namens Duden fließt durch ihren Ort und versorgt sie mit den nötigen Regelialien. Es ist ein paradiesmatisches Land, in dem einem gebratene Satzteile in den Mund fliegen. Nicht einmal von der allmächtigen Interpunktion werden die Blindtexte beherrscht – ein geradezu unorthographisches Leben.\r\n\r\nBei Bildern rechts gibt es Abstand Probleme:\r\n[IMGB=/file/picture/small/img_1.png;r;0;b]Logo[/IMGB]\r\n\r\n\r\n\r\n\r\n[IMGB=/file/picture/small/img_1.png;r]Logo[/IMGB]\r\n\r\n\r\n\r\n\r\nZeilenumbrüche müssen passen, sonst kleben die Bilder nebeneinander.\r\n\r\n[H1]ueberschrift h1[/H1]\r\n[H2]ueberschrift h2[/H2]\r\n[H3]ueberschrift h3[/H3]\r\n[H4]ueberschrift h4[/H4]\r\n[H5]ueberschrift h5[/H5]\r\n[H6]ueberschrift h6[/H6]\r\n\r\nAbsaetze mit css einstellen:\r\n[P]Im Absatz ist es Schoen[/P]\r\n\r\nDIV=class jeder css im Content:\r\n[DIV=anderst]Dieser Text ist schoener als der Rest[/DIV]'),
('de', 'ueberschrift', '-1', 'impressum', '', 'impressum', '0', 'Impressum'),
('de', 'inhalt', '-1', 'werner', '', 'werner', '0', 'Sie können sich mit\r\n\r\nname: ewebuki\r\npass: ewebuki\r\n\r\nam System anmelden.\r\n\r\n[B]ACHTUNG:[/B] Passwort ändern nicht vergessen!'),
('de', 'ueberschrift', '-1', 'demo', '', 'demo', '0', 'Demoseite'),
('de', 'inhalt', '-1', 'demo', '', 'demo', '0', 'Hier könnte [B]Ihr[/B] Text stehen.'),
('de', 'ueberschrift', '-1', '1924484980.test1', '/demo', 'test1', '0', 'Testseite 1'),
('de', 'ueberschrift', '-1', '1924484980.test2', '/demo', 'test2', '0', 'Testseite 2'),
('de', 'inhalt', '-1', '1924484980.test1', '/demo', 'test1', '0', 'Hier könnte [B]Ihr[/B] Text stehen.'),
('de', 'inhalt', '-1', '1924484980.test2', '/demo', 'test2', '0', 'Hier könnte [B]Ihr[/B] Text stehen.'),
('de', 'ueberschrift', '-1', 'werner', '', 'werner', '0', 'Glückwunsch Ihr eWeBuKi läuft.'),
('de', 'ueberschrift', '-1', 'main', '', 'index', '0', 'Glückwunsch Ihr eWeBuKi läuft!'),
('de', 'inhalt', '-1', 'main', '', 'index', '0', 'Um sich am System anzumelden benutzen Sie bitte folgende Daten:\r\n\r\nuser: ewebuki\r\npass: ewebuki\r\n\r\n[B]ACHTUNG:[/B] Passwort ändern nicht vergessen!');
