<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "administration.inc.php v1 emnili";
  $Script["desc"] = "administration modul";
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

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** ".$script["name"]." ** ]".$debugging["char"];

    if ( $_SESSION["username"] != "" ) {

        include $pathvars["moduleroot"]."wizard/wizard.cfg.php";
        unset($cfg["wizard"]["function"]);
        include $pathvars["moduleroot"]."wizard/wizard-functions.inc.php";

        // bereiche werden nach aenderungsdatum sortiert
        if ( !function_exists("sort_marked_content") ) {
            function sort_marked_content($a,$b) {
                if ( $a["changed_db"] < $b["changed_db"] ) {
                    return 1;
                } elseif ( $a["changed_db"] == $b["changed_db"] ) {
                    return 0;
                } else {
                    return -1;
                }
            }
        }

        //**********
        //** SUCHE
        //**********
        if ( $_GET["main"] == "ajax_suche" ) {

            header("HTTP/1.1 200 OK");
            $released_content = find_marked_content( $_GET["art"], $cfg, "inhalt", array(1), array("max_age"=>1700), FALSE ,array('presse','ausstellungen','termine'));

            $counter = 0;
            $hit = $released_content[1];

            if ( is_array($hit) ) {
                uasort($hit,'sort_marked_content');
                foreach ( $hit as $key => $value ) {
                    if ( $_GET["term"] ){
                        if ( stristr($value["titel"],$_GET["term"]) || stristr($value["ext"],$_GET["term"]) ) {

                        } else {
                            continue;
                        }
                    }
                    if ( $value["titel"] == "---" ) $value["titel"] = $value["ext"];

                    $counter++;
                    $value["titel"] = str_replace("\"","", $value["titel"]);
                    $value["titel"] = str_replace("'","", $value["titel"]);
                    $value["titel"] = str_replace("\r\n","", $value["titel"]);
                    $value["titel"] = str_replace("\n","", $value["titel"]);
                    $value["titel"] = str_replace("\r","", $value["titel"]);
                    $value["titel"] = str_replace("\n\r","", $value["titel"]);

                    $buffer[] = '{
                            "id": "'.$counter.'",
                            "label": "'.$value["titel"].'",
                                "edit":"'.$value["edit"].'",
                                "view":"'.$value["view"].'",
                            "last_author": "'.$value["last_author"].'",
                            "changed": "'.$value["changed"].'"
                            }';
                }
            }
            if ( $counter == 0 ) {
                echo "[{\"label\": \"Keine Treffer\"}]";
            } else {
                echo "[ ".implode(" , ",$buffer)." ]";
            }
            die();
        }
        //++++++++++
        //++ SUCHE
        //++++++++++


        // Wer bin ich - Benutzer und Gruppen holen
        $ausgaben["user"] = $_SESSION["username"];
        $sql = "SELECT *
                    FROM auth_member
                    JOIN auth_group
                    ON (auth_member.gid=auth_group.gid)
                    WHERE uid=".$_SESSION["uid"];
        $result = $db -> query($sql);
        while ( $data = $db -> fetch_array($result,1) ) {
            $dataloop["groups"][]["groups"] = $data["beschreibung"];
            $dataloop["group_id"][] = $data["gid"];
        }

        // evtl. externe skripte includen
        if ( $cfg["admin"]["ext_script"] != "" ) {
            include $pathvars["moduleroot"].$cfg["admin"]["ext_script"];
        } else {
            $ausgaben["ext_script"] = "";
        }

        // blogs durchgehen
        foreach ( $cfg["bloged"]["blogs"] as $url => $blog_value ) {
            $exclude[] = $url;
            if ( !priv_check($blog_key,"edit") ) continue;
            $bereich = str_replace("/", "",$url);
            unset($dataloop["edit_section"]);
            unset($hidedata["edit_section"]);
            unset($dataloop["release_wait_section"]);
            unset($hidedata["release_wait_section"]);
            unset($dataloop["release_queue"]);
            unset($hidedata["release_queue"]);

            // dataloop holen
            $buffer = find_marked_content( $url, $cfg, "inhalt", array(-2,-1), array(), FALSE );

            // buffer durchgehen und nach bereiche aufteilen
            if ( is_array($buffer) ) {
                foreach ( $buffer as $key => $value ) {
                    foreach ( $value as $own_key => $own_value ) {
                        if ( crc32($url) == substr($own_value["tname"],0,strpos($own_value["tname"],".")) ) {
                            ${$bereich}[$key][] = $buffer[$key][$own_key];
                            unset($buffer[$key][$own_key]);
                        }
                    }
                }
            }

            if ( count(${$bereich}[-1]) > 0 && priv_check($url, $cfg["wizard"]["right"]["edit"]) ) {
                $hidedata["edit_section"]["name"] = $bereich;
                $dataloop["edit_section"] = ${$bereich}[-1];
                uasort($dataloop["edit_section"],'sort_marked_content');
            }

            if ( count(${$bereich}[-2]) > 0 && priv_check($url, $cfg["wizard"]["right"]["publish"]) ) {
                $hidedata["release_queue"]["name"] = $bereich;
                $dataloop["release_queue"] = ${$bereich}[-2];
            }

            if ( ( !is_array($hidedata["release_queue"]) && count(${$bereich}[-2]) ) && priv_check($url, $cfg["wizard"]["right"]["edit"]) ) {
                $hidedata["release_wait_section"]["name"] = $bereich;
                $dataloop["release_wait_section"] = ${$bereich}[-2];
            }
            $ausgaben["bereiche"] .= parser("administration-blogs",'');

        }
        // normalen content ausschliesslich spezielle bereiche durchgehen
        // * * *
        unset($dataloop["edit_section"]);
        unset($hidedata["edit_section"]);
        unset($dataloop["release_wait_section"]);
        unset($hidedata["release_wait_section"]);
        unset($dataloop["release_queue"]);
        unset($hidedata["release_queue"]);

        $bereich = "normal";
        $buffer = find_marked_content( "/", $cfg, "inhalt", array(-2,-1), array(), FALSE, $exclude);

        if ( is_array($buffer) ) {
            foreach ( $buffer as $key => $value ) {
                foreach ( $value as $own_key => $own_value ) {
                    ${$bereich}[$key][] = $buffer[$key][$own_key];
                    unset($buffer[$key][$own_key]);
                }
            }
        }

        if ( count(${$bereich}[-1]) > 0 && priv_check($url, $cfg["wizard"]["right"]["edit"]) ) {
            $hidedata["edit_section"]["name"] = $bereich;
            $dataloop["edit_section"] = ${$bereich}[-1];
            uasort($dataloop["edit_section"],'sort_marked_content');
        }

        if ( count(${$bereich}[-2]) > 0 && priv_check($url, $cfg["wizard"]["right"]["publish"]) ) {
            $hidedata["release_queue"]["name"] = $bereich;
            $dataloop["release_queue"] = ${$bereich}[-2];
        }

        if ( ( !is_array($hidedata["release_queue"]) && count(${$bereich}[-2]) ) && priv_check($url, $cfg["wizard"]["right"]["edit"]) ) {
            $hidedata["release_wait_section"]["name"] = $bereich;
            $dataloop["release_wait_section"] = ${$bereich}[-2];
        }

        $ausgaben["bereiche"] .= parser("administration-content",'');

        $ausgaben["user"] = $_SESSION["username"];
        // ggf. toggles ausklappen
        if ( is_array($_SESSION["admin_toggle"]) ) {
            foreach ( $_SESSION["admin_toggle"] as $toggle ) {
    //             $ausgaben["toggle_".$toggle] = "block";
                $dataloop["toggles"][]["element"] = $toggle;
            }
        }
        // +++
        // funktions bereich

        // TEST MENUED
        if ( $_SESSION["uid"] ) $hidedata["menu_edit"]["on"] = "on";
        $ausgaben["ajax_menu"] = "login.html";
        if ( $_POST["ajax_menu"] != "" ) {
            $design = "modern";
            $stop["nop"] = "nop";
            $positionArray = "";
            include $pathvars["moduleroot"]."admin/menued2.cfg.php";
            $cfg["menued"]["function"]["login"] = array("locate","make_ebene");
            include $pathvars["moduleroot"]."admin/menued2-functions.inc.php";
            include $pathvars["moduleroot"]."libraries/function_menutree.inc.php";

            // welche buttons sollen angezeigt werden
            $mod = array(
                        "edit"=> array("", "Seite editieren", "edit"),
                        "add"=> array("", "Seite hinzufuegen", "add"),
                    "jump"=> array("", "zur Seite", "edit;publish")
                        );

            $_SESSION["menued_id"] = $_POST["point_id"];
            locate($_POST["point_id"]);

            $wizard_menu = sitemap($_POST["point_id"], "admin", "wizard",$mod,"");

            $lines = explode("<li>",$wizard_menu);
            array_shift($lines);

            $preg = '/(href="\/auth\/login,)([0-9]*)\.html"/i';

            $color = $cfg["wizard"]["color"]["a"];
            echo "<ul style=\"list-style: none\">";

            // zurueck - link bauen
            if ( is_numeric($positionArray[0]) ) {
                if ( is_numeric($positionArray[1]) ) {
                    $back_id = $positionArray[1];
                } else {
                    $back_id = 0;
                }
                echo "<li><a onclick=\"aj_menu(".$back_id.")\">zurück</a></li>";
            }
            // zurueck - link bauen

            foreach ( $lines as $line ) {

                ( $color == $cfg["wizard"]["color"]["a"] ) ? $color = $cfg["wizard"]["color"]["b"] : $color = $cfg["wizard"]["color"]["a"];
                preg_match($preg,$line,$regs);

                if ( $regs[2] ) {
                    $line = str_replace("href=\"/auth/login,".$regs[2].".html\"","onclick= aj_menu(".$regs[2].")",$line);
                    echo "<li style=\"background-color:".$color.";margin:0;padding:0.5em;clear:both;\">".$line."</li>";
                } else {
                    echo "<li style=\"background-color:".$color.";margin:0;padding:0.5em;clear:both;\">".$line."</li>";
                }
            }
            echo "</ul>";
            exit;
        }
        // TEST MENUED

        // page basics
        // ***

        // label bearbeitung aktivieren
        if ( isset($_GET["edit"]) ) {
            $specialvars["editlock"] = 0;
        } else {
            $specialvars["editlock"] = -1;
        }

        // wohin schicken
        $backlink = "";
        if ( $_SERVER["HTTP_REFERER"] != "" ) {
            if ( strstr($_SERVER["HTTP_REFERER"],"/login.html" )
                || strstr($_SERVER["HTTP_REFERER"],"/wizard/")
                || strstr($_SERVER["HTTP_REFERER"],"/admin/") ) {
                if ( $_SESSION["admin_back_link"] != "" ) {
                    $backlink = $_SESSION["admin_back_link"];
                } else {
                    $backlink = "/index.html";
                }
            } else {
                $backlink = $_SERVER["HTTP_REFERER"];
            }
        } else {
            if ( $_SESSION["admin_back_link"] != "" ) {
                $backlink = $_SESSION["admin_back_link"];
            } else {
                $backlink = "/index.html";
            }
        }
        $backlink = preg_replace(
                        array("/^(".str_replace("/","\/",$pathvars["webroot"]).")/","/^\/auth/"),
                        "",
                        $backlink
                    );
        if ( $_SESSION["uid"] != "" ) {
            $backlink = "/auth".$backlink;
        }
        session_start();
        $_SESSION["admin_back_link"] = $backlink;
        $ausgaben["back_link"] = $backlink;

        // +++
        // page basics

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($_GET["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (login) #(login)<br />";
            $ausgaben["inaccessible"] .= "# (error1) #(error1)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // was anzeigen
        $mapping["main"] = "administration";

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
