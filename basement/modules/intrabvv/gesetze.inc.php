<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "short description";
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

    #if ( count($HTTP_POST_VARS) == 0 ) {
    #    $sql = "SELECT ".$cfg["db"]["key"]." FROM ".$cfg["db"]["entries"];
    #    $result = $db -> query($sql);
    #    $form_values = $db -> fetch_array($result,$nop);
    #} else {
    #    $form_values = $HTTP_POST_VARS;
    #}

    // form options holen
    $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

    // form elememte bauen
    $element = form_elements( $cfg["db"]["entries"], $form_values );

    // navigation erstellen
    if ( $rechte[$cfg["right"]["adress"]] == -1 ) {
        $ausgaben["new"] = "<a href=\"".$cfg["basis"]."/modify,add.html\"><img src=\"".$pathvars["images"]."/button-gv-neu.png\" width=\"80\" height=\"18\" border=\"0\"></a>";
        #$aktion .= "<a href=\"".$environment["basis"]."/".$value[0].$name.",".$field[$db_entries_key].".html\"><img src=\"".$imgpath."/".$name.".png\" border=\"0\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></a>";
    } else {
        $ausgaben["new"] = "<img src=\"".$pathvars["images"]."/pos.png\" width=\"80\" height=\"18\" border=\"0\">";
        #$aktion .= "<img src=\"".$imgpath."/pos.png\" alt=\"\" width=\"24\" height=\"18\">";
    }

    // gueltigkeitsverzeichnis link
    $ausgaben["mask_target"] = "?hijack=http://fmwelt.stmf.bybn.de/service/informationsbroschueren/sonstige/gueltigkeitsverzeichnis/gueltigkeitsverzeichnis.pdf";

    // wohin schicken
    $ausgaben["form_error"] = "";
    $ausgaben["form_aktion"] = $cfg["basis"]."/list.html";

    // was anzeigen
    $mapping["main"] = crc32($environment["ebene"]).".auswahlmenu";
    $mapping["navi"] = "leer";

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
