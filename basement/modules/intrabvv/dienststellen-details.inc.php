<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "Dienststellen details";
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


    //
    // Details anzeigen
    //
    if ( $environment["kategorie"] == "details" ) {

        // ausgaben daten holen und variablen bauen
        $sql = "SELECT * FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][1]."'";
        $result = $db -> query($sql);
        $data = $db -> fetch_array($result,$nop);
        foreach($data as $key => $value) {
            $ausgaben[$key] = $value;
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


        // email und links mit href versehen weam 1405
        // ***
        $modify  = array ("ademail"     => "m",
                          "abdstemail"  => "m",
                          "adwebemail1" => "m",
                          "adwebemail2" => "m",
                          "adinternet"  => "u",
                          "adintranet"  => "u"
                         );
        foreach($modify as $key => $value) {
            if ( $value == "m" ) {
                $mailto = "mailto:";
            } else {
                $mailto = "";
            }
            if ( $ausgaben[$key] != "--" ) $ausgaben[$key] = "<a href=\"".$mailto.$ausgaben[$key]."\">".$ausgaben[$key]."</a>";
        }
        // +++
        // email und links mit href versehen weam 1405


        // navigation erstellen
        $ausgaben["print_url"] = $environment["basis"]."/print,dienststellen,details,".$environment["parameter"][1].".html";
        $ausgaben["navigation"] .= "<a href=\"".$_SERVER["HTTP_REFERER"]."\"><img src=\"".$pathvars["images"]."left.png\" border=\"0\" alt=\"Zurück\" title=\"Zurück\" width=\"24\" height=\"18\"></a>";
        $ausgaben["navigation"] .= "<a href=\"".$environment["basis"]."/print,".$environment["parameter"][1].".html\"><img src=\"".$pathvars["images"]."druck.png\" border=\"0\" alt=\"Zurück\" title=\"Details drucken\" width=\"24\" height=\"18\"></a>";

        #if ( $rechte[$cfg["right"]["adress"]] == -1 ) {
        if ( $rechte[$cfg["right"]["adress"]] == -1 && $HTTP_SESSION_VARS["custom"] == $ausgaben["adid"]) {
            $ausgaben["navigation"] .= "<a href=\"".$environment["basis"]."/modify,edit,".$environment["parameter"][1].".html\"><img src=\"".$pathvars["images"]."edit.png\" border=\"0\" alt=\"Bearbeiten\" title=\"Bearbeiten\" width=\"24\" height=\"18\"></a>";
        } else {
            #$ausgaben["navigation"] .= "<img src=\"".$pathvars["images"]."/pos.png\" border=\"0\" alt=\"\" width=\"24\" height=\"18\">";
        }

        // was anzeigen
        $mapping["main"] = crc32($environment["ebene"]).".details";
    }


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
