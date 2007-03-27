# phpMyAdmin MySQL-Dump
# version 2.3.2
# http://www.phpmyadmin.net/ (download page)
#
# Host: localhost
# Erstellungszeit: 21. Oktober 2002 um 16:44
# Server Version: 3.23.48
# PHP-Version: 4.1.0
# Datenbank: `chaoscms`
# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `site_form`
#

CREATE TABLE site_form (
  fid int(11) NOT NULL auto_increment,
  flabel varchar(20) NOT NULL default '',
  ftname varchar(40) NOT NULL default '',
  fsize varchar(7) NOT NULL default '0',
  fclass varchar(30) NOT NULL default '',
  fstyle varchar(60) NOT NULL default '',
  foption varchar(20) NOT NULL default '',
  frequired enum('0','-1') NOT NULL default '0',
  fcheck varchar(20) NOT NULL default '',
  PRIMARY KEY  (fid)
) TYPE=MyISAM COMMENT='formular settings';
# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `site_form_lang`
#

CREATE TABLE site_form_lang (
  flid int(11) NOT NULL auto_increment,
  fid int(11) NOT NULL default '0',
  flang char(3) NOT NULL default '',
  fwerte varchar(255) NOT NULL default '',
  PRIMARY KEY  (flid)
) TYPE=MyISAM COMMENT='formular settings sprachabhaengig';

