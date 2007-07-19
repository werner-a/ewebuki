--
-- PostgreSQL database dump
--

-- Started on 2007-07-18 12:13:26 CEST

SET client_encoding = 'LATIN9';
SET check_function_bodies = false;
SET client_min_messages = warning;

COMMENT ON SCHEMA public IS 'Standard public schema';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;


-- 
-- Tabellenstruktur für Tabelle `auth_level`
-- 

CREATE TABLE auth_level (
    lid serial NOT NULL,
    "level" character varying(10) DEFAULT ''::character varying NOT NULL,
    beschreibung text NOT NULL
);


SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('auth_level', 'lid'), 2, false);

-- 
-- Tabellenstruktur für Tabelle `auth_right`
-- 

CREATE TABLE auth_right (
    rid serial NOT NULL,
    uid integer DEFAULT 0 NOT NULL,
    lid integer DEFAULT 0 NOT NULL
);


SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('auth_right', 'rid'), 2, false);

-- 
-- Tabellenstruktur für Tabelle `auth_special`
-- 

CREATE TABLE auth_special (
    sid serial NOT NULL,
    suid integer DEFAULT 0 NOT NULL,
    content integer DEFAULT 0,
    sdb character varying(20) DEFAULT ''::character varying NOT NULL,
    stname character varying(50) DEFAULT ''::character varying NOT NULL,
    sebene text,
    skategorie text,
    sbeschreibung text
);

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('auth_special', 'sid'), 1, false);

-- 
-- Tabellenstruktur für Tabelle `auth_user`
-- 

CREATE TABLE auth_user (
    uid serial NOT NULL,
    nachname character varying(40) DEFAULT ''::character varying NOT NULL,
    vorname character varying(40) DEFAULT ''::character varying NOT NULL,
    email character varying(60) DEFAULT ''::character varying NOT NULL,
    username character varying(20) DEFAULT ''::character varying NOT NULL,
    pass character varying(20) DEFAULT ''::character varying NOT NULL
);

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('auth_user', 'uid'), 2, false);

-- 
-- Tabellenstruktur für Tabelle `site_file`
-- 

CREATE TABLE site_file (
    fid serial NOT NULL,
    frefid integer DEFAULT 0 NOT NULL,
    fuid integer DEFAULT 0 NOT NULL,
    fdid integer DEFAULT 0 NOT NULL,
    ftname character varying(255) DEFAULT ''::character varying NOT NULL,
    ffname character varying(255) DEFAULT ''::character varying NOT NULL,
    ffart character varying(10) DEFAULT 'jpg'::character varying NOT NULL,
    fdesc text DEFAULT ''::character varying NOT NULL,
    funder character varying(255),
    fhit character varying(255),
    fdel text
);

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('site_file', 'fid'), 1, false);

-- 
-- Tabellenstruktur für Tabelle `site_form`
-- 

CREATE TABLE site_form (
    fid serial NOT NULL,
    flabel character varying(20) DEFAULT ''::character varying NOT NULL,
    ftname character varying(40) DEFAULT ''::character varying NOT NULL,
    fsize character varying(7) DEFAULT '0'::character varying NOT NULL,
    fclass character varying(30) DEFAULT ''::character varying NOT NULL,
    fstyle character varying(60) DEFAULT ''::character varying NOT NULL,
    foption character varying(10),
    frequired character varying(10) DEFAULT '0'::character varying NOT NULL,
    fcheck text DEFAULT ''::character varying NOT NULL
);

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('site_form', 'fid'), 1, false);

-- 
-- Tabellenstruktur für Tabelle `site_form_lang`
-- 

CREATE TABLE site_form_lang (
    flid serial NOT NULL,
    fid integer DEFAULT 0 NOT NULL,
    flang character(5) DEFAULT 'de'::bpchar NOT NULL,
    fpgenum text,
    fwerte character varying(255) DEFAULT ''::character varying NOT NULL,
    ferror character varying(255) DEFAULT ''::character varying NOT NULL,
    fdberror character varying(255) DEFAULT ''::character varying NOT NULL,
    fchkerror character varying(255) NOT NULL
);

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('site_form_lang', 'flid'), 1, false);

-- 
-- Tabellenstruktur für Tabelle `site_menu`
-- 

CREATE TABLE site_menu (
    mid serial NOT NULL,
    refid integer DEFAULT 0,
    entry character varying(30) DEFAULT ''::character varying NOT NULL,
    picture character varying(128),
    sort integer DEFAULT 0 NOT NULL,
    hide character varying(10),
    "level" character varying(10),
    mandatory character varying(10),
    defaulttemplate character varying(10) DEFAULT 'default1'::character varying NOT NULL,
    dynamiccss character varying(5),
    dynamicbg character varying(128)
);

-- 
-- Tabellenstruktur für Tabelle `site_menu_lang`
-- 

CREATE TABLE site_menu_lang (
    mlid serial NOT NULL,
    mid integer DEFAULT 0 NOT NULL,
    lang character(5) DEFAULT 'de'::bpchar NOT NULL,
    label character varying(30) DEFAULT ''::character varying NOT NULL,
    exturl character varying(128),
    extend character varying(128)
);

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('site_menu_lang', 'mlid'), 6, false);

SELECT pg_catalog.setval(pg_catalog.pg_get_serial_sequence('site_menu', 'mid'), 6, false);

-- 
-- Tabellenstruktur für Tabelle `site_text`
-- 

CREATE TABLE site_text (
    lang character varying(4) DEFAULT ''::character varying NOT NULL,
    label character varying(20) DEFAULT ''::character varying NOT NULL,
    crc32 character varying(10) DEFAULT '0'::character varying NOT NULL,
    tname character varying(40) DEFAULT ''::character varying NOT NULL,
    ebene text NOT NULL,
    kategorie text NOT NULL,
    html character varying(10) DEFAULT '0'::character varying NOT NULL,
    content text NOT NULL,
    changed timestamp without time zone DEFAULT '1000-01-01 00:00:00'::timestamp without time zone,
    bysurname character varying(40),
    byforename character varying(40),
    byemail character varying(60),
    byalias character varying(20)
);

-- 
-- Daten für Tabelle `auth_level`
-- 

INSERT INTO auth_level (lid, "level", beschreibung) VALUES (1, 'cms_edit', 'berechtigt zum bearbeiten der templates');

-- 
-- Daten für Tabelle `auth_right`
-- 

INSERT INTO auth_right (rid, uid, lid) VALUES (1, 1, 1);

-- 
-- Daten für Tabelle `auth_user`
-- 

INSERT INTO auth_user (uid, nachname, vorname, email, username, pass) VALUES (1, '', '', '', 'ewebuki', 'JqXRXh15OlT8.');

-- 
-- Daten für Tabelle `site_menu`
-- 

INSERT INTO site_menu (mid, refid, entry, picture, sort, hide, "level", mandatory, defaulttemplate, dynamiccss, dynamicbg) VALUES (1, 0, 'demo', NULL, 10, NULL, NULL, NULL, 'default1', NULL, NULL);
INSERT INTO site_menu (mid, refid, entry, picture, sort, hide, "level", mandatory, defaulttemplate, dynamiccss, dynamicbg) VALUES (2, 1, 'test1', NULL, 10, NULL, NULL, NULL, 'default1', NULL, NULL);
INSERT INTO site_menu (mid, refid, entry, picture, sort, hide, "level", mandatory, defaulttemplate, dynamiccss, dynamicbg) VALUES (3, 1, 'test2', NULL, 20, NULL, NULL, NULL, 'default1', NULL, NULL);
INSERT INTO site_menu (mid, refid, entry, picture, sort, hide, "level", mandatory, defaulttemplate, dynamiccss, dynamicbg) VALUES (4, 0, 'show', NULL, 20, NULL, NULL, NULL, 'default1', NULL, NULL);
INSERT INTO site_menu (mid, refid, entry, picture, sort, hide, "level", mandatory, defaulttemplate, dynamiccss, dynamicbg) VALUES (5, 0, 'impressum', NULL, 30, NULL, NULL, NULL, 'default1', NULL, NULL);

-- 
-- Daten für Tabelle `site_menu_lang`
-- 

INSERT INTO site_menu_lang (mlid, mid, lang, label, exturl, extend) VALUES (1, 1, 'de   ', 'Demo', NULL, NULL);
INSERT INTO site_menu_lang (mlid, mid, lang, label, exturl, extend) VALUES (2, 2, 'de   ', 'Test 1', NULL, NULL);
INSERT INTO site_menu_lang (mlid, mid, lang, label, exturl, extend) VALUES (3, 3, 'de   ', 'Test 2', NULL, NULL);
INSERT INTO site_menu_lang (mlid, mid, lang, label, exturl, extend) VALUES (4, 4, 'de   ', 'eWeBuKi Show', NULL, NULL);
INSERT INTO site_menu_lang (mlid, mid, lang, label, exturl, extend) VALUES (5, 5, 'de   ', 'Impressum', NULL, NULL);

--
-- Daten für Tabelle `site_form`                                                                                                      --
--


INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES (1, 'username', '210295197.modify', '0', '', '', NULL, '-1', '');
INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES (2, 'pass', '210295197.modify', '0', '', '', 'password', '-1', '');
INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES (3, 'pass', '852881080.modify', '0', '', '', 'password', '-1', '');
INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES (4, 'fid', '-939795212.modify', '0', '', '', 'hidden', '-1', '');
INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES (5, 'entry', '-555504947.edit', '0', '', '', NULL, '-1', 'PREG:^[a-z_.-0-9]+$');
INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES (6, 'fdesc', '-939795212.modify', '25', '', '', NULL, '0', '');
INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES (7, 'funder', '-939795212.modify', '30', '', '', NULL, '0', '');
INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES (8, 'fhit', '-939795212.modify', '30', '', '', NULL, '0', '');

--
-- Daten für Tabelle `site_form`                                                                                                      --
--

INSERT INTO site_form_lang (flid, fid, flang, fpgenum, fwerte, ferror, fdberror, fchkerror) VALUES (1, 1, 'de', NULL, '', 'Username darf nicht leer sein.', 'Username bereits vorhanden.', '');
INSERT INTO site_form_lang (flid, fid, flang, fpgenum, fwerte, ferror, fdberror, fchkerror) VALUES (2, 2, 'de', NULL, '', 'Passworte nicht identisch oder leer.', '', '');
INSERT INTO site_form_lang (flid, fid, flang, fpgenum, fwerte, ferror, fdberror, fchkerror) VALUES (3, 3, 'de', NULL, '', 'Passworte nicht identisch oder leer.', '', '');
INSERT INTO site_form_lang (flid, fid, flang, fpgenum, fwerte, ferror, fdberror, fchkerror) VALUES (5, 5, 'de', NULL, '', '', '', 'Ungültige Zeichen im Feld Eintrag.');

-- 
-- Daten für Tabelle `site_text`
-- 

COPY site_text (lang, label, crc32, tname, ebene, kategorie, html, content, changed, bysurname, byforename, byemail, byalias) FROM stdin;
de	abort	-1	-555504947.delete	/admin/menued	delete	0	Abbrechen	1000-01-01 00:00:00				ewebuki
de	content	-1	-555504947.delete	/admin/menued	delete	0	Inhalt	1000-01-01 00:00:00				ewebuki
de	entry	-1	-555504947.delete	/admin/menued	delete	0	Eintrag	1000-01-01 00:00:00				ewebuki
de	error_menu	-1	-555504947.delete	/admin/menued	delete	0	Fehler beim löschen des Menüeintrag	1000-01-01 00:00:00				ewebuki
de	error_menu_lang	-1	-555504947.delete	/admin/menued	delete	0	Fehler beim löschen der Sprache(n)	1000-01-01 00:00:00				ewebuki
de	error_text	-1	-555504947.delete	/admin/menued	delete	0	Fehler beim löschen des/r Text/e	1000-01-01 00:00:00				ewebuki
de	inhalt	-1	-555504947.delete	/admin/menued	delete	0	Den Menüpunkt "!#ausgaben_entry" wirklich löschen?	1000-01-01 00:00:00				ewebuki
de	languages	-1	-555504947.delete	/admin/menued	delete	0	Sprachen	1000-01-01 00:00:00				ewebuki
de	no_content	-1	-555504947.delete	/admin/menued	delete	0	Kein Inhalt	1000-01-01 00:00:00				ewebuki
de	send	-1	-555504947.delete	/admin/menued	delete	0	Abschicken	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	-555504947.delete	/admin/menued	delete	0	Menü-Editor - Menüpunkt löschen	1000-01-01 00:00:00				ewebuki
de	abort	-1	-555504947.edit-multi	/admin/menued	edit	0	Abbrechen	1000-01-01 00:00:00				ewebuki
de	add	-1	-555504947.edit-multi	/admin/menued	edit	0	Neue Sprache hinzufügen	1000-01-01 00:00:00				ewebuki
de	basic	-1	-555504947.edit-multi	/admin/menued	add	0	Allgemein	1000-01-01 00:00:00				ewebuki
de	delete	-1	-555504947.edit-multi	/admin/menued	edit	0	Diese Sprache löschen	1000-01-01 00:00:00				ewebuki
de	entry	-1	-555504947.edit-multi	/admin/menued	edit	0	Eintrag	1000-01-01 00:00:00				ewebuki
de	error_lang_add	-1	-555504947.edit-multi	/admin/menued	edit	0	Diese Sprache ist bereits vorhanden.	1000-01-01 00:00:00				ewebuki
de	error_lang_delete	-1	-555504947.edit-multi	/admin/menued	edit	0	Die Entwickler Sprache kann nicht gelöscht werden.	1000-01-01 00:00:00				ewebuki
de	error_result	-1	-555504947.edit-multi	/admin/menued	edit	0	DB Fehler: 	1000-01-01 00:00:00				ewebuki
de	extended	-1	-555504947.edit-multi	/admin/menued	edit	0	Speziell	1000-01-01 00:00:00				ewebuki
de	exturl	-1	-555504947.edit-multi	/admin/menued	edit	0	externe Url	1000-01-01 00:00:00				ewebuki
de	hide	-1	-555504947.edit-multi	/admin/menued	edit	0	Deaktiviert	1000-01-01 00:00:00				ewebuki
de	label	-1	-555504947.edit-multi	/admin/menued	edit	0	Bezeichnung	1000-01-01 00:00:00				ewebuki
de	lang	-1	-555504947.edit-multi	/admin/menued	edit	0	Sprache	1000-01-01 00:00:00				ewebuki
de	language	-1	-555504947.edit-multi	/admin/menued	edit	0	Sprachen Verwaltung	1000-01-01 00:00:00				ewebuki
de	level	-1	-555504947.edit-multi	/admin/menued	edit	0	benötigter Level	1000-01-01 00:00:00				ewebuki
de	madatory	-1	-555504947.edit-multi	/admin/menued	edit	0	Erzwungen	1000-01-01 00:00:00				ewebuki
de	new_lang	-1	-555504947.edit-multi	/admin/menued	edit	0	Neue Sprache	1000-01-01 00:00:00				ewebuki
de	refid	-1	-555504947.edit-multi	/admin/menued	edit	0	Ref. ID	1000-01-01 00:00:00				ewebuki
de	reset	-1	-555504947.edit-multi	/admin/menued	edit	0	Zurücksetzen	1000-01-01 00:00:00				ewebuki
de	send	-1	-555504947.edit-multi	/admin/menued	edit	0	Abschicken	1000-01-01 00:00:00				ewebuki
de	sort	-1	-555504947.edit-multi	/admin/menued	edit	0	Sortierung	1000-01-01 00:00:00				ewebuki
de	template	-1	-555504947.edit-multi	/admin/menued	edit	0	Template	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	-555504947.edit-multi	/admin/menued	edit	0	Menü-Editor - Menüpunkt	1000-01-01 00:00:00				ewebuki
de	abort	-1	-555504947.edit-single	/admin/menued	edit	0	Abbrechen	1000-01-01 00:00:00				ewebuki
de	add	-1	-555504947.edit-single	/admin/menued	add	0	Neue Sprache hinzufügen	1000-01-01 00:00:00				ewebuki
de	basic	-1	-555504947.edit-single	/admin/menued	add	0	Allgemein	1000-01-01 00:00:00				ewebuki
de	entry	-1	-555504947.edit-single	/admin/menued	add	0	Eintrag	1000-01-01 00:00:00				ewebuki
de	error_lang_add	-1	-555504947.edit-single	/admin/menued	edit	0	Diese Sprache ist bereits vorhanden.	1000-01-01 00:00:00				ewebuki
de	error_lang_delete	-1	-555504947.edit-single	/admin/menued	edit	0	Die Entwickler Sprache kann nicht gelöscht werden.	1000-01-01 00:00:00				ewebuki
de	error_result	-1	-555504947.edit-single	/admin/menued	add	0	DB Fehler: 	1000-01-01 00:00:00				ewebuki
de	extended	-1	-555504947.edit-single	/admin/menued	add	0	Speziell	1000-01-01 00:00:00				ewebuki
de	exturl	-1	-555504947.edit-single	/admin/menued	add	0	ext. Url	1000-01-01 00:00:00				ewebuki
de	hide	-1	-555504947.edit-single	/admin/menued	edit	0	Versteckt	1000-01-01 00:00:00				ewebuki
de	label	-1	-555504947.edit-single	/admin/menued	add	0	Bezeichnung	1000-01-01 00:00:00				ewebuki
de	lang	-1	-555504947.edit-single	/admin/menued	add	0	Sprache	1000-01-01 00:00:00				ewebuki
de	level	-1	-555504947.edit-single	/admin/menued	add	0	benötigter Level	1000-01-01 00:00:00				ewebuki
de	madatory	-1	-555504947.edit-single	/admin/menued	add	0	Erzwungen	1000-01-01 00:00:00				ewebuki
de	new_lang	-1	-555504947.edit-single	/admin/menued	add	0	Neue Sprache	1000-01-01 00:00:00				ewebuki
de	refid	-1	-555504947.edit-single	/admin/menued	add	0	Ref ID.	1000-01-01 00:00:00				ewebuki
de	reset	-1	-555504947.edit-single	/admin/menued	edit	0	Zurücksetzen	1000-01-01 00:00:00				ewebuki
de	send	-1	-555504947.edit-single	/admin/menued	edit	0	Abschicken	1000-01-01 00:00:00				ewebuki
de	sort	-1	-555504947.edit-single	/admin/menued	add	0	Sortierung	1000-01-01 00:00:00				ewebuki
de	template	-1	-555504947.edit-single	/admin/menued	add	0	Template	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	-555504947.edit-single	/admin/menued	edit	0	Menü-Editor - Menüpunkt bearbeiten	1000-01-01 00:00:00				ewebuki
de	button_desc_add	-1	-555504947.list	/admin/menued	list	0	Unterpunkt hinzufügen	1000-01-01 00:00:00				ewebuki
de	button_desc_delete	-1	-555504947.list	/admin/menued	list	0	Löschen	1000-01-01 00:00:00				ewebuki
de	button_desc_down	-1	-555504947.list	/admin/menued	list	0	Sortierung - Nach unten	1000-01-01 00:00:00				ewebuki
de	button_desc_edit	-1	-555504947.list	/admin/menued	list	0	Bearbeiten	1000-01-01 00:00:00				ewebuki
de	button_desc_move	-1	-555504947.list	/admin/menued	list	0	Im Menü Baum verschieben	1000-01-01 00:00:00				ewebuki
de	button_desc_up	-1	-555504947.list	/admin/menued	list	0	Sortierung - Nach oben	1000-01-01 00:00:00				ewebuki
de	disabled	-1	-555504947.list	/admin/menued	list	0	Abgeschaltet	1000-01-01 00:00:00				ewebuki
de	enabled	-1	-555504947.list	/admin/menued	list	0	Eingeschaltet	1000-01-01 00:00:00				ewebuki
de	error1	-1	-555504947.list	/admin/menued	list	0	Menüpunkte mit Unterpunkten lassen sich nicht löschen.	1000-01-01 00:00:00				ewebuki
de	extern	-1	-555504947.list	/admin/menued	list	0	(extern)	1000-01-01 00:00:00				ewebuki
de	new	-1	-555504947.list	/admin/menued	list	0	Neuer Ast	1000-01-01 00:00:00				ewebuki
de	renumber	-1	-555504947.list	/admin/menued	list	0	Neu durchnummerieren	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	-555504947.list	/admin/menued	list	0	Menu-Editor - Übersicht	1000-01-01 00:00:00				ewebuki
de	abort	-1	-555504947.move	/admin/menued	move	0	Abbrechen	1000-01-01 00:00:00				ewebuki
de	entry	-1	-555504947.move	/admin/menued	move	0	Eintrag	1000-01-01 00:00:00				ewebuki
de	extern	-1	-555504947.move	/admin/menued	move	0	(extern)	1000-01-01 00:00:00				ewebuki
de	reset	-1	-555504947.move	/admin/menued	move	0	Zurücksetzen	1000-01-01 00:00:00				ewebuki
de	root	-1	-555504947.move	/admin/menued	move	0	Ins Hauptmenü	1000-01-01 00:00:00				ewebuki
de	send	-1	-555504947.move	/admin/menued	move	0	Abschicken	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	-555504947.move	/admin/menued	move	0	Menü-Editor - Menüpunkt verschieben	1000-01-01 00:00:00				ewebuki
de	send	-1	852881080.modify	/admin/passed	modify	0	Abschicken	1000-01-01 00:00:00				ewebuki
de	chkpass	-1	852881080.modify	/admin/passed	modify	0	Wiederholung	1000-01-01 00:00:00				ewebuki
de	inhalt	-1	852881080.modify	/admin/passed	modify	0	Passwort ändern	1000-01-01 00:00:00				ewebuki
de	newpass	-1	852881080.modify	/admin/passed	modify	0	Neues	1000-01-01 00:00:00				ewebuki
de	oldpass	-1	852881080.modify	/admin/passed	modify	0	Altes	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	852881080.modify	/admin/passed	modify	0	Passwort Editor	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	auth		index	0	Überschrift	1000-01-01 00:00:00				ewebuki
de	desc	-1	auth.logout		index	0	Werkzeuge	1000-01-01 00:00:00				ewebuki
de	fileed	-1	auth.logout		index	0	Datei-Editor	1000-01-01 00:00:00				ewebuki
de	leveled	-1	auth.logout		index	0	Level-Editor	1000-01-01 00:00:00				ewebuki
de	menued	-1	auth.logout		index	0	Menü-Editor	1000-01-01 00:00:00				ewebuki
de	nachher	-1	auth.logout		index	0	ist angemeldet.	1000-01-01 00:00:00				ewebuki
de	passed	-1	auth.logout		index	0	Passwort-Editor	1000-01-01 00:00:00				ewebuki
de	usered	-1	auth.logout		index	0	User-Editor	1000-01-01 00:00:00				ewebuki
de	vorher	-1	auth.logout		index	0	Benutzer	1000-01-01 00:00:00				ewebuki
de	abort	-1	cms.edit.cmstag		index	0	Abbrechen	1000-01-01 00:00:00				ewebuki
de	add	-1	cms.edit.cmstag		index	0	Datei einfügen	1000-01-01 00:00:00				ewebuki
de	b	-1	cms.edit.cmstag		index	0	Fett	1000-01-01 00:00:00				ewebuki
de	big	-1	cms.edit.cmstag		index	0	Grösser als der Rest	1000-01-01 00:00:00				ewebuki
de	br	-1	cms.edit.cmstag		index	0	Erzwungener Umbruch	1000-01-01 00:00:00				ewebuki
de	cent	-1	cms.edit.cmstag		index	0	Zentriert	1000-01-01 00:00:00				ewebuki
de	center	-1	cms.edit.cmstag		index	0	Zentriert	1000-01-01 00:00:00				ewebuki
de	cite	-1	cms.edit.cmstag		index	0	Logisch: cite	1000-01-01 00:00:00				ewebuki
de	col	-1	cms.edit.cmstag		index	0	Tabellenspalte	1000-01-01 00:00:00				ewebuki
de	db	-1	cms.edit.cmstag		index	0	DB	1000-01-01 00:00:00				ewebuki
de	div	-1	cms.edit.cmstag		index	0	Bereich	1000-01-01 00:00:00				ewebuki
de	e	-1	cms.edit.cmstag		index	0	Mail	1000-01-01 00:00:00				ewebuki
de	em	-1	cms.edit.cmstag		index	0	Logisch: emphatisch	1000-01-01 00:00:00				ewebuki
de	email	-1	cms.edit.cmstag		index	0	eMail Link	1000-01-01 00:00:00				ewebuki
de	file	-1	cms.edit.cmstag		index	0	Datei	1000-01-01 00:00:00				ewebuki
de	files	-1	cms.edit.cmstag		index	0	Dateien	1000-01-01 00:00:00				ewebuki
de	h1	-1	cms.edit.cmstag		index	0	Überschrift Klasse 1	1000-01-01 00:00:00				ewebuki
de	h2	-1	cms.edit.cmstag		index	0	Überschrift Klasse 2	1000-01-01 00:00:00				ewebuki
de	hl	-1	cms.edit.cmstag		index	0	Spezielle Trennlinie	1000-01-01 00:00:00				ewebuki
de	hr	-1	cms.edit.cmstag		index	0	Trennlinie	1000-01-01 00:00:00				ewebuki
de	i	-1	cms.edit.cmstag		index	0	Kursiv	1000-01-01 00:00:00				ewebuki
de	img	-1	cms.edit.cmstag		index	0	Bild	1000-01-01 00:00:00				ewebuki
de	imgb	-1	cms.edit.cmstag		index	0	Bild mit Rahmen	1000-01-01 00:00:00				ewebuki
de	in	-1	cms.edit.cmstag		index	0	Initial	1000-01-01 00:00:00				ewebuki
de	label	-1	cms.edit.cmstag		index	0	Marke	1000-01-01 00:00:00				ewebuki
de	language	-1	cms.edit.cmstag		index	0	Sprache	1000-01-01 00:00:00				ewebuki
de	link	-1	cms.edit.cmstag		index	0	Link	1000-01-01 00:00:00				ewebuki
de	list	-1	cms.edit.cmstag		index	0	Liste	1000-01-01 00:00:00				ewebuki
de	m1	-1	cms.edit.cmstag		index	0	Menü dieser Ebene	1000-01-01 00:00:00				ewebuki
de	m2	-1	cms.edit.cmstag		index	0	Menü der Unterebene	1000-01-01 00:00:00				ewebuki
de	pre	-1	cms.edit.cmstag		index	0	Vorformatiert	1000-01-01 00:00:00				ewebuki
de	quote	-1	cms.edit.cmstag		index	0	In Anführungszeichen	1000-01-01 00:00:00				ewebuki
de	row	-1	cms.edit.cmstag		index	0	Tabellenzeile	1000-01-01 00:00:00				ewebuki
de	s	-1	cms.edit.cmstag		index	0	Durchgestrichen	1000-01-01 00:00:00				ewebuki
de	save	-1	cms.edit.cmstag		index	0	Speichern	1000-01-01 00:00:00				ewebuki
de	small	-1	cms.edit.cmstag		index	0	Kleiner als der Rest	1000-01-01 00:00:00				ewebuki
de	sp	-1	cms.edit.cmstag		index	0	Geschütztes Leerzeichen	1000-01-01 00:00:00				ewebuki
de	strong	-1	cms.edit.cmstag		index	0	Logisch: strong	1000-01-01 00:00:00				ewebuki
de	sub	-1	cms.edit.cmstag		index	0	Tiefgestellt	1000-01-01 00:00:00				ewebuki
de	sup	-1	cms.edit.cmstag		index	0	Hochgestellt	1000-01-01 00:00:00				ewebuki
de	tab	-1	cms.edit.cmstag		index	0	Tabelle	1000-01-01 00:00:00				ewebuki
de	tagselect	-1	cms.edit.cmstag		index	0	Tag auswählen	1000-01-01 00:00:00				ewebuki
de	template	-1	cms.edit.cmstag		index	0	Template	1000-01-01 00:00:00				ewebuki
de	tt	-1	cms.edit.cmstag		index	0	Dickengleich	1000-01-01 00:00:00				ewebuki
de	u	-1	cms.edit.cmstag		index	0	Unterstrichen	1000-01-01 00:00:00				ewebuki
de	up	-1	cms.edit.cmstag		index	0	Zurück-Link	1000-01-01 00:00:00				ewebuki
de	upload	-1	cms.edit.cmstag		index	0	Hinaufladen	1000-01-01 00:00:00				ewebuki
de	inhalt	-1	404		indi	0	Die Uri !#ausgaben_404seite wurde nicht gefunden.\n\nLeider konnte das System nicht feststellen woher sie gekommen sind.	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	404		indi	0	Fehler 404 - Nicht gefunden.	1000-01-01 00:00:00				ewebuki
de	error_dupe	-1	-555504947.edit-single	/admin/menued	add	0	Der Eintrag ist bereits vorhanden.	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	404referer		test3	0	Fehler 404 - Nicht gefunden.	1000-01-01 00:00:00				ewebuki
de	inhalt	-1	404referer		test3	0	Die Uri: !#ausgaben_404seite wurde nicht gefunden.\n\nDie [LINK=!#ausgaben_404referer]Seite[/LINK] enthaelt einen falschen/alten Link.	1000-01-01 00:00:00				ewebuki
de	error_dupe	-1	-555504947.edit-multi	/admin/menued	edit	0	Der Eintrag ist bereits vorhanden.	1000-01-01 00:00:00				ewebuki
de	error_dupe	-1	-555504947.move	/admin/menued	move	0	In dieser Ebene existiert bereits ein Eintrag mit gleichem Namen.	1000-01-01 00:00:00				ewebuki
de	logout	-1	auth.login		auth.login	0	Abgemeldet	1000-01-01 00:00:00				ewebuki
de	denied	-1	auth.login		auth.login	0	Zugriff verweigert!	1000-01-01 00:00:00				ewebuki
de	picture	-1	-555504947.edit-multi	/admin/menued	edit	0	evt. Bild	1000-01-01 00:00:00				ewebuki
de	picture	-1	-555504947.edit-single	/admin/menued	edit	0	evt. Bild	1000-01-01 00:00:00				ewebuki
de	reset	-1	852881080.modify	/admin/passed	modify	0	Zurücksetzen	1000-01-01 00:00:00				ewebuki
de	abort	-1	852881080.modify	/admin/passed	modify	0	Abbrechen	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	-840786483.list	/admin/menued	list	0	Level-Editor - Übersicht	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	-840786483.modify	/admin/menued	edit	0	Level-Editor - Bearbeiten	1000-01-01 00:00:00				ewebuki
de	level	-1	-840786483.modify	/admin/leveled	modify	0	Bezeichnung	1000-01-01 00:00:00				ewebuki
de	description	-1	-840786483.modify	/admin/leveled	modify	0	Beschreibung	1000-01-01 00:00:00				ewebuki
de	del	-1	-840786483.modify	/admin/leveled	modify	0	entfernen	1000-01-01 00:00:00				ewebuki
de	add	-1	-840786483.modify	/admin/leveled	modify	0	hinzufügen	1000-01-01 00:00:00				ewebuki
de	send	-1	-840786483.modify	/admin/leveled	modify	0	Abschicken	1000-01-01 00:00:00				ewebuki
de	reset	-1	-840786483.modify	/admin/leveled	modify	0	Zurücksetzen	1000-01-01 00:00:00				ewebuki
de	abort	-1	-840786483.modify	/admin/leveled	modify	0	Abbrechen	1000-01-01 00:00:00				ewebuki
de	send	-1	-840786483.delete	/admin/leveled	modify	0	Löschen	1000-01-01 00:00:00				ewebuki
de	abort	-1	-840786483.delete	/admin/leveled	modify	0	Abbrechen	1000-01-01 00:00:00				ewebuki
de	frage	-1	-840786483.delete	/admin/leveled	modify	0	Wollen Sie den Level "!#ausgaben_level" wirklich löschen?	1000-01-01 00:00:00				ewebuki
de	level	-1	-840786483.details	/admin/leveled	details	0	Bezeichnung	1000-01-01 00:00:00				ewebuki
de	user	-1	-840786483.details	/admin/leveled	details	0	Mitglieder	1000-01-01 00:00:00				ewebuki
de	beschreibung	-1	-840786483.details	/admin/leveled	details	0	Beschreibung	1000-01-01 00:00:00				ewebuki
de	edit	-1	-840786483.details	/admin/leveled	details	0	Bearbeiten	1000-01-01 00:00:00				ewebuki
de	list	-1	-840786483.details	/admin/leveled	details	0	Übersicht	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	-840786483.details	/admin/leveled	details	0	Level Editor - Eigenschaften	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	-840786483.delete	/admin/leveled	modify	0	Level-Editor - Löschen	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	210295197.list	/admin/usered	list	0	User-Editor - Übersicht	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	-939795212.list	/admin/fileed	list	0	Datei-Editor - Übersicht	1000-01-01 00:00:00				ewebuki
de	search	-1	-939795212.list	/admin/fileed	list	0	Suche	1000-01-01 00:00:00				ewebuki
de	gesamt	-1	-939795212.list	/admin/fileed	list	0	Gesamt:	1000-01-01 00:00:00				ewebuki
de	send	-1	-939795212.list	/admin/fileed	list	0	Go	1000-01-01 00:00:00				ewebuki
de	fileedit	-1	-939795212.list	/admin/fileed	list	0	Bearbeiten	1000-01-01 00:00:00				ewebuki
de	filedelete	-1	-939795212.list	/admin/fileed	list	0	Löschen	1000-01-01 00:00:00				ewebuki
de	ffname	-1	-939795212.modify	/admin	usered	0	Dateiname	1000-01-01 00:00:00				ewebuki
de	fdesc	-1	-939795212.modify	/admin	usered	0	Bildbeschreibung	1000-01-01 00:00:00				ewebuki
de	funder	-1	-939795212.modify	/admin	usered	0	Bildunterschrift	1000-01-01 00:00:00				ewebuki
de	fhit	-1	-939795212.modify	/admin	usered	0	Schlagworte	1000-01-01 00:00:00				ewebuki
de	upa	-1	-939795212.modify	/admin	usered	0	Die aktuelle Datei durch	1000-01-01 00:00:00				ewebuki
de	upb	-1	-939795212.modify	/admin	usered	0	ersetzen.	1000-01-01 00:00:00				ewebuki
de	send	-1	-939795212.modify	/admin	usered	0	Abschicken	1000-01-01 00:00:00				ewebuki
de	reset	-1	-939795212.modify	/admin	usered	0	Zurücksetzen	1000-01-01 00:00:00				ewebuki
de	abort	-1	-939795212.modify	/admin	usered	0	Abbrechen	1000-01-01 00:00:00				ewebuki
de	cmslink	-1	-939795212.list	/admin/fileed	list	0	zum Content Editor	1000-01-01 00:00:00				ewebuki
de	level	-1	-840786483.list	/admin/leveled	list	0	Bezeichnung	1000-01-01 00:00:00				ewebuki
de	beschreibung	-1	-840786483.list	/admin/leveled	list	0	Beschreibung	1000-01-01 00:00:00				ewebuki
de	modify	-1	-840786483.list	/admin/leveled	list	0	Bearbeiten	1000-01-01 00:00:00				ewebuki
de	edit	-1	-840786483.list	/admin/leveled	list	0	Bearbeiten	1000-01-01 00:00:00				ewebuki
de	delete	-1	-840786483.list	/admin/leveled	list	0	Löschen	1000-01-01 00:00:00				ewebuki
de	details	-1	-840786483.list	/admin/leveled	list	0	Eigenschaften	1000-01-01 00:00:00				ewebuki
de	senden	-1	210295197.modify	/admin/usered	modify	0	Abschicken	1000-01-01 00:00:00				ewebuki
de	reset	-1	210295197.modify	/admin/usered	modify	0	Zurücksetzen	1000-01-01 00:00:00				ewebuki
de	abort	-1	210295197.modify	/admin/usered	modify	0	Abbrechen	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	210295197.modify	/admin/usered	modify	0	User-Editor - Bearbeiten	1000-01-01 00:00:00				ewebuki
de	error_oldpass	-1	852881080.modify	/admin/passed	modify	0	Das alte Passwort stimmt nicht!	1000-01-01 00:00:00				ewebuki
de	error_chkpass	-1	852881080.modify	/admin/passed	modify	0	Das Neue Passwort und die Wiederholung stimmen nicht überein!	1000-01-01 00:00:00				ewebuki
de	nachname	-1	210295197.modify	/admin/usered	modify	0	Nachname	1000-01-01 00:00:00				ewebuki
de	vorname	-1	210295197.modify	/admin/usered	modify	0	Vorname	1000-01-01 00:00:00				ewebuki
de	email	-1	210295197.modify	/admin/usered	modify	0	eMail	1000-01-01 00:00:00				ewebuki
de	new	-1	-840786483.list	/admin/leveled	list	0	Neuer Level	1000-01-01 00:00:00				ewebuki
de	new	-1	210295197.list	/admin/usered	list	0	Neuer User	1000-01-01 00:00:00				ewebuki
de	frage	-1	210295197.delete	/admin/usered	modify	0	Wollen Sie den User "!#ausgaben_username" wirklich löschen?	1000-01-01 00:00:00				ewebuki
de	delete	-1	210295197.delete	/admin/usered	modify	0	Delete	1000-01-01 00:00:00				ewebuki
de	abort	-1	210295197.delete	/admin/usered	modify	0	Abbrechen	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	210295197.delete	/admin/usered	modify	0	User-Editor - Löschen	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	210295197.details	/admin/usered	details	0	User-Editor - Eigenschaften	1000-01-01 00:00:00				ewebuki
de	username	-1	210295197.modify	/admin/usered	modify	0	Login	1000-01-01 00:00:00				ewebuki
de	newpass	-1	210295197.modify	/admin/usered	modify	0	Passwort	1000-01-01 00:00:00				ewebuki
de	chkpass	-1	210295197.modify	/admin/usered	modify	0	Wiederholung	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	base		impressum	0	Menu	1000-01-01 00:00:00				ewebuki
de	copyright	-1	base		index	0	eWeBuKi - Copyright 2003-2006	1000-01-01 00:00:00				ewebuki
de	kekse	-1	base		impressum	0	Kekse	1000-01-01 00:00:00				ewebuki
de	bloged	-1	auth.logout	/admin/passed	modify	0	Blog-Editor	1000-01-01 00:00:00				ewebuki
de	send	-1	-939795212.delete	/admin/menued	list	0	Abschicken	1000-01-01 00:00:00				ewebuki
de	abort	-1	-939795212.delete	/admin/menued	list	0	Abbrechen	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	-939795212.delete	/admin/menued	delete	0	Datei Editor - Datei löschen!	1000-01-01 00:00:00				ewebuki
de	inhalt	-1	-939795212.delete	/admin/menued	delete	0	Die Datei "!#ausgaben_ffname" wirklich löschen?	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	-939795212.upload	/admin/menued	list	0	Datei-Editor Upload	1000-01-01 00:00:00				ewebuki
de	file	-1	-939795212.upload	/admin/menued	list	0	Dateiauswahl	1000-01-01 00:00:00				ewebuki
de	send	-1	-939795212.upload	/admin/menued	list	0	Abschicken	1000-01-01 00:00:00				ewebuki
de	reset	-1	-939795212.upload	/admin/menued	edit	0	Zurücksetzen	1000-01-01 00:00:00				ewebuki
de	abort	-1	-939795212.upload	/admin/menued	edit	0	Abbrechen	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	-939795212.modify	/admin/menued	add	0	Datei-Editor - Datei Eigenschaften bearbeiten	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	demo		demo	0	Demoseite	1000-01-01 00:00:00				ewebuki
de	inhalt	-1	demo		demo	0	Hier könnte [B]Ihr[/B] Text stehen.	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	impressum		impressum	0	Impressum	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	index		index	0	Glückwunsch Ihr eWeBuKi läuft!	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	show		show	0	eWeBuKi Show	1000-01-01 00:00:00				ewebuki
de	inhalt	-1	impressum		impressum	0	eWeBuKi - Copyright 2003-2006\r\nby [EMAIL=w.ammon(at)chaos.de]Werner Ammon[/EMAIL]\r\n\r\nWeitere Infoseiten:\r\n[LINK=http://developer.berlios.de/projects/ewebuki/]developer.berlios.de/projects/ewebuki/[/LINK]\r\n[LINK=http://www.chaos.de/ewebuki.html]www.chaos.de/ewebuki.html[/LINK]	2007-07-18 12:28:58				ewebuki
de	ueberschrift	-1	1924484980.test1	/demo	test1	0	Testseite 1	1000-01-01 00:00:00				ewebuki
de	inhalt	-1	1924484980.test1	/demo	test1	0	Hier könnte [B]Ihr[/B] Text stehen.	1000-01-01 00:00:00				ewebuki
de	ueberschrift	-1	1924484980.test2	/demo	test2	0	Testseite 2	1000-01-01 00:00:00				ewebuki
de	answera	-1	-939795212.list	/admin/fileed	list	0	Ihre Schnellsuche nach	2006-09-26 12:18:44				ewebuki
de	answerb	-1	-939795212.list	/admin/fileed	list	0	hat	2006-09-26 12:18:58				ewebuki
de	answerc_no	-1	-939795212.list	/admin/fileed	list	0	keine Einträge gefunden.	2006-09-26 12:19:42				ewebuki
de	answerc_yes	-1	-939795212.list	/admin/fileed	list	0	folgende Einträge gefunden.	2006-09-26 12:20:01				ewebuki
de	next	-1	-939795212.list	/admin/fileed	list	0	Vorherige Seite	2006-09-26 12:22:25				ewebuki
de	prev	-1	-939795212.list	/admin/fileed	list	0	Nexte Seite	2006-09-26 12:22:35				ewebuki
de	error1	-1	-939795212.list	/admin/fileed	list	0	Bild wird bereits verwendet - Bearbeiten zeigt wo.	2006-10-06 20:07:05				ewebuki
de	error2	-1	-939795212.list	/admin/fileed	list	0	Bild kann nur vom Eigentümer gelöscht werden.	2006-10-06 20:22:05				ewebuki
de	error_edit	-1	-939795212.modify	/admin/fileed	edit	0	Bild kann nur vom Eigentümer bearbeitet werden.	2006-10-06 20:44:19				ewebuki
de	references	-1	-939795212.modify	/admin/fileed	edit	0	Ist enthalten in:	2006-10-06 19:59:07				ewebuki
de	inhalt	-1	index		index	0	Um sich am System anzumelden benutzen Sie bitte folgende Daten:\r\n\r\nuser: ewebuki\r\npass: ewebuki\r\n\r\n[B]ACHTUNG:[/B] Passwort ändern nicht vergessen!\r\n\r\nWeitere Infoseiten:\r\n[LINK=http://developer.berlios.de/projects/ewebuki/]developer.berlios.de/projects/ewebuki/[/LINK]\r\n[LINK=http://www.chaos.de/ewebuki.html]www.chaos.de/ewebuki.html[/LINK]	2007-07-18 12:30:44				ewebuki
de	inhalt	-1	show		show	0	Tabellen Positionen:\r\n[TAB=;300;1]\r\n[ROW]\r\n[COL]1,1\r\n\r\n\r\n[/COL]\r\n[COL=;;u]1,2\r\n[/COL]\r\n[COL=r]1,3[/COL]\r\n[/ROW][ROW]\r\n[COL=m]2,1[/COL]\r\n[COL=;;g]2,2[/COL]\r\n[COL=r;;m]2,3\r\n\r\n\r\n[/COL]\r\n[/ROW]\r\n[/TAB]\r\n\r\n\r\nEasy Template Links:\r\n\r\n!#lnk_1\r\n!#lnk_2\r\n!#lnk_3\r\n\r\nMenu oberhalb (M1,mit Bez.):\r\n[M1]nach oben[/M1]\r\n\r\nMenu oberhalb als Liste (M1=l,ohne Bez.);\r\n[M1=l][/M1]\r\n\r\nMenu gleiche Ebene (M2,mit Bez.)\r\n[M2]nach oben[/M2]\r\n\r\nMenu gleiche Ebene als Liste (M2=l,mit Bez.)\r\n[M2=l][/M2]\r\n\r\nTabellen Abstände (abstand text - tabelle 1)\r\n[TAB=;300;1]\r\n[ROW]\r\n[COL=l;150]links oben\r\n[/COL]\r\n[COL=l;150]rechts oben\r\n[/COL]\r\n[/ROW]\r\n[/TAB]\r\n[TAB=;300;1]\r\n[ROW]\r\n[COL=l;150]links oben\r\n[/COL]\r\n[COL=l;150]rechts oben\r\n[/COL]\r\n[/ROW]\r\n[/TAB]\r\nTabellen Abstände (abstand text - tabelle 2)\r\n\r\n[IN]I[/IN]nitial fuer Texte\r\n\r\n[H1][B][EM]Bold EM Tag[/EM][/B] im H1 Tag[/H1]\r\n\r\n\r\n\r\n\r\nText zwischen Linien:\r\n[HL][/HL]\r\nHier kommt der Text.\r\n[HL][/HL]\r\n\r\nWeit hinten, hinter den Wortbergen, fern der Länder Vokalien und Konsonantien leben die Blindtexte. Abgeschieden wohnen Sie[IMG=/file/picture/small/img_1.png;l;;;20;;20]eWeBuKi Logo[/IMG] in Buchstabhausen an der Küste des Semantik, eines großen Sprachozeans. Ein kleines Bächlein namens Duden fließt durch ihren Ort und versorgt sie mit den nötigen Regelialien. Es ist ein paradiesmatisches Land, in dem einem gebratene Satzteile in den Mund fliegen. Nicht einmal von der allmächtigen Interpunktion werden die Blindtexte beherrscht – ein geradezu unorthographisches Leben.\r\n\r\nBei Bildern rechts gibt es Abstand Probleme:\r\n[IMGB=/file/picture/small/img_1.png;r;0;b]Logo[/IMGB]\r\n\r\n\r\n\r\n\r\n[IMGB=/file/picture/small/img_1.png;r]Logo[/IMGB]\r\n\r\n\r\n\r\n\r\nZeilenumbrüche müssen passen, sonst kleben die Bilder nebeneinander.\r\n\r\n[H1]ueberschrift h1[/H1]\r\n[H2]ueberschrift h2[/H2]\r\n[H3]ueberschrift h3[/H3]\r\n[H4]ueberschrift h4[/H4]\r\n[H5]ueberschrift h5[/H5]\r\n[H6]ueberschrift h6[/H6]\r\n\r\nAbsaetze mit css einstellen:\r\n[P]Im Absatz ist es Schoen[/P]\r\n\r\nDIV=class jeder css im Content:\r\n[DIV=anderst]Dieser Text ist schoener als der Rest[/DIV]	2007-07-18 12:32:46				ewebuki
de	inhalt	-1	1924484980.test2	/demo	test2	0	Hier könnte [B]Ihr[/B] Text stehen.\r\n\r\n[LINK=demo3.html]404 Fehler mit Referer[/LINK]\r\n\r\nUm die zweite 404 Fehlermeldung (Referer unbekannt) sichtbar zu machen,\r\nin der Eingabezeile der obigen 404 Fehlermeldung einfach Enter drücken. 	2007-07-18 12:33:22				ewebuki
\.


ALTER TABLE ONLY auth_level
    ADD CONSTRAINT auth_level_pkey PRIMARY KEY (lid);


ALTER TABLE ONLY auth_right
    ADD CONSTRAINT auth_right_pkey PRIMARY KEY (rid);


ALTER TABLE ONLY auth_special
    ADD CONSTRAINT auth_special_pkey PRIMARY KEY (sid);


ALTER TABLE ONLY auth_user
    ADD CONSTRAINT auth_user_pkey PRIMARY KEY (uid);


ALTER TABLE ONLY site_file
    ADD CONSTRAINT site_file_pkey PRIMARY KEY (fid);

ALTER TABLE ONLY site_form_lang
    ADD CONSTRAINT site_form_lang_pkey PRIMARY KEY (flid);

ALTER TABLE ONLY site_form
    ADD CONSTRAINT site_form_pkey PRIMARY KEY (fid);

ALTER TABLE ONLY site_menu_lang
    ADD CONSTRAINT site_menu_lang_pkey PRIMARY KEY (mlid);

ALTER TABLE ONLY site_menu
    ADD CONSTRAINT site_menu_pkey PRIMARY KEY (mid);

ALTER TABLE ONLY site_text
    ADD CONSTRAINT site_text_pkey PRIMARY KEY (lang, label, tname);

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2007-07-18 12:13:26 CEST

--
-- PostgreSQL database dump complete
--

