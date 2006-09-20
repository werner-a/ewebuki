<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id$";
  $Script["desc"] = "rechte in bereichen pruefen";
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

    // aufruf: $berechtigt_in = right_check( $art, $ebene, $kategorie );
    // $art = 0 - artikel
    // $art = 1 - content

    function right_check($art, $ebene, $kategorie="",$database=DATABASE) {
        global $_SESSION,$db;

        $url = explode("/", $ebene."/".$kategorie);
        foreach ($url as $key => $value) {
            if ( $key > 0 ) $trenner = "/";
            $chkurl .= $trenner.$value;
            if ( $url[$key+1] == "" ) break;
            $stname = crc32($chkurl).".".$url[$key+1];
            if ( is_array($_SESSION["katzugriff"]) ) {
                if ( in_array($art.":".$database.":".$stname,$_SESSION["katzugriff"]) ) {
                    $berechtigt = $stname;
                    break;
                }
            }
        }
        return $berechtigt;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
