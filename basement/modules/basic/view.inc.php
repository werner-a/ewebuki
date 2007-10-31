<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id$";
  $Script["desc"] = "picture viewer";
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

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** ".$script["name"]." ** ]".$debugging["char"];

        // page basics
        // ***

        // warnung ausgeben
        if ( get_cfg_var('register_globals') == 1 ) $debugging["ausgabe"] .= "Warnung: register_globals in der php.ini steht auf on, evtl werden interne Variablen ueberschrieben!".$debugging["char"];

        // path fuer die schaltflaechen anpassen
        if ( $cfg["iconpath"] == "" ) $cfg["iconpath"] = "/images/default/";

        // label bearbeitung aktivieren
        if ( isset($_GET["edit"]) ) {
            $specialvars["editlock"] = 0;
        } else {
            $specialvars["editlock"] = -1;
        }

        // +++
        // page basics


        // funktions bereich
        // ***

        // back link
        $ausgaben["referer"] = dirname($pathvars["requested"]).".html";

        if ( $environment["parameter"][3] != "" ) {
            // selection mode
            $sql = "SELECT *
                      FROM ".$cfg["db"]["entries"]."
                     WHERE fhit like '%#p".$environment["parameter"][3]."%'
                  ORDER BY ".$cfg["db"]["order"];
        } else {
            // picture mode
            $sql = "SELECT *
                      FROM ".$cfg["db"]["entries"]."
                     WHERE fid =".$environment["parameter"][2];
        }
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
        $result = $db -> query($sql);

        while ( $data = $db -> fetch_array($result,1) ) {

            // selection mode - part1
            if ( $environment["parameter"][3] != "" ) {

                if ( $data["fid"] == $environment["parameter"][2] ){
                    $color = $cfg["color"]["selected"];
                } else {
                    $color = "none";
                }

                preg_match("/#p".$environment["parameter"][3]."[,]*([0-9]*)#/i",$data["fhit"],$match);
                $dataloop["thumbs"][] = array(
                       "id" => $data["fid"],
                     "sort" => $match[1],
                     "type" => $data["ffart"],
                      "src" => $pathvars["filebase"]["webdir"].$data["ffart"]."/".$data["fid"]."/tn/".$data["ffname"],
                     "link" => $pathvars["virtual"].$environment["ebene"]."/view,".$environment["parameter"][1].",".$data["fid"].",".$environment["parameter"][3].",".$environment["parameter"][4].".html",
                    "title" => $data["funder"],
                       "bg" => $color,
                );
            }

            if ( $data["fid"] == $environment["parameter"][2] ) {
                $filename = $data["ffname"];
                $filetyp = $data["ffart"];
                $ausgaben["beschriftung"] = $data["funder"];
                $ausgaben["beschreibung"] = $data["fdesc"];
            }
        }

        // selection mode - part2
        if ( $environment["parameter"][3] != "" ) {

            // thumbs sortieren
            foreach ($dataloop["thumbs"] as $key => $row) {
               $sort[$key]  = $row['sort'];
            }
            array_multisort($sort, $dataloop["thumbs"]);

            // thumb aktuell
            foreach ($dataloop["thumbs"] as $key => $row) {
               if ( $environment["parameter"][2] == $row['id'] ) {
                    $aktuell = $key;
                    $ausgaben["aktuell"] = $aktuell +1;
               }
            }

            // thumbs gesamt
            $ende = count($dataloop["thumbs"]) -1;
            $ausgaben["gesamt"] = $ende +1;

            // previous link
            if ( $aktuell == 0 ) {
                $prev = $dataloop["thumbs"][($ende)]["id"];
            } else {
                $prev = $dataloop["thumbs"][($aktuell-1)]["id"];
            }
            $ausgaben["prev"] = "view,".$environment["parameter"][1].",".$prev.",".$environment["parameter"][3].",".$environment["parameter"][4].".html";

            // next link
            if ( $aktuell == $ende ) {
                $next = $dataloop["thumbs"][0]["id"];
            } else {
                $next = $dataloop["thumbs"][($aktuell+1)]["id"];
            }
            $ausgaben["next"] = "view,".$environment["parameter"][1].",".$next.",".$environment["parameter"][3].",".$environment["parameter"][4].".html";

            // navi einblenden
            $hidedata["navi"][0] = "enable";

            // picture link
            $ausgaben["href"] = $ausgaben["next"];
        } else {
            // picture link
            $ausgaben["href"] = $ausgaben["referer"];
        }


        // img werte
        if ( $pathvars["filebase"]["realname"] == True ) {
            $img = $filetyp."/".$environment["parameter"][2]."/".$environment["parameter"][1]."/".$filename;
        } else {
            $img = $pathvars["filebase"]["pic"]["root"].$pathvars["filebase"]["pic"][$environment["parameter"][1]]."img_".$environment["parameter"][2].".".$filetyp;
        }

        $imgfile = $pathvars["filebase"]["maindir"].$img;
        $ausgaben["imgurl"] = $pathvars["webroot"].$pathvars["filebase"]["webdir"].$img;
        if ( file_exists($imgfile) ) {
            $imgsize = getimagesize($imgfile);
            $ausgaben["imgsize"] = " ".$imgsize[3];
        }

        // thumbs mode
        if ( $environment["parameter"][4] != "" ) {
            $hidedata["thumbs"][0] = "enable";
        }

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
            $ausgaben["form_error"] = "";
        }

        // navigation erstellen
        #$ausgaben["add"] = $cfg["basis"]."/add,".$environment["parameter"][1].",verify.html";
        #$mapping["navi"] = "leer";

        // hidden values
        #$ausgaben["form_hidden"] .= "";

        // was anzeigen
        #$mapping["main"] = crc32($environment["ebene"]).".list";
        $mapping["main"] = "viewer";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($_GET["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error1) #(error1)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        #n/a

        // +++
        // page basics

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>