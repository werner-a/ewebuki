<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id$";
  $Script["desc"] = "gesetze+verwaltung-modify";
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

    if ( $environment["parameter"][1] == "add" && $rechte[$cfg["right"]["adress"]] == -1) {

        if ( count($HTTP_POST_VARS) == 0 ) {
            session_register("images_memo");
            if ( is_array($HTTP_SESSION_VARS["images_memo"]) ) {
                foreach ( $HTTP_SESSION_VARS["images_memo"] as $value ) {
                    if ( $form_values["ipicfile"] != "" ) $form_values["ipicfile"] .= ";";
                    $form_values["ipicfile"] .= $value;
                }
                unset($HTTP_SESSION_VARS["images_memo"]);
            }
        } else {
            $form_values = $HTTP_POST_VARS;
        }

        // form options holen
        $form_options = form_options(crc32($environment["ebene"]).".modify");

        // form elememte bauen
        $element = form_elements( $cfg["db"]["entries"], $form_values );

        // wohin schicken
        $ausgaben["form_error"] = "";
        $ausgaben["form_aktion"] = $cfg["basis"]."/modify,add,verify.html";

        // referer im form mit hidden element mitschleppen
        if ( $HTTP_POST_VARS["form_referer"] == "" ) {
            $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        } else {
            $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        }

        if ( $environment["parameter"][2] == "verify" ) {

            // form eingaben prüfen
            form_errors( $form_options, $form_values );

            // ohne fehler sql bauen und ausfuehren
            if ( $ausgaben["form_error"] == "" && ( $HTTP_POST_VARS["submit"] != "" || $HTTP_POST_VARS["image"] != "" ) ) {
                $kick = array( "PHPSESSID", "submit", "image", "image_x", "image_y", "form_referer");
                foreach($form_values as $name => $value) {
                    if ( !in_array($name,$kick) ) {
                         if ( $sqla != "" ) $sqla .= ",";
                         $sqla .= " ".$name;
                         if ( $sqlb != "" ) $sqlb .= ",";
                         $sqlb .= " '".$value."'";
                    }
                }

                $sql = "insert into ".$cfg["db"]["entries"]." (".$sqla.") VALUES (".$sqlb.")";
                $result  = $db -> query($sql);
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                header("Location: ".$ausgaben["form_referer"]);
            }
        }

    } elseif ( $environment["parameter"][1] == "edit" && $rechte[$cfg["right"]["adress"]] == -1 ) {

        if ( count($HTTP_POST_VARS) == 0 ) {

            $sql = "SELECT * FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][2]."'";
            $result = $db -> query($sql);

            $form_values = $db -> fetch_array($result,$nop);
        } else {
            $form_values = $HTTP_POST_VARS;
        }


        // form options holen
        $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

        // wohin schicken
        $ausgaben["form_error"] = "";
        $ausgaben["form_aktion"] = $cfg["basis"]."/modify,edit,".$environment["parameter"][2].",verify.html";

        // referer im form mit hidden element mitschleppen
        if ( $HTTP_POST_VARS["form_referer"] == "" ) {
            $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        } else {
            $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        }

        // form elememte bauen
        $element = form_elements( $cfg["db"]["entries"], $form_values );

        if ( $environment["parameter"][3] == "verify" ) {

        // form eingaben prüfen
        form_errors( $form_options, $form_values );

        // ohne fehler sql bauen und ausfuehren
        if ( $ausgaben["form_error"] == "" && ( $HTTP_POST_VARS["submit"] != "" || $HTTP_POST_VARS["image"] != "" ) ) {
            $kick = array( "PHPSESSID", "submit", "image", "image_x", "image_y", "form_referer", "add", "add_x", "add_y", "delete", "delete_x", "delete_y" );
            foreach($form_values as $name => $value) {
                if ( !in_array($name,$kick) && !strstr($name, ")" ) ) {
                    if ( $sqla != "" ) $sqla .= ", ";
                    $sqla .= $name."='".$value."'";
                }
            }

            // Sql um spezielle Felder erweitern
            $sql = "update ".$cfg["db"]["entries"]." SET ".$sqla." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][2]."'";

            #echo $sql;
            $result  = $db -> query($sql);
            header("Location: ".$ausgaben["form_referer"]);
        }
    }

    }

    // was anzeigen+
    $mapping["main"] = crc32($environment["ebene"]).".modify";
    $mapping["navi"] = "leer";

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
