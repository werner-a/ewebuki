-- phpMyAdmin MySQL-Dump
-- version 2.5.0
-- http://www.phpmyadmin.net/ (download page)
--
-- Host: localhost
-- Erstellungszeit: 25. August 2003 um 08:16
-- Server Version: 3.23.52
-- PHP-Version: 4.2.2
-- Datenbank: eWeBuKi
-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle auth_level
--
-- Erzeugt am: 25. August 2003 um 07:58
-- Aktualisiert am: 25. August 2003 um 07:58
--

CREATE TABLE auth_level (
  lid SERIAL,
  level varchar(10) NOT NULL default '',
  beschreibung text NOT NULL,
  PRIMARY KEY (lid)
);

--
-- Daten für Tabelle auth_level
--

INSERT INTO auth_level VALUES (1, 'cms_edit', 'berechtigt zum bearbeiten der templates');
-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle auth_right
--
-- Erzeugt am: 25. August 2003 um 07:58
-- Aktualisiert am: 25. August 2003 um 07:58
--

CREATE TABLE auth_right (
  rid SERIAL,
  uid int NOT NULL default '0',
  lid int NOT NULL default '0',
  PRIMARY KEY (rid),
  UNIQUE (rid)
);

--
-- Daten für Tabelle auth_right
--

INSERT INTO auth_right VALUES (1, 1, 1);
-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle auth_user
--
-- Erzeugt am: 25. August 2003 um 07:58
-- Aktualisiert am: 25. August 2003 um 08:04
--

CREATE TABLE auth_user (
  uid SERIAL,
  nachname varchar(40) NOT NULL default '',
  vorname varchar(40) NOT NULL default '',
  email varchar(60) NOT NULL default '',
  username varchar(20) NOT NULL default '',
  pass varchar(20) NOT NULL default '',
  PRIMARY KEY (uid),
  UNIQUE (uid)
);

--
-- Daten für Tabelle auth_user
--

INSERT INTO auth_user VALUES (1, '', '', '', 'ewebuki', 'JqXRXh15OlT8.');
-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle site_file
--
-- Erzeugt am: 25. August 2003 um 07:58
-- Aktualisiert am: 25. August 2003 um 07:58
--

CREATE TABLE site_file (
  fid SERIAL,
  frefid int NOT NULL default '0',
  fuid int NOT NULL default '0',
  fdid int NOT NULL default '0',
  ftname varchar(255) NOT NULL default '',
  ffname varchar(255) NOT NULL default '',
  ffart varchar(10) NOT NULL default 'jpg',
  fdesc varchar(255) NOT NULL default '',
  funder varchar(255) default NULL,
  fhit varchar(255) default NULL,
  fdel text,
  PRIMARY KEY (fid)
);

--
-- Daten für Tabelle site_file
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle site_form
--
-- Erzeugt am: 25. August 2003 um 07:58
-- Aktualisiert am: 25. August 2003 um 07:58
--

CREATE TABLE site_form (
  fid SERIAL,
  flabel varchar(20) NOT NULL default '',
  ftname varchar(40) NOT NULL default '',
  fsize varchar(7) NOT NULL default '0',
  fclass varchar(30) NOT NULL default '',
  fstyle varchar(60) NOT NULL default '',
  foption varchar(10) default NULL,
  frequired varchar(10) NOT NULL default '0',
  fcheck varchar(20) NOT NULL default '',
  PRIMARY KEY (fid)
);

--
-- Daten für Tabelle site_form
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle site_form_lang
--
-- Erzeugt am: 25. August 2003 um 07:58
-- Aktualisiert am: 25. August 2003 um 07:58
--

CREATE TABLE site_form_lang (
  flid SERIAL,
  fid int NOT NULL default '0',
  flang char(3) NOT NULL default 'ger',
  fpgenum text,
  fwerte varchar(255) NOT NULL default '',
  ferror varchar(255) NOT NULL default '',
  fdberror varchar(255) NOT NULL default '',
  PRIMARY KEY (flid)
);

--
-- Daten für Tabelle site_form_lang
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle site_menu
--
-- Erzeugt am: 25. August 2003 um 07:58
-- Aktualisiert am: 25. August 2003 um 07:58
--

CREATE TABLE site_menu (
  mid SERIAL,
  refid int default '0',
  entry varchar(30) NOT NULL default '',
  picture varchar(128) default NULL,
  sort int NOT NULL default '0',
  hide varchar(10) default NULL,
  level varchar(10) default NULL,
  mandatory varchar(10) default NULL,
  defaulttemplate varchar(10) NOT NULL default 'default1',
  PRIMARY KEY (mid)
);

--
-- Daten für Tabelle site_menu
--

INSERT INTO site_menu VALUES (1, 0, 'demo', NULL, 10, NULL, NULL, NULL, 'default1');
INSERT INTO site_menu VALUES (2, 1, 'test1', NULL, 10, NULL, NULL, NULL, 'default1');
INSERT INTO site_menu VALUES (3, 1, 'test2', NULL, 20, NULL, NULL, NULL, 'default1');
INSERT INTO site_menu VALUES (4, 0, 'impressum', NULL, 20, NULL, NULL, NULL, 'default1');
-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle site_menu_lang
--
-- Erzeugt am: 25. August 2003 um 07:58
-- Aktualisiert am: 25. August 2003 um 07:58
--

CREATE TABLE site_menu_lang (
  mlid SERIAL,
  mid int NOT NULL default '0',
  lang char(3) NOT NULL default 'ger',
  label varchar(30) NOT NULL default '',
  exturl varchar(128) default NULL,
  PRIMARY KEY (mlid)
);

--
-- Daten für Tabelle site_menu_lang
--

INSERT INTO site_menu_lang VALUES (1, 1, 'ger', 'Demo', NULL);
INSERT INTO site_menu_lang VALUES (2, 2, 'ger', 'Test 1', NULL);
INSERT INTO site_menu_lang VALUES (3, 3, 'ger', 'Test 2', NULL);
INSERT INTO site_menu_lang VALUES (4, 4, 'ger', 'Impressum', NULL);
-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle site_text
--
-- Erzeugt am: 25. August 2003 um 07:58
-- Aktualisiert am: 25. August 2003 um 08:04
--

CREATE TABLE site_text (
  tid SERIAL,
  lang varchar(4) NOT NULL default '',
  label varchar(20) NOT NULL default '',
  crc32 varchar(10) NOT NULL default '0',
  tname varchar(40) NOT NULL default '',
  ebene text NOT NULL,
  kategorie text NOT NULL,
  html varchar(10) NOT NULL default '0',
  content text NOT NULL,
  PRIMARY KEY (tid)
);

--
-- Daten für Tabelle site_text
--

INSERT INTO site_text VALUES (1, 'ger', 'ueberschrift', '0', 'main', '', '', '', 'Gratulation');
INSERT INTO site_text VALUES (2, 'ger', 'inhalt', '-1', 'main', '', 'index', '', 'ChaoS Networks eWeBuKi laeuft nun.\r\n\r\nUm dich am System anzumelden benutze bitte folgende Daten:\r\n\r\nuser: ewebuki\r\npass: ewebuki');
INSERT INTO site_text VALUES (3, 'ger', 'ueberschrift', '0', 'impressum', '', '', '', 'Impressum');
INSERT INTO site_text VALUES (4, 'ger', 'inhalt', '-1', 'impressum', '', 'impressum', '', 'eWeBuKi - Copyright 2003\r\nby [EMAIL=w.ammon@chaos.de]Werner Ammon[/EMAIL]');

