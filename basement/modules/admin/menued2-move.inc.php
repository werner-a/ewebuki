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

    if ( priv_check("/".$cfg["menued"]["subdir"]."/".$cfg["menued"]["name"],$cfg["menued"]["right"]) ||
        priv_check_old("",$cfg["menued"]["right"]) ) {

        $hidedata["move"]["on"] = -1;

        $stop["nop"] = "nop";
        $design = "modern";
        $positionArray["nop"] = "nop";

        $_SESSION["menued_id"] = $environment["parameter"][1];
        locate($_SESSION["menued_id"]);

        // page basics
        // ***
        if ( count($HTTP_POST_VARS) == 0 ) {
            $sql = "SELECT * FROM ".$cfg["menued"]["db"]["menu"]["entries"]." WHERE ".$cfg["menued"]["db"]["menu"]["key"]."='".$environment["parameter"][2]."'";
            $result = $db -> query($sql);
            $form_values = $db -> fetch_array($result,1);
        } else {
            $form_values = $HTTP_POST_VARS;
        }

        // form options holen
        $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

        // form elememte bauen
        $element = form_elements( $cfg["menued"]["db"]["menu"]["entries"], $form_values );

        // form elemente erweitern
        #$element["new_lang"] = "<input name=\"new_lang\" type=\"text\" maxlength=\"5\" size=\"5\">";
        // +++
        // page basics
        #if ( $_GET["id"] != "" ) {
        #    locate($HTTP_GET_VARS["id"]);
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
        $mapping["main"] = crc32($environment["ebene"]).".list";
        $mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        // ***
        if ( isset($HTTP_GET_VARS["edit"]) ) {
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
            && $HTTP_POST_VARS["send"] != "" ) {

            // form eigaben prüfen
            form_errors( $form_options, $HTTP_POST_VARS );

            // gibt es in der neuen ebene einen solchen entry?
            $sql = "SELECT entry
                      FROM ".$cfg["menued"]["db"]["menu"]["entries"]."
                     WHERE refid = '".$HTTP_POST_VARS["refid"]."'
                       AND entry = '".$HTTP_POST_VARS["entry"]."'";
            $result = $db -> query($sql);
            #$data = $db -> fetch_array($result,1);
            $num_rows = $db -> num_rows($result);
            if ( $num_rows >= 1 ) $ausgaben["form_error"] .= "#(error_dupe)";

            // content tabellen aenderungen
            if ( $ausgaben["form_error"] == "" ) {
                $sql = "SELECT refid, entry FROM ".$cfg["menued"]["db"]["menu"]["entries"]." WHERE ".$cfg["menued"]["db"]["menu"]["key"]."='".$environment["parameter"][2]."'";
                $result = $db -> query($sql);
                $data = $db -> fetch_array($result,1);

                // content aktuelle seite aendern (alle sprachen)
                $ebene = make_ebene($data["refid"]);
                if ( $ebene != "/" ) {
                    $extend = crc32($ebene).".";
                } else {
                    $ebene = "";
                    $extend = "";
                }
                $old_tname = $extend.$data["entry"];
                #echo $ebene.":".$old_tname."<br>";
                $suchmuster = $ebene."/".$data["entry"];

                $ebene = make_ebene($HTTP_POST_VARS["refid"]);
                if ( $ebene != "/" ) {
                    $extend = crc32($ebene).".";
                } else {
                    $ebene = "";
                    $extend = "";
                }
                $new_tname = $extend.$HTTP_POST_VARS["entry"];
                #echo $ebene.":".$new_tname."<br>";
                $ersatz = $ebene."/".$HTTP_POST_VARS["entry"];

                $sql = "UPDATE ".$cfg["menued"]["db"]["text"]["entries"]."
                            SET tname = '".$new_tname."',
                                ebene = '".$ebene."',
                                kategorie = '".$HTTP_POST_VARS["entry"]."'
                            WHERE tname = '".$old_tname."';";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $result  = $db -> query($sql);
                if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(menu_error)<br />");

                // content der unterpunkte aendern (alle sprachen)
                update_tname($environment["parameter"][2], $suchmuster, $ersatz);
            }


            // menu tabellen aenderungen
            if ( $ausgaben["form_error"] == "" ) {
                $kick = array( "PHPSESSID", "send", "image", "image_x", "image_y", "form_referer",
                               "entry" );
                foreach($HTTP_POST_VARS as $name => $value) {
                    if ( !in_array($name,$kick) && !strstr($name, ")" ) ) {
                        if ( $sqla != "" ) $sqla .= ", ";
                        $sqla .= $name."='".$value."'";
                    }
                }

                // Sql um spezielle Felder erweitern
                #$entry = strtolower($HTTP_POST_VARS["entry"]);
                #$entry = str_replace(" ", "", $entry);
                #$sqla .= ", entry='".$entry."'";

                $sql = "update ".$cfg["menued"]["db"]["menu"]["entries"]." SET ".$sqla." WHERE ".$cfg["menued"]["db"]["menu"]["key"]."='".$environment["parameter"][2]."'";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $result  = $db -> query($sql);
                if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(menu_error)<br />");
                if ( $header == "" ) $header = $cfg["menued"]["basis"]."/list.html";
            }

            // wenn es keine fehlermeldungen gab, die uri $header laden
            if ( $ausgaben["form_error"] == "" ) {
                header("Location: ".$header);
            }
        }
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
