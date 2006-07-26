-- phpMyAdmin SQL Dump
-- version 2.6.4-pl3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 26. Juli 2006 um 10:02
-- Server Version: 4.0.24
-- PHP-Version: 5.1.4-0.0bpo1
--
-- Datenbank: `eWeBuKi`
--
-- "$Id$";

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `db_kontakt`
--

CREATE TABLE `db_kontakt` (
  `kid` int(11) NOT NULL default '0',
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
  PRIMARY KEY  (`kid`)
) TYPE=MyISAM;
