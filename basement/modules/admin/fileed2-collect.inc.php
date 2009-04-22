<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "edit - edit funktion";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2009 Werner Ammon ( wa<at>chaos.de )

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

    if ( $cfg["fileed"]["right"] == "" ||
        priv_check("/".$cfg["fileed"]["subdir"]."/".$cfg["fileed"]["name"],$cfg["fileed"]["right"]) ||
        priv_check_old("",$cfg["fileed"]["right"]) ) {

        // funktions bereich fuer erweiterungen
        // ***

        // ajax-handling
        if ( $_POST["ajax"] != "" ) {
            // bilder die in der gruppierung enthalten sind
            if ( $_POST["pics_chosen"] != "" ) {
                $_SESSION["compilation_temp"][$_POST["compid"]]["contain"] = $_POST["pics_chosen"];
            } else {
                $_SESSION["compilation_temp"][$_POST["compid"]]["contain"] = array();
            }
            // bilder die NICHT in der gruppierung enthalten sind
            if ( $_POST["pics_available"] != "" ) {
                $_SESSION["compilation_temp"][$_POST["compid"]]["trash"] = $_POST["pics_available"];
            } else {
                $_SESSION["compilation_temp"][$_POST["compid"]]["trash"] = array();
            }

            $_SESSION["compilation_temp"][$_POST["compid"]]["both"] = array_merge(
                                                                            $_SESSION["compilation_temp"][$_POST["compid"]]["contain"],
                                                                            $_SESSION["compilation_temp"][$_POST["compid"]]["trash"]
                                                                      );
            die();
        }

        // feststellen, ob die galerie schon irgendwo verwendet wird
        if ( $environment["parameter"][1] != "" ) {
            $sql = "SELECT *
                      FROM site_text
                     WHERE content LIKE '%[SEL=".$environment["parameter"][1]."]%'
                        OR content LIKE '%[SEL=".$environment["parameter"][1].";%'";
            $result = $db -> query($sql);
            $num = $db -> num_rows($result);
            if ( $num > 0 && $cfg["fileed"]["compilation"]["blocked_used"] == true ) {
                header("Location:".$cfg["fileed"]["basis"]."/compilation.html");
            }
        }

        // galerie loeschen
        if ( $environment["parameter"][2] == "delete" && $environment["parameter"][1] != "" ) {
            $sql = "SELECT *
                      FROM site_file
                     WHERE fhit LIKE '%#p".$environment["parameter"][1].",%'";
            $result = $db -> query($sql);
            while ( $data = $db -> fetch_array($result,1) ){
                preg_match("/#p".$environment["parameter"][1]."[,]*[0-9]*#/i",$data["fhit"],$match);
                echo print_r($match,true);
                $fhit = trim(str_replace($match,"",$data["fhit"]));
                $sql = "UPDATE site_file
                           SET fhit='".$fhit."'
                         WHERE fid=".$data["fid"];
                $res = $db -> query($sql);
            }
            header("Location:".$cfg["fileed"]["basis"]."/compilation.html");
        }

        if ( $environment["parameter"][1] != "" ) {
            /* compilation bearbeiten */

            $hidedata["modus"]["heading"] = "#(ueberschrift_edit)";
            // compilationID
            $ausgaben["compid"] = $environment["parameter"][1];

            // dateien aus der gruppierung (DB)
            if ( count($_SESSION["compilation_temp"][$ausgaben["compid"]]["contain"]) > 0 ) {
                $sql = "SELECT *
                          FROM ".$cfg["fileed"]["db"]["file"]["entries"]."
                         WHERE ".$cfg["fileed"]["db"]["file"]["key"]." IN (".implode(",",$_SESSION["compilation_temp"][$ausgaben["compid"]]["contain"]).")";
                function pics_sort($a,$b) {
                    global $ausgaben;
                    $order = array_flip($_SESSION["compilation_temp"][$ausgaben["compid"]]["contain"]);
                    return strcasecmp($order[$a["id"]], $order[$b["id"]]);
                }
            } else {
                $sql = "SELECT *
                          FROM ".$cfg["fileed"]["db"]["file"]["entries"]."
                         WHERE fhit LIKE '%#p".$environment["parameter"][1].",%'";
                function pics_sort($a, $b) {
                    return ($a["sort"] < $b["sort"]) ? -1 : 1;
                }
            }
            $result = $db -> query($sql);
            filelist($result, "fileed", $ausgaben["compid"]);
            uasort($dataloop["list_images"],"pics_sort");
            $dataloop["chosen"] = $dataloop["list_images"];
            unset($dataloop["list_images"]);

        } else {
            /* compilation hinzufuegen */

            $hidedata["modus"]["heading"] = "#(ueberschrift_add)";
            // id der naechsten compilation rausfinden
            $dataloop["group_dropdown"] = compilation_list($environment["parameter"][1]);
            reset($dataloop["group_dropdown"]);
            $ausgaben["compid"] = key($dataloop["group_dropdown"]) + 1;

            // dateien aus der gruppierung (Session)
            if ( count($_SESSION["compilation_temp"][$ausgaben["compid"]]["contain"]) > 0 ) {
                $sql = "SELECT *
                          FROM ".$cfg["fileed"]["db"]["file"]["entries"]."
                         WHERE ".$cfg["fileed"]["db"]["file"]["key"]." IN (".implode(",",$_SESSION["compilation_temp"][$ausgaben["compid"]]["contain"]).")";
                $result = $db -> query($sql);
                filelist($result, "fileed", $ausgaben["compid"]);
                $dataloop["chosen"] = $dataloop["list_images"];
                unset($dataloop["list_images"]);
            }

        }

        // dateien aus ablage und file_memo
        $clipboard = array();
        if ( is_array($_SESSION["compilation_temp"][$ausgaben["compid"]]["trash"]) ) $clipboard = array_merge($_SESSION["compilation_temp"][$ausgaben["compid"]]["trash"],$clipboard);
        if ( is_array($_SESSION["file_memo"]) ) $clipboard = array_merge($_SESSION["file_memo"],$clipboard);
        // ids die bereist im chosen sind auslassen
        if ( count($dataloop["chosen"]) > 0 ) {
            $clipboard = array_flip($clipboard);
            $clipboard = array_diff_key($clipboard, $dataloop["chosen"]);
            $clipboard = array_flip($clipboard);
        }
        if ( count($clipboard) > 0 ) {
            $sql = "SELECT *
                      FROM ".$cfg["fileed"]["db"]["file"]["entries"]."
                     WHERE ".$cfg["fileed"]["db"]["file"]["key"]." IN (".implode(",",$clipboard).")";
            $result = $db -> query($sql);
            filelist($result, "fileed", $ausgaben["compid"]);
            $dataloop["clipboard"] = $dataloop["list_images"];
        }

        // +++
        // funktions bereich fuer erweiterungen

        // page basics
        // ***

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["fileed"]["basis"]."/collect,".$environment["parameter"][1].",".$environment["parameter"][2].",verify.html";
        $ausgaben["form_ajax_aktion"] = $cfg["fileed"]["basis"]."/collect,".$ausgaben["compid"].",".$environment["parameter"][2].",verify.html";
        $ausgaben["form_break"] = $cfg["fileed"]["basis"]."/list.html";

        // hidden values
        $ausgaben["form_hidden"] .= "";

        // was anzeigen
        $mapping["main"] = eCRC($environment["ebene"]).".collect";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($_GET["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_edit) #(error_edit)<br />";
            $ausgaben["inaccessible"] .= "# (error_result) #(error_result)<br />";
            $ausgaben["inaccessible"] .= "# (saved_groups) #(saved_groups)<br />";
            $ausgaben["inaccessible"] .= "# (new_comp) #(new_comp)<br />";
            $ausgaben["inaccessible"] .= "# (get_pics) #(get_pics)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // +++
        // page basics

        if ( $environment["parameter"][3] == "verify"
          && $_POST["abort"] != "" ) {
            $header = $cfg["fileed"]["basis"]."/compilation.html";
            unset($_SESSION["compilation_temp"][$ausgaben["compid"]]);
            if ( count($_SESSION["compilation_temp"]) == 0 ) unset($_SESSION["compilation_temp"]);
            unset($_SESSION["file_memo"]);
            unset($_SESSION["comp_last_edit"]);
            header("Location: ".$header);
        }

        if ( $environment["parameter"][3] == "verify"
          && $_POST["get_pics"] != "" ) {
            $_SESSION["comp_last_edit"] = str_replace(",verify", "", $pathvars["requested"]);
            header("Location: ".$pathvars["virtual"]."/admin/fileed/list.html");
        }

        if ( $environment["parameter"][3] == "verify"
            &&  ( $_POST["send"] != ""
                || $_POST["abort"] != ""
                || $_POST["extension2"] != "" ) ) {

            // form eingaben pruefen
            form_errors( $form_options, $_POST );

            $header = $cfg["fileed"]["basis"]."/compilation.html";

            // evtl. zusaetzliche datensatz aendern
            if ( $ausgaben["form_error"] == "" ) {

                // zuerst alle l�schen
                $sql = "SELECT *
                          FROM ".$cfg["fileed"]["db"]["file"]["entries"]."
                         WHERE ".$cfg["fileed"]["db"]["file"]["key"]." IN (".implode(",",$_SESSION["compilation_temp"][$ausgaben["compid"]]["both"]).")";
                $result = $db -> query($sql);
                while ( $data = $db -> fetch_array($result,1) ) {
                    $fhit[$data["fid"]]  = preg_replace("/#p".$ausgaben["compid"]."[,0-9]*#/Ui", "",$data["fhit"]);
                    $sql = "UPDATE ".$cfg["fileed"]["db"]["file"]["entries"]."
                               SET fhit='".trim($fhit[$data["fid"]])."'
                             WHERE ".$cfg["fileed"]["db"]["file"]["key"]."='".$data["fid"]."'";
                    $res = $db -> query($sql);
                }

                // dateien mit gruppierung versehen
                $i = 1;
                foreach ( $_SESSION["compilation_temp"][$ausgaben["compid"]]["contain"] as $value ) {
                    $fhit_new = "#p".$ausgaben["compid"].",".($i*10)."# ".trim($fhit[$value]);
                    $sql = "UPDATE ".$cfg["fileed"]["db"]["file"]["entries"]."
                               SET fhit='".trim($fhit_new)."'
                             WHERE ".$cfg["fileed"]["db"]["file"]["key"]."='".$value."'";
                    $res = $db -> query($sql);
                    $i++;
                }
            }

            // wenn es keine fehlermeldungen gab, die uri $header laden
            if ( $ausgaben["form_error"] == "" ) {
                unset($_SESSION["compilation_temp"][$ausgaben["compid"]]);
                if ( count($_SESSION["compilation_temp"]) == 0 ) unset($_SESSION["compilation_temp"]);
                unset($_SESSION["file_memo"]);
                unset($_SESSION["comp_last_edit"]);
                header("Location: ".$header);
            }
        }
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
