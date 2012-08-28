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

    86343 K�nigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
   
    if ( $cfg["menued"]["modify"]["move"][2] == "" || priv_check(make_ebene($environment["parameter"][2]), $cfg["menued"]["modify"]["move"][2] ) || ($cfg["auth"]["menu"]["menued"][2] == -1 &&  priv_check('', $cfg["menued"]["modify"]["move"][2],$specialvars["dyndb"] ) ) ) {
        $ausgaben["root"] = "";
        $hidedata["move"]["on"] = -1;

        $stop["nop"] = "nop";
        $design = "modern";
        $positionArray["nop"] = "nop";

        $_SESSION["menued_id"] = $environment["parameter"][1];
        locate($_SESSION["menued_id"]);

        // page basics
        // ***
        if ( count($_POST) == 0 ) {
            $sql = "SELECT * FROM ".$cfg["menued"]["db"]["menu"]["entries"]." WHERE ".$cfg["menued"]["db"]["menu"]["key"]."='".$environment["parameter"][2]."'";
            $result = $db -> query($sql);
            $form_values = $db -> fetch_array($result,1);
        } else {
            $form_values = $_POST;
        }

        // form options holen
        $form_options = form_options(eCRC($environment["ebene"]).".".$environment["kategorie"]);

        // form elememte bauen
        $element = form_elements( $cfg["menued"]["db"]["menu"]["entries"], $form_values );

        // form elemente erweitern
        #$element["new_lang"] = "<input name=\"new_lang\" type=\"text\" maxlength=\"5\" size=\"5\">";
        // +++
        // page basics
        #if ( $_GET["id"] != "" ) {
        #    locate($_GET["id"]);
        #} else {
        #    $positionArray[] = "nop";
        #}

        $ausgaben["show_menu"] .= sitemap(0,"menued", "select", "");


        // page basics
        // ***

        // design
        $ausgaben["design"] = "";

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["menued"]["basis"]."/move,".$environment["parameter"][1].",".$environment["parameter"][2].",verify.html";
        $ausgaben["form_break"] = $cfg["menued"]["basis"]."/list.html";

        // hidden values
        $ausgaben["form_hidden"] .= "";

        // navigation erstellen
        $ausgaben["renumber"] = "";
        $ausgaben["new"] = "";

        // was anzeigen
        $mapping["main"] = eCRC($environment["ebene"]).".list";
        $mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        // ***
        if ( isset($_GET["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_dupe) #(error_dupe)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }
        // +++
        // unzugaengliche #(marken) sichtbar machen

        // wohin schicken
        # header("Location: ".$cfg["menued"]["basis"]."/?.html");
        // +++
        // page basics

        if ( $environment["parameter"][3] == "verify"
            && $_POST["send"] != "" ) {

            // form eigaben pr�fen
            form_errors( $form_options, $_POST );

            // gibt es in der neuen ebene einen solchen entry?
            $sql = "SELECT entry
                      FROM ".$cfg["menued"]["db"]["menu"]["entries"]."
                     WHERE refid = '".$_POST["refid"]."'
                       AND entry = '".$_POST["entry"]."'";
            $result = $db -> query($sql);
            $num_rows = $db -> num_rows($result);
            if ( $num_rows >= 1 ) $ausgaben["form_error"] .= "#(error_dupe)";

            if ( $ausgaben["form_error"] == "" ) {

                // content tabellen aenderungen
                $new_url = make_ebene($_POST["refid"]);
                if ( $new_url == "/" ) {
                    $new_url = "";
                }
                $new_url .= "/".$_POST["entry"];
                update_tname($environment["parameter"][2], $new_url);

                // menu tabellen aenderungen
                $kick = array( "PHPSESSID", "send", "image", "image_x", "image_y", "form_referer" );
                foreach($_POST as $name => $value) {
                    if ( !in_array($name,$kick) && !strstr($name, ")" ) ) {
                        if ( $sqla != "" ) $sqla .= ", ";
                        $sqla .= $name."='".$value."'";
                    }
                }

                // Sql um spezielle Felder erweitern
                #$entry = strtolower($_POST["entry"]);
                #$entry = str_replace(" ", "", $entry);
                #$sqla .= ", entry='".$entry."'";

                $sql = "update ".$cfg["menued"]["db"]["menu"]["entries"]." SET ".$sqla." WHERE ".$cfg["menued"]["db"]["menu"]["key"]."='".$environment["parameter"][2]."'";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $result  = $db -> query($sql);
                if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(menu_error)<br />");
                if ( $header == "" ) $header = $cfg["menued"]["basis"]."/list.html";

                // wenn es keine fehlermeldungen gab, die uri $header laden
                header("Location: ".$header);
            }
        }
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
