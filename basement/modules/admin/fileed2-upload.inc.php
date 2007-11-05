<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "fileed - upload funktion";
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

    if ( $cfg["right"] == "" || $rechte[$cfg["right"]] == -1 ) {

        // page basics
        // ***

        #if ( count($_POST) == 0 ) {
        #} else {
            $form_values = $_POST;
        #}

        // form options holen
        #$form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

        // form elememte bauen
        #$element = form_elements( $cfg["db"]["leer"]["entries"], $form_values );

        // form elemente erweitern
        #$element["extension1"] = "";
        #$element["extension2"] = "";
        if ( $_GET["anzahl"] ) {
            $anzahl = $_GET["anzahl"];
        } else {
            $anzahl = $cfg["upload"]["inputs"];
        }
        for ( $i = 1; $i <= $anzahl; $i++ ) {
            $dataloop["upload"][$i]["name"] = "upload".$i;
        }

        // +++
        // page basics


        // funktions bereich fuer erweiterungen
        // ***

        ### put your code here ###

        // +++
        // funktions bereich fuer erweiterungen


        // page basics
        // ***

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["basis"]."/upload,".$environment["parameter"][1].",verify.html";
        $ausgaben["form_break"] = $cfg["basis"]."/list.html";

        // hidden values
        $ausgaben["form_hidden"] .= "";

        // was anzeigen
        $mapping["main"] = crc32($environment["ebene"]).".upload";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($_GET["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_result) #(error_result)<br />";
            $ausgaben["inaccessible"] .= "# (error_dupe) #(error_dupe)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        #n/a

        // +++
        // page basics

        if ( $environment["parameter"][2] == "verify"
            &&  ( $_POST["send"] != ""
                || $_POST["extension1"] != ""
                || $_POST["extension2"] != "" ) ) {

            unset($dataloop["upload"]);

            // form eigaben prüfen
            form_errors( $form_options, $_POST );

            // evtl. zusaetzliche datensatz anlegen
            if ( $ausgaben["form_error"] == ""  ) {

                // funktions bereich fuer erweiterungen
                // ***

                ### put your code here ###

                foreach ( $_FILES as $key => $value ) {
                    if ( $value["name"] != "" || $value["size"] != 0 ) {
                        $file = file_verarbeitung( $pathvars["filebase"]["new"], $key, $cfg["filesize"], $cfg["filetyp"], $pathvars["filebase"]["maindir"] );
                        if ( $file["returncode"] == 0 ) {
                            rename($pathvars["filebase"]["maindir"].$pathvars["filebase"]["new"].$file["name"],$pathvars["filebase"]["maindir"].$pathvars["filebase"]["new"].$_SESSION["uid"]."_".$file["name"]);
                        } else {
                            $ausgaben["form_error"] .= "Ergebnis: ".$file["name"]." ";
                            $ausgaben["form_error"] .= file_error($file["returncode"])."<br>";
                        }
                    }
                }

                if ( $ausgaben["form_error"] == "" ) {
                    header("Location: ".$cfg["basis"]."/add,".$environment["parameter"][1].".html");
                    exit(); ### laut guenther wird es gebraucht, warum?
                } else {
                    $ausgaben["form_error"] .= "<br><br><a href=\"".$cfg["basis"]."/add,".$environment["parameter"][1].".html\">Trotzdem weiter</a>";
                    #$mapping["main"] = "default1";
                }

                if ( $error ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                // +++
                // funktions bereich fuer erweiterungen
            }
        }
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
