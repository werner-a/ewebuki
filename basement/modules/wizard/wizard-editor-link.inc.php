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
    $hidedata["link"] = array();
    $hidedata["link"]["num"] = $tag_marken[1] + 1;

    // ausgabenwerte werden belegt
    $ausgaben["description"] = $tag_meat[$tag_marken[0]][$tag_marken[1]]["meat"];
    $tag_werte = explode(";",str_replace(array("[LINK=","]"),"",$tag_meat[$tag_marken[0]][$tag_marken[1]]["tag_start"]));
    for ($i=0;$i<=2;$i++) {
        $ausgaben["tagwerte".$i] = $tag_werte[$i];
    }
    // link-target
    if ( count($cfg["wizard"]["link_edit"]["cb_target"]) > 0 ) {
        foreach ( $cfg["wizard"]["link_edit"]["cb_target"] as $value=>$label ) {
            $check = "";
            if ( $ausgaben["tagwerte1"] == $value ) $check = " checked=\"checked\"";
            $dataloop["target"][] = array(
                "value" => $value,
                "label" => "#(".$label.")",
                "check" => $check,
            );
        }
    } else {
        $dataloop["target"][] = array(
            "value" => $ausgaben["tagwerte1"],
            "label" => "not changeable",
            "check" => " checked=\"checked\"",
        );
    }

    if ( is_array($_SESSION["file_memo"]) ) {
        $dataloop["fileed"][] = array(
            "label" => $tag_werte[0],
             "link" => $tag_werte[0],
              "sel" => " checked=\"checked\"",
        );
        $hidedata["fileed"] = array();
        $sql = "SELECT * FROM site_file WHERE fid IN (".implode(",",$_SESSION["file_memo"]).")";
        $result = $db -> query($sql);
        while ( $data = $db -> fetch_array($result,1) ) {
            if ( $cfg["file"]["filetyp"][$data["ffart"]] != "arc" && $cfg["file"]["filetyp"][$data["ffart"]] != "pdf" && $cfg["file"]["filetyp"][$data["ffart"]] != "odf" ) {
                continue;
            }
            $link = $cfg["file"]["base"]["webdir"].
                    $data["ffart"]."/".
                    $data["fid"]."/".
                    $data["ffname"];
            $check = "";
            if ( $link == $tag_werte[0] ) {
                $check = " checked=\"checked\"";
                array_shift($dataloop["fileed"]);
            }
            $dataloop["fileed"][] = array(
                "label" => $data["ffname"],
                 "link" => $link,
                  "sel" => $check,
            );
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
        if ( $_POST["tagwerte_memo"] != "" ) $_POST["tagwerte"][0] = $_POST["tagwerte_memo"];
        $tag_werte = array();
        for ($i = 0; $i <= 2; $i++) {
            $tag_werte[] = $_POST["tagwerte"][$i];
        }
        $to_insert = "[LINK=".implode(";",$tag_werte)."]".$_POST["description"]."[/LINK]";
echo "<pre>".print_r($_POST,true)."</pre>";
// die($to_insert);

        if ( !is_array($_POST["add"]) ) unset($_SESSION["file_memo"]);

    }
    // + + +

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>