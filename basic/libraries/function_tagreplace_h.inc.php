<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// function_tagreplace_h.inc.php v1 chaot
// tagreplace "h" funktion
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

    function tagreplace_h($replace, $opentag, $tagoriginal, $closetag, $sign) {

        global $defaults ,$specialvars;

        $tagwert = $tagoriginal;
        // ------------------------------            

        $newtag = strtolower(substr($opentag,1,2));
        if ( $specialvars["newbrmode"] == True ) $tagwert = nlreplace($tagwert);
        if ( !isset($defaults["tag"][$newtag]) ) {
          $defaults["tag"][$newtag] = "<".$newtag.">";
          $defaults["tag"]["/".$newtag] = "</".$newtag.">";
        }
        if ( $sign == "]" ) {
            $replace = str_replace($opentag.$tagoriginal.$closetag,$defaults["tag"][$newtag].$tagwert.$defaults["tag"]["/".$newtag],$replace);
        } else {
            $tagwerte = explode("]",$tagwert,2);
            $attrib = " class=\"".$tagwerte[0]."\">";
            $tagwithclass = str_replace(">", $attrib, $defaults["tag"][$newtag]);
            $replace = str_replace($opentag.$tagoriginal.$closetag,$tagwithclass.$tagwerte[1].$defaults["tag"]["/".$newtag],$replace);
        }

        // ------------------------------
        return $replace;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
