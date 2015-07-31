<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// wizard-editor-yt.inc.php v1 emnili/krompi
// wizard - editor-yt funktion
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

    // was anzeigen
    $mapping["main"] = "wizard-edit";
    $hidedata["youtube"] = array();


    $ausgaben["radio-right"] = "";
    $ausgaben["radio-left"] = "checked";

    $ausgaben["yurl"] = $tag_meat[$tag_marken[0]][$tag_marken[1]]["meat"];
    $ausgaben["height"] = "";
    $complete = $tag_meat[$tag_marken[0]][$tag_marken[1]]["complete"];
    $tag_werte = explode(";",str_replace(array("[YT=","]"),"",$tag_meat[$tag_marken[0]][$tag_marken[1]]["tag_start"]));

    if ( $tag_werte[0] == "r") {
        $ausgaben["radio-left"] = "";
        $ausgaben["radio-right"] = "checked";
    }
    $ausgaben["info"] = "";
    if ( $tag_werte[4] == "-1") {
        $ausgaben["info"] = "checked";
    }

    $ausgaben["align"] = $tag_werte[0];
    $ausgaben["width"] = $tag_werte[1];
    $ausgaben["height"] = $tag_werte[2];

    // abspeichern
    // * * *

    if ( $_POST["send"] ) {
        $to_insert = "[YT=".$_POST["align"].";".$_POST["width"].";".$_POST["height"].";".$tag_werte[3].";".$_POST["info"]."]".$_POST["yurl"]."[/YT]";
    }
    // + + +

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
