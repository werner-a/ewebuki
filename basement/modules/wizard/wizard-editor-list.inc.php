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
    $hidedata["list"] = array();

    $hidedata["default"] = array();
    $hidedata["list"] = array();
    $buffer = explode("[*]",$form_values["content"]);
    $ausgaben["inhalt"] = "";
    foreach ( $buffer as $value ) {
        if ( $ausgaben["inhalt"] != "" ) $ausgaben["inhalt"] .= chr(13).chr(10).chr(13).chr(10);
        $ausgaben["inhalt"] .= trim($value);
    }


    // abspeichern, part 2
    // * * *
    if ( $environment["parameter"][7] == "verify"
        &&  ( $_POST["send"] != ""
            || $_POST["add"] != ""
            || $_POST["sel"] != ""
            || $_POST["refresh"] != ""
            || $_POST["upload"] != "" ) ) {

        // trennen nach leerzeilen
        $buffer = preg_split("/(".chr(13).chr(10)."){2}/",$_POST["content"],-1,PREG_SPLIT_NO_EMPTY);
        $to_insert = implode("\n[*]",$buffer);
        // verbotenen tags rausfiltern
        $buffer = array();
        foreach ( $allowed_tags as $value ) {
            $buffer[] = "[/".strtoupper($value)."]";
        }
        $to_insert = $tag_meat[$tag_marken[0]][$tag_marken[1]]["tag_start"].
                        tagremove($to_insert,False,$buffer).
                        $tag_meat[$tag_marken[0]][$tag_marken[1]]["tag_end"];

    }
    // + + +

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>