<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "Dienststellen Suchmaske";
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
    if ( $environment["kategorie"] == "mask" ) {

        #$form_values = $HTTP_POST_VARS;
        $position = $environment["parameter"][1]+0;

        // form options holen
        $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

        // form elememte bauen
        $element = form_elements( $cfg["db"]["entries"], $nop );


        // dropdown Kategorie erstellen (schae 1504)
        // ***
        $sql = "SELECT DISTINCT adkate FROM db_adrd ORDER BY adkate";
        $result = $db -> query($sql);
        $formularobject  = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"adkate\">\n";
        $formularobject .= "<option value=\"\"></option>\n";
        while ( $data = $db->fetch_array($result,$nop) ) {
            if ($form_values["adkate"] == $data["adkate"]) {
                $selected = " selected";
            } else {
                $selected = "";
            }
            $formularobject .= "<option value=\"".$data["adkate"]."\"".$selected.">" .$data["adkate"] ."</option>\n";
        }
        foreach($element as $name => $value) {
            if ($name == "adkate") {
                $element[$name] = $formularobject;
            }
        }
        $formularobject .= "</select>";
        // +++
        // dropdown Kateogrie erstellen (schae 1504)

        // dropdown Dienststelle erstellen (schae 1504)
        // ***
        $sql = "SELECT DISTINCT adststelle FROM db_adrd ORDER BY adststelle";
        $result = $db -> query($sql);
        $formularobject  = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"adststelle\">\n";
        $formularobject .= "<option value=\"\"></option>\n";
        while ( $data = $db->fetch_array($result,$nop) ) {
            if ($form_values["adststelle"] == $data["adststelle"]) {
                $selected = " selected";
            } else {
                $selected = "";
            }
            $formularobject .= "<option value=\"".$data["adststelle"]."\"".$selected.">" .$data["adststelle"] ."</option>\n";
        }
        foreach($element as $name => $value) {
            if ($name == "adststelle") {
                $element[$name] = $formularobject;
            }
        }
        $formularobject .= "</select>";
        // +++
        // dropdown Dienststelle erstellen (schae 1504)


        // dropdown BFD erstellen (schae 250403)
        // ***
        $sql = "SELECT DISTINCT adstbfd FROM db_adrd ORDER BY adstbfd";
        $result = $db -> query($sql);
        $formularobject  = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"adstbfd\">\n";
        $formularobject .= "<option value=\"\"></option>\n";
        while ( $data = $db->fetch_array($result,$nop) ) {
            if ($form_values["adstbfd"] == $data["adstbfd"]) {
                $selected = " selected";
            } else {
                $selected = "";
            }
            $formularobject .= "<option value=\"".$data["adstbfd"]."\"".$selected.">" .$data["adstbfd"] ."</option>\n";
        }
        foreach($element as $name => $value) {
            if ($name == "adstbfd") {
                $element[$name] = $formularobject;
            }
        }
        $formularobject .= "</select>";
        // +++
        // dropdown BFD erstellen (schae 250403)


        // ***
        // dropdown Amtsleiter erstellen (schae 230403)
        $sql = "SELECT * FROM db_adrb WHERE abbnet='".$ip_class[1]."' AND abcnet= '".$ip_class[2]."' ORDER BY abnamra";
        $result = $db -> query($sql);
        $formularobject  = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"adleiter\">\n";
        $formularobject .= "<option value=\"\"></option>\n";
        while ( $data = $db->fetch_array($result,$nop) ) {
            if ($form_values["adleiter"] == $data["abid"]) {
                $selected = " selected";
            } else {
                $selected = "";
            }
            $formularobject .= "<option value=\"".$data["abid"]."\"".$selected.">" .$data["abnamra"]." ".$data["abnamvor"] ."</option>\n";
        }
        foreach($element as $name => $value) {
            if ($name == "adleiter") {
                $element[$name] = $formularobject;
            }
        }
        $formularobject .= "</select>";
        // +++
        // dropdown Amtsleiter erstellen (schae 230403)

        // was anzeigen
        $mapping["main"] = crc32($environment["ebene"]).".".$environment["kategorie"];

        // wohin schicken
        $ausgaben["form_aktion"] = $environment["basis"]."/list,".$position.",esearch.html";

    //


    }


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
