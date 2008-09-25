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
    $hidedata["sort"] = array();

    include $pathvars["moduleroot"]."libraries/function_calendar.inc.php";

    $ausgaben["calendar"] = calendar("","","cal_termine","-1","-1");

    // ausgabenwerte werden belegt
    if ( $_GET["day"] != "" && $_GET["month"] != "" && $_GET["year"] != "" ) {
        ( strlen($_GET["day"]) == 1 ) ? $hidedata["sort"]["day"] = "0".$_GET["day"] : $hidedata["sort"]["day"] = $_GET["day"];
        ( strlen($_GET["month"]) == 1 ) ? $hidedata["sort"]["month"] = "0".$_GET["month"] : $hidedata["sort"]["month"] = $_GET["month"];
        $hidedata["sort"]["year"] = $_GET["year"];
    } elseif ( $_GET["day"] == "" && $_GET["month"] != "" && $_GET["year"] != "" ) {
        $hidedata["sort"]["day"] = "01";
        ( strlen($_GET["month"]) == 1 ) ? $hidedata["sort"]["month"] = "0".$_GET["month"] : $hidedata["sort"]["month"] = $_GET["month"];
        $hidedata["sort"]["year"] = $_GET["year"];
    } else {
        $hidedata["sort"]["day"] = date('d');
        $hidedata["sort"]["month"] = date('m');
        $hidedata["sort"]["year"] = date('Y');
    }

    $hidedata["sort"]["date"] = $hidedata["sort"]["day"].".".$hidedata["sort"]["month"].".".$hidedata["sort"]["year"];

    // abspeichern
    // * * *

    if ( $_POST["send"] && $_POST["year"] != "" && $_POST["month"] != "" && $_POST["day"] != "" ) {
        $to_insert = "[SORT]".$_POST["year"]."-".$_POST["month"]."-".$_POST["day"]."[/SORT]";
    }
    // + + +

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>