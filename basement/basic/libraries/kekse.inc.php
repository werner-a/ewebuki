<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $script_name = "$Id$";
    $Script_desc = "kekse erstellen";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001, 2002, 2003 Werner Ammon <wa@chaos.de>

    This script is a part of eWeBuKi

    eWeBuKi is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    eWeBuKi is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with eWeBuKi; If you did not, you may download a copy at:

    URL:  http://www.gnu.org/licenses/gpl.txt

    You may also request a copy from:

    Free Software Foundation, Inc.
    59 Temple Place, Suite 330
    Boston, MA 02111-1307
    USA

    You may contact the author/development team at:

    Chaos Networks
    c/o Werner Ammon
    Lerchenstr. 11c

    86343 Königsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** $script_name ** ]".$debugging["char"];

    // datenbank wechseln -> variablen in menuctrl.inc.php
    if ( $environment["fqdn"][0] == $specialvars["dyndb"] ) {
        $db->selectDb($specialvars["dyndb"],FALSE);
        $specialvars["rootname"] = $db->getDb();
    }

    $environment["kekse"] = "<a href=\"".$pathvars["virtual"]."/\">".$specialvars["rootname"]."</a>";

    // special eintraege markieren
    #$special = array( "list", "details", "modify", "start" );
    #if ( in_array($environment["kategorie"], $special) ) {
    #    $sign = "%";
    #} else {
        $sign = "/";
    #}

    // kekspath splitten und fuer jede ebene die beschreibung holen
    $kekspath = substr( $environment["ebene"].$sign.$environment["kategorie"], 1);
    $kekspath = explode('/', $kekspath);

    // reset refid, um im "/" anzufangen
    $refid = 0;
    foreach ($kekspath as $key => $value) {
        // makierte eintraege aendern
        #if ( strstr($value, "/") ) {
        #    $search = "like '".substr($value, 0, strpos($value, "/"))."%'";
        #} else {
        #    $search = "= '".$value."'";
        #}

        $search = "like '".$value."%'";
        $sql = "SELECT site_".$mt.".mid, site_".$mt.".refid,  site_".$mt.".entry, site_".$mt.".defaulttemplate, site_".$mt."_lang.label FROM site_".$mt." INNER JOIN site_".$mt."_lang ON site_".$mt.".mid = site_".$mt."_lang.mid WHERE site_".$mt.".entry ".$search." AND site_".$mt."_lang.lang='".$environment["language"]."' AND site_".$mt.".refid = '".$refid."' ;";
        #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
        $keksresult = $db -> query($sql);


        if ( $db -> num_rows($keksresult) == 1 ) {
            $keksarray  = $db -> fetch_array($keksresult,1);

            // refid setzen um richtigen eintrag zu finden
            $refid = $keksarray["mid"];
            // seitentitel und kekse zusammensetzen
            if ( $keksarray["entry"] != "" ) {

                // navbar links
                if ( $path == "" ) {
                    $ausgaben["UP"] = $pathvars["virtual"]."/index.html";
                } else {
                    $ausgaben["UP"] = $pathvars["virtual"].$path.".html";
                }

                $path .= "/".$keksarray["entry"];
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "path: ".$path.$debugging["char"];
                $specialvars["pagetitle"] .= " - ".$keksarray["label"];
                $environment["kekse"] .= " - "."<a href=\"".$pathvars["virtual"].$path.".html\">".$keksarray["label"]."</a>";
            }

            // variables template laut menueintrag setzen
            $specialvars["default_template"] = $keksarray["defaulttemplate"];

            // navbar erstellen
            #$ausgaben["UP"] = "<a class=\"menu_punkte\" href=\"".$pathvars["virtual"].$back.".html\">Zurück</a>";
            $ausgaben["M1"] = "";
            $ausgaben["M2"] = "";
            $ausgaben["M3"] = crc32($path)." <a class=\"menu_punkte\" href=\"".$pathvars["virtual"].$back.".html\">Zurück</a>";

            if ( $path.".html" == $environment["ebene"]."/".$environment["kategorie"].".html" ) {
                $sql = "SELECT site_menu.entry, site_menu.refid, site_menu.level, site_menu_lang.lang, site_menu_lang.label, site_menu_lang.exturl FROM site_menu INNER JOIN site_menu_lang ON site_menu.mid = site_menu_lang.mid WHERE (((site_menu.refid)=".$keksarray["refid"].") AND ((site_menu_lang.lang)='".$environment["language"]."')) order by sort;";
                $navbarresult  = $db -> query($sql);
                while ( $navbararray = $db -> fetch_array($navbarresult,1) ) {
                    if ( $navbararray["level"] == "" ) {
                        $right = -1;
                    } else {
                        if ( $rechte[$navbararray["level"]] == -1 ) {
                            $right = -1;
                        } else {
                            $right = 0;
                        }
                    }

                    if ( $right == -1 ) {
                        if ( $ausgaben["M1"] != "" ) $ausgaben["M1"] .= " &middot; ";
                        $ausgaben["M1"] .= "<a class=\"menu_punkte\" href=\"./".$navbararray["entry"].".html\">".$navbararray["label"]."</a>";

                        $ausgaben["L1"] .= "&middot; <a class=\"menu_punkte\" href=\"./".$navbararray["entry"].".html\">".$navbararray["label"]."</a><br>";
                    }
                }
                $sql = "SELECT site_menu.entry, site_menu.refid, site_menu.level, site_menu_lang.lang, site_menu_lang.label, site_menu_lang.exturl FROM site_menu INNER JOIN site_menu_lang ON site_menu.mid = site_menu_lang.mid WHERE (((site_menu.refid)=".$keksarray["mid"].") AND ((site_menu_lang.lang)='".$environment["language"]."')) order by sort;";
                $navbarresult  = $db -> query($sql);
                while ( $navbararray = $db -> fetch_array($navbarresult,1) ) {
                    if ( $navbararray["level"] == "" ) {
                        $right = -1;
                    } else {
                        if ( $rechte[$navbararray["level"]] == -1 ) {
                            $right = -1;
                        } else {
                            $right = 0;
                        }
                    }

                    if ( $right == -1 ) {
                        if ( $ausgaben["M2"] != "" ) $ausgaben["M2"] .= " &middot; ";
                        $ausgaben["M2"] .= "<a class=\"menu_punkte\" href=\"".$pathvars["virtual"].$path."/".$navbararray["entry"].".html\">".$navbararray["label"]."</a>";

                        $ausgaben["L2"] .= "&middot; <a class=\"menu_punkte\" href=\"".$pathvars["virtual"].$path."/".$navbararray["entry"].".html\">".$navbararray["label"]."</a><br>";
                    }
                }
            }
        }
    }

    // zurück zur haupdatenbank
    $db -> selectDb(DATABASE,FALSE);

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ $script_name ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
