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

    function tagremove( $text, $legende = False ) {
        global $image_ausgabe, $links_ausgabe;
        // tags entfernen
        $i = 0;
        $preg = "\[[A-Z]{1,6}(\]|=)";
        while ( preg_match("/$preg/",$text,$regs ) ) {
            $opentag = $regs[0];
            # echo $opentag;
            if ( strstr($text, $opentag) ){
                // wo beginnt der tag
                $tagbeg = strpos($text,$opentag);
                // wie sieht der endtag aus
                if ( strstr($opentag, "=") ) {
                  $endtag = str_replace("=","]",$opentag);
                  $endtag = str_replace("[","[/",$endtag);
                } else {
                  $endtag = str_replace("[","[/",$opentag);
                }
                // wo endet der tag
                $tagend = strpos($text,$endtag);
                // wie lang ist der tag
                $taglen = (int) $tagend-$tagbeg;
                // wie lautet der tagwert
                $tagwertbeg = $tagbeg + strlen($opentag);
                $tagwertlen = $taglen - strlen($endtag)+1;
                $tagwert = substr($text,$tagwertbeg,$tagwertlen);

                /*
                // cariage return + linefeed fix
                $tagwert_nocrlf = str_replace("AB]\r\n","AB]",$tagwert);
                $tagwert_nocrlf = str_replace("W]\r\n","W]",$tagwert_nocrlf);
                $tagwert_nocrlf = str_replace("L]\r\n","L]",$tagwert_nocrlf);
                $tagwert_nocrlf = str_replace("\r\n[","[",$tagwert_nocrlf);
                */


                /*
                // offene tags abfangen
                #if ( strstr($tagwert, $opentag) || ( strstr($replace, $opentag) && $tagwert == "" ) ) {
                if ( strstr($tagwert, $opentag) || $tagwertlen < 0 ) {
                    $i++;
                    $merk_es_dir["##$i##"] = $opentag;
                    $ausgabewert = "<big><font color=\"#FF0000\">##$i## (close tag?) </font></big>";
                    $replace = str_replace($opentag,$ausgabewert,$replace);
                }
                */
                switch ($opentag) {
                    case "[IMG=":
                            if (strstr($tagwert,"schlagzeile.") || strstr($tagwert,"extlink.gif") || strstr($tagwert,"bullet.gif") || strstr($tagwert,"space20.gif")) {
                                $text = str_replace($opentag.$tagwert.$endtag,"",$text);
                            continue;
                            }
                            $j++;
                            $image[$j] = $tagwert;
                            if ( $legende == True ) {
                                $replace = "{BILD ".$j."}";
                            } else {
                                $replace = "";
                            }
                            $text = str_replace($opentag.$tagwert.$endtag,$replace,$text);
                        break;
                    case "[IMG]":
                            if (strstr($tagwert,"schlagzeile.") || strstr($tagwert,"extlink.gif") || strstr($tagwert,"bullet.gif") || strstr($tagwert,"space20.gif")) {
                                $text = str_replace($opentag.$tagwert.$endtag,"",$text);
                            continue;
                            }
                            $j++;
                            $image[$j] = $tagwert;
                            $text = str_replace($opentag.$tagwert.$endtag,"{BILD $j}",$text);
                        break;
                    case "[LINK]":
                            $i++;
                            $text = str_replace($opentag.$tagwert.$endtag,"{LINK ".$i."}",$text);
                            $links[$i] = $tagwert;
                            break;
                    case "[LINK=":
                            $tagwerte = explode("]",$tagwert,2);
                            if ( strstr($tagwerte[0],"#") ) {
                                $text = str_replace($opentag.$tagwert.$endtag,$tagwerte[1],$text);
                                continue;
                            }
                            $i++;
                            $pos = strrpos($tagwerte[0],";");
                            if ( $pos >= 1 ) {
                                $replace = substr($tagwerte[0],0,$pos);
                            } else {
                                $replace = $tagwerte[0];
                            }
                            $text = str_replace($opentag.$tagwert.$endtag,"{LINK ".$i."}".$tagwerte[1],$text);
                            $links[$i] = $replace;
                        break;
                    case "[LIST]":
                            $text = str_replace($opentag.$tagwert.$endtag,"§§".$tagwert."%%",$text);
                        break;
                    case "[LIST=":
                            $taglistend = strpos($tagwert,"]");
                            $taglist = substr($tagwert,$taglistend+1);
                            $text = str_replace($opentag.$tagwert.$endtag,"§§".$taglist."%%",$text);
                        break;
                    case "[EMAIL=":
                            $tagwerte = explode("]",$tagwert,2);
                            if ( $tagwerte[1] == "" ) {
                                $beschriftung = $tagwerte[0];
                            } else {
                                $beschriftung = $tagwerte[1]." ".$tagwerte[0];
                            }
                            $text = str_replace($opentag.$tagwert,$beschriftung,$text);
                            $text = str_replace($endtag,"",$text);
                        break;
                    case "[ANK=":
                        $tagrestbeg = strpos($tagwert,"]");
                        $ankart = substr($tagwert,0,$tagrestbeg+1);
                        $text = str_replace($opentag.$ankart,"",$text);
                        $text = str_replace($endtag,"",$text);
                        break;
                    default:
                        $text = str_replace($opentag,"",$text);
                        $text = str_replace($endtag,"",$text);
                }
            }
        }

        // links und bilder nach text wandeln
        $i = 0;
        if ( $legende == True ) {

            // links
            if (is_array($links) ) {
                foreach ($links as $key => $value) {
                    $i++;
                    $links_ausgabe .= "Link ".$i.": ".$value."\n";
                }
                $links_ausgabe = "\nLinks zum Beitrag:\n".$links_ausgabe;
            }

            // bilder
            if ( is_array($image) ) {
                foreach ($image as $key => $value) {
                    if ( strstr($value, ";") ) {
                        $pos = strpos($value,";");
                        $value = substr($value,0,$pos);
                    }
                    if ( strstr($value, "]") ) {
                        $pos = strpos($value,"]");
                        $value = substr($value,0,$pos);
                    }
                    $image_ausgabe .= "Bild ".$key." http://www.bvv.bayern.de".$value."\n";
                }
                $image_ausgabe = "\nBilder zum Beitrag:\n".$image_ausgabe;
            }
        }

        // den markierten LIST-Tag finden und formatiert ausgeben
        $preg= "§§";
        while ( preg_match("/$preg/",$text,$regs ) ) {
            $listbeg = strpos($text,"§§");
            $listend = strpos($text,"%%");
            $listlen = (int) ($listend)-$listbeg;
            $wert = substr($text,$listbeg,$listlen+2);
            $listausgabe = substr($text,$listbeg+2,$listlen-2);
            #echo $listausgabe;
            $liste = explode("[*]",$listausgabe);
            foreach ($liste as $key) {
                $key = wordwrap($key,60,"\n",0);
                $key = str_replace("\n","\n     ",$key);
                $ersatz .= "\n   * ".$key."\n";
            }
            $text = str_replace($wert,$ersatz."\n",$text);
            $wert = "";
            $ersatz = "";
        }
        return($text);
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
