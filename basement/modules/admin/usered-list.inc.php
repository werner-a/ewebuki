<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "leer - list funktion";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2007 Werner Ammon ( wa<at>chaos.de )

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

    if ( $cfg["usered"]["right"] == "" ||
        priv_check("/".$cfg["usered"]["subdir"]."/".$cfg["usered"]["name"],$cfg["usered"]["right"]) ||
        priv_check_old("",$cfg["usered"]["right"]) ) {

        // funktions bereich
        // ***

        $sql = "SELECT *
                  FROM ".$cfg["usered"]["db"]["user"]["entries"]."
              ORDER BY ".$cfg["usered"]["db"]["user"]["order"];
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];

        // seiten umschalter
        $inhalt_selector = inhalt_selector( $sql, $environment["parameter"][1], $cfg["usered"]["db"]["user"]["rows"], $parameter, 1, 3, $getvalues );
        $ausgaben["inhalt_selector"] = $inhalt_selector[0]."<br />";
        $sql = $inhalt_selector[1];
        $ausgaben["anzahl"] = $inhalt_selector[2];

        $result = $db -> query($sql);
        while ( $data = $db -> fetch_array($result,1) ) {

            // tabellen farben wechseln
            if ( $cfg["usered"]["color"]["set"] == $cfg["usered"]["color"]["a"]) {
                $cfg["usered"]["color"]["set"] = $cfg["usered"]["color"]["b"];
            } else {
                $cfg["usered"]["color"]["set"] = $cfg["usered"]["color"]["a"];
            }

            $dataloop["user"][] = array(
                        "login" => $data[$cfg["usered"]["db"]["user"]["login"]],
                     "forename" => $data[$cfg["usered"]["db"]["user"]["forename"]],
                      "surname" => $data[$cfg["usered"]["db"]["user"]["surname"]],
                         "edit" => $cfg["usered"]["basis"]."/edit,".$data[$cfg["usered"]["db"]["user"]["key"]].".html",
                       "delete" => $cfg["usered"]["basis"]."/delete,".$data[$cfg["usered"]["db"]["user"]["key"]].".html",
                      "details" => $cfg["usered"]["basis"]."/details,".$data[$cfg["usered"]["db"]["user"]["key"]].".html",
                        "color" => $cfg["usered"]["color"]["set"]
            );
        }

        // +++
        // funktions bereich


        // page basics
        // ***

        // fehlermeldungen
        if ( $_GET["error"] != "" ) {
            if ( $_GET["error"] == 1 ) {
                $ausgaben["form_error"] = "#(error1)";
            }
        } else {
            $ausgaben["form_error"] = "";
        }

        // navigation erstellen
        $ausgaben["link_new"] = $cfg["usered"]["basis"]."/add.html";

        // was anzeigen
        $cfg["usered"]["path"] = str_replace($pathvars["virtual"],"",$cfg["usered"]["basis"]);
        $mapping["main"] = eCRC($cfg["usered"]["path"]).".list";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($_GET["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error1) #(error1)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        #n/a

        // +++
        // page basics

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
