<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "short description";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001, 2002, 2003 Werner Ammon <wa@chaos.de>

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

    echo "<pre>";
    print_r($HTTP_POST_VARS);
    echo "</pre>";
    session_register("images_memo");
    $HTTP_SESSION_VARS["images_memo"] = $HTTP_POST_VARS["checkbox"];

    foreach ( $HTTP_POST_VARS["checkbox"] as $value ) {
        $ausgaben["output"] .= $value.", ";
    }

    session_register("return");
    session_register("referer");
    $ausgaben["output"] .= "<br><br><br>";
    $ausgaben["output"] .= "<a href=\"".$HTTP_SESSION_VARS["return"]."?referer=".$HTTP_SESSION_VARS["referer"]."\">Zum Artikel</a>";
    unset ($HTTP_SESSION_VARS["return"]);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
