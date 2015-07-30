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

    if ( $cfg["menued"]["modify"]["edit"][2] == "" || priv_check(make_ebene($environment["parameter"][1]), $cfg["menued"]["modify"]["edit"][2] ) || ($cfg["auth"]["menu"]["menued"][2] == -1 &&  priv_check('', $cfg["menued"]["right"],$specialvars["dyndb"] ) ) ) {

        // page basics
        // ***
        if ( count($_POST) == 0 ) {
            $sql = "SELECT * FROM ".$cfg["menued"]["db"]["menu"]["entries"]." WHERE ".$cfg["menued"]["db"]["menu"]["key"]."='".$environment["parameter"][1]."'";
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
        $element["new_lang"] = "<input name=\"new_lang\" type=\"text\" maxlength=\"5\" size=\"5\">";
        // +++
        // page basics



        // verwaltung multi language
        // ***
        if ( count($_POST) == 0 ) {
            $sql = "SELECT * FROM ".$cfg["menued"]["db"]["lang"]["entries"]." where mid=".$environment["parameter"][1]." ORDER by lang";
            $result = $db -> query($sql);
            $num_rows = $db -> num_rows($result);

            // nur eine sprache?
            if ( $num_rows <= 1 ) {
                $array = $db -> fetch_array($result,1);
                $element = array_merge($element, form_elements( $cfg["menued"]["db"]["lang"]["entries"], $array ));
                $art = "-single";
            } else {
                while ( $array = $db -> fetch_array($result,1) ) {
                    // element erweiterung aus zeile bauen (form options bereits geholt)
                    $ext_element = form_elements( $cfg["menued"]["db"]["lang"]["entries"], $array, "[".$array[$cfg["menued"]["db"]["lang"]["key"]]."]" );
                    $ext_element = array_slice($ext_element,2);

                    // elemente array erweitern
                    $element = array_merge($element, $ext_element);
                }
                $art = "-multi";
            }
        } else {
            // nur eine sprache?
            if ( count($_POST["lang"]) <= 1 ) {
                $array = array(
                    "lang"    => $_POST["lang"],
                    "label"   => $_POST["label"],
                    "exturl"  => $_POST["exturl"],
                );
                $element = array_merge($element, form_elements( $cfg["menued"]["db"]["lang"]["entries"], $array ));
                $art = "-single";
            } else {
                foreach( $_POST["lang"] as $key => $value ) {
                    $array = array(
                        "lang"    => $value,
                        "label"   => $_POST["label"][$key],
                        "exturl"  => $_POST["exturl"][$key],
                    );
                    // element erweiterung aus zeile bauen (form options bereits geholt)
                    $ext_element = form_elements( $cfg["menued"]["db"]["lang"]["entries"], $array, "[".$key."]" );
                    $ext_element = array_slice($ext_element,2);

                    // elemente array erweitern
                    $element = array_merge($element, $ext_element);
                }
                $art = "-multi";
            }
        }

        if ( $element["extend"] != "" ) {
            $hidedata["extend"] = array();
        }

        // langtabelle ausgabe
        // ***
        //elemente aussortieren
        foreach ( $element as $name => $value ) {
            if ( strstr($name,"[") ) {
                $key = str_replace(array("[","]"),"",strstr($name,"["));
                $field = substr($name,0,strpos($name,"["));
                $row[$key][$field] = $value;
            }
        }

        if ( is_array($row) ) {
            $ausgaben["langtabelle"]  = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
            $ausgaben["langtabelle"] .= "<tr>";
            // ueberschriften
            foreach ( $row[$key] as $label => $value ) {
                $ausgaben["langtabelle"] .= "<td>#(".$label.")</td>";
            }
            $ausgaben["langtabelle"] .= "<td>&nbsp;</td>";
            $ausgaben["langtabelle"] .= "</tr>\n";
            // felder
            foreach ( $row as $key => $label ) {
                $ausgaben["langtabelle"] .= "<tr>";
                foreach ( $label as $value ) {
                    $ausgaben["langtabelle"] .= "<td>".$value."</td>";
                }
                $ausgaben["langtabelle"] .= "<td><input name=\"delete[".$key."]\" type=\"image\" src=\"".$cfg["menued"]["iconpath"]."delete.png\" alt=\"#(delete)\" title=\"#(delete)\" width=\"24\" height=\"18\" border=\"0\"></td>";
                $ausgaben["langtabelle"] .= "</tr>\n";
            }
            $ausgaben["langtabelle"] .= "</table>\n";
        }
        #$debug_array = $row;
        // +++
        // langtabelle ausgabe

        // +++
        // verwaltung multi language


        // page basics
        // ***
        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["menued"]["basis"]."/edit,".$environment["parameter"][1].",".$environment["parameter"][2].",verify.html";
        $ausgaben["form_break"] = $cfg["menued"]["basis"]."/list.html";

        // hidden values
        $ausgaben["form_hidden"] .= "";

        // was anzeigen
        $mapping["main"] = eCRC($environment["ebene"]).".edit".$art;
        $mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        // ***
        if ( isset($_GET["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_result) #(error_result)<br />";
            $ausgaben["inaccessible"] .= "# (error_dupe) #(error_dupe)<br />";
            $ausgaben["inaccessible"] .= "# (error_lang_add) #(error_lang_add)<br />";
            $ausgaben["inaccessible"] .= "# (error_lang_delete) #(error_lang_delete)<br />";
            $ausgaben["inaccessible"] .= "# (error_entry) #(error_entry)<br />";
            $ausgaben["inaccessible"] .= "# (error_url) #(error_url)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }
        // +++
        // unzugaengliche #(marken) sichtbar machen

        // wohin schicken
        # header("Location: ".$cfg["menued"]["basis"]."/?.html");
        // +++
        // page basics


        #$fixed_entry = str_replace(" ", "", $_POST["entry"]);
        $fixed_entry = preg_replace("/[^A-Za-z_\-\.0-9]+/", "", $_POST["entry"]);  // PREG:^[a-z_.-0-9]+$

        if ( $environment["parameter"][3] == "verify"
            &&  ( $_POST["send"] != ""
                || $_POST["add"] != ""
                || $_POST["delete"] != "" ) ) {

            // form eigaben pr�fen
            form_errors( $form_options, $_POST );

            // black-list-test
            black_list($environment["parameter"][2],$_POST["entry"]);

            // lang tabellen aenderungen
            if ( $ausgaben["form_error"] == ""  ) {

                $header_link = $cfg["menued"]["basis"]."/edit,".$environment["parameter"][1].".html"; #?referer=".$ausgaben["form_referer"]);
                if ( $_POST["add"] && $_POST["new_lang"] != "" ) {
                    $sql = "SELECT label
                              FROM ".$cfg["menued"]["db"]["lang"]["entries"]."
                             WHERE mid = ".$environment["parameter"][1]."
                               AND lang = '".$_POST["new_lang"]."'";
                    if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                    $result  = $db -> query($sql);
                    if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                    $num_rows = $db -> num_rows($result);

                    if ( $num_rows >= 1 ) {
                        $ausgaben["form_error"] .= "#(error_lang_add)";
                        $header = $header_link;
                    } else {
                        if ( $element["extend"] != "" ) {
                            $extenda = "extend, ";
                            $extendb = "'".$_POST["extend"]."', ";
                        }
                        $sql = "insert into ".$cfg["menued"]["db"]["lang"]["entries"]." (mid, lang, ".$extenda."label) VALUES ('".$environment["parameter"][1]."', '".$_POST["new_lang"]."', ".$extendb."'".$fixed_entry."' )";
                        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                        $result  = $db -> query($sql);
                        if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                        #header("Location: ".$cfg["menued"]["basis"]."/edit,".$environment["parameter"][1].",verify.html"); #?referer=".$ausgaben["form_referer"]);
                        $header = $header_link;
                    }
                } elseif ( $_POST["delete"] ) {
                    $key = key($_POST["delete"]);
                    $sql = "SELECT lang
                              FROM ".$cfg["menued"]["db"]["lang"]["entries"]."
                             WHERE mlid = ".$key;
                    if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                    $result  = $db -> query($sql);
                    if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                    $data = $db -> fetch_array($result,1);
                    if ( $data["lang"] == $specialvars["default_language"] ) {
                        $ausgaben["form_error"] .= "#(error_lang_delete)";
                        $header = $header_link;
                    } else {
                        $sql = "delete from ".$cfg["menued"]["db"]["lang"]["entries"]." where mlid=".$key;
                        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                        $result  = $db -> query($sql);
                        if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                        #header("Location: ".$cfg["menued"]["basis"]."/edit,".$environment["parameter"][1].",verify.html"); #?referer=".$ausgaben["form_referer"]);
                        $header = $header_link;
                    }
                }

                if ( count($_POST["lang"]) <= 1 ) {
                    if ( $element["extend"] != "" ) $extenddesc = "extend = '".$_POST["extend"]."',";
                    $sql = "update ".$cfg["menued"]["db"]["lang"]["entries"]."
                            set label = '".$_POST["label"]."',
                                ".$extenddesc."
                                exturl = '".$_POST["exturl"]."'
                            where mid = ".$environment["parameter"][1]; # mid statt mlid weil $key fehlt
                    if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                    $result  = $db -> query($sql);
                    if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                } else {
                    foreach( $_POST["lang"] as $key => $value ) {
                        if ( $element["extend"] != "" ) $extenddesc = "extend = '".$_POST["extend"][$key]."',";
                        $sql = "update ".$cfg["menued"]["db"]["lang"]["entries"]."
                                set label = '".$_POST["label"][$key]."',
                                    ".$extenddesc."
                                    exturl = '".$_POST["exturl"][$key]."'
                                where mlid=".$key;
                        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                        $result  = $db -> query($sql);
                        if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                    }
                }
            }


            // content tabellen aenderungen
            if ( $ausgaben["form_error"] == "" ) {

                $sql = "SELECT entry
                          FROM ".$cfg["menued"]["db"]["menu"]["entries"]."
                         WHERE ".$cfg["menued"]["db"]["menu"]["key"]." = '".$environment["parameter"][1]."'";
                $result = $db -> query($sql);
                $data = $db -> fetch_array($result,1);

                // wurde der entry geaendert?
                if ( $data["entry"] != $fixed_entry ) {

                    // gibt den geaenderten entry bereits?
                    $sql = "SELECT entry
                            FROM ".$cfg["menued"]["db"]["menu"]["entries"]."
                            WHERE refid = '".$_POST["refid"]."'
                            AND entry = '".$fixed_entry."'";
                    $result1 = $db -> query($sql);
                    $test = $db -> fetch_array($result,1);
                    if ( $test["entry"] == $fixed_entry ) $ausgaben["form_error"] .= "#(error_dupe)";

                    if ( $ausgaben["form_error"] == ""  ) {

                        $new_url = make_ebene($_POST["refid"]);
                        if ( $new_url == "/" ) {
                            $new_url = "";
                        }
                        $new_url .= "/".$fixed_entry;
                        update_tname($environment["parameter"][1], $new_url);

                    }
                }
            }


            // menu tabellen aenderungen
            if ( $ausgaben["form_error"] == ""  ) {

                $kick = array( "PHPSESSID", "send", "add", "delete", "image", "image_x", "image_y", "form_referer",
                               "new_lang", "lang", "label", "extend", "exturl",
                               "entry" );
                foreach($_POST as $name => $value) {
                    if ( !in_array($name,$kick) && !strstr($name, ")" ) ) {
                        if ( $sqla != "" ) $sqla .= ", ";
                        $sqla .= $name."='".$value."'";
                    }
                }

                // Sql um spezielle Felder erweitern
                #$entry = strtolower($_POST["entry"]); // wird jetzt mit einer regex erledigt
                #$entry = str_replace(" ", "", $entry); // siehe $fixed_entry
                $sqla .= ", entry='".$fixed_entry."'";

                #$ldate = $_POST["ldate"];
                #$ldate = substr($ldate,6,4)."-".substr($ldate,3,2)."-".substr($ldate,0,2)." ".substr($ldate,11,9);
                #$sqla .= ", ldate='".$ldate."'";

                $sql = "update ".$cfg["menued"]["db"]["menu"]["entries"]." SET ".$sqla." WHERE ".$cfg["menued"]["db"]["menu"]["key"]."='".$environment["parameter"][1]."'";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $result  = $db -> query($sql);
                if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                if ( $header == "" ) $header = $cfg["menued"]["basis"]."/list,".$environment["parameter"][2].".html";
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