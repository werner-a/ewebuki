# phpMyAdmin MySQL-Dump
# version 2.3.2
# http://www.phpmyadmin.net/ (download page)
#
# Host: localhost
# Erstellungszeit: 21. Oktober 2002 um 16:30
# Server Version: 3.23.48
# PHP-Version: 4.1.0
# Datenbank: `test`
# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `site_form`
#

CREATE TABLE site_form (
  fid int(11) NOT NULL auto_increment,
  label varchar(20) NOT NULL default '',
  tname varchar(40) NOT NULL default '',
  size varchar(7) NOT NULL default '0',
  class varchar(30) NOT NULL default '',
  style varchar(60) NOT NULL default '',
  opt varchar(20) NOT NULL default '',
  required enum('0','-1') NOT NULL default '0',
  check varchar(20) NOT NULL default '',
  PRIMARY KEY  (fid)
) TYPE=MyISAM COMMENT='formular settings';

#
# Daten für Tabelle `site_form`
#

INSERT INTO site_form VALUES (1, 'nick', 'fli4l.datenerfassung', '5', 'test', 'width:400;font-size:11px', '', '-1', '');
INSERT INTO site_form VALUES (2, 'cpu', 'fli4l.datenerfassung', '', 'test', 'width:200;font-size:9px', '', '0', '');
INSERT INTO site_form VALUES (3, 'mhz', 'fli4l.datenerfassung', '', '', '', '', '0', '');
INSERT INTO site_form VALUES (4, 'ram', 'fli4l.datenerfassung', '', '', '', '', '0', '');
INSERT INTO site_form VALUES (5, 'cdrom', 'fli4l.datenerfassung', '', '', '', '', '-1', '');
INSERT INTO site_form VALUES (6, 'monat', 'fli4l.datenerfassung', '', '', '', '', '0', '');
INSERT INTO site_form VALUES (7, 'opt', 'fli4l.datenerfassung', '', '', '', '', '0', '');
INSERT INTO site_form VALUES (8, 'plz', 'fli4l.datenerfassung', '5', '', '', '', '-1', '');
INSERT INTO site_form VALUES (9, 'bemerkung', 'fli4l.datenerfassung', '10;10', 'test', 'width:400;height:140;font-size:11px', 'wrap', '0', '');
# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `site_form_lang`
#

CREATE TABLE site_form_lang (
  flid int(11) NOT NULL auto_increment,
  fid int(11) NOT NULL default '0',
  lang char(3) NOT NULL default '',
  werte varchar(255) NOT NULL default '',
  PRIMARY KEY  (flid)
) TYPE=MyISAM COMMENT='formular settings sprachabhaengig';

#
# Daten für Tabelle `site_form_lang`
#

INSERT INTO site_form_lang VALUES (1, 2, 'ger', '386er;486er;Pentium;AMD-K6;AMD-K7;NS Geode;besser');
INSERT INTO site_form_lang VALUES (2, 3, 'ger', '');
INSERT INTO site_form_lang VALUES (3, 4, 'ger', '< 8;8 bis 15;16 bis 23;24 bis 31;32 bis 63;64 oder mehr');
INSERT INTO site_form_lang VALUES (4, 5, 'ger', 'ja;nein');
INSERT INTO site_form_lang VALUES (5, 6, 'ger', 'Januar;Februar;März;April;Mai;Juni');
INSERT INTO site_form_lang VALUES (6, 7, 'ger', 'weniger als 5;5 bis 10; mehr als 10');

