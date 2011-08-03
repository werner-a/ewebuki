<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "prived - list funktion";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2010 Werner Ammon ( wa<at>chaos.de )

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

    86343 K�nigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( $cfg["prived"]["right"] == "" || priv_check('', $cfg["prived"]["right"] ) ) {

        // funktions bereich
        // ***

        // suche
        // ***

        // get-verarbeitung: schnellsuche verarbeiten
        if ( isset($_GET["search"]) ) {
            $_SESSION["prived_position"] = 0;
            $_SESSION["prived_search"] = $_GET["search"];
        } elseif ( isset($_GET["search"]) && $_GET["search"] == "" ) {
            unset($_SESSION["prived_search"]);
        }

        // umleitung, damit die Get-Vars wieder weg sind
        if ( count($_GET) > 0 && !isset($_GET["edit"]) ) {
            $header = $cfg["prived"]["basis"]."/list.html";
            header("Location: ".$header);
        }

        // suche verarbeiten
        if ( $_SESSION["prived_search"] ) {
            $ausgaben["search"] = $_SESSION["prived_search"];
            $filters[] = $_SESSION["prived_search"];
            $array1 = explode( " ", $_SESSION["prived_search"] );
            $array2 = array( "priv" );

            foreach ( $array1 as $value1 ) {
                if ( $value1 != "" ) {
                    foreach ( $array2 as $value2 ) {
                        if ( $part["search"] != "" ) $part["search"] .= " or ";
                       $part["search"] .= $value2. " LIKE '%".$value1."%'";
                    }
                }
            }
            if ( $part["search"] != "" ) $part["search"] = " WHERE (".$part["search"].")";
        } else {
            $part["search"] = "";
            $ausgaben["search"] = "";
        }


        // +++
        // suche

        /* z.B. db query */

        $sql = "SELECT *
                  FROM ".$cfg["prived"]["db"]["priv"]["entries"].$part["search"];

        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];

        // seiten umschalter
        $inhalt_selector = inhalt_selector( $sql, $environment["parameter"][1], $cfg["prived"]["db"]["priv"]["rows"], $parameter, 1, 3, $getvalues );
        $ausgaben["inhalt_selector"] = $inhalt_selector[0]."<br />";
        $sql = $inhalt_selector[1];
        $ausgaben["anzahl"] = $inhalt_selector[2];

        $result = $db -> query($sql);
        while ( $data = $db -> fetch_array($result,1) ) {

            // platz fuer vorbereitungen hier z.B.tabellen farben wechseln
            if ( $cfg["prived"]["color"]["set"] == $cfg["prived"]["color"]["a"]) {
                $cfg["prived"]["color"]["set"] = $cfg["prived"]["color"]["b"];
            } else {
                $cfg["prived"]["color"]["set"] = $cfg["prived"]["color"]["a"];
            }

            // wie im einfachen modul k�nnten nur die marken !{0}, !{1} befuellt werden
            #$dataloop["list"][$data["id"]][0] = $data["field1"];
            #$dataloop["list"][$data["id"]][1] = $data["field2"];

            // der uebersicht halber fuellt das erweiterte modul aber einzeln benannte marken
            $dataloop["list"][$data[$cfg["prived"]["db"]["priv"]["key"]]] = array(
                                   "color" => $cfg["prived"]["color"]["set"],
                                  "field1" => $data[$cfg["prived"]["db"]["priv"]["order"]],
                                    "edit" => $cfg["prived"]["basis"]."/edit,".$data[$cfg["prived"]["db"]["priv"]["key"]].".html",
                                  "delete" => $cfg["prived"]["basis"]."/delete,".$data[$cfg["prived"]["db"]["priv"]["key"]].".html",
                                 "details" => $cfg["prived"]["basis"]."/details,".$data[$cfg["prived"]["db"]["priv"]["key"]].".html",
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
        $ausgaben["link_new"] = $cfg["prived"]["basis"]."/add.html";

        // hidden values
        #$ausgaben["form_hidden"] .= "";

        // was anzeigen
        $cfg["prived"]["path"] = str_replace($pathvars["virtual"],"",$cfg["prived"]["basis"]);
        $mapping["main"] = eCRC($cfg["prived"]["path"]).".list";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error1) #(error1)<br />";
            $ausgaben["inaccessible"] .= "# (edittitel) #(edittitel)<br />";
            $ausgaben["inaccessible"] .= "# (deletetitel) #(deletetitel)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        $ausgaben["form_aktion"] = "";

        // +++
        // page basics

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
