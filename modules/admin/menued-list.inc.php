<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// menued-list.inc.php v1 emnili
// menued - list funktion
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2015 Werner Ammon ( wa<at>chaos.de )

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

    86343 Koenigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( $rechte[$cfg["right"]] == -1 ) {

        $modify  = array (
            "add"       => array("", "#(button_desc_add)", $cfg["right"]),
            "edit"      => array("", "#(button_desc_edit)", $cfg["right"]),
            "delete"    => array("", "#(button_desc_delete)", $cfg["right"]),
            "up"        => array("sort,", "#(button_desc_up)", $cfg["right"]),
            "down"      => array("sort,", "#(button_desc_down)", $cfg["right"]),
            "move"      => array("", "#(button_desc_move)", $cfg["right"]),
        );

        // bei eingeschalteten content recht wird button hinzugefuegt
        if ( $specialvars["security"]["enable"] == -1 ) {
            $modify["rights"] = array("", "#(button_desc_right)", $cfg["right"]);
        }

        $ausgaben["output"] .= sitemap(0, "menued", $modify);

        // fehlermeldungen
        if ( $_GET["error"] != "" ) {
            if ( $_GET["error"] == 1 ) {
                $ausgaben["form_error"] = "#(error1)";
            }
        } else {
            $ausgaben["form_error"] = "";
        }

        // navigation erstellen
        $ausgaben["renumber"] = "<a href=\"".$cfg["basis"]."/sort,all,nop,0.html\">#(renumber)</a>";
        $ausgaben["new"] = "<a href=\"".$cfg["basis"]."/add,0.html\">g(new)</a>";

        // was anzeigen
        $mapping["main"] = eCRC($environment["ebene"]).".list";
        $mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($_GET["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error1) #(error1)<br />";
            $ausgaben["inaccessible"] .= "# (disabled) #(disabled)<br />";
            $ausgaben["inaccessible"] .= "# (enabled) #(enabled)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        #n/a
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
