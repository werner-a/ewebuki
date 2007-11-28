<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "leer - list funktion";
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

    if ( $cfg["right"] == "" || $rechte[$cfg["right"]] == -1 ) {

        // funktions bereich
        // ***

        if ( is_numeric($environment["parameter"][2]) ){
            if ( isset($_SESSION["compilation_memo"][$environment["parameter"][1]])
              && in_array($environment["parameter"][2],$_SESSION["compilation_memo"][$environment["parameter"][1]]) ){
                $key = array_search($environment["parameter"][2],$_SESSION["compilation_memo"][$environment["parameter"][1]]);
                unset($_SESSION["compilation_memo"][$environment["parameter"][1]][$key]);
            } else {
                $_SESSION["compilation_memo"][$environment["parameter"][1]][] = $environment["parameter"][2];
            }
            if ( count($_SESSION["compilation_memo"][$environment["parameter"][1]]) == 0 ) unset($_SESSION["compilation_memo"][$environment["parameter"][1]]);
            if ( count($_SESSION["compilation_memo"]) == 0 ) unset($_SESSION["compilation_memo"]);
            if ( isset($_GET["ajax"]) ){
                if ( count($_SESSION["compilation_memo"][$environment["parameter"][1]]) == 0 ) {
                    header("HTTP/1.0 404 Not Found");
                }
                exit;
            } else {
                header("Location: ".$cfg["basis"]."/".$environment["parameter"][0].",".$environment["parameter"][1].".html");
            }
        }

        $ausgaben["compid"] = $environment["parameter"][1];

        // dropdown bauen lassen
        $dataloop["groups"] = compilation_list($environment["parameter"][1]);

        // schnellsuche
        if ( $_GET["send"] ){
            if ( $_GET["search"] == "" ){
                unset($_SESSION["compilation_search"]);
            }else{
                $_SESSION["compilation_search"] = $_GET["search"];
            }
        }
        if ( isset($_SESSION["compilation_search"]) || $environment["parameter"][2] == "sel" ){
            function groups_filter ($var) {
                if ( stristr($var["name"],$_SESSION["compilation_search"])
                  || stristr($var["desc"],$_SESSION["compilation_search"])
                  || stristr($var["id"],$_SESSION["compilation_search"])
                  || ( count($_SESSION["compilation_memo"]) > 0 && array_key_exists($var["id"],$_SESSION["compilation_memo"]) ) ) {
                    return $var;
                }
            }
            $dataloop["groups"] = array_filter($dataloop["groups"], "groups_filter");
            $ausgaben["search"] = $_SESSION["compilation_search"];
        }else{
            $ausgaben["search"] = "";
        }
        if ( count($_SESSION["compilation_memo"]) > 0 ) {
            if ( $environment["parameter"][2] == "sel" ) {
                $link = $cfg["basis"]."/compilation.html";
                $aktion = "#(sel_hide)";
            } else {
                $link = $cfg["basis"]."/compilation,,sel.html";
                $aktion = "#(sel_show)";
            }
            $hidedata["selected"] = array(
                 "count" => count($_SESSION["compilation_memo"]),
                  "link" => $link,
                "aktion" => $aktion
            );
        }

        // get wird environment-parameter, weiterleitung
        if ( is_numeric($_GET["compID"]) ){
            $header = $cfg["basis"]."/compilation,".$_GET["compID"].",".$environment["parameter"][2].".html";
            header("Location: ".$header);
        } elseif (( !isset($environment["parameter"][1])
                 || !isset($dataloop["groups"][$environment["parameter"][1]])
                  ) && count($dataloop["groups"]) > 0 ){
            reset($dataloop["groups"]);
            $buffer = current($dataloop["groups"]);
            $header = $cfg["basis"]."/compilation,".$buffer["id"].",".$environment["parameter"][2].".html";
            header("Location: ".$header);
        }

        // content editor link erstellen
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "SESSION (cms_last_edit): ".$_SESSION["cms_last_edit"].$debugging["char"];
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "SESSION (cms_last_referer): ".$_SESSION["cms_last_referer"].$debugging["char"];
        if ( isset($_SESSION["cms_last_edit"]) ) {
            // abrechen im cms editor soll zur ursrungseite springen und nicht in den fileed
            $_SESSION["page"] = $_SESSION["cms_last_referer"];
            $hidedata["cms"] = array(
                   "link" => $_SESSION["cms_last_edit"]."?referer=".$_SESSION["cms_last_referer"],
                "display" => "inline",
            );
        }

        // vor- und zurueck-links
        $vor = ""; $zurueck = ""; $aktuell = ""; $i = 0;
        foreach ( $dataloop["groups"] as $value ){
            if ( $aktuell != "" ){
                $vor = $value["id"];
                break;
            }
            if ( $value["id"] == $environment["parameter"][1] ){
                $aktuell = $environment["parameter"][1];
                $ausgaben["compilation"] = "#".$value["id"].": ".$value["name"];
            }
            if ( $aktuell == "" ) {
                $zurueck = $value["id"];
            }
            $i++;
        }
        $ausgaben["comp_count"] = count($dataloop["groups"]);
        $ausgaben["aktuell"] = $i;
        if ( $vor != "" ){
            $hidedata["vor"]["link"] = $cfg["basis"]."/compilation,".$vor.",".$environment["parameter"][2].".html";
        }
        if ( $zurueck != "" ){
            $hidedata["zurueck"]["link"] = $cfg["basis"]."/compilation,".$zurueck.",".$environment["parameter"][2].".html";
        }

        // bilderliste erstellen, sortieren, zaehlen
        if ( count($dataloop["groups"]) > 0 ) {
            $sql = "SELECT *
                    FROM site_file
                    WHERE fhit
                    LIKE '%#p".$environment["parameter"][1]."%'
                ORDER BY fid";
            $result = $db -> query($sql);
            filelist($result,$environment["parameter"][1]);
            if ( count($dataloop["list"]) > 0 ) {
                function pics_sort($a, $b) {
                    return ($a["sort"] < $b["sort"]) ? -1 : 1;
                }
                uasort($dataloop["list"],"pics_sort");
            }
            $hidedata["compilation"]["pic_count"] = count($dataloop["list"]);
        } else {
            unset($hidedata["list_plain"]);
            unset($hidedata["list_ajax"]);
        }

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["basis"]."/compilation.html";
        $ausgaben["form_break"]  = $cfg["basis"]."/list.html";
        $ausgaben["edit"]        = $cfg["basis"]."/collect,".$environment["parameter"][1].".html";

        // hidden values
        #$ausgaben["form_hidden"] .= "";

        // was anzeigen
        $cfg["path"] = str_replace($pathvars["virtual"],"",$cfg["basis"]);
        $mapping["main"] = crc32($cfg["path"]).".compilation";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (img_plural) #(img_plural)<br />";
            $ausgaben["inaccessible"] .= "# (img_sing) #(img_sing)<br />";

            $ausgaben["inaccessible"] .= "# (answera) #(answera)<br />";
            $ausgaben["inaccessible"] .= "# (answerb) #(answerb)<br />";
            $ausgaben["inaccessible"] .= "# (answerc_no) #(answerc_no)<br />";
            $ausgaben["inaccessible"] .= "# (answerc_yes) #(answerc_yes)<br />";
            $ausgaben["inaccessible"] .= "# (answerc_yes_sing) #(answerc_yes_sing)<br />";
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
