<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id$";
  $Script["desc"] = "short description";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    phpWEBkit - a easy website building kit
    Copyright (C)2001, 2002, 2003 Werner Ammon <wa@chaos.de>

    This script is a part of phpWEBkit

    phpWEBkit is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    phpWEBkit is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with phpWEBkit; If you did not, you may download a copy at:

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

    $sql = "SELECT * from ".$cfg["db"]["entries"]." ORDER BY ".$cfg["db"]["order"];
    $result = $db -> query($sql);
    $gesamt = $db -> num_rows($result);
    $menge = $gesamt / 3;
    $menge = (int) $menge +1;

    $ausgaben["output"] .= "<table width=\"100%\"><tr>";
    for ( $i=1; $i <= $gesamt; $i++ ) {
        $data = $db->fetch_array($result,$nop);
        if (($data["adkate"] == "BFD") || ($data["adkate"] == "StMF")) {
            $class = "class=\"hervorgehoben\"";
            if ($i > 1)$br = "<br><br>";
        } else {
            $test = "";
            $br = "";
            $class = "";
        }
        #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= $data["adinternet"].$debugging["char"];
        if ( $i <= $menge ) {
            $seite = "links";
        } elseif ( $i <= $menge * 2 ) {
            $seite = "mitte";
        } else {
            $seite = "rechts";
        }
        $$seite .= "<tr>";
        #$$seite .= "<td>".$data["adakz"]."</td><td>".$data["adkate"]."</td><td><a href=\"".$data["adintranet"]."\">".$data["adststelle"]."</a></td>";
        $$seite .= "<td ".$class.">".$br."<a href=\"".$data["adintranet"]."/net/ger/index.html\">".$data["adkate"]."</a>".$br."</td><td ".$class.">".$br."<a href=\"".$data["adintranet"]."\">".$data["adststelle"]."</a>".$br."</td>";
        $$seite .= "</tr>";
    }
    $ausgaben["output"] .= "<td valign=\"top\"><table".$links."</table></td><td valign=\"top\"><table>".$mitte."</table></td><td valign=\"top\"><table>".$rechts."</table></td>";
    $ausgaben["output"] .= "</tr></table>";

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
