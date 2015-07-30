<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// function_intelilink.inc.php v1 chaot
// intelilink funktion
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

    function intelilink( $replace ) {
        while ( strstr( $replace, "#[" ) ) {
            // wo beginnt die marke
            $linkbeg = strpos( $replace, "#[" );
            // gibt es einen link text?
            $textbeg = strpos( $replace, "][", $linkbeg );
            if ( $textbeg === false ) {
                // nein, wo endet die marke
                $linkend = strpos( $replace, "]", $linkbeg );
                // wie lang ist die marke
                $linklen = $linkend-$linkbeg;
                // link extrahieren
                $link_url = substr( $replace, $linkbeg+2, $linklen-2 );
                // link text setzen
                $link_text = $link_url;
                // wie sieht needle aus
                $link_needle = "#[".$link_url."]";
            } else {
                // nein, wo endet die marke
                $linkend = $textbeg;
                // wie lang ist die marke
                $linklen = $linkend-$linkbeg;
                // link extrahieren
                $link_url = substr( $replace, $linkbeg+2, $linklen-2 );
                // link text extrahieren
                $rest = substr($replace,$textbeg+2);
                $textend = strpos( $rest, "]" );
                $link_text = substr( $rest, 0, $textend );
                // wie sieht needle aus
                $link_needle = "#[".$link_url."][".$link_text."]";
            }
            // den link token entsprechend umbauen
            if ( strstr($link_url,"@") && !strstr($link_url,"ftp://") && !strstr($link_url,"html://")) {
                $link_replace="<a href=\"mailto:".$link_url."\">".$link_text."</a>";
            } elseif ( strstr($link_url,"html://") || strstr($link_url,"ftp://") ) {
                $link_replace="<a target=\"_blank\" href=\"".$link_url."\">".$link_text."</a>";
            } else {
                $link_replace="<a href=\"".$link_url."\">".$link_text."</a>";
            }
            $replace = str_replace($link_needle,$link_replace,$replace);
        }
        return $replace;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
