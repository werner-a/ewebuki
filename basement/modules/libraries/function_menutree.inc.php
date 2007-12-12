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
        global $hidedata,$design,$opentree,$treelink,$ausgaben,$cfg, $environment, $db, $pathvars, $specialvars, $rechte, $buffer,$positionArray;

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
                $sitemap = -1;
                break;
            default:

        }

        $sql = "SELECT  ".$cfg[$art]["db"]["menu"]["entries"].".mid,
                        ".$cfg[$art]["db"]["menu"]["entries"].".entry,
                        ".$cfg[$art]["db"]["menu"]["entries"].".refid,
                        ".$cfg[$art]["db"]["menu"]["entries"].".level,
                        ".$cfg[$art]["db"]["menu"]["entries"].".sort,
                        ".$cfg[$art]["db"]["menu"]["entries"].".hide,
                        ".$cfg[$art]["db"]["lang"]["entries"].".lang,
                        ".$cfg[$art]["db"]["lang"]["entries"].".label,
                        ".$cfg[$art]["db"]["lang"]["entries"].".exturl
                FROM  ".$cfg[$art]["db"]["menu"]["entries"]."
            INNER JOIN  ".$cfg[$art]["db"]["lang"]["entries"]."
                    ON  ".$cfg[$art]["db"]["menu"]["entries"].".mid = ".$cfg[$art]["db"]["lang"]["entries"].".mid
                WHERE (".$cfg[$art]["db"]["menu"]["entries"].".refid=".$refid.")
                AND (".$cfg[$art]["db"]["lang"]["entries"].".lang='".$environment["language"]."')
            ORDER BY  ".$cfg[$art]["db"]["menu"]["order"].";";

        $result  = $db -> query($sql);
        $count = $db->num_rows($result);

        while ( $array = $db -> fetch_array($result,1) ) {

            // aufbau des pfads
            if ( $refid == 0 || in_array($refid,$positionArray) || $sitemap == -1 ) {
                $buffer["pfad"] .= "/".$array["entry"];
                $buffer["pfad_label"] .= "/".$array["label"];
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

                // zweiten parameter mitziehen wenn er gesetzt ist
                if ( $environment["parameter"][2] != "" ) {
                    $move_parameter = ",".$environment["parameter"][2];
                } else {
                    $move_parameter = "";
                }

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
                            $ausgaben["path"] = $buffer["pfad_label"];
                            if ( $array["refid"] == 0 ) {
                                $hidedata["back"]["link"] = $cfg[$art]["basis"]."/".$environment["parameter"][0].",".$array["refid"].$move_parameter.".html\"";
                            } else {
                                $hidedata["back"]["link"] = $cfg[$art]["basis"]."/".$environment["parameter"][0].",".$array["refid"].$move_parameter.".html\"";
                            }
                        }
                    }
                }

                // schauen ob unterpunkte vorhanden !
                $sql = "SELECT * FROM ".$cfg[$art]["db"]["menu"]["entries"]." WHERE refid=".$array["mid"];
                $result_in  = $db -> query($sql);
                $count_in = $db->num_rows($result_in);

                // sind unterpunkte vorhanden + oder - einblenden
                if  ( $count_in > 0 && $array["mid"] != $environment["parameter"][2]) {
                    $copy = $positionArray;
                    array_shift($copy);
                    ( is_array($opentree) && in_array($array["mid"],$opentree) ) ? $sign = "-" : $sign = "+";
                    $href = "<a class=".$class_hide." href=\"".$cfg[$art]["basis"]."/".$environment["parameter"][0].",".$array["mid"].$move_parameter.".html\">".$array["label"]."+</a>"."\n";
                } else {
                    $href = "<span class=".$class_hide.">".$array["label"]."</span>";
                }
            // hier wird komplett geoeffnet
            } elseif ( $sitemap == -1 ) {
                $href = "<a href=\"".$pathvars["virtual"].$buffer["pfad"].".html\">".$array["label"]."</a>";
            }else {
                $href = $array["label"] ;
            }

            // schaltflaechen erstellen
            if ( $aktionlinks == -1) {
                // hier der alte rechte-check ! fällt weg !
                if ( $specialvars["security"]["enable"] == -1 ) {
                    // kategorie u. ebene herausfinden
                    $kategorie2check = substr($buffer["pfad"],0,strpos($buffer["pfad"],"/"));
                    $ebene2check = substr($buffer["pfad"],strpos($buffer["pfad"],"/"));
                    // hier findet der rechte-check statt
                    if ( right_check("-1",$ebene2check,$kategorie2check != "") || $rechte[$cfg[$art]["right_admin"]] == -1 ) {
                        $right = -1;
                    } else {
                        $right = "";
                    }
                }

                $aktion = "";
                if ( is_array($modify) ) {
                    foreach($modify as $name => $value) {
                        if ( !priv_check(make_ebene($array["mid"]),$value[2]) && !$rechte[$cfg[$art]["right"]] == -1 && $right != "-1") { 
                            continue;
                        }
                        if ( $name == "rights" ) {
                            if ( $specialvars["security"]["new"] == -1 ) {
                                $aktion .= "<a href=\"".$pathvars["virtual"]."/".$cfg[$art]["subdir"]."/righted/edit,".$array["mid"].".html\"><img style=\"float:right\" src=\"".$cfg[$art]["iconpath"].$name.".png\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></img></a>";
                                continue;
                            }
                        }
                        // anzeige der sortierung
                        if ( $sortinfo != "" ) {
                            if ( $name == "sort") {
                                $aktion .= "<span title=\"".$value[1]."\" style=\"float:right\">(".$array["sort"].")</span>";
                                continue;
                            }
                        }
                        // anzeige des icons zur content-seite
                        if ( $name == "jump" ) {
                            $aktion .= "<a href=\"".$pathvars["virtual"].$buffer["pfad"].".html".$ankerlnk."\"><img style=\"float:right\" src=\"".$cfg[$art]["iconpath"].$name.".png\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></img></a>";
                            continue;
                        }

                        // beim move ausnahme!
                        if ( $name == "move" ) {
                            $aktion .= "<a href=\"".$cfg[$art]["basis"]."/".$value[0].$name.",0,".$array["mid"].".html\"><img style=\"float:right\" src=\"".$cfg[$art]["iconpath"].$name.".png\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></img></a>";
                        } else {
                            $aktion .= "<a href=\"".$cfg[$art]["basis"]."/".$value[0].$name.",".$array["mid"].",".$array["refid"].".html\"><img style=\"float:right\" src=\"".$cfg[$art]["iconpath"].$name.".png\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></img></a>";
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
                        if ( $art == "select" ) {
                            $tree .= "<li><input type=\"radio\" name=\"refid\" value=\"".$refid."\" />#(root)</li>";
                        }
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
                if ( ($array["mid"] == $environment["parameter"][2]) || $array["refid"] == $environment["parameter"][2]  ) {
                    $radio_disabled = " disabled";
                } else {
                    $radio_disabled = "";
                }
                $radiobutton = "<input type=\"radio\" name=\"refid\" ".$radio_disabled." value=\"".$array["mid"]."\" />";
            }

            // listenpunkt schreiben
            if ( $buffer[$refid]["display"] != "none" ) {
                $tree .= "<li>".$aktion.$radiobutton.$href;
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
                $buffer["pfad_label"] = substr($buffer["pfad_label"],0,strrpos($buffer["pfad_label"],"/"));
                // zaehler 1 zurücksetzen
                $buffer[$refid]["zaehler"] = $buffer[$refid]["zaehler"] -1;
                // ul anbringen wenn zaehler bei 0
                if ( $buffer[$refid]["zaehler"] == 0  && ( $art == "sitemap" || $refid == $_SESSION["menued_id"]) ) {
                    $tree .= "</ul>\n";
                }
            }
        }
        return $tree;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
