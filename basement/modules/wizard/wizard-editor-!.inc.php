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

    $hidedata["terminecal"]["on"] = "on";
    $hidedata["terminecal1"]["on"] = "on";
    $ausgaben["error"] = "";
    $ausgaben["checked"] = "";
    $hidedata["termine"]["on"] = "ON";

    $preg = "/\[([_A-Z]*)\](.*)\[\/[_A-Z]*\]/Us";
    if  ( $_GET["_NAME"] ) $ausgaben["error"] = "Beginn und Ende-Datum sind nicht korrekt";
    preg_match_all($preg,$tag_meat["!"][0]["complete"],$regs);
    foreach ( $regs[1] as $key => $value ) {
        if ( $value == "KATEGORIE" ) continue;
        if  ( $_GET[$value] ) {
            $regs[2][$key] = $_GET[$value];
        }
        $hidedata["termine"][$value] = $regs[2][$key];
        $$value = $regs[2][$key];
        if ( $value == "_TERMIN" && $regs[2][$key] != "1970-01-01" ) $ausgaben["checked"] = "checked";
        if ( $_POST["send"] ) {
            if ( $value == "_TERMIN" || $value == "SORT" ) {
                $_POST[$value] = substr($_POST[$value],6,4)."-".substr($_POST[$value],3,2)."-".substr($_POST[$value],0,2);
            }
            $tag_meat["!"][0]["complete"] = preg_replace("/\[".$value."\]".$$value."\[\/".$value."\]/","[".$value."]".$_POST[$value]."[/".$value."]",$tag_meat["!"][0]["complete"]);
        }
    }
    $SORT = substr($SORT,0,10);

    $SORT = substr($SORT,8,2).".".substr($SORT,5,2).".".substr($SORT,0,4);
    $_TERMIN = substr($_TERMIN,8,2).".".substr($_TERMIN,5,2).".".substr($_TERMIN,0,4);

    if ( $_TERMIN != "01.01.1970" ) {
        $display = "";
    } else {
        $display = "none";
    }
    $ausgaben["begin"] = "<input readonly=true style=\"float:left\" type=\"text\" id=\"date1\" name=\"SORT\" value=\"".$SORT."\"><button onClick=\"alert('hi')\" class=\"button\" style=\"font-size:0.6em;margin-left:5px;\" id=\"trigger1\">...</button>";
    $ausgaben["begin"] .= "<div class=\"clear\"></div>";
    $ausgaben["ende"]  = "<div style=\"display:".$display."\" id=\"end_termin\"><input readonly=true style=\"float:left\" type=\"text\" id=\"date2\" name=\"_TERMIN\" value=\"".$_TERMIN."\"> <button  class=\"button\" style=\"font-size:0.6em;margin-left:5px;\" id=\"trigger2\">...</button></div>";
    $ausgaben["ende"] .= "<div class=\"clear\"></div>";

    if ( $_POST["send"]  ) {
        if ( $_POST["_TERMIN"] != "1970-01-01" && mktime(0,0,0,substr($_POST["_TERMIN"],5,2),substr($_POST["_TERMIN"],8,2),substr($_POST["_TERMIN"],0,4)) <= mktime(0,0,0,substr($_POST["SORT"],5,2),substr($_POST["SORT"],8,2),substr($_POST["SORT"],0,4)) ) {
            echo "Beginn und Ende-Datum sind nicht korrekt";
        if ( $_POST["send"][0] == "Abschicken" ) {
            header("Location: ".$_SESSION["page"]."?_NAME=".$_POST["_NAME"]."&_VERANSTALTER=".$_POST["_VERANSTALTER"]."&_ORT=".$_POST["_ORT"]."&_BESCHREIBUNG=".urlencode($_POST["_BESCHREIBUNG"]));
            exit;
            }
        }
        $to_insert = $tag_meat["!"][0]["complete"];
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>