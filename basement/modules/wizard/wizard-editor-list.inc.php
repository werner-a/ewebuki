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
    $hidedata["list"]["new_point"] = "#(new_list)";

    // liste oder faq
    $pos = strpos($environment["parameter"][4],":");
    $list_id = substr($environment["parameter"][4],$pos+1);
    $art = "normal";
    if ( preg_match("/DEF/",$tag_meat["LIST"][$list_id]["tag_start"]) ){
        $hidedata["list"]["new_point"] = "#(new_faq)";
        $art = "def";
    }

    if ( $_POST["del"] ) {

        if ( ( $art != "def" && count($_POST["areas"]) > 1 ) || ( $art == "def" && count($_POST["areas"]) > 2 ) ) {
            $to_del = array_keys($_POST["del"]);
            unset($_POST["areas"][$to_del[0]]);
        }
    }

    if ( count($_POST) > 0 ) {
        foreach ( $_POST["areas"] as $key => $value ) {
            $buffer[$key] = $value;
        }
        if ( $_POST["new_line"] ) {
            if ( $art == "def" ) {
                $buffer[] = "Frage";
                $buffer[] = "Antwort";
            } else {
                $buffer[] = "Listeneintrag";
            }
        }
    } else {
        $buffer = explode("[*]",$form_values["content"]);
    }

    $ausgaben["inhalt"] = "";
    foreach ( $buffer as $key => $value ) {
        if ( $art == "def"  ) { 
            if ( $key % 2 == 0 ) {
                if ( (preg_match("/^\[DIV.*\/DIV\]$/is",$buffer[$key+1]) || $_POST[$key+1] == $key+1) ) {
                    $dataloop["faq"][$key]["checked"] = "checked";
                    $buffer[$key+1] = str_replace("[DIV]","",$buffer[$key+1]);
                    $buffer[$key+1] = str_replace("[/DIV]","",$buffer[$key+1]);
                }
                $buffer[$key+1] = str_replace("[/DIV][DIV]","[/DIV]\n[DIV]",$buffer[$key+1] );
            } else {
                continue;
            }

            $dataloop["faq"][$key]["answer"] = $buffer[$key+1];
            $dataloop["faq"][$key]["question"] = $value;
            $dataloop["faq"][$key]["count"] = $key;
            $dataloop["faq"][$key]["count1"] = $key+1;
            $dataloop["faq"][$key]["del"] = "<button type=\"submit\" name=\"del[".$key."]\" value=\"#(delete)\" title=\"#(delete)\" class=\"button\">#(delete)</button>";
        } else {
            $dataloop["list"][$key]["del"] = "<button type=\"submit\" name=\"del[".$key."]\" style=\"margin-left:5px;float:right\" value=\"#(delete)\" title=\"#(delete)\" class=\"button\">#(delete)</button>";
            $dataloop["list"][$key]["inhalt"] = $value;
            $dataloop["list"][$key]["count"] = $key;
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

        $buffer = "";
        foreach ( $_POST["areas"] as $key => $value ) {
            if ( $key % 2 == 1 && $_POST[$key] == $key) {
                $list_display = preg_split("/(".chr(10).")/",$value,-1,PREG_SPLIT_NO_EMPTY);
                $e = "";
                foreach ( $list_display as $test ) {
                    $e .= "[DIV]".$test."[/DIV]";
                }
                $value = $e;

            }
            $value = preg_replace("/\[\/DIV\][\s]?/","[/DIV]",$value);
            $ende[] = $value;
        }

        foreach ( $ende as $key => $value ) {
            $trenner = "[*]";
            if ( $key == 0 ) $trenner = "";
            $list_buffer .= $trenner.$value;
        }

        $list_buffer = preg_replace("/^\[\*\]/","",$list_buffer);

        // verbotenen tags rausfiltern
        $buffer = array();
        foreach ( $allowed_tags as $value ) {
            $buffer[] = "[/".strtoupper($value)."]";
        }

        $to_insert = $tag_meat[$tag_marken[0]][$tag_marken[1]]["tag_start"].
                        tagremove($list_buffer,False,$buffer).
                        $tag_meat[$tag_marken[0]][$tag_marken[1]]["tag_end"];
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>