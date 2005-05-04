--
-- PostgreSQL database dump
--

--
-- Tabellenstruktur fuer auth_level
--

CREATE TABLE auth_level (
    lid serial NOT NULL,
    "level" character varying(10) DEFAULT ''::character varying NOT NULL,
    beschreibung text NOT NULL
);


--
-- Tabellenstruktur fuer auth_right
--

CREATE TABLE auth_right (
    rid serial NOT NULL,
    uid integer DEFAULT 0 NOT NULL,
    lid integer DEFAULT 0 NOT NULL
);


--
-- Tabellenstruktur fuer auth_user
--

CREATE TABLE auth_user (
    uid serial NOT NULL,
    nachname character varying(40) DEFAULT ''::character varying NOT NULL,
    vorname character varying(40) DEFAULT ''::character varying NOT NULL,
    email character varying(60) DEFAULT ''::character varying NOT NULL,
    username character varying(20) DEFAULT ''::character varying NOT NULL,
    pass character varying(20) DEFAULT ''::character varying NOT NULL
);


--
-- Tabellenstruktur site_file
--

CREATE TABLE site_file (
    fid serial NOT NULL,
    frefid integer DEFAULT 0 NOT NULL,
    fuid integer DEFAULT 0 NOT NULL,
    fdid integer DEFAULT 0 NOT NULL,
    ftname character varying(255) DEFAULT ''::character varying NOT NULL,
    ffname character varying(255) DEFAULT ''::character varying NOT NULL,
    ffart character varying(10) DEFAULT 'jpg'::character varying NOT NULL,
    fdesc character varying(255) DEFAULT ''::character varying NOT NULL,
    funder character varying(255),
    fhit character varying(255),
    fdel text
);


--
-- Tabellenstruktur fuer site_form
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
    fcheck character varying(20) DEFAULT ''::character varying NOT NULL
);


--
-- Tabellenstruktur fuer site_form_lang
--

CREATE TABLE site_form_lang (
    flid serial NOT NULL,
    fid integer DEFAULT 0 NOT NULL,
    flang character(3) DEFAULT 'ger'::bpchar NOT NULL,
    fpgenum text,
    fwerte character varying(255) DEFAULT ''::character varying NOT NULL,
    ferror character varying(255) DEFAULT ''::character varying NOT NULL,
    fdberror character varying(255) DEFAULT ''::character varying NOT NULL
);


--
-- Tabellenstruktur fuer site_menu
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
-- Tabellenstruktur fuer site_menu_lang
--

CREATE TABLE site_menu_lang (
    mlid serial NOT NULL,
    mid integer DEFAULT 0 NOT NULL,
    lang character(3) DEFAULT 'ger'::bpchar NOT NULL,
    label character varying(30) DEFAULT ''::character varying NOT NULL,
    exturl character varying(128)
);


--
-- Tabellenstruktur fuer site_text
--

CREATE TABLE site_text (
    lang character varying(4) DEFAULT ''::character varying NOT NULL,
    label character varying(20) DEFAULT ''::character varying NOT NULL,
    crc32 character varying(10) DEFAULT '0'::character varying NOT NULL,
    tname character varying(40) DEFAULT ''::character varying NOT NULL,
    ebene text NOT NULL,
    kategorie text NOT NULL,
    html character varying(10) DEFAULT '0'::character varying NOT NULL,
    content text NOT NULL
);


--
-- Daten fuer auth_level
--

INSERT INTO auth_level VALUES (1, 'cms_edit', 'berechtigt zum bearbeiten der templates');


--
-- Daten fuer auth_right
--

INSERT INTO auth_right VALUES (1, 1, 1);


--
-- Daten fuer auth_user
--

INSERT INTO auth_user VALUES (1, '', '', '', 'ewebuki', 'JqXRXh15OlT8.');


--
-- Daten fuer site_menu
--

INSERT INTO site_menu VALUES (1, 0, 'demo', NULL, 10, NULL, NULL, NULL, 'default1', NULL, NULL);
INSERT INTO site_menu VALUES (2, 1, 'test1', NULL, 10, NULL, NULL, NULL, 'default1', NULL, NULL);
INSERT INTO site_menu VALUES (3, 1, 'test2', NULL, 20, NULL, NULL, NULL, 'default1', NULL, NULL);
INSERT INTO site_menu VALUES (4, 0, 'impressum', NULL, 20, NULL, NULL, NULL, 'default1', NULL, NULL);


--
-- Daten fuer site_menu_lang
--

INSERT INTO site_menu_lang VALUES (1, 1, 'ger', 'Demo', NULL);
INSERT INTO site_menu_lang VALUES (2, 2, 'ger', 'Test 1', NULL);
INSERT INTO site_menu_lang VALUES (3, 3, 'ger', 'Test 2', NULL);
INSERT INTO site_menu_lang VALUES (4, 4, 'ger', 'Impressum', NULL);


--
-- Daten fuer site_text
--

INSERT INTO site_text VALUES ('de', 'abort', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Abbrechen');
INSERT INTO site_text VALUES ('de', 'content', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Inhalt');
INSERT INTO site_text VALUES ('de', 'entry', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Eintrag');
INSERT INTO site_text VALUES ('de', 'error_menu', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Fehler beim löschen des Menüeintrag');
INSERT INTO site_text VALUES ('de', 'error_menu_lang', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Fehler beim löschen der Sprache(n)');
INSERT INTO site_text VALUES ('de', 'error_text', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Fehler beim löschen des/r Text/e');
INSERT INTO site_text VALUES ('de', 'inhalt', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Den Menüpunkt "!#ausgaben_entry" wirklich löschen?');
INSERT INTO site_text VALUES ('de', 'languages', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Sprachen');
INSERT INTO site_text VALUES ('de', 'no_content', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Kein Inhalt');
INSERT INTO site_text VALUES ('de', 'send', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Abschicken');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', '-555504947.delete', '/admin/menued', 'delete', '0', 'Menü-Editor - Menüpunkt löschen');
INSERT INTO site_text VALUES ('de', 'abort', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Abbrechen');
INSERT INTO site_text VALUES ('de', 'add', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Neue Sprache hinzufügen');
INSERT INTO site_text VALUES ('de', 'basic', '-1', '-555504947.edit-multi', '/admin/menued', 'add', '0', 'Allgemein');
INSERT INTO site_text VALUES ('de', 'delete', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Diese Sprache löschen');
INSERT INTO site_text VALUES ('de', 'entry', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Eintrag');
INSERT INTO site_text VALUES ('de', 'error_lang_add', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Diese Sprache ist bereits vorhanden.');
INSERT INTO site_text VALUES ('de', 'error_lang_delete', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Die Entwickler Sprache kann nicht gelöscht werden.');
INSERT INTO site_text VALUES ('de', 'error_result', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'DB Fehler: ');
INSERT INTO site_text VALUES ('de', 'extended', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Speziell');
INSERT INTO site_text VALUES ('de', 'exturl', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'externe Url');
INSERT INTO site_text VALUES ('de', 'hide', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Deaktiviert');
INSERT INTO site_text VALUES ('de', 'label', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Bezeichnung');
INSERT INTO site_text VALUES ('de', 'lang', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Sprache');
INSERT INTO site_text VALUES ('de', 'language', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Sprachen Verwaltung');
INSERT INTO site_text VALUES ('de', 'level', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'benötigter Level');
INSERT INTO site_text VALUES ('de', 'madatory', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Erzwungen');
INSERT INTO site_text VALUES ('de', 'new_lang', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Neue Sprache');
INSERT INTO site_text VALUES ('de', 'refid', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Ref. ID');
INSERT INTO site_text VALUES ('de', 'reset', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Zurücksetzen');
INSERT INTO site_text VALUES ('de', 'send', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Abschicken');
INSERT INTO site_text VALUES ('de', 'sort', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Sortierung');
INSERT INTO site_text VALUES ('de', 'template', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Template');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Menü-Editor - Menüpunkt');
INSERT INTO site_text VALUES ('de', 'abort', '-1', '-555504947.edit-single', '/admin/menued', 'edit', '0', 'Abbrechen');
INSERT INTO site_text VALUES ('de', 'add', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Neue Sprache hinzufügen');
INSERT INTO site_text VALUES ('de', 'basic', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Allgemein');
INSERT INTO site_text VALUES ('de', 'entry', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Eintrag');
INSERT INTO site_text VALUES ('de', 'error_lang_add', '-1', '-555504947.edit-single', '/admin/menued', 'edit', '0', 'Diese Sprache ist bereits vorhanden.');
INSERT INTO site_text VALUES ('de', 'error_lang_delete', '-1', '-555504947.edit-single', '/admin/menued', 'edit', '0', 'Die Entwickler Sprache kann nicht gelöscht werden.');
INSERT INTO site_text VALUES ('de', 'error_result', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'DB Fehler: ');
INSERT INTO site_text VALUES ('de', 'extended', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Speziell');
INSERT INTO site_text VALUES ('de', 'exturl', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'ext. Url');
INSERT INTO site_text VALUES ('de', 'hide', '-1', '-555504947.edit-single', '/admin/menued', 'edit', '0', 'Versteckt');
INSERT INTO site_text VALUES ('de', 'label', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Bezeichnung');
INSERT INTO site_text VALUES ('de', 'lang', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Sprache');
INSERT INTO site_text VALUES ('de', 'level', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'benötigter Level');
INSERT INTO site_text VALUES ('de', 'madatory', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Erzwungen');
INSERT INTO site_text VALUES ('de', 'new_lang', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Neue Sprache');
INSERT INTO site_text VALUES ('de', 'refid', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Ref ID.');
INSERT INTO site_text VALUES ('de', 'reset', '-1', '-555504947.edit-single', '/admin/menued', 'edit', '0', 'Zurücksetzen');
INSERT INTO site_text VALUES ('de', 'send', '-1', '-555504947.edit-single', '/admin/menued', 'edit', '0', 'Abschicken');
INSERT INTO site_text VALUES ('de', 'sort', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Sortierung');
INSERT INTO site_text VALUES ('de', 'template', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Template');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Menü-Editor - Menüpunkt');
INSERT INTO site_text VALUES ('de', 'button_desc_add', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Unterpunkt hinzufügen');
INSERT INTO site_text VALUES ('de', 'button_desc_delete', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Löschen');
INSERT INTO site_text VALUES ('de', 'button_desc_down', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Sortierung - Nach unten');
INSERT INTO site_text VALUES ('de', 'button_desc_edit', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Bearbeiten');
INSERT INTO site_text VALUES ('de', 'button_desc_move', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Im Menü Baum verschieben');
INSERT INTO site_text VALUES ('de', 'button_desc_up', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Sortierung - Nach oben');
INSERT INTO site_text VALUES ('de', 'disabled', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Abgeschaltet');
INSERT INTO site_text VALUES ('de', 'enabled', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Eingeschaltet');
INSERT INTO site_text VALUES ('de', 'error1', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Menüpunkte mit Unterpunkten lassen sich nicht löschen.');
INSERT INTO site_text VALUES ('de', 'extern', '-1', '-555504947.list', '/admin/menued', 'list', '0', '(extern)');
INSERT INTO site_text VALUES ('de', 'inhalt', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Soll hier ein beschreibender Text rein?');
INSERT INTO site_text VALUES ('de', 'new', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Neuer Ast');
INSERT INTO site_text VALUES ('de', 'renumber', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Neu durchnummerieren');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', '-555504947.list', '/admin/menued', 'list', '0', 'Menu-Editor - Übersicht');
INSERT INTO site_text VALUES ('de', 'abort', '-1', '-555504947.move', '/admin/menued', 'move', '0', 'Abbrechen');
INSERT INTO site_text VALUES ('de', 'entry', '-1', '-555504947.move', '/admin/menued', 'move', '0', 'Eintrag');
INSERT INTO site_text VALUES ('de', 'extern', '-1', '-555504947.move', '/admin/menued', 'move', '0', '(extern)');
INSERT INTO site_text VALUES ('de', 'reset', '-1', '-555504947.move', '/admin/menued', 'move', '0', 'Zurücksetzen');
INSERT INTO site_text VALUES ('de', 'root', '-1', '-555504947.move', '/admin/menued', 'move', '0', 'Ins Hauptmenü');
INSERT INTO site_text VALUES ('de', 'send', '-1', '-555504947.move', '/admin/menued', 'move', '0', 'Abschicken');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', '-555504947.move', '/admin/menued', 'move', '0', 'Menü-Editor - Menüpunkt verschieben');
INSERT INTO site_text VALUES ('de', 'send', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Abschicken');
INSERT INTO site_text VALUES ('de', 'chkpass', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Wiederholung');
INSERT INTO site_text VALUES ('de', 'inhalt', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Passwort ändern');
INSERT INTO site_text VALUES ('de', 'newpass', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Neues');
INSERT INTO site_text VALUES ('de', 'oldpass', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Altes');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Passwort Editor');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', 'auth', '', 'index', '0', 'Überschrift');
INSERT INTO site_text VALUES ('de', 'desc', '-1', 'auth.logout', '', 'index', '0', 'Werkzeuge');
INSERT INTO site_text VALUES ('de', 'fileed', '-1', 'auth.logout', '', 'index', '0', 'Datei-Editor');
INSERT INTO site_text VALUES ('de', 'leveled', '-1', 'auth.logout', '', 'index', '0', 'Level-Editor');
INSERT INTO site_text VALUES ('de', 'menued', '-1', 'auth.logout', '', 'index', '0', 'Menü-Editor');
INSERT INTO site_text VALUES ('de', 'nachher', '-1', 'auth.logout', '', 'index', '0', 'ist angemeldet.');
INSERT INTO site_text VALUES ('de', 'passed', '-1', 'auth.logout', '', 'index', '0', 'Passwort-Editor');
INSERT INTO site_text VALUES ('de', 'usered', '-1', 'auth.logout', '', 'index', '0', 'User-Editor');
INSERT INTO site_text VALUES ('de', 'vorher', '-1', 'auth.logout', '', 'index', '0', 'Benutzer');
INSERT INTO site_text VALUES ('de', 'abort', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Abbrechen');
INSERT INTO site_text VALUES ('de', 'add', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Datei einfügen');
INSERT INTO site_text VALUES ('de', 'b', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Fett');
INSERT INTO site_text VALUES ('de', 'big', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Grösser als der Rest');
INSERT INTO site_text VALUES ('de', 'br', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Erzwungener Umbruch');
INSERT INTO site_text VALUES ('de', 'cent', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Zentriert');
INSERT INTO site_text VALUES ('de', 'center', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Zentriert');
INSERT INTO site_text VALUES ('de', 'cite', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Logisch: cite');
INSERT INTO site_text VALUES ('de', 'col', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Tabellenspalte');
INSERT INTO site_text VALUES ('de', 'db', '-1', 'cms.edit.cmstag', '', 'index', '0', 'DB');
INSERT INTO site_text VALUES ('de', 'div', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Bereich');
INSERT INTO site_text VALUES ('de', 'e', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Mail');
INSERT INTO site_text VALUES ('de', 'em', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Logisch: emphatisch');
INSERT INTO site_text VALUES ('de', 'email', '-1', 'cms.edit.cmstag', '', 'index', '0', 'eMail Link');
INSERT INTO site_text VALUES ('de', 'file', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Datei');
INSERT INTO site_text VALUES ('de', 'files', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Dateien');
INSERT INTO site_text VALUES ('de', 'h1', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Überschrift Klasse 1');
INSERT INTO site_text VALUES ('de', 'h2', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Überschrift Klasse 2');
INSERT INTO site_text VALUES ('de', 'hl', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Spezielle Trennlinie');
INSERT INTO site_text VALUES ('de', 'hr', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Trennlinie');
INSERT INTO site_text VALUES ('de', 'i', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Kursiv');
INSERT INTO site_text VALUES ('de', 'img', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Bild');
INSERT INTO site_text VALUES ('de', 'imgb', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Bild mit Rahmen');
INSERT INTO site_text VALUES ('de', 'in', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Initial');
INSERT INTO site_text VALUES ('de', 'label', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Marke');
INSERT INTO site_text VALUES ('de', 'language', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Sprache');
INSERT INTO site_text VALUES ('de', 'link', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Link');
INSERT INTO site_text VALUES ('de', 'list', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Liste');
INSERT INTO site_text VALUES ('de', 'm1', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Menü dieser Ebene');
INSERT INTO site_text VALUES ('de', 'm2', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Menü der Unterebene');
INSERT INTO site_text VALUES ('de', 'pre', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Vorformatiert');
INSERT INTO site_text VALUES ('de', 'quote', '-1', 'cms.edit.cmstag', '', 'index', '0', 'In Anführungszeichen');
INSERT INTO site_text VALUES ('de', 'row', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Tabellenzeile');
INSERT INTO site_text VALUES ('de', 's', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Durchgestrichen');
INSERT INTO site_text VALUES ('de', 'save', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Speichern');
INSERT INTO site_text VALUES ('de', 'small', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Kleiner als der Rest');
INSERT INTO site_text VALUES ('de', 'sp', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Geschütztes Leerzeichen');
INSERT INTO site_text VALUES ('de', 'strong', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Logisch: strong');
INSERT INTO site_text VALUES ('de', 'sub', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Tiefgestellt');
INSERT INTO site_text VALUES ('de', 'sup', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Hochgestellt');
INSERT INTO site_text VALUES ('de', 'tab', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Tabelle');
INSERT INTO site_text VALUES ('de', 'tagselect', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Tag auswählen');
INSERT INTO site_text VALUES ('de', 'template', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Template');
INSERT INTO site_text VALUES ('de', 'tt', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Dickengleich');
INSERT INTO site_text VALUES ('de', 'u', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Unterstrichen');
INSERT INTO site_text VALUES ('de', 'up', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Zurück-Link');
INSERT INTO site_text VALUES ('de', 'upload', '-1', 'cms.edit.cmstag', '', 'index', '0', 'Hinaufladen');
INSERT INTO site_text VALUES ('de', 'inhalt', '-1', '404', '', 'indi', '0', 'Die Uri !#ausgaben_404seite wurde nicht gefunden.\r\n\r\nLeider konnte das System nicht feststellen woher sie gekommen sind.');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', '404', '', 'indi', '0', 'Fehler 404 - Nicht gefunden.');
INSERT INTO site_text VALUES ('de', 'error_dupe', '-1', '-555504947.edit-single', '/admin/menued', 'add', '0', 'Der Eintrag ist bereits vorhanden.');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', '404referer', '', 'test3', '0', 'Fehler 404 - Nicht gefunden.');
INSERT INTO site_text VALUES ('de', 'inhalt', '-1', '404referer', '', 'test3', '0', 'Die Uri: !#ausgaben_404seite wurde nicht gefunden.\r\n\r\nDie [LINK=!#ausgaben_404referer]Seite[/LINK] enthaelt einen falschen/alten Link.');
INSERT INTO site_text VALUES ('de', 'error_dupe', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'Der Eintrag ist bereits vorhanden.');
INSERT INTO site_text VALUES ('de', 'error_dupe', '-1', '-555504947.move', '/admin/menued', 'move', '0', 'In dieser Ebene existiert bereits ein Eintrag mit gleichem Namen.');
INSERT INTO site_text VALUES ('de', 'logout', '-1', 'auth.login', '', 'auth.login', '0', 'Abgemeldet');
INSERT INTO site_text VALUES ('de', 'denied', '-1', 'auth.login', '', 'auth.login', '0', 'Zugriff verweigert!');
INSERT INTO site_text VALUES ('de', 'picture', '-1', '-555504947.edit-multi', '/admin/menued', 'edit', '0', 'evt. Bild');
INSERT INTO site_text VALUES ('de', 'picture', '-1', '-555504947.edit-single', '/admin/menued', 'edit', '0', 'evt. Bild');
INSERT INTO site_text VALUES ('de', 'reset', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Zurücksetzen');
INSERT INTO site_text VALUES ('de', 'abort', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Abbrechen');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', '-840786483.list', '/admin/menued', 'list', '0', 'Level-Editor - Übersicht');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', '-840786483.modify', '/admin/menued', 'edit', '0', 'Level-Editor - Bearbeiten');
INSERT INTO site_text VALUES ('de', 'level', '-1', '-840786483.modify', '/admin/leveled', 'modify', '0', 'Bezeichnung');
INSERT INTO site_text VALUES ('de', 'description', '-1', '-840786483.modify', '/admin/leveled', 'modify', '0', 'Beschreibung');
INSERT INTO site_text VALUES ('de', 'del', '-1', '-840786483.modify', '/admin/leveled', 'modify', '0', 'entfernen');
INSERT INTO site_text VALUES ('de', 'add', '-1', '-840786483.modify', '/admin/leveled', 'modify', '0', 'hinzufügen');
INSERT INTO site_text VALUES ('de', 'send', '-1', '-840786483.modify', '/admin/leveled', 'modify', '0', 'Abschicken');
INSERT INTO site_text VALUES ('de', 'reset', '-1', '-840786483.modify', '/admin/leveled', 'modify', '0', 'Zurücksetzen');
INSERT INTO site_text VALUES ('de', 'abort', '-1', '-840786483.modify', '/admin/leveled', 'modify', '0', 'Abbrechen');
INSERT INTO site_text VALUES ('de', 'send', '-1', '-840786483.delete', '/admin/leveled', 'modify', '0', 'Löschen');
INSERT INTO site_text VALUES ('de', 'abort', '-1', '-840786483.delete', '/admin/leveled', 'modify', '0', 'Abbrechen');
INSERT INTO site_text VALUES ('de', 'frage', '-1', '-840786483.delete', '/admin/leveled', 'modify', '0', 'Wollen Sie den Level "!#ausgaben_level" wirklich löschen?');
INSERT INTO site_text VALUES ('de', 'level', '-1', '-840786483.details', '/admin/leveled', 'details', '0', 'Bezeichnung');
INSERT INTO site_text VALUES ('de', 'user', '-1', '-840786483.details', '/admin/leveled', 'details', '0', 'Mitglieder');
INSERT INTO site_text VALUES ('de', 'beschreibung', '-1', '-840786483.details', '/admin/leveled', 'details', '0', 'Beschreibung');
INSERT INTO site_text VALUES ('de', 'edit', '-1', '-840786483.details', '/admin/leveled', 'details', '0', 'Bearbeiten');
INSERT INTO site_text VALUES ('de', 'list', '-1', '-840786483.details', '/admin/leveled', 'details', '0', 'Übersicht');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', '-840786483.details', '/admin/leveled', 'details', '0', 'Level Editor - Eigenschaften');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', '-840786483.delete', '/admin/leveled', 'modify', '0', 'Level-Editor - Löschen');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', '210295197.list', '/admin/usered', 'list', '0', 'User-Editor - Übersicht');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', '-939795212.list', '/admin', 'usered', '0', 'Datei-Editor - Übersicht');
INSERT INTO site_text VALUES ('de', 'search', '-1', '-939795212.list', '/admin', 'usered', '0', 'Suche');
INSERT INTO site_text VALUES ('de', 'gesamt', '-1', '-939795212.list', '/admin', 'usered', '0', 'Gesamt:');
INSERT INTO site_text VALUES ('de', 'send', '-1', '-939795212.list', '/admin', 'usered', '0', 'Go');
INSERT INTO site_text VALUES ('de', 'describe', '-1', '-939795212.list', '/admin', 'usered', '0', 'Bearbeiten');
INSERT INTO site_text VALUES ('de', 'delete1', '-1', '-939795212.list', '/admin', 'usered', '0', 'Löschen');
INSERT INTO site_text VALUES ('de', 'ffname', '-1', '-939795212.describe', '/admin', 'usered', '0', 'Dateiname');
INSERT INTO site_text VALUES ('de', 'fdesc', '-1', '-939795212.describe', '/admin', 'usered', '0', 'Bildbeschreibung');
INSERT INTO site_text VALUES ('de', 'funder', '-1', '-939795212.describe', '/admin', 'usered', '0', 'Bildunterschrift');
INSERT INTO site_text VALUES ('de', 'fhit', '-1', '-939795212.describe', '/admin', 'usered', '0', 'Schlagworte');
INSERT INTO site_text VALUES ('de', 'upa', '-1', '-939795212.describe', '/admin', 'usered', '0', 'Die aktuelle Datei durch');
INSERT INTO site_text VALUES ('de', 'upb', '-1', '-939795212.describe', '/admin', 'usered', '0', 'ersetzen.');
INSERT INTO site_text VALUES ('de', 'send', '-1', '-939795212.describe', '/admin', 'usered', '0', 'Abschicken');
INSERT INTO site_text VALUES ('de', 'reset', '-1', '-939795212.describe', '/admin', 'usered', '0', 'Zurücksetzen');
INSERT INTO site_text VALUES ('de', 'abort', '-1', '-939795212.describe', '/admin', 'usered', '0', 'Abbrechen');
INSERT INTO site_text VALUES ('de', 'inhalt', '-1', 'impressum', '', 'impressum', '0', 'eWeBuKi - Copyright 2003, 2004\r\nby [EMAIL=w.ammon@chaos.de]Werner Ammon[/EMAIL]');
INSERT INTO site_text VALUES ('de', 'send_image', '-1', '-939795212.list', '', 'impressum', '0', 'zum Content Editor');
INSERT INTO site_text VALUES ('de', 'delete2', '-1', '-939795212.list', '', 'impressum', '0', 'Alle Löschen');
INSERT INTO site_text VALUES ('de', 'level', '-1', '-840786483.list', '/admin/leveled', 'list', '0', 'Bezeichnung');
INSERT INTO site_text VALUES ('de', 'beschreibung', '-1', '-840786483.list', '/admin/leveled', 'list', '0', 'Beschreibung');
INSERT INTO site_text VALUES ('de', 'modify', '-1', '-840786483.list', '/admin/leveled', 'list', '0', 'Bearbeiten');
INSERT INTO site_text VALUES ('de', 'edit', '-1', '-840786483.list', '/admin/leveled', 'list', '0', 'Bearbeiten');
INSERT INTO site_text VALUES ('de', 'delete', '-1', '-840786483.list', '/admin/leveled', 'list', '0', 'Löschen');
INSERT INTO site_text VALUES ('de', 'details', '-1', '-840786483.list', '/admin/leveled', 'list', '0', 'Eigenschaften');
INSERT INTO site_text VALUES ('de', 'senden', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'Abschicken');
INSERT INTO site_text VALUES ('de', 'reset', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'Zurücksetzen');
INSERT INTO site_text VALUES ('de', 'abort', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'Abbrechen');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'User-Editor - Bearbeiten');
INSERT INTO site_text VALUES ('de', 'error_oldpass', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Das alte Passwort stimmt nicht!');
INSERT INTO site_text VALUES ('de', 'error_chkpass', '-1', '852881080.modify', '/admin/passed', 'modify', '0', 'Das Neue Passwort und die Wiederholung stimmen nicht überein!');
INSERT INTO site_text VALUES ('de', 'nachname', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'Nachname');
INSERT INTO site_text VALUES ('de', 'vorname', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'Vorname');
INSERT INTO site_text VALUES ('de', 'email', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'eMail');
INSERT INTO site_text VALUES ('de', 'new', '-1', '-840786483.list', '/admin/leveled', 'list', '0', 'Neuer Level');
INSERT INTO site_text VALUES ('de', 'new', '-1', '210295197.list', '/admin/usered', 'list', '0', 'Neuer User');
INSERT INTO site_text VALUES ('de', 'frage', '-1', '210295197.delete', '/admin/usered', 'modify', '0', 'Wollen Sie den User "!#ausgaben_username" wirklich löschen?');
INSERT INTO site_text VALUES ('de', 'delete', '-1', '210295197.delete', '/admin/usered', 'modify', '0', 'Delete');
INSERT INTO site_text VALUES ('de', 'abort', '-1', '210295197.delete', '/admin/usered', 'modify', '0', 'Abbrechen');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', '210295197.delete', '/admin/usered', 'modify', '0', 'User-Editor - Löschen');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', '210295197.details', '/admin/usered', 'details', '0', 'User-Editor - Eigenschaften');
INSERT INTO site_text VALUES ('de', 'username', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'Login');
INSERT INTO site_text VALUES ('de', 'newpass', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'Passwort');
INSERT INTO site_text VALUES ('de', 'chkpass', '-1', '210295197.modify', '/admin/usered', 'modify', '0', 'Wiederholung');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', 'index', '', 'index', '0', 'Menü');
INSERT INTO site_text VALUES ('de', 'copyright', '-1', 'index', '', 'impressum', '0', 'eWeBuKi - Copyright 2003, 2004');
INSERT INTO site_text VALUES ('de', 'kekse', '-1', 'index', '', 'impressum', '0', 'Kekse');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', 'show', '', 'show', '0', 'eWeBuKi Show');
INSERT INTO site_text VALUES ('de', 'inhalt', '-1', 'show', '', 'show', '0', 'Tabellen Positionen:\r\n[TAB=;300;1]\r\n[ROW]\r\n[COL]1,1\r\n\r\n\r\n[/COL]\r\n[COL=;;u]1,2\r\n[/COL]\r\n[COL=r]1,3[/COL]\r\n[/ROW][ROW]\r\n[COL=m]2,1[/COL]\r\n[COL=;;g]2,2[/COL]\r\n[COL=r;;m]2,3\r\n\r\n\r\n[/COL]\r\n[/ROW]\r\n[/TAB]\r\n\r\n\r\nEasy Template Links:\r\n!#lnk_0\r\n!#lnk_1\r\n!#lnk_2\r\n!#lnk_3\r\n\r\nMenu oberhalb (M1,mit Bez.):\r\n[M1]nach oben[/M1]\r\n\r\nMenu oberhalb als Liste (M1=l,ohne Bez.);\r\n[M1=l][/M1]\r\n\r\nMenu gleiche Ebene (M2,mit Bez.)\r\n[M2]nach oben[/M2]\r\n\r\nMenu gleiche Ebene als Liste (M2=l,mit Bez.)\r\n[M2=l][/M2]\r\n\r\nTabellen Abstände (abstand text - tabelle 1)\r\n[TAB=;300;1]\r\n[ROW]\r\n[COL=l;150]links oben\r\n[/COL]\r\n[COL=l;150]rechts oben\r\n[/COL]\r\n[/ROW]\r\n[/TAB]\r\n[TAB=;300;1]\r\n[ROW]\r\n[COL=l;150]links oben\r\n[/COL]\r\n[COL=l;150]rechts oben\r\n[/COL]\r\n[/ROW]\r\n[/TAB]\r\nTabellen Abstände (abstand text - tabelle 2)\r\n\r\n[IN]I[/IN]nitial fuer Texte\r\n\r\n[H1][B][EM]Bold EM Tag[/EM][/B] im H1 Tag[/H1]\r\n\r\n\r\n\r\n\r\nText zwischen Linien:\r\n[HL][/HL]\r\nHier kommt der Text.\r\n[HL][/HL]\r\n\r\nWeit hinten, hinter den Wortbergen, fern der Länder Vokalien und Konsonantien leben die Blindtexte. Abgeschieden wohnen Sie[IMG=/file/picture/small/img_1.png;l;;;20;;20]eWeBuKi Logo[/IMG] in Buchstabhausen an der Küste des Semantik, eines großen Sprachozeans. Ein kleines Bächlein namens Duden fließt durch ihren Ort und versorgt sie mit den nötigen Regelialien. Es ist ein paradiesmatisches Land, in dem einem gebratene Satzteile in den Mund fliegen. Nicht einmal von der allmächtigen Interpunktion werden die Blindtexte beherrscht – ein geradezu unorthographisches Leben.\r\n\r\nBei Bildern rechts gibt es Abstand Probleme:\r\n[IMGB=/file/picture/small/img_1.png;r;0;b]Logo[/IMGB]\r\n\r\n\r\n\r\n\r\n[IMGB=/file/picture/small/img_1.png;r]Logo[/IMGB]\r\n\r\n\r\n\r\n\r\nZeilenumbrüche müssen passen, sonst kleben die Bilder nebeneinander.\r\n\r\n[H1]ueberschrift h1[/H1]\r\n[H2]ueberschrift h2[/H2]\r\n[H3]ueberschrift h3[/H3]\r\n[H4]ueberschrift h4[/H4]\r\n[H5]ueberschrift h5[/H5]\r\n[H6]ueberschrift h6[/H6]\r\n\r\nAbsaetze mit css einstellen:\r\n[P]Im Absatz ist es Schoen[/P]\r\n\r\nDIV=class jeder css im Content:\r\n[DIV=anderst]Dieser Text ist schoener als der Rest[/DIV]');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', 'impressum', '', 'impressum', '0', 'Impressum');
INSERT INTO site_text VALUES ('de', 'inhalt', '-1', 'werner', '', 'werner', '0', 'Sie können sich mit\r\n\r\nname: ewebuki\r\npass: ewebuki\r\n\r\nam System anmelden.\r\n\r\n[B]ACHTUNG:[/B] Passwort ändern nicht vergessen!');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', 'demo', '', 'demo', '0', 'Demoseite');
INSERT INTO site_text VALUES ('de', 'inhalt', '-1', 'demo', '', 'demo', '0', 'Hier könnte [B]Ihr[/B] Text stehen.');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', '1924484980.test1', '/demo', 'test1', '0', 'Testseite 1');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', '1924484980.test2', '/demo', 'test2', '0', 'Testseite 2');
INSERT INTO site_text VALUES ('de', 'inhalt', '-1', '1924484980.test1', '/demo', 'test1', '0', 'Hier könnte [B]Ihr[/B] Text stehen.');
INSERT INTO site_text VALUES ('de', 'inhalt', '-1', '1924484980.test2', '/demo', 'test2', '0', 'Hier könnte [B]Ihr[/B] Text stehen.');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', 'werner', '', 'werner', '0', 'Glückwunsch Ihr eWeBuKi läuft.');
INSERT INTO site_text VALUES ('de', 'ueberschrift', '-1', 'main', '', 'index', '0', 'Glückwunsch Ihr eWeBuKi läuft!');
INSERT INTO site_text VALUES ('de', 'inhalt', '-1', 'main', '', 'index', '0', 'Um sich am System anzumelden benutzen Sie bitte folgende Daten:\r\n\r\nuser: ewebuki\r\npass: ewebuki\r\n\r\n[B]ACHTUNG:[/B] Passwort ändern nicht vergessen!');


--
-- TOC entry 22 (OID 180673)
-- Name: auth_level_pkey; Type: CONSTRAINT; Schema: public; Owner: mor
--

ALTER TABLE ONLY auth_level
    ADD CONSTRAINT auth_level_pkey PRIMARY KEY (lid);


--
-- TOC entry 23 (OID 180683)
-- Name: auth_right_pkey; Type: CONSTRAINT; Schema: public; Owner: mor
--

ALTER TABLE ONLY auth_right
    ADD CONSTRAINT auth_right_pkey PRIMARY KEY (rid);


--
-- TOC entry 24 (OID 180696)
-- Name: auth_user_pkey; Type: CONSTRAINT; Schema: public; Owner: mor
--

ALTER TABLE ONLY auth_user
    ADD CONSTRAINT auth_user_pkey PRIMARY KEY (uid);


--
-- TOC entry 25 (OID 180714)
-- Name: site_file_pkey; Type: CONSTRAINT; Schema: public; Owner: mor
--

ALTER TABLE ONLY site_file
    ADD CONSTRAINT site_file_pkey PRIMARY KEY (fid);


--
-- TOC entry 26 (OID 180728)
-- Name: site_form_pkey; Type: CONSTRAINT; Schema: public; Owner: mor
--

ALTER TABLE ONLY site_form
    ADD CONSTRAINT site_form_pkey PRIMARY KEY (fid);


--
-- TOC entry 27 (OID 180743)
-- Name: site_form_lang_pkey; Type: CONSTRAINT; Schema: public; Owner: mor
--

ALTER TABLE ONLY site_form_lang
    ADD CONSTRAINT site_form_lang_pkey PRIMARY KEY (flid);


--
-- TOC entry 28 (OID 180754)
-- Name: site_menu_pkey; Type: CONSTRAINT; Schema: public; Owner: mor
--

ALTER TABLE ONLY site_menu
    ADD CONSTRAINT site_menu_pkey PRIMARY KEY (mid);


--
-- TOC entry 29 (OID 180768)
-- Name: site_menu_lang_pkey; Type: CONSTRAINT; Schema: public; Owner: mor
--

ALTER TABLE ONLY site_menu_lang
    ADD CONSTRAINT site_menu_lang_pkey PRIMARY KEY (mlid);


--
-- TOC entry 30 (OID 181955)
-- Name: site_text_pkey; Type: CONSTRAINT; Schema: public; Owner: mor
--

ALTER TABLE ONLY site_text
    ADD CONSTRAINT site_text_pkey PRIMARY KEY (lang, label, tname);


--
-- TOC entry 14 (OID 180664)
-- Name: auth_level_lid_seq; Type: SEQUENCE SET; Schema: public; Owner: mor
--

SELECT pg_catalog.setval('auth_level_lid_seq', 1, false);


--
-- TOC entry 15 (OID 180676)
-- Name: auth_right_rid_seq; Type: SEQUENCE SET; Schema: public; Owner: mor
--

SELECT pg_catalog.setval('auth_right_rid_seq', 1, false);


--
-- TOC entry 16 (OID 180686)
-- Name: auth_user_uid_seq; Type: SEQUENCE SET; Schema: public; Owner: mor
--

SELECT pg_catalog.setval('auth_user_uid_seq', 1, false);


--
-- TOC entry 17 (OID 180699)
-- Name: site_file_fid_seq; Type: SEQUENCE SET; Schema: public; Owner: mor
--

SELECT pg_catalog.setval('site_file_fid_seq', 1, false);


--
-- TOC entry 18 (OID 180716)
-- Name: site_form_fid_seq; Type: SEQUENCE SET; Schema: public; Owner: mor
--

SELECT pg_catalog.setval('site_form_fid_seq', 1, false);


--
-- TOC entry 19 (OID 180730)
-- Name: site_form_lang_flid_seq; Type: SEQUENCE SET; Schema: public; Owner: mor
--

SELECT pg_catalog.setval('site_form_lang_flid_seq', 1, false);


--
-- TOC entry 20 (OID 180745)
-- Name: site_menu_mid_seq; Type: SEQUENCE SET; Schema: public; Owner: mor
--

SELECT pg_catalog.setval('site_menu_mid_seq', 1, false);


--
-- TOC entry 21 (OID 180760)
-- Name: site_menu_lang_mlid_seq; Type: SEQUENCE SET; Schema: public; Owner: mor
--

SELECT pg_catalog.setval('site_menu_lang_mlid_seq', 1, false);
