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

    #} elseif ( $environment["parameter"][1] == "delete" && $rechte[$cfg["right"]] == -1 ) {
    if ( $rechte[$cfg["right"]] == -1 ) {

        // erst mal kucken ob leoschen eine gute idee ist?
        // ***
        $sql = "SELECT ".$cfg["db"]["menu"]["key"]."
                  FROM ".$cfg["db"]["menu"]["entries"]."
                 WHERE refid='".$environment["parameter"][1]."'";
        $result = $db -> query($sql);
        $num_rows = $db -> num_rows($result);
        // +++
        // erst mal kucken ob leoschen eine gute idee ist?

        if ( $num_rows > 0 ) {

            // was anzeigen
            $mapping["main"] = crc32($environment["ebene"]).".list";
            $mapping["navi"] = "leer";

            // wohin schicken
            header("Location: ".$cfg["basis"]."/list.html?error=1");

        } else {

            // menupunkt holen
            // ***
            $sql = "SELECT mid, refid, entry
                      FROM ".$cfg["db"]["menu"]["entries"]."
                     WHERE ".$cfg["db"]["menu"]["key"]."='".$environment["parameter"][1]."'";
            $result = $db -> query($sql);
            $array = $db -> fetch_array($result,$nop);
            $refid = $array["refid"];
            $kategorie = $array["entry"];
            $ausgaben["entry"] = $kategorie;
            $ausgaben["form_hidden"] .= "<input type=\"hidden\" name=\"mid\" value=\"".$array["mid"]."\" />";
            // +++
            // menupunkt holen

            // bezeichnungen holen (alle sprachen)
            // ***
            $sql = "SELECT mlid, lang, label
                      FROM ".$cfg["db"]["lang"]["entries"]."
                     WHERE mid='".$environment["parameter"][1]."'";
            $result = $db -> query($sql);
            while ( $array = $db -> fetch_array($result,$nop) ) {
                if ( $mlids != "" ) $mlids .= ",";
                $mlids .= $array["mlid"];
                $ausgaben["languages"] .= $array["lang"]." ";
                $ausgaben["languages"] .= $array["label"]."<br />";
            }
            $ausgaben["form_hidden"] .= "<input type=\"hidden\" name=\"mlids\" value=\"".$mlids."\" />";
            // +++
            // bezeichnungen holen (alle sprachen)


            // content holen (alle sprachen)
            // ***
            $ebene = make_ebene($refid);
            if ( $ebene != "/" ) $extend = crc32($ebene).".";
            $tname = $extend.$kategorie;
            $sql = "SELECT lang, label, tname, content
                      FROM ".$cfg["db"]["text"]["entries"]."
                     WHERE tname='".$tname."';";
            $result  = $db -> query($sql);
            while ( $array = $db -> fetch_array($result,$nop) ) {
                $ausgaben["content"] .=  $array["lang"].": "
                                        .$array["label"].": "
                                        .substr($array["content"],0,20)." ...<br />";
            }
            if ( $ausgaben["content"] == "" ) $ausgaben["content"] = "#(no_content)";
            $ausgaben["form_hidden"] .= "<input type=\"hidden\" name=\"tname\" value=\"".$tname."\" />";
            // +++
            // content holen (alle sprachen)


            // page basics
            // ***
            // fehlermeldungen
            $ausgaben["form_error"] = "";

            // navigation erstellen
            $ausgaben["form_aktion"] = $cfg["basis"]."/delete,".$environment["parameter"][1].".html";
            $ausgaben["form_break"] = $cfg["basis"]."/list.html";

            // hidden values
            $ausgaben["form_hidden"] .= "<input type=\"hidden\" name=\"delete\" value=\"true\" />";

            // was anzeigen
            $mapping["main"] = crc32($environment["ebene"]).".delete";
            $mapping["navi"] = "leer";

            // unzugaengliche #(marken) sichtbar machen
            // ***
            if ( isset($HTTP_GET_VARS["edit"]) ) {
                $ausgaben["inaccessible"] = "inaccessible values:<br />";
                $ausgaben["inaccessible"] .= "# (error_menu) #(error_menu)<br />";
                $ausgaben["inaccessible"] .= "# (error_menu_lang) #(error_menu_lang)<br />";
                $ausgaben["inaccessible"] .= "# (error_text) #(error_text)<br />";
                $ausgaben["inaccessible"] .= "# (no_content) #(no_content)<br />";
            } else {
                $ausgaben["inaccessible"] = "";
            }
            // +++
            // unzugaengliche #(marken) sichtbar machen

            // wohin schicken
            # header("Location: ".$cfg["basis"]."/?.html");
            // +++
            // page basics


            // das loeschen wurde bestaetigt, loeschen!
            // ***
            if ( $HTTP_POST_VARS["delete"] ) {
                unset($result);

                // content loeschen
                // ***
                if ( $HTTP_POST_VARS["tname"] != "" ) {
                    $sql = "DELETE FROM ".$cfg["db"]["text"]["entries"]." WHERE tname = '".$HTTP_POST_VARS["tname"]."'";
                    if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                    $result  = $db -> query($sql);
                    if ( !$result ) $ausgaben["form_error"] = $db -> error("#(text_error)<br />");
                }
                // +++
                // content loeschen

                // ohne fehler bezeichnungen loeschen
                // ***
                if ( $HTTP_POST_VARS["mlids"] != "" && $ausgaben["form_error"] == "" ) {
                    $array = split(",",$HTTP_POST_VARS["mlids"]);
                    foreach( $array as $value) {
                        $sql = "DELETE FROM ".$cfg["db"]["lang"]["entries"]." WHERE ".$cfg["db"]["lang"]["key"]."='".$value."';";
                        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                        $result  = $db -> query($sql);
                        if ( !$result ) $ausgaben["form_error"] = $db -> error("#(menu_lang_error)<br />");
                    }
                }
                // +++
                // ohne fehler bezeichnungen loeschen

                // ohne fehler menupunkte loeschen
                // ***
                if ( $HTTP_POST_VARS["mid"] != "" && $ausgaben["form_error"] == "" ) {
                    $array = split(",",$HTTP_POST_VARS["mid"]);
                    foreach( $array as $value) {
                        $sql = "DELETE FROM ".$cfg["db"]["menu"]["entries"]." WHERE ".$cfg["db"]["menu"]["key"]."='".$value."';";
                        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                        $result  = $db -> query($sql);
                        if ( !$result ) $ausgaben["form_error"] = $db -> error("#(menu_error)<br />");
                    }
                }
                // +++
                // ohne fehler menupunkte loeschen

                // wohin schicken
                if ( $ausgaben["form_error"] == "" ) {
                    header("Location: ".$cfg["basis"]."/list.html");
                }
            }
            // +++
            // das loeschen wurde bestaetigt, loeschen!
        }
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
