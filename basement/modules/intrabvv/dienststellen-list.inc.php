<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "Dienststellen Liste";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    phpWEBkit - a easy website building kit
    Copyright (C)2001, 2002, 2003 Werner Ammon <wa@chaos.de>

    This script is a part of phpWEBkit

    phpWEBkit is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    phpWEBkit is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with phpWEBkit; If you did not, you may download a copy at:

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


    // Liste anzeigen
    //
    if ( $environment["kategorie"] == "list" || $environment["kategorie"] == $environment["name"] ) {

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
            $suche = array("adststelle","adkate","adstbfd","adakz");
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
        // sql um lokal erweitern
        #if ( $HTTP_GET_VARS["lokal"] == "on") {
        #    $getvalues .= "&lokal=on";
        #    $whereb = " (akbnet='".$ip_class[1]."' AND akcnet='".$ip_class[2]."')";
        #}
        // gibt es beide
        #if ($wherea && $whereb) $trenner = " AND ";
        // ist wherea da klammern setezn
        #if ($wherea) $wherea = "(".$wherea.")";
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
                            #if ($key == "akkate") {
                            #    $where .= $key."=".$value;
                            #} elseif ($key == "acnet") {
                            #    $dstnet = explode(",",$value);
                            #    $where .= $key."=".$dstnet[1]." AND abnet=".$dstnet[0];
                            #} elseif ($key == "abnet") {
                            #    $where .= $key."=".$value;
                            #} else {
                                $where .= $key." LIKE '%".$value."%'";
                            #}
                            // suchergebnis ausgabe bauen
                            if ( $suchergebnis !="" ) $suchergebnis .= " und ";
                            if ( $key == "adleiter" ) {
                                $sql = "SELECT abnamra, abnamvor from db_adrb WHERE abid = ".$HTTP_GET_VARS["adleiter"];
                                $result = $db -> query($sql);
                                $field = $db -> fetch_array($result,$nop);
                                $suchergebnis .= "\"".$field["abnamra"]."".$field["abnamvor"]."\"";
                            #} elseif ( $key == "abnet" ) {
                            #    $sql = "SELECT adstbfd ,adkate from db_adrd WHERE adkate=\"BFD\" AND adbnet=".$HTTP_GET_VARS["abnet"];
                            #    $result = $db -> query($sql);
                            #    $field = $db -> fetch_array($result,$nop);
                            #    $suchergebnis .= "\"".$field["adkate"]." ".$field["adstbfd"]."\"";
                            #} elseif ( $key == "acnet") {
                            #    $dstnet = explode(",",$value);
                            #   $sql = "SELECT adststelle, adkate from db_adrd WHERE adbnet=".$dstnet[0]." AND adcnet=".$dstnet[1];
                            #    $result = $db -> query($sql);
                            #    $field = $db -> fetch_array($result,$nop);
                            #    $suchergebnis .= "\"".$field["adkate"]." ".$field["adststelle"]."\"";
                            } else {
                                $suchergebnis .= "\"".$value."\"";
                            }
                        }
                    }
                }
                $getvalues .= "&esearch=true";
                if ( $where != "" ) {
                    $ausgaben["result"] = "Ihre Erweiterte Suche nach ".$suchergebnis." hat ";
                    $where = " WHERE (".$where.")";
                }
        }
        // +++
        // Erweiterte Suche (mor 2404)


        // Sql Query
        $sql = "SELECT * FROM ".$cfg["db"]["entries"].$where." ORDER by ".$cfg["db"]["order"];

        // Inhalt Selector erstellen und SQL modifizieren
        $inhalt_selector = inhalt_selector( $sql, $position, $cfg["db"]["rows"], $parameter, 1, 10, $getvalues );  # neu mit get
        //$inhalt_selector = inhalt_selector( $sql, $position, $cfg["db"]["rows"], $parameter, 1, 10 );            # neu mit get
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
            $ausgaben["output"] .= "<td".$class." colspan=\"12\"><img src=\"".$pathvars["images"]."pos.png\" alt=\"\" width=\"1\" height=\"1\"></td>";
            $ausgaben["output"] .= "</tr>";
            $class = " class=\"contenthead\"";
            #$size  = " width=\"30\" height=\"20\"";
            #$ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
            #$ausgaben["output"] .= "<td".$class.">&nbsp;</td>";
            $size  = " width=\"5\"";
            $ausgaben["output"] .= "<td".$class.">Dienststelle</td>";
            $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
            $ausgaben["output"] .= "<td".$class.">Kategorie</td>";
            $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
            $ausgaben["output"] .= "<td".$class.">BFD</td>";
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
                "edit"        => array("modify,", "Editieren", $cfg["right"]["adress"]), ###
                #"delete"      => array("modify,", "Löschen", $cfg["right"]["adress"]),
                "details"     => array("", "Details", "")
            );
            $imgpath = $pathvars["images"];

            while ( $field = $db -> fetch_array($result,$nop) ) {

                $ausgaben["output"] .= "<tr>";
                $class = " class=\"contenttabs\"";
                #$size  = " width=\"30\" height=\"20\"";
                #$ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
                #$ausgaben["output"] .= "<td".$class.">&nbsp;</td>";

                $size  = " width=\"5\"";

                #$ldate = $field["ldate"];
                #$field["ldate"] = substr($ldate,8,2).".".substr($ldate,5,2).".".substr($ldate,0,4)." ".substr($ldate,11,9);
                $ausgaben["output"] .= "<td".$class."><a href=\"".$environment["basis"]."/details,".$field[$cfg["db"]["key"]].".html\">".$field["adststelle"]."</a></td>";
                $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";


                $ausgaben["output"] .= "<td".$class."><a href=\"".$environment["basis"]."/details,".$field[$cfg["db"]["key"]].".html\">".$field["adkate"]."</a></td>";
                $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
                $ausgaben["output"] .= "<td".$class.">".$field["adstbfd"]."</td>";

                $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
                $ausgaben["output"] .= "<td".$class."><a href=\"mailto:".$field["ademail"]."\">".$field["ademail"]."</a></td>";
                #$ausgaben["output"] .= "<td".$class."><a href=\"mailto:".$field["abdstemail"]."\">".$field["ademail"]."</a></td>";
                $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
                $ausgaben["output"] .= "<td".$class.">".$field["adtelver"]."</td>";
                $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";


                $aktion = "";
                foreach($modify as $name => $value) {
                    if ( $value[2] == ""
                            || $rechte[$value[2]] == -1
                            && $HTTP_SESSION_VARS["custom"] == $field["adid"]) {
                        $aktion .= "<a href=\"".$environment["basis"]."/".$value[0].$name.",".$field[$cfg["db"]["key"]].".html\"><img src=\"".$imgpath.$name.".png\" border=\"0\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></a>";
                    } else {
                        $aktion .= "<img src=\"".$imgpath."/pos.png\" alt=\"\" width=\"24\" height=\"18\">";
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

        // wohin schicken
        $ausgaben["form_aktion"] = $environment["basis"]."/list.html";
        $ausgaben["mask_target"] = $environment["basis"]."/mask.html";


    }


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
