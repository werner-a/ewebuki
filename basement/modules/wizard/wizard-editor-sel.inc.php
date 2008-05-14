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
    $hidedata["sel"] = array();

    // ausgabenwerte werden belegt
    $ausgaben["description"] = $tag_meat[$tag_marken[0]][$tag_marken[1]]["meat"];
    $tag_werte = explode(";",str_replace(array("[SEL=","]"),"",$tag_meat[$tag_marken[0]][$tag_marken[1]]["tag_start"]));
    for ($i=0;$i<=4;$i++) {
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
    if ( $ausgaben["tagwerte2"] != "" ) $hidedata["sel"]["check_thumb"] = " checked=\"true\"";
    if ( $ausgaben["tagwerte4"] != "" ) $hidedata["sel"]["check_lbox"] = " checked=\"true\"";
    // selection aus session/tag holen
    if ( is_array($_SESSION["compilation_memo"]) || $tag_werte[3] != "" ) {
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
        filelist($result, "fileed");

        foreach ( $dataloop["list_images"] as $key=>$value ) {
            if ( in_array($key,$array) ) {
                $dataloop["list_images"][$key]["checked"] = " checked=\"true\"";
            } else {
                $dataloop["list_images"][$key]["checked"] = "";
            }
        }
    }


    // abspeichern, part 2
    // * * *
    if ( $environment["parameter"][7] == "verify"
        &&  ( $_POST["send"] != ""
            || $_POST["add"] != ""
            || $_POST["sel"] != ""
            || $_POST["refresh"] != ""
            || $_POST["upload"] != "" ) ) {

        if ( is_array($_POST["tagwerte"][3]) ) $_POST["tagwerte"][3] = implode(":",$_POST["tagwerte"][3]);
        $tag_werte = array();
        for ($i = 0; $i <= 4; $i++) {
            $tag_werte[] = $_POST["tagwerte"][$i];
        }
        $to_insert = "[SEL=".implode(";",$tag_werte)."]".$_POST["description"]."[/SEL]";

    }
    // + + +

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>