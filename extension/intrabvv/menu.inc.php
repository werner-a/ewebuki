<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script_name = "$Id$";
  $Script_desc = "menu generieren - 3 stufen ";
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

    #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** $script_name ** ]".$debugging["char"];

    // mandatory

    if ( $cfg["menu"]["level1"]["full"] == "-1" ) {
        $mandatory = "";
    } else {
        $mandatory = " AND ((".$cfg["menu"]["db"]["entries"].".mandatory)='-1')";
    }

    // menupunkte level 1
    $sql = "SELECT ".$cfg["menu"]["db"]["entries"].".mid, ".$cfg["menu"]["db"]["entries"].".entry, ".$cfg["menu"]["db"]["entries"].".refid, ".$cfg["menu"]["db"]["entries"].".level, ".$cfg["menu"]["db"]["entries"]."_lang.lang, ".$cfg["menu"]["db"]["entries"]."_lang.label, ".$cfg["menu"]["db"]["entries"]."_lang.exturl FROM ".$cfg["menu"]["db"]["entries"]." INNER JOIN ".$cfg["menu"]["db"]["entries"]."_lang ON ".$cfg["menu"]["db"]["entries"].".mid = ".$cfg["menu"]["db"]["entries"]."_lang.mid WHERE (((".$cfg["menu"]["db"]["entries"].".refid)=0) AND ((".$cfg["menu"]["db"]["entries"]."_lang.lang)='".$environment["language"]."') ".$mandatory.") order by sort, label;";

    $level1result  = $db -> query($sql);

    #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "level0res: ".$submenuresult.$debugging["char"];

    // entweder alle in anderer sprache oder nichts
    #if ( $db -> num_rows($level0result) == 0 ){
    #    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "Language: \"".$environment["language"]."\" for menu not found using default: \"".$specialvars["default_language"]."\"".$debugging["char"];
    #    $sql = "SELECT ".$cfg["menu"]["db"]["entries"].".mid, ".$cfg["menu"]["db"]["entries"].".entry, ".$cfg["menu"]["db"]["entries"].".refid, ".$cfg["menu"]["db"]["entries"].".level, ".$cfg["menu"]["db"]["language"].".lang, ".$cfg["menu"]["db"]["language"].".label, ".$cfg["menu"]["db"]["language"].".exturl FROM ".$cfg["menu"]["db"]["entries"]." INNER JOIN ".$cfg["menu"]["db"]["language"]." ON ".$cfg["menu"]["db"]["entries"].".mid = ".$cfg["menu"]["db"]["language"].".mid WHERE (((".$cfg["menu"]["db"]["entries"].".refid)=0) AND ((".$cfg["menu"]["db"]["language"].".lang)='".$specialvars["default_language"]."')) order by sort;";
    #    $mainmenuresult  = $db -> query($sql);
    #}

    while ( $level1array = $db -> fetch_array($level1result,1) ) {
        if ( $cfg["menu"]["level1"]["enable"] == -1 ) {
            if ( $level1array["level"] == "" ) {
                $right = -1;
                $parser = -1;
            } else {
                if ( $rechte[$level1array["level"]] == -1 ) {
                    $right = -1;
                    $parser = -1;
                } else {
                    $right = 0;
                    $parser = 0;
                }
            }
            if ( $right == -1 ) {
                // mandatory
                $mandatory = " AND ((".$cfg["menu"]["db"]["entries"].".mandatory)='-1')";
                if ( $cfg["menu"]["level1"]["force"] == -1 ) $mandatory = "";
                #if ( strpos($environment["ebene"],$level1array["entry"]) == 1 || ( $environment["kategorie"] == $level1array["entry"] && $environment["ebene"] == "" ) ) {
                $aktdb = $db->getDb();
                if ( $environment["fqdn"][0] == "www" ) {
                    $aktlev = DATABASE;
                } else {
                    $aktlev = $environment["fqdn"][0];
                }
                if ( strpos($environment["ebene"],$level1array["entry"]) == 1 && $aktlev == $aktdb || ( $environment["kategorie"] == $level1array["entry"] && $environment["ebene"] == "" && $aktlev == $aktdb ) ) {
                    // ???open.png
                    $ausgaben["ordner"] = "<a class=\"".$cfg["menu"]["level1"]["style"]."\" href=\"".$cfg["menu"]["fqdn"].$pathvars["virtual"]."/".$level1array["entry"].".html\"><img src=\"../../images/".$environment["design"]."/".$cfg["menu"]["image"]."open.png\" width=\"16\" height=\"13\" align=\"absbottom\" border=\"0\" alt=\"".$level1array["label"]."\"></a>";
                    // mandatory
                    if ( $cfg["menu"]["level2"]["full"] == -1 ) $mandatory = "";
                    if ( $cfg["menu"]["level2"]["dynamic"] == -1 ) $cfg["menu"]["level2"]["enable"] = -1;
                } else {
                    // ???close.png
                    $ausgaben["ordner"] = "<a class=\"".$cfg["menu"]["level1"]["style"]."\" href=\"".$cfg["menu"]["fqdn"].$pathvars["virtual"]."/".$level1array["entry"].".html\"><img src=\"../../images/".$environment["design"]."/".$cfg["menu"]["image"]."close.png\" width=\"16\" height=\"13\" align=\"absbottom\" border=\"0\" alt=\"".$level1array["label"]."\"></a>";
                    if ( $cfg["menu"]["level2"]["dynamic"] == -1 ) $cfg["menu"]["level2"]["enable"] = 0;
                }
                if ( $level1array["entry"] != "" ) {
                    $ausgaben["ueberschrift"] = "<a class=\"".$cfg["menu"]["level1"]["style"]."\" href=\"".$cfg["menu"]["fqdn"].$pathvars["virtual"]."/".$level1array["entry"].".html\">".$level1array["label"]."</a><br>";
                } else {
                    #$ausgaben["ueberschrift"] = "<a class=\"".$cfg["menu"]["level1"]["style"]."\" target=\"_blank\" href=\"".$level1array["exturl"]."\">".$level1array["label"]."</a><br>";
                    $ausgaben["ueberschrift"] = "<a class=\"".$cfg["menu"]["level1"]["style"]."\" href=\"".$level1array["exturl"]."\">".$level1array["label"]."</a><br>";
                }
            }
        } else {
            $ausgaben["ordner"] = "";
            $ausgaben["ueberschrift"] = "";
        }


        // menupunkte level 2
        $sql = "SELECT ".$cfg["menu"]["db"]["entries"].".mid, ".$cfg["menu"]["db"]["entries"].".entry, ".$cfg["menu"]["db"]["entries"].".refid, ".$cfg["menu"]["db"]["entries"].".sort, ".$cfg["menu"]["db"]["entries"].".level, ".$cfg["menu"]["db"]["language"].".lang, ".$cfg["menu"]["db"]["language"].".label, ".$cfg["menu"]["db"]["language"].".exturl FROM ".$cfg["menu"]["db"]["entries"]." INNER JOIN ".$cfg["menu"]["db"]["language"]." ON ".$cfg["menu"]["db"]["entries"].".mid = ".$cfg["menu"]["db"]["language"].".mid WHERE (((".$cfg["menu"]["db"]["entries"].".refid)=".$level1array["mid"].") AND ((".$cfg["menu"]["db"]["language"].".lang)='".$environment["language"]."')".$mandatory.") order by sort, label;";
        #echo $sql;
        #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "level1sql: ".$sql.$debugging["char"];
        $level2result = $db -> query($sql);
        #echo $db->getDb();
        #echo $sql."<br>";

        #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "level1res: ".$submenuresult.$debugging["char"];

        // entweder alle in anderer sprache oder nichts
        #if ( $db -> num_rows($level1result) == 0 ){
        #    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "Language: \"".$environment["language"]."\" for submenu not found using default: \"".$specialvars["default_language"]."\"".$debugging["char"];
        #    $sql = "SELECT ".$cfg["menu"]["db"]["entries"].".mid, ".$cfg["menu"]["db"]["entries"].".entry, ".$cfg["menu"]["db"]["entries"].".refid, ".$cfg["menu"]["db"]["entries"].".level, ".$cfg["menu"]["db"]["language"].".lang, ".$cfg["menu"]["db"]["language"].".label, ".$cfg["menu"]["db"]["language"].".exturl FROM ".$cfg["menu"]["db"]["entries"]." INNER JOIN ".$cfg["menu"]["db"]["language"]." ON ".$cfg["menu"]["db"]["entries"].".mid = ".$cfg["menu"]["db"]["language"].".mid WHERE (((".$cfg["menu"]["db"]["entries"].".refid)=".$level1array["mid"].") AND ((".$cfg["menu"]["db"]["language"].".lang)='".$specialvars["default_language"]."')) order by sort;";
        #    $submenuresult  = $db -> query($sql);
        #}

        $ausgaben["punkte"] = "";
        while ( $level2array = $db -> fetch_array($level2result,$nop) ) {


            if ( $cfg["menu"]["level2"]["enable"] == -1 ) {
                if ( $level2array["level"] == "" ) {
                    $right = -1;
                } else {
                    if ( $rechte[$level2array["level"]] == -1 ) {
                        $right = -1;
                    } else {
                        $right = 0;
                    }
                }
                if ( $right == -1 ) {
                    if ( $level2array["entry"] != "" ) {
                        // mandatory
                        $mandatory = " AND ((".$cfg["menu"]["db"]["entries"].".mandatory)='-1')";
                        if ( $cfg["menu"]["level1"]["force"] == -1 ) $mandatory = "";
                        if ( strstr($environment["ebene"],"/".$level1array["entry"]."/".$level2array["entry"]) || strstr($environment["kategorie"],$level2array["entry"]) ) {
                            // mandatory
                            if ( $cfg["menu"]["level3"]["full"] == -1 ) $mandatory = "";
                            if ( $cfg["menu"]["level3"]["dynamic"] == -1 ) $cfg["menu"]["level3"]["enable"] = -1;
                        } else {
                            if ( $cfg["menu"]["level3"]["dynamic"] == -1 ) $cfg["menu"]["level3"]["enable"] = 0;
                        }
                       $ausgaben["punkte"] .= "<a class=\"".$cfg["menu"]["level2"]["style"]."\" href=\"".$cfg["menu"]["fqdn"].$pathvars["virtual"]."/".$level1array["entry"]."/".$level2array["entry"].".html\">".$level2array["label"]."</a><br>";
                    } else {
                       #$ausgaben["punkte"] .= "<a class=\"".$cfg["menu"]["level2"]["style"]."\" target=\"_blank\" href=\"".$level2array["exturl"]."\">".$level2array["label"]."</a><br>";
                       $ausgaben["punkte"] .= "<a class=\"".$cfg["menu"]["level2"]["style"]."\" href=\"".$level2array["exturl"]."\">".$level2array["label"]."</a><br>";
                    }
                }
            }

            // menupunkte level 3
            if ( strstr($environment["ebene"],"/".$level1array["entry"]) || strstr($environment["kategorie"],$level1array["entry"]) ) {
                $sql = "SELECT ".$cfg["menu"]["db"]["entries"].".mid, ".$cfg["menu"]["db"]["entries"].".entry, ".$cfg["menu"]["db"]["entries"].".refid, ".$cfg["menu"]["db"]["entries"].".sort, ".$cfg["menu"]["db"]["entries"].".level, ".$cfg["menu"]["db"]["language"].".lang, ".$cfg["menu"]["db"]["language"].".label, ".$cfg["menu"]["db"]["language"].".exturl FROM ".$cfg["menu"]["db"]["entries"]." INNER JOIN ".$cfg["menu"]["db"]["language"]." ON ".$cfg["menu"]["db"]["entries"].".mid = ".$cfg["menu"]["db"]["language"].".mid WHERE (((".$cfg["menu"]["db"]["entries"].".refid)=".$level2array["mid"].") AND ((".$cfg["menu"]["db"]["language"].".lang)='".$environment["language"]."')".$mandatory.") order by sort, label;";
                #echo "hier";
                #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "level2sql: ".$sql.$debugging["char"];
                $level3result = $db -> query($sql);

                #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "level2res: ".$submenuresult.$debugging["char"];

                #if ( $db -> num_rows($level1result) == 0 ){
                #    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "Language: \"".$environment["language"]."\" for submenu not found using default: \"".$specialvars["default_language"]."\"".$debugging["char"];
                #    $sql = "SELECT ".$cfg["menu"]["db"]["entries"].".mid, ".$cfg["menu"]["db"]["entries"].".entry, ".$cfg["menu"]["db"]["entries"].".refid, ".$cfg["menu"]["db"]["entries"].".level, ".$cfg["menu"]["db"]["language"].".lang, ".$cfg["menu"]["db"]["language"].".label, ".$cfg["menu"]["db"]["language"].".exturl FROM ".$cfg["menu"]["db"]["entries"]." INNER JOIN ".$cfg["menu"]["db"]["language"]." ON ".$cfg["menu"]["db"]["entries"].".mid = ".$cfg["menu"]["db"]["language"].".mid WHERE (((".$cfg["menu"]["db"]["entries"].".refid)=".$level2array["mid"].") AND ((".$cfg["menu"]["db"]["language"].".lang)='".$specialvars["default_language"]."')) order by sort;";
                #    $submenuresult  = $db -> query($sql);
                #}

                while ( $level3array = $db -> fetch_array($level3result,$nop) ) {


                    if ( $cfg["menu"]["level3"]["enable"] == -1 ) {
                        if ( $level3array["level"] == "" ) {
                            $right = -1;
                        } else {
                            if ( $rechte[$level3array["level"]] == -1 ) {
                                $right = -1;
                            } else {
                                $right = 0;
                            }
                        }
                        if ( $right == -1 ) {
                            if ( $level3array["entry"] != "" ) {
                               $ausgaben["punkte"] .= "<img src=\"../../images/".$environment["design"]."/menu.png\" width=\"2\" height=\"12\" align=\"absbottom\"> <a class=\"".$cfg["menu"]["level3"]["style"]."\" href=\"".$cfg["menu"]["fqdn"].$pathvars["virtual"]."/".$level1array["entry"]."/".$level2array["entry"]."/".$level3array["entry"].".html\">".$level3array["label"]."</a><br>";
                            } else {
                               #$ausgaben["punkte"] .= "<img src=\"../../images/".$environment["design"]."/menu.png\" width=\"2\" height=\"12\" align=\"absbottom\"> <a class=\"".$cfg["menu"]["level3"]["style"]."\" target=\"_blank\" href=\"".$level3array["exturl"]."\">".$level3array["label"]."</a><br>";
                               $ausgaben["punkte"] .= "<img src=\"../../images/".$environment["design"]."/menu.png\" width=\"2\" height=\"12\" align=\"absbottom\"> <a class=\"".$cfg["menu"]["level3"]["style"]."\" href=\"".$level3array["exturl"]."\">".$level3array["label"]."</a><br>";
                            }
                        }
                    }
                }
            }

        }


        if ( $cfg["menu"]["level1"]["enable"] == -1 && $parser == -1 ) $ausgaben[$cfg["menu"]["name"]] .= parser( $cfg["menu"]["name"], "", $parse_find, $parse_put);
    }

    #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ $script_name ++ ]".$debugging["char"];
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
