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

    $hidedata["termine"]["on"] = "ON";

    $einsatz = "2008-01-01";
    $preg = "/\[([A-Z]*)\](.*)\[\/[A-Z]*\]/Us";

    preg_match_all($preg,$tag_meat["!"][0]["complete"],$regs);
    foreach ( $regs[1] as $key => $value ) {
        if ( $value == "KATEGORIE" ) continue;
        $hidedata["termine"][$value] = $regs[2][$key];
        $$value = $regs[2][$key];
        if ( $_POST["send"]  ) {
// echo $value.$$value."<br>";
#echo "[".$value."]".$_POST[$value]."[/".$value."]<br>";
            $tag_meat["!"][0]["complete"] = preg_replace("/\[".$value."\]".$$value."\[\/".$value."\]/","[".$value."]".$_POST[$value]."[/".$value."]",$tag_meat["!"][0]["complete"]);
// echo $tet."<br>";
        }
    }
    $SORT = substr($SORT,0,10);
//     echo $TERMIN;
    $ausgaben["begin"] = "<script>DateInput('SORT', 'true', 'YYYY-MM-DD', '$SORT' )</script>";
    $ausgaben["ende"] = "<script>DateInput('TERMIN', 'true', 'YYYY-MM-DD', '$TERMIN' )</script>";



// echo "<pre>";
// print_r($tag_meat["!"][0]["complete"]);
// print_r($hidedata["termine"]);
// echo "</pre>";
$tag_meat["!"][0]["complete"] = $tag_meat["!"][0]["complete"];


    if ( $_POST["send"]  ) {

// echo "<pre>";
// print_r($_POST);
// print_r($tet);
// print_r($tag_meat["!"][0]["complete"]);
// print_r($hidedata["termine"]);
// echo "</pre>";
// exit;
        $to_insert = $tag_meat["!"][0]["complete"];
    }
#echo $environment["parameter"][2];
 #echo tname2path($environment["parameter"][2]);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>