<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: leer-edit.inc.php 1355 2008-05-29 12:38:53Z buffy1860 $";
// "leer - edit funktion";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2008 Werner Ammon ( wa<at>chaos.de )

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

    86343 Koenigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( priv_check($environment["ebene"]."/".$environment["kategorie"],$cfg["keyworded"]["right"]["keywords"]) ) {

        // page basics
        // ***

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // form options holen
        $form_options = form_options(eCRC($environment["ebene"]).".".$environment["kategorie"]);

        // +++
        // page basics


        // funktions bereich fuer erweiterungen
        // ***

        // schlagwoerter-dropdown
        $sql = "SELECT DISTINCT ".$cfg["keyworded"]["db"]["keyword"]["keyword"]."
                           FROM ".$cfg["keyworded"]["db"]["keyword"]["entries"]."
                       ORDER BY ".$cfg["keyworded"]["db"]["keyword"]["order"];
        $result = $db -> query($sql);
        while ( $data = $db -> fetch_array($result,1) ) {
            $sel = "";
            if ( urldecode($environment["parameter"][1]) == $data[$cfg["keyworded"]["db"]["keyword"]["keyword"]] ) $sel = " selected=\"selected\"";
            $dataloop["keywords"][] = array(
                "keyword" => $data[$cfg["keyworded"]["db"]["keyword"]["keyword"]],
                    "sel" => $sel,
            );
        }

        $ausgaben["new"] = $_POST["new_keyword"];

        // +++
        // funktions bereich fuer erweiterungen


        // page basics
        // ***

        // hidden values
        $ausgaben["form_hidden"] .= "";

        // was anzeigen
        $mapping["main"] = $cfg["keyworded"]["name"].".rename_tag";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_result) #(error_result)<br />";
            $ausgaben["inaccessible"] .= "# (error_dupe) #(error_dupe)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        if ( $_SERVER["HTTP_REFERER"] != "" && !strstr($_SERVER["HTTP_REFERER"],$cfg["keyworded"]["basis"]."/".$environment["kategorie"]) ) {
            $_SESSION["form_referer"] = $_SERVER["HTTP_REFERER"];
        }

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["keyworded"]["basis"]."/".$environment["kategorie"].",".$environment["parameter"][1].",verify.html";
        $ausgaben["form_break"] = $_SESSION["form_referer"];

        // +++
        // page basics

        if ( $environment["parameter"][2] == "verify"
            &&  ( $_POST["send"] != ""
                || $_POST["extension1"] != ""
                || $_POST["extension2"] != "" ) ) {

            // form eingaben pruefen
            form_errors( $form_options, $_POST );

            // evtl. zusaetzliche datensatz aendern
            if ( $ausgaben["form_error"] == ""  ) {

                // funktions bereich fuer erweiterungen
                // ***

                ### put your code here ###

                if ( $error ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                // +++
                // funktions bereich fuer erweiterungen
            }

            // datensatz aendern
            if ( $ausgaben["form_error"] == ""  ) {
                $sql = "SELECT *
                          FROM ".$cfg["keyworded"]["db"]["keyword"]["entries"]."
                         WHERE ".$cfg["keyworded"]["db"]["keyword"]["keyword"]."='".$_POST["old_keyword"]."'";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $result = $db -> query($sql);
                while ( $data = $db -> fetch_array($result,1) ) {
                    $sql = "SELECT *
                              FROM ".$cfg["keyworded"]["db"]["keyword"]["entries"]."
                             WHERE ".$cfg["keyworded"]["db"]["keyword"]["keyword"]."='".$_POST["new_keyword"]."'
                               AND ".$cfg["keyworded"]["db"]["keyword"]["tname"]."='".$data[$cfg["keyworded"]["db"]["keyword"]["tname"]]."'
                               AND ".$cfg["keyworded"]["db"]["keyword"]["lang"]."='".$environment["language"]."'";
                    if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                    $res_num = $db -> query($sql);
                    $num = $db->num_rows($res_num);
                    if ( $num == 0 ) {
                        $sql = "UPDATE ".$cfg["keyworded"]["db"]["keyword"]["entries"]."
                                   SET ".$cfg["keyworded"]["db"]["keyword"]["keyword"]."='".$_POST["new_keyword"]."'
                                 WHERE ".$cfg["keyworded"]["db"]["keyword"]["keyword"]."='".$_POST["old_keyword"]."'
                                   AND ".$cfg["keyworded"]["db"]["keyword"]["tname"]."='".$data[$cfg["keyworded"]["db"]["keyword"]["tname"]]."'
                                   AND ".$cfg["keyworded"]["db"]["keyword"]["lang"]."='".$environment["language"]."'";
                    } else {
                        $sql = "DELETE
                                  FROM ".$cfg["keyworded"]["db"]["keyword"]["entries"]."
                                 WHERE ".$cfg["keyworded"]["db"]["keyword"]["keyword"]."='".$_POST["old_keyword"]."'
                                   AND ".$cfg["keyworded"]["db"]["keyword"]["tname"]."='".$data[$cfg["keyworded"]["db"]["keyword"]["tname"]]."'
                                   AND ".$cfg["keyworded"]["db"]["keyword"]["lang"]."='".$environment["language"]."'";
                    }
                    $result  = $db -> query($sql);
                }
            }

            // wenn es keine fehlermeldungen gab, die uri $header laden
            if ( $ausgaben["form_error"] == "" ) {
                $header = $_SESSION["form_referer"];
                // war das veraenderte schlagwort im list ausgewaehlt
                if ( strstr($_SESSION["form_referer"],$cfg["keyworded"]["basis"]."/list,") ) {
                    preg_match("/".str_replace("/","\/",$cfg["keyworded"]["basis"]."/list,")."(.*)\.html$/",$_SESSION["form_referer"],$sel_tags);
                    $tags = explode(",",$sel_tags[1]);
                    if ( in_array(urlencode($_POST["old_keyword"]),$tags) ) {
                        $key = array_search(urlencode($_POST["old_keyword"]),$tags);
                        unset($tags[$key]);
                        $tags[] = urlencode($_POST["new_keyword"]);
                    }
                    $header =  str_replace($sel_tags[1].".html",implode(",",$tags).".html",$_SESSION["form_referer"]);
                }
                unset($_SESSION["form_referer"]);
                header("Location: ".$header);
            }
        }
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
