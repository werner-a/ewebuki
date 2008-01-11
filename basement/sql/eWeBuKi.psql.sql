--
-- PostgreSQL database dump
--

SET client_encoding = 'LATIN9';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

--
-- Name: SCHEMA public; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON SCHEMA public IS 'Standard public schema';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: auth_content; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE auth_content (
    uid integer DEFAULT 0 NOT NULL,
    gid integer DEFAULT 0 NOT NULL,
    pid integer DEFAULT 0 NOT NULL,
    db character varying(20) DEFAULT ''::character varying NOT NULL,
    tname character varying(50) DEFAULT ''::character varying NOT NULL,
    ebene text DEFAULT ''::text NOT NULL,
    kategorie text DEFAULT ''::text NOT NULL
);


ALTER TABLE public.auth_content OWNER TO postgres;

--
-- Name: auth_group; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE auth_group (
    gid integer NOT NULL,
    ggroup character varying(30) NOT NULL,
    beschreibung text NOT NULL
);


ALTER TABLE public.auth_group OWNER TO postgres;

--
-- Name: auth_group_gid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE auth_group_gid_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.auth_group_gid_seq OWNER TO postgres;

--
-- Name: auth_group_gid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE auth_group_gid_seq OWNED BY auth_group.gid;


--
-- Name: auth_group_gid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('auth_group_gid_seq', 2, true);


--
-- Name: auth_level; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE auth_level (
    lid integer NOT NULL,
    "level" character varying(10) NOT NULL,
    beschreibung text NOT NULL
);


ALTER TABLE public.auth_level OWNER TO postgres;

--
-- Name: auth_level_lid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE auth_level_lid_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.auth_level_lid_seq OWNER TO postgres;

--
-- Name: auth_level_lid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE auth_level_lid_seq OWNED BY auth_level.lid;


--
-- Name: auth_level_lid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('auth_level_lid_seq', 1, false);


--
-- Name: auth_member; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE auth_member (
    uid integer DEFAULT 0 NOT NULL,
    gid integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.auth_member OWNER TO postgres;

--
-- Name: auth_priv; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE auth_priv (
    pid integer DEFAULT 0 NOT NULL,
    priv character varying(20) NOT NULL
);


ALTER TABLE public.auth_priv OWNER TO postgres;

--
-- Name: auth_right; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE auth_right (
    uid integer DEFAULT 0 NOT NULL,
    lid integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.auth_right OWNER TO postgres;

--
-- Name: auth_special; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE auth_special (
    sid integer NOT NULL,
    suid integer DEFAULT 0 NOT NULL,
    content integer DEFAULT 0,
    sdb character varying(20) NOT NULL,
    stname character varying(50) NOT NULL,
    sebene text,
    skategorie text,
    sbeschreibung text
);


ALTER TABLE public.auth_special OWNER TO postgres;

--
-- Name: auth_special_sid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE auth_special_sid_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.auth_special_sid_seq OWNER TO postgres;

--
-- Name: auth_special_sid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE auth_special_sid_seq OWNED BY auth_special.sid;


--
-- Name: auth_special_sid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('auth_special_sid_seq', 1, false);


--
-- Name: auth_user; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE auth_user (
    uid integer NOT NULL,
    nachname character varying(40) NOT NULL,
    vorname character varying(40) NOT NULL,
    email character varying(60) NOT NULL,
    username character varying(20) NOT NULL,
    pass character varying(20) NOT NULL
);


ALTER TABLE public.auth_user OWNER TO postgres;

--
-- Name: auth_user_uid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE auth_user_uid_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.auth_user_uid_seq OWNER TO postgres;

--
-- Name: auth_user_uid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE auth_user_uid_seq OWNED BY auth_user.uid;


--
-- Name: auth_user_uid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('auth_user_uid_seq', 1, false);


--
-- Name: db_leer; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE db_leer (
    id integer NOT NULL,
    field1 character varying(255) NOT NULL,
    field2 text NOT NULL
);


ALTER TABLE public.db_leer OWNER TO postgres;

--
-- Name: db_leer_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE db_leer_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.db_leer_id_seq OWNER TO postgres;

--
-- Name: db_leer_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE db_leer_id_seq OWNED BY db_leer.id;


--
-- Name: db_leer_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('db_leer_id_seq', 1, false);


--
-- Name: site_file; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE site_file (
    fid integer NOT NULL,
    frefid integer DEFAULT 0 NOT NULL,
    fuid integer DEFAULT 0 NOT NULL,
    fdid character varying(2) DEFAULT 0 NOT NULL,
    ftname character varying(255) DEFAULT ''::character varying NOT NULL,
    ffname character varying(255) DEFAULT ''::character varying NOT NULL,
    ffart character varying(3) DEFAULT 'jpg'::character varying NOT NULL,
    fdesc text DEFAULT ''::text NOT NULL,
    funder character varying(255),
    fhit character varying(255),
    fdel text,
    CONSTRAINT site_file_ffart_check CHECK (((((((((((ffart)::text = 'gif'::text) OR ((ffart)::text = 'jpg'::text)) OR ((ffart)::text = 'png'::text)) OR ((ffart)::text = 'zip'::text)) OR ((ffart)::text = 'odt'::text)) OR ((ffart)::text = 'ods'::text)) OR ((ffart)::text = 'odp'::text)) OR ((ffart)::text = 'gz'::text)) OR ((ffart)::text = 'bz2'::text)))
);


ALTER TABLE public.site_file OWNER TO postgres;

--
-- Name: site_file_fid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE site_file_fid_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.site_file_fid_seq OWNER TO postgres;

--
-- Name: site_file_fid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE site_file_fid_seq OWNED BY site_file.fid;


--
-- Name: site_file_fid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('site_file_fid_seq', 10, true);


--
-- Name: site_form; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE site_form (
    fid integer NOT NULL,
    flabel character varying(20) NOT NULL,
    ftname character varying(40) NOT NULL,
    fsize character varying(7) DEFAULT 0 NOT NULL,
    fclass character varying(30) NOT NULL,
    fstyle character varying(60) NOT NULL,
    foption character varying(20),
    frequired integer DEFAULT 0 NOT NULL,
    fcheck text NOT NULL,
    CONSTRAINT site_form_foption_check CHECK (((((((foption)::text = 'file'::text) OR ((foption)::text = 'hidden'::text)) OR ((foption)::text = 'password'::text)) OR ((foption)::text = 'pgenum'::text)) OR ((foption)::text = 'readonly'::text))),
    CONSTRAINT site_form_frequired_check CHECK (((frequired = 0) OR (frequired = -1)))
);


ALTER TABLE public.site_form OWNER TO postgres;

--
-- Name: site_form_fid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE site_form_fid_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.site_form_fid_seq OWNER TO postgres;

--
-- Name: site_form_fid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE site_form_fid_seq OWNED BY site_form.fid;


--
-- Name: site_form_fid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('site_form_fid_seq', 1, false);


--
-- Name: site_form_lang; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE site_form_lang (
    flid integer NOT NULL,
    fid integer DEFAULT 0 NOT NULL,
    flang character varying(5) DEFAULT 'de'::character varying NOT NULL,
    fpgenum text,
    fwerte character varying(255) NOT NULL,
    ferror character varying(255) NOT NULL,
    fdberror character varying(255) NOT NULL,
    fchkerror character(255) NOT NULL
);


ALTER TABLE public.site_form_lang OWNER TO postgres;

--
-- Name: site_form_lang_flid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE site_form_lang_flid_seq
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.site_form_lang_flid_seq OWNER TO postgres;

--
-- Name: site_form_lang_flid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE site_form_lang_flid_seq OWNED BY site_form_lang.flid;


--
-- Name: site_form_lang_flid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('site_form_lang_flid_seq', 1, false);


--
-- Name: site_lock; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE site_lock (
    lang character varying(5) NOT NULL,
    label character varying(20) NOT NULL,
    tname character varying(40) NOT NULL,
    byalias character varying(20) NOT NULL,
    lockat timestamp without time zone DEFAULT '1000-01-01 00:00:00'::timestamp without time zone NOT NULL
);


ALTER TABLE public.site_lock OWNER TO postgres;

--
-- Name: site_menu; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE site_menu (
    mid integer NOT NULL,
    refid integer DEFAULT 0,
    entry character varying(30) NOT NULL,
    picture character varying(128),
    sort integer DEFAULT 1000 NOT NULL,
    hide integer DEFAULT 0,
    "level" character varying(10),
    mandatory integer DEFAULT 0,
    defaulttemplate character varying(20) DEFAULT 'default1'::character varying NOT NULL,
    dynamiccss character varying(5),
    dynamicbg character varying(128),
    CONSTRAINT site_menu_defaulttemplate_check CHECK ((((((defaulttemplate)::text = 'default1'::text) OR ((defaulttemplate)::text = 'default2'::text)) OR ((defaulttemplate)::text = 'default3'::text)) OR ((defaulttemplate)::text = 'default4'::text))),
    CONSTRAINT site_menu_hide_check CHECK (((hide = -1) OR (hide = 0))),
    CONSTRAINT site_menu_mandatory_check CHECK (((mandatory = -1) OR (mandatory = 0)))
);


ALTER TABLE public.site_menu OWNER TO postgres;

--
-- Name: site_menu_lang; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE site_menu_lang (
    mlid integer NOT NULL,
    mid integer DEFAULT 0 NOT NULL,
    lang character varying(5) DEFAULT 'de'::character varying NOT NULL,
    label character varying(30),
    exturl character varying(128)
);


ALTER TABLE public.site_menu_lang OWNER TO postgres;

--
-- Name: site_menu_lang_mlid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE site_menu_lang_mlid_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.site_menu_lang_mlid_seq OWNER TO postgres;

--
-- Name: site_menu_lang_mlid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE site_menu_lang_mlid_seq OWNED BY site_menu_lang.mlid;


--
-- Name: site_menu_lang_mlid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('site_menu_lang_mlid_seq', 5, true);


--
-- Name: site_menu_mid_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE site_menu_mid_seq
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;


ALTER TABLE public.site_menu_mid_seq OWNER TO postgres;

--
-- Name: site_menu_mid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE site_menu_mid_seq OWNED BY site_menu.mid;


--
-- Name: site_menu_mid_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('site_menu_mid_seq', 5, true);


--
-- Name: site_text; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE site_text (
    lang character varying(5) DEFAULT 'de'::character varying NOT NULL,
    label character varying(20) NOT NULL,
    tname character varying(40) NOT NULL,
    version integer DEFAULT 0 NOT NULL,
    ebene text NOT NULL,
    kategorie text NOT NULL,
    crc32 integer DEFAULT -1 NOT NULL,
    html integer DEFAULT -1 NOT NULL,
    content text NOT NULL,
    changed timestamp without time zone DEFAULT '1970-01-01 00:00:00'::timestamp without time zone NOT NULL,
    bysurname character(40) NOT NULL,
    byforename character varying(40) NOT NULL,
    byemail character varying(60) NOT NULL,
    byalias character varying(20) NOT NULL
);


ALTER TABLE public.site_text OWNER TO postgres;

--
-- Name: gid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE auth_group ALTER COLUMN gid SET DEFAULT nextval('auth_group_gid_seq'::regclass);


--
-- Name: lid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE auth_level ALTER COLUMN lid SET DEFAULT nextval('auth_level_lid_seq'::regclass);


--
-- Name: sid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE auth_special ALTER COLUMN sid SET DEFAULT nextval('auth_special_sid_seq'::regclass);


--
-- Name: uid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE auth_user ALTER COLUMN uid SET DEFAULT nextval('auth_user_uid_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE db_leer ALTER COLUMN id SET DEFAULT nextval('db_leer_id_seq'::regclass);


--
-- Name: fid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE site_file ALTER COLUMN fid SET DEFAULT nextval('site_file_fid_seq'::regclass);


--
-- Name: fid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE site_form ALTER COLUMN fid SET DEFAULT nextval('site_form_fid_seq'::regclass);


--
-- Name: flid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE site_form_lang ALTER COLUMN flid SET DEFAULT nextval('site_form_lang_flid_seq'::regclass);


--
-- Name: mid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE site_menu ALTER COLUMN mid SET DEFAULT nextval('site_menu_mid_seq'::regclass);


--
-- Name: mlid; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE site_menu_lang ALTER COLUMN mlid SET DEFAULT nextval('site_menu_lang_mlid_seq'::regclass);


--
-- Data for Name: auth_content; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO auth_content (uid, gid, pid, db, tname, ebene, kategorie) VALUES (0, 1, 1, '', '/', '', '');
INSERT INTO auth_content (uid, gid, pid, db, tname, ebene, kategorie) VALUES (0, 1, 2, '', '/', '', '');
INSERT INTO auth_content (uid, gid, pid, db, tname, ebene, kategorie) VALUES (0, 1, 3, '', '/', '', '');
INSERT INTO auth_content (uid, gid, pid, db, tname, ebene, kategorie) VALUES (0, 1, 4, '', '/', '', '');
INSERT INTO auth_content (uid, gid, pid, db, tname, ebene, kategorie) VALUES (0, 1, 5, '', '/', '', '');


--
-- Data for Name: auth_group; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO auth_group (gid, ggroup, beschreibung) VALUES (1, 'manager', 'manager');


--
-- Data for Name: auth_level; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO auth_level (lid, "level", beschreibung) VALUES (1, 'cms_edit', 'berechtigt zum bearbeiten der templates');
INSERT INTO auth_level (lid, "level", beschreibung) VALUES (2, 'cms_admin', 'berechtigt zur administration');


--
-- Data for Name: auth_member; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO auth_member (uid, gid) VALUES (1, 1);
INSERT INTO auth_member (uid, gid) VALUES (1, 2);


--
-- Data for Name: auth_priv; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO auth_priv (pid, priv) VALUES (1, 'view');
INSERT INTO auth_priv (pid, priv) VALUES (2, 'edit');
INSERT INTO auth_priv (pid, priv) VALUES (3, 'publish');
INSERT INTO auth_priv (pid, priv) VALUES (4, 'admin');
INSERT INTO auth_priv (pid, priv) VALUES (5, 'add');


--
-- Data for Name: auth_right; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO auth_right (uid, lid) VALUES (1, 1);
INSERT INTO auth_right (uid, lid) VALUES (1, 2);


--
-- Data for Name: auth_special; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Data for Name: auth_user; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO auth_user (uid, nachname, vorname, email, username, pass) VALUES (1, 'Doe', 'John', 'john.doe@ewebuki.de', 'ewebuki', 'WFffxluy26Lew');


--
-- Data for Name: db_leer; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO db_leer (id, field1, field2) VALUES (1, 'Erster Eintrag', 'Zweite Spalte');
INSERT INTO db_leer (id, field1, field2) VALUES (2, 'Zweiter Eintrag', 'Zweite Spalte');


--
-- Data for Name: site_file; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO site_file (fid, frefid, fuid, fdid, ftname, ffname, ffart, fdesc, funder, fhit, fdel) VALUES (1, 0, 1, '0', '', 'ewebuki_160x67.png', 'png', 'eWeBuKi Logo Beschreibung', 'eWeBuKi Logo Unterschift', '', NULL);
INSERT INTO site_file (fid, frefid, fuid, fdid, ftname, ffname, ffart, fdesc, funder, fhit, fdel) VALUES (2, 0, 1, '0', '', 'upload--alex.jpg', 'jpg', 'Der Alex in Berlin', 'Der Alex in Berlin', '#p2,10# #p1,10#', NULL);
INSERT INTO site_file (fid, frefid, fuid, fdid, ftname, ffname, ffart, fdesc, funder, fhit, fdel) VALUES (3, 0, 1, '0', '', 'upload--dark.jpg', 'jpg', 'Unwetter naht', ' Unwetter naht', '#p1,20#', NULL);
INSERT INTO site_file (fid, frefid, fuid, fdid, ftname, ffname, ffart, fdesc, funder, fhit, fdel) VALUES (4, 0, 1, '0', '', 'upload--dust.jpg', 'jpg', 'Staub im PC', 'Staub im PC', '#p2,20#', NULL);
INSERT INTO site_file (fid, frefid, fuid, fdid, ftname, ffname, ffart, fdesc, funder, fhit, fdel) VALUES (5, 0, 1, '0', '', 'upload--hummel.jpg', 'jpg', 'Hummelflug', 'Hummelflug', '#p2,30#', NULL);
INSERT INTO site_file (fid, frefid, fuid, fdid, ftname, ffname, ffart, fdesc, funder, fhit, fdel) VALUES (6, 0, 1, '0', '', 'upload--italy.jpg', 'jpg', 'Süditalien', 'Süditalien', '#p1,30#', NULL);
INSERT INTO site_file (fid, frefid, fuid, fdid, ftname, ffname, ffart, fdesc, funder, fhit, fdel) VALUES (7, 0, 1, '0', '', 'upload--karibik.jpg', 'jpg', 'Karibik Blick', 'Karibik Blick', '#p1,40#', NULL);
INSERT INTO site_file (fid, frefid, fuid, fdid, ftname, ffname, ffart, fdesc, funder, fhit, fdel) VALUES (8, 0, 1, '0', '', 'upload--palenque.jpg', 'jpg', 'Maya Ruine', 'Maya Ruine', '#p1,50#', NULL);
INSERT INTO site_file (fid, frefid, fuid, fdid, ftname, ffname, ffart, fdesc, funder, fhit, fdel) VALUES (9, 0, 1, '0', '', 'upload--rose.jpg', 'jpg', 'Rose blüht', 'Rose blüht', '#p2,40#', NULL);
INSERT INTO site_file (fid, frefid, fuid, fdid, ftname, ffname, ffart, fdesc, funder, fhit, fdel) VALUES (10, 0, 1, '0', '', 'upload--wolken.jpg', 'jpg', 'Wolkenblick', 'Wolkenblick', '#p1,60#', NULL);


--
-- Data for Name: site_form; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES (1, 'username', '210295197.modify', '0', '', '', NULL, -1, '');
INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES (2, 'pass', '210295197.modify', '0', '', '', 'password', -1, '');
INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES (3, 'pass', '852881080.modify', '0', '', '', 'password', -1, '');
INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES (4, 'fid', '-939795212.modify', '0', 'hidden', '', 'hidden', -1, '');
INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES (6, 'fdesc', '-939795212.modify', '25', '', '', NULL, 0, '');
INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES (7, 'funder', '-939795212.modify', '30', '', '', NULL, 0, '');
INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES (8, 'fhit', '-939795212.modify', '30', '', '', NULL, 0, '');
INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES (9, 'entry', '-555504947.add', '0', '', '', NULL, -1, 'PREG:^[a-z_\\-\\.0-9]+$');
INSERT INTO site_form (fid, flabel, ftname, fsize, fclass, fstyle, foption, frequired, fcheck) VALUES (10, 'entry', '-555504947.edit', '0', '', '', NULL, -1, 'PREG:^[a-z_\\-\\.0-9]+$');


--
-- Data for Name: site_form_lang; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO site_form_lang (flid, fid, flang, fpgenum, fwerte, ferror, fdberror, fchkerror) VALUES (1, 1, 'de', NULL, '', 'Username darf nicht leer sein.', 'Username bereits vorhanden.', '                                                                                                                                                                                                                                                               ');
INSERT INTO site_form_lang (flid, fid, flang, fpgenum, fwerte, ferror, fdberror, fchkerror) VALUES (2, 2, 'de', NULL, '', 'Passworte nicht identisch oder leer.', '', '                                                                                                                                                                                                                                                               ');
INSERT INTO site_form_lang (flid, fid, flang, fpgenum, fwerte, ferror, fdberror, fchkerror) VALUES (3, 3, 'de', NULL, '', 'Passworte nicht identisch oder leer.', '', '                                                                                                                                                                                                                                                               ');
INSERT INTO site_form_lang (flid, fid, flang, fpgenum, fwerte, ferror, fdberror, fchkerror) VALUES (9, 9, 'de', NULL, '', '', '', 'Ungültige Zeichen im Feld Eintrag.                                                                                                                                                                                                                             ');
INSERT INTO site_form_lang (flid, fid, flang, fpgenum, fwerte, ferror, fdberror, fchkerror) VALUES (10, 10, 'de', NULL, '', '', '', 'Ungültige Zeichen im Feld Eintrag.                                                                                                                                                                                                                             ');


--
-- Data for Name: site_lock; Type: TABLE DATA; Schema: public; Owner: postgres
--



--
-- Data for Name: site_menu; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO site_menu (mid, refid, entry, picture, sort, hide, "level", mandatory, defaulttemplate, dynamiccss, dynamicbg) VALUES (1, 0, 'demo', NULL, 10, NULL, NULL, NULL, 'default1', NULL, NULL);
INSERT INTO site_menu (mid, refid, entry, picture, sort, hide, "level", mandatory, defaulttemplate, dynamiccss, dynamicbg) VALUES (2, 0, 'show', NULL, 20, NULL, NULL, NULL, 'default1', NULL, NULL);
INSERT INTO site_menu (mid, refid, entry, picture, sort, hide, "level", mandatory, defaulttemplate, dynamiccss, dynamicbg) VALUES (3, 0, 'bilderstrecke', NULL, 30, NULL, NULL, NULL, 'default1', NULL, NULL);
INSERT INTO site_menu (mid, refid, entry, picture, sort, hide, "level", mandatory, defaulttemplate, dynamiccss, dynamicbg) VALUES (4, 0, 'fehler', NULL, 40, NULL, NULL, NULL, 'default1', NULL, NULL);
INSERT INTO site_menu (mid, refid, entry, picture, sort, hide, "level", mandatory, defaulttemplate, dynamiccss, dynamicbg) VALUES (5, 0, 'impressum', NULL, 50, NULL, NULL, NULL, 'default1', NULL, NULL);


--
-- Data for Name: site_menu_lang; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO site_menu_lang (mlid, mid, lang, label, exturl) VALUES (1, 1, 'de', 'Demo', NULL);
INSERT INTO site_menu_lang (mlid, mid, lang, label, exturl) VALUES (2, 2, 'de', 'eWeBuKi Show', NULL);
INSERT INTO site_menu_lang (mlid, mid, lang, label, exturl) VALUES (3, 3, 'de', 'Bilderstrecke', NULL);
INSERT INTO site_menu_lang (mlid, mid, lang, label, exturl) VALUES (4, 4, 'de', '404', NULL);
INSERT INTO site_menu_lang (mlid, mid, lang, label, exturl) VALUES (5, 5, 'de', 'Impressum', NULL);


--
-- Data for Name: site_text; Type: TABLE DATA; Schema: public; Owner: postgres
--

INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'abort', '-555504947.delete', 0, '/admin/menued', 'delete', -1, 0, 'Abbrechen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'content', '-555504947.delete', 0, '/admin/menued', 'delete', -1, 0, 'Inhalt', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'entry', '-555504947.delete', 0, '/admin/menued', 'delete', -1, 0, 'Eintrag', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'error_menu', '-555504947.delete', 0, '/admin/menued', 'delete', -1, 0, 'Fehler beim löschen des Menüeintrag', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'error_menu_lang', '-555504947.delete', 0, '/admin/menued', 'delete', -1, 0, 'Fehler beim löschen der Sprache(n)', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'error_text', '-555504947.delete', 0, '/admin/menued', 'delete', -1, 0, 'Fehler beim löschen des/r Text/e', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'inhalt', '-555504947.delete', 0, '/admin/menued', 'delete', -1, 0, 'Den Menüpunkt "!#ausgaben_entry" wirklich löschen?', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'languages', '-555504947.delete', 0, '/admin/menued', 'delete', -1, 0, 'Sprachen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'no_content', '-555504947.delete', 0, '/admin/menued', 'delete', -1, 0, 'Kein Inhalt', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'send', '-555504947.delete', 0, '/admin/menued', 'delete', -1, 0, 'Abschicken', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-555504947.delete', 0, '/admin/menued', 'delete', -1, 0, 'Menü-Editor - Menüpunkt löschen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'abort', '-555504947.edit-multi', 0, '/admin/menued', 'edit', -1, 0, 'Abbrechen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'add', '-555504947.edit-multi', 0, '/admin/menued', 'edit', -1, 0, 'Neue Sprache hinzufügen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'basic', '-555504947.edit-multi', 0, '/admin/menued', 'add', -1, 0, 'Allgemein', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'delete', '-555504947.edit-multi', 0, '/admin/menued', 'edit', -1, 0, 'Diese Sprache löschen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'entry', '-555504947.edit-multi', 0, '/admin/menued', 'edit', -1, 0, 'Eintrag', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'error_lang_add', '-555504947.edit-multi', 0, '/admin/menued', 'edit', -1, 0, 'Diese Sprache ist bereits vorhanden.', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'error_lang_delete', '-555504947.edit-multi', 0, '/admin/menued', 'edit', -1, 0, 'Die Entwickler Sprache kann nicht gelöscht werden.', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'error_result', '-555504947.edit-multi', 0, '/admin/menued', 'edit', -1, 0, 'DB Fehler: ', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'extended', '-555504947.edit-multi', 0, '/admin/menued', 'edit', -1, 0, 'Speziell', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'exturl', '-555504947.edit-multi', 0, '/admin/menued', 'edit', -1, 0, 'externe Url', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'hide', '-555504947.edit-multi', 0, '/admin/menued', 'edit', -1, 0, 'Deaktiviert', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'label', '-555504947.edit-multi', 0, '/admin/menued', 'edit', -1, 0, 'Bezeichnung', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'lang', '-555504947.edit-multi', 0, '/admin/menued', 'edit', -1, 0, 'Sprache', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'language', '-555504947.edit-multi', 0, '/admin/menued', 'edit', -1, 0, 'Sprachen Verwaltung', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'level', '-555504947.edit-multi', 0, '/admin/menued', 'edit', -1, 0, 'benötigter Level', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'madatory', '-555504947.edit-multi', 0, '/admin/menued', 'edit', -1, 0, 'Erzwungen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'new_lang', '-555504947.edit-multi', 0, '/admin/menued', 'edit', -1, 0, 'Neue Sprache', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'refid', '-555504947.edit-multi', 0, '/admin/menued', 'edit', -1, 0, 'Ref. ID', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'reset', '-555504947.edit-multi', 0, '/admin/menued', 'edit', -1, 0, 'Zurücksetzen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'send', '-555504947.edit-multi', 0, '/admin/menued', 'edit', -1, 0, 'Abschicken', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'sort', '-555504947.edit-multi', 0, '/admin/menued', 'edit', -1, 0, 'Sortierung', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'template', '-555504947.edit-multi', 0, '/admin/menued', 'edit', -1, 0, 'Template', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-555504947.edit-multi', 0, '/admin/menued', 'edit', -1, 0, 'Menü-Editor - Menüpunkt', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'abort', '-555504947.edit-single', 0, '/admin/menued', 'edit', -1, 0, 'Abbrechen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'add', '-555504947.edit-single', 0, '/admin/menued', 'add', -1, 0, 'Neue Sprache hinzufügen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'basic', '-555504947.edit-single', 0, '/admin/menued', 'add', -1, 0, 'Allgemein', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'entry', '-555504947.edit-single', 0, '/admin/menued', 'add', -1, 0, 'Eintrag', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'error_lang_add', '-555504947.edit-single', 0, '/admin/menued', 'edit', -1, 0, 'Diese Sprache ist bereits vorhanden.', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'error_lang_delete', '-555504947.edit-single', 0, '/admin/menued', 'edit', -1, 0, 'Die Entwickler Sprache kann nicht gelöscht werden.', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'error_result', '-555504947.edit-single', 0, '/admin/menued', 'add', -1, 0, 'DB Fehler: ', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'extended', '-555504947.edit-single', 0, '/admin/menued', 'add', -1, 0, 'Speziell', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'exturl', '-555504947.edit-single', 0, '/admin/menued', 'add', -1, 0, 'ext. Url', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'hide', '-555504947.edit-single', 0, '/admin/menued', 'edit', -1, 0, 'Versteckt', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'label', '-555504947.edit-single', 0, '/admin/menued', 'add', -1, 0, 'Bezeichnung', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'lang', '-555504947.edit-single', 0, '/admin/menued', 'add', -1, 0, 'Sprache', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'level', '-555504947.edit-single', 0, '/admin/menued', 'add', -1, 0, 'benötigter Level', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'madatory', '-555504947.edit-single', 0, '/admin/menued', 'add', -1, 0, 'Erzwungen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'new_lang', '-555504947.edit-single', 0, '/admin/menued', 'add', -1, 0, 'Neue Sprache', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'refid', '-555504947.edit-single', 0, '/admin/menued', 'add', -1, 0, 'Ref ID.', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'reset', '-555504947.edit-single', 0, '/admin/menued', 'edit', -1, 0, 'Zurücksetzen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'send', '-555504947.edit-single', 0, '/admin/menued', 'edit', -1, 0, 'Abschicken', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'sort', '-555504947.edit-single', 0, '/admin/menued', 'add', -1, 0, 'Sortierung', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'template', '-555504947.edit-single', 0, '/admin/menued', 'add', -1, 0, 'Template', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-555504947.edit-single', 0, '/admin/menued', 'edit', -1, 0, 'Menü-Editor - Menüpunkt bearbeiten', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'button_desc_add', '-555504947.list', 0, '/admin/menued', 'list', -1, 0, 'Unterpunkt hinzufügen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'button_desc_delete', '-555504947.list', 0, '/admin/menued', 'list', -1, 0, 'Löschen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'button_desc_down', '-555504947.list', 0, '/admin/menued', 'list', -1, 0, 'Sortierung - Nach unten', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'button_desc_edit', '-555504947.list', 0, '/admin/menued', 'list', -1, 0, 'Bearbeiten', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'button_desc_move', '-555504947.list', 0, '/admin/menued', 'list', -1, 0, 'Im Menü Baum verschieben', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'button_desc_up', '-555504947.list', 0, '/admin/menued', 'list', -1, 0, 'Sortierung - Nach oben', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'disabled', '-555504947.list', 0, '/admin/menued', 'list', -1, 0, 'Abgeschaltet', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'enabled', '-555504947.list', 0, '/admin/menued', 'list', -1, 0, 'Eingeschaltet', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'error1', '-555504947.list', 0, '/admin/menued', 'list', -1, 0, 'Menüpunkte mit Unterpunkten lassen sich nicht löschen.', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'extern', '-555504947.list', 0, '/admin/menued', 'list', -1, 0, '(extern)', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', 'my', 0, '', 'my', -1, 0, 'Modul Beispiel "my" einfach', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'renumber', '-555504947.list', 0, '/admin/menued', 'list', -1, 0, 'Neu durchnummerieren', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-555504947.list', 0, '/admin/menued', 'list', -1, 0, 'Menu-Editor - Übersicht', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'abort', '-555504947.move', 0, '/admin/menued', 'move', -1, 0, 'Abbrechen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'entry', '-555504947.move', 0, '/admin/menued', 'move', -1, 0, 'Eintrag', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'extern', '-555504947.move', 0, '/admin/menued', 'move', -1, 0, '(extern)', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'reset', '-555504947.move', 0, '/admin/menued', 'move', -1, 0, 'Zurücksetzen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'root', '-555504947.move', 0, '/admin/menued', 'move', -1, 0, 'Ins Hauptmenü', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'send', '-555504947.move', 0, '/admin/menued', 'move', -1, 0, 'Abschicken', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-555504947.move', 0, '/admin/menued', 'move', -1, 0, 'Menü-Editor - Menüpunkt verschieben', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'send', '852881080.modify', 0, '/admin/passed', 'modify', -1, 0, 'Abschicken', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'chkpass', '852881080.modify', 0, '/admin/passed', 'modify', -1, 0, 'Wiederholung', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'inhalt', '852881080.modify', 0, '/admin/passed', 'modify', -1, 0, 'Passwort ändern', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'newpass', '852881080.modify', 0, '/admin/passed', 'modify', -1, 0, 'Neues', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'oldpass', '852881080.modify', 0, '/admin/passed', 'modify', -1, 0, 'Altes', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '852881080.modify', 0, '/admin/passed', 'modify', -1, 0, 'Passwort Editor', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', 'auth', 0, '', 'index', -1, 0, 'Intern', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'desc', 'auth', 0, '', 'index', -1, 0, 'Werkzeuge', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'fileed', 'auth', 0, '', 'index', -1, 0, 'Datei-Editor', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'leveled', 'auth', 0, '', 'index', -1, 0, 'Level-Editor', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'menued', 'auth', 0, '', 'index', -1, 0, 'Menü-Editor', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'nachher', 'auth', 0, '', 'index', -1, 0, 'ist angemeldet.', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'passed', 'auth', 0, '', 'index', -1, 0, 'Passwort-Editor', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'usered', 'auth', 0, '', 'index', -1, 0, 'User-Editor', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'vorher', 'auth', 0, '', 'index', -1, 0, 'Benutzer', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'abort', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Abbrechen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'add', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Datei einfügen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'b', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Fett', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'big', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Grösser als der Rest', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'br', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Erzwungener Umbruch', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'cent', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Zentriert', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'center', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Zentriert', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'cite', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Logisch: cite', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'col', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Tabellenspalte', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'db', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'DB', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'div', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Bereich', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'e', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Mail', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'em', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Logisch: emphatisch', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'email', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'eMail Link', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Datei', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'files', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Dateien', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'h1', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Überschrift Klasse 1', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'h2', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Überschrift Klasse 2', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'hl', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Spezielle Trennlinie', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'hr', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Trennlinie', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'i', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Kursiv', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'img', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Bild', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'imgb', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Bild mit Rahmen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'in', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Initial', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'label', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Marke', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'language', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Sprache', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'link', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Link', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'list', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Liste', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'm1', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Menü dieser Ebene', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'm2', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Menü der Unterebene', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'pre', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Vorformatiert', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'quote', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'In Anführungszeichen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'row', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Tabellenzeile', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 's', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Durchgestrichen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'save', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Speichern', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'small', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Kleiner als der Rest', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'sp', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Geschütztes Leerzeichen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'strong', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Logisch: strong', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'sub', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Tiefgestellt', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'sup', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Hochgestellt', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'tab', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Tabelle', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'tagselect', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Tag auswählen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'template', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Template', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'tt', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Dickengleich', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'u', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Unterstrichen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'up', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Zurück-Link', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'upload', 'cms.edit.cmstag', 0, '', 'index', -1, 0, 'Hinaufladen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'inhalt', '404', 0, '', 'fehlt', -1, 0, '[H1]Fehler 404 - Nicht gefunden.[/H1]

[P]Die Uri !#ausgaben_404seite wurde nicht gefunden.

Leider konnte das System nicht feststellen woher sie gekommen sind[/P].', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'modcol', 'global', 0, '/admin/leveled', 'list', -1, 0, 'Funktionen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'error_dupe', '-555504947.edit-single', 0, '/admin/menued', 'add', -1, 0, 'Der Eintrag ist bereits vorhanden.', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'inhalt', '404referer', 0, '', 'fehlt', -1, 0, '[H1]Fehler 404 - Nicht gefunden.[/H1]

[P]Die Uri: !#ausgaben_404seite wurde nicht gefunden.

Die [LINK=!#ausgaben_404referer]Seite[/LINK] enthaelt einen falschen/alten Link.[/P]', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'error_dupe', '-555504947.edit-multi', 0, '/admin/menued', 'edit', -1, 0, 'Der Eintrag ist bereits vorhanden.', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'error_dupe', '-555504947.move', 0, '/admin/menued', 'move', -1, 0, 'In dieser Ebene existiert bereits ein Eintrag mit gleichem Namen.', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'logout', 'auth', 0, '', 'auth.login', -1, 0, 'Abgemeldet', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'denied', 'auth', 0, '', 'auth.login', -1, 0, 'Zugriff verweigert!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'picture', '-555504947.edit-multi', 0, '/admin/menued', 'edit', -1, 0, 'evt. Bild', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'picture', '-555504947.edit-single', 0, '/admin/menued', 'edit', -1, 0, 'evt. Bild', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'reset', '852881080.modify', 0, '/admin/passed', 'modify', -1, 0, 'Zurücksetzen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'abort', '852881080.modify', 0, '/admin/passed', 'modify', -1, 0, 'Abbrechen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-840786483.list', 0, '/admin/menued', 'list', -1, 0, 'Level-Editor - Übersicht', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-840786483.modify', 0, '/admin/menued', 'edit', -1, 0, 'Level-Editor - Bearbeiten', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'level', '-840786483.modify', 0, '/admin/leveled', 'modify', -1, 0, 'Bezeichnung', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'description', '-840786483.modify', 0, '/admin/leveled', 'modify', -1, 0, 'Beschreibung', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'del', '-840786483.modify', 0, '/admin/leveled', 'edit', -1, 0, 'Entfernen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'add', '-840786483.modify', 0, '/admin/leveled', 'edit', -1, 0, 'Hinzufügen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'login', '210295197.list', 0, '/admin/usered', 'list', -1, 0, 'Login', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'send', '-840786483.delete', 0, '/admin/leveled', 'modify', -1, 0, 'Löschen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'abort', '-840786483.delete', 0, '/admin/leveled', 'modify', -1, 0, 'Abbrechen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'frage', '-840786483.delete', 0, '/admin/leveled', 'modify', -1, 0, 'Wollen Sie den Level "!#ausgaben_level" wirklich löschen?', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'level', '-840786483.details', 0, '/admin/leveled', 'details', -1, 0, 'Bezeichnung', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'user', '-840786483.details', 0, '/admin/leveled', 'details', -1, 0, 'Mitglieder', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'beschreibung', '-840786483.details', 0, '/admin/leveled', 'details', -1, 0, 'Beschreibung', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'edit', '-840786483.details', 0, '/admin/leveled', 'details', -1, 0, 'Bearbeiten', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'list', '-840786483.details', 0, '/admin/leveled', 'details', -1, 0, 'Übersicht', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-840786483.details', 0, '/admin/leveled', 'details', -1, 0, 'Level Editor - Eigenschaften', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-840786483.delete', 0, '/admin/leveled', 'modify', -1, 0, 'Level-Editor - Löschen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '210295197.list', 0, '/admin/usered', 'list', -1, 0, 'User-Editor - Übersicht', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-939795212.list', 0, '/admin/fileed', 'list', -1, 0, 'Datei-Editor - Übersicht', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'search', '-939795212.list', 0, '/admin/fileed', 'list', -1, 0, 'Suche', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'gesamt', '-939795212.list', 0, '/admin/fileed', 'list', -1, 0, 'Gesamt:', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'fileedit', '-939795212.list', 0, '/admin/fileed', 'list', -1, 0, 'Bearbeiten', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'filedelete', '-939795212.list', 0, '/admin/fileed', 'list', -1, 0, 'Löschen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ffname', '-939795212.modify', 0, '/admin', 'usered', -1, 0, 'Dateiname', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'fdesc', '-939795212.modify', 0, '/admin', 'usered', -1, 0, 'Bildbeschreibung', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'funder', '-939795212.modify', 0, '/admin', 'usered', -1, 0, 'Bildunterschrift', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'fhit', '-939795212.modify', 0, '/admin', 'usered', -1, 0, 'Schlagworte', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'upa', '-939795212.modify', 0, '/admin', 'usered', -1, 0, 'Die aktuelle Datei durch', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'upb', '-939795212.modify', 0, '/admin', 'usered', -1, 0, 'ersetzen.', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'send', '-939795212.modify', 0, '/admin', 'usered', -1, 0, 'Abschicken', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'reset', '-939795212.modify', 0, '/admin', 'usered', -1, 0, 'Zurücksetzen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'abort', '-939795212.modify', 0, '/admin', 'usered', -1, 0, 'Abbrechen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'cmslink', 'global', 1, '/admin/fileed', 'list', -1, 0, 'zum Content Editor', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'level', '-840786483.list', 0, '/admin/leveled', 'list', -1, 0, 'Bezeichnung', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'beschreibung', '-840786483.list', 0, '/admin/leveled', 'list', -1, 0, 'Beschreibung', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'delete', 'global', 0, '/admin/leveled', 'list', -1, 0, 'Löschen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'edit', 'global', 0, '/admin/leveled', 'list', -1, 0, 'Bearbeiten', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'back', 'global', 0, '/admin/leveled', 'details', -1, 0, 'Zurück', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'members', '-840786483.delete', 0, '/admin/leveled', 'delete', -1, 0, 'Mitglieder', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '210295197.modify', 0, '/admin/usered', 'modify', -1, 0, 'User-Editor - Bearbeiten', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'error_oldpass', '852881080.modify', 0, '/admin/passed', 'modify', -1, 0, 'Das alte Passwort stimmt nicht!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'error_chkpass', '852881080.modify', 0, '/admin/passed', 'modify', -1, 0, 'Das Neue Passwort und die Wiederholung stimmen nicht überein!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'nachname', '210295197.modify', 0, '/admin/usered', 'modify', -1, 0, 'Nachname', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'vorname', '210295197.modify', 0, '/admin/usered', 'modify', -1, 0, 'Vorname', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'email', '210295197.modify', 0, '/admin/usered', 'modify', -1, 0, 'eMail', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'send', 'global', 0, '/admin/usered', 'edit', -1, 0, 'Abschicken', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'frage', '210295197.delete', 0, '/admin/usered', 'modify', -1, 0, 'Wollen Sie den User "!#ausgaben_username" wirklich löschen?', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'login', '210295197.details', 0, '/admin/usered', 'details', -1, 0, 'Login', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '210295197.delete', 0, '/admin/usered', 'modify', -1, 0, 'User-Editor - Löschen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '210295197.details', 0, '/admin/usered', 'details', -1, 0, 'User-Editor - Eigenschaften', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'username', '210295197.modify', 0, '/admin/usered', 'modify', -1, 0, 'Login', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'newpass', '210295197.modify', 0, '/admin/usered', 'modify', -1, 0, 'Passwort', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'chkpass', '210295197.modify', 0, '/admin/usered', 'modify', -1, 0, 'Wiederholung', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', 'base', 0, '', 'impressum', -1, 0, 'Menu', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'copyright', 'base', 0, '', 'index', -1, 0, 'eWeBuKi - Copyright 2003-2008', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'kekse', 'base', 0, '', 'impressum', -1, 0, 'Kekse', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'bloged', 'auth', 0, '/admin/passed', 'modify', -1, 0, 'Blog-Editor', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'send', '-939795212.delete', 0, '/admin/menued', 'list', -1, 0, 'Abschicken', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'abort', '-939795212.delete', 0, '/admin/menued', 'list', -1, 0, 'Abbrechen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-939795212.delete', 0, '/admin/menued', 'delete', -1, 0, 'Datei Editor - Datei löschen!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'inhalt', '-939795212.delete', 0, '/admin/fileed', 'delete', -1, 0, 'Folgende Dateien wurden zum löschen ausgewählt:', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-939795212.upload', 0, '/admin/menued', 'list', -1, 0, 'Datei-Editor Upload', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file', '-939795212.upload', 0, '/admin/menued', 'list', -1, 0, 'Dateiauswahl', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'send', '-939795212.upload', 0, '/admin/menued', 'list', -1, 0, 'Abschicken', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'reset', '-939795212.upload', 0, '/admin/menued', 'edit', -1, 0, 'Zurücksetzen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'abort', '-939795212.upload', 0, '/admin/menued', 'edit', -1, 0, 'Abbrechen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-939795212.modify', 0, '/admin/menued', 'add', -1, 0, 'Datei-Editor - Datei Eigenschaften bearbeiten', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'answera', '-939795212.list', 0, '/admin/fileed', 'list', -1, 0, 'Ihre Schnellsuche nach', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'answerb', '-939795212.list', 0, '/admin/fileed', 'list', -1, 0, 'hat', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'answerc_no', '-939795212.list', 0, '/admin/fileed', 'list', -1, 0, 'keine Einträge gefunden.', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'answerc_yes', '-939795212.list', 0, '/admin/fileed', 'list', -1, 0, 'folgende Einträge gefunden.', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'next', '-939795212.list', 0, '/admin/fileed', 'list', -1, 0, 'Vorherige Seite', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'prev', '-939795212.list', 0, '/admin/fileed', 'list', -1, 0, 'Nexte Seite', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'filecollect', '-939795212.list', 2, '/admin/fileed', 'list', -1, 0, 'Gruppieren', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-939795212.compilation', 1, '/admin/fileed', 'compilation', -1, 0, 'Gruppierung - Übersicht', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'error_edit', '-939795212.modify', 0, '/admin/fileed', 'edit', -1, 0, 'Bild kann nur vom Eigentümer bearbeitet werden.', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'references', '-939795212.modify', 0, '/admin/fileed', 'edit', -1, 0, 'Ist enthalten in:', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'details', 'global', 0, '/admin/leveled', 'list', -1, 0, 'Details', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'new', 'global', 0, '/admin/leveled', 'list', -1, 0, 'Neuer Eintrag', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'special', '210295197.delete', 0, '/admin/usered', 'delete', -1, 0, 'Spezial Rechte', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'reset', 'global', 0, '/admin/usered', 'edit', -1, 0, 'Zurücksetzen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'abort', 'global', 0, '/admin/usered', 'edit', -1, 0, 'Abbrechen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'del', '210295197.modify', 0, '/admin/usered', 'edit', -1, 0, 'Nehmen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'actual', '210295197.modify', 0, '/admin/usered', 'edit', -1, 0, 'Besitzt', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'avail', '210295197.modify', 0, '/admin/usered', 'edit', -1, 0, 'Verfügbar', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'add', '210295197.modify', 0, '/admin/usered', 'edit', -1, 0, 'Geben', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'actual', '-840786483.modify', 0, '/admin/leveled', 'edit', -1, 0, 'Mitglieder', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'avail', '-840786483.modify', 0, '/admin/leveled', 'edit', -1, 0, 'Verfügbar', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'surname', '210295197.list', 0, '/admin/usered', 'list', -1, 0, 'Nachname', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'forename', '210295197.list', 0, '/admin/usered', 'list', -1, 0, 'Vorname', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'right', '210295197.delete', 0, '/admin/usered', 'delete', -1, 0, 'Rechte', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-2051315182.list', 0, '/admin/bloged', 'list', -1, 0, 'Blog-Editor - Übersicht', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'anzahl', 'global', 0, '/admin/leveled', 'list', -1, 0, 'Einträge: ', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'surname', '210295197.details', 0, '/admin/usered', 'details', -1, 0, 'Nachname', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'forename', '210295197.details', 0, '/admin/usered', 'details', -1, 0, 'Vorname', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'email', '210295197.details', 0, '/admin/usered', 'details', -1, 0, 'E-Mail', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'level', '210295197.details', 0, '/admin/usered', 'details', -1, 0, 'Rechte', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'field1', 'my', 0, '', 'my', -1, 0, 'Feld 1', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'field2', 'my', 0, '', 'my', -1, 0, 'Feld 2', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'inhalt', 'my', 0, '', 'my', -1, 0, 'Beispiel für eine einfache Funktion.', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-1468826685.list', 0, '/dir/my', 'list', -1, 0, 'Modul Beispiel "my" erweitert - Übersicht', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'field1', '-1468826685.list', 0, '/dir/my', 'list', -1, 0, 'Feld 1', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-1468826685.modify', 0, '/dir/my', 'edit', -1, 0, 'Modul Beispiel "my" erweitert - Bearbeiten', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'field1', '-1468826685.modify', 0, '/admin/leveled', 'list', -1, 0, 'Feld 1', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'field2', '-1468826685.modify', 0, '/dir/my', 'edit', -1, 0, 'Feld 2', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-1468826685.delete', 0, '/dir/my', 'delete', -1, 0, 'Modul Beispiel "my" erweitert - Löschen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'field1', '-1468826685.delete', 0, '/dir/my', 'delete', -1, 0, 'Feld 1', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'field2', '-1468826685.delete', 0, '/dir/my', 'delete', -1, 0, 'Feld 2', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-1468826685.details', 0, '/dir/my', 'details', -1, 0, 'Modul Beispiel "my" erweitert - Details', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'field1', '-1468826685.details', 0, '/dir/my', 'details', -1, 0, 'Feld 1', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'field2', '-1468826685.details', 0, '/dir/my', 'details', -1, 0, 'Feld 2', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'inhalt', 'demo', 0, '', 'demo', -1, 0, '[H1]Erstes Kapitel[/H1]

[H2]Erster Absatz[/H2]

[P]Weit hinten, hinter den Wortbergen, fern der Länder Vokalien und Konsonantien leben die Blindtexte. Abgeschieden wohnen Sie in Buchstabhausen an der Küste des Semantik, eines großen Sprachozeans. Ein kleines Bächlein namens Duden fließt durch ihren Ort und versorgt sie mit den nötigen Regelialien. Es ist ein paradiesmatisches Land, in dem einem gebratene Satzteile in den Mund fliegen. Nicht einmal von der allmächtigen Interpunktion werden die Blindtexte beherrscht – ein geradezu unorthographisches Leben.[/P]


[H2]Zweiter Absatz[/H2]


[P]Eines Tages aber beschloß eine kleine Zeile Blindtext, ihr Name war Lorem Ipsum, hinaus zu gehen in die weite Grammatik. Der große Oxmox riet ihr davon ab, da es dort wimmele von bösen Kommata, wilden Fragezeichen und hinterhältigen Semikoli, doch das Blindtextchen ließ sich nicht beirren. Es packte seine sieben Versalien, schob sich sein Initial in den Gürtel und machte sich auf den Weg.[/P]


[H1]Zweites Kapitel[/H1]

[H2]Erster Absatz[/H2]

[P]Als es die ersten Hügel des Kursivgebirges erklommen hatte, warf es einen letzten Blick zurück auf die Skyline seiner Heimatstadt Buchstabhausen, die Headline von Alphabetdorf und die Subline seiner eigenen Straße, der Zeilengasse. Wehmütig lief ihm eine rethorische Frage über die Wange, dann setzte es seinen Weg fort.[/P]

[P=box]Unterwegs traf es eine Copy. Die Copy warnte das Blindtextchen, da, wo sie herkäme wäre sie zigmal umgeschrieben worden und alles, was von ihrem Ursprung noch übrig wäre, sei das Wort "und" und das Blindtextchen solle umkehren und wieder in sein eigenes, sicheres Land zurückkehren.[/P]

[H2]Dritter Absatz[/H2]

[P]Doch alles Gutzureden konnte es nicht überzeugen und so dauerte es nicht lange, bis ihm ein paar heimtückische Werbetexter auflauerten, es mit Longe und Parole betrunken machten und es dann in ihre Agentur schleppten, wo sie es für ihre Projekte wieder und wieder mißbrauchten. Und wenn es nicht umgeschrieben wurde, dann benutzen Sie es immernoch.[/P]', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-102562964.list', 1, '/admin/grouped', 'list', -1, 0, 'Gruppen-Editor - Übersicht', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'filelist', 'global', 1, '/admin/fileed', 'list', -1, 0, 'Datei-Editor', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'filecompilation', 'global', 3, '/admin/fileed', 'list', -1, 0, 'Gruppierung', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'fileupload', 'global', 1, '/admin/fileed', 'list', -1, 0, 'Upload', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'group', '-939795212.list', 1, '/admin/fileed', 'list', -1, 0, 'Gruppe', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'search', '-939795212.compilation', 1, '/admin/fileed', 'list', -1, 0, 'Suche', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'compilation_search', '-939795212.compilation', 1, '/admin/fileed', 'compilation', -1, 0, 'Galerien', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'compilation', '-939795212.compilation', 1, '/admin/fileed', 'compilation', -1, 0, 'Galerie', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'of', '-939795212.compilation', 1, '/admin/fileed', 'list', -1, 0, 'von', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'group', '-102562964.list', 1, '/admin/grouped', 'list', -1, 0, 'Gruppe', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error0', 'global', 1, '/admin/fileed', 'upload', -1, 0, 'Kein Fehler, Datei entspricht den Vorgaben', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'beschreibung', '-102562964.list', 1, '/admin/grouped', 'list', -1, 0, 'Beschreibung', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error1', 'global', 1, '/admin/fileed', 'upload', -1, 0, 'Die hochgeladene Datei überschreitet die Größenbeschränkung "upload_max_filesize" der php.ini!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error2', 'global', 1, '/admin/fileed', 'upload', -1, 0, 'Die hochgeladene Datei überschreitet die im Formular festgelegte "MAX_FILE_SIZE"-Anweisung!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'group', '-102562964.modify', 3, '/admin/grouped', 'add', -1, 0, 'Gruppe', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error3', 'global', 1, '/admin/fileed', 'upload', -1, 0, 'Die Datei wurde nur teilweise hochgeladen!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error4', 'global', 1, '/admin/fileed', 'upload', -1, 0, 'Es wurde keine Datei hochgeladen!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error6', 'global', 1, '/admin/fileed', 'upload', -1, 0, 'Es ist kein temporäres Upload-Verzeichnis verfügbar!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error7', 'global', 1, '/admin/fileed', 'upload', -1, 0, 'Es kann nicht auf die Platte geschrieben werden!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error8', 'global', 1, '/admin/fileed', 'upload', -1, 0, 'Der Upload wurde von einer Erweiterung verhindert!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error10', 'global', 1, '/admin/fileed', 'upload', -1, 0, 'Die Datei ist zu groß!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error11', 'global', 1, '/admin/fileed', 'upload', -1, 0, 'Ungültiges Dateiformat!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error12', 'global', 1, '/admin/fileed', 'upload', -1, 0, 'Die Datei ist schon vorhanden!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error13', 'global', 1, '/admin/fileed', 'upload', -1, 0, 'Unbekannter Fehler. Eventuell ist die "post_max_size" der php.ini die Ursache. Bitte nicht weiter probieren!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error14', 'global', 1, '/admin/fileed', 'upload', -1, 0, 'Es wird mindestens die PHP-Version 4.x.x benötigt!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'avail', '-102562964.modify', 1, '/admin/grouped', 'add', -1, 0, 'Verfügbar', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'actual', '-102562964.modify', 1, '/admin/grouped', 'edit', -1, 0, 'Mitglieder', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'del', '-102562964.modify', 1, '/admin/grouped', 'edit', -1, 0, 'Entfernen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'add', '-102562964.modify', 1, '/admin/grouped', 'edit', -1, 0, 'Hinzufügen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'description', '-102562964.modify', 1, '/admin/grouped', 'edit', -1, 0, 'Beschreibung', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'frage', '-102562964.delete', 2, '/admin/grouped', 'delete', -1, 0, 'Wollen Sie die Gruppe "!#ausgaben_ggroup" wirklich löschen ?', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'members', '-102562964.delete', 1, '/admin/grouped', 'delete', -1, 0, 'Mitglieder', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'user', '-102562964.details', 1, '/admin/grouped', 'details', -1, 0, 'Mitglieder', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'group', '-102562964.details', 1, '/admin/grouped', 'details', -1, 0, 'Gruppe', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'beschreibung', '-102562964.details', 1, '/admin/grouped', 'details', -1, 0, 'Beschreibung', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-102562964.details', 1, '/admin/grouped', 'details', -1, 0, 'Gruppen-Editor - Eigenschaften', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-102562964.delete', 1, '/admin/grouped', 'delete', -1, 0, 'Gruppen-Editor - Löschen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-102562964.modify', 1, '/admin/grouped', 'edit', -1, 0, 'Gruppen-Editor - Bearbeiten', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'error_dupe', '-102562964.modify', 1, '/admin/grouped', 'add', -1, 0, 'Fehler: Es gibt bereits eine Gruppe mit diesem Namen !', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'secret', 'auth', 0, '', 'auth', -1, 0, 'Hintereingang', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'all_files', 'global', 1, '/admin/fileed', 'list', -1, 0, 'Dateien', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'sel_files', 'global', 1, '/admin/fileed', 'list', -1, 0, 'Markierte', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'images', '-939795212.list', 1, '/admin/fileed', 'list', -1, 0, 'Bilder', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'button_desc_jump', '-555504947.list', 1, '/admin/menued', 'list', -1, 0, 'zur Content-Seite', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'grouped', 'auth', 0, '', 'auth', -1, 0, 'Gruppen-Editor', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'url', '807284226.modify', 1, '/admin/righted', 'edit', -1, 0, 'Pfad: ', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'actual', '807284226.modify', 1, '/admin/righted', 'edit', -1, 0, 'Vorhanden Rechte für diese Url', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'group', '807284226.modify', 1, '/admin/righted', 'edit', -1, 0, 'Gruppen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'priv', '807284226.modify', 1, '/admin/righted', 'edit', -1, 0, 'Rechte', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '807284226.modify', 2, '/admin/righted', 'edit', -1, 0, 'Rechte-Editor', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'delete_ok', '-939795212.delete', 2, '/admin/fileed', 'delete', -1, 0, 'wird gelöscht!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'inhalt', '-939795212.delete', 1, '/admin/fileed', 'delete', -1, 0, 'Folgende Dateien wurden zum löschen ausgewählt:', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'user_error', '-939795212.delete', 1, '/admin/fileed', 'delete', -1, 0, 'Sie sind nicht Eigentümer dieser Datei!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'content_error', '-939795212.delete', 1, '/admin/fileed', 'delete', -1, 0, 'ist enthalten auf', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'group_error', '-939795212.delete', 1, '/admin/fileed', 'delete', -1, 0, 'ist enthalten in folgender Gruppe:', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'delete_error', '-939795212.delete', 1, '/admin/fileed', 'delete', -1, 0, 'Datei konnte nicht gelöscht werden!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'root', '-555504947.list', 1, '/admin/menued', 'move', -1, 0, 'Als Hauptpunkt anlegen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'compilation_info', 'global', 1, '/impressum', 'test', -1, 0, 'Fotostrecke starten: Klicken Sie auf ein Bild', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'add', '807284226.modify', 1, '/admin/righted', 'edit', -1, 0, 'Hinzufügen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'compilation_pics', 'global', 1, '/impressum', 'test', -1, 0, 'Bilder', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'of', 'global', 1, '/impressum/test', 'view', -1, 0, 'von', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'selected', '-939795212.compilation', 1, '/admin/fileed', 'compilation', -1, 0, 'ausgewählte Selektion(en):', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'sel_show', '-939795212.compilation', 1, '/admin/fileed', 'compilation', -1, 0, 'Nur diese anzeigen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'num_pics', '-939795212.compilation', 1, '/admin/fileed', 'compilation', -1, 0, 'Bilder insgesamt: ', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'all_names', '-939795212.compilation', 1, '/admin/fileed', 'compilation', -1, 0, 'Alle verwendeten Titel', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'inhalt', 'bilderstrecke', 0, '', 'bilderstrecke', -1, 0, '[H1]Überschrift[/H1]

[P]Demo des "Selection Tag".[/P]

[DIV=box][LINK=http://ewebuki.de/auth/dokumentation/tags/spezial.html#SEL]Beschreibung des "Tag"[/LINK][/DIV]

[HS][/HS]

[P]Das ist ein [SEL=1;m;;]Textlink[/SEL] zu der Bilderstrecke.
[E][SEL=1;m;;]Textlink[/SEL][/E][/P]

[HS][/HS]

[P]Bitte nur ein Bild.
[E][SEL=1;b;True;8]Gruppierung, ein Bild[/SEL][/E][/P]

[SEL=1;b;True;8]Gruppierung, ein Bild[/SEL][HS][/HS]

[P]Oder doch ein paar "Teaser Thumbs"?
[E][SEL=1;b;;3:7:10]Gruppierung, viele Bilder[/SEL][/E][/P]

[SEL=1;b;;3:7:10]Gruppierung, viele Bilder[/SEL]


[HS][/HS]

[P]Und jetzt die "Thumbs" aller Bilder?
[E][SEL=1;b;;a]Gruppierung, alle Bilder[/SEL][/E][/P]

[SEL=1;b;;a]Gruppierung, alle Bilder[/SEL]', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'cmslink', 'global', 0, '/admin/fileed', 'list', -1, 0, 'zum Content Editor', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'filecollect', '-939795212.list', 0, '/admin/fileed', 'list', -1, 0, 'Gruppieren', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-939795212.compilation', 0, '/admin/fileed', 'compilation', -1, 0, 'Gruppierung - Übersicht', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-102562964.list', 0, '/admin/grouped', 'list', -1, 0, 'Gruppen-Editor - Übersicht', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'filelist', 'global', 0, '/admin/fileed', 'list', -1, 0, 'Datei-Editor', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'filecompilation', 'global', 0, '/admin/fileed', 'list', -1, 0, 'Gruppierung', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'fileupload', 'global', 0, '/admin/fileed', 'list', -1, 0, 'Upload', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'group', '-939795212.list', 0, '/admin/fileed', 'list', -1, 0, 'Gruppe', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'search', '-939795212.compilation', 0, '/admin/fileed', 'list', -1, 0, 'Suche', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'compilation_search', '-939795212.compilation', 0, '/admin/fileed', 'compilation', -1, 0, 'Galerien', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'compilation', '-939795212.compilation', 0, '/admin/fileed', 'compilation', -1, 0, 'Galerie', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'of', '-939795212.compilation', 0, '/admin/fileed', 'list', -1, 0, 'von', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'group', '-102562964.list', 0, '/admin/grouped', 'list', -1, 0, 'Gruppe', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error0', 'global', 0, '/admin/fileed', 'upload', -1, 0, 'Kein Fehler, Datei entspricht den Vorgaben', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'beschreibung', '-102562964.list', 0, '/admin/grouped', 'list', -1, 0, 'Beschreibung', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error1', 'global', 0, '/admin/fileed', 'upload', -1, 0, 'Die hochgeladene Datei überschreitet die Größenbeschränkung "upload_max_filesize" der php.ini!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error2', 'global', 0, '/admin/fileed', 'upload', -1, 0, 'Die hochgeladene Datei überschreitet die im Formular festgelegte "MAX_FILE_SIZE"-Anweisung!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'group', '-102562964.modify', 0, '/admin/grouped', 'add', -1, 0, 'Gruppe', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error3', 'global', 0, '/admin/fileed', 'upload', -1, 0, 'Die Datei wurde nur teilweise hochgeladen!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error4', 'global', 0, '/admin/fileed', 'upload', -1, 0, 'Es wurde keine Datei hochgeladen!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error6', 'global', 0, '/admin/fileed', 'upload', -1, 0, 'Es ist kein temporäres Upload-Verzeichnis verfügbar!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error7', 'global', 0, '/admin/fileed', 'upload', -1, 0, 'Es kann nicht auf die Platte geschrieben werden!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error8', 'global', 0, '/admin/fileed', 'upload', -1, 0, 'Der Upload wurde von einer Erweiterung verhindert!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error10', 'global', 0, '/admin/fileed', 'upload', -1, 0, 'Die Datei ist zu groß!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error11', 'global', 0, '/admin/fileed', 'upload', -1, 0, 'Ungültiges Dateiformat!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error12', 'global', 0, '/admin/fileed', 'upload', -1, 0, 'Die Datei ist schon vorhanden!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error13', 'global', 0, '/admin/fileed', 'upload', -1, 0, 'Unbekannter Fehler. Eventuell ist die "post_max_size" der php.ini die Ursache. Bitte nicht weiter probieren!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'file_error14', 'global', 0, '/admin/fileed', 'upload', -1, 0, 'Es wird mindestens die PHP-Version 4.x.x benötigt!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'avail', '-102562964.modify', 0, '/admin/grouped', 'add', -1, 0, 'Verfügbar', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'actual', '-102562964.modify', 0, '/admin/grouped', 'edit', -1, 0, 'Mitglieder', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'del', '-102562964.modify', 0, '/admin/grouped', 'edit', -1, 0, 'Entfernen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'add', '-102562964.modify', 0, '/admin/grouped', 'edit', -1, 0, 'Hinzufügen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'description', '-102562964.modify', 0, '/admin/grouped', 'edit', -1, 0, 'Beschreibung', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'frage', '-102562964.delete', 0, '/admin/grouped', 'delete', -1, 0, 'Wollen Sie die Gruppe "!#ausgaben_ggroup" wirklich löschen ?', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'members', '-102562964.delete', 0, '/admin/grouped', 'delete', -1, 0, 'Mitglieder', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'user', '-102562964.details', 0, '/admin/grouped', 'details', -1, 0, 'Mitglieder', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'group', '-102562964.details', 0, '/admin/grouped', 'details', -1, 0, 'Gruppe', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'beschreibung', '-102562964.details', 0, '/admin/grouped', 'details', -1, 0, 'Beschreibung', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-102562964.details', 0, '/admin/grouped', 'details', -1, 0, 'Gruppen-Editor - Eigenschaften', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-102562964.delete', 0, '/admin/grouped', 'delete', -1, 0, 'Gruppen-Editor - Löschen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-102562964.modify', 0, '/admin/grouped', 'edit', -1, 0, 'Gruppen-Editor - Bearbeiten', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'error_dupe', '-102562964.modify', 0, '/admin/grouped', 'add', -1, 0, 'Fehler: Es gibt bereits eine Gruppe mit diesem Namen !', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'all_files', 'global', 0, '/admin/fileed', 'list', -1, 0, 'Dateien', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'sel_files', 'global', 0, '/admin/fileed', 'list', -1, 0, 'Markierte', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'images', '-939795212.list', 0, '/admin/fileed', 'list', -1, 0, 'Bilder', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'button_desc_jump', '-555504947.list', 0, '/admin/menued', 'list', -1, 0, 'zur Content-Seite', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'url', '807284226.modify', 0, '/admin/righted', 'edit', -1, 0, 'Pfad: ', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'actual', '807284226.modify', 0, '/admin/righted', 'edit', -1, 0, 'Vorhanden Rechte für diese Url', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'group', '807284226.modify', 0, '/admin/righted', 'edit', -1, 0, 'Gruppen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'priv', '807284226.modify', 0, '/admin/righted', 'edit', -1, 0, 'Rechte', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '807284226.modify', 0, '/admin/righted', 'edit', -1, 0, 'Rechte-Editor', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'delete_ok', '-939795212.delete', 0, '/admin/fileed', 'delete', -1, 0, 'wird gelöscht!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'user_error', '-939795212.delete', 0, '/admin/fileed', 'delete', -1, 0, 'Sie sind nicht Eigentümer dieser Datei!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'content_error', '-939795212.delete', 0, '/admin/fileed', 'delete', -1, 0, 'ist enthalten auf', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'group_error', '-939795212.delete', 0, '/admin/fileed', 'delete', -1, 0, 'ist enthalten in folgender Gruppe:', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'delete_error', '-939795212.delete', 0, '/admin/fileed', 'delete', -1, 0, 'Datei konnte nicht gelöscht werden!', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'root', '-555504947.list', 0, '/admin/menued', 'move', -1, 0, 'Als Hauptpunkt anlegen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'compilation_info', 'global', 0, '/impressum', 'test', -1, 0, 'Fotostrecke starten: Klicken Sie auf ein Bild', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'add', '807284226.modify', 0, '/admin/righted', 'edit', -1, 0, 'Hinzufügen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'compilation_pics', 'global', 0, '/impressum', 'test', -1, 0, 'Bilder', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'of', 'global', 0, '/impressum/test', 'view', -1, 0, 'von', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'selected', '-939795212.compilation', 0, '/admin/fileed', 'compilation', -1, 0, 'ausgewählte Selektion(en):', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'sel_show', '-939795212.compilation', 0, '/admin/fileed', 'compilation', -1, 0, 'Nur diese anzeigen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'num_pics', '-939795212.compilation', 0, '/admin/fileed', 'compilation', -1, 0, 'Bilder insgesamt: ', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'all_names', '-939795212.compilation', 0, '/admin/fileed', 'compilation', -1, 0, 'Alle verwendeten Titel', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'button_desc_sort', '-555504947.list', 0, '/admin/menued', 'list', -1, 0, 'Sortierung', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'button_desc_right', '-555504947.list', 0, '/admin/menued', 'list', -1, 0, 'Rechte vergeben', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'h1', '-141347382.modify', 0, '', 'index', -1, 0, 'Überschrift Gr. 1', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'box', '-141347382.modify', 0, '', 'index', -1, 0, 'Kasten', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'p', '-141347382.modify', 0, '', 'index', -1, 0, 'Absatz', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'h2', '-141347382.modify', 0, '', 'index', -1, 0, 'Überschrift Gr. 2', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'h3', '-141347382.modify', 0, '', 'index', -1, 0, 'Überschrift Gr. 3', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ueberschrift', '-141347382.modify', 0, '', 'index', -1, 0, 'Content-Editor', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'template', '-141347382.modify', 0, '', 'index', -1, 0, 'Template', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'label', '-141347382.modify', 0, '', 'index', -1, 0, 'Marke', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'language', '-141347382.modify', 0, '', 'index', -1, 0, 'Sprache', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'h4', '-141347382.modify', 0, '', 'index', -1, 0, 'Überschrift Gr. 4', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'version', '-141347382.modify', 0, '', 'index', -1, 0, 'Version', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'db', '-141347382.modify', 0, '', 'index', -1, 0, 'Datenbank', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'h5', '-141347382.modify', 0, '', 'index', -1, 0, 'Überschrift Gr. 5', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'h6', '-141347382.modify', 0, '', 'index', -1, 0, 'Überschrift Gr. 6', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'pre', '-141347382.modify', 0, '', 'index', -1, 0, 'Vorformatiert', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'div', '-141347382.modify', 0, '', 'index', -1, 0, 'Style Element', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'list', '-141347382.modify', 0, '', 'index', -1, 0, 'Liste', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'hr', '-141347382.modify', 0, '', 'index', -1, 0, 'Trennlinie', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'table', '-141347382.modify', 0, '', 'index', -1, 0, 'Tabelle komplett', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'tab', '-141347382.modify', 0, '', 'index', -1, 0, 'Tabelle', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'row', '-141347382.modify', 0, '', 'index', -1, 0, 'Zeile', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'col', '-141347382.modify', 0, '', 'index', -1, 0, 'Spalte', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'br', '-141347382.modify', 0, '', 'index', -1, 0, 'Umbruch erzwingen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'b', '-141347382.modify', 0, '', 'index', -1, 0, 'Fett', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'img', '-141347382.modify', 0, '', 'index', -1, 0, 'Bild einfügen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'link', '-141347382.modify', 0, '', 'index', -1, 0, 'Link einfügen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'em', '-141347382.modify', 0, '', 'index', -1, 0, 'Emphatisch', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'strong', '-141347382.modify', 0, '', 'index', -1, 0, 'Stark betont', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'cite', '-141347382.modify', 0, '', 'index', -1, 0, 'Zitat', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'i', '-141347382.modify', 0, '', 'index', -1, 0, 'Kursiv', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'tt', '-141347382.modify', 0, '', 'index', -1, 0, 'Dicktengleich', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'u', '-141347382.modify', 0, '', 'index', -1, 0, 'Unterstrichen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 's', '-141347382.modify', 0, '', 'index', -1, 0, 'Durchgestrichen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'st', '-141347382.modify', 0, '', 'index', -1, 0, 'Durchgestrichen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'big', '-141347382.modify', 0, '', 'index', -1, 0, 'Größer', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'small', '-141347382.modify', 0, '', 'index', -1, 0, 'Kleiner', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'sub', '-141347382.modify', 0, '', 'index', -1, 0, 'Tiefgestellt', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'sup', '-141347382.modify', 0, '', 'index', -1, 0, 'Hochgestellt', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'ank', '-141347382.modify', 0, '', 'index', -1, 0, 'Sprung Marke', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'email', '-141347382.modify', 0, '', 'index', -1, 0, 'E-Mail Adresse einfügen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'hs', '-141347382.modify', 0, '', 'index', -1, 0, 'Bearbeiten Marke', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'hl', '-141347382.modify', 0, '', 'index', -1, 0, 'Eigene horizontale Trennlinie', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'imgb', '-141347382.modify', 0, '', 'index', -1, 0, 'Bild erweitert einfügen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'in', '-141347382.modify', 0, '', 'index', -1, 0, 'Initial', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'quote', '-141347382.modify', 0, '', 'index', -1, 0, 'Anführungszeichen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'sel', '-141347382.modify', 0, '', 'index', -1, 0, 'Gruppierung einfügen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'sp', '-141347382.modify', 0, '', 'index', -1, 0, 'Geschütztes Leerzeichen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'charakters', '-141347382.modify', 0, '', 'index', -1, 0, 'Übrige Zeichen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'e', '-141347382.modify', 0, '', 'index', -1, 0, 'eWeBuKi Tag darstellen', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', '!', '-141347382.modify', 0, '', 'index', -1, 0, 'Unsichtbarer Kommentar', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'm0', '-141347382.modify', 0, '', 'index', -1, 0, 'Menü oberhalb', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'm1', '-141347382.modify', 0, '', 'index', -1, 0, 'Menü gleiche Ebene', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'm2', '-141347382.modify', 0, '', 'index', -1, 0, 'Menü unterhalb', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'up', '-141347382.modify', 0, '', 'index', -1, 0, 'Im Menü nach oben', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'prev', '-141347382.modify', 0, '', 'index', -1, 0, 'Vorheriger Menüpunkt', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'next', '-141347382.modify', 0, '', 'index', -1, 0, 'Nächster Menüpunkt', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'int', '-141347382.modify', 0, '', 'index', -1, 0, 'Intelligenter Link (deprecated)', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'center', '-141347382.modify', 0, '', 'index', -1, 0, 'Zentriert', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'inhalt', 'fehler', 0, '', 'fehler', -1, 0, '[H1]404 Test[/H1]\r\n\r\n\r\n[P]Hiermit wird die 404 Fehlerseite angezeigt.\r\n\r\n[LINK=fehlt.html]404 Fehler mit Referer[/LINK]\r\n\r\nUm die zweite 404 Fehlermeldung (Referer unbekannt) sichtbar zu machen,\r\nin der Eingabezeile der obigen 404 Fehlermeldung einfach Enter drücken.[/P]', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'inhalt', 'impressum', 0, '', 'impressum', -1, 0, '[H1]Impressum[/H1]\r\n\r\n\r\n[P]eWeBuKi - Copyright 2003-2007\r\nby [EMAIL=w.ammon(at)chaos.de]Werner Ammon[/EMAIL][/P]\r\n\r\n\r\n[H2]Weitere Infoseiten:[/H2]\r\n\r\n\r\n[P][LINK=http://www.ewebuki.de/]www.ewebuki.de[/LINK]\r\n[LINK=http://developer.berlios.de/projects/ewebuki/]developer.berlios.de/projects/ewebuki/[/LINK][/P]', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'inhalt', 'index', 0, '', 'index', -1, 0, '[H1]Glückwunsch Ihr eWeBuKi läuft![/H1]\r\n\r\n[P]Um sich am System anzumelden benutzen Sie bitte folgende Daten:\r\n\r\nuser: ewebuki\r\npass: ewebuki[/P]\r\n\r\n[P=box][B]ACHTUNG:[/B] Passwort ändern nicht vergessen![/P]\r\n\r\n[P]Weitere Infoseiten:\r\n[LINK=http://www.ewebuki.de/]www.ewebuki.de[/LINK]\r\n[LINK=http://developer.berlios.de/projects/ewebuki/]developer.berlios.de/projects/ewebuki/[/LINK][/P]\r\n', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');
INSERT INTO site_text (lang, label, tname, version, ebene, kategorie, crc32, html, content, changed, bysurname, byforename, byemail, byalias) VALUES ('de', 'inhalt', 'show', 0, '', 'show', -1, 0, '[H1]eWeBuKi Show[/H1]\r\n\r\n\r\n[H2]Tabellen Positionen[/H2]\r\n\r\n[TAB=;300;1]\r\n[ROW]\r\n[COL]1,1[/COL]\r\n[COL=;;u]1,2[/COL]\r\n[COL=r]1,3\r\n\r\n\r\n[/COL]\r\n[/ROW][ROW]\r\n[COL=m]2,1[/COL]\r\n[COL=;;g]2,2[/COL]\r\n[COL=r;;m]2,3\r\n\r\n\r\n[/COL]\r\n[/ROW]\r\n[/TAB]\r\n\r\n\r\n[H2]Easy Template Links[/H2]\r\n\r\n[P]!#lnk_1\r\n!#lnk_2\r\n!#lnk_3[/P]\r\n\r\n[H2]Menu oberhalb (M1,mit Bez.)[/H2]\r\n[M1]nach oben[/M1]\r\n\r\n[H2]Menu oberhalb als Liste (M1=l,ohne Bez.)[/H2]\r\n[M1=l][/M1]\r\n\r\n[H2]Menu gleiche Ebene (M2,mit Bez.)[/H2]\r\n[M2]nach oben[/M2]\r\n\r\n[H2]Menu gleiche Ebene als Liste (M2=l,mit Bez.)[/H2]\r\n[M2=l][/M2]\r\n\r\n\r\n\r\n[H2]Tabellen Abstände[/H2]\r\n[P]Tabellen Abstände (abstand text - tabelle 1)[/P]\r\n\r\n\r\n[TAB=;300;1]\r\n[ROW]\r\n\r\n[COL=l;150]links oben\r\n[/COL]\r\n\r\n[COL=l;150]rechts oben\r\n[/COL]\r\n\r\n[/ROW]\r\n[/TAB]\r\n\r\n[TAB=;300;1]\r\n[ROW]\r\n\r\n[COL=l;150]links oben\r\n[/COL]\r\n\r\n[COL=l;150]rechts oben\r\n[/COL]\r\n\r\n[/ROW]\r\n[/TAB]\r\n\r\n\r\n[P]Tabellen Abstände (abstand text - tabelle 2)[/P]\r\n\r\n[IN]I[/IN]nitial fuer Texte\r\n\r\n[H1][B][EM]Bold EM Tag[/EM][/B] im H1 Tag[/H1]\r\n\r\nText zwischen Linien:\r\n[HL][/HL]\r\nHier kommt der Text.\r\n[HL][/HL]\r\n\r\n[H2]Bilder im Text[/H2]\r\n\r\n[P][IMG=/file/picture/small/img_1.png;l;;;20;;20]eWeBuKi Logo[/IMG]Weit hinten, hinter den Wortbergen, fern der Länder Vokalien und Konsonantien leben die Blindtexte. Abgeschieden wohnen Sie in Buchstabhausen an der Küste des Semantik, eines großen Sprachozeans. Ein kleines Bächlein namens Duden fließt durch ihren Ort und versorgt sie mit den nötigen Regelialien. Es ist ein paradiesmatisches Land, in dem einem gebratene Satzteile in den Mund fliegen. Nicht einmal von der allmächtigen Interpunktion werden die Blindtexte beherrscht - ein geradezu unorthographisches Leben.[/P]\r\n\r\n[H2]Mehrere Bilder rechts[/H2]\r\n\r\n[P]Bei mehreren Bildern rechts gibt es Abstand Probleme. Um das zu umgehen muss der Umlauf mit dem Tag BR=a angehalten werden.[/P]\r\n\r\n[IMGB=/file/picture/small/img_1.png;r;0;b]Logo[/IMGB]Text neben Bild 1[BR=a][/BR]\r\n\r\n[IMGB=/file/picture/small/img_1.png;r]Logo[/IMGB]Text neben Bild 2[BR=a][/BR]\r\n\r\n[P]Nicht nur Bilder sondern auch Text kann mit diesem Trick unter das Bild geschoben werden.[/P]\r\n[H1]ueberschrift h1[/H1]\r\n[H2]ueberschrift h2[/H2]\r\n[H3]ueberschrift h3[/H3]\r\n[H4]ueberschrift h4[/H4]\r\n[H5]ueberschrift h5[/H5]\r\n[H6]ueberschrift h6[/H6]\r\n\r\nAbsaetze mit css einstellen:\r\n[P]Im Absatz ist es Schoen[/P]\r\n\r\nDIV=class jeder css im Content:\r\n[DIV=anderst]Dieser Text ist schoener als der Rest[/DIV]', '1970-01-01 00:00:00', 'Doe                                     ', 'John', 'john.doe@ewebuki.de', 'ewebuki');


--
-- Name: auth_content_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY auth_content
    ADD CONSTRAINT auth_content_pkey PRIMARY KEY (uid, gid, pid, db, tname);


--
-- Name: auth_group_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY auth_group
    ADD CONSTRAINT auth_group_pkey PRIMARY KEY (gid);


--
-- Name: auth_level_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY auth_level
    ADD CONSTRAINT auth_level_pkey PRIMARY KEY (lid);


--
-- Name: auth_member_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY auth_member
    ADD CONSTRAINT auth_member_pkey PRIMARY KEY (uid, gid);


--
-- Name: auth_priv_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY auth_priv
    ADD CONSTRAINT auth_priv_pkey PRIMARY KEY (pid);


--
-- Name: auth_right_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY auth_right
    ADD CONSTRAINT auth_right_pkey PRIMARY KEY (uid, lid);


--
-- Name: auth_special_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY auth_special
    ADD CONSTRAINT auth_special_pkey PRIMARY KEY (sid);


--
-- Name: auth_user_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY auth_user
    ADD CONSTRAINT auth_user_pkey PRIMARY KEY (uid);


--
-- Name: db_leer_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY db_leer
    ADD CONSTRAINT db_leer_pkey PRIMARY KEY (id);


--
-- Name: site_file_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY site_file
    ADD CONSTRAINT site_file_pkey PRIMARY KEY (fid);


--
-- Name: site_form_lang_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY site_form_lang
    ADD CONSTRAINT site_form_lang_pkey PRIMARY KEY (flid);


--
-- Name: site_form_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY site_form
    ADD CONSTRAINT site_form_pkey PRIMARY KEY (fid);


--
-- Name: site_lock_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY site_lock
    ADD CONSTRAINT site_lock_pkey PRIMARY KEY (lang, label, tname);


--
-- Name: site_menu_lang_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY site_menu_lang
    ADD CONSTRAINT site_menu_lang_pkey PRIMARY KEY (mlid);


--
-- Name: site_menu_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY site_menu
    ADD CONSTRAINT site_menu_pkey PRIMARY KEY (mid);


--
-- Name: site_menu_refid_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY site_menu
    ADD CONSTRAINT site_menu_refid_key UNIQUE (refid, mid);


--
-- Name: site_text_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY site_text
    ADD CONSTRAINT site_text_pkey PRIMARY KEY (lang, label, tname, version);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

