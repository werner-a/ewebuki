<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id$";
  $Script["desc"] = "short description";
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

    if ( $cfg["right"] == "" ||
        priv_check("/".$cfg["subdir"]."/".$cfg["name"],$cfg["right"]) ||
        priv_check_old("",$cfg["right"]) ) {

        // funktions bereich
        // ***

        $sql = "SELECT *
                  FROM ".$cfg["db"]["user"]["entries"]."
                 WHERE ".$cfg["db"]["user"]["key"]."='".$environment["parameter"][1]."'";
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
        $result = $db -> query($sql);
        $data = $db -> fetch_array($result,1);

        $ausgaben["login"]    = $data[$cfg["db"]["user"]["login"]];
        $ausgaben["surname"]  = $data[$cfg["db"]["user"]["surname"]];
        $ausgaben["forename"] = $data[$cfg["db"]["user"]["forename"]];
        $ausgaben["email"]    = $data[$cfg["db"]["user"]["email"]];

        // level management form form elemente begin
        // ***
        $sql = "SELECT auth_right.lid, auth_level.level
                  FROM auth_level
            INNER JOIN auth_right ON auth_level.lid = auth_right.lid
                 WHERE auth_right.uid = ".$environment["parameter"][1]."
              ORDER BY level";
        $result = $db -> query($sql);
        while ( $all = $db -> fetch_array($result,1) ) {
            if ( isset($ausgaben["level"]) ) $ausgaben["level"] .= ", ";
            $ausgaben["level"] .= $all["level"]."";
        }
        if ( !isset($ausgaben["level"]) ) $ausgaben["level"] = "---";
        // +++
        // level management form form elemente end

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

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($_GET["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error1) #(error1)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        $ausgaben["back"] = $_SERVER["HTTP_REFERER"];
        $ausgaben["edit"] = $cfg["basis"]."/edit,".$environment["parameter"][1].".html";

        // was wird angezeigt
        $mapping["main"] = crc32($environment["ebene"]).".details";

        // +++
        // page basics

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
