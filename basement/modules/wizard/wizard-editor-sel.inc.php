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

    86343 K�nigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // was anzeigen
    $mapping["main"] = "wizard-edit";
    $hidedata["sel_global"]["on"] = "on";
    $hidedata["sel"]["on"] = "on";
    $hidedata["sel_global"] = array();
    $hidedata["sel_global"]["num"] = $tag_marken[1] + 1;
    $ausgaben["max_sel_num"] = $cfg["wizard"]["sel_edit"]["max_num"];
    $ausgaben["check_id"] = "grid_list";

    // youtube nur fuer admin
    if ( priv_check("/","admin") ) $hidedata["youtube"]["enable"] = "enable";

    // ausgabenwerte werden belegt
    $hidedata["sel_global"]["description"] = $tag_meat[$tag_marken[0]][$tag_marken[1]]["meat"];
    if ( $_POST["description"] != "" ) $hidedata["sel_global"]["description"] = $_POST["description"];

    $tag_werte = explode(";",str_replace(array("[SEL=","[SEL","]"),"",$tag_meat[$tag_marken[0]][$tag_marken[1]]["tag_start"]));
    for ($i=0;$i<=5;$i++) {
        $ausgaben["tagwerte".$i] = $tag_werte[$i];
    }
// size-radiobutton
    if ( count($cfg["wizard"]["sel_edit"]["cb_link_size"]) > 0 ) {
        foreach ( $cfg["wizard"]["sel_edit"]["cb_link_size"] as $value=>$label ) {
            $check = "";
            if ( $ausgaben["tagwerte1"] == $value ) $check = " checked=\"checked\"";
            $dataloop["size"][] = array(
                "value" => $value,
                "label" => "#(".$label.")",
                "check" => $check,
            );
        }
    } else {
        $dataloop["size"][] = array(
            "value" => $ausgaben["tagwerte1"],
            "label" => "not changeable",
            "check" => " checked=\"checked\"",
        );
    }
    // checkboxen
    if ( $ausgaben["tagwerte2"] != "" ) $hidedata["sel_global"]["check_thumb"] = " checked=\"true\"";
    if ( $ausgaben["tagwerte4"] != "" ) $hidedata["sel_global"]["check_lbox"] = " checked=\"true\"";
    // sobald ein doppelpunkt im ersten parameter ist es die bildergalerie on the fly :)
    if ( strstr($ausgaben["tagwerte0"],":") || strstr($cfg["wizard"]["add_tags"]["Selection"],"[SEL=:;") ) {
        $hidedata["sel2"]["on"] = "on";
        $hidedata["jquery"]["on"] = "on";
        $ausgaben["check_id"] = "grid_list_2";

        // ggf bilder aus db holen
        if (!strstr($ausgaben["tagwerte0"],":")) {
            $sql = "SELECT *
                            FROM site_file
                            WHERE fhit
                            LIKE '%p".$ausgaben["tagwerte0"].",%'";
            $result = $db -> query($sql);
            while ( $data = $db -> fetch_array($result,1) ) {

                preg_match("/#p".$ausgaben["tagwerte0"]."[,]*([0-9]*)#/i",$data["fhit"],$match);
                $dataloop["list_files"][$match[1]] = array(
                            "id"    => $data["fid"],
                            "src"   => "/file/picture/thumbnail//tn_".$data["fid"],
                            "ffart"  => $data["ffart"],
                            "ffname" => $data["ffname"],
                            "under" => $data["funder"],
                            "desc"  => $data["fdesc"]
                            );
            }
            ksort($dataloop["list_files"]);
            foreach ( $dataloop["list_files"] as $bild_id ) {
                $sel_pics[] = $trenner.$bild_id["id"];
            }
        } else {
            $sel_pics = explode(":",$ausgaben["tagwerte0"]);
        }
        $prev_pics = explode(":",$ausgaben["tagwerte3"]);
        $i = 0;

        // sql erstellen und array fuer multisort
        foreach ( $sel_pics as $value ) {
            if ( $value == "" ) continue;
            $i++;
            $tmp_sort[$value] = $i;
            ( $iarray == "" ) ? $trenner = "" : $trenner = ",";
            $iarray .= $trenner.$value;
        }
        unset($dataloop["list_images"]);
        if ( $iarray == "" ) $iarray = 0;
        $sql = "SELECT * FROM site_file WHERE fid in (".$iarray.")";
        $result = $db -> query($sql);
        filelist($result, "fileed",$ausgaben["tagwerte0"]);
        $dataloop["chosen_images"] = $dataloop["list_images"];
        if ( is_array($dataloop["chosen_images"]) ) {
            foreach ( $dataloop["chosen_images"] as $key => $value ) {
                $sortarray[] = $tmp_sort[$key];
                if ( in_array($key, $prev_pics ) ) {
                    $dataloop["chosen_images"][$key]["checked"] = "checked";
                }
            }
            array_multisort( $sortarray, $dataloop["chosen_images"]);
            unset($dataloop["list_images"]);
        }

        if ( is_array($_SESSION["file_memo"]) ) {
            $sess_images = implode(",",$_SESSION["file_memo"]);
        }
        if ( $sess_images == "" ) $sess_images = 0;

        $sql = "SELECT * FROM site_file WHERE fid in (".$sess_images.") and fid not in (".$iarray.")";
        $result = $db -> query($sql);
        // dataloop wird ueber eine share-funktion aufgebaut
        filelist($result, "fileed",$ausgaben["tagwerte0"]);
// Bildergalerie mit DB
    } else {
        $hidedata["sel_db"]["on"] = "on";
        // selection aus session/tag holen
        if ( is_array($_SESSION["compilation_memo"]) || $tag_werte[0] != "" ) {
            if ( is_array($_SESSION["compilation_memo"]) ) {
                $ausgaben["tagwerte0"] = key($_SESSION["compilation_memo"]);
                $array = current($_SESSION["compilation_memo"]);
            } elseif ( $tag_werte[3] != "" ) {
                $array = explode(":",$tag_werte[3]);
            }

            $sql = "SELECT *
                      FROM site_file
                     WHERE fhit
                      LIKE '%p".$ausgaben["tagwerte0"].",%'";
            $result = $db -> query($sql);
            // dataloop wird ueber eine share-funktion aufgebaut
            filelist($result, "fileed",$ausgaben["tagwerte0"]);

    // echo "<pre>".print_r($array,true)."</pre>";
            if ( count($dataloop["list_images"]) > 0 ) {
                foreach ( $dataloop["list_images"] as $key=>$value ) {
                    $buffer[$value["sort"]] = $value;
                    if ( ( is_array($array) && in_array($key,$array) )
                      || ( is_array($_POST["tagwerte"][3]) && in_array($key,$_POST["tagwerte"][3]) ) ) {
                        $buffer[$value["sort"]]["checked"] = " checked=\"true\"";
                    } else {
                        $buffer[$value["sort"]]["checked"] = "";
                    }
                }
                ksort($buffer);
                $dataloop["list_images"] = $buffer;
            }
        }
    }
 

    // abspeichern, part 2
    // * * *
    if ( $environment["parameter"][7] == "verify"
        &&  ( $_POST["send"] != ""
            || $_POST["add"] != ""
            || $_POST["sel"] != ""
            || $_POST["create_sel"] != ""
            || $_POST["refresh"] != ""
            || $_POST["uploaded"] != "" ) ) {

        // ggf bild einfuegen
        $error = file_validate($_FILES["new_file"]["tmp_name"], $_FILES["new_file"]["size"], $cfg["file"]["filesize"], $cfg["file"]["filetyp"], "new_file");
        if ( $error == 0 ) {
            $newname = $cfg["file"]["base"]["maindir"].$cfg["file"]["base"]["new"].$_SESSION["uid"]."_".$_FILES["new_file"]["name"];
            rename($_FILES["new_file"]["tmp_name"],$newname);
        }

        if ( strstr($ausgaben["tagwerte0"],":") || strstr($cfg["wizard"]["add_tags"]["Selection"],"[SEL=:;") ) {
            $_POST["tagwerte"][0] = "";
            if (is_array($_POST["pics"]) ) {
                foreach ( $_POST["pics"] as $value ) {
                    if ( $value == "" ) continue;
                    $_POST["tagwerte"][0] .= $value.":";
                }
            }
            if ( $_POST["tagwerte"][0] == "" ) $_POST["tagwerte"][0] = ":";
        }
            if ( is_array($_POST["tagwerte"][3]) ) $_POST["tagwerte"][3] = implode(":",$_POST["tagwerte"][3]);
            $tag_werte = array();
            for ($i = 0; $i <= 5; $i++) {
                $tag_werte[] = $_POST["tagwerte"][$i];
            }

//echo "<pre>";
//print_r($_POST);
//echo "</pre>";
//exit;

        $to_insert = "[SEL=".implode(";",$tag_werte)."]".$_POST["description"]."[/SEL]";
        
        if ( $cfg["wizard"]["sel_edit"]["max_num"] != "" && count(explode(":",$_POST["tagwerte"][3])) > $cfg["wizard"]["sel_edit"]["max_num"] ) {
            $ausgaben["form_error"] .= count(explode(":",$_POST["tagwerte"][3]))."#(sel_num_error)".$cfg["wizard"]["sel_edit"]["max_num"]."<br />";;
        }

    }
    // + + +

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>