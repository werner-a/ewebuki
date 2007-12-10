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

    86343 Königsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( priv_check("/".$cfg["subdir"]."/".$cfg["name"],$cfg["right"]) ||
        priv_check_old("",$cfg["right"]) ) {

        if ( $environment["parameter"][1] == "up" ) {
            $sql = "UPDATE ".$cfg["db"]["menu"]["entries"]."
                       SET sort=sort-11
                     WHERE mid='".$environment["parameter"][2]."'";
            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $db -> query($sql);
        } elseif ( $environment["parameter"][1] == "down" ) {
            $sql = "UPDATE ".$cfg["db"]["menu"]["entries"]."
                       SET sort=sort+11
                     WHERE mid='".$environment["parameter"][2]."'";
            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $db -> query($sql);
        }

        // alle sollen neu numeriert werden
        if ( $environment["parameter"][1] == "all" ) {
            $all = -1;
        }

        // ob up, down, oder all renumber funktion aufrufen
        renumber($cfg["db"]["menu"]["entries"], $cfg["db"]["lang"]["entries"], $environment["parameter"][3], $all);
        header("Location: ".$cfg["basis"]."/list,".$environment["parameter"][3].",".$environment["parameter"][4].".html");
    }

///////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
