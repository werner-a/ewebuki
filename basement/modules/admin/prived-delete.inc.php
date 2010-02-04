<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "prived - delete funktion";
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

     if ( priv_check("/".$cfg["prived"]["subdir"]."/".$cfg["prived"]["name"],$cfg["prived"]["right"]) ||
        priv_check_old("",$cfg["prived"]["right"]) ) {

        // funktions bereich fuer erweiterungen
        // ***

        $hidedata["delete_button"]["on"] = "on";

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // auf verknuepften datensatz pruefen
        $sql = "SELECT *
                  FROM ".$cfg["prived"]["db"]["priv"]["entries"]."
                  INNER JOIN ".$cfg["prived"]["db"]["content"]["entries"]."
                  ON (".$cfg["prived"]["db"]["priv"]["entries"].".".$cfg["prived"]["db"]["priv"]["key"]."=".$cfg["prived"]["db"]["content"]["entries"].".".$cfg["prived"]["db"]["content"]["priv"].")
                  INNER JOIN ".$cfg["prived"]["db"]["group"]["entries"]."
                  ON (".$cfg["prived"]["db"]["content"]["entries"].".".$cfg["prived"]["db"]["content"]["group"]."=".$cfg["prived"]["db"]["group"]["entries"].".".$cfg["prived"]["db"]["group"]["key"].")
                 WHERE ".$cfg["prived"]["db"]["priv"]["entries"].".".$cfg["prived"]["db"]["priv"]["key"]."='".$environment["parameter"][1]."'";
        $result = $db -> query($sql);
        $num_rows = $db -> num_rows($result);

        if ( $num_rows > 0 ) {
            $hidedata["delete_liste"]["on"] = "on";
            while ( $data = $db -> fetch_array($result,$nop) ) {
                $id++;
                ($data["neg"] == -1 ) ? $data["neg"] = "entzogen" : $data["neg"] = "erteilt";
                $ausgaben["priv"] = $data["priv"];
                $dataloop["delete"][$id]["group"] = $data["ggroup"];
                $dataloop["delete"][$id]["content"] = $data["tname"];
                $dataloop["delete"][$id]["neg"] = $data["neg"];
           }

        } else {
            // datensatz holen
            $sql = "SELECT *
                      FROM ".$cfg["prived"]["db"]["priv"]["entries"]."
                     WHERE ".$cfg["prived"]["db"]["priv"]["key"]."='".$environment["parameter"][1]."'";

            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $result = $db -> query($sql);
            $data = $db -> fetch_array($result,$nop);
            $ausgaben["priv"] = $data["priv"];

        }
        
        // das loeschen wurde bestaetigt, loeschen!
        // ***
        if ( $HTTP_POST_VARS["delete"] != "" ) {

            $sql = "DELETE FROM ".$cfg["prived"]["db"]["priv"]["entries"]."
                          WHERE ".$cfg["prived"]["db"]["priv"]["key"]."='".$environment["parameter"][1]."';";
            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $result  = $db -> query($sql);
            if ( !$result ) $ausgaben["form_error"] = $db -> error("#(error_result1)<br />");

            $sql = "DELETE FROM ".$cfg["prived"]["db"]["content"]["entries"]."
                          WHERE ".$cfg["prived"]["db"]["content"]["priv"]."='".$environment["parameter"][1]."';";
            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $result  = $db -> query($sql);
            if ( !$result ) $ausgaben["form_error"] = $db -> error("#(error_result1)<br />");

            // wohin schicken
            if ( $ausgaben["form_error"] == "" ) {
                header("Location: ".$cfg["prived"]["basis"]."/list.html");
            }
        }
        // +++
        // das loeschen wurde bestaetigt, loeschen!


        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["prived"]["basis"]."/delete,".$environment["parameter"][1].",verify.html";
        $ausgaben["form_break"] = $cfg["prived"]["basis"]."/list.html";

        // unzugaengliche #(marken) sichtbar machen
        // ***
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_result1) #(error_result1)<br />";
            $ausgaben["inaccessible"] .= "# (error_result2) #(error_result2)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // was anzeigen
        $mapping["main"] = eCRC($environment["ebene"]).".modify";
        $mapping["navi"] = "leer";
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
