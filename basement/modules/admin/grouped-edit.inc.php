<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "grouped edit funktion";
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

        if ( count($_POST) == 0 ) {
            $sql = "SELECT *
                      FROM ".$cfg["grouped"]["db"]["group"]["entries"]."
                     WHERE ".$cfg["grouped"]["db"]["group"]["key"]."='".$environment["parameter"][1]."'";
            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $result = $db -> query($sql);
            $form_values = $db -> fetch_array($result,1);

            # nice sql query tnx@bastard!
            $sql = "SELECT auth_user.uid, auth_user.vorname,auth_user.nachname,auth_user.username, auth_member.gid FROM auth_user LEFT JOIN auth_member ON (auth_user.uid = auth_member.uid and auth_member.gid = ".$environment["parameter"][1].") ORDER by username";
            $result = $db -> query($sql);

            while ( $all = $db -> fetch_array($result,1) ) {
                if ( $all["gid"] == $environment["parameter"][1] ) {
                    $_SESSION["chosen_user"][] = $all["uid"];
                    $dataloop["actual"][] = array(
                                            "value"     => $all["uid"],
                                            "username"  => $all["username"],
                                            "name"      => $all["nachname"],
                                            "vorname"   => $all["vorname"]
                                        );
                } else {
                    $dataloop["avail"][] = array(
                                            "value"     => $all["uid"],
                                            "username"  => $all["username"],
                                            "name"      => $all["nachname"],
                                            "vorname"   => $all["vorname"]
                                        );
                }
            }
        } else {
            $form_values = $_POST;
        }

        // form options holen
        $form_options = form_options(eCRC($environment["ebene"]).".".$environment["kategorie"]);

        // form elememte bauen
        $element = form_elements( $cfg["grouped"]["db"]["group"]["entries"], $form_values );

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["grouped"]["basis"]."/edit,".$environment["parameter"][1].",verify.html";
        $ausgaben["form_break"] = $cfg["grouped"]["basis"]."/list.html";

        // hidden values
        $ausgaben["form_hidden"] .= "";

        // was anzeigen
        $mapping["main"] = eCRC($environment["ebene"]).".modify";
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

        if ( $environment["parameter"][2] == "verify" && $_POST["send"] != "" ) {

            // form eingaben prüfen
            form_errors( $form_options, $_POST );

            // gibt es diesen gruppe bereits?
            $sql = "SELECT ".$cfg["grouped"]["db"]["group"]["order"].",gid
                        FROM ".$cfg["grouped"]["db"]["group"]["entries"]."
                       WHERE ".$cfg["grouped"]["db"]["group"]["order"]." = '".$_POST[$cfg["grouped"]["db"]["group"]["order"]]."'";
            $result  = $db -> query($sql);
            $num_rows = 0;
            while ( $array = $db -> fetch_array($result,1) ) {;
                if ( $array["gid"] != $environment["parameter"][1] ) {
                    $num_rows++;
                }
            }

            if ( $num_rows >= 1 ) $ausgaben["form_error"] = "#(error_dupe)";

            // evtl. zusaetzliche datensatz aendern
            if ( $ausgaben["form_error"] == ""  ) {

                // funktions bereich fuer erweiterungen
                // ***

                // erst einmal alle loeschen
                $sql = "DELETE FROM ".$cfg["grouped"]["db"]["member"]["entries"]." WHERE ".$cfg["grouped"]["db"]["member"]["group"]." = ".$environment["parameter"][1];
                $result = $db -> query($sql);

                // user hinzufuegen
                if ( is_array($_SESSION["chosen_user"]) ) {

                    // session variable in db schreiben
                    foreach ($_SESSION["chosen_user"] as $value ) {
                        $sql = "INSERT INTO ".$cfg["grouped"]["db"]["member"]["entries"]."
                                            (".$cfg["grouped"]["db"]["member"]["group"].",".$cfg["grouped"]["db"]["member"]["user"].")
                                     VALUES ('".$environment["parameter"][1]."','".$value."')";
                        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                        $result = $db -> query($sql);
                    }
                }
                // +++
                // funktions bereich fuer erweiterungen
            }

            // datensatz aendern
            if ( $ausgaben["form_error"] == ""  ) {

                $kick = array( "PHPSESSID", "form_referer", "send", "actual", "avail" );
                foreach($_POST as $name => $value) {
                    if ( !in_array($name,$kick) && !strstr($name, ")" ) ) {
                        if ( $sqla != "" ) $sqla .= ", ";
                        $sqla .= $name."='".$value."'";
                    }
                }

                // Sql um spezielle Felder erweitern
                #$ldate = $HTTP_POST_VARS["ldate"];
                #$ldate = substr($ldate,6,4)."-".substr($ldate,3,2)."-".substr($ldate,0,2)." ".substr($ldate,11,9);
                #$sqla .= ", ldate='".$ldate."'";

                // gruppe aendern
                $sql = "UPDATE ".$cfg["grouped"]["db"]["group"]["entries"]."
                            SET ".$cfg["grouped"]["db"]["group"]["order"]." = '".$_POST[$cfg["grouped"]["db"]["group"]["order"]]."',
                                beschreibung = '".$_POST["beschreibung"]."'
                            WHERE gid='".$environment["parameter"][1]."'";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $result  = $db -> query($sql);

                if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
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
