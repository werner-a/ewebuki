<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id: search.inc.php 503 2006-09-22 06:16:23Z chaot $";
  $Script["desc"] = "suche im content";
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

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** ".$script["name"]." ** ]".$debugging["char"];

    if ( $cfg["search"]["right"] == "" || $rechte[$cfg["search"]["right"]] == -1 ) {

        ////////////////////////////////////////////////////////////////////
        // achtung: bei globalen funktionen, variablen nicht zuruecksetzen!
        // z.B. $ausgaben["form_error"],$ausgaben["inaccessible"]
        ////////////////////////////////////////////////////////////////////

        // page basics
        // ***

        // warnung ausgeben
        if ( get_cfg_var('register_globals') == 1 ) $debugging["ausgabe"] .= "Warnung: register_globals in der php.ini steht auf on, evtl werden interne Variablen ueberschrieben!".$debugging["char"];

        // path fuer die schaltflaechen anpassen
        if ( $cfg["search"]["iconpath"] == "" ) $cfg["search"]["iconpath"] = "/images/default/";

        // label bearbeitung aktivieren
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $specialvars["editlock"] = 0;
        } else {
            $specialvars["editlock"] = -1;
        }

        // +++
        // page basics


        // funktions bereich
        // ***

        $sanitized["search"] = preg_replace("/[^A-Za-z0-9_ .,-]+/", "", $HTTP_POST_VARS["search"]);
        $sanitized["requested"] = preg_replace("/[^A-Za-z0-9_.-]+/", "", $HTTP_POST_VARS["requested"]);
        // txt2regex [^A-Za-z_.-0-9]+, [a-z_.-0-9]+

        $ausgaben["search"] = $sanitized["search"];

        if ( $sanitized["search"] != "" ) {
            $sql = "SELECT *
                    FROM ".$cfg["search"]["db"]["text"]["entries"]."
                    WHERE ".$cfg["search"]["db"]["text"]["where"]." LIKE '%".$sanitized["search"]."%'";;
            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $result = $db -> query($sql);
            $hits = $db -> num_rows($result);

            if ( $hits == 0 ) {
                $ausgaben["result"] = "#(found_nothing)";
            } elseif ( $hits == 1 ) {
                $data = $db -> fetch_array($result,1);
                header("Location: ".$pathvars["virtual"].$data["ebene"]."/".$data["kategorie"].".html");
            } else {
                $ausgaben["result"] = "#(found_something)";
                while ( $data = $db -> fetch_array($result,1) ) {
                    #$dataloop["leer"][$data["id"]][1] = $data["field1"];

                    $content = tagremove($data["content"]);

                    $p = strpos($content, $sanitized["search"]);
                    $l = strlen($content);

                    $b = 120;
                    $h = $b / 2;
                    if ( $b >= $l ) {
                        $s = 0;
                    } elseif ( $p >= ($h) ) {
                        $s = $p-$h;
                    } elseif ( $p == 0 ) {
                        $s = $p;
                    } else {
                        $s = $h-$p;
                    }

                    $found = substr($content, $s, $b);
                    $found = "... ".$found." ..."." ( $l / $b / $p / $s )";


                    $dataloop["result"][$data["label"]."_".$data["tname"]]["found_in"] = $data["ebene"]."/".$data["kategorie"].".html";
                    $dataloop["result"][$data["label"]."_".$data["tname"]]["url"] = $pathvars["virtual"].$data["ebene"]."/".$data["kategorie"].".html";
                    $dataloop["result"][$data["label"]."_".$data["tname"]]["content"] = $found;
                }
                $hidedata["result"][0] = "enable";
            }
        } else {
            header("Location: ".$sanitized["requested"]);
        }

        // +++
        // funktions bereich


        // page basics
        // ***

        // fehlermeldungen
        if ( $HTTP_GET_VARS["error"] != "" ) {
            if ( $HTTP_GET_VARS["error"] == 1 ) {
                $ausgaben["form_error"] = "#(error1)";
            }
        } else {
            $ausgaben["form_error"] = "";
        }

        // navigation erstellen
        #$ausgaben["add"] = $cfg["search"]["basis"]."/add,".$environment["parameter"][1].",verify.html";
        #$mapping["navi"] = "leer";

        // hidden values
        #$ausgaben["form_hidden"] .= "";

        // was anzeigen
        #$mapping["main"] = crc32($environment["ebene"]).".list";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (found_nothing) #(found_nothing)<br />";
            $ausgaben["inaccessible"] .= "# (found_something) #(found_something)<br />";
            $ausgaben["inaccessible"] .= "# (error1) #(error1)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        #n/a

        // +++
        // page basics

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
