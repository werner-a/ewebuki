<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: function_menutree.inc.php 311 2005-03-12 21:46:39Z chaot $";
// "menubaum bauen";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2007 Werner Ammon ( wa<at>chaos.de )

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
        global $design,$opentree,$treelink,$ausgaben,$cfg, $environment, $db, $pathvars, $specialvars, $rechte, $ast, $astpath, $buffer,$positionArray;


        switch($art) {
            case menued:
                $flapmenu = -1;
                $aktionlinks = -1;
                $hidestatus = -1;
                $sortinfo = -1;
                break;
            case select:
                $flapmenu = -1;
                $radiorefid = -1;
                $hidestatus = -1;
                break;
            case sitemap:
                break;
            default:

        }

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

            // aufbau des pfads
            if ( $refid == 0 || in_array($refid,$positionArray) ) {
                $buffer["pfad"] .= "/".$array["entry"];
            }

            // hide-status signalisieren
            $class_hide = "\"\"";
            if ( $hidestatus == -1 ) {
                if ( $array["hide"] == -1 ) {
                    $class_hide = "\"red\"";
                }
            }

            // menu-aufbau ala konqueror oder zum klappen
            if ( $flapmenu == -1) {
                // alle punkte die nicht im array sind nicht anzeigen
                if ( $refid != 0 && !in_array($refid,$positionArray) ) {
                    continue;
                } else {
                    // menu auf werner-art, hier auch noch den gesamten ast ausblenden !
                    // nur noch die mit der refid laut $_SESSION
                    if ( $design == "modern" ) {
                        if ( $array["refid"] != $_SESSION["menued_id"] ) {
                            if ( $_SESSION["menued_id"] != "" || $array["refid"] != 0 ) {
                                $buffer[$refid]["display"] = "none";
                            }
                        }

                        // back-link bauen
                        if ( $array["mid"] == $_SESSION["menued_id"] ) {
                            $ausgaben["path"] = $buffer["pfad"];
                            if ( $array["refid"] == 0 ) {
                                $ausgaben["back"] = "<a href=\"".$cfg["basis"]."/".$environment["parameter"][0].".html\">zurück</a>";
                            } else {
                                $ausgaben["back"] = "<a href=\"".$cfg["basis"]."/".$environment["parameter"][0].",".$array["refid"].".html\">zurück</a>";
                            }
                        }
                    }
                }

                // schauen ob unterpunkte vorhanden !
                $sql = "SELECT * FROM ".$cfg["db"]["menu"]["entries"]." WHERE refid=".$array["mid"];
                $result_in  = $db -> query($sql);
                $count_in = $db->num_rows($result_in);

                // sind unterpunkte vorhanden + oder - einblenden
                if  ( $count_in > 0 ) {
                    $copy = $positionArray;
                    array_shift($copy);

                    if ( $environment["parameter"][2] != "" ) {
                        $move_para = ",".$environment["parameter"][2];
                    } else {
                        $move_para = "";
                    }
                    ( is_array($opentree) && in_array($array["mid"],$opentree) ) ? $sign = "-" : $sign = "+";
                    $href = "<a class=".$class_hide." href=\"".$cfg["basis"]."/".$environment["parameter"][0].",".$array["mid"].$move_para.".html\">".$array["label"]."+</a>"."\n";
                } else {
                    $href = "<span class=".$class_hide.">".$array["label"]."</span>";
                }
            // hier wird komplett geoeffnet
            } else {
                $href = $array["label"];
            }

            // schaltflaechen erstellen
            if ( $aktionlinks == -1) {
                // kategorie u. ebene herausfinden
                $kategorie2check = substr($buffer["pfad"],0,strpos($buffer["pfad"],"/"));
                $ebene2check = substr($buffer["pfad"],strpos($buffer["pfad"],"/"));

                // hier findet der rechte-check statt
                if ( right_check("-1",$ebene2check,$kategorie2check != "") || $rechte[$cfg["right_admin"]] == -1 ) {
                    $right = -1;
                } else {
                    $right = "";
                }

                if ( $right == -1 ) {
                    $aktion = "";
                    if ( is_array($modify) ) {
                        foreach($modify as $name => $value) {
                            // anzeige der sortierung
                            if ( $sortinfo != "" ) {
                                if ( $name == "sort") {
                                    $aktion .= "<span title=\"".$value[1]."\" style=\"float:right\">(".$array["sort"].")</span>";
                                    continue;
                                }
                            }
                            // anzeige des icons zur content-seite
                            if ( $name == "jump" ) {
                                $aktion .= "<a href=\"".$pathvars["virtual"].$buffer["pfad"].".html".$ankerlnk."\"><img style=\"float:right\" src=\"".$cfg["iconpath"].$name.".png\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></img></a>";
                                continue;
                            }
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
                            // je nach recht icon anzeigen !
                            if ( $value[2] == "" || $rechte[$value[2]] == -1 ) {
                                if ( $name == "move" ) {
                                    $aktion .= "<a href=\"".$cfg["basis"]."/".$value[0].$name.",0,".$array["mid"].".html".$ankerlnk."\"><img style=\"float:right\" src=\"".$cfg["iconpath"].$name.".png\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></img></a>";
                                } else {
                                    $aktion .= "<a href=\"".$cfg["basis"]."/".$value[0].$name.",".$array["mid"].",".$array["refid"].".html".$ankerlnk."\"><img style=\"float:right\" src=\"".$cfg["iconpath"].$name.".png\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></img></a>";
                                }
                            } else {
                                $aktion .= "<img src=\"".$cfg["iconpath"]."pos.png\" alt=\"\" width=\"24\" height=\"18\">";
                            }
                        }
                    }
                }
            }

            // wo geht der href hin?
            if ( $array["exturl"] != "" ) {
                $href = "<a class=".$class_hide." href=".$array["exturl"].">".$array["label"]."</a>"."\n";
            }

            // in den buffer schreiben wieviel unterpunkte fuer jeweiligen überpunkt vorhanden sind !
            if ( !isset($buffer[$refid]["zaehler"]) ) {
                $buffer[$refid]["zaehler"] = $count;

                if ( $buffer[$refid]["display"] != "none" ) {
                    // beim ersten aufruf eine class menued setzen
                    if ( $self == "" ) {
                        $tree .= "<ul class=\"menued\">\n";
                    } else {
                        if ( $design == "modern" ) {
                            $tree .= "<ul class=\"menued\">\n";
                        } else {
                            $tree .= "<ul>\n";
                        }
                    }
                }
            }

            // refid radio button
            if ( $radiorefid != "" ) {
                $radiobutton = "<input type=\"radio\" name=\"refid\" value=\"".$array["mid"]."\" />";
            }

            // listenpunkt schreiben
            if ( $buffer[$refid]["display"] != "none" ) {
                $tree .= "<li>".$aktion.$ankerpos.$radiobutton.$href;
            }

            // funktionsaufruf
            $tree .= sitemap($array["mid"], $art, $modify, -1);

            // abschliessendes li anbringen
            if ( $buffer[$refid]["display"] != "none" ) {
                $tree .= "</li>\n";
            }

            // abschliessendes ul anbringen u. pfad kuerzen
            if ( isset($buffer[$refid]["zaehler"]) ) {
                // pfad kürzen
                $buffer["pfad"] = substr($buffer["pfad"],0,strrpos($buffer["pfad"],"/"));
                // zaehler 1 zurücksetzen
                $buffer[$refid]["zaehler"] = $buffer[$refid]["zaehler"] -1;
                // ul anbringen wenn zaehler bei 0
                if ( $buffer[$refid]["zaehler"] == 0 && $refid == $_SESSION["menued_id"] ) {
                    $tree .= "</ul>\n";
                }
            }
        }
        return $tree;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
