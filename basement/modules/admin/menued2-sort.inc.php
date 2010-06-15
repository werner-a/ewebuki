<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "menued - sortier und neu nummerier script";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001, 2002, 2003 Werner Ammon <wa@chaos.de>

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

    86343 K�nigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    $kategorie2check = substr(make_ebene($environment["parameter"][2]),0,strpos(make_ebene($environment["parameter"][2]),"/"));
    $ebene2check = substr(make_ebene($environment["parameter"][2]),strpos(make_ebene($environment["parameter"][2]),"/"));

    if ( $environment["parameter"][1] != "all" ) {
        // um bei den menupunkten die Reihenfolge veraendern zu koennen muss man das recht fuer den uebergeordneten Punkt besitzen
        $sql = "SELECT refid FROM ".$cfg["menued"]["db"]["menu"]["entries"]." WHERE mid='".$environment["parameter"][2]."'";
        $result = $db -> query($sql);
        $refid = $db -> fetch_array($result,1);

        $kategorie2check_2 = substr(make_ebene($refid["refid"]),0,strpos(make_ebene($refid["refid"]),"/"));
        $ebene2check_2 = substr(make_ebene($refid["refid"]),strpos(make_ebene($refid["refid"]),"/"));

        if ( ( $specialvars["security"]["new"] == -1 && priv_check(make_ebene($environment["parameter"][2]),$cfg["menued"]["modify"]["sort"][2]) && priv_check(make_ebene($refid["refid"]),$cfg["menued"]["modify"]["sort"][2]) ) ||
            ( $specialvars["security"]["new"] != -1 && ( function_exists(priv_check_old) && priv_check_old("",$cfg["menued"]["right_admin"]) || ( right_check("-1",$ebene2check,$kategorie2check) != "" && right_check("-1",$ebene2check_2,$kategorie2check_2) ) ) ) ) {

            if ( $environment["parameter"][1] == "up" ) {
                $sql = "UPDATE ".$cfg["menued"]["db"]["menu"]["entries"]."
                           SET sort=sort-11
                         WHERE mid='".$environment["parameter"][2]."'";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $db -> query($sql);
            } elseif ( $environment["parameter"][1] == "down" ) {
                $sql = "UPDATE ".$cfg["menued"]["db"]["menu"]["entries"]."
                           SET sort=sort+11
                         WHERE mid='".$environment["parameter"][2]."'";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $db -> query($sql);
            }
        }
    }

    // alle sollen neu numeriert werden
    if ( $environment["parameter"][1] == "all" ) {
        $all = -1;
    }

    // ob up, down, oder all renumber funktion aufrufen
    renumber($cfg["menued"]["db"]["menu"]["entries"], $cfg["menued"]["db"]["lang"]["entries"], $environment["parameter"][3], $all);
    header("Location: ".$cfg["menued"]["basis"]."/list,".$environment["parameter"][3].",".$environment["parameter"][4].".html");

///////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
