<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "kunden-list";
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
    #} elseif ( $environment["kategorie"] == "list" || $environment["kategorie"] == $environment["name"] ) {
        $ausgaben["adressen"] = "test";
        $position = $environment["parameter"][1]+0;
        $ausgaben["search"] = "";
        $ip_class = explode(".", $_SERVER["REMOTE_ADDR"]);

        // Schnellsuche (mor1305)
        // ***
        if ( $HTTP_GET_VARS["search"] != "" ) {
            $search_value = $HTTP_GET_VARS["search"];
            $ausgaben["search"] = $search_value;
            $ausgaben["result"] = "Ihre Schnellsuche nach \"".$search_value."\" hat ";
            $search_value = explode(" ",$search_value);
            // sql aus get vars erstellen
            $suche = array("kate","aknam","akvor","akfirma1","akfirma2","akort","akplz");
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
            $ausgaben["lokalcheck"] = "checked";
            if ($getvalues != "") $und= "&";
            $getvalues .= $und."lokal=on";
            $whereb = " (abnet='".$ip_class[1]."' AND acnet='".$ip_class[2]."')";
        } else {
            $ausgaben["lokalcheck"] = "";
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
                            if ($key == "akkate") {
                                $where .= $key."=".$value;
                            } elseif ($key == "acnet") {
                                $dstnet = explode(",",$value);
                                $where .= $key."=".$dstnet[1]." AND abnet=".$dstnet[0];
                            } elseif ($key == "abnet") {
                                $where .= $key."=".$value;
                            } else {
                                $where .= $key." LIKE '".$value."%'";
                            }
                            // suchergebnis ausgabe bauen
                            if ( $suchergebnis !="" ) $suchergebnis .= " und ";
                            if ( $key == "akkate" ) {
                                $sql = "SELECT kate FROM ".$cfg["db"]["entries_kago"]." WHERE katid = ".$HTTP_GET_VARS["akkate"];
                                $result = $db -> query($sql);
                                $field = $db -> fetch_array($result,$nop);
                                $suchergebnis .= "\"".$field["kate"]."\"";
                            } elseif ( $key == "abnet" ) {
                                $sql = "SELECT adstbfd ,adkate from db_adrd WHERE adkate=\"BFD\" AND adbnet=".$HTTP_GET_VARS["abnet"];
                                $result = $db -> query($sql);
                                $field = $db -> fetch_array($result,$nop);
                                $suchergebnis .= "\"".$field["adkate"]." ".$field["adstbfd"]."\"";
                            } elseif ( $key == "akdst") {
                                #$dstnet = explode(",",$value);
                                $sql = "SELECT adststelle, adkate from db_adrd WHERE adid =".$HTTP_GET_VARS["akdst"];
                                $result = $db -> query($sql);
                                $field = $db -> fetch_array($result,$nop);
                                $suchergebnis .= "\"".$field["adkate"]." ".$field["adststelle"]."\"";
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


        // Sql Query
        #$sql = "SELECT * FROM ".$cfg["db"]["entries"]." INNER JOIN ".$cfg["db"]["entries_kago"]." ON (akkate=katid) ".$where." ORDER by ".$cfg["db"]["order"];
        $sql = "SELECT concat( akfirma1, aknam )AS sort ,akid,akfirma1,aknam,akkate,kate,akemail,aktel,akdst FROM db_adrk INNER  JOIN db_adrk_kate ON ( akkate = katid ) ".$where." ORDER  BY sort";
        #cho $sql;
        // Inhalt Selector erstellen und SQL modifizieren
        $inhalt_selector = inhalt_selector( $sql, $position, $cfg["db"]["rows"], $parameter, 1, 10, $getvalues);

        #if ( $environment["parameter"][2] == "esearch" ) $inhalt_selector[0] = str_replace("html","html?".$search_referer,$inhalt_selector[0]);
        $ausgaben["inhalt_selector"] .= $inhalt_selector[0];
        $sql = $inhalt_selector[1];
        $ausgaben["gesamt"] = $inhalt_selector[2];

        // Daten holen und ausgeben
        $result = $db -> query($sql);

        if ( $db->num_rows($result) == 0 ) {
            $ausgaben["result"] .= " keine Einträge gefunden.<br><br>";
        } else {
            session_register($HTTP_SESSION_VARS["custom"]);
             // nur erweitern wenn bereits was drin steht
            if ( $ausgaben["result"] ) {
                $ausgaben["result"] .= " folgende Einträge gefunden.<br><br>";
            } else {
                $ausgaben["result"]  = "";
            }

            $ausgaben["output"] .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";
            $ausgaben["output"] .= "<tr>";
            $class = " class=\"lines\"";
            $ausgaben["output"] .= "<td".$class." colspan=\"12\"><img src=\"".$pathvars["images"]."pos.png\" alt=\"\" width=\"1\" height=\"1\"></td>";
            $ausgaben["output"] .= "</tr>";
            $class = " class=\"contenthead\"";
            #$size  = " width=\"30\" height=\"20\"";
            #$ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
            #$ausgaben["output"] .= "<td".$class.">&nbsp;</td>";

            $size  = " width=\"5\"";
            $ausgaben["output"] .= "<td".$class.">Firma 1</td>";
            $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
            $ausgaben["output"] .= "<td".$class.">Name</td>";
            $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
            $ausgaben["output"] .= "<td".$class.">Kategorie</td>";
            $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
            $ausgaben["output"] .= "<td".$class.">eMail</td>";
            $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
            $ausgaben["output"] .= "<td".$class.">Telefon</td>";
            $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
            $ausgaben["output"] .= "<td".$class." align=\"right\">Aktion</td>";
            $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
            $ausgaben["output"] .= "</tr><tr>";
            $class = " class=\"lines\"";
            $ausgaben["output"] .= "<td".$class." colspan=\"12\"><img src=\"".$pathvars["images"]."pos.png\" alt=\"\" width=\"1\" height=\"1\"></td>";
            $ausgaben["output"] .= "</tr>";

            $modify  = array (
                "edit"        => array("modify,", "Bearbeiten",$cfg["right"]["adress"]), ###
                "delete"      => array("modify,", "Löschen", $cfg["right"]["adress"]),
                "details"     => array("", "Details", "")
            );

            while ( $field = $db -> fetch_array($result,$nop) ) {

                $ausgaben["output"] .= "<tr>";
                $class = " class=\"contenttabs\"";
                #$size  = " width=\"30\" height=\"20\"";
                #$ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
                #$ausgaben["output"] .= "<td".$class.">&nbsp;</td>";

                $size  = " width=\"5\"";

                #$ldate = $field["ldate"];
                #$field["ldate"] = substr($ldate,8,2).".".substr($ldate,5,2).".".substr($ldate,0,4)." ".substr($ldate,11,9);
                $ausgaben["output"] .= "<td".$class."><a href=\"".$environment["basis"]."/details,".$field[$cfg["db"]["key"]].".html\">".$field["akfirma1"]."</a></td>";
                $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
                $ausgaben["output"] .= "<td".$class."><a href=\"".$environment["basis"]."/details,".$field[$cfg["db"]["key"]].".html\">".$field["aknam"]."</a></td>";
                $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
                $ausgaben["output"] .= "<td".$class."><a href=\"".$environment["basis"]."/details,".$field[$cfg["db"]["key"]].".html\">".$field["kate"]."</a></td>";
                $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
                $ausgaben["output"] .= "<td".$class."><a href=\"mailto:".$field["akemail"]."\">".str_replace($field["akemail"],(substr($field["akemail"],0,10))."...",$field["akemail"])."</a></td>";
                $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
                $ausgaben["output"] .= "<td".$class."><a href=\"".$environment["basis"]."/details,".$field[$cfg["db"]["key"]].".html\">".$field["aktel"]."</a></td>";
                $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";

                $aktion = "";
                foreach($modify as $name => $value) {
                    if ( $rechte[$value[2]] == -1 && $HTTP_SESSION_VARS["custom"] == $field["akdst"] || $value[2] == "") {
                        $aktion .= "<a href=\"".$environment["basis"]."/".$value[0].$name.",".$field[$cfg["db"]["key"]].".html\"><img src=\"".$pathvars["images"].$name.".png\" border=\"0\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></a>";
                    } elseif ( $rechte[$value[2]] == -1 && $name == "edit") {
                        $aktion .= "<a href=\"".$environment["basis"]."/".$value[0].$name.",".$field[$cfg["db"]["key"]].".html\"><img src=\"".$pathvars["images"].$name."a.png\" border=\"0\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></a>";
                    } else {
                        $aktion .= "<img src=\"".$pathvars["images"]."pos.png\" alt=\"\" width=\"24\" height=\"18\">";
                    }
                }
                $ausgaben["output"] .= "<td".$class." align=\"right\">".$aktion."</td>";
                $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";


                $ausgaben["output"] .= "</tr><tr>";
                $class = " class=\"lines\"";
                $ausgaben["output"] .= "<td".$class." colspan=\"12\"><img src=\"".$pathvars["images"]."pos.png\" alt=\"\" width=\"1\" height=\"1\"></td>";
                $ausgaben["output"] .= "</tr>";

            }
            $ausgaben["output"] .= "</table>";
        }

        // navigation erstellen
        if ( $rechte[$cfg["right"]["adress"]] == -1 ) {
            $ausgaben["new"] = "<a href=\"".$environment["basis"]."/modify,add.html\"><img src=\"".$pathvars["images"]."button-neueadresse.png\" width=\"80\" height=\"18\" border=\"0\"></a>";
            #$aktion .= "<a href=\"".$environment["basis"]."/".$value[0].$name.",".$field[$cfg["db"]["key"]].".html\"><img src=\"".$imgpath."/".$name.".png\" border=\"0\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></a>";
        } else {
            $ausgaben["new"] = "<img src=\"".$pathvars["images"]."/pos.png\" width=\"80\" height=\"18\" border=\"0\">";
            #$aktion .= "<img src=\"".$imgpath."/pos.png\" alt=\"\" width=\"24\" height=\"18\">";
        }

        // was anzeigen
        $mapping["main"] = crc32($environment["ebene"]).".list";
        #$mapping["navi"] = "navi";
        #$mapping["navi"] = "leer";

        // wohin schicken
        #$ausgaben["print_url"] = "http://www.bvv.bayern.de";#$environment["ebene"];
        $ausgaben["form_aktion"] = $environment["basis"]."/list.html";
        $ausgaben["mask_target"] = $environment["basis"]."/mask.html";
       # $ausgaben["eintrag_neu"] = $environment["basis"]."/modify,add.html";

    #}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
