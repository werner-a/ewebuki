<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "gerdate";
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


    function gerdate($art="", $value="") {

        // gds -> german day short
        // gdl -> german day log
        // gms -> german month short
        // gml -> german month long

        if ( $art == "gds" ) {
            switch ($value) {
                case "0": $tag="So, "; break;
                case "1": $tag="Mo, "; break;
                case "2": $tag="Di, "; break;
                case "3": $tag="Mi, "; break;
                case "4": $tag="Do, "; break;
                case "5": $tag="Fr, "; break;
                case "6": $tag="Sa, "; break;
            }
        } else {
            if ( $value == "" ) $value=date("w");
            switch ($value) {
                case "0": $tag="Sonntag, "; break;
                case "1": $tag="Montag, "; break;
                case "2": $tag="Dienstag, "; break;
                case "3": $tag="Mittwoch, "; break;
                case "4": $tag="Donnerstag, "; break;
                case "5": $tag="Freitag, "; break;
                case "6": $tag="Samstag, "; break;
            }
        }

        if ( $art == "gms" ) {
            switch ($value) {
                case 1: $monat="Jan."; break;
                case 2: $monat="Feb."; break;
                case 3: $monat="Mär."; break;
                case 4: $monat="Apr."; break;
                case 5: $monat="Mai"; break;
                case 6: $monat="Jun."; break;
                case 7: $monat="Jul."; break;
                case 8: $monat="Aug."; break;
                case 9: $monat="Sep."; break;
                case 10: $monat="Okt."; break;
                case 11: $monat="Nov."; break;
                case 12: $monat="Dez."; break;
            }
        } elseif ( $art == "gml" ) {
            switch ($value) {
                case 1: $monat="Januar"; break;
                case 2: $monat="Februar"; break;
                case 3: $monat="März"; break;
                case 4: $monat="April"; break;
                case 5: $monat="Mai"; break;
                case 6: $monat="Juni"; break;
                case 7: $monat="July"; break;
                case 8: $monat="August"; break;
                case 9: $monat="September"; break;
                case 10: $monat="Oktober"; break;
                case 11: $monat="November"; break;
                case 12: $monat="Dezember"; break;
            }
        } else {
            if ( $value == "" ) $value=date("n");
            switch ($value) {
                case 1: $monat="01."; break;
                case 2: $monat="02."; break;
                case 3: $monat="03."; break;
                case 4: $monat="04."; break;
                case 5: $monat="05."; break;
                case 6: $monat="06."; break;
                case 7: $monat="07."; break;
                case 8: $monat="08."; break;
                case 9: $monat="09."; break;
                case 10: $monat="10."; break;
                case 11: $monat="11."; break;
                case 12: $monat="12."; break;
            }
        }

        $tagom=date("d");
        $jahr=date("Y");

        if ( $art == "gds" || $art == "gdl" ) {
            return sprintf("%s",$tag);
        } elseif ( $art == "gms" || $art == "gml" ) {
            return sprintf("%s",$monat);
        } else {
            return sprintf("%s%s.%s%s",$tag,$tagom,$monat,$jahr);
        }
    }


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
