<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id$";
  $Script["desc"] = "gesetze+verwaltung-list";
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

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** ".$script["name"]." ** ]".$debugging["char"];

    // Liste anzeigen
    //
    $position = $environment["parameter"][1]+0;

    $ausgaben["search"] = "";
    $ip_class = explode(".", $_SERVER["REMOTE_ADDR"]);

    // form options holen
    $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

    // form elememte bauen
    $element = form_elements( $cfg["db"]["entries"], $form_values );

    // dropdown vorselektieren
    $element["g_kat"] = str_replace("value=\"".$HTTP_GET_VARS["g_kat"]."\"","value=\"".$HTTP_GET_VARS["g_kat"]."\" selected", $element["g_kat"]);

    // Schnellsuche (mor1305)
    // ***
    if ( $HTTP_GET_VARS["search"] != "" ) {
        $search_value = $HTTP_GET_VARS["search"];
        $ausgaben["search"] = $search_value;
        $ausgaben["result"] = "Ihre Schnellsuche nach \"".$search_value."\" hat ";
        $search_value = explode(" ",$search_value);
        // sql aus get vars erstellen
        $suche = array("gv_beschr","gv_bez","g_kat","gv_gliedernr","gv_quelle");
        $where = "";

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
        #$where = " WHERE ".$where;

    }
    // +++
    // Schnellsuche (mor1305)

    // Erweiterte Suche (mor 2404)
    // ***
    if ( $HTTP_GET_VARS["g_kat"] != "") {
            $kick = array( "image", "image_x", "image_y", "esearch", "search");
            foreach ($HTTP_GET_VARS as $key => $value) {
                if ( !in_array($key,$kick)  ) {
                    if ( $value != "" ) {
                            if ($getvalues != "") $getvalues .= "&";
                            $getvalues .= $key."=".$value;
                            if ($value == "Alle") {
                                $whereb = "";
                                continue;
                            }
                            $whereb .= $key."='".$value."'";

                        } else {
                            $suchergebnis .= "\"".$value."\"";
                     }
                }
            }
            $getvalues .= "&esearch=true";
            if ( $whereb != "" ) {
                $ausgaben["result"] = "Ihre Erweiterte Suche nach ".$suchergebnis." hat ";
                #$whereb = " WHERE ".$whereb;
            }
    }
    // +++
    // Erweiterte Suche (mor 2404)

    // gibt es beide
    if ($wherea && $whereb) $trenner = " AND ";
    // ist wherea da klammern setezn
    if ($wherea) $wherea = "(".$wherea.")";
    // where zusammensetzen
    if ($wherea || $whereb) $where = " WHERE ".$wherea.$trenner.$whereb;


    #echo $where;
    // sortierreihenfolge festlegen
    if (!$cfg["db"]["order"][$HTTP_GET_VARS["g_kat"]]) {
        $sort = "gv_sort";
    } else {
        $sort = $cfg["db"]["order"][$HTTP_GET_VARS["g_kat"]];
    }

    // Sql Query
    $sql = "SELECT * FROM ".$cfg["db"]["entries"].$where." ORDER by ".$sort;
    #echo $sql;

    // Kategorie ausgeben
    if ($HTTP_GET_VARS["g_kat"] != "") $suche1 = "Kategorie: ".$HTTP_GET_VARS["g_kat"];
    if ($HTTP_GET_VARS["search"] != "") $suche2 = "<br> Schnellsuche nach: ".$HTTP_GET_VARS["search"];
    $ausgaben["result"] = $suche1.$suche2;

    // Inhalt Selector erstellen und SQL modifizieren
    $inhalt_selector = inhalt_selector( $sql, $position, $cfg["db"]["rows"], $parameter, 1, 10, $getvalues );  # neu mit get
    //$inhalt_selector = inhalt_selector( $sql, $position, $db_rows, $parameter, 1, 10 );            # neu mit get
    $ausgaben["inhalt_selector"] .= $inhalt_selector[0];
    $sql = $inhalt_selector[1];
    $ausgaben["gesamt"] = $inhalt_selector[2];

    // head spulen
    $ausgaben["output"] .= parser( "-900193709.list-head", "");

    // query absetzen und variablen bauen
    $result = $db -> query($sql);
    $modify  = array (
        "edit"        => array("modify,", "Editieren", $cfg["right"]["adress"]), ###
        "delete"      => array("", "Löschen", $cfg["right"]["adress"])
        #"details"     => array("", "Details", "")
    );
    $imgpath = $pathvars["images"];

    // Daten holen, row spulen
    if ( $db->num_rows($result) == 0 ) {
        $ausgaben["output"] .= " keine Einträge gefunden.<br><br>";
    } else {

        // daten holen, row spulen
        while ( $data = $db -> fetch_array($result,$nop) ) {

            $ausgaben["dbjn"] = "Ja";
            foreach($data as $key => $value) {
                $$key = $value;
            }
            //ausgabe ob in jurisdb
            if(strstr($gv_url,"8081") || strstr($gv_url,"www.bayernrecht.bybn.de")) {
                $ausgaben["dbjn"] = "Ja";
            } else {
                $ausgaben["dbjn"] = "Nein";
            }
            // bezeichnung verlinken
            if ($gv_url != "") {
                 $gv_bez = "<a href=\"".$gv_url."\">".$gv_bez."</a>";
            }
            // aktionen erstellen
            $aktion = "";
            foreach($modify as $name => $value) {
                #$aktion .= "<a href=\"".$cfg["basis"]."/".$cfg["ebene"]["zwei"]."/".$value[0].$name.",".$data[$cfg["db"]["key"]].".html\"><img src=\"".$imgpath."/".$name.".png\" border=\"0\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></a>";
                if ( $rechte[$value[2]] == -1 || $value[2] == "" ) {
                    $aktion .= "<a href=\"".$cfg["basis"]."/".$value[0].$name.",".$data[$cfg["db"]["key"]].".html\"><img src=\"".$imgpath."/".$name.".png\" border=\"0\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></a>";
                #} elseif ( $rechte["cms_admin"] == -1 && $name == "edit" ) {
                #    $aktion .= "<a href=\"".$environment["basis"]."/".$value[0].$name.",".$field[$db_entries_key].".html\"><img src=\"".$imgpath."/".$name."a.png\" border=\"0\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></a>";
                } else {
                    $aktion .= "<img src=\"".$imgpath."/pos.png\" alt=\"\" width=\"24\" height=\"18\">";
                }

            }
            $ausgaben["output"] .= parser( "-900193709.list-row", "");
        }
    }

    // navigation erstellen
    if ( $rechte[$cfg["right"]["adress"]] == -1 ) {
        $ausgaben["new"] = "<a href=\"".$cfg["basis"]."/modify,add.html\"><img src=\"".$pathvars["images"]."/button-gv-neu.png\" width=\"80\" height=\"18\" border=\"0\"></a>";
        #$aktion .= "<a href=\"".$environment["basis"]."/".$value[0].$name.",".$field[$db_entries_key].".html\"><img src=\"".$imgpath."/".$name.".png\" border=\"0\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></a>";
    } else {
        $ausgaben["new"] = "<img src=\"".$pathvars["images"]."/pos.png\" width=\"80\" height=\"18\" border=\"0\">";
        #$aktion .= "<img src=\"".$imgpath."/pos.png\" alt=\"\" width=\"24\" height=\"18\">";
    }

    // was anzeigen
    $mapping["main"] = crc32($environment["ebene"]).".list";
    $mapping["navi"] = "leer";

    // wohin schicken
    #$ausgaben["print_url"] = "http://www.bvv.bayern.de";#$environment["ebene"];
    $ausgaben["form_aktion"] = $cfg["basis"]."/list.html";

    // gueltigkeitsverzeichnis link
    $ausgaben["mask_target"] = "?hijack=http://fmwelt.stmf.bybn.de/service/informationsbroschueren/sonstige/gueltigkeitsverzeichnis/gueltigkeitsverzeichnis.pdf";

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
