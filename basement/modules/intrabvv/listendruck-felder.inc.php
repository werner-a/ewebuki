<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  "$Id$";
//  "anwender kann felder fuer listen waehlen";
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

    // beschriftung
    foreach ( $cfg["desc"]["default"] as $key => $value ) {
        if ( isset($cfg["desc"][$environment["parameter"][2]][$key]) ) {
            $ausgaben[$key] = $cfg["desc"][$environment["parameter"][2]][$key];
        } else {
            $ausgaben[$key] = $value;
        }
    }

    // bild
    $ausgaben["bildvorschau"] = $environment["parameter"][2].".png";

    // form eigenschaften
    $ausgaben["form_methode"] = "GET";
    $ausgaben["form_aktion"] = $cfg["basis"]."/print,".$environment["parameter"][2].".html";
    $ausgaben["form_break"] = $cfg["basis"].".html";

    // checkboxen
    $gesamt = count($cfg["field"][$environment["parameter"][2]]);
    $menge = $gesamt / 2;
    $seite = "";
    $ausgaben["check"]  = "<table width=\"100%\">\n";
    foreach ( $cfg["field"][$environment["parameter"][2]] as $name => $value ) {
        if ( $i < $menge ) {
            $seite = "links";
        } else {
            $seite = "rechts";
        }
        $i++;
        if ( $value[2] == "required" ) {
            $$seite .= "<td><input type=\"hidden\" name=\"".$name."\" value=\"1\"><img src=\"".$pathvars["images"]."checked.png\" align=\"middle\" alt=\"\" width=\"21\" height=\"21\"></td><td>".$value[0]."</td>";
        } else {
            $$seite .= "<td><input type=\"checkbox\" name=\"".$name."\" value=\"1\" ".$value[1]."></td><td>".$value[0]."</td>";
        }

        $rest = $i /2;
        if ( is_int($rest) ) $$seite .= "</tr><tr>\n";
    }
    $ausgaben["check"] .= "<tr>\n".$links;
    $ausgaben["check"] .= $rechts."\n</tr>\n";
    $ausgaben["check"] .= "</table>\n";


    // dropdown
    $selected = "";
    $ip_class = explode(".", $_SERVER["REMOTE_ADDR"]);

    // dropdown bei dststelle deaktiviert
    if ( $environment["parameter"][2] != "dststelle" ) {

        // externe listen nur fuer va
        if ( $environment["parameter"][2] == "telext" ) {
            $sql = "SELECT adid, adbnet, adcnet, adkate, adststelle FROM db_adrd WHERE adkate = 'va' ORDER BY adsort,adststelle";
        } else {
            $sql = "SELECT adid, adbnet, adcnet, adkate, adststelle FROM db_adrd ORDER BY adsort,adststelle";
        }

        $result = $db -> query($sql);
        $ausgaben["dropdown"] .= "<select name=\"adid\">";
        while ( $data = $db->fetch_array($result,$nop) ) {
            if ( ($data["adcnet"] == $ip_class[2]) &&  ($data["adbnet"] == $ip_class[1])) {
                $selected = "selected";
            } else {
                $selected = "";
            }
            $ausgaben["dropdown"] .= "<option value=\"".$data["adid"]."\"".$selected.">". $data["adkate"]." ".$data["adststelle"]."</option>";
        }
        $ausgaben["dropdown"] .= "</select>";

    } else {
        $ausgaben["dropdown"] .= "";
    }


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
