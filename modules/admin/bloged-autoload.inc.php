<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// bloged-autoload.inc.php v1 emnili
// bloged - automatic loader
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2015 Werner Ammon <wa@chaos.de>

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

    // blogs werden automatisch eingebunden, eigene eintraege in der modules.cfg nicht mehr noetig
    foreach ( $cfg["bloged"]["blogs"] as $key => $value ) {
        $blogs = explode("/",substr($key,1));
        $blog_kategorie = array_pop($blogs);

        if ( count($blogs) > 0 ) {
            $blog_ebene = "/".implode("/",$blogs);
        } else {
            $blog_ebene = "";
        }

        if ( $environment["ebene"] == $blog_ebene && $environment["kategorie"] == $blog_kategorie ) {
            require_once $pathvars["moduleroot"]."libraries/function_menu_convert.inc.php";
            require_once $pathvars["moduleroot"]."libraries/function_show_blog.inc.php";

            // erstellen der tags die angezeigt werden
            if ( !isset($cfg["bloged"]["blogs"][$key]["addons"]) ) { $cfg["bloged"]["blogs"][$key]["addons"] = null;}
            if ( is_array($cfg["bloged"]["blogs"][$key]["addons"]) ) {
                foreach ( $cfg["bloged"]["blogs"][$key]["addons"] as $key_tag => $value) {
                    $tags[$key_tag] = $value;
                }
            }

            // erstellen der tags die angezeigt werden
            if ( is_array($cfg["bloged"]["blogs"][$key]["tags"]) ) {
                foreach ( $cfg["bloged"]["blogs"][$key]["tags"] as $key_tag => $value) {
                    $tags[$key_tag] = $value;
                }
            }
            $show_kat = "";
            if ( isset($cfg["bloged"]["blogs"][$key]["category"]) ) {
                if ( $environment["ebene"] == "" ) {
                    $show_kat = "/".$environment["kategorie"];
                } else {
                    $show_kat = $environment["ebene"]."/".$environment["kategorie"];
                }
            }

            if ( empty($environment["parameter"][2]) ) {
                $dataloop["list"] = show_blog($key,$tags,$cfg["auth"]["ghost"]["contented"],$cfg["bloged"]["blogs"][$key]["rows"],$show_kat);
            } else {
                $all = show_blog($key,$tags,$cfg["auth"]["ghost"]["contented"],$limit,$show_kat);
                unset($hidedata["new"]);
                $hidedata["all"]["inhalt"] = $all[1]["all"];
            }
        }
    }

////+///////+///////+///////+///////+///////+///////+///////////////////////////////////////////////////////////
?>
