<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id$";
  $script["desc"] = "info-markt ctrl";
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

    // ebenen setzen
    $ebene = explode("/", $environment["ebene"]."/".$environment["kategorie"]);

    $cfg["ebene"]["eins"] = $ebene[1];
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "ebene eins: ".$cfg["ebene"]["eins"].$debugging["char"];
    $cfg["ebene"]["zwei"] = $ebene[2];
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "ebene zwei: ".$cfg["ebene"]["zwei"].$debugging["char"];
    $cfg["ebene"]["drei"] = $ebene[3];
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "ebene drei: ".$cfg["ebene"]["drei"].$debugging["char"];
    $cfg["ebene"]["vier"] = $ebene[4];
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "ebene vier: ".$cfg["ebene"]["vier"].$debugging["char"];

    // uebersicht ebene eins
    if ( $cfg["ebene"]["zwei"] == "" ) {
        $uebersichtsql = " AND iuebersicht = -1";
        include $pathvars["addonroot"].$cfg["subdir"]."/".$cfg["name"]."-list.inc.php";
        #$ausgaben["output"] .= "SELECT * FROM db_info WHERE ifqdn0 = '".$environment["fqdn"][0]."'";
        #$ausgaben["output"] .= "Ausgabe der Übersicht mit aktuellem(n) Artikel(n)";

    // uebersicht ebene zwei
    } else {
        #$ausgaben["output"]  = "<b>Ebene eins:</b> ".$cfg["ebene"]["eins"]."<br>";
        #$ausgaben["output"] .= "<b>Ebene zwei:</b> ".$cfg["ebene"]["zwei"]."<br>";
        #$ausgaben["output"] .= "<b>Ebene drei:</b> ".$cfg["ebene"]["drei"]."<br>";

        if ( in_array($environment["kategorie"], $cfg["function"]) ) {
            include $pathvars["addonroot"].$cfg["subdir"]."/".$cfg["name"]."-".$environment["kategorie"].".inc.php";
        } elseif ( $cfg["ebene"]["vier"] == "" ) {
            include $pathvars["addonroot"].$cfg["subdir"]."/".$cfg["name"]."-list.inc.php";
        } else {
            # evtl. include fuer unterseiten
        }
    }

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
