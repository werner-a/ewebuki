<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// leer-delete.inc.php v1 chaot
// leer - delete funktion
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

    if ( $cfg["leer"]["right"] == "" || priv_check('', $cfg["leer"]["right"]) ) {
    #if ( $cfg["leer"]["right"] == "" || $rechte[$cfg["leer"]["right"]] == -1 ) {

        // funktions bereich fuer erweiterungen
        // ***

        ### put your code here ###

        /* z.B. evtl. auf verknuepften datensatz pruefen
        $sql = "SELECT ".$cfg["leer"]["db"]["menu"]["key"]."
                  FROM ".$cfg["leer"]["db"]["menu"]["entries"]."
                 WHERE refid='".$environment["parameter"][1]."'";
        $result = $db -> query($sql);
        $num_rows = $db -> num_rows($result);
        */
        $num_rows = 0; // es gibt keine

        // +++
        // funktions bereich fuer erweiterungen

        if ( $num_rows > 0 ) {

            // was anzeigen
            $mapping["main"] = eCRC($environment["ebene"]).".list";
            $mapping["navi"] = "leer";

            // wohin schicken
            header("Location: ".$cfg["leer"]["basis"]."/list.html?error=1");

        } else {

            // datensatz holen
            $sql = "SELECT *
                      FROM ".$cfg["leer"]["db"]["main"]["entries"]."
                     WHERE ".$cfg["leer"]["db"]["main"]["key"]."='".$environment["parameter"][1]."'";
            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $result = $db -> query($sql);
            $data = $db -> fetch_array($result, null);
            $ausgaben["form_id1"] = $data["id"];
            $ausgaben["field1"] = $data["field1"];
            $ausgaben["field2"] = $data["field2"];

            // funktions bereich fuer erweiterungen
            // ***

            ### put your code here ###

            /* z.B. evtl. verknuepfte datensatze holen
            $sql = "SELECT *
                      FROM ".$cfg["leer"]["db"]["more"]["entries"]."
                     WHERE ".$cfg["leer"]["db"]["more"]["key"]." ='".$environment["parameter"][1]."'";
            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $result = $db -> query($sql);
            while ( $data2 = $db -> fetch_array($result,$nop) ) {
                if ( $ids != "" ) $ids .= ",";
                $ids .= $data2["id"];
                $ausgaben["field3"] .= $data2["field1"]." ";
                $ausgaben["field3"] .= $data2["field2"]."<br />";
            }
            $ausgaben["form_id2"] = $ids;
            */

            // +++
            // funktions bereich fuer erweiterungen


            // page basics
            // ***

            // fehlermeldungen
            $ausgaben["form_error"] = null;

            // navigation erstellen
            $ausgaben["form_aktion"] = $cfg["leer"]["basis"]."/delete,".$environment["parameter"][1].".html";
            $ausgaben["form_break"] = $cfg["leer"]["basis"]."/list.html";

            // hidden values
            $ausgaben["form_hidden"] = null;
            $ausgaben["form_delete"] = true;

            // was anzeigen
            #$mapping["main"] = eCRC($environment["ebene"]).".details";
            #$mapping["navi"] = "leer";

            // unzugaengliche #(marken) sichtbar machen
            // ***
            if ( isset($_GET["edit"]) ) {
                $ausgaben["inaccessible"] = "inaccessible values:<br />";
                $ausgaben["inaccessible"] .= "# (error_result1) #(error_result1)<br />";
                $ausgaben["inaccessible"] .= "# (error_result2) #(error_result2)<br />";
            } else {
                $ausgaben["inaccessible"] = null;
            }
            // +++
            // unzugaengliche #(marken) sichtbar machen

            // wohin schicken
            #n/a

            // +++
            // page basics


            // das loeschen wurde bestaetigt, loeschen!
            // ***
            if ( isset($_POST["delete"])
                && isset($_POST["send"]) ) {

                // evtl. zusaetzlichen datensatz loeschen
                if ( isset($_POST["id2"]) ) {
                    // funktions bereich fuer erweiterungen
                    // ***

                    ### put your code here ###

                    /* z.B. evtl. verknuepfte datensatze loeschen
                    $sql = "DELETE FROM ".$cfg["leer"]["db"]["more"]["entries"]."
                                  WHERE ".$cfg["leer"]["db"]["more"]["key"]." = '".$_POST["id2"]."'";
                    if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                    $result  = $db -> query($sql);
                    if ( !$result ) $ausgaben["form_error"] = $db -> error("#(error_result2)<br />");
                    */

                    // +++
                    // funktions bereich fuer erweiterungen
                }

                // datensatz loeschen
                if ( !isset($ausgaben["form_error"]) ) {
                    $sql = "DELETE FROM ".$cfg["leer"]["db"]["main"]["entries"]."
                                  WHERE ".$cfg["leer"]["db"]["main"]["key"]."='".$_POST["id1"]."';";
                    if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                    $result  = $db -> query($sql);
                    if ( !$result ) $ausgaben["form_error"] = $db -> error("#(error_result1)<br />");
                }

                // wohin schicken
                if ( !isset($ausgaben["form_error"]) ) {
                    header("Location: ".$cfg["leer"]["basis"]."/list.html");
                }
            }
            // +++
            // das loeschen wurde bestaetigt, loeschen!
        }
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
