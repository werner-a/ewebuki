<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "Dienststellen delete";
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


    if ( $environment["parameter"][1] == "delete" && $rechte[$cfg["right"]["adress"]] == -1 ) {

        $sql = "SELECT * FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][2]."'";
        $result = $db -> query($sql);
        $field = $db -> fetch_array($result,$nop);
        foreach($field as $name => $value) {
            $ausgaben[$name] = $value;
        }

        // amtsleiter daten holen und variablen bauen weam 1205
        // ***
        $sql = "SELECT abtitel,
                       abnamvor,
                       abnamra,
                       abamtbezlang as abamtbez,
                       abdsttel,
                       abdstfax,
                       abdstfax,
                       abdstmobil,
                       abdstemail
                FROM db_adrb INNER JOIN db_adrb_amtbez ON (abamtbez = abamtbez_id)
                WHERE abid='".$ausgaben["adleiter"]."'" ;
        $result = $db -> query($sql);
        $data = $db -> fetch_array($result,$nop);
        if ( is_array($data) ){
            foreach($data as $key => $value) {
                $ausgaben[$key] = $value;
            }
        } else {
            $ausgaben["abtitel"] = "--";
            $ausgaben["abnamvor"] = "";
            $ausgaben["abnamra"] = "";
            $ausgaben["abamtbez"] = "--";
            $ausgaben["abdsttel"] = "--";
            $ausgaben["abdstfax"] = "--";
            $ausgaben["abdstmobil"] = "--";
            $ausgaben["abdstemail"] = "--";
        }
        // +++
        // amtsleiter daten holen und variablen bauen weam 1205

        // redaktion daten holen und variablen bauen weam 1205
        // ***
        for ( $i = 1; $i <= 2; $i++ ) {
            $sql = "SELECT abnamra as adwebname,
                           abnamvor as adwebvorname,
                           abdsttel as adwebtel,
                           abdstemail as adwebemail
                    FROM db_adrb where abid='".$ausgaben["adwebmid".$i]."'" ;
            $result = $db -> query($sql);
            $data = $db -> fetch_array($result,$nop);
            if ( is_array($data) ){
               foreach($data as $key => $value) {
                    $ausgaben[$key.$i] = $value;
               }
            } else {
                $ausgaben["adwebname".$i] = "--";
                $ausgaben["adwebvorname".$i] = "--";
                $ausgaben["adwebtel".$i] = "--";
                $ausgaben["adwebemail".$i] = "--";
            }
        }
        // +++
        // redaktion daten holen und variablen bauen weam 1205


        // was anzeigen
        $mapping["main"] = crc32($environment["ebene"]).".delete";
        $mapping["navi"] = "leer";

        // wohin schicken
        $ausgaben["form_aktion"] = $environment["basis"]."/modify,delete,".$environment["parameter"][2].".html";
        $ausgaben["form_break"] = $_SERVER["HTTP_REFERER"];

        if ( $HTTP_POST_VARS["delete"] == "true" ) {
            $sql = "DELETE FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][2]."'";
            $result  = $db -> query($sql);
            header("Location: ".$environment["basis"]."/list.html");
        }
    #}
    // wa 1707
    } else {
        header("Location: ".$pathvars["webroot"]."/".$environment["design"]."/".$environment["language"]."/index.html");
        }



////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
