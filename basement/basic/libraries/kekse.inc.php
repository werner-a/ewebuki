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

    86343 K�nigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** $script_name ** ]".$debugging["char"];

    // datenbank wechseln -> variablen in menuctrl.inc.php
    if ( $environment["fqdn"][0] == $specialvars["dyndb"] ) {
        $db->selectDb($specialvars["dyndb"],FALSE);
        $specialvars["rootname"] = $db->getDb();
    }

    
    // altes verhalten wiederherstellen
    $defaults["split"]["title"] == "" ? $defaults["split"]["title"] = " - " : NOP;
    $defaults["split"]["kekse"] == "" ? $defaults["split"]["kekse"] = " - " : NOP;
    $defaults["split"]["m1"] == "" ? $defaults["split"]["m1"] = " &middot; " : NOP ;
    $defaults["split"]["m2"] == "" ? $defaults["split"]["m2"] = " &middot; " : NOP ;
    $defaults["split"]["l1"] == "" ? $defaults["split"]["l1"] = " &middot; " : NOP ;
    $defaults["split"]["l2"] == "" ? $defaults["split"]["l2"] = " &middot; " : NOP ;        

    
    // dynamic style - db test/extension
    $sql = "select dynamiccss from site_".$mt ;
    $result = $db -> query($sql);
    if ( $result ) {
        #echo $db-> field_name($result,0);
        $dynamiccss =  "site_".$mt.".dynamiccss,";
    } else {
        unset($dynamiccss);
    }
    
    // dynamic bg - db test/extension
    $sql = "select dynamicbg from site_".$mt ;
    $result = $db -> query($sql);
    if ( $result ) {
        #echo $db-> field_name($result,0);
        $dynamicbg =  "site_".$mt.".dynamicbg,";
    } else {
        unset($dynamicbg);
    }

    $environment["kekse"] = "<a href=\"".$pathvars["virtual"]."/index.html\">".$specialvars["rootname"]."</a>";

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
        $sql = "SELECT site_".$mt.".mid, site_".$mt.".refid,  site_".$mt.".entry, site_".$mt.".defaulttemplate, ".$dynamiccss.$dynamicbg." site_".$mt."_lang.label FROM site_".$mt." INNER JOIN site_".$mt."_lang ON site_".$mt.".mid = site_".$mt."_lang.mid WHERE site_".$mt.".entry ".$search." AND site_".$mt."_lang.lang='".$environment["language"]."' AND site_".$mt.".refid = '".$refid."' ;";
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
                $specialvars["pagetitle"] .= $defaults["split"]["title"].$keksarray["label"];
                $environment["kekse"] .= $defaults["split"]["kekse"]."<a href=\"".$pathvars["virtual"].$path.".html\">".$keksarray["label"]."</a>";
            }

            // variables template laut menueintrag setzen
            $specialvars["default_template"] = $keksarray["defaulttemplate"];

            // variables css file - erweiterung laut menueintrag setzen
            if ( $keksarray["dynamiccss"] != "" ) {
                $specialvars["dynamic_css"] = $keksarray["dynamiccss"];
            }
            
            // variables bg bild - erweiterung laut menueintrag setzen
            if ( $keksarray["dynamicbg"] != "" ) {
                $specialvars["dynamic_bg"] = $keksarray["dynamicbg"];
            }          
            
            // navbar erstellen
            #$ausgaben["UP"] = "<a class=\"menu_punkte\" href=\"".$pathvars["virtual"].$back.".html\">Zur�ck</a>";
            $ausgaben["M1"] = "";
            $ausgaben["M2"] = "";
            $ausgaben["M3"] = crc32($path)." <a class=\"menu_punkte\" href=\"".$pathvars["virtual"].$back.".html\">Zur�ck</a>";

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
                        if ( $ausgaben["M1"] != "" ) $ausgaben["M1"] .= $defaults["split"]["m1"];
                        $ausgaben["M1"] .= "<a class=\"menu_punkte\" href=\"./".$navbararray["entry"].".html\">".$navbararray["label"]."</a>";

                        $ausgaben["L1"] .= $defaults["split"]["l1"]."<a class=\"menu_punkte\" href=\"./".$navbararray["entry"].".html\">".$navbararray["label"]."</a><br>";
                    }
                }

                // $lnk_0 mit back link belegen
                $lnkcount = 0;
                $lnk[$lnkcount] = $ausgaben["UP"];

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
                        if ( $ausgaben["M2"] != "" ) $ausgaben["M2"] .=$defaults["split"]["m2"] ;
                        $ausgaben["M2"] .= "<a class=\"menu_punkte\" href=\"".$pathvars["virtual"].$path."/".$navbararray["entry"].".html\">".$navbararray["label"]."</a>";

                        $ausgaben["L2"] .= $defaults["split"]["l2"]."<a class=\"menu_punkte\" href=\"".$pathvars["virtual"].$path."/".$navbararray["entry"].".html\">".$navbararray["label"]."</a><br>";

                        // $lnk_* mit links belegen
                        $lnkcount++;
                        $lnk[$lnkcount] = $pathvars["virtual"].$path."/".$navbararray["entry"].".html";
                    }
                }
            }
        }
    }

    // zur�ck zur haupdatenbank
    $db -> selectDb(DATABASE,FALSE);

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ $script_name ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
