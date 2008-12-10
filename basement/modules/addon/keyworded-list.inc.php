<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: leer-list.inc.php 1355 2008-05-29 12:38:53Z buffy1860 $";
// "leer - list funktion";
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

    if ( $cfg["leer"]["right"] == "" || $rechte[$cfg["leer"]["right"]] == -1 ) {

        // funktions bereich
        // ***

        // bereits gewaehlte schlagwoerter holen
        $tags = array();
        if ( count($environment["parameter"]) > 1 ) {
            $buffer = $environment["parameter"];
            array_shift($buffer);
            foreach ( $buffer as $value ) {
                if ( $value == "" ) continue;
                $tags[] = trim(urldecode($value));
                $tags_url[] = $value;
            }
        }

        // schlagwoerter hinzufuegen/entfernen
        if ( $_GET["toggle_tag"] != "" ) {
            if ( in_array($_GET["toggle_tag"],$tags) ) {
                $key = array_search($_GET["toggle_tag"],$tags);
                unset($tags[$key]);
                unset($tags_url[$key]);
            } else {
                $tags_url[] = urlencode($_GET["toggle_tag"]);
            }
            $header = $cfg["keyworded"]["basis"]."/list,".implode(",",$tags_url).".html";
            header("Location:".$header);
        }

        // dataloop fuellen
        $dataloop["all_tags"] = cloud_loop("all",0,$tags);
        if ( count($dataloop["all_tags"]) > 0 ) {
            if ( priv_check($environment["ebenen"]."/".$environment["kategorie"],$cfg["keyworded"]["right"]["keywords"]) ) {
                $hidedata["all_tags_edit"] = array();
            } else {
                $hidedata["all_tags_show"] = array();
            }
        }

        if ( count($tags) > 0 ) {
            $sql = "SELECT ".$cfg["keyworded"]["db"]["keyword"]["tname"].",
                           ".$cfg["keyworded"]["db"]["keyword"]["ebene"].",
                           ".$cfg["keyworded"]["db"]["keyword"]["kategorie"].",
                           count(".$cfg["keyworded"]["db"]["keyword"]["keyword"].") as anzahl
                      FROM ".$cfg["keyworded"]["db"]["keyword"]["entries"]."
                     WHERE ".$cfg["keyworded"]["db"]["keyword"]["keyword"]." IN ('".implode("','",$tags)."')
                       AND ".$cfg["keyworded"]["db"]["keyword"]["lang"]."='".$environment["language"]."'
                  GROUP BY ".$cfg["keyworded"]["db"]["keyword"]["tname"];
            $result = $db -> query($sql);
            $pages = array();
            while ( $data = $db -> fetch_array($result,1) ) {
                if ( $data["anzahl"] != count($tags) ) continue;

                $tname = $data[$cfg["keyworded"]["db"]["keyword"]["tname"]];
                $url = $data[$cfg["keyworded"]["db"]["keyword"]["ebene"]]."/".$data[$cfg["keyworded"]["db"]["keyword"]["kategorie"]];

                // versuch den seitentitel aus menu-tabelle zu holen
                $mid = make_id($url);
                $sql = "SELECT *
                          FROM site_menu_lang
                         WHERE mid=".$mid["mid"]."
                           AND lang='".$environment["language"]."'";
                $res_menu = $db -> query($sql);
                $dat_menu = $db -> fetch_array($res_menu,1);

                // alle schlagwoerter der seite finden
                $sql = "SELECT *
                          FROM ".$cfg["keyworded"]["db"]["keyword"]["entries"]."
                         WHERE ".$cfg["keyworded"]["db"]["keyword"]["tname"]."='".$tname."'
                           AND ".$cfg["keyworded"]["db"]["keyword"]["lang"]."='".$environment["language"]."'
                      ORDER BY ".$cfg["keyworded"]["db"]["keyword"]["keyword"];
                $res_key = $db -> query($sql);
                $keyword = array();
                while ( $dat_key = $db -> fetch_array($res_key,1) ) {
                    $keyword[] = $dat_key[$cfg["keyworded"]["db"]["keyword"]["keyword"]];
                }

                $img = "pos.png";
                if ( priv_check($url,$cfg["keyworded"]["right"]["content"]) ) $img = "edit.png";

                $dataloop["pages"][$tname] = array(
                        "index" => 0,
                          "url" => $pathvars["virtual"].$url.".html",
                          "mid" => $mid["mid"],
                        "title" => $dat_menu["label"],
                     "keywords" => implode(",",$keyword),
                    "link_edit" => $cfg["keyworded"]["basis"]."/edit_page,".$tname.".html",
                          "img" => $img,
                );
            }
            if ( count($dataloop["pages"]) > 0 ) {
                usort($dataloop["pages"],"sort_pages");
                $hidedata["pages_show"] = array();
            }
        }
        // +++
        // funktions bereich


        // page basics
        // ***

        // fehlermeldungen
        if ( $HTTP_GET_VARS["error"] != "" ) {
            if ( $HTTP_GET_VARS["error"] == 1 ) {
                $ausgaben["form_error"] = "#(error1)";
            }
        } else {
            $ausgaben["form_error"] = "";
        }

        // navigation erstellen
        $ausgaben["link_new"] = $cfg["keyworded"]["basis"]."/add.html";

        // hidden values
        #$ausgaben["form_hidden"] .= "";

        // was anzeigen
        $mapping["main"] = $cfg["keyworded"]["name"].".list";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (edit_page) #(edit_page)<br />";
            $ausgaben["inaccessible"] .= "# (edit_keyword) #(edit_keyword)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        #n/a

        // +++
        // page basics

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
