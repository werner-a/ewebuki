--
-- PostgreSQL database dump
--

-- Started on 2010-01-29 13:23:00 CET

SET statement_timeout = 0;
SET client_encoding = 'LATIN9';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 1305 (class 1259 OID 3068279)
-- Dependencies: 1665 1666 1667 1668 1669 1670 1671 4
-- Name: auth_content; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE auth_content (
    uid integer DEFAULT 0 NOT NULL,
    gid integer DEFAULT 0 NOT NULL,
    pid integer DEFAULT 0 NOT NULL,
    neg integer DEFAULT 0,
    db character varying(20) DEFAULT ''::character varying NOT NULL,
    tname character varying(50) DEFAULT ''::character varying NOT NULL,
    ebene text NOT NULL,
    kategorie text NOT NULL,
    CONSTRAINT auth_content_neg_check CHECK (((neg = (-1)) OR (neg = 0)))
);

--
-- data for table `auth_content`
--

INSERT INTO auth_content (uid, gid, pid, neg, db, tname, ebene, kategorie) VALUES(0, 1, 1, 0, '', '/', '', '');
INSERT INTO auth_content (uid, gid, pid, neg, db, tname, ebene, kategorie) VALUES(0, 1, 2, 0, '', '/', '', '');
INSERT INTO auth_content (uid, gid, pid, neg, db, tname, ebene, kategorie) VALUES(0, 1, 3, 0, '', '/', '', '');
INSERT INTO auth_content (uid, gid, pid, neg, db, tname, ebene, kategorie) VALUES(0, 1, 4, 0, '', '/', '', '');
INSERT INTO auth_content (uid, gid, pid, neg, db, tname, ebene, kategorie) VALUES(0, 1, 5, 0, '', '/', '', '');

--
-- TOC entry 1321 (class 1259 OID 3068486)
-- Dependencies: 4
-- Name: auth_group_gid_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE auth_group_gid_seq
    START WITH 2
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1306 (class 1259 OID 3068292)
-- Dependencies: 1672 1673 4
-- Name: auth_group; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE auth_group (
    gid integer DEFAULT nextval('auth_group_gid_seq'::regclass) NOT NULL,
    ggroup character varying(30) DEFAULT ''::character varying NOT NULL,
    beschreibung text NOT NULL
);

--
-- data for table `auth_group`
--

INSERT INTO auth_group (gid, ggroup, beschreibung) VALUES(1, 'manager', 'manager');

--
-- TOC entry 1322 (class 1259 OID 3068488)
-- Dependencies: 4
-- Name: auth_level_lid_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE auth_level_lid_seq
    START WITH 3
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1307 (class 1259 OID 3068300)
-- Dependencies: 1674 1675 4
-- Name: auth_level; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE auth_level (
    lid integer DEFAULT nextval('auth_level_lid_seq'::regclass) NOT NULL,
    level character varying(10) DEFAULT ''::character varying NOT NULL,
    beschreibung text NOT NULL
);

--
-- data for table `auth_level`
--

INSERT INTO auth_level (lid, level, beschreibung) VALUES(1, 'cms_edit', 'berechtigt zum bearbeiten der templates');
INSERT INTO auth_level (lid, level, beschreibung) VALUES(2, 'cms_admin', 'berechtigt zur administration');

--
-- TOC entry 1308 (class 1259 OID 3068308)
-- Dependencies: 1676 1677 4
-- Name: auth_member; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE auth_member (
    uid integer DEFAULT 0 NOT NULL,
    gid integer DEFAULT 0 NOT NULL
);

--
-- data for table `auth_memeber`
--

INSERT INTO auth_member (uid, gid) VALUES(1, 1);

--
-- TOC entry 1331 (class 1259 OID 3068508)
-- Dependencies: 4
-- Name: auth_priv_pid_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE auth_priv_pid_seq
    START WITH 6
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1309 (class 1259 OID 3068314)
-- Dependencies: 1678 1679 4
-- Name: auth_priv; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE auth_priv (
    pid integer DEFAULT nextval('auth_priv_pid_seq'::regclass) NOT NULL,
    priv character varying(20) DEFAULT ''::character varying NOT NULL
);

--
-- data for table `auth_priv`
--

INSERT INTO auth_priv (pid, priv) VALUES(1, 'view');
INSERT INTO auth_priv (pid, priv) VALUES(2, 'edit');
INSERT INTO auth_priv (pid, priv) VALUES(3, 'publish');
INSERT INTO auth_priv (pid, priv) VALUES(4, 'admin');
INSERT INTO auth_priv (pid, priv) VALUES(5, 'add');

--
-- TOC entry 1310 (class 1259 OID 3068321)
-- Dependencies: 1680 1681 4
-- Name: auth_right; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE auth_right (
    uid integer DEFAULT 0 NOT NULL,
    lid integer DEFAULT 0 NOT NULL
);

--
-- data for table `auth_right`
--

INSERT INTO auth_right (uid, lid) VALUES(1, 1);
INSERT INTO auth_right (uid, lid) VALUES(1, 2);

--
-- TOC entry 1323 (class 1259 OID 3068490)
-- Dependencies: 4
-- Name: auth_special_sid_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE auth_special_sid_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1311 (class 1259 OID 3068327)
-- Dependencies: 1682 1683 1684 1685 1686 4
-- Name: auth_special; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE auth_special (
    sid integer DEFAULT nextval('auth_special_sid_seq'::regclass) NOT NULL,
    suid integer DEFAULT 0 NOT NULL,
    content integer DEFAULT 0 NOT NULL,
    sdb character varying(20) DEFAULT ''::character varying NOT NULL,
    stname character varying(50) DEFAULT ''::character varying NOT NULL,
    sebene text,
    skategorie text,
    sbeschreibung text
);


--
-- TOC entry 1324 (class 1259 OID 3068492)
-- Dependencies: 4
-- Name: auth_user_uid_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE auth_user_uid_seq
    START WITH 2
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1312 (class 1259 OID 3068338)
-- Dependencies: 1687 1688 1689 1690 1691 1692 4
-- Name: auth_user; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE auth_user (
    uid integer DEFAULT nextval('auth_user_uid_seq'::regclass) NOT NULL,
    nachname character varying(40) DEFAULT ''::character varying NOT NULL,
    vorname character varying(40) DEFAULT ''::character varying NOT NULL,
    email character varying(60) DEFAULT ''::character varying NOT NULL,
    username character varying(20) DEFAULT ''::character varying NOT NULL,
    pass character varying(20) DEFAULT ''::character varying NOT NULL
);

--
-- data for table `auth_user`
--

INSERT INTO auth_user (uid, nachname, vorname, email, username, pass) VALUES(1, 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki', 'WFffxluy26Lew');

--
-- TOC entry 1325 (class 1259 OID 3068494)
-- Dependencies: 4
-- Name: db_leer_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE db_leer_id_seq
    START WITH 3
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1313 (class 1259 OID 3068349)
-- Dependencies: 1693 1694 4
-- Name: db_leer; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE db_leer (
    id integer DEFAULT nextval('db_leer_id_seq'::regclass) NOT NULL,
    field1 character varying(255) DEFAULT ''::character varying NOT NULL,
    field2 text NOT NULL
);

--
-- data for table `db_leer`
--

INSERT INTO db_leer (id, field1, field2) VALUES(1, 'Erster Eintrag', 'Zweite Spalte');
INSERT INTO db_leer (id, field1, field2) VALUES(2, 'Zweiter Eintrag', 'Zweite Spalte');

--
-- TOC entry 1326 (class 1259 OID 3068496)
-- Dependencies: 4
-- Name: site_file_fid_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE site_file_fid_seq
    START WITH 2
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1314 (class 1259 OID 3068363)
-- Dependencies: 1695 1696 1697 1698 1699 1700 1701 1702 4
-- Name: site_file; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE site_file (
    fid integer DEFAULT nextval('site_file_fid_seq'::regclass) NOT NULL,
    frefid integer DEFAULT 0 NOT NULL,
    fuid integer DEFAULT 0 NOT NULL,
    fdid character varying(2) DEFAULT 0 NOT NULL,
    ftname character varying(255) DEFAULT ''::character varying NOT NULL,
    ffname character varying(255) DEFAULT ''::character varying NOT NULL,
    ffart character varying(8) DEFAULT 'jpg'::character varying NOT NULL,
    fdesc text NOT NULL,
    funder character varying(255),
    fhit character varying(255),
    fdel text,
    fgroups character varying(150) DEFAULT ''::character varying NOT NULL,
    CONSTRAINT site_file_ffart_check CHECK ((((((((((((ffart)::text = 'gif'::text) OR ((ffart)::text = 'jpg'::text)) OR ((ffart)::text = 'png'::text)) OR ((ffart)::text = 'pdf'::text)) OR ((ffart)::text = 'zip'::text)) OR ((ffart)::text = 'odt'::text)) OR ((ffart)::text = 'ods'::text)) OR ((ffart)::text = 'odp'::text)) OR ((ffart)::text = 'gz'::text)) OR ((ffart)::text = 'bz2'::text)))
);

--
-- data for table `site_file`
--

INSERT INTO site_file (fid, frefid, fuid, fdid, ftname, ffname, ffart, fdesc, funder, fhit, fdel, fgroups) VALUES(1, 0, 1, 0, '', 'ewebuki_160x67.png', 'png', 'eWeBuKi Logo Beschreibung', 'eWeBuKi Logo Unterschift', '', NULL, '');

--
-- TOC entry 1327 (class 1259 OID 3068498)
-- Dependencies: 4
-- Name: site_form_fid_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE site_form_fid_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1315 (class 1259 OID 3068376)
-- Dependencies: 1703 1704 1705 1706 1707 1708 1709 1710 1711 4
-- Name: site_form; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE site_form (
    fid integer DEFAULT nextval('site_form_fid_seq'::regclass) NOT NULL,
    flabel character varying(20) DEFAULT ''::character varying NOT NULL,
    ftname character varying(40) DEFAULT ''::character varying NOT NULL,
    fsize character varying(7) DEFAULT 0 NOT NULL,
    fclass character varying(30) DEFAULT ''::character varying NOT NULL,
    fstyle character varying(60) DEFAULT ''::character varying NOT NULL,
    foption character varying(16),
    frequired integer DEFAULT 0 NOT NULL,
    fcheck text NOT NULL,
    CONSTRAINT site_form_foption_check CHECK (((((((foption)::text = 'file'::text) OR ((foption)::text = 'hidden'::text)) OR ((foption)::text = 'password'::text)) OR ((foption)::text = 'pgenum'::text)) OR ((foption)::text = 'readonly'::text))),
    CONSTRAINT site_form_frequired_check CHECK (((frequired = 0) OR (frequired = (-1))))
);

--
-- data for table `site_form`
--

INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES(1, 'username', '210295197.modify', '0', '', '', NULL, '-1', '');
INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES(2, 'pass', '210295197.modify', '0', '', '', 'password', '-1', '');
INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES(3, 'pass', '852881080.modify', '0', '', '', 'password', '-1', '');
INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES(4, 'fid', '-939795212.modify', '0', 'hidden', '', 'hidden', '-1', '');
INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES(6, 'fdesc', '-939795212.modify', '25', '', '', NULL, '0', '');
INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES(7, 'funder', '-939795212.modify', '30', '', '', NULL, '0', '');
INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES(8, 'fhit', '-939795212.modify', '30', '', '', NULL, '0', '');
INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES(9, 'entry', '-555504947.add', '0', '', '', NULL, '-1', 'PREG:^[a-z_\\-\\.0-9]+$');
INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES(10, 'entry', '-555504947.edit', '0', '', '', NULL, '-1', 'PREG:^[a-z_\\-\\.0-9]+$');
INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES(11, 'new_keyword', '1950102507.rename_tag', '0', '', '', NULL, '-1', '');

--
-- TOC entry 1328 (class 1259 OID 3068500)
-- Dependencies: 4
-- Name: site_form_lang_flid_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE site_form_lang_flid_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1316 (class 1259 OID 3068389)
-- Dependencies: 1712 1713 1714 1715 1716 1717 1718 4
-- Name: site_form_lang; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE site_form_lang (
    flid integer DEFAULT nextval('site_form_lang_flid_seq'::regclass) NOT NULL,
    fid integer DEFAULT 0 NOT NULL,
    flang character varying(5) DEFAULT 'de'::character varying NOT NULL,
    fpgenum text,
    fwerte character varying(255) DEFAULT ''::character varying NOT NULL,
    ferror character varying(255) DEFAULT ''::character varying NOT NULL,
    fdberror character varying(255) DEFAULT ''::character varying NOT NULL,
    fchkerror character varying(255) DEFAULT ''::character varying NOT NULL
);

--
-- data for table `site_form_lang`
--

INSERT INTO site_form_lang (flid, fid, flang, fpgenum, fwerte, ferror, fdberror, fchkerror) VALUES(1, 1, 'de', NULL, '', 'Username darf nicht leer sein.', 'Username bereits vorhanden.', '');
INSERT INTO site_form_lang (flid, fid, flang, fpgenum, fwerte, ferror, fdberror, fchkerror) VALUES(2, 2, 'de', NULL, '', 'Passworte nicht identisch oder leer.', '', '');
INSERT INTO site_form_lang (flid, fid, flang, fpgenum, fwerte, ferror, fdberror, fchkerror) VALUES(3, 3, 'de', NULL, '', 'Passworte nicht identisch oder leer.', '', '');
INSERT INTO site_form_lang (flid, fid, flang, fpgenum, fwerte, ferror, fdberror, fchkerror) VALUES(9, 9, 'de', NULL, '', '', '', 'Ungültige Zeichen im Feld Eintrag.');
INSERT INTO site_form_lang (flid, fid, flang, fpgenum, fwerte, ferror, fdberror, fchkerror) VALUES(10, 10, 'de', NULL, '', '', '', 'Ungültige Zeichen im Feld Eintrag.');

--
-- TOC entry 1332 (class 1259 OID 3068517)
-- Dependencies: 4
-- Name: site_keyword_kid_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE site_keyword_kid_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1317 (class 1259 OID 3068412)
-- Dependencies: 1719 1720 1721 1722 4
-- Name: site_keyword; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE site_keyword (
    kid bigint DEFAULT nextval('site_keyword_kid_seq'::regclass) NOT NULL,
    tname character varying(40) DEFAULT ''::character varying NOT NULL,
    ebene text NOT NULL,
    kategorie text NOT NULL,
    lang character varying(5) DEFAULT ''::character varying NOT NULL,
    word character varying(100) DEFAULT ''::character varying NOT NULL
);


--
-- TOC entry 1318 (class 1259 OID 3068434)
-- Dependencies: 1723 1724 1725 1726 1727 4
-- Name: site_lock; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE site_lock (
    lang character varying(5) DEFAULT ''::character varying NOT NULL,
    label character varying(20) DEFAULT ''::character varying NOT NULL,
    tname character varying(40) DEFAULT ''::character varying NOT NULL,
    byalias character varying(20) DEFAULT ''::character varying NOT NULL,
    lockat timestamp without time zone DEFAULT '1970-01-01 00:00:00'::timestamp without time zone NOT NULL
);


--
-- TOC entry 1330 (class 1259 OID 3068504)
-- Dependencies: 4
-- Name: site_menu_mid_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE site_menu_mid_seq
    START WITH 12
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

--
-- TOC entry 1334 (class 1259 OID 3068757)
-- Dependencies: 1745 1746 1747 1748 4
-- Name: site_menu; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE site_menu (
    mid integer NOT NULL,
    refid integer DEFAULT 0,
    entry character varying(30) DEFAULT ''::character varying NOT NULL,
    picture character varying(128),
    sort integer DEFAULT 1000 NOT NULL,
    hide character varying(2) DEFAULT 0,
    level character varying(10),
    mandatory character varying(2) DEFAULT 0,
    dynamiccss character varying(5),
    dynamicbg character varying(128),
    defaulttemplate character varying(20) DEFAULT 'default1'::character varying NOT NULL,
    CONSTRAINT site_menu_hide_check CHECK (((hide = ('-1')) OR (hide = ('0')) OR (hide = ('')) ))
);

--
-- data for table `site_menu`
--

INSERT INTO site_menu (mid, refid, entry, picture, sort, hide, level, mandatory, defaulttemplate) VALUES(1, 0, 'demo', NULL, 10, NULL, NULL, NULL, 'default1');
INSERT INTO site_menu (mid, refid, entry, picture, sort, hide, level, mandatory, defaulttemplate) VALUES(2, 0, 'show', NULL, 20, NULL, NULL, NULL, 'default1');
INSERT INTO site_menu (mid, refid, entry, picture, sort, hide, level, mandatory, defaulttemplate) VALUES(3, 0, 'bilderstrecke', NULL, 30, NULL, NULL, NULL, 'default1');
INSERT INTO site_menu (mid, refid, entry, picture, sort, hide, level, mandatory, defaulttemplate) VALUES(4, 0, 'lightbox', '', 40, NULL, NULL, NULL, 'default1');
INSERT INTO site_menu (mid, refid, entry, picture, sort, hide, level, mandatory, defaulttemplate) VALUES(5, 0, 'doku', '', 50, NULL, NULL, NULL, 'default1');
INSERT INTO site_menu (mid, refid, entry, picture, sort, hide, level, mandatory, defaulttemplate) VALUES(6, 5, 'kapitel1', '', 10, NULL, NULL, NULL, 'default1');
INSERT INTO site_menu (mid, refid, entry, picture, sort, hide, level, mandatory, defaulttemplate) VALUES(7, 6, 'punkt_1', '', 10, NULL, NULL, NULL, 'default1');
INSERT INTO site_menu (mid, refid, entry, picture, sort, hide, level, mandatory, defaulttemplate) VALUES(8, 6, 'punkt_2', '', 20, NULL, NULL, NULL, 'default1');
INSERT INTO site_menu (mid, refid, entry, picture, sort, hide, level, mandatory, defaulttemplate) VALUES(9, 5, 'kapitel_2', '', 20, NULL, NULL, NULL, 'default1');
INSERT INTO site_menu (mid, refid, entry, picture, sort, hide, level, mandatory, defaulttemplate) VALUES(10, 0, 'fehler', NULL, 60, NULL, NULL, NULL, 'default1');
INSERT INTO site_menu (mid, refid, entry, picture, sort, hide, level, mandatory, defaulttemplate) VALUES(11, 0, 'impressum', NULL, 70, NULL, NULL, NULL, 'default1');

--
-- TOC entry 1329 (class 1259 OID 3068502)
-- Dependencies: 4
-- Name: site_menu_lang_mlid_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE site_menu_lang_mlid_seq
    START WITH 12
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


--
-- TOC entry 1319 (class 1259 OID 3068455)
-- Dependencies: 1728 1729 1730 1731 4
-- Name: site_menu_lang; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE site_menu_lang (
    mlid integer DEFAULT nextval('site_menu_lang_mlid_seq'::regclass) NOT NULL,
    mid integer DEFAULT 0 NOT NULL,
    lang character varying(5) DEFAULT 'de'::character varying NOT NULL,
    label character varying(30) DEFAULT ''::character varying NOT NULL,
    exturl character varying(128)
);

--
-- data for table `site_menu_lang`
--

INSERT INTO site_menu_lang (mlid, mid, lang, label, exturl) VALUES(1, 1, 'de', 'Demo', NULL);
INSERT INTO site_menu_lang (mlid, mid, lang, label, exturl) VALUES(2, 2, 'de', 'eWeBuKi Show', NULL);
INSERT INTO site_menu_lang (mlid, mid, lang, label, exturl) VALUES(3, 3, 'de', 'Bilderstrecke', NULL);
INSERT INTO site_menu_lang (mlid, mid, lang, label, exturl) VALUES(4, 4, 'de', 'Lightbox', NULL);
INSERT INTO site_menu_lang (mlid, mid, lang, label, exturl) VALUES(5, 5, 'de', 'Doku', NULL);
INSERT INTO site_menu_lang (mlid, mid, lang, label, exturl) VALUES(6, 6, 'de', 'Kapitel 1', NULL);
INSERT INTO site_menu_lang (mlid, mid, lang, label, exturl) VALUES(7, 7, 'de', 'Punkt 1', NULL);
INSERT INTO site_menu_lang (mlid, mid, lang, label, exturl) VALUES(8, 8, 'de', 'Punkt 2', NULL);
INSERT INTO site_menu_lang (mlid, mid, lang, label, exturl) VALUES(9, 9, 'de', 'Kapitel 2', NULL);
INSERT INTO site_menu_lang (mlid, mid, lang, label, exturl) VALUES(10, 10, 'de', '404', NULL);
INSERT INTO site_menu_lang (mlid, mid, lang, label, exturl) VALUES(11, 11, 'de', 'Impressum', NULL);

--
-- TOC entry 1320 (class 1259 OID 3068462)
-- Dependencies: 1732 1733 1734 1735 1736 1737 1738 1739 1740 1741 1742 1743 4
-- Name: site_text; Type: TABLE; Schema: public; Owner: -; Tablespace: 
--

CREATE TABLE site_text (
    lang character varying(5) DEFAULT 'de'::character varying NOT NULL,
    label character varying(20) DEFAULT ''::character varying NOT NULL,
    tname character varying(40) DEFAULT ''::character varying NOT NULL,
    version integer DEFAULT 0 NOT NULL,
    ebene text NOT NULL,
    kategorie text NOT NULL,
    crc32 integer DEFAULT (-1) NOT NULL,
    html integer DEFAULT 0 NOT NULL,
    content text NOT NULL,
    changed timestamp without time zone DEFAULT '1970-01-01 00:00:00'::timestamp without time zone NOT NULL,
    bysurname character varying(40) DEFAULT ''::character varying NOT NULL,
    byforename character varying(40) DEFAULT ''::character varying NOT NULL,
    byemail character varying(60) DEFAULT ''::character varying NOT NULL,
    byalias character varying(20) DEFAULT ''::character varying NOT NULL,
    status integer DEFAULT 1 NOT NULL
);


--
-- TOC entry 1744 (class 2604 OID 3068759)
-- Dependencies: 1333 1334 1334
-- Name: mid; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE site_menu ALTER COLUMN mid SET DEFAULT nextval('site_menu_mid_seq'::regclass);


--
-- TOC entry 1786 (class 2606 OID 3068767)
-- Dependencies: 1334 1334 1334
-- Name: DUPE; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY site_menu
    ADD CONSTRAINT "DUPE" UNIQUE (refid, entry);


--
-- TOC entry 1750 (class 2606 OID 3068291)
-- Dependencies: 1305 1305 1305 1305 1305 1305
-- Name: auth_content_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY auth_content
    ADD CONSTRAINT auth_content_pkey PRIMARY KEY (uid, gid, pid, db, tname);


--
-- TOC entry 1752 (class 2606 OID 3068299)
-- Dependencies: 1306 1306
-- Name: auth_group_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY auth_group
    ADD CONSTRAINT auth_group_pkey PRIMARY KEY (gid);


--
-- TOC entry 1754 (class 2606 OID 3068307)
-- Dependencies: 1307 1307
-- Name: auth_level_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY auth_level
    ADD CONSTRAINT auth_level_pkey PRIMARY KEY (lid);


--
-- TOC entry 1756 (class 2606 OID 3068313)
-- Dependencies: 1308 1308 1308
-- Name: auth_member_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY auth_member
    ADD CONSTRAINT auth_member_pkey PRIMARY KEY (uid, gid);


--
-- TOC entry 1758 (class 2606 OID 3068318)
-- Dependencies: 1309 1309
-- Name: auth_priv_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY auth_priv
    ADD CONSTRAINT auth_priv_pkey PRIMARY KEY (pid);


--
-- TOC entry 1762 (class 2606 OID 3068326)
-- Dependencies: 1310 1310 1310
-- Name: auth_right_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY auth_right
    ADD CONSTRAINT auth_right_pkey PRIMARY KEY (uid, lid);


--
-- TOC entry 1764 (class 2606 OID 3068337)
-- Dependencies: 1311 1311
-- Name: auth_special_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY auth_special
    ADD CONSTRAINT auth_special_pkey PRIMARY KEY (sid);


--
-- TOC entry 1766 (class 2606 OID 3068346)
-- Dependencies: 1312 1312
-- Name: auth_user_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY auth_user
    ADD CONSTRAINT auth_user_pkey PRIMARY KEY (uid);


--
-- TOC entry 1770 (class 2606 OID 3068356)
-- Dependencies: 1313 1313
-- Name: db_leer_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY db_leer
    ADD CONSTRAINT db_leer_pkey PRIMARY KEY (id);


--
-- TOC entry 1778 (class 2606 OID 3068421)
-- Dependencies: 1317 1317
-- Name: kid; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY site_keyword
    ADD CONSTRAINT kid PRIMARY KEY (kid);


--
-- TOC entry 1760 (class 2606 OID 3068320)
-- Dependencies: 1309 1309
-- Name: priv; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY auth_priv
    ADD CONSTRAINT priv UNIQUE (priv);


--
-- TOC entry 1772 (class 2606 OID 3068375)
-- Dependencies: 1314 1314
-- Name: site_file_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY site_file
    ADD CONSTRAINT site_file_pkey PRIMARY KEY (fid);


--
-- TOC entry 1776 (class 2606 OID 3068401)
-- Dependencies: 1316 1316
-- Name: site_form_lang_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY site_form_lang
    ADD CONSTRAINT site_form_lang_pkey PRIMARY KEY (flid);


--
-- TOC entry 1774 (class 2606 OID 3068388)
-- Dependencies: 1315 1315
-- Name: site_form_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY site_form
    ADD CONSTRAINT site_form_pkey PRIMARY KEY (fid);


--
-- TOC entry 1780 (class 2606 OID 3068442)
-- Dependencies: 1318 1318 1318 1318
-- Name: site_lock_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY site_lock
    ADD CONSTRAINT site_lock_pkey PRIMARY KEY (lang, label, tname);


--
-- TOC entry 1782 (class 2606 OID 3068461)
-- Dependencies: 1319 1319
-- Name: site_menu_lang_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY site_menu_lang
    ADD CONSTRAINT site_menu_lang_pkey PRIMARY KEY (mlid);


--
-- TOC entry 1788 (class 2606 OID 3068765)
-- Dependencies: 1334 1334
-- Name: site_menu_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY site_menu
    ADD CONSTRAINT site_menu_pkey PRIMARY KEY (mid);


--
-- TOC entry 1784 (class 2606 OID 3068480)
-- Dependencies: 1320 1320 1320 1320 1320
-- Name: site_text_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY site_text
    ADD CONSTRAINT site_text_pkey PRIMARY KEY (lang, label, tname, version);


--
-- TOC entry 1768 (class 2606 OID 3068348)
-- Dependencies: 1312 1312
-- Name: username; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace: 
--

ALTER TABLE ONLY auth_user
    ADD CONSTRAINT username UNIQUE (username);


--
-- TOC entry 1793 (class 0 OID 0)
-- Dependencies: 4
-- Name: public; Type: ACL; Schema: -; Owner: -
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


-- Completed on 2010-01-29 13:23:00 CET

--
-- PostgreSQL database dump complete
--

