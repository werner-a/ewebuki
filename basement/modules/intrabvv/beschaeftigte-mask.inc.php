<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "Beschaeftigte Suchmaske";
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
    // suchmaske anzeigen
    //
    if ( $environment[kategorie] == "mask" ) {
        $position = $environment["parameter"][1]+0;
        $form_values = $HTTP_POST_VARS;

        // form options holen
        $form_options = form_options(crc32($environment[ebene]).".".$environment[kategorie]);

        // form elememte bauen
        $element = form_elements( $cfg["db"]["entries"], $HTTP_POST_VARS );

        // dropdown amtsbezeichnung aus db erstellen (wach 0304)
        // ***
        $sql = "SELECT abamtbez_id, abamtbezkurz FROM db_adrb_amtbez ORDER by abamtbez_sort";
        $result = $db -> query($sql);
        $formularobject[1] = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"abamtbez\">\n";
        $formularobject[1] .= "<option value=\"\">Bitte auswählen</option>\n";

        while ( $field = $db->fetch_row($result,$nop) ) {
            $formularobject[1] .= "<option value=\"".$field[0]."\"".$selected.">".$field[1] ."</option>\n";
        }
        $formularobject[1] .= "</select>\n";
        // +++
        // dropdown amtsbezeichnung aus db erstellen


        // dropdown dienstposten aus db erstellen (wach 0404)
        // ***
        $sql = "SELECT abdienst_id, abdienst FROM db_adrb_dienst ORDER by abdienst_sort";
        $result = $db -> query($sql);
        $formularobject[2] = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"abdstposten\">\n";
        $formularobject[2] .= "<option value=\"\">Bitte auswählen</option>\n";
        while ( $field = $db->fetch_row($result,$nop) ) {
            $formularobject[2] .= "<option value=\"".$field[0]."\"".$selected.">".$field[1] ."</option>\n";
        }
        $formularobject[2] .= "</select>\n";
        // +++
        // dropdown dienstposten aus db erstellen


        // dropdown interessen aus db erstellen (wach 1104)
        // ***
        $sql = "SELECT abint FROM db_adrb_int ORDER by abint_sort";
        $result = $db -> query($sql);
        $formularobject[3] = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"abinteressen\">\n";
        $formularobject[3] .= "<option value=\"\">Bitte auswählen</option>\n";

        if ( !is_array($form_values["abinteressen"]) ) $form_values["abinteressen"] = explode(";", $form_values["abinteressen"]);

        while ( $field = $db->fetch_row($result,$nop) ) {
            #foreach ($form_values["abinteressen"] as $single_interest) {
            #}
            $formularobject[3] .= "<option value=\"".$field[0]."\"".$selected.">".$field[0] ."</option>\n";
        }
        $formularobject[3] .= "</select>\n";
        // +++
        // dropdown interessen aus db erstellen


        // dropdown dienststelle aus db erstellen (wach 0404)
        // ***
        $sql = "SELECT adid, adkate, adststelle FROM db_adrd ORDER by adsort, adststelle";
        $result = $db -> query($sql);
        $formularobject[4] = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"abdststelle\">\n";
        $formularobject[4] .= "<option value=\"\">Bitte auswählen</option>\n";
        while ( $field = $db->fetch_row($result,$nop) ) {
            $formularobject[4] .= "<option value=\"".$field[0]."\"".$selected.">".$field[1]." ".$field[2]."</option>\n";
        }
        $formularobject[4] .= "</select>\n";
        // +++
        // dropdown dienststelle aus db erstellen


        // dropdown bfd aus db erstellen (wach 0404)
        // ***
        $sql = "SELECT adid,adkate, adstbfd FROM db_adrd WHERE adkate= 'BFD' ORDER by adsort";
        $result = $db -> query($sql);
        $formularobject[5] = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"abdstbfd\">\n";
        $formularobject[5] .= "<option value=\"\">Bitte auswählen</option>\n";
        while ( $field = $db->fetch_row($result,$nop) ) {
            $formularobject[5] .= "<option value=\"".$field[2]."\"".$selected.">".$field[2]."</option>\n";
        }
        $formularobject[5] .= "</select>\n";
        // +++
        // dropdown bfd aus db erstellen


        // alle veraenderten elemente umbauen
        // ***

        // abdstbfd nicht in db_adrb enthalten
        $element["abdstbfd"] = $formularobject[5];

        foreach($element as $name => $value) {
            if ($name == "abamtbez") {
                $element[$name] = $formularobject[1];
            } elseif ($name == "abdstposten") {
                $element[$name] = $formularobject[2];
            } elseif ($name == "abinteressen") {
                $element[$name] = $formularobject[3];
            } elseif ($name == "abdststelle") {
                $element[$name] = $formularobject[4];
            }
        }
        // +++
        // alle veraenderten elemente umbauen

        // was anzeigen
        $mapping["main"] = crc32($environment[ebene]).".".$environment["kategorie"];
        $mapping["navi"] = "leer";

        // wohin schicken
        $ausgaben["form_aktion"] = $cfg["basis"]."/list,".$position.",esearch.html";
    }



////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
