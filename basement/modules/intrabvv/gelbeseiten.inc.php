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



    // dropdown bfd aus db erstellen (wach 0404)
    // ***
    $sql = "SELECT adid,adkate, adstbfd FROM db_adrd WHERE adkate= 'BFD' ORDER by adsort";
    $result = $db -> query($sql);
    $formularobject[5] = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"abdstbfd\">\n";
    $formularobject[5] .= "<option value=\"\">Alle</option>\n";
    while ( $field = $db->fetch_row($result,$nop) ) {
        $formularobject[5] .= "<option value=\"".$field[2]."\"".$selected.">".$field[2]."</option>\n";
    }
    $formularobject[5] .= "</select>\n";
    // +++
    // dropdown bfd aus db erstellen


    // dropdown interessen aus db erstellen (wach 1104)
    // ***
    $sql = "SELECT abint FROM db_adrb_int ORDER by abint_sort";
    $result = $db -> query($sql);
    $gesamt = $db -> num_rows($result);
    $menge = $gesamt / 2;
    $menge = (int) $menge +1;
    $formularobject[3] = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"abinteressen\">\n";
    $formularobject[3] .= "<option value=\"\">Alle</option>\n";

    if ( !is_array($form_values["abinteressen"]) ) $form_values["abinteressen"] = explode(";", $form_values["abinteressen"]);
    for ( $i=1; $i <= $gesamt; $i++ ) {
        $field = $db->fetch_array($result,$nop);
        if ( $i <= $menge ) {
            $seite = "links";
        } else {
            $seite = "rechts";
        }
        $$seite .= "<tr>";
        $$seite .= "<td><li type=\"square\">".$field["abint"]."</li></td>";
        $$seite .= "</tr>";
        $formularobject[3] .= "<option value=\"".$field[0]."\"".$selected.">".$field[0] ."</option>\n";
    }
    $formularobject[3] .= "</select>\n";
    // +++
    // dropdown interessen aus db erstellen


    // beschrieb ausgabe
    // ***
    $links = "<ul>".$links;
    $rechts = $rechts." </ul";

    $ausgaben["beschrieb"]  = "<table width=\"100%\"><tr>";
    $ausgaben["beschrieb"] .= "<td valign=\"top\"><table".$links."</table></td><td valign=\"top\"><table>".$rechts."</table></td>";
    $ausgaben["beschrieb"] .= "</tr></table>";
    // +++
    // beschrieb ausgabe


    // abdstbfd nicht in db_adrb enthalten
    $element["abdstbfd"] = $formularobject[5];
    $element["abinteressen"] = $formularobject[3];


    // referer im form mit hidden element mitschleppen
    if ( $HTTP_POST_VARS["form_referer"] == "" ) {
        $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
        $ausgaben["form_break"] = $ausgaben["form_referer"];
    } else {
        $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
        $ausgaben["form_break"] = $ausgaben["form_referer"];
    }


    // was anzeigen
    $mapping["main"] = crc32($environment["ebene"]).".".$environment["kategorie"];


    // wohin schicken
    $ausgaben["form_aktion"] = $environment["basis"]."adressen/beschaeftigte/list.html";



    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
