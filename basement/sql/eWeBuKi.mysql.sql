# phpMyAdmin SQL Dump
# version 2.5.3
# http://www.phpmyadmin.net
#
# Host: localhost
# Erstellungszeit: 19. Januar 2004 um 15:59
# Server Version: 3.23.55
# PHP-Version: 4.3.1
# 
# Datenbank: `eWeBuKi`
# 

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `auth_level`
#

CREATE TABLE `auth_level` (
  `lid` int(11) NOT NULL auto_increment,
  `level` varchar(10) NOT NULL default '',
  `beschreibung` text NOT NULL,
  PRIMARY KEY  (`lid`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

#
# Daten für Tabelle `auth_level`
#

INSERT INTO `auth_level` VALUES (1, 'cms_edit', 'berechtigt zum bearbeiten der templates');
INSERT INTO `auth_level` VALUES (2, 'cms_admin', 'berechtigt zur administration');

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `auth_right`
#

CREATE TABLE `auth_right` (
  `rid` int(11) NOT NULL auto_increment,
  `uid` int(11) NOT NULL default '0',
  `lid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`rid`),
  UNIQUE KEY `rid` (`rid`)
) TYPE=MyISAM AUTO_INCREMENT=3 ;

#
# Daten für Tabelle `auth_right`
#

INSERT INTO `auth_right` VALUES (1, 1, 1);
INSERT INTO `auth_right` VALUES (2, 1, 2);

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `auth_user`
#

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

#
# Daten für Tabelle `auth_user`
#

INSERT INTO `auth_user` VALUES (1, '', '', '', 'ewebuki', 'JqXRXh15OlT8.');

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `site_file`
#

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

#
# Daten für Tabelle `site_file`
#

INSERT INTO `site_file` VALUES (1, 0, 1, 0, '', 'ewebuki_160x67.png', 'png', 'eWeBuKi Logo', '', '', NULL);

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `site_form`
#

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

#
# Daten für Tabelle `site_form`
#

INSERT INTO `site_form` VALUES (1, 'username', '210295197.modify', '0', '', '', NULL, '-1', '');
INSERT INTO `site_form` VALUES (2, 'pass', '210295197.modify', '0', '', '', 'password', '-1', '');
INSERT INTO `site_form` VALUES (3, 'pass', '852881080.modify', '0', '', '', 'password', '-1', '');
INSERT INTO `site_form` VALUES (4, 'fid', '-939795212.describe', '0', '', '', 'hidden', '-1', '');

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `site_form_lang`
#

CREATE TABLE `site_form_lang` (
  `flid` int(11) NOT NULL auto_increment,
  `fid` int(11) NOT NULL default '0',
  `flang` char(3) NOT NULL default 'ger',
  `fpgenum` text,
  `fwerte` varchar(255) NOT NULL default '',
  `ferror` varchar(255) NOT NULL default '',
  `fdberror` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`flid`)
) TYPE=MyISAM AUTO_INCREMENT=4 ;

#
# Daten für Tabelle `site_form_lang`
#

INSERT INTO `site_form_lang` VALUES (1, 1, 'ger', NULL, '', 'Username darf nicht leer sein.', 'Username bereits vorhanden.');
INSERT INTO `site_form_lang` VALUES (2, 2, 'ger', NULL, '', 'Passworte nicht identisch oder leer.', '');
INSERT INTO `site_form_lang` VALUES (3, 3, 'ger', NULL, '', 'Passworte nicht identisch oder leer.', '');

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `site_menu`
#

CREATE TABLE `site_menu` (
  `mid` int(10) NOT NULL auto_increment,
  `refid` int(10) default '0',
  `entry` varchar(30) NOT NULL default '',
  `picture` varchar(128) default NULL,
  `sort` int(8) NOT NULL default '0',
  `hide` enum('-1') default NULL,
  `level` varchar(10) default NULL,
  `mandatory` enum('-1') default NULL,
  `defaulttemplate` enum('default1','default2','default3','default4') NOT NULL default 'default1',
  PRIMARY KEY  (`mid`)
) TYPE=MyISAM AUTO_INCREMENT=5 ;

#
# Daten für Tabelle `site_menu`
#

INSERT INTO `site_menu` VALUES (1, 0, 'demo', NULL, 10, NULL, NULL, NULL, 'default1');
INSERT INTO `site_menu` VALUES (2, 1, 'test1', NULL, 10, NULL, NULL, NULL, 'default1');
INSERT INTO `site_menu` VALUES (3, 1, 'test2', NULL, 20, NULL, NULL, NULL, 'default1');
INSERT INTO `site_menu` VALUES (4, 0, 'impressum', NULL, 20, NULL, NULL, NULL, 'default1');

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `site_menu_lang`
#

CREATE TABLE `site_menu_lang` (
  `mlid` int(10) NOT NULL auto_increment,
  `mid` int(10) NOT NULL default '0',
  `lang` char(3) NOT NULL default 'ger',
  `label` varchar(30) NOT NULL default '',
  `exturl` varchar(128) default NULL,
  PRIMARY KEY  (`mlid`)
) TYPE=MyISAM AUTO_INCREMENT=5 ;

#
# Daten für Tabelle `site_menu_lang`
#

INSERT INTO `site_menu_lang` VALUES (1, 1, 'ger', 'Demo', NULL);
INSERT INTO `site_menu_lang` VALUES (2, 2, 'ger', 'Test 1', NULL);
INSERT INTO `site_menu_lang` VALUES (3, 3, 'ger', 'Test 2', NULL);
INSERT INTO `site_menu_lang` VALUES (4, 4, 'ger', 'Impressum', NULL);

# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `site_text`
#

CREATE TABLE `site_text` (
  `tid` int(11) NOT NULL auto_increment,
  `lang` varchar(4) NOT NULL default '',
  `label` varchar(20) NOT NULL default '',
  `crc32` enum('0','-1') NOT NULL default '0',
  `tname` varchar(40) NOT NULL default '',
  `ebene` text NOT NULL,
  `kategorie` text NOT NULL,
  `html` enum('-1','0') NOT NULL default '0',
  `content` text NOT NULL,
  PRIMARY KEY  (`tid`)
) TYPE=MyISAM PACK_KEYS=0 AUTO_INCREMENT=29 ;

#
# Daten für Tabelle `site_text`
#

INSERT INTO `site_text` VALUES (1, 'ger', 'ueberschrift', '0', 'main', '', '', '', 'Gratulation');
INSERT INTO `site_text` VALUES (2, 'ger', 'inhalt', '-1', 'main', '', 'index', '', 'ChaoS Networks eWeBuKi laeuft nun.\r\n\r\nUm dich am System anzumelden benutze bitte folgende Daten:\r\n\r\nuser: ewebuki\r\npass: ewebuki');
INSERT INTO `site_text` VALUES (3, 'ger', 'ueberschrift', '0', 'impressum', '', '', '', 'Impressum');
INSERT INTO `site_text` VALUES (4, 'ger', 'inhalt', '-1', 'impressum', '', 'impressum', '', 'eWeBuKi - Copyright 2003\r\nby [EMAIL=w.ammon@chaos.de]Werner Ammon[/EMAIL]');
INSERT INTO `site_text` VALUES (5, 'ger', 'fileed', '-1', 'auth.logout', '/demo', 'test1', '', 'FileEd');
INSERT INTO `site_text` VALUES (6, 'ger', 'passed', '-1', 'auth.logout', '/demo', 'test1', '', 'PassEd');
INSERT INTO `site_text` VALUES (7, 'ger', 'desc', '-1', 'auth.logout', '/images/business', 'po', '', 'Funktionen:');
INSERT INTO `site_text` VALUES (8, 'ger', 'leveled', '-1', 'auth.logout', '/images/business', 'po', '', 'LevelEd');
INSERT INTO `site_text` VALUES (9, 'ger', 'usered', '-1', 'auth.logout', '/images/business', 'po', '', 'UserEd');
INSERT INTO `site_text` VALUES (10, 'ger', 'menued', '-1', 'auth.logout', '/images/business', 'po', '', 'MenuEd');
INSERT INTO `site_text` VALUES (11, 'ger', 'ueberschrift', '-1', 'auth', '/images/business', 'po', '', 'Intern');
INSERT INTO `site_text` VALUES (12, 'ger', 'ueberschrift', '-1', 'index', '/images/business', 'po', '', 'Menu');
INSERT INTO `site_text` VALUES (13, 'ger', 'inhalt', '-1', 'demo', '/images/business', 'bgco', '', 'Bilder im Content.\r\n\r\n[IMG=/file/picture/original/img_1.png]eWeBuKi Logo[/IMG]');
INSERT INTO `site_text` VALUES (14, 'ger', 'senden', '-1', '-939795212.list', '/images/business', 'bgco', '', 'Go');
INSERT INTO `site_text` VALUES (15, 'ger', 'fieldlabel', '-1', '-939795212.list', '/images/business', 'bgco', '', 'Suche');
INSERT INTO `site_text` VALUES (16, 'ger', 'send_image', '-1', '-939795212.list', '/images/business', 'bgco', '', 'Zum Artikel');
INSERT INTO `site_text` VALUES (17, 'ger', 'describe', '-1', '-939795212.list', '/images/business', 'bgco', '', 'Metadaten');
INSERT INTO `site_text` VALUES (18, 'ger', 'delete1', '-1', '-939795212.list', '/images/business', 'bgco', '', 'Löschen');
INSERT INTO `site_text` VALUES (19, 'ger', 'gesamt', '-1', '-939795212.list', '/images/business', 'bgco', '', 'Gesamt:');
INSERT INTO `site_text` VALUES (20, 'ger', 'ffname', '-1', '-939795212.describe', '/images/business', 'po', '', 'Dateiname');
INSERT INTO `site_text` VALUES (21, 'ger', 'fdesc', '-1', '-939795212.describe', '/images/business', 'po', '', 'Beschreibung');
INSERT INTO `site_text` VALUES (22, 'ger', 'funder', '-1', '-939795212.describe', '/images/business', 'po', '', 'Beschriftung');
INSERT INTO `site_text` VALUES (23, 'ger', 'fhit', '-1', '-939795212.describe', '/images/business', 'po', '', 'Schlagworte');
INSERT INTO `site_text` VALUES (24, 'ger', 'upa', '-1', '-939795212.describe', '/images/business', 'po', '', 'Ersetzen durch');
INSERT INTO `site_text` VALUES (25, 'ger', 'upb', '-1', '-939795212.describe', '/images/business', 'po', '', 'Die Endung der vorhandenen Datei\r\nmuß mit der Endung der neuen\r\nDatei identisch sein!');
INSERT INTO `site_text` VALUES (26, 'ger', 'senden', '-1', '-939795212.describe', '/images/business', 'po', '', 'Eintragen');
INSERT INTO `site_text` VALUES (27, 'ger', 'reset', '-1', '-939795212.describe', '/images/business', 'po', '', 'Zurücksetzen');
INSERT INTO `site_text` VALUES (28, 'ger', 'abbruch', '-1', '-939795212.describe', '/images/business', 'po', '', 'Abbrechen');
