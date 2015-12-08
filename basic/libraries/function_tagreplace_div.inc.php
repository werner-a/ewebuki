<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// function_tagreplace_div.inc.php v1 chaot
// tagreplace "div" funktion
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

    function tagreplace_div($replace, $opentag, $tagoriginal, $closetag, $sign) {

        global $cfg, $defaults, $specialvars;

        $tagwert = $tagoriginal;
        // ------------------------------

        if ( $specialvars["newbrmode"] == True && strpos( $specialvars["newbrblock"], "DIV") === false ) $tagwert = nlreplace($tagwert);
        if ( $sign == "]" ) {
            $ausgabewert = "<div>".$tagwert."</div>";
            $replace = str_replace($opentag.$tagoriginal.$closetag,$ausgabewert,$replace);
        } else {
            $tagwerte = explode("]",$tagwert,2);
            $divwerte = explode(";",$tagwerte[0]);
            $extrawerte = explode(":",$divwerte[0]);
            if ( isset($extrawerte[1]) ) $divwerte[0] = $extrawerte[1];
            if ( $extrawerte[0] == "id" ) {
                $art = "id";
            } else {
                $art = "class";
            }
            $attrib = null;
            if ( isset($divwerte[0]) ) {
                $attrib = " ".$art."=\"".$divwerte[0]."\"";
            }
            $replace = str_replace($opentag.$tagoriginal.$closetag,"<div".$attrib.">".$tagwerte[1]."</div>",$replace);
        }

        // ------------------------------
        return $replace;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
