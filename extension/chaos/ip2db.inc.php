<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id$";
  $Script["desc"] = "ip2db - dyndns fallback";
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

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** ".$script["name"]." ** ]".$debugging["char"];

    if ( $environment["kategorie"] != "ip2db" ) {
        # db schreiben

        if ( $environment["parameter"][1] == "create" ) {
            $sqla = "name, ip";
            $sqlb = "'".$environment["kategorie"]."', '".$_SERVER["REMOTE_ADDR"]."'";
            $sql = "insert into ".$cfg["db"]["entries"]." (".$sqla.") VALUES (".$sqlb.")";
        } else {
            $sqla = "name = '".$environment["kategorie"]."', ip = '".$_SERVER["REMOTE_ADDR"]."'";
            $sql  = "update ".$cfg["db"]["entries"]." SET ".$sqla." WHERE ".$cfg["db"]["key"]."='".$environment["kategorie"]."'";
        }
        $result  = $db -> query($sql);

        if ( $result ) {
            $ausgaben["output"] = "sucess";
        }

    } else {
        # db lesen
        $sql = "SELECT * FROM ".$cfg["db"]["entries"]." ORDER by ".$cfg["db"]["order"];
        $result = $db -> query($sql);
        while ( $data = $db -> fetch_array($result,$nop) ) {
            $ausgaben["output"] .= $data["name"]." -> ".$data["ip"]."<br>";
        }
    }

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
