


# phpMyAdmin MySQL-Dump
# version 2.3.0
# http://phpwizard.net/phpMyAdmin/
# http://www.phpmyadmin.net/ (download page)
#
# Host: localhost
# Erstellungszeit: 10. September 2002 um 09:46
# Server Version: 3.23.47
# PHP-Version: 4.2.2
# Datenbank: `creative`
# --------------------------------------------------------

#
# Tabellenstruktur für Tabelle `bestof_fli4l`
#

CREATE TABLE bestof_fli4l (
  vom datetime NOT NULL default '0000-00-00 00:00:00',
  bis datetime NOT NULL default '0000-00-00 00:00:00',
  nick varchar(20) NOT NULL default '',
  mail varchar(40) NOT NULL default '',
  text longtext NOT NULL
) TYPE=MyISAM;

    


