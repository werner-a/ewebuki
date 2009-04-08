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

    $day = substr($tag_meat["SORT"][0]["meat"],8,2);
    $month = substr($tag_meat["SORT"][0]["meat"],5,2);
    $year = substr($tag_meat["SORT"][0]["meat"],0,4);

    $ausgaben["calendar"] = "<input readonly=true style=\"float:left\" type=\"text\" id=\"date1\" name=\"SORT\" value=\"".$day.".".$month.".".$year."\"><button class=\"button\" style=\"font-size:0.6em;margin-left:5px;\" id=\"trigger1\">...</button>";
    $ausgaben["calendar"] .= "<div class=\"clear\"></div>";

    $hidedata["terminecal"]["on"] = "on";

    // abspeichern
    // * * *

    if ( $_POST["send"] && $_POST["SORT"] != ""  ) {
        $to_insert = "[SORT]".substr($_POST["SORT"],6,4)."-".substr($_POST["SORT"],3,2)."-".substr($_POST["SORT"],0,2)."[/SORT]";
    }
    // + + +

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>