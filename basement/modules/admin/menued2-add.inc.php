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

    if ( priv_check(make_ebene($environment["parameter"][1]),$cfg["menued"]["modify"]["add"][2]) ||
        priv_check_old("",$cfg["menued"]["right"]) ) {
        // page basics
        // ***
        #if ( count($HTTP_POST_VARS) == 0 ) {
        #    $sql = "SELECT * FROM ".$cfg["menued"]["db"]["menu"]["entries"]." WHERE ".$cfg["menued"]["db"]["menu"]["key"]."='".$environment["parameter"][2]."'";            $result = $db -> query($sql);
        #    $form_values = $db -> fetch_array($result,1);
        #} else {
            $form_values = $HTTP_POST_VARS;
        #}

        // form options holen
        $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

        // form elememte bauen
        $element = form_elements( $cfg["menued"]["db"]["menu"]["entries"], $form_values );

        // form elemente erweitern
        $element = array_merge($element, form_elements( $cfg["menued"]["db"]["lang"]["entries"], $form_values ));
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
        $element_lang = form_elements( $cfg["menued"]["db"]["lang"]["entries"], $HTTP_POST_VARS );
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
        $ausgaben["form_aktion"] = $cfg["menued"]["basis"]."/add,verify.html";
        $ausgaben["form_break"] = $cfg["menued"]["basis"]."/list.html";

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


        #$fixed_entry = str_replace(" ", "", $HTTP_POST_VARS["entry"]);
        $fixed_entry = preg_replace("/[^A-Za-z_\-\.0-9]+/", "", $HTTP_POST_VARS["entry"]);  // PREG:^[a-z_.-0-9]+$

        if ( $environment["parameter"][1] == "verify"
            && ( $HTTP_POST_VARS["send"] != ""
                || $HTTP_POST_VARS["image"]
                || $HTTP_POST_VARS["add"] ) ) {

            // form eigaben prüfen
            form_errors( $form_options, $HTTP_POST_VARS );

            // gibt es einen solchen entry bereits?
            if ( $fixed_entry != "" ) {
                $sql = "SELECT entry
                        FROM ".$cfg["menued"]["db"]["menu"]["entries"]."
                        WHERE refid = '".$HTTP_POST_VARS["refid"]."'
                        AND entry = '".$fixed_entry."'";
                $result = $db -> query($sql);
                $test = $db -> fetch_array($result,1);
                if ( $test["entry"] == $fixed_entry ) $ausgaben["form_error"] .= "#(error_dupe)";
            }

            // entry hinzufuegen
            if ( $ausgaben["form_error"] == "" ) {
                $kick = array( "PHPSESSID", "send", "image", "image_x", "image_y",
                               "add_x", "add_y", "add", "form_referer", "lang", "label", "extend",
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
                #$entry = strtolower($HTTP_POST_VARS["entry"]); // wird jetzt mit einer regex erledigt
                #$entry = str_replace(" ", "", $entry); // siehe $fixed_entry
                $sqla .= ", entry";
                $sqlb .= ", '".$fixed_entry."'";

                $sql = "insert into ".$cfg["menued"]["db"]["menu"]["entries"]." (".$sqla.") VALUES (".$sqlb.")";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $result  = $db -> query($sql);
                if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
            }

            // sprache hinzufuegen
            if ( $ausgaben["form_error"] == "" ) {
                $lastid = $db -> lastid();
                if ( checkext() != "" ) {
                    $extenda = "extend, ";
                    $extendb = "'".$HTTP_POST_VARS["extend"]."', ";
                }
                $sql = "INSERT INTO ".$cfg["menued"]["db"]["lang"]["entries"]."
                                    ( mid, lang, label, ".$extenda." exturl )
                             VALUES ( '".$lastid."',
                                      '".$HTTP_POST_VARS["lang"]."',
                                      '".$HTTP_POST_VARS["label"]."',
                                      ".$extendb."
                                      '".$HTTP_POST_VARS["exturl"]."' )";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $result  = $db -> query($sql);
                if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
            }

            // wohin schicken
            if ( $HTTP_POST_VARS["add"] ) {
                $header = $cfg["menued"]["basis"]."/edit,".$lastid.",verify.html";
            } else {
                if ( $_SESSION["REFERER"] != "" ) {
                    #$header = $_SESSION["REFERER"]."/".$fixed_entry.".html";
                    $crc = crc32(str_replace( $pathvars["virtual"], "", $_SESSION["REFERER"]));
                    $header = $pathvars["virtual"]."/admin/contented/edit,". DATABASE . ",".$crc.".".$fixed_entry.",inhalt.html?referer=".$_SESSION["REFERER"]."/".$fixed_entry.".html";
                    unset($_SESSION["referer"]);
                } else {
                    $sql = "SELECT refid FROM ".$cfg["menued"]["db"]["menu"]["entries"]." WHERE ".$cfg["menued"]["db"]["menu"]["key"]."=".$lastid;
                    $result  = $db -> query($sql);
                    $lastrefid = $db -> fetch_array($result,1); 
                    $header = $cfg["menued"]["basis"]."/list,".$lastrefid["refid"].".html";
                }
            }
            if ( $ausgaben["form_error"] == "" ) {
                header("Location: ".$header);
            }
        }
    }  else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>