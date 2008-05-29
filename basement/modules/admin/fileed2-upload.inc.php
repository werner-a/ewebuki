<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "fileed - upload funktion";
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

    if ( $cfg["fileed"]["right"] == "" ||
        priv_check("/".$cfg["fileed"]["subdir"]."/".$cfg["fileed"]["name"],$cfg["fileed"]["right"]) ||
        priv_check_old("",$cfg["fileed"]["right"]) ) {

        // page basics
        // ***

        #if ( count($_POST) == 0 ) {
        #} else {
            $form_values = $_POST;
        #}

        // form options holen
        #$form_options = form_options(eCRC($environment["ebene"]).".".$environment["kategorie"]);

        // form elememte bauen
        #$element = form_elements( $cfg["fileed"]["db"]["leer"]["entries"], $form_values );

        // form elemente erweitern
        #$element["extension1"] = "";
        #$element["extension2"] = "";
        if ( $_GET["anzahl"] ) {
            $anzahl = $_GET["anzahl"];
        } else {
            $anzahl = $cfg["fileed"]["upload"]["inputs"];
        }
        for ( $i = 1; $i <= $anzahl; $i++ ) {
            $dataloop["upload"][$i]["name"] = $name.$i;
        }

        $ausgaben["filesize"] = sprintf("%0.1f",($cfg["file"]["filesize"]/1000000))." MB";
        $ausgaben["filetyp"] = "";
        ksort($cfg["file"]["filetyp"]);
        foreach ( $cfg["file"]["filetyp"] as $key=>$value ) {
            if ( $ausgaben["filetyp"] != "" ) $ausgaben["filetyp"] .= ", ";
            $ausgaben["filetyp"] .= $key;
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
        $ausgaben["form_aktion"] = $cfg["fileed"]["basis"]."/upload,".$environment["parameter"][1].",verify.html";
        $ausgaben["form_break"] = $cfg["fileed"]["basis"]."/list.html";

        // hidden values
        $ausgaben["form_hidden"] .= "";

        // was anzeigen
        $mapping["main"] = eCRC($environment["ebene"]).".upload";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($_GET["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_result) #(error_result)<br />";
            $ausgaben["inaccessible"] .= "# (file_error0) g(file_error0)<br />";
            $ausgaben["inaccessible"] .= "# (file_error1) g(file_error1)<br />";
            $ausgaben["inaccessible"] .= "# (file_error2) g(file_error2)<br />";
            $ausgaben["inaccessible"] .= "# (file_error3) g(file_error3)<br />";
            $ausgaben["inaccessible"] .= "# (file_error4) g(file_error4)<br />";
            $ausgaben["inaccessible"] .= "# (file_error6) g(file_error6)<br />";
            $ausgaben["inaccessible"] .= "# (file_error7) g(file_error7)<br />";
            $ausgaben["inaccessible"] .= "# (file_error8) g(file_error8)<br />";
            $ausgaben["inaccessible"] .= "# (file_error10) g(file_error10)<br />";
            $ausgaben["inaccessible"] .= "# (file_error11) g(file_error11)<br />";
            $ausgaben["inaccessible"] .= "# (file_error12) g(file_error12)<br />";
            $ausgaben["inaccessible"] .= "# (file_error13) g(file_error13)<br />";
            $ausgaben["inaccessible"] .= "# (file_error14) g(file_error14)<br />";
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
                            $error = file_validate($value["tmp_name"], $value["size"], $cfg["file"]["filesize"], $cfg["file"]["filetyp"], $key);
                            if ( $error == 0 ) {
                                $newname = $cfg["file"]["base"]["maindir"].$cfg["file"]["base"]["new"].$_SESSION["uid"]."_".$value["name"];
                                rename($value["tmp_name"],$newname);
                                if ( function_exists("zip_open") ) {
                                    // compilation
                                    $buffer = compilation_list();
                                    reset($buffer);
                                    $new_comp = key($buffer) + 1;
                                    zip_handling($newname,
                                                $cfg["file"]["base"]["maindir"].$cfg["file"]["base"]["new"],
                                                $cfg["fileed"]["filetyp"],
                                                $cfg["fileed"]["filesize"],
                                                "selection",
                                                $new_comp,
                                                $cfg["fileed"]["zip_handling"]["sektions"]
                                    );
                                }
                            } else {
                                $ausgaben["form_error"] .= "Ergebnis: ".$file["name"]." g(file_error".$error.")";
                            }
                    }
                }

                if ( $ausgaben["form_error"] == "" ) {
                    header("Location: ".$cfg["fileed"]["basis"]."/add,".$environment["parameter"][1].".html");
                    exit(); ### laut guenther wird es gebraucht, warum?
                } else {
                    $ausgaben["form_error"] .= "<br><br><a href=\"".$cfg["fileed"]["basis"]."/add,".$environment["parameter"][1].".html\">Trotzdem weiter</a>";
                    unset($hidedata["modus"]);
                    #$mapping["main"] = "default1";
                }

                // +++
                // funktions bereich fuer erweiterungen
            }
        }
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
