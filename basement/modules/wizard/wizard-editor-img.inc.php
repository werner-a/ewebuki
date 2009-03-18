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
    $hidedata["img"] = array();
    $hidedata["img"]["num"] = $tag_marken[1] + 1;

    // beschreibung
    $hidedata["img"]["description"] = $tag_meat[$tag_marken[0]][$tag_marken[1]]["meat"];
    if ( $_POST["description"] != "" ) $hidedata["img"]["description"] = $_POST["description"];

    // tag-attribute
    $opentag = str_replace(array("[","]"),"",$tag_meat[$tag_marken[0]][$tag_marken[1]]["tag_start"]);
    $tag_werte = explode(";",trim(strstr($opentag,"="),"="));
    for ($i = 0; $i <= 6; $i++) {
        if ( is_array($_POST["tagwerte"]) ) {
            $ausgaben["tagwerte".$i] = $_POST["tagwerte"][$i];
        } elseif ( $tag_werte[$i] != "" ) {
            $ausgaben["tagwerte".$i] = $tag_werte[$i];
        } else {
            $ausgaben["tagwerte".$i] = "";
        }
    }

    // preview-bild holen
    $pic_info = str_replace($cfg["file"]["base"]["webdir"],"",$ausgaben["tagwerte0"]);
    $pic_array = explode("/",str_replace($cfg["file"]["base"]["webdir"],"",$tag_werte[0]));
    // unterscheidung zwischen realname und alter bildpfad-angabe
    if ( is_numeric($pic_array[1]) ) {
        $file_id = $pic_array[1];
        $file_size = $pic_array[2];
    } else {
        $file_id = substr($pic_array[2],(strpos($pic_array[2],"_")+1) );
        $file_id = substr($file_id,0,strpos($file_id,".") );
        $file_size = array_search($pic_array[1]."/", $cfg["file"]["base"]["pic"]);
    }
    if ( is_array($_SESSION["file_memo"])
      || $pic_array[1] != "" ) {
        // ggf session ergaenzen
        if ( !is_array($_SESSION["file_memo"]) || !in_array($file_id,$_SESSION["file_memo"]) ) {
            $_SESSION["file_memo"][$file_id] = $file_id;
        }
        $sql = "SELECT *
                  FROM site_file
                 WHERE fid IN (".implode(",",$_SESSION["file_memo"]).")";
        $result = $db -> query($sql);
            $hidedata["imgmulti"] = array();
            while ( $data = $db -> fetch_array($result,1) ) {
                $src =  $cfg["file"]["base"]["webdir"].
                        $data["ffart"]."/".
                        $data["fid"]."/".
                        "tn/".
                        $data["ffname"];
                $checked = "";
                if ( $_POST["tagwerte"] != "" ) {
                    if ( $src == $_POST["tagwerte"][0] ) {
                        $checked = " checked=\"checked\"";
                        $selected_fid = $data["fid"];
                    }
                } else {
                    if ( (count($_SESSION["file_memo"]) == 1 && in_array($data["fid"],$_SESSION["file_memo"]))
                    || (count($_SESSION["file_memo"]) > 1 && $data["fid"] == $file_id)
                    || (count($_SESSION["file_memo"]) == 0) ) {
                        $checked = " checked=\"checked\"";
                        $selected_fid = $data["fid"];
                    }
                }
                $dataloop["imgmulti"][] = array(
                         "id" => $data["fid"],
                        "src" => $src,
                     "funder" => $data["funder"],
                      "fdesc" => $data["fdesc"],
                    "checked" => $checked,
                );
            }
    }

    // anzeigen-groesse-radiobutton
    if ( count($cfg["wizard"]["img_edit"]["cb_show_size"]) >0 ) {
        foreach ( $cfg["wizard"]["img_edit"]["cb_show_size"] as $value=>$label ) {
            $check = "";
            if ( $value == $file_size ) $check = " checked=\"checked\"";
            $dataloop["show"][] = array(
                "value" => $value,
                "label" => "#(".$label.")",
                "check" => $check,
            );
        }
    } else {
        $dataloop["show"][] = array(
            "value" => $file_size,
            "label" => "not changeable",
            "check" => " checked=\"checked\"",
        );
    }

    // align-radiobutton
    if ( count($cfg["wizard"]["img_edit"]["cb_align"]) >0 ) {
        foreach ( $cfg["wizard"]["img_edit"]["cb_align"] as $value=>$label ) {
            $check = "";
            if ( $ausgaben["tagwerte1"] == $value ) $check = " checked=\"checked\"";
            $dataloop["align"][] = array(
                "value" => $value,
                "label" => "#(".$label.")",
                "check" => $check,
            );
        }
    } else {
        $dataloop["align"][] = array(
            "value" => $ausgaben["tagwerte1"],
            "label" => "not changeable",
            "check" => " checked=\"checked\"",
        );
    }

    // size-radiobutton
    if ( count($cfg["wizard"]["img_edit"]["cb_link_size"]) > 0 ) {
        foreach ( $cfg["wizard"]["img_edit"]["cb_link_size"] as $value=>$label ) {
            $check = "";
            if ( $ausgaben["tagwerte3"] == $value ) $check = " checked=\"checked\"";
            $dataloop["size"][] = array(
                "value" => $value,
                "label" => "#(".$label.")",
                "check" => $check,
            );
        }
    } else {
        $dataloop["size"][] = array(
            "value" => $ausgaben["tagwerte3"],
            "label" => "not changeable",
            "check" => " checked=\"checked\"",
        );
    }


    // abspeichern, part 2
    // * * *
    if ( $environment["parameter"][7] == "verify"
        &&  ( $_POST["send"] != ""
            || $_POST["add"] != ""
            || $_POST["sel"] != ""
            || $_POST["refresh"] != ""
            || $_POST["upload"] != ""
            || $_POST["uploaded"] != ""
            || $_POST["change_pic"] != "" ) ) {

        // ggf bild einfuegen
        $error = file_validate($_FILES["new_file"]["tmp_name"], $_FILES["new_file"]["size"], $cfg["file"]["filesize"], $cfg["file"]["filetyp"], "new_file");
        if ( $error == 0 ) {
            $newname = $cfg["file"]["base"]["maindir"].$cfg["file"]["base"]["new"].$_SESSION["uid"]."_".$_FILES["new_file"]["name"];
            rename($_FILES["new_file"]["tmp_name"],$newname);
        }

        // einzubauender content
        $tag_werte = array();
        for ($i = 0; $i <= 6; $i++) {
            if ( $i == 0 ) {
                $tag_werte[] = str_replace("/tn/","/".$_POST["pic_size"]."/",$_POST["tagwerte"][$i]);
            } else {
                $tag_werte[] = $_POST["tagwerte"][$i];
            }
        }
        $tag = str_replace( array("[","/","]"),"",$tag_meat[$tag_marken[0]][$tag_marken[1]]["tag_end"] );
        $to_insert = "[".$tag."=".implode(";",$tag_werte)."]".$_POST["description"]."[/".$tag."]";

        if ( !is_array($_POST["change_pic"]) && !is_array($_POST["add"]) ) unset($_SESSION["file_memo"]);

    }
    // + + +

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>