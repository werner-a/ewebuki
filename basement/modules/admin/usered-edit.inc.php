<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: leer-edit.inc.php 503 2006-09-22 06:16:23Z chaot $";
// "leer - edit funktion";
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

        if ( count($_POST) == 0 ) {
            $sql = "SELECT *
                      FROM ".$cfg["db"]["user"]["entries"]."
                     WHERE ".$cfg["db"]["user"]["key"]."='".$environment["parameter"][1]."'";
            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $result = $db -> query($sql);
            $form_values = $db -> fetch_array($result,1);
        } else {
            $form_values = $_POST;
        }

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
        $sql = "SELECT auth_level.lid, auth_level.level, auth_right.uid, auth_right.rid
                  FROM auth_level
             LEFT JOIN auth_right ON auth_level.lid = auth_right.lid and auth_right.uid = ".$environment["parameter"][1]."
              ORDER BY level";
        $result = $db -> query($sql);
        while ( $all = $db -> fetch_array($result,1) ) {
            // unterscheidung, ob recht schon vergeben ist oder noch zu vergeben ist
            if ( $all["uid"] == $environment["parameter"][1] ) {
                $loop_label = "actual";
            } else {
                $loop_label = "avail";
            }
            // wurde das element schon angewaehlt
            $sel = "";
            if ( is_array($form_values[$loop_label]) && in_array($all["lid"],$form_values[$loop_label]) ) $sel = ' selected="true"';
            $hidedata[$loop_label][0] = -1;
            $dataloop[$loop_label][] = array(
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
        $ausgaben["form_aktion"] = $cfg["basis"]."/edit,".$environment["parameter"][1].",verify.html";
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

        // referer im form mit hidden element mitschleppen
        $ausgaben["form_referer"] = $cfg["basis"]."/list.html";
        $ausgaben["form_break"] = $ausgaben["form_referer"];

        // +++
        // page basics

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
                if ( $_POST["newpass"] != "" || $_POST["chkpass"] != "" ) {
                    if ( $_POST["newpass"] == $_POST["chkpass"] ) {
                        $checked_password = $_POST["newpass"];
                        mt_srand((double)microtime()*1000000);
                        $a=mt_rand(1,128);
                        $b=mt_rand(1,128);
                        $mysalt = chr($a).chr($b);
                        $checked_password = crypt($checked_password, $mysalt);
                        // da ich das passwort erstellt habe, klappt magic_quotes_gpc nicht
                        $checked_password = addslashes($checked_password);
                    }else{
                        $ausgaben["form_error"] .= $form_options["pass"]["ferror"];
                    }
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
                        if ( $sqla != "" ) $sqla .= ", ";
                        $sqla .= $name."='".$value."'";
                    }
                }

                // Sql um spezielle Felder erweitern
                if ( $checked_password != "" ) {
                    $sqla .= ", pass='".$checked_password."'";
                }

                $sql = "UPDATE ".$cfg["db"]["user"]["entries"]." SET ".$sqla." WHERE uid='".$environment["parameter"][1]."'";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];

                if ($result  = $db -> query($sql)) {

                    // level management sql begin
                    // ***
                    if ( is_array($_POST["avail"]) || $_POST["add"] != "" ) {
                        foreach ($_POST["avail"] as $name => $value ) {
                            $sql = "INSERT INTO auth_right (uid, lid) VALUES ('".$environment["parameter"][1]."', '".$value."')";
                            $db -> query($sql);
                            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                        }
                    }

                    if ( is_array($_POST["actual"]) || $_POST["del"] != "" ) {
                        foreach ($_POST["actual"] as $name => $value ) {
                            $sql = "DELETE FROM auth_right WHERE lid='".$value."' AND uid=".$environment["parameter"][1];
                            $db -> query($sql);
                            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                        }
                    }
                    // +++
                    // level management sql end

                    if ( $_POST["add"] != "" || $_POST["del"] != "" ) {
                        $header = $cfg["basis"]."/edit,".$environment["parameter"][1].".html";
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
