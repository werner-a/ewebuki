<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// keyworded-edit_page.inc.php v1 krompi
// keyworded-edit_page - edit funktion
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2015 Werner Ammon ( wa<at>chaos.de )

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

    if ( $cfg["keyworded"]["right"]["keywords"] == "" || priv_check(tname2path($environment["parameter"][1]), $cfg["keyworded"]["right"]["keywords"] ) ) {

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        if ( !empty($_POST["url"]) ) {
            $ausgaben["path"] = $_POST["url"];
            foreach ( $_POST["keywords"] as $key=>$value ) {
                if ( $value == "##value##") continue;
                $keywords[$key] = trim($value);
            }
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
                if ( preg_match("/^\/keywords\/edit_page/",$url) ) {
                    header("Location: ".$_SERVER["HTTP_REFERER"]);
                    exit;
                }
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
                $tname = addslashes($tname);
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

            // schlagwoerter fuer die seite holen
            $sql = "SELECT *
                    FROM ".$cfg["keyworded"]["db"]["keyword"]["entries"]."
                    WHERE ".$cfg["keyworded"]["db"]["keyword"]["tname"]."='".$environment["parameter"][1]."'";
            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $result = $db -> query($sql);
            $keywords = array();
            while ( $data = $db -> fetch_array($result,1) ) {
                $dataloop["sitetags"][]["keyword"] = $data[$cfg["keyworded"]["db"]["keyword"]["keyword"]];
                $keywords[] = $data[$cfg["keyworded"]["db"]["keyword"]["keyword"]];
            }
            $ausgaben["schlag"] ="";
            if ( count($dataloop["sitetags"]) == 0 ) $ausgaben["schlag"] ="none";;
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

        // hidden values
        $ausgaben["form_hidden"] = "";

        // was anzeigen
        $mapping["main"] = $cfg["keyworded"]["name"].".edit_page";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($_GET["edit"]) ) {
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

        if ( isset($environment["parameter"][2]) && $environment["parameter"][2] == "verify"
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
                $tname = addslashes($tname);

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
            }

            // wenn es keine fehlermeldungen gab, die uri $header laden
            if ( $ausgaben["form_error"] == "" ) {
                $header = $_SESSION["form_referer"];
                unset($_SESSION["form_referer"]);
                header("Location: ".$header);
            }
        }
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
