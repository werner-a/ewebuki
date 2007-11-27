<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id$";
  $Script["desc"] = "rekursive pruefung der rechte";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2006 Werner Ammon ( wa<at>chaos.de )

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

    // aufruf: $priv_check(ebene,kategorie,database,$right);
    // funktion prueft rekursiv, ob die aktuelle url rechte in der $_SESSION["content"] besitzt !

    function priv_check($ebene, $kategorie="",$database=DATABASE,&$RightConcept) {
        if ( is_array($_SESSION["content"] ) ){
                if ( array_key_exists(crc32($ebene).".".$kategorie,$_SESSION["content"]) ) {
                    $check = crc32($ebene).".".$kategorie;

                foreach ( $_SESSION["content"][$check] as $key => $value ) {
                        $RightConcept[$key][$check] = "on" ;
                    }
                }

                if ( !$RightConcept && $kategorie != "index") {
                    $newebene = substr($ebene,0,strrpos($ebene,"/"));
                    $newkat = substr($ebene,strrpos($ebene,"/")+1);
                    if ( $ebene == "" ) $newkat = "index";
                    priv_check($newebene,$newkat,"",$RightConcept);
                }
        }
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
