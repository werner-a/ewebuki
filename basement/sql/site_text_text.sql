-- phpMyAdmin SQL Dump
-- version 2.8.2.1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Erstellungszeit: 19. September 2006 um 14:46
-- Server Version: 4.0.24
-- PHP-Version: 4.3.10-16
-- 
-- Datenbank: `eWeBuKi_mdetext`
-- 

-- 
-- Daten für Tabelle `site_text`
-- 

REPLACE INTO `site_text` (`lang`, `label`, `crc32`, `tname`, `ebene`, `kategorie`, `html`, `content`, `changed`, `bysurname`, `byforename`, `byemail`, `byalias`) VALUES ('de', 'ueberschrift', '-1', 'demo', '', 'demo', '0', 'Demoseite', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'inhalt', '-1', 'demo', '', 'demo', '0', 'Hier könnte [B]Ihr[/B] Text stehen.', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ueberschrift', '-1', 'impressum', '', 'impressum', '0', 'Impressum', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'inhalt', '-1', 'impressum', '', 'impressum', '0', 'eWeBuKi - Copyright 2003-2006\r\nby [EMAIL=w.ammon(at)chaos.de]Werner Ammon[/EMAIL]\r\n\r\nWeitere Infoseiten:\r\n[LINK=http://developer.berlios.de/projects/ewebuki/]developer.berlios.de/projects/ewebuki/[/LINK]\r\n[LINK=http://www.chaos.de/ewebuki.html]www.chaos.de/ewebuki.html[/LINK]', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ueberschrift', '-1', 'index', '', 'index', '0', 'Glückwunsch Ihr eWeBuKi läuft!', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'inhalt', '-1', 'index', '', 'index', '0', 'Um sich am System anzumelden benutzen Sie bitte folgende Daten:\r\n\r\nuser: ewebuki\r\npass: ewebuki\r\n\r\n[B]ACHTUNG:[/B] Passwort ändern nicht vergessen!\r\n\r\nWeitere Infoseiten:\r\n[LINK=http://developer.berlios.de/projects/ewebuki/]developer.berlios.de/projects/ewebuki/[/LINK]\r\n[LINK=http://www.chaos.de/ewebuki.html]www.chaos.de/ewebuki.html[/LINK]', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ueberschrift', '-1', 'show', '', 'show', '0', 'eWeBuKi Show', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'inhalt', '-1', 'show', '', 'show', '0', 'Tabellen Positionen:\r\n[TAB=;300;1]\r\n[ROW]\r\n[COL]1,1\r\n\r\n\r\n[/COL]\r\n[COL=;;u]1,2\r\n[/COL]\r\n[COL=r]1,3[/COL]\r\n[/ROW][ROW]\r\n[COL=m]2,1[/COL]\r\n[COL=;;g]2,2[/COL]\r\n[COL=r;;m]2,3\r\n\r\n\r\n[/COL]\r\n[/ROW]\r\n[/TAB]\r\n\r\n\r\nEasy Template Links:\r\n!#lnk_0\r\n!#lnk_1\r\n!#lnk_2\r\n!#lnk_3\r\n\r\nMenu oberhalb (M1,mit Bez.):\r\n[M1]nach oben[/M1]\r\n\r\nMenu oberhalb als Liste (M1=l,ohne Bez.);\r\n[M1=l][/M1]\r\n\r\nMenu gleiche Ebene (M2,mit Bez.)\r\n[M2]nach oben[/M2]\r\n\r\nMenu gleiche Ebene als Liste (M2=l,mit Bez.)\r\n[M2=l][/M2]\r\n\r\nTabellen Abstände (abstand text - tabelle 1)\r\n[TAB=;300;1]\r\n[ROW]\r\n[COL=l;150]links oben\r\n[/COL]\r\n[COL=l;150]rechts oben\r\n[/COL]\r\n[/ROW]\r\n[/TAB]\r\n[TAB=;300;1]\r\n[ROW]\r\n[COL=l;150]links oben\r\n[/COL]\r\n[COL=l;150]rechts oben\r\n[/COL]\r\n[/ROW]\r\n[/TAB]\r\nTabellen Abstände (abstand text - tabelle 2)\r\n\r\n[IN]I[/IN]nitial fuer Texte\r\n\r\n[H1][B][EM]Bold EM Tag[/EM][/B] im H1 Tag[/H1]\r\n\r\n\r\n\r\n\r\nText zwischen Linien:\r\n[HL][/HL]\r\nHier kommt der Text.\r\n[HL][/HL]\r\n\r\nWeit hinten, hinter den Wortbergen, fern der Länder Vokalien und Konsonantien leben die Blindtexte. Abgeschieden wohnen Sie[IMG=/file/picture/small/img_1.png;l;;;20;;20]eWeBuKi Logo[/IMG] in Buchstabhausen an der Küste des Semantik, eines großen Sprachozeans. Ein kleines Bächlein namens Duden fließt durch ihren Ort und versorgt sie mit den nötigen Regelialien. Es ist ein paradiesmatisches Land, in dem einem gebratene Satzteile in den Mund fliegen. Nicht einmal von der allmächtigen Interpunktion werden die Blindtexte beherrscht – ein geradezu unorthographisches Leben.\r\n\r\nBei Bildern rechts gibt es Abstand Probleme:\r\n[IMGB=/file/picture/small/img_1.png;r;0;b]Logo[/IMGB]\r\n\r\n\r\n\r\n\r\n[IMGB=/file/picture/small/img_1.png;r]Logo[/IMGB]\r\n\r\n\r\n\r\n\r\nZeilenumbrüche müssen passen, sonst kleben die Bilder nebeneinander.\r\n\r\n[H1]ueberschrift h1[/H1]\r\n[H2]ueberschrift h2[/H2]\r\n[H3]ueberschrift h3[/H3]\r\n[H4]ueberschrift h4[/H4]\r\n[H5]ueberschrift h5[/H5]\r\n[H6]ueberschrift h6[/H6]\r\n\r\nAbsaetze mit css einstellen:\r\n[P]Im Absatz ist es Schoen[/P]\r\n\r\nDIV=class jeder css im Content:\r\n[DIV=anderst]Dieser Text ist schoener als der Rest[/DIV]', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ueberschrift', '-1', '1924484980.test1', '/demo', 'test1', '0', 'Testseite 1', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'inhalt', '-1', '1924484980.test1', '/demo', 'test1', '0', 'Hier könnte [B]Ihr[/B] Text stehen.', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'ueberschrift', '-1', '1924484980.test2', '/demo', 'test2', '0', 'Testseite 2', '0000-00-00 00:00:00', '', '', '', 'ewebuki'),
('de', 'inhalt', '-1', '1924484980.test2', '/demo', 'test2', '0', 'Hier könnte [B]Ihr[/B] Text stehen.\r\n\r\n[LINK=demo3.html]404 Fehler mit Referer[/LINK]\r\n\r\nUm die zweite 404 Fehlermeldung (Referer unbekannt) sichtbar zu machen,\r\nin der Eingabezeile der obigen 404 Fehlermeldung einfach Enter drücken. ', '0000-00-00 00:00:00', '', '', '', 'ewebuki');
