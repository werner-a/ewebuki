<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "short description";
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

    #if ( $environment["parameter"][1] == "add" && $rechte[$cfg["right"]] == -1 ) {
    if ( $rechte[$cfg["right"]] == -1 ) {
        // page basics
        // ***
        #if ( count($HTTP_POST_VARS) == 0 ) {
        #    $sql = "SELECT * FROM ".$cfg["db"]["menu"]["entries"]." WHERE ".$cfg["db"]["menu"]["key"]."='".$environment["parameter"][2]."'";            $result = $db -> query($sql);
        #    $form_values = $db -> fetch_array($result,1);
        #} else {
            $form_values = $HTTP_POST_VARS;
        #}

        // form options holen
        $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

        // form elememte bauen
        $element = form_elements( $cfg["db"]["menu"]["entries"], $form_values );

        // form elemente erweitern
        $element = array_merge($element, form_elements( $cfg["db"]["lang"]["entries"], $array ));
        if ( $HTTP_POST_VARS["refid"] == "" ) {
            $value = $environment["parameter"][1];
        } else {
            $value = $HTTP_POST_VARS["refid"];
        }
        $element["refid"] = str_replace("refid\"","refid\" value=\"".$value."\" readonly",$element["refid"]);
        $element["new_lang"] = "<input name=\"new_lang\" type=\"text\" maxlength=\"5\" size=\"3\" value=\"n/a\" readonly>";
        // +++
        // page basics


        /*
        // lang management form elemente begin
        // ***
        $element_lang = form_elements( $cfg["db"]["lang"]["entries"], $HTTP_POST_VARS );
        $element_lang["lang"] = str_replace("lang\"","lang\" value=\"".$environment["language"]."\"",$element_lang["lang"]);

        $ausgaben["langtabelle"]  = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
        $ausgaben["langtabelle"] .= "<tr><td>#(lang)</td><td>#(label)</td><td>#(exturl)</td><td>&nbsp;</td></tr>\n";
        $ausgaben["langtabelle"] .= "<tr>";
        $ausgaben["langtabelle"] .= "<td>".$element_lang["lang"]."</td>";
        $ausgaben["langtabelle"] .= "<td>".$element_lang["label"]."</td>";
        $ausgaben["langtabelle"] .= "<td>".$element_lang["exturl"]."</td>";
        $ausgaben["langtabelle"] .= "<td>";
        #$ausgaben["langtabelle"] .= "<input name=\"edit\" type=\"image\" src=\"".$pathvars["images"]."edit.png\" width=\"24\" height=\"18\" border=\"0\" value=\"".$lang["mlid"]."\">";
        #$ausgaben["langtabelle"] .= "<input name=\"delete\" type=\"image\" src=\"".$pathvars["images"]."delete.png\" width=\"24\" height=\"18\" border=\"0\" value=\"".$lang["mlid"]."\">";
        $ausgaben["langtabelle"] .= "</td></tr>";
        $ausgaben["langtabelle"] .= "</table>";
        // +++
        // lang management form elemente end
        */


        // page basics
        // ***
        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["basis"]."/add,verify.html";
        $ausgaben["form_break"] = $cfg["basis"]."/list.html";

        // hidden values
        $ausgaben["form_hidden"] .= "";

        // was anzeigen
        $mapping["main"] = crc32($environment["ebene"]).".edit-single";
        $mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        // ***
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_result) #(error_result)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }
        // +++
        // unzugaengliche #(marken) sichtbar machen

        // wohin schicken
        # header("Location: ".$cfg["basis"]."/?.html");
        // +++
        // page basics

        if ( $environment["parameter"][1] == "verify" ) {

            // form eigaben prüfen
            form_errors( $form_options, $HTTP_POST_VARS );

            // ohne fehler sql bauen und ausfuehren
            if ( $ausgaben["form_error"] == ""
                && ( $HTTP_POST_VARS["send"] != ""
                    || $HTTP_POST_VARS["image"]
                    || $HTTP_POST_VARS["add"] ) ) {

                $kick = array( "PHPSESSID", "send", "image", "image_x", "image_y",
                               "add_x", "add_y", "add", "form_referer", "lang", "label",
                               "exturl", "new_lang", "entry");
                foreach($HTTP_POST_VARS as $name => $value) {
                    if ( !in_array($name,$kick) ) {
                        if ( $sqla != "" ) $sqla .= ",";
                        $sqla .= " ".$name;
                        if ( $sqlb != "" ) $sqlb .= ",";
                        $sqlb .= " '".$value."'";
                    }
                }

                // Sql um spezielle Felder erweitern
                $entry = strtolower($HTTP_POST_VARS["entry"]);
                $entry = str_replace(" ", "", $entry);
                $sqla .= ", entry";
                $sqlb .= ", '".$entry."'";

                $sql = "insert into ".$cfg["db"]["menu"]["entries"]." (".$sqla.") VALUES (".$sqlb.")";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $result  = $db -> query($sql);
                if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");

                // lang management form elemente begin
                // ***
                if ( $ausgaben["form_error"] == "" ) {
                    $lastid = $db -> lastid();
                    $sql = "insert into ".$cfg["db"]["lang"]["entries"]." (mid, lang, label) VALUES ('".$lastid."', '".$HTTP_POST_VARS["lang"]."', '".$HTTP_POST_VARS["label"]."' )";
                    if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                    $result  = $db -> query($sql);
                    if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                }
                // +++
                // lang management form elemente end
                if ( $HTTP_POST_VARS["add"] ) {
                    $header = $cfg["basis"]."/edit,".$lastid.",verify.html";
                } else {
                    $header = $cfg["basis"]."/list.html";
                }

                if ( $ausgaben["form_error"] == "" ) {
                    header("Location: ".$header);
                }
            }
        }
    }  else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
