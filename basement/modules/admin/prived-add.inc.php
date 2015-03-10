<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "prived - add funktion";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2015 Werner Ammon ( wa<at>chaos.de )

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

    86343 Koenigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( $cfg["prived"]["right"] == "" || priv_check('', $cfg["prived"]["right"] ) ) {

        // page basics
        // ***

        $hidedata["edit"]["on"] = "on";
        $hidedata["send_button"]["on"] = "on";
        $ausgaben["priv"] = "";

        #if ( count($_POST) == 0 ) {
        #} else {
            $form_values = $_POST;
        #}

        // form options holen
        $form_options = form_options(eCRC($environment["ebene"]).".".$environment["kategorie"]);

        // form elememte bauen
        $element = form_elements( $cfg["prived"]["db"]["priv"]["entries"], $form_values );

        // form elemente erweitern
        $element["extension1"] = null;
        $element["extension2"] = null;


        // fehlermeldungen
        $ausgaben["form_error"] = null;

        // navigation erstellen
        if ( !isset($environment["parameter"][1]) ) $environment["parameter"][1] = null;
        $ausgaben["form_aktion"] = $cfg["prived"]["basis"]."/add,".$environment["parameter"][1].",verify.html";
        $ausgaben["form_break"] = $cfg["prived"]["basis"]."/list.html";

        // hidden values
        $ausgaben["form_hidden"] = null;

        // was anzeigen
        $mapping["main"] = eCRC($environment["ebene"]).".modify";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($_GET["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_result) #(error_result)<br />";
            $ausgaben["inaccessible"] .= "# (error_dupe) #(error_dupe)<br />";
        } else {
            $ausgaben["inaccessible"] = null;
        }

        // wohin schicken
        #n/a

        // +++
        // page basics
        if ( !isset($environment["parameter"][2]) ) $environment["parameter"][2] = null;
        if ( $environment["parameter"][2] == "verify" && $_POST["send"] != "" ) {

            // form eigaben pruefen
            form_errors( $form_options, $_POST );

            // evtl. zusaetzliche datensatz anlegen
            if ( $ausgaben["form_error"] == ""  ) {
                if ( !isset($error) ) $error = null;
                if ( $error ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
            }

            // datensatz anlegen
            if ( $ausgaben["form_error"] == ""  ) {

                $kick = array( "PHPSESSID", "form_referer", "send", "avail" );
                $sqla = null; $sqlb = null;
                foreach($_POST as $name => $value) {
                    if ( !in_array($name, $kick) ) {
                        if ( $sqla != "" ) $sqla .= ",";
                        $sqla .= " ".$name;
                        if ( $sqlb != "" ) $sqlb .= ",";
                        $sqlb .= " '".$value."'";
                    }
                }

                $sql = "insert into ".$cfg["prived"]["db"]["priv"]["entries"]." (".$sqla.") VALUES (".$sqlb.")";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $result  = $db -> query($sql);
                if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                if ( !isset($header) ) $header = $cfg["prived"]["basis"]."/list.html";
            }

            // wenn es keine fehlermeldungen gab, die uri $header laden
            if ( $ausgaben["form_error"] == "" ) {
                header("Location: ".$header);
            }
        }
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>