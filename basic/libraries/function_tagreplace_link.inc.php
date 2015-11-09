<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// function_tagreplace_link.inc.php v1 chaot
// tagreplace "link" funktion
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

    function tagreplace_link($replace, $opentag, $tagoriginal, $closetag, $sign) {

        global $cfg, $defaults, $specialvars;

        $tagwert = $tagoriginal;
        // ------------------------------

        // tcpdf extra
        if ( $cfg["pdfc"]["state"] == true ) {
            if ( !preg_match("/^http/",$tagwert) ) {
                $tagwert = "http://".$_SERVER["SERVER_NAME"]."/".$tagwert;
            }
        }

        if ( $sign == "]" ) {
            $ausgabewert  = "<a href=\"".$tagwert."\" title=\"".$tagwert."\">".$tagwert."</a>";
            $replace = str_replace($opentag.$tagoriginal.$closetag,$ausgabewert,$replace);
        } else {
            $tagwerte = explode("]",$tagwert,2);
            $linkwerte = explode(";",$tagwerte[0]);
            $href = $linkwerte[0];
            if ( !isset($tagwerte[1]) ) {
                $beschriftung = $href;
            } else {
                $beschriftung = $tagwerte[1];
            }

            // ziel
            if ( isset($linkwerte[1]) && $linkwerte[1] != "" ) {
                $target = " target=\"".$linkwerte[1]."\"";
            } else {
                $target = null;
            }

            // title-tag
            if ( isset($linkwerte[2]) ) {
                $title = $linkwerte[2];
            } else {
                if ( !isset($linkwerte[1]) ) $linkwerte[1] = null;
                if ( $linkwerte[1] == "_blank" ) {
                    $title = "Link in neuem Fenster: ".str_replace("http://","",$href);
                } elseif ( !strstr($beschriftung,"<") ) {
                    $title = $beschriftung;
                } else {
                    $title = null;
                }
            }

            // css-klasse
            $class = " class=\"link_intern";
            if ( preg_match("/^http/",$href) ) { # automatik
                $class = " class=\"link_extern";
            } elseif ( preg_match("/^".str_replace("/","\/",$cfg["file"]["base"]["webdir"]).".*\.([a-zA-Z]+)/",$href,$match) ) {
                if ( $cfg["file"]["filetyp"][$match[1]] != "" ) {
                    $class = " class=\"link_".$cfg["file"]["filetyp"][$match[1]];
                }
            }
            if ( isset($linkwerte[3]) ) { # oder manuell
                $class .= " ".$linkwerte[3];
            }
            $class .= "\"";

            // id
            if ( isset($linkwerte[4]) ) {
                $id = " id=\"".$linkwerte[4]."\"";
            } else {
                $id = null;
            }

            if ( !isset($pic) ) $pic = null;
            $ausgabewert = $pic."<a href=\"".$href."\"".$id.$target." title=\"".$title."\"".$class.">".$beschriftung."</a>";
            $replace = str_replace($opentag.$tagoriginal.$closetag,$ausgabewert,$replace);
        }

        // ------------------------------
        return $replace;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
