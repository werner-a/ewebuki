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

    function priv_check($url,$required) {
        if ( !function_exists(priv_check_path) ) {
            function priv_check_path($url,$required,&$hit) {
                if ( is_array($_SESSION["content"] ) ){
                    if ( strpos($_SESSION["content"][$url],$required) !== False ) {
                        $hit = True;
                    }
                }
                if ( $url != "/" ) {
                    $url = dirname($url);
                    priv_check_path($url,$required,$hit);
                }
            }
        }
        $hit = "";
        priv_check_path($url,$required,$hit);
        return $hit;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
