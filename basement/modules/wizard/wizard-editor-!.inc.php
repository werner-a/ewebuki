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
    include $pathvars["moduleroot"]."libraries/function_calendar.inc.php";


    // ausgabenwerte werden belegt
    if ( $environment["parameter"][9] != "" && $environment["parameter"][8] != "" && $environment["parameter"][7] != "" ) {
        ( strlen($environment["parameter"][9]) == 1 ) ? $hidedata["termine"]["day"] = "0".$environment["parameter"][9] : $hidedata["termine"]["day"] = $environment["parameter"][9];
        ( strlen($environment["parameter"][8]) == 1 ) ? $hidedata["termine"]["month"] = "0".$environment["parameter"][8] : $hidedata["termine"]["month"] = $environment["parameter"][8];
        $hidedata["termine"]["year"] = $environment["parameter"][7];
    } else {
        $hidedata["termine"]["day"] = substr($tag_meat["SORT"][0]["meat"],8,2);
        $hidedata["termine"]["month"] = substr($tag_meat["SORT"][0]["meat"],5,2);
        $hidedata["termine"]["year"] = substr($tag_meat["SORT"][0]["meat"],0,4);
    }



    $ausgaben["calendar"] = calendar($hidedata["termine"]["month"],$hidedata["termin"]["year"],"cal_termine","-1","-1",-1,6);

    $hidedata["termine"]["date"] = $hidedata["termine"]["day"].".".$hidedata["termine"]["month"].".".$hidedata["termine"]["year"];

echo "<pre>";
print_r($tag_meat["!"][0]["complete"]);
echo "</pre>";
$tag_meat["!"][0]["complete"] = $tag_meat["!"][0]["complete"];


// echo "<pre>";
// print_r($temp);
// echo "</pre>";
//     foreach ( $temp as $key => $value ) {
//         echo $value["id"];
//         $dataloop["wizardlist"][$key]["titel_org"] = $value["titel_org"];
//         $dataloop["wizardlist"][$key]["faq"] = $value["faq"];
//         $dataloop["wizardlist"][$key]["id"] = $value["id"];
//         $dataloop["wizardlist"][$key]["url"] = "editor,".$environment["parameter"][1].",".eCrc("/service/fragen").".";
//         $dataloop["wizardlist"][$key]["tag"] = $faq_tag;
//         $dataloop["wizardlist"][$key]["tag1"] = $question_tag;
//     }
#echo $tag_meat[$tag_marken[0]][$tag_marken[1]]["tag_start"];
#echo tname2path($environment["parameter"][2]);
    if ( $_POST["send"]  ) {
        $to_insert = $tag_meat["!"][0]["complete"];
    }
#echo $environment["parameter"][2];
 #echo tname2path($environment["parameter"][2]);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>