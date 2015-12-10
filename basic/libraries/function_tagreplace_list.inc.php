<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// function_tagreplace_list.inc.php v1 chaot
// tagreplace "list" funktion
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

    function tagreplace_list($replace, $opentag, $tagoriginal, $closetag, $sign) {

        global $cfg, $defaults, $specialvars;

        $tagwert = $tagoriginal;
        // ------------------------------
        
        if ( $sign == "]" ) {
            $tagwerte = explode("[*]",$tagwert);
            $ausgabewert  = "<ul>";
            while ( list ($key, $punkt) = each($tagwerte)) {
                if ( $specialvars["newbrmode"] == True ) $punkt = nlreplace($punkt);
                $ausgabewert .= "<li><span>".$punkt."</span></li>";
            }
            $ausgabewert .= "</ul>";
            $replace = str_replace($opentag.$tagoriginal.$closetag,$ausgabewert,$replace);
        } else {
            $tagrestbeg = strpos($tagwert,"]");
            $listart = substr($tagwert,0,$tagrestbeg);
            $tagrest = substr($tagwert,$tagrestbeg+1);
            $tagwerte = explode("[*]",$tagrest);
            if ( $listart == 1 ) {
                $ausgabewert  = "<ol>";
                while ( list ($key, $punkt) = each($tagwerte)) {
                    if ( $specialvars["newbrmode"] == True ) $punkt = nlreplace($punkt);
                    $ausgabewert .= "<li><span>".$punkt."</span></li>";
                }
                $ausgabewert .= "</ol>";
            } elseif ( $listart == "DEF" ) {
                // tcpdf extra
                if ( $cfg["pdfc"]["state"] == true ) {
                    $ausgabewert = "<div class=\"pdf_state\"><div class=\"deflist\">";
                    while ( list ($key, $punkt) = each($tagwerte)) {
                        if ( $specialvars["newbrmode"] == True ) $punkt = nlreplace($punkt);
                        if ( $key % 2 != 0 ) {
                            $ausgabewert .= "<p>".$punkt."</p>";
                        } else {
                            $ausgabewert .= "<h1>".$punkt."</h1>";
                        }
                    }
                    $ausgabewert .= "</div></div>";
                } else {
                    $ausgabewert = "<dl>";
                    while ( list ($key, $punkt) = each($tagwerte)) {
                        if ( $specialvars["newbrmode"] == True ) $punkt = nlreplace($punkt);
                        if ( $key % 2 != 0 ) {
                            $ausgabewert .= "<dd>".$punkt."</dd>";
                        } else {
                            $ausgabewert .= "<dt>".$punkt."</dt>";
                        }
                    }
                    $ausgabewert .= "</dl>";
                }
            } else {
                if ( strlen($listart) > 1 ) {
                    $ausgabewert  = "<ul type=\"".$listart."\">";
                } else {
                    $ausgabewert  = "<ol type=\"".$listart."\">";
                }
                while ( list ($key, $punkt) = each($tagwerte)) {
                    if ( $specialvars["newbrmode"] == True ) $punkt = nlreplace($punkt);
                    $ausgabewert .= "<li><span>".$punkt."</span></li>";
                }
                if ( strlen($listart) > 1 ) {
                    $ausgabewert .= "</ul>";
                } else {
                    $ausgabewert .= "</ol>";
                }
            }
            $replace = str_replace($opentag.$tagoriginal.$closetag,$ausgabewert,$replace);
        }
        
        // ------------------------------
        return $replace;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
