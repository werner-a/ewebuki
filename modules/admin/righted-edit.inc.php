<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// righted-edit.inc.php v1 emnili
// righted - edit funktion
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

    if ( !isset($environment["parameter"][1]) ) $environment["parameter"][1] = null;
    if ( !isset($environment["parameter"][2]) ) $environment["parameter"][2] = null;
    if ( !isset($environment["parameter"][2]) ) $environment["parameter"][3] = null;

    $url = make_ebene($environment["parameter"][1]);

    if ( $cfg["righted"]["right"] == "" || priv_check($url, $cfg["righted"]["right"] ) ) {

        // Plausibilitaetskontrolle der vergebenen Rechte
        $rechte_check = plausibleCheck();
        if ( is_array($rechte_check ) ) {
            $hidedata["plausible_check"]["on"] = -1;
            foreach ( $rechte_check as $key => $value ) {
                foreach ( $value as $key_in => $value_in ) {
                    $dataloop["plausible_check"][$key][$key_in] = $value_in;
                }
            }
        }

        // bauen der legende
        foreach ( $cfg["righted"]["button"]  as $key => $value ) {
            switch ($key) {
                case "new":
                    $dataloop["legende"][$key]["color"] = $value["color"];
                    $dataloop["legende"][$key]["text"] = "Kein Recht";
                    break;
                case "add":
                    $dataloop["legende"][$key]["color"] = $value["color"];
                    $dataloop["legende"][$key]["text"] = "Zugewiesenes Recht";
                    break;
                case "del":
                    $dataloop["legende"][$key]["color"] = $value["color"];
                    $dataloop["legende"][$key]["text"] = "Negiertes Recht";
                    break;
                case "inh":
                    $dataloop["legende"][$key]["color"] = $value["color"];
                    $dataloop["legende"][$key]["text"] = "Vererbtes Recht";
                    break;
            }
        }

        // ausgeben der url wo man sich gerade befindet
        $ausgaben["url"] = $url;

        $url2 = $url;
        $infos = "";
        // erstellen der info - box
        $infos = priv_info($url,$nop);

        // wenn parameter 2 gesetzt ist, info-box oeffnen
        if ( !empty($environment["parameter"][2]) ) {
            $ausgaben["display"] = "visible";
        } else {
            $ausgaben["display"] = "none";
        }

        // holen aller rechte
        $sql ="SELECT * FROM ".$cfg["righted"]["db"]["priv"]["entries"];
        $result = $db -> query($sql);
        while ( $all = $db -> fetch_array($result,1) ) {
            $all_rights[$all[$cfg["righted"]["db"]["priv"]["key"]]] = $all[$cfg["righted"]["db"]["priv"]["name"]];
        }

        // holen aller gruppen
        $sql ="SELECT * FROM ".$cfg["righted"]["db"]["group"]["entries"];
        $result = $db -> query($sql);
        while ( $all = $db -> fetch_array($result,1) ) {
            $all_groups[$all[$cfg["righted"]["db"]["group"]["key"]]] = $all[$cfg["righted"]["db"]["group"]["name"]];
        }

        // holen aller user
        if ( $cfg["righted"]["db"]["user"]["entries"] != "" ) {
            $sql ="SELECT * FROM ".$cfg["righted"]["db"]["user"]["entries"];
            $result = $db -> query($sql);
            while ( $all = $db -> fetch_array($result,1) ) {
                $all_user[$all[$cfg["righted"]["db"]["user"]["key"]]] = $all[$cfg["righted"]["db"]["user"]["name"]];
            }
        }

        $anzahl_rechte = count($all_rights);
        $prozent = intval(100/$anzahl_rechte);


        if ( is_array($infos["group"]) ) {
            $infos["group"] = array_reverse($infos["group"]);
        }
        $counter = 0;
        foreach ( $all_groups as $group_key => $group_value ) {
            $counter++;
            $dataloop["infos_group"][$counter]["info"] = null;
            $dataloop["infos_group"][$counter]["gruppe"] = $group_value;
            foreach ( $all_rights as $rights_key => $rights_value ) {
                $background = $cfg["righted"]["button"]["new"]["color"];
                $name = "new";
                if ( is_array($infos["group"]) ) {
                    foreach ( $infos["group"] as $info_key => $info_value ) {
                        if ( isset($info_value["add"]) && is_array($info_value["add"]) ) {
                            if ( preg_match("/".$rights_value.",/", @$info_value["add"][$group_value]) ) {
                                $background = $cfg["righted"]["button"]["add"]["color"];
                                $name = "add";
                            }
                        }
                        if ( isset($info_value["inh"]) && is_array($info_value["inh"]) ) {
                            if ( preg_match("/".$rights_value.",/", @$info_value["inh"][$group_value]) ) {
                                $background = $cfg["righted"]["button"]["inh"]["color"];
                                $name = "add";
                            }
                        }
                        if ( isset($info_value["del"]) && is_array($info_value["del"]) ) {
                            if ( preg_match("/".$rights_value.",/", $info_value["del"][$group_value]) ) {
                                $background = $cfg["righted"]["button"]["del"]["color"];
                                $name = "del";
                            }
                        }
                    }
                }
                $dataloop["infos_group"][$counter]["info"] .= "<input name=\"group_".$name."_".$group_key."_".$rights_key."\" value=\"".$rights_value."\" style=width:". $prozent."%;background:".$background." type=\"submit\"></input>";
            }
        }

        if ( isset($infos["user"]) && is_array($infos["user"]) ) {
            $infos["user"] = array_reverse($infos["user"]);
        }
        if ( is_array($all_user) ) {
            $counter = 0;
            foreach ( $all_user as $user_key => $user_value ) {
                $counter++;
                $dataloop["infos_user"][$counter]["info"] = null;
                $dataloop["infos_user"][$counter]["user"] = $user_value;
                foreach ( $all_rights as $rights_key => $rights_value ) {
                    $background = $cfg["righted"]["button"]["new"]["color"];
                    $name = "new";
                    if ( isset($infos["user"]) && is_array($infos["user"]) ) {
                        foreach ( $infos["user"] as $info_key => $info_value ) {
                            if ( isset($info_value["add"]) && is_array($info_value["add"]) ) {
                                if ( isset( $info_value["add"][$user_value]) ) {
                                    if ( preg_match("/".$rights_value.",/",$info_value["add"][$user_value]) ) {
                                        $background = $cfg["righted"]["button"]["add"]["color"];
                                        $name = "add";
                                    }
                                }
                            }
                            if ( isset($info_value["inh"]) && is_array($info_value["inh"]) ) {
                                if ( isset( $info_value["inh"][$user_value]) ) {
                                    if ( preg_match("/".$rights_value.",/",$info_value["inh"][$user_value]) ) {
                                        $background = $cfg["righted"]["button"]["inh"]["color"];
                                        $name = "add";
                                    }
                                }
                            }
                            if ( isset($info_value["del"]) && is_array($info_value["del"]) ) {
                                if ( isset( $info_value["del"][$user_value]) ) {
                                    if ( preg_match("/".$rights_value.",/",$info_value["del"][$user_value]) ) {
                                        $background = $cfg["righted"]["button"]["del"]["color"];
                                        $name = "del";
                                    }
                                }
                            }
                        }
                    }
                    $dataloop["infos_user"][$counter]["info"] .= "<input name=\"user_".$name."_".$user_key."_".$rights_key."\"  value=\"".$rights_value."\" style=width:". $prozent."%;background:".$background." type=\"submit\"></input>";
                }
            }
        } else {
            $dataloop["infos_user"][$counter]["user"] = "ERROR";
            $dataloop["infos_user"][$counter]["info"] = "Bitte aktuelle config verwenden";
        }

        // Rechte Uebersicht erstellen
        $sql = "SELECT * FROM ".$cfg["righted"]["db"]["user"]["entries"];
        $result = $db -> query($sql);
        while ( $UserData = $db -> fetch_array($result,1) ) {
            $text = "";
            $User_tname = "";
            $sql_in = "SELECT * FROM ".$cfg["righted"]["db"]["content"]["entries"]." INNER JOIN auth_priv ON (auth_content.pid=auth_priv.pid) WHERE ".$cfg["righted"]["db"]["user"]["key"]."=".$UserData["uid"];
            $result_in = $db -> query($sql_in);
            while ( $UserRightData = $db -> fetch_array($result_in,1) ) {
                $User_tname .= $UserRightData["tname"]." - ".$UserRightData["priv"]."<br>";
            }            
            $dataloop["overview"][$UserData[$cfg["righted"]["db"]["user"]["key"]]]["name"] = $UserData[$cfg["righted"]["db"]["user"]["name"]];
            $dataloop["overview"][$UserData[$cfg["righted"]["db"]["user"]["key"]]]["text"] = parser(eCRC($environment["ebene"]).".modify-parse",'');
        }



        // form options holen
        $form_options = form_options(eCRC($environment["ebene"]).".".$environment["kategorie"]);

        // form elememte bauen
        $element = form_elements( $cfg["righted"]["db"]["content"]["entries"], @$form_values );

        // form elemente erweitern
        $element["extension1"] = "<input name=\"extension1\" type=\"text\" maxlength=\"5\" size=\"5\">";
        $element["extension2"] = "<input name=\"extension2\" type=\"text\" maxlength=\"5\" size=\"5\">";

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["righted"]["basis"]."/edit,".$environment["parameter"][1].",".$environment["parameter"][2].",verify.html";
        $sql = "SELECT refid FROM site_menu WHERE mid=".$environment["parameter"][1];
        $result = $db -> query($sql);
        $data = $db -> fetch_array($result,1);
        $ausgaben["form_break"] = $pathvars["virtual"]."/admin/menued/list,".$data["refid"].".html";

        // hidden values
        $ausgaben["form_hidden"] = "";

        // was anzeigen
        $mapping["main"] = eCRC($environment["ebene"]).".modify";

        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($_GET["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_result) #(error_result)<br />";
            $ausgaben["inaccessible"] .= "# (error_dupe) #(error_dupe)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        #n/a

        // +++
        // page basics


        $preg = "/^(group|user)_(add|new|del)_([0-9]*)_([0-9]*)$/";
        
        if ( !empty($environment["parameter"][3]) && $environment["parameter"][3] == "verify" && preg_match($preg,key($_POST),$regs) ){

            $art = $regs[1];
            $aktion = $regs[2];
            $gruppe = $regs[3];
            $recht = $regs[4];

            $id = "gid";
            if ( $art == "user" ) {
                $id = "uid";
            }

            $sql = "SELECT pid FROM auth_priv WHERE pid='".$recht."'";
            $result = $db -> query($sql);
            $data_priv = $db -> fetch_array($result,1);
            if ( $aktion == "add" ) {
                $sql = "SELECT * FROM auth_content WHERE ".$id."='".$gruppe."' AND pid='".$data_priv["pid"]."' AND db='".$specialvars["dyndb"]."' AND tname='".$url."' AND neg !='-1'";
                $result_pruef = $db -> query($sql);
                $treffer = $db -> num_rows($result_pruef,1);
                if ( $treffer == 1 && $url == $url2 ) {
                    $sql = "DELETE FROM auth_content WHERE ".$id."='".$gruppe."' AND pid='".$data_priv["pid"]."' AND tname='".$url."' AND neg!='-1'";
                } else {
                    $sql = "INSERT INTO auth_content (".$id.",pid,db,tname,neg,ebene,kategorie) VALUES ('".$gruppe."','".$data_priv["pid"]."','".$specialvars["dyndb"]."','".$url."','-1','','')";
                }
            } elseif ( $aktion == "del" ) {
                $sql_test = "SELECT * FROM auth_content WHERE ".$id."='".$gruppe."' AND pid='".$data_priv["pid"]."' AND tname='".$url."' AND neg='-1'";
                $result_test = $db -> query($sql_test);
                if ( $db -> num_rows($result_test) > 0 ) {
                    $sql = "DELETE FROM auth_content WHERE ".$id."='".$gruppe."' AND pid='".$data_priv["pid"]."' AND tname='".$url."' AND neg='-1'";
                } else {
                    $sql = "INSERT INTO auth_content (".$id.",pid,db,tname,ebene,kategorie) VALUES ('".$gruppe."','".$data_priv["pid"]."','".$specialvars["dyndb"]."','".$url."','','')";
                }
            } else {
                $sql = "INSERT INTO auth_content (".$id.",pid,db,tname,ebene,kategorie) VALUES ('".$gruppe."','".$data_priv["pid"]."','".$specialvars["dyndb"]."','".$url."','','')";
            }
            $result = $db -> query($sql);

            if ( $header == "" ) $header = $cfg["righted"]["basis"]."/edit,".$environment["parameter"][1].",".$environment["parameter"][2].".html#".$art;

            // wenn es keine fehlermeldungen gab, die uri $header laden
            if ( $ausgaben["form_error"] == "" ) {
                header("Location: ".$header);
            }
        }
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
