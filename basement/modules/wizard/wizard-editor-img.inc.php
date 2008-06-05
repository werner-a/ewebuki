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

    // was anzeigen
    $mapping["main"] = "wizard-edit";
    $hidedata["img"] = array();

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
    $pic_array = explode("/",$pic_info);
    if ( is_array($_SESSION["file_memo"]) || $pic_array[1] != "" ) {
        if ( is_array($_SESSION["file_memo"]) ) {
            $fid = current($_SESSION["file_memo"]);
        } else {
            $fid = $pic_array[1];
        }
        $sql = "SELECT *
                  FROM site_file
                 WHERE fid=".$fid;
        $result = $db -> query($sql);
        if ( $db -> num_rows($result) == 1 ) {
            $data = $db -> fetch_array($result);
            $hidedata["imgpreview"] = array(
                       "src" => $cfg["file"]["base"]["webdir"].
                                $data["ffart"]."/".
                                $fid."/s/".
                                $data["ffname"],
            );
            $target_src = $cfg["file"]["base"]["webdir"].
                            $data["ffart"]."/".
                            $fid."/".
                            $pic_array[count($pic_array)-2]."/".
                            $data["ffname"];
            // falls noch keine bildbeschriftung vorhanden ist, bildunterschrift einsetzen
            if ( is_array($_SESSION["file_memo"]) && $hidedata["img"]["description"] == "" ) $hidedata["img"]["description"] = $data["funder"];
            if ( is_array($_SESSION["file_memo"]) && $hidedata["img"]["meat"] == "" ) $hidedata["img"]["meat"] = $data["funder"];
        }
        unset($_SESSION["file_memo"]);
    }

    // anzeigen-groesse-radiobutton
    if ( count($cfg["wizard"]["img_edit"]["cb_show_size"]) >0 ) {
        foreach ( $cfg["wizard"]["img_edit"]["cb_show_size"] as $value=>$label ) {
            $pic_url = $cfg["file"]["base"]["webdir"].$data["ffart"]."/".$fid."/".$value."/".$data["ffname"];
            $check = "";
            if ( strstr($ausgaben["tagwerte0"],"/".$value."/") ) $check = " checked=\"checked\"";
            $dataloop["show"][] = array(
                "value" => $pic_url,
                "label" => "#(".$label.")",
                "check" => $check,
            );
        }
    } else {
        $dataloop["show"][] = array(
            "value" => $target_src,
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
            || $_POST["upload"] != "" ) ) {

        // ggf bild einfuegen
        $error = file_validate($_FILES["new_file"]["tmp_name"], $_FILES["new_file"]["size"], $cfg["file"]["filesize"], $cfg["file"]["filetyp"], "new_file");
        if ( $error == 0 ) {
            $newname = $cfg["file"]["base"]["maindir"].$cfg["file"]["base"]["new"].$_SESSION["uid"]."_".$_FILES["new_file"]["name"];
            rename($_FILES["new_file"]["tmp_name"],$newname);
        }

        // einzubauender content
        $tag_werte = array();
        for ($i = 0; $i <= 6; $i++) {
            $tag_werte[] = $_POST["tagwerte"][$i];
        }
        $to_insert = "[IMG=".implode(";",$tag_werte)."]".$_POST["description"]."[/IMG]";

    }
    // + + +

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>