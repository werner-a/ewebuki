<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// funtion_menutree.inc.php v1 emnili
// funktion loader: sitemap
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2015 Werner Ammon ( wa<at>chaos.de )

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

    86343 Koenigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function sitemap($refid, $script_name, $art = "", $modify = "", $self = "") {
        global $hidedata, $design, $opentree, $treelink, $ausgaben, $cfg, $environment, $db, $pathvars, $specialvars, $rechte, $buffer, $positionArray;

        if ( isset($ausgaben["path"]) ) $ausgaben["path"] = null;
        if ( !isset($environment["parameter"][2]) ) $environment["parameter"][2] = null;

        $tree = null;
        $flapmenu = -1;
        $aktionlinks = -1;
        $hidestatus = -1;
        $sitemap = 0;
        $where = null;
        switch($art) {
            case 'menued':
                $sortinfo = -1;
                break;
            case 'select':
                $aktionlinks = 0;
                $radiorefid = -1;
                break;
            case 'wizard':
                $hidestatus = -1;
                break;
            case 'sitemap':
                $flapmenu = 0;
                $aktionlinks = 0;
                $hidestatus = 0;
                $sitemap = -1;
                $where = "AND (".$cfg[$script_name]["db"]["menu"]["entries"].".hide IS NULL OR ".$cfg[$script_name]["db"]["menu"]["entries"].".hide IN ('','0'))";
                break;
            default:
        }

        $sql = "SELECT  *  FROM  ".$cfg[$script_name]["db"]["menu"]["entries"]."
            INNER JOIN  ".$cfg[$script_name]["db"]["lang"]["entries"]."
                    ON  ".$cfg[$script_name]["db"]["menu"]["entries"].".mid = ".$cfg[$script_name]["db"]["lang"]["entries"].".mid
                 WHERE (".$cfg[$script_name]["db"]["menu"]["entries"].".refid=".$refid.")
                   AND (".$cfg[$script_name]["db"]["lang"]["entries"].".lang='".$environment["language"]."')
                   ".$where."
              ORDER BY  ".$cfg[$script_name]["db"]["menu"]["order"].";";

        $result  = $db -> query($sql);
        $count = $db->num_rows($result);

        if ( empty($buffer["pfad"]) ) $buffer["pfad"] = null;
        if ( empty($buffer["pfad_label"]) ) $buffer["pfad_label"] = null;
        while ( $array = $db -> fetch_array($result,1) ) {

            // aufbau des pfads
            if ( $refid == 0 || ( isset($positionArray) && in_array($refid, $positionArray) ) || $sitemap == -1 ) {
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
                if ( isset($environment["parameter"][2]) ) {
                    $move_parameter = ",".$environment["parameter"][2];
                } else {
                    $move_parameter = "";
                }

                // alle punkte die nicht im array sind nicht anzeigen
                if ( $refid != 0 && ( isset($positionArray) && !in_array($refid, $positionArray) ) ) {
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
                                $hidedata["back"]["link"] = $cfg[$script_name]["basis"]."/".$environment["parameter"][0].",".$array["refid"].$move_parameter.".html\"";
                            } else {
                                $hidedata["back"]["link"] = $cfg[$script_name]["basis"]."/".$environment["parameter"][0].",".$array["refid"].$move_parameter.".html\"";
                            }
                        }
                    }
                }

                // schauen ob unterpunkte vorhanden !
                $sql = "SELECT * FROM ".$cfg[$script_name]["db"]["menu"]["entries"]." WHERE refid=".$array["mid"];
                $result_in  = $db -> query($sql);
                $count_in = $db->num_rows($result_in);

                // sind unterpunkte vorhanden + oder - einblenden
                if  ( $count_in > 0 && $array["mid"] != $environment["parameter"][2]) {
                    $copy = $positionArray;
                    array_shift($copy);
                    ( is_array($opentree) && in_array($array["mid"],$opentree) ) ? $sign = "-" : $sign = "+";
                    $href = "<a class=".$class_hide." href=\"".$cfg[$script_name]["basis"]."/".$environment["parameter"][0].",".$array["mid"].$move_parameter.".html\">".$array["label"]."+</a>";
                } else {
                    $href = "<span class=".$class_hide.">".$array["label"]."</span>";
                }
            // hier wird komplett geoeffnet
            } elseif ( $sitemap == -1 ) {
                $title = $array["label"];
                if ( isset($array["extend"]) ) $title = $array["extend"];
                $href = "<a href=\"".$pathvars["virtual"].$buffer["pfad"].".html\" title=\"".$title."\">".$array["label"]."</a>";
            }else {
                $href = $array["label"] ;
            }
            $aktion = "";
            // schaltflaechen erstellen
            if ( $aktionlinks == -1) {
                // hier der alte rechte-check ! faellt weg !
                if ( $specialvars["security"]["enable"] == -1 ) {
                    // kategorie u. ebene herausfinden
                    $kategorie2check = substr($buffer["pfad"],0,strpos($buffer["pfad"],"/"));
                    $ebene2check = substr($buffer["pfad"],strpos($buffer["pfad"],"/"));
                    // hier findet der rechte-check statt
                    if ( right_check("-1",$ebene2check,$kategorie2check != "") || $rechte[$cfg[$script_name]["right_admin"]] == -1 ) {
                        $right = -1;
                    } else {
                        $right = "";
                    }
                }

                if ( is_array($modify) ) {
                    foreach($modify as $name => $value) {
                        if ( $specialvars["security"]["new"] == -1 ) {
                            if ( !priv_check(make_ebene($array["mid"]),$value[2],$specialvars["dyndb"]) && !priv_check(make_ebene($array["mid"]),$value[2])  ) continue;
                        } else {
                            if ( !$rechte[$cfg[$script_name]["right_admin"]] == -1 && $right != "-1" ) continue;
                        }
                        if ( $name == "up" || $name == "down") {
                            if ( $specialvars["security"]["new"] == -1 ) {
                                if ( !priv_check(make_ebene($array["refid"]),$value[2],$specialvars["dyndb"])  && !priv_check(make_ebene($array["mid"]),$value[2]) ) continue;
                            } else {
                                $kategorie2check = substr(make_ebene($array["refid"]),0,strpos(make_ebene($array["refid"]),"/"));
                                $ebene2check = substr(make_ebene($array["refid"]),strpos(make_ebene($array["refid"]),"/"));
                                if ( !$rechte[$cfg[$script_name]["right_admin"]] == -1 && ( !right_check("-1",$ebene2check,$kategorie2check != "") ) )  continue;
                            }
                        }
                        if ( $name == "rights" ) {
                            if ( $specialvars["security"]["new"] == -1 ) {
                                $aktion .= "<a href=\"".$pathvars["virtual"]."/".$cfg[$script_name]["subdir"]."/righted/edit,".$array["mid"].".html\"><img style=\"float:right\" src=\"".$cfg[$script_name]["iconpath"].$name.".png\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></img></a>";
                            } elseif ( $specialvars["security"]["enable"] == -1 ) {
                                $aktion .= "<a href=\"".$cfg[$script_name]["basis"]."/".$value[0].$name.",".$array["mid"].",".$array["refid"].".html\"><img style=\"float:right\" src=\"".$cfg[$script_name]["iconpath"].$name.".png\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></img></a>";
                            }
                            continue;
                        }
                        // anzeige der sortierung
                        if ( !empty($sortinfo) ) {
                            if ( $name == "sort") {
                                $aktion .= "<span title=\"".$value[1]."\" style=\"float:right\">(".$array["sort"].")</span>";
                                continue;
                            }
                        }
                        // anzeige des icons zur content-seite
                        if ( $name == "jump" ) {
                            $ankerlnk = null;
                            $aktion .= "<a target=\"_blank\" href=\"".$pathvars["virtual"].make_ebene($array["mid"]).".html".$ankerlnk."\"><img style=\"float:right\" src=\"".$cfg[$script_name]["iconpath"].$name.".png\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></img></a>";
                            continue;
                        }

                        // beim move ausnahme!
                        if ( $name == "move" ) {
                            $aktion .= "<a href=\"".$cfg[$script_name]["basis"]."/".$value[0].$name.",0,".$array["mid"].".html\"><img style=\"float:right\" src=\"".$cfg[$script_name]["iconpath"].$name.".png\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></img></a>";
                        } else {
                            if ( $art == "wizard") {
                                $database = "";
                                $label = "";
                                $ebene = make_ebene($array["mid"]);
                                $last =strrpos($ebene,"/");
                                $wizard_ebene = substr($ebene, 0, $last);
                                $wizard_kat = substr($ebene,$last+1);
                                $dest = $name;
                                if ( $name == "edit" ) {
                                    $database = DATABASE;
                                    $label =",inhalt";
                                    $dest = "show";
                                }

                                if ( isset($value[3]) ) {
                                        $dest = "show";
                                        $database = DATABASE;
                                        $wizard_array = explode(":",$value[4]);
                                        $template_array = explode(":",$value[3]);
                                        if ( in_array($array["defaulttemplate"],$template_array )) {
                                            foreach ( $wizard_array as $wizard_marken ) {
                                                if ( $wizard_marken ) {
                                                    $label = ",".$wizard_marken;
                                                    if ( $wizard_ebene == "" ) {
                                                        $aktion .= "<a href=/auth/wizard/".$dest.",".$database.",".$wizard_kat.$label.".html><span style=\"float:right\">".$cfg["menubaum_desc"][$wizard_marken]."</span></a>";
                                                    } else {
                                                        $aktion .= "<a href=/auth/wizard/".$dest.",".$database.",".eCRC($wizard_ebene).".".$wizard_kat.$label.".html><span style=\"float:right\">|".$cfg["menubaum_desc"][$wizard_marken]."</span></a>";
                                                    }
                                                }
                                            }
                                            continue;
                                        }
                                }
                                if (preg_match("/^wizard/", $name )) continue;

                                if ( $wizard_ebene == "" ) {
                                    $aktion .= "<a href=/auth/wizard/".$dest.",".$database.",".$wizard_kat.$label.".html><img style=\"float:right\" src=\"".$cfg[$script_name]["iconpath"].$name.".png\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></img></a>";
                                } else {
                                    $aktion .= "<a href=/auth/wizard/".$dest.",".$database.",".eCRC($wizard_ebene).".".$wizard_kat.$label.".html><img style=\"float:right\" src=\"".$cfg[$script_name]["iconpath"].$name.".png\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></img></a>";
                                }
                            } else {
                                $aktion .= "<a href=\"".$cfg[$script_name]["basis"]."/".$value[0].$name.",".$array["mid"].",".$array["refid"].".html\"><img style=\"float:right\" src=\"".$cfg[$script_name]["iconpath"].$name.".png\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></img></a>";
                            }
                        }
                    }
                }
             }

            // wo geht der href hin?
            if ( $array["exturl"] != "" ) {
                $href .= "&nbsp;<a class=".$class_hide." href=".$array["exturl"].">(->ext. Link)</a>";
            }

            // in den buffer schreiben wieviel unterpunkte fuer jeweiligen Ueberpunkt vorhanden sind !
            if ( !isset($buffer[$refid]["zaehler"]) ) {
                $buffer[$refid]["zaehler"] = $count;

                if ( !isset($buffer[$refid]["display"]) ) $buffer[$refid]["display"] = null;
                if ( $buffer[$refid]["display"] != "none" ) {
                    // beim ersten aufruf eine class menued setzen
                    if ( $self == "" ) {
                        $tree .= "<ul class=\"menued\">\n";
                        if ( $art == "select" && priv_check(make_ebene(0),$cfg["menued"]["modify"]["move"][2], $specialvars["dyndb"]) ) {
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
            $radiobutton = null;
            if ( isset($radiorefid) ) {
                if ( $array["mid"] == $environment["parameter"][2] || ( $specialvars["security"]["new"] == -1 && !priv_check(make_ebene($array["mid"]),$cfg["menued"]["modify"]["move"][2]) ) ) {
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
            $tree .= sitemap($array["mid"], $script_name, $art, $modify, -1);

            // abschliessendes li anbringen
            if ( $buffer[$refid]["display"] != "none" ) {
                $tree .= "</li>\n";
            }

            // abschliessendes ul anbringen u. pfad kuerzen
            if ( isset($buffer[$refid]["zaehler"]) ) {
                // pfad kuerzen
                $buffer["pfad"] = substr($buffer["pfad"],0,strrpos($buffer["pfad"],"/"));
                $buffer["pfad_label"] = substr($buffer["pfad_label"],0,strrpos($buffer["pfad_label"],"/"));
                // zaehler 1 zuruecksetzen
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
