<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "leer - delete funktion";
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

    if ( $cfg["usered"]["right"] == "" ||
        priv_check("/".$cfg["usered"]["subdir"]."/".$cfg["usered"]["name"],$cfg["usered"]["right"]) ||
        priv_check_old("",$cfg["usered"]["right"]) ) {

        // funktions bereich fuer erweiterungen
        // ***

        // datensatz holen
        $sql ="SELECT *
                 FROM ".$cfg["usered"]["db"]["user"]["entries"]."
                WHERE ".$cfg["usered"]["db"]["user"]["key"]."='".$environment["parameter"][1]."'";
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
        $result = $db -> query($sql);
        $data = $db -> fetch_array($result,1);

        // ausgaben belegen
        foreach($data as $name => $value) {
            $ausgaben[$name] = $value;
        }

        // funktions bereich fuer erweiterungen
        // ***

        // evtl. zusaetzlichen datensatz anzeigen
        // rechte
        $sql = "SELECT *
                  FROM ".$cfg["usered"]["db"]["right"]["entries"]."
                  JOIN ".$cfg["usered"]["db"]["level"]["entries"]."
                    ON (".$cfg["usered"]["db"]["level"]["entries"].".".$cfg["usered"]["db"]["right"]["level"]."=".$cfg["usered"]["db"]["right"]["entries"].".".$cfg["usered"]["db"]["level"]["key"].")
                 WHERE ".$cfg["usered"]["db"]["right"]["user"]." ='".$environment["parameter"][1]."'";
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
        $result = $db -> query($sql);
        $ausgaben["rights"] = "---";
        if ( $db->num_rows($result) > 0 ){
            $hidedata["right"][] = -1;
            $ausgaben["rights"] = "";
            while ( $data = $db -> fetch_array($result,1) ) {
                if ( $ausgaben["rights"] != "" ) $ausgaben["rights"] .= ", ";
                $ausgaben["rights"] .= $data[$cfg["usered"]["db"]["level"]["level"]];
            }
        }

        // spezial-rechte
        $sql = "SELECT *
                  FROM ".$cfg["usered"]["db"]["special"]["entries"]."
                 WHERE ".$cfg["usered"]["db"]["special"]["user"]." ='".$environment["parameter"][1]."'";
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
        $result = $db -> query($sql);
        $ausgaben["special"] = "---";
        if ( $db->num_rows($result) > 0 ){
            $hidedata["special"][] = -1;
            $ausgaben["special"] = "";
            while ( $data = $db -> fetch_array($result,1) ) {
                if ( $ausgaben["special"] != "" ) $ausgaben["special"] .= "<br />";
                $ausgaben["special"] .= $data[$cfg["usered"]["db"]["special"]["tname"]];
            }
        }

        // +++
        // funktions bereich fuer erweiterungen


        // page basics
        // ***

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["usered"]["basis"]."/delete,".$environment["parameter"][1].".html";
        $ausgaben["form_break"] = $cfg["usered"]["basis"]."/list.html";

        // hidden values
        $ausgaben["form_hidden"] = "";
        $ausgaben["form_delete"] = "true";

        // was anzeigen
        $mapping["main"] = crc32($environment["ebene"]).".delete";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        // ***
        if ( isset($_GET["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_result1) #(error_result1)<br />";
            $ausgaben["inaccessible"] .= "# (error_result2) #(error_result2)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }
        // +++
        // unzugaengliche #(marken) sichtbar machen

        // wohin schicken
        #n/a

        // +++
        // page basics


        // das loeschen wurde bestaetigt, loeschen!
        // ***
        if ( $_POST["delete"] != "" ) {

            // evtl. zusaetzlichen datensatz loeschen
            $sql = "DELETE FROM ".$cfg["usered"]["db"]["right"]["entries"]."
                          WHERE ".$cfg["usered"]["db"]["right"]["user"]." ='".$environment["parameter"][1]."'";
            if ( !$db->query($sql) ) $ausgaben["form_error"] = $db -> error("#(error_result2)<br />");

            $sql = "DELETE FROM ".$cfg["usered"]["db"]["special"]["entries"]."
                          WHERE ".$cfg["usered"]["db"]["special"]["user"]." ='".$environment["parameter"][1]."'";
            if ( !$db->query($sql) ) $ausgaben["form_error"] = $db -> error("#(error_result2)<br />");

            // datensatz loeschen
            if ( $ausgaben["form_error"] == "" ) {
                $sql = "DELETE FROM ".$cfg["usered"]["db"]["user"]["entries"]."
                              WHERE ".$cfg["usered"]["db"]["user"]["key"]."='".$environment["parameter"][1]."';";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $result  = $db -> query($sql);
                if ( !$result ) $ausgaben["form_error"] = $db -> error("#(error_result1)<br />");
            }

            // wohin schicken
            if ( $ausgaben["form_error"] == "" ) {
                header("Location: ".$cfg["usered"]["basis"]."/list.html");
            }
        }
        // +++
        // das loeschen wurde bestaetigt, loeschen!
} else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
