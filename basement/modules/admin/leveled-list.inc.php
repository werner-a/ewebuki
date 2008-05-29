<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "leveled - list funktion";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2006 Werner Ammon ( wa<at>chaos.de )

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

      if ( $cfg["leveled"]["right"] == "" ||
        priv_check("/".$cfg["leveled"]["subdir"]."/".$cfg["leveled"]["name"],$cfg["leveled"]["right"]) ||
        priv_check_old("",$cfg["leveled"]["right"]) ) {

        // funktions bereich
        // ***

        /* db query */
        $sql = "SELECT *
                  FROM ".$cfg["leveled"]["db"]["level"]["entries"]."
              ORDER BY ".$cfg["leveled"]["db"]["level"]["order"];

        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];

        // seiten umschalter
        $inhalt_selector = inhalt_selector( $sql, $environment["parameter"][1], $cfg["leveled"]["db"]["level"]["rows"], $parameter, 1, 3, $getvalues );
        $ausgaben["inhalt_selector"] = $inhalt_selector[0]."<br />";
        $sql = $inhalt_selector[1];
        $ausgaben["anzahl"] = $inhalt_selector[2];
        $result = $db -> query($sql);
        while ( $data = $db -> fetch_array($result,1) ) {
            // tabellen farben wechseln
            if ( $cfg["leveled"]["color"]["set"] == $cfg["leveled"]["color"]["a"]) {
                $cfg["leveled"]["color"]["set"] = $cfg["leveled"]["color"]["b"];
            } else {
                $cfg["leveled"]["color"]["set"] = $cfg["leveled"]["color"]["a"];
            }
            $dataloop["list"][] = array(
                        "color" => $cfg["leveled"]["color"]["set"],
                  "bezeichnung" => $data[$cfg["leveled"]["db"]["level"]["order"]],
                 "beschreibung" => $data[$cfg["leveled"]["db"]["level"]["desc"]],
                     "editlink" => $cfg["leveled"]["basis"]."/edit,".$data[$cfg["leveled"]["db"]["level"]["key"]].".html",
                    "edittitel" => "#(edittitel)",
                   "deletelink" => $cfg["leveled"]["basis"]."/delete,".$data[$cfg["leveled"]["db"]["level"]["key"]].".html",
                  "deletetitel" => "#(deletetitel)",
                   "detaillink" => $cfg["leveled"]["basis"]."/details,".$data[$cfg["leveled"]["db"]["level"]["key"]].".html",
                  "detailtitel" => "#(detailtitel)",
            );
        }

        // +++
        // funktions bereich


        // page basics
        // ***

        // fehlermeldungen
        if ( $HTTP_GET_VARS["error"] != "" ) {
            if ( $HTTP_GET_VARS["error"] == 1 ) {
                $ausgaben["form_error"] = "#(error1)";
            }
        } else {
            $ausgaben["form_error"] = "";
        }

        // navigation erstellen
        $ausgaben["link_new"] = $cfg["leveled"]["basis"]."/add.html";

        // hidden values
        #$ausgaben["form_hidden"] .= "";

        // was anzeigen
        $cfg["leveled"]["path"] = str_replace($pathvars["virtual"],"",$cfg["leveled"]["basis"]);
        $mapping["main"] = eCRC($cfg["leveled"]["path"]).".list";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
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