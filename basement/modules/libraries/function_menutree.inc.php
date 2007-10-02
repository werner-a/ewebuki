<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: function_menutree.inc.php 311 2005-03-12 21:46:39Z chaot $";
// "menubaum bauen";
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

        global $cfg, $environment, $db, $pathvars, $specialvars, $rechte, $ast, $astpath, $buffer,$positionArray;
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

        while ( $array = $db -> fetch_array($result,1) ) {
            if ( $art == "menued" ) {
                if ( $environment["parameter"][1] == "modern" ) {
                    // der gesamte pfad muss hierein !
                    if ( in_array($array["mid"],$positionArray) || in_array($array["refid"],$positionArray)  ) {
                        // punkt vom pfad die nicht angezeigt werden sollen
                        // ausnahme ist hier die uebersichtsseite
                        if ( $array["refid"] != $_GET["id"] ) {
                            if ( $_GET["id"] != "" || $array["refid"] != 0 ) {
                                $buffer[$refid]["display"] = "none";
                            } 
                        } 
                    } else {
                        continue;
                    }
                } else {
                    // wenn punkt nicht im array dann nicht anzeigen !
                    if ( $refid != 0 && !in_array($refid,$positionArray) ) {
                        continue;
                    }
                }

                // schauen ob unterpunkte vorhanden !
                $sql = "SELECT * FROM site_menu where refid=".$array["mid"];
                $result_in  = $db -> query($sql);
                $count_in = $db->num_rows($result_in);

                // sind unterpunkte vorhanden + einblenden
                if  ( $count_in > 0 ) {
                    $plus = "<a class=\"\" href=\"?id=".$array["mid"]."\"> +</a>";
                } else {
                    $plus = "";
                }
            }

            // aufbau des pfads
            $buffer["pfad"] .= "/".$array["entry"];

            // kategorie u. ebene herausfinden
            $kategorie2check = substr($buffer["pfad"],0,strpos($buffer["pfad"],"/"));
            $ebene2check = substr($buffer["pfad"],strpos($buffer["pfad"],"/"));

            // hier findet der rechte-check statt
            if ( right_check("-1",$ebene2check,$kategorie2check != "") || $rechte[$cfg["right_admin"]] == -1 ) {
                $right = -1;
            } else {
                $right = "";
            }

            // schaltflaechen erstellen
            if ( $right == -1 ) {
                $aktion = "";
                if ( is_array($modify) ) {
                    foreach($modify as $name => $value) {
                        if ( $name == "up" || $name == "down" ) {
                            if ( $array["refid"] == 0 ) {
                                $ankerpos = "<a name=\"".$array["mid"]."\"></a>";
                                $ankerlnk = "#".$array["mid"];
                            } else {
                                $ankerpos = "";
                                $ankerlnk = "#".$ast[1];
                            }
                        } else {
                            $ankerlnk = "";
                        }
                        if ( $value[2] == "" || $rechte[$value[2]] == -1 ) {
                            $aktion .= "<a href=\"".$cfg["basis"]."/".$value[0].$name.",".$array["mid"].",".$array["refid"].".html".$ankerlnk."\"><img style=\"float:right\" src=\"".$cfg["iconpath"].$name.".png\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></img></a>";
                        } else {
                            $aktion .= "<img src=\"".$cfg["iconpath"]."pos.png\" alt=\"\" width=\"24\" height=\"18\">";
                        }
                    }
                }
            }

            // hauptpunkt fett
            if ( $refid == 0 ) $array["label"] = "<b>".$array["label"]."</b>";

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
                $href = $buffer["pfad"].".html";
                $extern = "";
            } else {
                $href = $array["exturl"];
                $extern = " #(extern)";
            }

            // in den buffer schreiben wieviel unterpunkte fuer jeweiligen überpunkt vorhanden sind !
            if ( !isset($buffer[$refid]["zaehler"]) ) {
                $tiefe++;
                $buffer[$refid]["zaehler"] = $count;

                if ( $buffer[$refid]["display"] != "none" ) {
                    // beim ersten aufruf eine class menued setzen
                    if ( $self == "" ) {
                        $tree .= "<ul class=\"menued\">\n";
                    } else {
                        $tree .= "<ul>\n";
                    }
                }
            }

            // refid radio button
            if ( $radiorefid != "" ) {
                $radiobutton = "<input type=\"radio\" name=\"refid\" value=\"".$array["mid"]."\" />";
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

            if ( $buffer[$refid]["display"] != "none" ) {
                $tree .= "<li>".$aktion.$ankerpos.$radiobutton."<a class=\"\" href=\"".$href."\">".$array["label"]."</a>".$plus."\n";
            }
            $tree .= sitemap($array["mid"], $art, $modify, -1);

            if ( $buffer[$refid]["display"] != "none" ) {
                $tree .= "</li>\n";
            }
            if ( isset($buffer[$refid]["zaehler"]) ) {
                $buffer["pfad"] = substr($buffer["pfad"],0,strrpos($buffer["pfad"],"/"));
                $buffer[$refid]["zaehler"] = $buffer[$refid]["zaehler"] -1;
                if ( $buffer[$refid]["zaehler"] == 0 ) {
                    $tree .= "</ul>\n";
                }
            }
        }
        if ( $self == "" ) {
                if ( $art == "select" ) {
                    $tree = "<ul><li class=\"menued\"><input type=\"radio\" name=\"refid\" value=\"".$refid."\" />\n</li><li>#(root)</li></ul>".$tree;
                }
        }
        return $tree;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
