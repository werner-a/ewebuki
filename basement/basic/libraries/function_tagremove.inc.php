<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "tagremove";
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

    function tagremove( $replace, $legende = False, $allowed_tags = "all" ) {
        global $image_ausgabe, $links_ausgabe, $defaults;
        $image_ausgabe = "";
        $links_ausgabe = "";
        $preg = "\[\/[A-Z0-9]{1,6}\]";
        while ( preg_match("/$preg/", $replace, $match ) ) {

            $closetag = $match[0];

//             if ( is_array($allowed_tags) ) {
//
//                 $replace = str_replace($opentag.$tagwert.$closetag,$removed,$replace);
//                 continue;
//             }
            if ( strstr($replace, $closetag) ){

                // wo beginnt der closetag
                $closetagbeg = strpos($replace,$closetag);

                // wie sieht der opentag aus
                $opentag = str_replace(array("/","]"),array("",""),$closetag);

                // wie lang ist der opentag
                $opentaglen = strlen($opentag);

                // nur hier kann der opentag sein
                $haystack = substr($replace,0,$closetagbeg);

                // fehlenden open tag abfangen
                if ( (strpos($haystack,$opentag."]") === false) && (strpos($haystack,$opentag."=") === false) ) {
                    if ( $defaults["tag"]["error"] == "" ) {
                        $error = " <font color=\"#FF0000\">".$opentag."]?</font> ";
                    } else {
                        $error = $defaults["tag"]["error"].$opentag."]".$defaults["tag"]["/error"];
                    }
                    $replace = $error;
                    #$replace = $haystack.$error.substr($replace,$closetagbeg+$opentaglen+2);
                    continue;
                }

                // wie lautet der tagwert
                $tagwertbeg = strlen($haystack) - (strpos(strrev($haystack), strrev($opentag)) + strlen($opentag)) + $opentaglen + 1;
                $tagwert = substr($replace,$tagwertbeg,$closetagbeg-$tagwertbeg);

                // parameter?
                $sign = substr($replace,$tagwertbeg-1,1);
                // opentag komplettieren
                $opentag = $opentag.$sign;

                if ( is_array($allowed_tags) ) {
                    if ( in_array($closetag,$allowed_tags) ) {
                        $removed = $opentag.$tagwert.str_replace("]","---]",$closetag);
                    } else {
                        if ( $sign == "=" ) {
                            $tagwerte = explode("]",$tagwert,2);
                            $removed = $tagwerte[1];
                        } else {
                            $removed = $tagwert;
                        }
                    }
                } else {
                    switch ($closetag) {
                        case "[/IMGB]": case "[/IMG]":
                            if (strstr($tagwert,"schlagzeile.") || strstr($tagwert,"extlink.gif") || strstr($tagwert,"bullet.gif") || strstr($tagwert,"space20.gif")) {
                                $removed = "";
                            } else {
                                $j++;
                                $tagwerte = explode("]",$tagwert,2);
                                $parameter = explode(";",$tagwerte[0],2);
                                // legende erzeugen
                                if ( $legende == true ) $image[$j] = $parameter[0];
                                $removed = " {BILD ".$j."} ";
                            }
                            $replace = str_replace($opentag.$tagwert.$closetag,$removed,$replace);
                            break;
                        case "[/LINK]":
                            if ( substr($tagwert,0,1) == "#" ) {
                                $removed = "";
                            } else {
                                $l++;
                                $tagwerte = explode("]",$tagwert,2);
                                $parameter = explode(";",$tagwerte[0],2);
                                // legende erzeugen
                                if ( $legende == true ) $links[$l] = $parameter[0];
                                $removed = " {LINK ".$l."} ";
                            }

                            break;
                        case "[/LIST]":
                            if ( $sign == "]" ) {
                                $removed = str_replace("[*]"," ",$tagwert);
                            } else {
                                $tagwerte = explode("]",$tagwert,2);
                                $removed = str_replace("[*]"," ",$tagwerte[1]);
                            }
                            break;
                        case "[/EMAIL]":
                            if ( $sign == "]" ) {
                                $removed = $tagwert;
                            } else {
                                $tagwerte = explode("]",$tagwert,2);
                                $removed = $tagwerte[0];
                            }
                        break;
                        default:
                            if ( $sign == "=" ) {
                                $tagwerte = explode("]",$tagwert,2);
                                $removed = $tagwerte[1];
                            } else {
                                $removed = $tagwert;
                            }
                    }
                }

                $replace = str_replace($opentag.$tagwert.$closetag,$removed,$replace);
            }
        }
        $replace = str_replace("---]","]",$replace);
        // links und bilder nach text wandeln
        $i = 0;
        if ( $legende == True ) {
            // links
            foreach ((array)$links as $key => $value) {
                $zl++;
                if ( $zl == 1 ) $links_ausgabe = "<br>Links zum Beitrag:<br>";
                $links_ausgabe .= "<a href=".$value.">LINK ".$key."</a><br>";
            }
            // bilder
            foreach ((array)$image as $key => $value) {
                $zb++;
                if ( $zb == 1 ) $image_ausgabe = "<br>Bilder zum Beitrag:<br>";
                $image_ausgabe .= "<a href=".$value.">Bild ".$key."</a><br>";
            }
        }


        return($replace);
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
