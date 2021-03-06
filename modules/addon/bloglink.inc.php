<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "bloglink.inc.php v1 chaot";
  $Script["desc"] = "bloglink modul";
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

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** ".$script["name"]." ** ]".$debugging["char"];

    if ( $rechte[$cfg["bloglink"]["right"]] == "" || $rechte[$cfg["bloglink"]["right"]] == -1 ) {

        // page basics
        // ***

        //////////////////////////////////////////////////////////////////
        // achtung bei globalen funktionen variablen nicht zurueck setzen!
        // z.B. $ausgaben["form_error"],$ausgaben["inaccessible"]
        //////////////////////////////////////////////////////////////////

        // warnung ausgeben
        if ( get_cfg_var('register_globals') == 1 ) $debugging["ausgabe"] .= "Warnung: register_globals in der php.ini steht auf on, evtl werden interne Variablen ueberschrieben!".$debugging["char"];

        // path fuer die schaltflaechen anpassen
        if ( $cfg["bloglink"]["iconpath"] == "" ) $cfg["bloglink"]["iconpath"] = "/images/default/";

        // label bearbeitung aktivieren
        #if ( isset($_GET["edit"]) ) {
        #    $specialvars["editlock"] = 0;
        #} else {
        #    $specialvars["editlock"] = -1;
        #}

        // +++
        // page basics


        // funktions bereich
        // ***
        $sql = "SELECT *
                  FROM ".$cfg["bloglink"]["db"]["entries"]."
                 WHERE ".$cfg["bloglink"]["db"]["key"]." LIKE '1692582295.%'
              ORDER BY ".$cfg["bloglink"]["db"]["order"]."
                 LIMIT 0,10";

        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
        $result = $db -> query($sql);
        while ( $data = $db -> fetch_array($result,1) ) {
            $preg = "|\[[^\]]+\](.*)\[/[^\]]+\]|U";
            preg_match_all($preg, $data["content"], $match, PREG_PATTERN_ORDER );
            #$debugging["ausgabe"] .= "<pre>".print_r($match,True)."</pre>";

            $dataloop["bloglink"][$match[1][1]][0] = $match[1][1];
            $dataloop["bloglink"][$match[1][1]][1] = $pathvars["virtual"]."/blog/".$data["kategorie"].".html";

            #$dataloop["bloglink"][$data["id"]][0] = $data["teaser"];
            #$dataloop["bloglink"][$data["id"]][1] = $data["entry"];
        }
        $hidedata["bloglink"][0] = "enable";

        // +++
        // funktions bereich


        // page basics
        // ***

        // fehlermeldungen
        if ( $_GET["error"] != "" ) {
            if ( $_GET["error"] == 1 ) {
                $ausgaben["form_error"] = "#(error1)";
            }
        } else {
            #$ausgaben["form_error"] = "";
        }

        // navigation erstellen
        #$ausgaben["new"] = "<a href=\"".$cfg["bloglink"]["basis"]."/add.html\">#(new)</a>";

        // hidden values
        #$ausgaben["form_hidden"] .= "";

        // was anzeigen
        #$mapping["main"] = eCRC($environment["ebene"]).".list";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($_GET["edit"]) ) {
            #$ausgaben["inaccessible"] = "inaccessible values:<br />";
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
