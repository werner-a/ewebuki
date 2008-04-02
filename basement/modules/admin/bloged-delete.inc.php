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

    if ( $rechte[$cfg["bloged"]["right"]] == "" || $rechte[$cfg["bloged"]["right"]] == -1 ) {

        // datensatz holen
        $sql = "SELECT Cast(SUBSTR(content,6,19) as DATETIME) AS date,content,tname
                    FROM ".$cfg["bloged"]["db"]["bloged"]["entries"]."
                    WHERE ".$cfg["bloged"]["db"]["bloged"]["key"]."='".crc32(make_ebene($environment["parameter"][1])).".".$environment["parameter"][2]."' AND
                    content REGEXP '^\\\[!\\\]1'";

        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
        $result = $db -> query($sql);
        $data = $db -> fetch_array($result,$nop);
        $test = preg_replace("|\r\n|","\\r\\n",$data["content"]);
        foreach ( $cfg["bloged"]["blogs"][make_ebene($environment["parameter"][1])]["tags"] as $key => $value ) {

            (strpos($value,"=")) ? $endtag= substr($value,0,strpos($value,"=")): $endtag=$value;
            if ( $endtag == "IMG" ) {
                $preg = "\[IMG=\/file\/(png|jpg|gif)\/([0-9]*)\/(.*)\[\/".$endtag."\]";
            } else {
                $preg = "\[".$value."\](.*)\[\/".$endtag."\]";
            }
            if ( preg_match("/$preg/U",$test,$regs) ) {
                if ( $endtag == "IMG" ) {
                    $$key = $regs[2].".".$regs[1];
                } else {
                    $$key = str_replace('\r\n',"<br>",$regs[1]);
                }
            } else {
                $$key = "unknown";
            }
            $dataloop["list"][$counter][$key] = $$key;
        }
            $dataloop["list"][$counter]["datum"] = substr($data["date"],8,2).".".substr($data["date"],5,2).".".substr($data["date"],0,4);

        // page basics
        // ***

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // navigation erstellen
        $ausgaben["form_aktion"] = $pathvars["virtual"].$environment["ebene"]."/delete,".$environment["parameter"][1].",".$environment["parameter"][2].".html";
        $ausgaben["form_break"] = $cfg["bloged"]["basis"]."/list.html";

        // hidden values
        $ausgaben["form_hidden"] = "";
        $ausgaben["form_delete"] = True;

        // was anzeigen
        $mapping["main"] = "-2051315182.delete";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        // ***
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_result) #(error_result)<br />";
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
        if (  $HTTP_POST_VARS["send"] != "" ) {
            $data["content"] = preg_replace("/^\[!\]1/","[!]0",$data["content"]);
            // datensatz loeschen
            if ( $ausgaben["form_error"] == "" ) {
                $sql = "UPDATE ".$cfg["bloged"]["db"]["bloged"]["entries"]." SET content = '".$data["content"]."' WHERE ".$cfg["bloged"]["db"]["bloged"]["key"]."='".crc32(make_ebene($environment["parameter"][1])).".".$environment["parameter"][2]."' AND content REGEXP '^\\\[!\\\]1'";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $result  = $db -> query($sql);
                if ( !$result ) $ausgaben["form_error"] = $db -> error("#(error_result)<br />");
            }
            // +++
            // ohne fehler menupunkte loeschen

            // wohin schicken
            if ( $ausgaben["form_error"] == "" ) {
                header("Location: ".$pathvars["virtual"].make_ebene($environment["parameter"][2]).".html");
            }
        }
        // +++
        // das loeschen wurde bestaetigt, loeschen!

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
