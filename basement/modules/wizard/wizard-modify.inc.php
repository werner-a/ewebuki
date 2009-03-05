<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: contented-edit.inc.php 1242 2008-02-08 16:16:50Z chaot $";
// "contented - edit funktion";
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

    86343 Königsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // parameter-verzeichnis:
    // 1: Datenbank
    // 2: tname
    // 3: label
    // 4: marke
    // 5: version
    // 6: anker-name
    // 7: modus

    // datenbank auswaehlen
    $database = $environment["parameter"][1];
    $db->selectDb($database,FALSE);

    if ( is_array($_SESSION["content"]) ) {

        // page basics
        // ***
        if ( $environment["parameter"][5] != "" ) {
            $version = " AND version=".$environment["parameter"][5];
        } else {
            $version = "";
        }

        if ( count($_POST) == 0 ) {

            $sql = "SELECT version, html, content, changed, byalias
                      FROM ". SITETEXT ."
                     WHERE lang = '".$environment["language"]."'
                       AND label ='".$environment["parameter"][3]."'
                       AND tname ='".$environment["parameter"][2]."'
                       $version
                     ORDER BY version DESC
                     LIMIT 0,1";
            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $result = $db -> query($sql);

            $form_values = $db -> fetch_array($result,1);

        } else {
            $form_values = $_POST;
        }

        // falls content in session steht
        $identifier = $environment["parameter"][1].",".$environment["parameter"][2].",".$environment["parameter"][3];
        if ( $_SESSION["wizard_content"][$identifier] != "" ) {
            $form_values["content"] = $_SESSION["wizard_content"][$identifier];
        }

        // wizard-typ rausfinden
        preg_match("/\[!\]wizard:(.*)\[\/!\]/i",$form_values["content"],$match);
        if ( $match[1] != "" && is_array($cfg["wizard"]["wizardtyp"][$match[1]]) ) {
            $wizard_name = $match[1];
        } else {
            $wizard_name = "standard";
        }

        // was soll modifiziert werden
        $tag_marken = explode(":",$environment["parameter"][4]);
        $tag_meat = content_split_all($form_values["content"]);
        $anker = "";

        if ( ( count($tag_marken) >  1 || $environment["parameter"][4] == "nop" )
          && ( strstr($_SERVER["HTTP_REFERER"],$cfg["wizard"]["basis"]) || $_SERVER["HTTP_REFERER"] == "" ) ) {
            switch ( $environment["parameter"][7] ) {
                case "add":
                    $allcontent = content_level1($form_values["content"]);

                    if ( $cfg["wizard"]["wizardtyp"][$wizard_name]["section_block"][1] == 0 ) {
                        $buffer = $allcontent;
                        $buffer[] = $cfg["wizard"]["add_tags"][$tag_marken[0]];
                        end($buffer);
//                         $environment["parameter"][6] = key($buffer);
                        $anker = "?scroll=item_".key($buffer);
                    } else {
                        foreach ( $allcontent as $key=>$value ) {
                            if ( (count($allcontent) - $key) <= $cfg["wizard"]["wizardtyp"][$wizard_name]["section_block"][1] ) {
                                $buffer[] = preg_replace("/^[ ]+/m","",$cfg["wizard"]["add_tags"][$tag_marken[0]]);
//                                 $environment["parameter"][6] = $key + 1;
                                $anker = "scroll=item_".$key + 1;
                            }
                            $buffer[] = trim($value);
                        }
                    }
                    $content = implode(chr(13).chr(10).chr(13).chr(10),$buffer);
                    break;
                case "delete":
                    if ( $tag_marken[0] == "section" ) {
                        $allcontent = content_level1($form_values["content"]);
                        foreach ( $allcontent as $key=>$value ) {
                            if ( $key == $tag_marken[1] ) continue;
                            $buffer[] = trim($value);
                        }
                        $content = implode(chr(13).chr(10).chr(13).chr(10),$buffer);
                    } else {
                        $content = substr($form_values["content"],0,$tag_meat[$tag_marken[0]][$tag_marken[1]]["start"]).
                                   substr($form_values["content"],$tag_meat[$tag_marken[0]][$tag_marken[1]]["end"]);
                    }
                    $environment["parameter"][6] = "";
                    break;
                case "rip":
                    $content = substr($form_values["content"],0,$tag_meat[$tag_marken[0]][$tag_marken[1]]["start"]).
                               $tag_meat[$tag_marken[0]][$tag_marken[1]]["meat"].
                               substr($form_values["content"],$tag_meat[$tag_marken[0]][$tag_marken[1]]["end"]);
                    break;
                case "move":
                    $allcontent = content_level1($form_values["content"]);
                    // sortier-array bestimmen
                    if ( is_array($_GET["content_blocks"]) ) {
                        $order = $_GET["content_blocks"];
                    } elseif ( is_array($_GET["img_map"]) ) {
                        $order = $_GET["img_map"];
                    }
                    // sortier-array ggf auffuellen
                    $min = min($order);
                    $max = max($order);
                    $pre_buffer = array();$post_buffer = array();
                    foreach ( $allcontent as $key=>$index ) {
                        if ( !in_array($key,$order) ) {
                            if ( $key < $min ) {
                                $pre_buffer[] = $key;
                            } elseif ( $key > $max ) {
                                $post_buffer[] = $key;
                            }
                        }
                    }
                    $order = array_merge($pre_buffer, $order, $post_buffer);
                    // content neu sortieren
                    foreach ( $order as $key ) {
                        $buffer[] = $allcontent[$key];
                    }
                    // ggf parameter anpassen
                    if ( $environment["parameter"][6] != "" && is_numeric($environment["parameter"][6]) ) {
                        $environment["parameter"][6] = array_search($environment["parameter"][6],$order);
                    }
                    // content neu zusammenbauen
                    $content = implode(chr(13).chr(10).chr(13).chr(10),$buffer);
                    break;
                default:
                    header("Location: ".$_SERVER["HTTP_REFERER"]);
                    break;
            }

            $_SESSION["wizard_content"][$identifier] = $content;
        }

        if ( strstr($_SERVER["HTTP_REFERER"],$cfg["wizard"]["basis"]) || $_SERVER["HTTP_REFERER"] == "" ) {
            $header = $cfg["wizard"]["basis"]."/show,".
                                                $environment["parameter"][1].",".
                                                $environment["parameter"][2].",".
                                                $environment["parameter"][3].",,".
                                                $environment["parameter"][5].",".
                                                $environment["parameter"][6].".html";
//             $anker = "";
//             if ( $environment["parameter"][6] != "" && is_numeric($environment["parameter"][6]) ) $anker = "#item_".$environment["parameter"][6];
            header("Location: ".$header.$anker);
        } else {
            header("Location: ".$cfg["wizard"]["basis"]."/show,".$environment["parameter"][1].",".$environment["parameter"][2].",".$environment["parameter"][3].".html");
        }
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

    $db -> selectDb(DATABASE,FALSE);



////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
