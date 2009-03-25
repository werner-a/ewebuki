<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: leer-functions.inc.php 1131 2007-12-12 08:45:50Z chaot $";
// "funktion loader";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2007 Werner Ammon ( wa<at>chaos.de )

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

    if ( !function_exists(cloud_loop)) {
        include $pathvars["moduleroot"]."libraries/function_menu_convert.inc.php";
        if ( !is_array($cfg["keyworded"]) ) include $pathvars["moduleroot"]."addon/keyworded.cfg.php";

        function cloud_loop( $area, $quantum=0, $sel_tags=array() ) {
            global $db, $environment, $pathvars, $cfg, $debugging;

            // nur schlagwoerter der aktuellen seite?
            $where = "";$where_part = array();
            if ( $area == "local" ) {
                if ( $environment["ebene"] == "" ) {
                    $tname = $environment["kategorie"];
                } else {
                    $tname = eCRC($environment["ebene"]).".".$environment["kategorie"];
                }
                $where_part[] = "tname='".$tname."'";
            }
            $where = implode(" AND ",$where_part);
            if ( $where != "" ) $where = "WHERE ".$where;

            // maximum und minimum rausfinden
            $sql = "SELECT count(".$cfg["keyworded"]["db"]["keyword"]["key"].") as count
                      FROM ".$cfg["keyworded"]["db"]["keyword"]["entries"]."
                      ".$where."
                  GROUP BY ".$cfg["keyworded"]["db"]["keyword"]["keyword"]."
                  ORDER BY count ASC
                     LIMIT 0,1";
            $result = $db -> query($sql);
            $data = $db -> fetch_array($result,1);
            $min = $data["count"] - 0.01;
            $sql = "SELECT count(".$cfg["keyworded"]["db"]["keyword"]["key"].") as count
                      FROM ".$cfg["keyworded"]["db"]["keyword"]["entries"]."
                      ".$where."
                  GROUP BY ".$cfg["keyworded"]["db"]["keyword"]["keyword"]."
                  ORDER BY count DESC
                     LIMIT 0,1";
            $result = $db -> query($sql);
            $data = $db -> fetch_array($result,1);
            $max = $data["count"] + 0.01;

            // "spannweite"
            $range = $max - $min;

            // anzahl der tag-klassen
            $count_class = count($cfg["keyworded"]["tag_class"]);

            $sql = "SELECT ".$cfg["keyworded"]["db"]["keyword"]["keyword"].",
                           count(".$cfg["keyworded"]["db"]["keyword"]["key"].") as count
                      FROM ".$cfg["keyworded"]["db"]["keyword"]["entries"]."
                      ".$where."
                  GROUP BY ".$cfg["keyworded"]["db"]["keyword"]["keyword"]."
                  ORDER BY count DESC";
            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql (cloud_loop): ".$sql.$debugging["char"];
            $result = $db -> query($sql);

            $loop = array();$sel_keywords = array();

            while ( $data = $db -> fetch_array($result,1) ) {

                $keyword = $data[$cfg["keyworded"]["db"]["keyword"]["keyword"]];

                $anzahl = "(".$data["count"].")";
                if ( floor($range) == 0 ) $anzahl = "";

                $class = "";
                if ( in_array($data[$cfg["keyworded"]["db"]["keyword"]["keyword"]],$sel_tags) ) {
                    $class = " selected";
                }
                $class_id = floor(($data["count"] - $min)/($range/$count_class));
                $class = $cfg["keyworded"]["tag_class"][$class_id].$class;

                $link = "";
                if ( !strstr($pathvars["uri"],$cfg["keyworded"]["basis"]) ) $link = $cfg["keyworded"]["basis"]."/list.html";
                $link .= "?toggle_tag=".urlencode($data[$cfg["keyworded"]["db"]["keyword"]["keyword"]]);

                $loop[strtolower($keyword)] = array(
                       "keyword" => $keyword,
                    "keyword_id" => str_replace(" ","_",$keyword),
                        "anzahl" => $anzahl,
                         "class" => $class,
                          "link" => $link,
                     "link_edit" => $cfg["keyworded"]["basis"]."/rename_tag,".urlencode($keyword).".html",
                );
            }
            ksort($loop);

            // maximalanzahl der angezeigten tags
            if ( $quantum > 0 ) $loop = array_slice($loop,0,$quantum);
            return $loop;
        }

        function related_pages( $quantum=0 ) {
            global $db, $environment, $pathvars, $cfg, $debugging;


            // seiten mit gleichen schlagwoertern finden
            if ( $environment["ebene"] == "" ) {
                $tname = $environment["kategorie"];
            } else {
                $tname = eCRC($environment["ebene"]).".".$environment["kategorie"];
            }
            $sql = "SELECT *
                      FROM ".$cfg["keyworded"]["db"]["keyword"]["entries"]."
                     WHERE ".$cfg["keyworded"]["db"]["keyword"]["keyword"]."
                        IN (SELECT ".$cfg["keyworded"]["db"]["keyword"]["keyword"]."
                              FROM ".$cfg["keyworded"]["db"]["keyword"]["entries"]."
                             WHERE ".$cfg["keyworded"]["db"]["keyword"]["tname"]."='".$tname."'
                           )
                       AND ".$cfg["keyworded"]["db"]["keyword"]["tname"]." != '".$tname."'
                  ORDER BY ".$cfg["keyworded"]["db"]["keyword"]["keyword"];
            $result = $db -> query($sql);
            $pages = array();
            while ( $data = $db -> fetch_array($result,1) ) {
                $url = $data[$cfg["keyworded"]["db"]["keyword"]["ebene"]]."/".$data[$cfg["keyworded"]["db"]["keyword"]["kategorie"]];

                $tname = $data[$cfg["keyworded"]["db"]["keyword"]["tname"]];

                $keywords = $pages[$tname]["keywords"];
                if ( $keywords != "" ) {
                    $keywords .= ", ";
                }
                $keywords .= $data[$cfg["keyworded"]["db"]["keyword"]["keyword"]];

                $index = $pages[$tname]["index"] + 1;

                $mid = make_id($url);
                $sql = "SELECT *
                          FROM site_menu_lang
                         WHERE mid=".$mid["mid"]."
                           AND lang='".$environment["language"]."'";
                $res_menu = $db -> query($sql);
                $dat_menu = $db -> fetch_array($res_menu,1);
                $title = $dat_menu["label"];
                // zus. titel aus content holen
                if ( $specialvars["content_release"] == -1 && $version == "" ) {
                    $content_release = "AND status>0";
                } else {
                    $content_release = "";
                }
                $sql = "SELECT *
                          FROM site_text
                         WHERE tname='".$tname."'
                           AND lang='".$environment["language"]."'
                           AND label='inhalt'
                            ".$content_release."
                      ORDER BY version DESC
                         LIMIT 0,1";
                $res_content = $db -> query($sql);
                $dat_content = $db -> fetch_array($res_content,1);
                preg_match("/\[H1\](.*)\[\/H1\]/U",$dat_content["content"],$match);
                if ( $match[1] != "" ) {
                    $title_content = $match[1];
                } else {
                    $title_content = $title;
                }

                if ( $title == "" ) continue;

                $pages[$tname] = array(
                       "index" => $index,
                         "url" => $pathvars["virtual"].$url.".html",
                         "mid" => $mid["mid"],
                       "title" => $title,
               "title_content" => $title_content,
                    "keywords" => $keywords,
                );
            }
            usort($pages,"sort_pages");
            if ( $quantum > 0 ) $pages = array_slice($pages, 0, $quantum);
            return $pages;
        }

        function sort_pages($a, $b) {
            if ($a["index"] == $b["index"]) {
                return (strtolower($a["title"]) < strtolower($b["title"])) ? -1 : 1;
            }
            return ($a["index"] > $b["index"]) ? -1 : 1;
        }

    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
