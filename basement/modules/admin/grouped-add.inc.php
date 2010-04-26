<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "grouped - add funktion";
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

    if ( priv_check("/".$cfg["grouped"]["subdir"]."/".$cfg["grouped"]["name"],$cfg["grouped"]["right"]) ||
        priv_check_old("",$cfg["grouped"]["right"]) ) {

        // page basics
        // ***
        $form_values = $HTTP_POST_VARS;

        $hidedata["edit"]["enable"] = "on";
        $ausgaben["parameter"] = $environment["parameter"][1];

        if ( $_POST["ajaxsuche"] == "on") {
            echo "<li><b>Treffer</b></li>";
            $sql = "SELECT * FROM auth_user WHERE username like '%".$_POST["text"]."%' OR vorname like '%".$_POST["text"]."%' OR nachname like '%".$_POST["text"]."%'";
            $result = $db -> query($sql);
            while ( $data = $db -> fetch_array($result,1) ) {
                if ( in_array($data["uid"], $_SESSION["chosen_user"])) continue;
                echo "<li class=\"sel_item\">".$data["vorname"]." ".$data["nachname"]."</li>";
            }
            exit;
        }

        if ( $_POST["ajax"]) {
            $_SESSION["chosen_user"] = $_POST["chosen_user"];
            exit;
        }

        // form options holen
        $form_options = form_options(eCRC($environment["ebene"]).".".$environment["kategorie"]);

        // form elememte bauen
        $element = form_elements( $cfg["grouped"]["db"]["group"]["entries"], $form_values );

        // form elemente erweitern
        $element["extension1"] = "";
        $element["extension2"] = "";

        // +++
        // page basics


        // funktions bereich fuer erweiterungen
        // ***

        // user holen und mit dataloop ausgeben
        $sql = "SELECT *
                  FROM ".$cfg["grouped"]["db"]["user"]["entries"]."
              ORDER BY ".$cfg["grouped"]["db"]["user"]["order"];
        $result = $db -> query($sql);
        while ( $all = $db -> fetch_array($result,1) ) {
            $dataloop["avail"][] = array(
                                            "value"     => $all["uid"],
                                            "username"  => $all["username"],
                                            "name"      => $all["nachname"],
                                            "vorname"   => $all["vorname"]
                                    );
        }

        // +++
        // funktions bereich fuer erweiterungen


        // page basics
        // ***

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["grouped"]["basis"]."/add,".$environment["parameter"][1].",verify.html";
        $ausgaben["form_break"] = $cfg["grouped"]["basis"]."/list.html";

        // hidden values
        $ausgaben["form_hidden"] .= "";

        // was anzeigen
        $mapping["main"] = eCRC($environment["ebene"]).".modify";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
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
            &&  ( $HTTP_POST_VARS["send"] != ""
                || $HTTP_POST_VARS["extension1"] != ""
                || $HTTP_POST_VARS["extension2"] != "" ) ) {

            // form eigaben prüfen
            form_errors( $form_options, $HTTP_POST_VARS );

            // evtl. zusaetzliche datensatz anlegen
            if ( $ausgaben["form_error"] == ""  ) {

                // funktions bereich fuer erweiterungen
                // ***

               // gibt es diesen gruppe bereits?
                $sql = "SELECT ".$cfg["grouped"]["db"]["group"]["order"]."
                          FROM ".$cfg["grouped"]["db"]["group"]["entries"]."
                         WHERE ".$cfg["grouped"]["db"]["group"]["order"]." = '".$HTTP_POST_VARS["ggroup"]."'";
                $result  = $db -> query($sql);
                $num_rows = $db -> num_rows($result);
                if ( $num_rows >= 1 ) $ausgaben["form_error"] = "#(error_dupe)";


                if ( $error ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                // +++
                // funktions bereich fuer erweiterungen
            }

            // datensatz anlegen
            if ( $ausgaben["form_error"] == ""  ) {

                $kick = array( "PHPSESSID", "form_referer", "send" );
                foreach($_POST as $name => $value) {
                    if ( !in_array($name,$kick) ) {
                        if ( $sqla != "" ) $sqla .= ",";
                        $sqla .= $name;
                        if ( $sqlb != "" ) $sqlb .= ",";
                        $sqlb .= " '".$value."'";
                    }
                }

                // Sql um spezielle Felder erweitern
                #$sqla .= ", pass";
                #$sqlb .= ", password('".$checked_password."')";

                // gruppr hinzufuegen
                $sql = "insert into ".$cfg["grouped"]["db"]["group"]["entries"]." (".$sqla.") VALUES (".$sqlb.")";

                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $result  = $db -> query($sql);
                if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");

                // usern mit neuem level versehen
                if ( $ausgaben["form_error"] == "" ) {
                    if ( is_array($_SESSION["chosen_user"]) ) {
                        $gid = $db -> lastid();
                        foreach ($_SESSION["chosen_user"] as $value ) {
                            $sql = "INSERT INTO auth_member (gid, uid) VALUES ('".$gid."', '".$value."')";
                            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                            $db -> query($sql);
                            if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                        }
                    }
                }


                if ( $header == "" ) $header = $cfg["grouped"]["basis"]."/list.html";
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