<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "Beschaeftigte Liste anzeigen";
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



    //
    // Liste anzeigen
    //
    if ( $environment["kategorie"] == "list" || $environment["kategorie"] == $cfg["name"] ) {
        $position = $environment["parameter"][1]+0;
        $ausgaben["search"] = "";
        $ip_class = explode(".", $_SERVER["REMOTE_ADDR"]);

        #echo "<pre>";
        #print_r($ip_class);
        #echo "</pre>";

        // Schnellsuche (mor1305)
        // ***
        if ( $HTTP_GET_VARS["search"] != "" ) {
            $search_value = $HTTP_GET_VARS["search"];
            $ausgaben["search"] = $search_value;
            $ausgaben["result"] = "Ihre Schnellsuche nach \"".$search_value."\" hat ";
            $search_value = explode(" ",$search_value);
            // sql aus get vars erstellen
            $suche = array("abtitel", "abnamra", "abnamvor", "adkate", "adststelle", "adstbfd", "abdstref", "abinteressen");
            $wherea = "";

            foreach ( $search_value as $value1 ) {
                if ( $value1 != "" ) {
                    if ($getvalues == "") $getvalues = "search=";
                    $getvalues .= $value1." ";
                    foreach ($suche as $value2) {
                        if ($wherea != "") $wherea .= " or ";
                        $wherea .= $value2. " LIKE '%" .$value1."%'";
                   }
                }
            }
        }
        // sql um lokal erweitern (mor1305)
        if ( $HTTP_GET_VARS["lokal"] == "on") {
            $lokalcheck = "checked";
            if ($getvalues != "") $und= "&";
            $getvalues .= $und."lokal=on";
            $whereb = " (abbnet='".$ip_class[1]."' AND abcnet='".$ip_class[2]."')";
        } else {
            $lokalcheck = "";
        }
        // gibt es beide
        if ($wherea && $whereb) $trenner = " AND ";
        // ist wherea da klammern setezn
        if ($wherea) $wherea = "(".$wherea.")";
        // where zusammensetzen
        if ($wherea || $whereb) $where = " WHERE ".$wherea.$trenner.$whereb;
        // +++
        // Schnellsuche (mor1305)

        // Erweiterte Suche (mor 2404)
        // ***
        if ( $HTTP_GET_VARS["esearch"] == true) {
                $kick = array( "image", "image_x", "image_y", "esearch");
                foreach ($HTTP_GET_VARS as $key => $value) {
                    if ( !in_array($key,$kick)  ) {
                        if ( $value != "" ) {
                            if ($getvalues != "") $getvalues .= "&";
                            $getvalues .= $key."=".$value;
                            // sql WHERE bauen
                            if ( $where != "" ) $where .= " AND ";
                            if ($key == "abdstposten") {
                                $where .= $key."=".$value;
                            } elseif ($key == "abdststelle") {
                                $where .= $key."=".$value;
                            } elseif ($key == "abinteressen") {
                                $where .= $key." LIKE '%".$value."%'";
                            } elseif ($key == "abnet") {
                                $where .= $key."=".$value;
                            } elseif ($key == "abdstbfd") {
                                $sql1 = "SELECT adid FROM db_adrd WHERE adstbfd = '".$value."'";
                                $result1 = $db -> query($sql1);
                                while ( $data = $db->fetch_array($result1,$nop) ) {
                                    if ($where1 != "") $where1 .= " or ";
                                    $where1 .= "abdststelle = ".$data["adid"];
                                }
                                $where = $where ."(".$where1.")";
                            } else {
                                #$where .= $key." LIKE '%".$value."%'";
                                $where .= $key." LIKE '".$value."%'";
                            }
                            // suchergebnis ausgabe bauen
                            if ( $suchergebnis !="" ) $suchergebnis .= " und ";
                            if ( $key == "abdstposten" ) {
                                $sql = "SELECT abdienst from db_adrb_dienst WHERE abdienst_id = ".$HTTP_GET_VARS["abdstposten"];
                                $result = $db -> query($sql);
                                $field = $db -> fetch_array($result,$nop);
                                $suchergebnis .= "\"".$field["abdienst"]."\"";
                            } elseif ( $key == "abdststelle" ) {
                                $sql = "SELECT adststelle ,adkate from db_adrd WHERE adid=".$HTTP_GET_VARS["abdststelle"];
                                $result = $db -> query($sql);
                                $field = $db -> fetch_array($result,$nop);
                                $suchergebnis .= "\"".$field["adkate"]." ".$field["adststelle"]."\"";
                            } elseif ( $key == "abamtbez") {
                                $sql = "SELECT abamtbezkurz from db_adrb_amtbez WHERE abamtbez_id=".$HTTP_GET_VARS["abamtbez"];
                                $result = $db -> query($sql);
                                $field = $db -> fetch_array($result,$nop);
                                $suchergebnis .= "\"".$field["abamtbezkurz"]."\"";
                            } elseif ( $key == "abad") {
                                $suchergebnis .= "\"Außendienst\"";
                            } else {
                                $suchergebnis .= "\"".$value."\"";
                            }
                        }
                    }
                }
                $getvalues .= "&esearch=true";
                if ( $where != "" ) {
                    $ausgaben["result"] = "Ihre Erweiterte Suche nach ".$suchergebnis." hat";
                    $where = " WHERE (".$where.")";
                }
        }
        // +++
        // Erweiterte Suche (mor 2404)

        //checkbox lokale suche bauen
        // ***
        $ausgaben["check"] = "<input type=\"checkbox\" name=\"lokal\" value=\"on\"".$lokalcheck.">";
        // +++
        //checkbox lokale suche bauen

        // Sql Query
        $sql = "SELECT abid, abbnet, abcnet, abanrede, abnamra, abnamvor, abpasswort, adkate, adststelle, adstbfd, abdstemail, abdsttel, abdststelle FROM ".$cfg["db"]["entries"]." INNER JOIN db_adrd ON abdststelle=adid".$where." ORDER by ".$cfg["db"]["order"];

        // Inhalt Selector erstellen und SQL modifizieren
        $inhalt_selector = inhalt_selector( $sql, $position, $cfg["db"]["rows"], $parameter, 1, $cfg["db"]["selects"], $getvalues );  # neu mit get
        $ausgaben["inhalt_selector"] .= $inhalt_selector[0];
        $sql = $inhalt_selector[1];
        $ausgaben["gesamt"] = $inhalt_selector[2];

        // Daten holen und ausgeben
        $result = $db -> query($sql);

        if ( $db->num_rows($result) == 0 ) {
            $ausgaben["result"] .= " keine Einträge gefunden.<br><br>";
        } else {

            // nur erweitern wenn bereits was drin steht
            if ( $ausgaben["result"] ) {
                $ausgaben["result"] .= " folgende Einträge gefunden.<br><br>";
            } else {
                $ausgaben["result"]  = "";
            }

            $ausgaben["output"] .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
            $ausgaben["output"] .= "<tr>";
            $class = " class=\"lines\"";
            $ausgaben["output"] .= "<td".$class." colspan=\"12\"><img src=\"".$pathvars["images"]."/pos.png\" alt=\"\" width=\"1\" height=\"1\"></td>";
            $ausgaben["output"] .= "</tr>";
            $class = " class=\"contenthead\"";

            $size  = " width=\"5\"";
            $ausgaben["output"] .= "<td".$class.">Name</td>";
            $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
            $ausgaben["output"] .= "<td".$class.">Vorname</td>";
            $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
            $ausgaben["output"] .= "<td".$class.">Dienststelle</td>";
            $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
            $ausgaben["output"] .= "<td".$class.">eMail</td>";
            $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
            $ausgaben["output"] .= "<td".$class.">Telefon</td>";
            $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
            $ausgaben["output"] .= "<td".$class." align=\"right\">Aktion</td>";
            $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
            $ausgaben["output"] .= "</tr><tr>";
            $class = " class=\"lines\"";
            $ausgaben["output"] .= "<td".$class." colspan=\"12\"><img src=\"".$pathvars["images"]."/pos.png\" alt=\"\" width=\"1\" height=\"1\"></td>";
            $ausgaben["output"] .= "</tr>";

            $modify = array (
              "edit"        => array("modify,", "Bearbeiten", $cfg["right"]["adress"]), ###
              "delete"      => array("modify,", "Löschen", $cfg["right"]["adress"]),
              "details"     => array("", "Details", ""),
            );
            $imgpath = $pathvars["images"];

            while ( $field = $db -> fetch_array($result,$nop) ) {

                $ausgaben["output"] .= "<tr>";
                $class = " class=\"contenttabs\"";
                $size  = " width=\"5\"";

                #$ldate = $field["ldate"];
                #$field["ldate"] = substr($ldate,8,2).".".substr($ldate,5,2).".".substr($ldate,0,4)." ".substr($ldate,11,9);
                $ausgaben["output"] .= "<td".$class."><a href=\"".$cfg["basis"]."/details,".$field[$cfg["db"]["key"]].".html\">".$field["abnamra"]."</a></td>";
                $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
                $ausgaben["output"] .= "<td".$class."><a href=\"".$cfg["basis"]."/details,".$field[$cfg["db"]["key"]].".html\">".$field["abnamvor"]."</a></td>";
                $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
                $ausgaben["output"] .= "<td".$class." nowrap>".$field["adkate"]." ".$field["adststelle"]."</td>";
                $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
                $ausgaben["output"] .= "<td".$class."><a href=\"mailto:".$field["abdstemail"]."\">".str_replace($field["abdstemail"],(substr($field["abdstemail"],0,10))."...",$field["abdstemail"])."</a></td>";
                $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
                $ausgaben["output"] .= "<td".$class.">".$field["abdsttel"]."</td>";
                $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";

                $aktion = "";


                // icon "recht hinzufügen" bzw. "recht bearbeiten" erstellen
                // wenn berechtigung vorhanden
                if ( $rechte[$cfg["right"]["admin"]] == -1 && in_array($field["abdststelle"],$HTTP_SESSION_VARS["dstzugriff"]) && $field["abanrede"] != "Raum" ) {

                    // icon schwarz für eigene dienststelle
                    if ( $HTTP_SESSION_VARS["custom"] == $field["abdststelle"] ) {
                        if ( $field["abpasswort"] == "") {
                            $aktion .= "<a href=\"".$pathvars["virtual"]."/admin/usered/modify,add,".$field[$cfg["db"]["key"]].".html\"><img src=\"".$pathvars["images"]."/addr.png\" border=\"0\" alt=\"Rechte hinzufügen\" title=\"Rechte hinzufügen\" width=\"24\" height=\"18\"></a>";
                        } else {
                            $aktion .= "<a href=\"".$pathvars["virtual"]."/admin/usered/modify,edit,".$field[$cfg["db"]["key"]].".html\"><img src=\"".$pathvars["images"]."/editr.png\" border=\"0\" alt=\"Rechte bearbeiten\" title=\"Rechte bearbeiten\" width=\"24\" height=\"18\"></a>";
                        }
                    // icon rot für fremde dienststelle
                    } else {
                            if ( $field["abpasswort"] == "") {
                            $aktion .= "<a href=\"".$pathvars["virtual"]."/admin/usered/modify,add,".$field[$cfg["db"]["key"]].".html\"><img src=\"".$pathvars["images"]."/addra.png\" border=\"0\" alt=\"Fremde Rechte hinzufügen\" title=\"Fremde Rechte hinzufügen\" width=\"24\" height=\"18\"></a>";
                        } else {
                            $aktion .= "<a href=\"".$pathvars["virtual"]."/admin/usered/modify,edit,".$field[$cfg["db"]["key"]].".html\"><img src=\"".$pathvars["images"]."/editra.png\" border=\"0\" alt=\"Fremde Rechte bearbeiten\" title=\"Fremde Rechte bearbeiten\" width=\"24\" height=\"18\"></a>";
                        }
                    }
                }

                // icons "bearbeiten", "löschen" und "details" erstellen
                // wenn berechtigung vorhanden
                foreach($modify as $name => $value) {

                    // icon schwarz für eigene dienststelle
                    if ( $rechte[$value[2]] == -1
                            && $HTTP_SESSION_VARS["custom"] == $field["abdststelle"]
                            || $value[2] == "" ) {
                        $aktion .= "<a href=\"".$cfg["basis"]."/".$value[0].$name.",".$field[$cfg["db"]["key"]].".html\"><img src=\"".$imgpath.$name.".png\" border=\"0\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></a>";

                     // icon rot für fremde dienststelle
                    } elseif ( $rechte[$cfg["right"]["admin"]] == -1
                            && in_array($field["abdststelle"],$HTTP_SESSION_VARS["dstzugriff"])
                            && $name == "edit" ) {
                        $aktion .= "<a href=\"".$cfg["basis"]."/".$value[0].$name.",".$field[$cfg["db"]["key"]].".html\"><img src=\"".$imgpath.$name."a.png\" border=\"0\" alt=\"Fremde Beschäftigte ".$value[1]."\" title=\"Fremde Beschäftigte ".$value[1]."\" width=\"24\" height=\"18\"></a>";
                    } else {
                        $aktion .= "<img src=\"".$imgpath."/pos.png\" alt=\"\" width=\"24\" height=\"18\">";
                    }
                }
                $ausgaben["output"] .= "<td".$class." align=\"right\">".$aktion."</td>";
                $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";


                $ausgaben["output"] .= "</tr><tr>";
                $class = " class=\"lines\"";
                $ausgaben["output"] .= "<td".$class." colspan=\"12\"><img src=\"".$pathvars["images"]."/pos.png\" alt=\"\" width=\"1\" height=\"1\"></td>";
                $ausgaben["output"] .= "</tr>";
            }
            $ausgaben["output"] .= "</table>";
        }


        if ( $rechte[$cfg["right"]["chf"]] == -1 || $rechte[$cfg["right"]["sti"]] == -1 ) {
            // mail the list funktion weam 1908
            // **
            if ( $ausgaben["gesamt"] <= $cfg["mail"]["limit"] || $cfg["mail"]["limit"] == 0 ) {
                $sql = str_replace(strstr($sql,"LIMIT"),"",$sql);
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "<font color=\"#FF0000\">BUFFY".$sql."</font>".$debugging["char"];
                $result = $db -> query($sql);
                while ( $field = $db -> fetch_array($result,$nop) ) {
                    if ( $field["abdstemail"] != "" ) {
                        $field["abnamvor"] = str_replace (array("ä","ö","ü","ß"),array("ae","oe","ue","ss"), $field["abnamvor"]);
                        $field["abnamra"] = str_replace (array("ä","ö","ü","ß"),array("ae","oe","ue","ss"), $field["abnamra"]);
                        if ( $verteiler == "" ) {
                            #$verteiler = "mailto:".$field["abnamvor"]." ".$field["abnamra"]." <".$field["abdstemail"].">";
                            $verteiler = "mailto:?bcc=".$field["abnamvor"]." ".$field["abnamra"]." <".$field["abdstemail"].">";

                        } else {
                            #if ( strstr($verteiler,"?bcc=") ) {
                                $verteiler .= "&bcc=".$field["abnamvor"]." ".$field["abnamra"]." <".$field["abdstemail"].">";
                            #} else {
                            #    $verteiler .= "?bcc=".$field["abnamvor"]." ".$field["abnamra"]."<".$field["abdstemail"].">";
                            #}
                        }
                    }
                }
                $ausgaben["mailall"] .= "-> <a href=\"".$verteiler."\">Mail an Alle</a>";
            } else {
                $ausgaben["mailall"] .= "";
            }
            // ++
            // mail the list funktion weam 1908
        } else  {
                $ausgaben["mailall"]  = "";
        }


        // navigation erstellen
        if ( $rechte[$cfg["right"]["adress"]] == -1 ) {
            $ausgaben["new"] = "<a href=\"".$cfg["basis"]."/modify,add.html\"><img src=\"".$pathvars["images"]."/button-neueadresse.png\" width=\"80\" height=\"18\" border=\"0\"></a>";
            #$aktion .= "<a href=\"".$cfg["basis"]."/".$value[0].$name.",".$field[$cfg["db"]["key"]].".html\"><img src=\"".$imgpath."/".$name.".png\" border=\"0\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></a>";
        } else {
             $ausgaben["new"] = "<img src=\"".$pathvars["images"]."/pos.png\" width=\"80\" height=\"18\" border=\"0\">";
             #$aktion .= "<img src=\"".$imgpath."/pos.png\" alt=\"\" width=\"24\" height=\"18\">";
        }

        // was anzeigen (945351025)
        $mapping["main"] = "945351025.list";
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "<font color=\"#FF0000\">ATTENTION: template overwrite -> ".$mapping["main"].".tem.html</font>".$debugging["char"];
        #$mapping["main"] = crc32($environment["ebene"]).".list";

        // wohin schicken
        $ausgaben["form_aktion"] = $cfg["basis"]."/list.html";
        $ausgaben["mask_target"] = $cfg["basis"]."/mask.html";
        #$ausgaben["eintrag_neu"] = $environment["basis"]."/modify,add.html";
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
