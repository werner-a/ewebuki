<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "short description";
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

    if (( $environment["parameter"][0] == "delete" ) && ($rechte[$cfg["right"]["adress"]] == -1)) {
        $ausgaben["form_error"] = "";

        $sql = "SELECT * FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][1]."'";
        $result = $db -> query($sql);
        $field = $db -> fetch_array($result,$nop);

        // Ausgeben
        // ***
        foreach( $field as $key => $value) {
            if ( $value == "") $value ="--";
            $ausgaben[$key] = $value;
        }
        // +++
        // Ausgeben

        // was anzeigen
        $mapping["main"] = crc32($environment["ebene"]).".delete";
        $mapping["navi"] = "leer";

      // referer im form mit hidden element mitschleppen
        if ( $HTTP_POST_VARS["form_referer"] == "" ) {
            $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        } else {
            $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        }

        // wohin schicken
        $ausgaben["form_aktion"] = $cfg["basis"]."/delete,".$environment["parameter"][1].".html";
        $ausgaben["form_break"] = $ausgaben["form_referer"]; // Nun mit Post im hidden element (mor 0605)

        if ( $HTTP_POST_VARS["delete"] == "true" ) {
            $sql = "DELETE FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][1]."'";
            $result  = $db -> query($sql);
            #echo $sql;
            header("Location: ".$cfg["basis"]."/list.html");
        }
    } else {
        #header("Location: ".$pathvars["webroot"]."/".$environment["design"]."/".$environment["language"]."/index.html");
        #$ausgaben["output"] .= "ZUGRIFF VERWEIGERT";
    }

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ $script_name ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
