# phpMyAdmin MySQL-Dump
# version 2.3.2
# http://www.phpmyadmin.net/ (download page)
#
# Host: localhost
# Erstellungszeit: 14. Februar 2003 um 15:33
# Server Version: 3.23.52
# PHP-Version: 4.2.2
# Datenbank: `chaos`
# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `news_content`
#

CREATE TABLE news_content (
  inhaltid int(11) NOT NULL auto_increment,
  beitragid int(11) NOT NULL default '0',
  seite int(2) NOT NULL default '0',
  template int(1) default '0',
  inh_1 text,
  inh_2 text,
  inh_3 text,
  inh_4 text,
  inh_5 text,
  PRIMARY KEY  (inhaltid)
) TYPE=MyISAM;
# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `news_header`
#

CREATE TABLE news_header (
  beitragid int(11) NOT NULL auto_increment,
  beitrag varchar(18) NOT NULL default '',
  erstellt datetime NOT NULL default '0000-01-01 00:00:00',
  geaendert datetime default '0000-01-01 00:00:00',
  autor varchar(18) default NULL,
  leitartikel enum('0','-1') default '0',
  ausgabe varchar(7) default '0000-00',
  PRIMARY KEY  (beitragid)
) TYPE=MyISAM;

