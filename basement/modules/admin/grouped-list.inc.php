<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "grouped - list funktion";
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

    if ( $cfg["grouped"]["right"] == "" || priv_check('', $cfg["grouped"]["right"] ) ) {

        // funktions bereich
        // ***

        // get-verarbeitung: schnellsuche verarbeiten
        $ausgaben["search"] = "";
        $where = "";
        if ( isset($_GET["search"]) ) {
            $ausgaben["search"] = $_GET["search"];
            $where = " WHERE ".$cfg["grouped"]["db"]["group"]["desc"]." like '%".$_GET["search"]."%' OR ".$cfg["grouped"]["db"]["group"]["order"]." like '%".$_GET["search"]."%'";
            $getvalues = "search=".$_GET["search"];
        }


        /* db query */
        $sql = "SELECT *
                  FROM ".$cfg["grouped"]["db"]["group"]["entries"].$where."
              ORDER BY ".$cfg["grouped"]["db"]["group"]["order"];

        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];

        // seiten umschalter
        $inhalt_selector = inhalt_selector( $sql, $environment["parameter"][1], $cfg["grouped"]["db"]["group"]["rows"], $parameter, 1, 3, $getvalues );
        $ausgaben["inhalt_selector"] = $inhalt_selector[0]."<br />";
        $sql = $inhalt_selector[1];
        $ausgaben["anzahl"] = $inhalt_selector[2];
        $result = $db -> query($sql);

        while ( $data = $db -> fetch_array($result,1) ) {
            // tabellen farben wechseln
            if ( $cfg["grouped"]["color"]["set"] == $cfg["grouped"]["color"]["a"]) {
                $cfg["grouped"]["color"]["set"] = $cfg["grouped"]["color"]["b"];
            } else {
                $cfg["grouped"]["color"]["set"] = $cfg["grouped"]["color"]["a"];
            }
            $dataloop["list"][] = array(
                        "color" => $cfg["grouped"]["color"]["set"],
                  "bezeichnung" => $data[$cfg["grouped"]["db"]["group"]["order"]],
                 "beschreibung" => $data[$cfg["grouped"]["db"]["group"]["desc"]],
                     "editlink" => $cfg["grouped"]["basis"]."/edit,".$data[$cfg["grouped"]["db"]["group"]["key"]].".html",
                    "edittitel" => "#(edittitel)",
                   "deletelink" => $cfg["grouped"]["basis"]."/delete,".$data[$cfg["grouped"]["db"]["group"]["key"]].".html",
                  "deletetitel" => "#(deletetitel)",
                   "detaillink" => $cfg["grouped"]["basis"]."/details,".$data[$cfg["grouped"]["db"]["group"]["key"]].".html",
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
        $ausgaben["link_new"] = $cfg["grouped"]["basis"]."/add.html";

        // hidden values
        #$ausgaben["form_hidden"] .= "";

        // was anzeigen
        $cfg["grouped"]["path"] = str_replace($pathvars["virtual"],"",$cfg["grouped"]["basis"]);
        $mapping["main"] = eCRC($cfg["grouped"]["path"]).".list";
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
