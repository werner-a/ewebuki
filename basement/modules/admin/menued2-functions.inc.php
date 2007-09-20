<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: menued-functions.inc.php 311 2005-03-12 21:46:39Z chaot $";
// "funktion loader";
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

    // funktion um eine sitemap zu erstellen
    if ( in_array("sitemap", $cfg["function"][$environment["kategorie"]]) ) {

        function sitemap($refid, $art = "", $modify = "", $self = "") {

            switch($art) {
                case menued:
                    $hidestatus = -1;
                    $sortinfo = -1;
                    $aktionlinks = -1;
                    break;
                case select:
                    $radiorefid = -1;
                    break;
                default:
            }

            global $cfg, $environment, $db, $pathvars, $specialvars, $rechte, $ast, $astpath, $buffer,$zaehler,$tiefe;
            $sql = "SELECT  ".$cfg["db"]["menu"]["entries"].".mid,
                            ".$cfg["db"]["menu"]["entries"].".entry,
                            ".$cfg["db"]["menu"]["entries"].".refid,
                            ".$cfg["db"]["menu"]["entries"].".level,
                            ".$cfg["db"]["menu"]["entries"].".sort,
                            ".$cfg["db"]["menu"]["entries"].".hide,
                            ".$cfg["db"]["lang"]["entries"].".lang,
                            ".$cfg["db"]["lang"]["entries"].".label,
                            ".$cfg["db"]["lang"]["entries"].".exturl
                      FROM  ".$cfg["db"]["menu"]["entries"]."
                INNER JOIN  ".$cfg["db"]["lang"]["entries"]."
                        ON  ".$cfg["db"]["menu"]["entries"].".mid = ".$cfg["db"]["lang"]["entries"].".mid
                     WHERE (".$cfg["db"]["menu"]["entries"].".refid=".$refid.")
                       AND (".$cfg["db"]["lang"]["entries"].".lang='".$environment["language"]."')
                  ORDER BY  ".$cfg["db"]["menu"]["order"].";";
            $result  = $db -> query($sql);
            $count = $db->num_rows($result);
            $zaehler++;

            while ( $array = $db -> fetch_array($result,1) ) {

                // beim moven ausblenden des zu verschiebenden ast
                if ( $art == "select" && $array["mid"] == $modify ) continue;

                // rechte !!!!!!
                if ( $array["level"] == "" ) {
                    $right = -1;
                } else {
                    if ( $rechte[$array["level"]] == -1 ) {
                        $right = -1;
                    } else {
                        $right = 0;
                    }
                }

                if ( $right == -1 ) {

                    // schaltflaechen erstellen
                    $aktion = "";
                    if ( is_array($modify) ) {
                        foreach($modify as $name => $value) {
                            if ( $name == "up" || $name == "down" ) {
                                if ( $array["refid"] == 0 ) {
                                    $ankerpos = "<a name=\"".$array["mid"]."\"</a>";
                                    $ankerlnk = "#".$array["mid"];
                                } else {
                                    #$anker   = "#".$ankerid;
                                    $ankerpos = "";
                                    $ankerlnk = "#".$ast[1];
                                }
                            } else {
                                $ankerlnk = "";
                            }
                            if ( $value[2] == "" || $rechte[$value[2]] == -1 ) {
                                $aktion .= "<a href=\"".$cfg["basis"]."/".$value[0].$name.",".$array["mid"].",".$array["refid"].".html".$ankerlnk."\"><img style=float:right src=\"".$cfg["iconpath"].$name.".png\" border=\"0\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></a>";
                                #$aktion .= "<a style=float:right href=\"".$cfg["basis"]."/".$value[0].$name.",".$array["mid"].",".$array["refid"].".html".$ankerlnk."\"><img src=\"".$cfg["iconpath"].$name.".png\" border=\"0\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></a>";
                            } else {
                                $aktion .= "<img src=\"".$cfg["iconpath"]."pos.png\" alt=\"\" width=\"24\" height=\"18\">";
                            }
                        }
                    }

                    // hauptpunkt fett
                    if ( $level == "" ) $array["label"] = "<b>".$array["label"]."</b>";

                    // hide status anzeigen
                    if ( $hidestatus != "" ) {
                        if ( $array["hide"] == -1 ) {
                            $hideimage = "cms-cb0.png";
                            $hidetext = "#(disabled)";
                        } else {
                            $hideimage = "cms-cb1.png";
                            $hidetext = "#(enabled)";
                        }
                    }

                    // wo geht der href hin?
                    if ( $array["exturl"] == "" ) {
                        $href = $array["entry"];
                        $extern = "";
                    } else {
                        $href = $array["exturl"];
                        $extern = " #(extern)";
                    }

                    if ( !isset($buffer[$refid]["zaehler"]) ) {
                        $tiefe++;
                        $buffer[$refid]["zaehler"] = $count;

                        if ( $zaehler == 1 ) { 
                            $tree .= "<ul>\n";
                        } else {
                            $tree .= "<ul class=\"menued\">\n";
                        }
                    }
                    $buffer["pfad"] .= "/".$href;

                    // refid radio button
                    if ( $radiorefid != "" ) {
                        $radiobutton = "<span style=left:-".(($tiefe-1)*20)."pt;position:relative><input type=\"radio\" name=\"refid\" value=\"".$array["mid"]."\" /></span>";
                    }

                    if ( $hidestatus != "" ) {
                        $hide = "<span style=left:-".(($tiefe-1)*20)."pt;position:relative><img src=\"".$cfg["iconpath"].$hideimage."\" border=\"0\" alt=\"".$hidetext."\" title=\"".$hidetext."\" width=\"13\" height=\"13\"></span>\n";
                    } else {
                        $hide = "";
                    }
                    if ( $sortinfo != "" ) {
                        $sort = "<span style=left:-".(($tiefe-1)*20)."pt;position:relative>".$array["sort"]."</span>";
                    } else {
                        $sort = "";
                    }

                    $tree .= "<li class=\"menued\">".$aktion.$ankerpos.$radiobutton."<a class=\"\" href=\"".$buffer["pfad"].".html\">".$array["label"]."</a>";

                    $tree .= sitemap($array["mid"], $art, $modify, -1);
                    $tree .= "</li>\n";

                    if ( isset($buffer[$refid]["zaehler"]) ) {
                        $buffer["pfad"] = substr($buffer["pfad"],0,strrpos($buffer["pfad"],"/"));
                        $buffer[$refid]["zaehler"] = $buffer[$refid]["zaehler"] -1;
                        if ( $buffer[$refid]["zaehler"] == 0 ) {
                            $tiefe--;
                            $tree .= "</ul>\n";
                        }
                    }

                }
            }
            if ( $self == "" ) {
                    if ( $art == "select" ) {
                       # $tree = "<tr>\n<td><input type=\"radio\" name=\"refid\" value=\"".$refid."\" />\n</td><td width=\"100%\">#(root)</td>\n</tr>\n".$tree;
                    }
                  #  $tree = "<table width=\"100%\">".$tree."</table>";
            }
            return $tree;
        }
    }

    // rekursive renumber funktion
    if ( in_array("renumber", $cfg["function"][$environment["kategorie"]]) ) {

        function renumber($mt, $mtl, $refid, $rekursiv=0) {
            global $environment, $debugging, $db;
            $sql = "SELECT  ".$mt.".mid
                      FROM  ".$mt."
                INNER JOIN  ".$mtl."
                        ON  ".$mt.".mid = ".$mtl.".mid
                     WHERE (".$mt.".refid=".$refid.")
                       AND (".$mtl.".lang='".$environment["language"]."')
                  ORDER BY sort, label;";
            $menuresult  = $db -> query($sql);
            while ( $menuarray = $db -> fetch_array($menuresult,1) ) {
                $sort += 10;
                $sql = "UPDATE ".$mt."
                           SET sort=".$sort."
                         WHERE mid='".$menuarray["mid"]."'";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $db -> query($sql);
                if ( $rekursiv == -1 ) renumber($mt, $mtl, $menuarray["mid"], -1);
            }
        }
    }


    // funktion um die ebene aus der refid zu erstellen
    if ( in_array("make_ebene", $cfg["function"][$environment["kategorie"]]) ) {

        function make_ebene($mid, $ebene="") {
            # call: make_ebene(refid);
            global $db, $cfg;
            $sql = "SELECT refid, entry
                    FROM ".$cfg["db"]["menu"]["entries"]."
                    WHERE mid='".$mid."'";
            $result = $db -> query($sql);
            $array = $db -> fetch_array($result,$nop);
            $ebene = "/".$array["entry"].$ebene;
            if ( $array["refid"] != 0 ) {
                $ebene = make_ebene($array["refid"],$ebene);
            }
            return $ebene;
        }
    }


    // funktion um den content unterhalb eine eintrags zu verschieben
    if ( in_array("update_tname", $cfg["function"][$environment["kategorie"]]) ) {

        function update_tname($refid, /*$new = "",*/ $suchmuster = "", $ersatz = "") {
            global $db, $cfg, $debugging, $ausgaben;
            $sql = "SELECT mid, refid, entry FROM ".$cfg["db"]["menu"]["entries"]." WHERE refid ='".$refid."'";
            $result = $db -> query($sql);
            while ( $data = $db -> fetch_array($result,1) ) {

                // aktuelle ebene suchen
                $ebene = make_ebene($data["refid"]);

                // eindeutiges suchmuster erstellen
                #if ( $suchmuster == "" ) {
                #    $suchmuster = $ebene;
                #    $ersatz = substr($ebene,0,strrpos($ebene,"/"))."/".$new;
                #}

                // alter tname
                if ( $ebene != "/" ) $extend = crc32($ebene).".";
                $old_tname = $extend.$data["entry"];
                #echo $ebene.":".$old_tname."<br>";

                // neuer tname
                $ebene = str_replace($suchmuster, $ersatz, $ebene);
                if ( $ebene != "/" ) $extend = crc32($ebene).".";
                $new_tname = $extend.$data["entry"];
                #echo $ebene.":".$new_tname."<br>";

                $sql = "UPDATE ".$cfg["db"]["text"]["entries"]."
                            SET tname = '".$new_tname."',
                                ebene = '".$ebene."',
                                kategorie = '".$data["entry"]."'
                            WHERE tname = '".$old_tname."'";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $subresult = $db -> query($sql);
                if ( !$subresult ) $ausgaben["form_error"] .= $db -> error("#(menu_error)<br />");

                // und das gleiche fuer alle unterpunkte
                update_tname($data["mid"], /*$new,*/ $suchmuster, $ersatz);
            }
        }
    }

    // funktion um zu pruefen, ob das feld extend in der lang tabelle existiert
    #if ( in_array("checkext", $cfg["function"][$environment["kategorie"]]) ) {

        function checkext() {
            global $db, $cfg;

            // extend - db test
            $sql = "select extend from ".$cfg["db"]["lang"]["entries"] ;
            $result = $db -> query($sql);
            return $result;
        }
    #}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
