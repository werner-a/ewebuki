<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "tagreplace funktion";
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

    function tagreplace($replace) {
        global $pathvars, $environment, $ausgaben;
        // neues generelles tagreplace
        while ( ereg("\[[A-Z1-2]{1,6}(\]|=)", $replace, $tag ) ) {
            $opentag = $tag[0];
            if ( strstr($replace, $opentag) ){
                // wo beginnt der tag
                $tagbeg = strpos($replace,$opentag);
                // wie sieht der endtag aus
                if ( strstr($opentag, "=") ) {
                  $endtag = str_replace("=","]",$opentag);
                  $endtag = str_replace("[","[/",$endtag);
                } else {
                  $endtag = str_replace("[","[/",$opentag);
                }
                // wo endet der tag
                $tagend = strpos($replace,$endtag);
                // wie lang ist der tag
                $taglen = (int) $tagend-$tagbeg;
                // wie lautet der tagwert
                $tagwertbeg = $tagbeg + strlen($opentag);
                $tagwertlen = $taglen - strlen($endtag)+1;
                $tagwert = substr($replace,$tagwertbeg,$tagwertlen);

                // cariage return + linefeed fix
                $tagwert_nocrlf = str_replace("AB]\r\n","AB]",$tagwert);
                $tagwert_nocrlf = str_replace("W]\r\n","W]",$tagwert_nocrlf);
                $tagwert_nocrlf = str_replace("L]\r\n","L]",$tagwert_nocrlf);
                $tagwert_nocrlf = str_replace("\r\n[","[",$tagwert_nocrlf);
                #echo "<pre>";
                #echo ">".$tagwert_nocrlf."<";
                #echo "</pre>";

                // offene tags abfangen
                #if ( strstr($tagwert, $opentag) || ( strstr($replace, $opentag) && $tagwert == "" ) ) {
                if ( strstr($tagwert, $opentag) || $tagwertlen < 0 ) {
                    $i++;
                    $merk_es_dir["##$i##"] = $opentag;
                    $ausgabewert = "<big><font color=\"#FF0000\">##$i## (close tag?) </font></big>";
                    $replace = str_replace($opentag,$ausgabewert,$replace);
                }

                // kompletten tag mit tagwert ersetzen
                switch ($opentag) {
                    case "[B]":
                        $replace = str_replace($opentag.$tagwert.$endtag,"<b>".$tagwert."</b>",$replace);
                        break;
                    case "[I]":
                        $replace = str_replace($opentag.$tagwert.$endtag,"<i>".$tagwert."</i>",$replace);
                        break;
                    case "[TT]":
                        $replace = str_replace($opentag.$tagwert.$endtag,"<tt>".$tagwert."</tt>",$replace);
                        break;
                    case "[U]":
                        $replace = str_replace($opentag.$tagwert.$endtag,"<u>".$tagwert."</u>",$replace);
                        break;
                    case "[S]":
                        $replace = str_replace($opentag.$tagwert.$endtag,"<s>".$tagwert."</s>",$replace);
                        break;
                    case "[ST]":
                        $replace = str_replace($opentag.$tagwert.$endtag,"<strike>".$tagwert."</strike>",$replace);
                        break;
                    case "[BIG]":
                        $replace = str_replace($opentag.$tagwert.$endtag,"<big>".$tagwert."</big>",$replace);
                        break;
                    case "[SMALL]":
                        $replace = str_replace($opentag.$tagwert.$endtag,"<small>".$tagwert."</small>",$replace);
                        break;
                    case "[SUP]":
                        $replace = str_replace($opentag.$tagwert.$endtag,"<sup>".$tagwert."</sup>",$replace);
                        break;
                    case "[SUB]":
                        $replace = str_replace($opentag.$tagwert.$endtag,"<sub>".$tagwert."</sub>",$replace);
                        break;
                    case "[CENTER]":
                        $replace = str_replace($opentag.$tagwert.$endtag,"<center>".$tagwert."</center>",$replace);
                        break;
                    case "[QUOTE]":
                        $replace = str_replace($opentag.$tagwert.$endtag,"&quot;".$tagwert."&quot;",$replace);
                        break;
                    case "[CITE]":
                        $replace = str_replace($opentag.$tagwert.$endtag,"<blockquote>".$tagwert."</blockquote>",$replace);
                        break;
                    case "[PRE]":
                        $replace = str_replace($opentag.$tagwert.$endtag,"<pre>".$tagwert."</pre>",$replace);
                        break;
                    case "[BR]":
                        $replace = str_replace($opentag.$tagwert.$endtag,"<br />",$replace);
                        break;
                    case "[LIST]":
                        $tagwerte = explode("[*]",$tagwert);
                        $ausgabewert  = "<ul>";
                        while ( list ($key, $punkt) = each($tagwerte)) {
                          $ausgabewert .= "<li>".$punkt."</li>";
                        }
                        $ausgabewert .= "</ul>";
                        $replace = str_replace($opentag.$tagwert.$endtag,$ausgabewert,$replace);
                        break;
                    case "[LIST=":
                        $tagrestbeg = strpos($tagwert,"]");
                        $listart = substr($tagwert,0,$tagrestbeg);
                        $tagrest = substr($tagwert,$tagrestbeg+1);
                        $tagwerte = explode("[*]",$tagrest);
                        if ( $listart == 1 ) {
                            $ausgabewert  = "<ol>";
                            while ( list ($key, $punkt) = each($tagwerte)) {
                                $ausgabewert .= "<li>".$punkt."</li>";
                            }
                            $ausgabewert .= "</ol>";
                        } elseif ( $listart == "DEF" ) {
                            $ausgabewert = "<dl>";
                            while ( list ($key, $punkt) = each($tagwerte)) {
                                if ( $key % 2 != 0 ) {
                                    $ausgabewert .= "<dd>".$punkt."</dd>";
                                } else {
                                    $ausgabewert .= "<dt>".$punkt."</dt>";
                                }
                            }
                            $ausgabewert .= "</dl>";
                        } else {
                            if ( strlen($listart) > 1 ) {
                                $ausgabewert  = "<ul type=\"".$listart."\">";
                            } else {
                                $ausgabewert  = "<ol type=\"".$listart."\">";
                            }
                            while ( list ($key, $punkt) = each($tagwerte)) {
                              $ausgabewert .= "<li>".$punkt."</li>";
                            }
                            if ( strlen($listart) > 1 ) {
                                $ausgabewert .= "</ul>";
                            } else {
                                $ausgabewert .= "</ol>";
                            }
                        }
                        $replace = str_replace($opentag.$tagwert.$endtag,$ausgabewert,$replace);
                        break;
                    case "[LINK]":
                        $ausgabewert  = "<a href=\"".$tagwert."\">".$tagwert."</a>";
                        $replace = str_replace($opentag.$tagwert.$endtag,$ausgabewert,$replace);
                        break;
                    case "[LINK=":
                        $tagwerte = explode("]",$tagwert,2);
                        $pos = strrpos($tagwerte[0],";");
                        if ( $pos >= 1 ) {
                            $target = substr($tagwerte[0],$pos+1);
                            $href = substr($tagwerte[0],0,$pos);
                            $target = " target=\"".$target."\"";
                        } else {
                            $target = "";
                            $href = $tagwerte[0];
                        }
                        if ( $tagwerte[1] == "" ) {
                            $beschriftung = $href;
                        } else {
                            $beschriftung = $tagwerte[1];
                        }
                        $ausgabewert  = "<a href=\"".$href."\"".$target.">".$beschriftung."</a>";
                        $replace = str_replace($opentag.$tagwert.$endtag,$ausgabewert,$replace);
                        break;
                    case "[ANK]":
                        $ausgabewert  = "<a name=\"".$tagwert."\"></a>";
                        $replace = str_replace($opentag.$tagwert.$endtag,$ausgabewert,$replace);
                        break;
                    case "[ANK=":
                        $tagwerte = explode("]",$tagwert,2);
                        $ausgabewert  = "<a name=\"".$tagwerte[0]."\">".$tagwerte[1]."</a>";
                        $replace = str_replace($opentag.$tagwert.$endtag,$ausgabewert,$replace);
                        break;
                    case "[EMAIL]":
                        $ausgabewert  = "<a href=\"mailto:".$tagwert."\">".$tagwert."</a>";
                        $replace = str_replace($opentag.$tagwert.$endtag,$ausgabewert,$replace);
                        break;
                    case "[EMAIL=":
                        $tagwerte = explode("]",$tagwert,2);
                        if ( $tagwerte[1] == "" ) {
                            $beschriftung = $tagwerte[0];
                        } else {
                            $beschriftung = $tagwerte[1];
                        }
                        $ausgabewert  = "<a href=\"mailto:".$tagwerte[0]."\">".$beschriftung."</a>";
                        $replace = str_replace($opentag.$tagwert.$endtag,$ausgabewert,$replace);
                        break;
                    case "[IMG]":
                        if ( !strstr($tagwert, "/") ) {
                            $imgfile = $pathvars["fileroot"]."images/".$environment["design"]."/".$tagwert;
                            if ( file_exists($imgfile) ) {
                                $imgsize = getimagesize($imgfile);
                                $imgsize = " ".$imgsize[3];
                                $imgurl = $pathvars["images"].$tagwert;
                            }
                        } else {
                            $imgurl = $tagwert;
                            if ( !strstr($tagwert, "http") ) {
                                if ( strstr($tagwert[0], $pathvars["filebase"]["webdir"]) ) {
                                    $imgfile = str_replace($pathvars["filebase"]["webdir"],"",$tagwert);
                                    $imgfile = $pathvars["filebase"]["maindir"].$imgfile;
                                } else {
                                    $imgfile = $pathvars["fileroot"].$tagwert;
                                }
                                if ( file_exists($imgfile) ) {
                                    $imgsize = getimagesize($imgfile);
                                    $imgsize = " ".$imgsize[3];
                                }
                            }
                        }
                        $ausgabewert = "<img src=\"".$imgurl."\" alt=\"".$tagwert."\"".$imgsize.">";
                        $replace = str_replace($opentag.$tagwert.$endtag,$ausgabewert,$replace);
                        break;
                    case "[IMG=":
                        $tagwerte = explode("]",$tagwert,2);
                        $imgwerte = explode(";",$tagwerte[0]);
                        if ( $imgwerte[1] == "r" ) {
                            $align = " align=\"right\"";
                        } elseif ( $imgwerte[1] == "l" ) {
                            $align = " align=\"left\"";
                        } else {
                            $align = "";
                        }
                        if ( $imgwerte[2] == "0" ) {
                            $border = " border=\"0\"";
                        } elseif ( $imgwerte[2] > 0 ) {
                            $border = " border=\"".$imgwerte[2]."\"";
                        } else {
                            $border = "";
                        }
                        if ( $tagwerte[1] == "" ) {
                            $beschriftung = $imgwerte[0];
                        } else {
                            $beschriftung = $tagwerte[1];
                        }
                        if ( !strstr($imgwerte[0], "/") ) {
                            $imgfile = $pathvars["fileroot"]."images/".$environment["design"]."/".$imgwerte[0];
                            if ( file_exists($imgfile) ) {
                                $imgsize = getimagesize($imgfile);
                                $imgsize = " ".$imgsize[3];
                                $imgurl = $pathvars["images"].$imgwerte[0];
                            }
                        } else {
                            $imgurl = $imgwerte[0];
                            if ( !strstr($imgwerte[0], "http") ) {
                                if ( strstr($imgwerte[0], $pathvars["filebase"]["webdir"]) ) {
                                    $imgfile = str_replace($pathvars["filebase"]["webdir"],"",$imgwerte[0]);
                                    $imgfile = $pathvars["filebase"]["maindir"].$imgfile;
                                } else {
                                    $imgfile = $pathvars["fileroot"].$imgwerte[0];
                                }
                                if ( file_exists($imgfile) ) {
                                    $imgsize = getimagesize($imgfile);
                                    $imgsize = " ".$imgsize[3];
                                }
                            }
                        }
                        $ausgabewert = "<img src=\"".$imgurl."\" alt=\"".$beschriftung."\"".$align.$border.$imgsize.">";
                        $replace = str_replace($opentag.$tagwert.$endtag,$ausgabewert,$replace);
                        break;
                    case "[IMGB=":
                        $tagwerte = explode("]",$tagwert,2);
                        $imgwerte = explode(";",$tagwerte[0]);
                        if ( $imgwerte[1] == "r" ) {
                            $ausgaben["align"] = " align=\"right\"";
                            if ( $imgwerte[5] == "" ) {
                                $lspace = "10";
                            } else {
                                $lspace = $imgwerte[5];
                            }
                            $rspace = "0";
                        } elseif ( $imgwerte[1] == "l" ) {
                            $ausgaben["align"] = " align=\"left\"";
                            $lspace = "0";
                            if ( $imgwerte[5] == "" ) {
                                $rspace = "10";
                            } else {
                                $rspace = $imgwerte[5];
                            }
                        } else {
                            $ausgaben["align"] = "";
                        }
                        if ( $imgwerte[2] == "0" ) {
                            $ausgaben["border"] = " border=\"0\"";
                        } elseif ( $imgwerte[2] > 0 ) {
                            $ausgaben["border"] = " border=\"".$imgwerte[2]."\"";
                        } else {
                            $ausgaben["border"] = "";
                        }
                        if ( $imgwerte[3] == "" ) {
                            $tspace = "0";
                        } else {
                            $tspace = $imgwerte[3];
                        }
                        if ( $imgwerte[4] == "" ) {
                            $bspace = "0";
                        } else {
                            $bspace = $imgwerte[4];
                        }
                        if ( $tagwerte[1] == "" ) {
                            $beschriftung = $imgwerte[0];
                        } else {
                            $beschriftung = $tagwerte[1];
                        }
                        if ( !strstr($imgwerte[0], "/") ) {
                            $imgfile = $pathvars["fileroot"]."images/".$environment["design"]."/".$imgwerte[0];
                            if ( file_exists($imgfile) ) {
                                $imgsize = getimagesize($imgfile);
                                $imgsize = " ".$imgsize[3];
                                $ausgaben["imgurl"] = $pathvars["images"].$imgwerte[0];
                            }
                        } else {
                            $ausgaben["imgurl"] = $imgwerte[0];
                            if ( !strstr($imgwerte[0], "http") ) {
                                if ( strstr($imgwerte[0], $pathvars["filebase"]["webdir"]) ) {
                                    $imgfile = str_replace($pathvars["filebase"]["webdir"],"",$imgwerte[0]);
                                    $imgfile = $pathvars["filebase"]["maindir"].$imgfile;
                                } else {
                                    $imgfile = $pathvars["fileroot"].$imgwerte[0];
                                }
                                if ( file_exists($imgfile) ) {
                                    $imgsize = getimagesize($imgfile);
                                    $ausgaben["tabwidth"] = $imgsize[0];
                                    $ausgaben["imgsize"] = " ".$imgsize[3];

                                }
                            }
                        }
                        #$ausgabewert = "<img src=\"".$imgurl."\" alt=\"".$beschriftung."\"".$align.$border.$imgsize.">";
                        #$ausgaben["align"] = $align;
                        #$ausgaben["imgurl"] = $imgurl;
                        $ausgaben["alt"] = $beschriftung;
                        $ausgaben["beschriftung"] = $beschriftung;

                        $ausgaben["tspace"] = "<img src=\"".$pathvars["images"]."pos.png\" width=\"1\" height=\"".$tspace."\">";
                        $ausgaben["lspace"] = "<img src=\"".$pathvars["images"]."pos.png\" width=\"".$lspace."\" height=\"1\">";
                        $ausgaben["rspace"] = "<img src=\"".$pathvars["images"]."pos.png\" width=\"".$rspace."\" height=\"1\">";
                        $ausgaben["bspace"] = "<img src=\"".$pathvars["images"]."pos.png\" width=\"1\" height=\"".$bspace."\">";
                        $ausgabewert = str_replace(chr(13).chr(10),"",parser("imgb", ""));

                        $replace = str_replace($opentag.$tagwert.$endtag,$ausgabewert,$replace);
                        break;
                    case "[TAB]":
                        $replace = str_replace($opentag.$tagwert.$endtag,"<table cellspacing=\"0\" cellpadding=\"1\">".$tagwert_nocrlf."</table>",$replace);
                        break;
                    case "[TAB=":
                        $tagwerte = explode("]",$tagwert_nocrlf,2);
                        $tabwerte = explode(";",$tagwerte[0]);
                        if ( $tabwerte[0] == "l" ) {
                            $align = " align=\"left\"";
                        } elseif ( $tabwerte[0] == "m" ) {
                            $align = " align=\"center\"";
                        } elseif ( $tabwerte[0] == "r" ) {
                            $align = " align=\"right\"";
                        } else {
                            $align = "";
                        }
                        if ( $tabwerte[1] != "" ) {
                            $width = " width=\"".$tabwerte[1]."\"";
                        }
                        if ( $tabwerte[2] != "" ) {
                            $border = " border=\"".$tabwerte[2]."\"";
                        }
                        if ( $tabwerte[3] != "" ) {
                            $cellspacing = " cellspacing=\"".$tabwerte[3]."\"";
                        } else {
                            $cellspacing = " cellspacing=\"0\"";
                        }
                        if ( $tabwerte[4] != "" ) {
                            $cellpadding = " cellpadding=\"".$tabwerte[4]."\"";
                        } else {
                            $cellpadding = " cellpadding=\"1\"";
                        }
                        $replace = str_replace($opentag.$tagwert.$endtag,"<table".$cellspacing.$cellpadding.$width.$align.$border.">".$tagwerte[1]."</table>",$replace);
                        break;
                    case "[ROW]":
                        $replace = str_replace($opentag.$tagwert.$endtag,"<tr>".$tagwert_nocrlf."</tr>",$replace);
                        break;
                    case "[COL]":
                        $replace = str_replace($opentag.$tagwert.$endtag,"<td valign=\"top\">".$tagwert_nocrlf."</td>",$replace);
                        break;
                    case "[COL=":
                        $tagwerte = explode("]",$tagwert_nocrlf,2);
                        $colwerte = explode(";",$tagwerte[0]);
                        if ( $colwerte[0] == "l" ) {
                            $align = " align=\"left\"";
                        } elseif ( $colwerte[0] == "m" ) {
                            $align = " align=\"center\"";
                        } elseif ( $colwerte[0] == "r" ) {
                            $align = " align=\"right\"";
                        } else {
                            $align = "";
                        }
                        if ( $colwerte[1] != "" ) {
                            $width = " width=\"".$colwerte[1]."\"";
                        }
                        $replace = str_replace($opentag.$tagwert.$endtag,"<td valign=\"top\"".$align.$width.">".$tagwerte[1]."</td>",$replace);
                        break;
                    case "[H1]":
                        $replace = str_replace($opentag.$tagwert.$endtag,"<span class=\"id_top_head\">".$tagwert."</span>",$replace);
                        break;
                    case "[H2]":
                        $imgfile = $pathvars["fileroot"]."images/".$environment["design"]."/dot1.gif";
                        if ( file_exists($imgfile) ) {
                            $imgsize = getimagesize($imgfile);
                            $imgsize = " ".$imgsize[3];
                            $imgurl = $pathvars["images"]."dot1.gif";
                        }
                        $replace = str_replace($opentag.$tagwert.$endtag,"<img src=\"".$imgurl."\" alt=\"\"".$imgsize."> <span class=\"fkrcontentlead\">".$tagwert."</span>",$replace);
                        break;
                    case "[HL]":
                        $imgfile = $pathvars["fileroot"]."images/".$environment["design"]."/dot1.gif";
                        $replace = str_replace($opentag.$tagwert.$endtag,"<img src=\"".$pathvars["images"]."hl.png\" height=\"1\" width=\"628\" vspace=\"2\" alt=\"\">",$replace);
                        break;
                    case "[M1]":
                        $replace = str_replace($opentag.$tagwert.$endtag,$ausgaben["M1"],$replace);
                        break;
                    case "[M2]":
                        $replace = str_replace($opentag.$tagwert.$endtag,$ausgaben["M2"],$replace);
                        break;
                    default:
                        #$ausgabewert = "\"illegal tag: ".strtolower($opentag)."\"";
                        #$replace = str_replace($opentag,$ausgabewert,$replace);
                        $i++;
                        $ausgabewert = "##$i##";
                        $merk_es_dir["##$i##"] = $opentag;
                        $replace = str_replace($opentag,$ausgabewert,$replace);
               }
           }
        }

        // gemerkte illegale tags wieder rein
        if ( is_array($merk_es_dir) ) {
            foreach($merk_es_dir as $key => $value) {
               $replace = str_replace($key, $value, $replace);
               if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "ersetze marke ".$key." => ".$value.$debugging["char"];
            }
        }

        return $replace;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
