<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id$";
  $Script["desc"] = "listendruck";
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

    //
    //  drucken
    //
    if ( $environment["kategorie"] == "print" ) {
        #echo $pathvars["addonroot"].$cfg["subdir"]."/".$cfg["name"]."-print.inc.php";
        include $pathvars["addonroot"].$cfg["subdir"]."/".$cfg["name"]."-print.inc.php";

        if ( $environment["parameter"][1] == "telext" ) {
            include $pathvars["addonroot"].$cfg["subdir"]."/".$cfg["name"]."-print-art1.inc.php";
        } elseif ( $environment["parameter"][1] == "dststelle"  )  {
            include $pathvars["addonroot"].$cfg["subdir"]."/".$cfg["name"]."-print-art2.inc.php";
        } elseif ( $environment["parameter"][1] == "kunden"  )  {
            include $pathvars["addonroot"].$cfg["subdir"]."/".$cfg["name"]."-print-art2.inc.php";
        } else {
            include $pathvars["addonroot"].$cfg["subdir"]."/".$cfg["name"]."-print-art0.inc.php";
        }

        // was anzeigen
        exit(); // nichts


    //
    // felder auswahl
    //
    } elseif ( $environment["kategorie"] == "felder" ) {

        include $pathvars["addonroot"].$cfg["subdir"]."/".$cfg["name"]."-felder.inc.php";

        // was anzeigen
        $mapping["navi"] = "leer";


    //
    // auswahl listen art
    //
    } elseif ( $environment["kategorie"] == "list" || $environment["kategorie"] == $cfg["name"] ) {

        include $pathvars["addonroot"].$cfg["subdir"]."/".$cfg["name"]."-art.inc.php";

        // was anzeigen
        $mapping["main"] = "-1794872881.list";
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "<font color=\"#FF0000\">ATTENTION: template overwrite -> ".$mapping["main"].".tem.html</font>".$debugging["char"];

    }

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
