<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "kunden-mask";
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


    //
    // Suchmaske anzeigen
    //

    $position = $environment["parameter"][1]+0;

    // form elememte bauen
    $element = form_elements( $cfg["db"]["entries"], $nop );

    // dropdown Kategorie erstellen (mor 0204)
    // ***
    $selected = "";
    $sql = "SELECT * FROM ".$cfg["db"]["entries_kago"]." ORDER BY ".$cfg["db"]["order_kago"];
    $result = $db -> query($sql);
    $formularobject  = "<select class=\"".$cfg["form_defaults"]["class"]["dropdown"]."\" name=\"akkate\">\n";
    $formularobject .= "<option value=\"\">Bitte auswählen</option>\n";
    while ( $data = $db->fetch_array($result,$nop) ) {
        $formularobject .= "<option value=\"".$data["katid"]."\">" .$data["kate"] ."</option>\n";
    }
    $formularobject .= "</select>";
    foreach($element as $name => $value) {
        if ($name == "akkate") {
            $element[$name] = $formularobject;
        }
    }
    // +++
    // dropdown Kategorie erstellen (mor 0204)

    // dropdown Dienststelle erstellen (mor 3004)
    // ***
    $sql = "SELECT adkate, adststelle, adbnet, adcnet, adid FROM db_adrd ORDER BY adsort";
    $result = $db -> query($sql);
    $formularobject  = "<select class=\"".$cfg["form_defaults"]["class"]["dropdown"]."\" name=\"akdst\">\n";
    $formularobject .= "<option value=\"\">Bitte auswählen</option>\n";
    while ( $data = $db->fetch_array($result,$nop) ) {
        $formularobject .= "<option value=\"".$data["adid"]."\">".$data["adkate"]." ".$data["adststelle"] ."</option>\n";
    }
    $formularobject .= "</select>";
    $element["akdienst"] = $formularobject;

    // +++
    //  dropdown Dienststelle erstellen (mor 3004)

    // dropdown BFD-Bereich erstellen (mor 3004)
    // ***
    $sql = "SELECT adstbfd, adbnet, adcnet FROM db_adrd WHERE adkate = \"BFD\" ORDER BY adstbfd";
    $result = $db -> query($sql);
    $formularobject  = "<select class=\"".$cfg["form_defaults"]["class"]["dropdown"]."\" name=\"abnet\">\n";
    $formularobject .= "<option value=\"\">Bitte auswählen</option>\n";
    while ( $data = $db->fetch_array($result,$nop) ) {
        $formularobject .= "<option value=\"".$data["adbnet"]."\">".$data["adstbfd"]."</option>\n";
    }
    $formularobject .= "</select>";
    $element["akbfd"] = $formularobject;
    // +++
    //  dropdown Dienststelle erstellen (mor 3004)

    // was anzeigen
    $mapping["main"] = crc32($environment["ebene"]).".".$environment["kategorie"];

    // wohin schicken
    #echo $environment["basis"]."/list.html";
    $ausgaben["form_aktion"] = $environment["basis"]."/list.html";

    // referer im form mit hidden element mitschleppen
    if ( $HTTP_POST_VARS["form_referer"] == "" ) {
        $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
        $ausgaben["form_break"] = $ausgaben["form_referer"];
    } else {
        $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
        $ausgaben["form_break"] = $ausgaben["form_referer"];
    }


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
