<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "leer - add funktion";
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

    if ( $rechte[$cfg["right"]] == -1 ) {

        // page basics
        // ***

        $form_values = $_POST;

        // form options holen
        $form_options = form_options(crc32($environment["ebene"]).".modify");

        // form elememte bauen
        $element = form_elements( $cfg["db"]["user"]["entries"], $form_values );

        // form elemente erweitern
        $element["newpass"] = str_replace("pass\"","newpass\"",$element["pass"]);
        $element["chkpass"] = str_replace("pass\"","chkpass\"",$element["pass"]);
        $element["pass"] = "";

        // +++
        // page basics


        // funktions bereich fuer erweiterungen
        // ***

        // level management form form elemente begin
        // ***
        $sql = "SELECT lid, level
                  FROM auth_level
              ORDER BY level";
        $result = $db -> query($sql);
        if ( $db->num_rows($result) > 0 ) $hidedata["avail"][] = -1;
        while ( $all = $db -> fetch_array($result,1) ) {
            $sel = "";
            if ( is_array($form_values["avail"]) && in_array($all["lid"],$form_values["avail"]) ) $sel = ' selected="true"';
            $dataloop["avail"][] = array(
                "id" => $all["lid"],
                "level" => $all["level"],
                "sel" => $sel
            );
        }
        // +++
        // level management form form elemente end

        // +++
        // funktions bereich fuer erweiterungen


        // page basics
        // ***

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["basis"]."/add,".$environment["parameter"][1].",verify.html";
        $ausgaben["form_break"] = $cfg["basis"]."/list.html";

        // hidden values
        $ausgaben["form_hidden"] .= "";

        // was anzeigen
        $mapping["main"] = crc32($environment["ebene"]).".modify";
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

        // referer im form mit hidden element mitschleppen
        if ( $_POST["form_referer"] == "" ) {
            $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        } else {
            $ausgaben["form_referer"] = $_POST["form_referer"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        }

        if ( $environment["parameter"][2] == "verify"
            &&  ( $_POST["send"] != ""
                || $_POST["add"] != ""
                || $_POST["del"] != "" ) ) {

            // form eigaben prüfen
            form_errors( $form_options, $_POST );

            // evtl. zusaetzliche datensatz anlegen
            if ( $ausgaben["form_error"] == ""  ) {

                // funktions bereich fuer erweiterungen
                // ***

                // form eingaben prüfen erweitern
                if ( $_POST["newpass"] != "" && $_POST["newpass"] == $_POST["chkpass"] ) {
                    $checked_password = $_POST["newpass"];
                    mt_srand((double)microtime()*1000000);
                    $a=mt_rand(1,128);
                    $b=mt_rand(1,128);
                    $mysalt = chr($a).chr($b);
                    $checked_password = crypt($checked_password, $mysalt);
                    // da ich das passwort erstellt habe, klappt magic_quotes_gpc nicht
                    $checked_password = addslashes($checked_password);
                } else {
                    $ausgaben["form_error"] .= $form_options["pass"]["ferror"];
                }

                if ( $error ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                // +++
                // funktions bereich fuer erweiterungen
            }

            // datensatz anlegen
            if ( $ausgaben["form_error"] == ""  ) {

                $kick = array( "PHPSESSID", "pass", "newpass", "chkpass", "form_referer", "send", "avail", "actual", "add", "del" );
                foreach($_POST as $name => $value) {
                    if ( !in_array($name,$kick) ) {
                        if ( $sqla != "" ) $sqla .= ",";
                        $sqla .= " ".$name;
                        if ( $sqlb != "" ) $sqlb .= ",";
                        $sqlb .= " '".$value."'";
                    }
                }

                // Sql um spezielle Felder erweitern
                $sqla .= ", pass";
                $sqlb .= ", '".$checked_password."'";

                $sql = "INSERT INTO ".$cfg["db"]["user"]["entries"]." (".$sqla.") VALUES (".$sqlb.")";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];

                if ($result  = $db -> query($sql)) {

                    $uid = $db -> lastid();
                    // level management sql begin
                    // ***
                    if ( is_array($_POST["avail"]) ) {
                        foreach ($_POST["avail"] as $name => $value ) {
                            $sql = "INSERT INTO auth_right (uid, lid) VALUES ('".$uid."', '".$value."')";
                            $db -> query($sql);
                            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                        }
                    }
                    // +++
                    // level management sql end

                    if ( $_POST["add"] != "" || $_POST["del"] != "" ) {
                        $header = $cfg["basis"]."/edit,".$uid.".html";
                    }else{
                        $header = $ausgaben["form_referer"];
                    }

                } else {
                    if ( @$db -> error() == 1062 ) {
                        if ( $form_options["username"]["fdberror"] != "" ) {
                            $ausgaben["form_error"] .= $form_options["username"]["fdberror"];
                        } else {
                            $ausgaben["form_error"] .= "duplicate username, please change";
                        }
                    }
                    $error = $db -> error("sql error:");
                    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= $error.$debugging["char"];
                }

                if ( $header == "" ) $header = $cfg["basis"]."/list.html";
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
