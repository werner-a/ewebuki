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

//     if ( priv_check(tname2path(),$cfg["keyworded"]["right"]["content"]) ) {

        // page basics
        // ***

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // form options holen
//         $form_options = form_options(eCRC($environment["ebene"]).".".$environment["kategorie"]);

        // form elememte bauen
//         $element = form_elements( $cfg["keyworded"]["db"]["leer"]["entries"], $form_values );

        // form elemente erweitern
        $element["extension1"] = "<input name=\"extension1\" type=\"text\" maxlength=\"5\" size=\"5\">";
        $element["extension2"] = "<input name=\"extension2\" type=\"text\" maxlength=\"5\" size=\"5\">";

        // +++
        // page basics


        // funktions bereich fuer erweiterungen
        // ***

        // ajax fuer autocompleter
        if ( $_POST["ajax"] == "on" ) {
            $sql = "SELECT DISTINCT ".$cfg["keyworded"]["db"]["keyword"]["keyword"]."
                               FROM ".$cfg["keyworded"]["db"]["keyword"]["entries"]."
                              WHERE ".$cfg["keyworded"]["db"]["keyword"]["keyword"]." LIKE '".$_POST["keywords"]."%'";
            $result = $db -> query($sql);
            $output = array();
            $output[0] = "<li><b>".$_POST["keywords"]."</b></li>";
            while ( $data = $db -> fetch_array($result,1) ) {
                $output[] = "<li>".preg_replace("/^(".$_POST["keywords"].")/Ui",'<b>${1}</b>',$data[$cfg["keyworded"]["db"]["keyword"]["keyword"]])."</li>";
                if ( $_POST["keywords"] == $data[$cfg["keyworded"]["db"]["keyword"]["keyword"]] ) unset($output[0]);
            }
            echo "<ul>".implode("\n",$output)."</ul>";
            die();
        }

//         $ausgaben["path"] = tname2path($environment["parameter"][1]).".html";
        if ( $_POST["url"] != "" ) {
            $ausgaben["path"] = $_POST["url"];
            $keywords = explode(",",$_POST["keywords"]);
            foreach ( $keywords as $key=>$value ) {
                $keywords[$key] = trim($value);
            }
            $keywords = array_flip($keywords);$keywords = array_flip($keywords);
            $ausgaben["keywords"] = implode(", ",$keywords);
        } else {

            if ( $environment["parameter"][1] == "" ) {
                $url = preg_replace(
                                array(
                                    "/^".str_replace("/","\/",$pathvars["webroot"])."/",
                                    "/^".str_replace("/","\/",$pathvars["virtual"])."/",
                                    "/\.html$/",
                                ),
                                "",
                                $_SERVER["HTTP_REFERER"]
                );
                $path = explode("/",$url);
                $kategorie = array_pop($path);
                if ( $kategorie == "" ) {
                    $kategorie = "index";
                }
                $ebene = implode("/",$path);
                if ( $ebene == "" ) {
                    $tname = $kategorie;
                } else {
                    $tname = eCRC($ebene).".".$kategorie;
                }
                header("Location: ".$cfg["keyworded"]["basis"]."/".$environment["kategorie"].",".$tname.".html");
            } else {
                $url = tname2path($environment["parameter"][1]);
                if ( $url == "/" ) {
                    if ( $environment["parameter"][1] == "index" ) {
                        $ausgaben["path"] .= "index";
                    } else {
                        $ausgaben["path"] = "";
                        $ausgaben["form_error"] .= "#(error_url_na)";
                    }
                } else {
                    $ausgaben["path"] = $url.".html";
                }
            }

            if ( $ausgaben["path"] != "" && !priv_check($ausgaben["path"],$cfg["keyworded"]["right"]["content"]) ) {
                $ausgaben["form_error"] = "#(error_right)";
            }

            // schlagwoerter fuer die seite holen
            $sql = "SELECT *
                    FROM ".$cfg["keyworded"]["db"]["keyword"]["entries"]."
                    WHERE ".$cfg["keyworded"]["db"]["keyword"]["tname"]."='".$environment["parameter"][1]."'";
            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $result = $db -> query($sql);
            $keywords = array();
            while ( $data = $db -> fetch_array($result,1) ) {
                $keywords[] = $data[$cfg["keyworded"]["db"]["keyword"]["keyword"]];
            }
            $ausgaben["keywords"] = implode(", ",$keywords);
        }

        // in der site_menu nach titel suchen
        $menu_item = make_id(str_replace(".html","",$ausgaben["path"]));
        if ( $menu_item["mid"] != 0 ) {
            $sql = "SELECT *
                      FROM site_menu_lang
                     WHERE mid=".$menu_item["mid"]."
                       AND lang='".$environment["language"]."'";
            $result = $db -> query($sql);
            $data = $db -> fetch_array($result,1);
            $ausgaben["title"] = $data["label"];
        } else {
            $ausgaben["title"] = "";
        }

        // alle schlagwoerter holen
        $dataloop["tags_all"] = cloud_loop("all","",$keywords);
        if ( count($dataloop["tags_all"]) > 0 ) $hidedata["tags_all"] = array();



        ### put your code here ###

        // +++
        // funktions bereich fuer erweiterungen


        // page basics
        // ***

        // hidden values
        $ausgaben["form_hidden"] .= "";

        // was anzeigen
        $mapping["main"] = $cfg["keyworded"]["name"].".edit_page";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_result) #(error_result)<br />";
            $ausgaben["inaccessible"] .= "# (error_dupe) #(error_dupe)<br />";
            $ausgaben["inaccessible"] .= "# (error_right) #(error_right)<br />";
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

            // pfad-informationen holen
            $url = preg_replace(
                            array(
                                "/^".str_replace("/","\/",$pathvars["webroot"])."/",
                                "/^".str_replace("/","\/",$pathvars["virtual"])."/",
                                "/\.html$/",
                            ),
                            "",
                            $_POST["url"]
            );

            if ( !priv_check($url,$cfg["keyworded"]["right"]["content"]) ) {
                $ausgaben["form_error"] = "#(error_right)";
            }

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
                $path = explode("/",$url);
                $kategorie = array_pop($path);
                $ebene = implode("/",$path);
                if ( $ebene == "" ) {
                    $tname = $kategorie;
                } else {
                    $tname = eCRC($ebene).".".$kategorie;
                }

                // zuerst alle loeschen
                $sql = "DELETE
                          FROM ".$cfg["keyworded"]["db"]["keyword"]["entries"]."
                         WHERE ".$cfg["keyworded"]["db"]["keyword"]["tname"]."='".$tname."'
                           AND ".$cfg["keyworded"]["db"]["keyword"]["lang"]."='".$environment["language"]."'";
                $result  = $db -> query($sql);
                // neue hinzufuegen
                foreach ( $keywords as $tag ) {
                    if ( $tag == "" ) continue;
                    $sql = "INSERT INTO ".$cfg["keyworded"]["db"]["keyword"]["entries"]."
                                        (".$cfg["keyworded"]["db"]["keyword"]["tname"].",
                                         ".$cfg["keyworded"]["db"]["keyword"]["ebene"].",
                                         ".$cfg["keyworded"]["db"]["keyword"]["kategorie"].",
                                         ".$cfg["keyworded"]["db"]["keyword"]["lang"].",
                                         ".$cfg["keyworded"]["db"]["keyword"]["keyword"]."
                                        )
                                 VALUES ('".$tname."',
                                         '".$ebene."',
                                         '".$kategorie."',
                                         '".$environment["language"]."',
                                         '".$tag."'
                                        )";
                    $result  = $db -> query($sql);
                }

                // Sql um spezielle Felder erweitern
                #$ldate = $_POST["ldate"];
                #$ldate = substr($ldate,6,4)."-".substr($ldate,3,2)."-".substr($ldate,0,2)." ".substr($ldate,11,9);
                #$sqla .= ", ldate='".$ldate."'";

//                 $sql = "update ".$cfg["keyworded"]["db"]["leer"]["entries"]." SET ".$sqla." WHERE ".$cfg["keyworded"]["db"]["leer"]["key"]."='".$environment["parameter"][1]."'";
//                 if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
//                 $result  = $db -> query($sql);
//                 if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
//                 if ( $header == "" ) $header = $cfg["keyworded"]["basis"]."/list.html";
            }

            // wenn es keine fehlermeldungen gab, die uri $header laden
            if ( $ausgaben["form_error"] == "" ) {
                $header = $_SESSION["form_referer"];
                unset($_SESSION["form_referer"]);
                header("Location: ".$header);
            }
        }
//     } else {
//         header("Location: ".$pathvars["virtual"]."/");
//     }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
