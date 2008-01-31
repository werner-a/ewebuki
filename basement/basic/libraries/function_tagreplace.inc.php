<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "tagreplace funktion";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2008 Werner Ammon ( wa<at>chaos.de )

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
        global $db, $debugging, $cfg, $pathvars, $environment, $ausgaben, $defaults, $specialvars;

        // cariage return + linefeed fix
        if ( $specialvars["newbrmode"] != True ) {
            $sear = array("\r\n[TA", "\r\n[RO", "\r\n[CO", "/H1]\r\n", "/H2]\r\n", "/H3]\r\n", "/H4]\r\n", "/H5]\r\n", "/H6]\r\n[", "/HR]\r\n", "AB]\r\n", "OW]\r\n", "OL]\r\n", "IV]\r\n",);
            $repl = array("[TA",     "[RO",     "[CO",     "/H1]",     "/H2]",     "/H3]",     "/H4]",     "/H5]",     "/H6]",      "/HR]",     "AB]",     "OW]",     "OL]",     "IV]",);
            $replace = str_replace($sear,$repl,$replace);
        }

        $preg = "|\[\/[!A-Z0-9]{1,6}\]|";
        while ( preg_match($preg, $replace, $match ) ) {

            $closetag = $match[0];
            if ( strstr($replace, $closetag) ) {

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
                    $replace = $haystack.$error.substr($replace,$closetagbeg+$opentaglen+2);
                    continue;
                }

                // wie lautet der tagwert
                $tagwertbeg = strlen($haystack) - (strpos(strrev($haystack), strrev($opentag)) + strlen($opentag)) + $opentaglen + 1;
                $tagoriginal = substr($replace,$tagwertbeg,$closetagbeg-$tagwertbeg);
                $tagwert = $tagoriginal;

                // parameter?
                $sign = substr($replace,$tagwertbeg-1,1);
                // opentag komplettieren
                $opentag = $opentag.$sign;

                // kompletten tag mit tagwert ersetzen
                switch ($closetag) {
                    //
                    // Block Elemente
                    // H1-6 | P | PRE | DIV | LIST | HR | TAB, ROW, COL | CENTER
                    //
                    case "[/H1]":
                        if ( $specialvars["newbrmode"] == True ) $tagwert = nlreplace($tagwert);
                        if ( $defaults["tag"]["h1"] == "" ) {
                          $defaults["tag"]["h1"] = "<h1>";
                          $defaults["tag"]["/h1"] = "</h1>";
                        }
                        $replace = str_replace($opentag.$tagoriginal.$closetag,$defaults["tag"]["h1"].$tagwert.$defaults["tag"]["/h1"],$replace);
                        break;
                    case "[/H2]":
                        if ( $specialvars["newbrmode"] == True ) $tagwert = nlreplace($tagwert);
                        if ( $defaults["tag"]["h2"] == "" ) {
                          $defaults["tag"]["h2"] = "<h2>";
                          $defaults["tag"]["/h2"] = "</h2>";
                        }
                        $replace = str_replace($opentag.$tagoriginal.$closetag,$defaults["tag"]["h2"].$tagwert.$defaults["tag"]["/h2"],$replace);
                        break;
                    case "[/H3]":
                        if ( $specialvars["newbrmode"] == True ) $tagwert = nlreplace($tagwert);
                        if ( $defaults["tag"]["h3"] == "" ) {
                          $defaults["tag"]["h3"] = "<h3>";
                          $defaults["tag"]["/h3"] = "</h3>";
                        }
                        $replace = str_replace($opentag.$tagoriginal.$closetag,$defaults["tag"]["h3"].$tagwert.$defaults["tag"]["/h3"],$replace);
                        break;
                    case "[/H4]":
                        if ( $specialvars["newbrmode"] == True ) $tagwert = nlreplace($tagwert);
                        if ( $defaults["tag"]["h4"] == "" ) {
                          $defaults["tag"]["h4"] = "<h4>";
                          $defaults["tag"]["/h4"] = "</h4>";
                        }
                        $replace = str_replace($opentag.$tagoriginal.$closetag,$defaults["tag"]["h4"].$tagwert.$defaults["tag"]["/h4"],$replace);
                        break;
                    case "[/H5]":
                        if ( $specialvars["newbrmode"] == True ) $tagwert = nlreplace($tagwert);
                        if ( $defaults["tag"]["h5"] == "" ) {
                          $defaults["tag"]["h5"] = "<h5>";
                          $defaults["tag"]["/h5"] = "</h5>";
                        }
                        $replace = str_replace($opentag.$tagoriginal.$closetag,$defaults["tag"]["h5"].$tagwert.$defaults["tag"]["/h5"],$replace);
                        break;
                    case "[/H6]":
                        if ( $specialvars["newbrmode"] == True ) $tagwert = nlreplace($tagwert);
                        if ( $defaults["tag"]["h6"] == "" ) {
                          $defaults["tag"]["h6"] = "<h6>";
                          $defaults["tag"]["/h6"] = "</h6>";
                        }
                        $replace = str_replace($opentag.$tagoriginal.$closetag,$defaults["tag"]["h6"].$tagwert.$defaults["tag"]["/h6"],$replace);
                        break;
                    case "[/P]":
                        if ( $specialvars["newbrmode"] == True ) $tagwert = nlreplace($tagwert);
                        if ( $sign == "]" ) {
                            $ausgabewert = "<p>".$tagwert."</p>";
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$ausgabewert,$replace);
                        } else {
                            $tagwerte = explode("]",$tagwert,2);
                            $pwerte = explode(";",$tagwerte[0]);
                            $extrawerte = explode(":",$pwerte[0]);
                            if ( $extrawerte[1] != "" ) $pwerte[0] = $extrawerte[1];
                            if ( $extrawerte[0] == "id" ) {
                                $art = "id";
                            } else {
                                $art = "class";
                            }
                            $attrib = "";
                            if ( $pwerte[0] != "" ) {
                                $attrib = " ".$art."=\"".$pwerte[0]."\"";
                            }
                            $replace = str_replace($opentag.$tagoriginal.$closetag,"<p".$attrib.">".$tagwerte[1]."</p>",$replace);
                        }
                        break;
                    case "[/PRE]":
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"<pre>".$tagwert."</pre>",$replace);
                        break;
                    case "[/DIV]":
                        if ( $specialvars["newbrmode"] == True && strpos( $specialvars["newbrblock"], "DIV") === false ) $tagwert = nlreplace($tagwert);
                        if ( $sign == "]" ) {
                            $ausgabewert = "<div>".$tagwert."</div>";
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$ausgabewert,$replace);
                        } else {
                            $tagwerte = explode("]",$tagwert,2);
                            $divwerte = explode(";",$tagwerte[0]);
                            $extrawerte = explode(":",$divwerte[0]);
                            if ( $extrawerte[1] != "" ) $divwerte[0] = $extrawerte[1];
                            if ( $extrawerte[0] == "id" ) {
                                $art = "id";
                            } else {
                                $art = "class";
                            }
                            $attrib = "";
                            if ( $divwerte[0] != "" ) {
                                $attrib = " ".$art."=\"".$divwerte[0]."\"";
                            }
                            $replace = str_replace($opentag.$tagoriginal.$closetag,"<div".$attrib.">".$tagwerte[1]."</div>",$replace);
                        }
                        break;
                    case "[/LIST]":
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
                        break;
                    case "[/HR]":
                        if ( $defaults["tag"]["hr"] == "" ) {
                          $defaults["tag"]["hr"] = "<hr />";
                          $defaults["tag"]["/hr"] = "";
                        }
                        $replace = str_replace($opentag.$tagoriginal.$closetag,$defaults["tag"]["hr"].$tagwert.$defaults["tag"]["/hr"],$replace);
                        break;
                    case "[/TAB]":
                        if ( $specialvars["newbrmode"] == True ) $replace = str_replace("\r\n","",$replace);
                        if ( $sign == "]" ) {
                            $replace = str_replace($opentag.$tagoriginal.$closetag,"<table cellspacing=\"0\" cellpadding=\"1\">".$tagwert."</table>",$replace);
                        } else {
                            $tagwerte = explode("]",$tagwert,2);
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
                            $replace = str_replace($opentag.$tagoriginal.$closetag,"<table".$cellspacing.$cellpadding.$width.$align.$border.">".$tagwerte[1]."</table>",$replace);
                            $replace = tagreplace($replace);
                        }
                        break;
                    case "[/ROW]":
                        if ( $specialvars["newbrmode"] == True ) $replace = str_replace("\r\n","",$replace);
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"<tr>".$tagwert."</tr>",$replace);
                        break;
                    case "[/COL]":
                        if ( $specialvars["newbrmode"] == True ) $tagwert = nlreplace($tagwert);
                        if ( $sign == "]" ) {
                            $replace = str_replace($opentag.$tagoriginal.$closetag,"<td valign=\"top\">".$tagwert."</td>",$replace);
                        } else {

                            $tagwerte = explode("]",$tagwert,2);
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
                            if ( $colwerte[2] == "o" ) {
                                $valign = " valign=\"top\"";
                            } elseif ( $colwerte[2] == "m" ) {
                                $valign = " valign=\"middle\"";
                            } elseif ( $colwerte[2] == "u" ) {
                                $valign = " valign=\"bottom\"";
                            } elseif ( $colwerte[2] == "g" ) {
                                $valign = " valign=\"baseline\"";
                            } else {
                                $valign = " valign=\"top\"";
                            }
                            $replace = str_replace($opentag.$tagoriginal.$closetag,"<td".$align.$width.$valign.">".$tagwerte[1]."</td>",$replace);
                        }
                        break;
                    case "[/CENTER]":
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"<center>".$tagwert."</center>",$replace);
                        break;
                    //
                    // Inline Elemente
                    // BR | IMG | LINK | -span-
                    //
                    case "[/BR]":
                        if ( $sign == "]" ) {
                            $replace = str_replace($opentag.$tagoriginal.$closetag,"<br />",$replace);
                        } else {
                            $tagwerte = explode("]",$tagwert,2);
                            $brwerte = explode(";",$tagwerte[0]);
                            if ( $brwerte[0] == "a" ) {
                                $clear = "all";
                            } elseif ( $brwerte[0] == "l" ) {
                                $clear = "left";
                            } elseif ( $brwerte[0] == "r" ) {
                                $clear = "right";
                            } else {
                                $clear = "";
                            }
                            if ( $clear != "" ) $clear = " clear=\"".$clear."\"";
                            $replace = str_replace($opentag.$tagoriginal.$closetag,"<br ".$clear."/>",$replace);
                        }
                        break;
                    case "[/IMG]":
                        $imgsize = ""; $imgurl = "";
                        if ( $sign == "]" ) {
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
                                    if ( strpos($tagwert,$cfg["file"]["base"]["pic"]["root"]) === false ) {
                                        $opt = explode("/",$tagwert);
                                        $imgfile = $cfg["file"]["base"]["maindir"]
                                                   .$cfg["file"]["base"]["pic"]["root"]
                                                   .$cfg["file"]["base"]["pic"][$opt[4]]
                                                   ."img_".$opt[3].".".$opt[2];
                                    } elseif ( strstr($tagwert, $cfg["file"]["base"]["webdir"]) ) {
                                        $imgfile = str_replace($cfg["file"]["base"]["webdir"],"",$tagwert);
                                        $imgfile = $cfg["file"]["base"]["maindir"].$imgfile;
                                    } else {
                                        $imgfile = $pathvars["fileroot"].$tagwert;
                                    }
                                    if ( file_exists($imgfile) ) {
                                        $imgsize = getimagesize($imgfile);
                                        $imgsize = " ".$imgsize[3];
                                    } else {
                                        $imgsize = "";
                                    }
                                }
                            }
                            $ausgabewert = "<img src=\"".$imgurl."\" title=\"".$tagwert."\" alt=\"".$tagwert."\"".$imgsize." />";
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$ausgabewert,$replace);
                        } else {
                            $tagwerte = explode("]",$tagwert,2);
                            $imgwerte = explode(";",$tagwerte[0]);
                            $extrawerte = explode(":",$imgwerte[1]);
                            if ( $extrawerte[1] != "" ) $imgwerte[1] = $extrawerte[1];
                            if ( $extrawerte[0] == "id" ) {
                                $art = "id";
                            } else {
                                $art = "class";
                            }
                            $align = ""; $attrib = "";
                            if ( $imgwerte[1] == "r" ) {
                                $align = " align=\"right\"";
                            } elseif ( $imgwerte[1] == "l" ) {
                                $align = " align=\"left\"";
                            } elseif ( $imgwerte[1] != "" ) {
                                $attrib = " ".$art."=\"".$imgwerte[1]."\"";
                            }
                            if ( $imgwerte[2] == "0" ) {
                                $border = " border=\"0\"";
                            } elseif ( $imgwerte[2] > 0 ) {
                                $border = " border=\"".$imgwerte[2]."\"";
                            } else {
                                $border = "";
                            }
                            if ($imgwerte[4] == "" ) {
                                $vspace = "";
                            } else {
                                $vspace = " vspace=\"".$imgwerte[4]."\"";
                            }
                            if ($imgwerte[6] == "" ) {
                                $hspace = "";
                            } else {
                                $hspace = " hspace=\"".$imgwerte[6]."\"";
                            }
                            if ( $tagwerte[1] == "" ) {
                                $beschriftung = $imgwerte[0];
                            } else {
                                $beschriftung = $tagwerte[1];
                            }

                            $linka = "";
                            $linkb = "";
                            if ( !strstr($imgwerte[0], "/") ) {
                                $imgfile = $pathvars["fileroot"]."images/".$environment["design"]."/".$imgwerte[0];
                                if ( file_exists($imgfile) ) {
                                    $imgsize = getimagesize($imgfile);
                                    $imgsize = " ".$imgsize[3];
                                    $imgurl = $pathvars["images"].$imgwerte[0];
                                } else {
                                    $imgsize = "";
                                }
                            } else {
                                $imgurl = $imgwerte[0];
                                if ( !strstr($imgwerte[0], "http") ) {
                                    if ( strpos($imgwerte[0],$cfg["file"]["base"]["pic"]["root"]) === false ) {
                                        $opt = explode("/",str_replace($pathvars["subdir"],"",$imgwerte[0]));
                                        $imgfile = $cfg["file"]["base"]["maindir"]
                                                .$cfg["file"]["base"]["pic"]["root"]
                                                .$cfg["file"]["base"]["pic"][$opt[4]]
                                                ."img_".$opt[3].".".$opt[2];
                                    } elseif ( strstr($imgwerte[0], $cfg["file"]["base"]["webdir"]) ) {
                                        $imgfile = str_replace($cfg["file"]["base"]["webdir"],"",$imgwerte[0]);
                                        $imgfile = $cfg["file"]["base"]["maindir"].$imgfile;
                                    } else {
                                        $imgfile = $pathvars["fileroot"].$imgwerte[0];
                                    }
                                    if ( file_exists($imgfile) ) {
                                        $imgsize = getimagesize($imgfile);
                                        $imgsize = " ".$imgsize[3];
                                    }
                                    if ( $imgwerte[3] != "" ) {
                                        $bilderstrecke = ",".$imgwerte[7];
                                    } else {
                                        $bilderstrecke = "";
                                    }
                                    if ( $imgwerte[3] != "" ) {
                                        if ( strpos($imgurl,$cfg["file"]["base"]["pic"]["root"]) === false ) {
                                            $opt = explode("/",str_replace($pathvars["subdir"],"",$imgurl));
                                            $imgid = $opt[3];
                                        } else {
                                            $opt = split("[_.]",$imgurl);
                                            $imgid = $opt[1];
                                        }
                                        $path = dirname($pathvars["requested"]);
                                        if ( substr( $path, -1 ) != '/') $path = $path."/";
                                        $imglnk = $path.basename($pathvars["requested"],".html")."/view,".$imgwerte[3].",".$imgid.$bilderstrecke.".html";
                                        $linka = "<a href=\"".$imglnk."\">";
                                        if ( $imgwerte[3] == "l" ) {
                                            $linka = "<a href=\"".str_replace("/s/", "/b/", $imgurl)."\" title=\"".$beschriftung."\" rel=\"lightbox[own]\">";;
                                        }
                                        $linkb = "</a>";
                                    }
                                } else {
                                    $imgsize = "";
                                }
                            }
                            $ausgabewert = $linka."<img src=\"".$imgurl."\"".$attrib.$vspace.$hspace." title=\"".$beschriftung."\" alt=\"".$beschriftung."\"".$align.$border.$imgsize." />".$linkb;
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$ausgabewert,$replace);
                        }
                        break;
                    case "[/LINK]":
                        if ( $sign == "]" ) {
                            $ausgabewert  = "<a href=\"".$tagwert."\">".$tagwert."</a>";
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$ausgabewert,$replace);
                        } else {
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
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$ausgabewert,$replace);
                        }
                        break;
                    //
                    // Inline Elemente - Logische Auszeichnungen
                    // AKR | EM | STRONG | CODE | CITE | -q-
                    //
                    case "[/ACR]":
                        if ( $sign == "]" ) {
                            $ausgabewert = "<acronym>".$tagwert."</acronym>";
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$ausgabewert,$replace);
                        } else {
                            $tagwerte = explode("]",$tagwert,2);
                            $acrwerte = explode(";",$tagwerte[0]);
                            $attrib = "";
                            if ( $acrwerte[0] != "" ) {
                                $attrib = " title=\"".$acrwerte[0]."\"";
                            }
                            $replace = str_replace($opentag.$tagoriginal.$closetag,"<acronym".$attrib.">".$tagwerte[1]."</acronym>",$replace);
                        }
                        break;
                    case "[/EM]":
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"<em>".$tagwert."</em>",$replace);
                        break;
                    case "[/STRONG]":
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"<strong>".$tagwert."</strong>",$replace);
                        break;
                    case "[/CODE]":
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"<code>".$tagwert."</code>",$replace);
                        break;
                    case "[/CITE]":
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"<blockquote>".$tagwert."</blockquote>",$replace);
                        break;
                    //
                    // Inline Elemente - Physische Auszeichnungen
                    // B | I | TT | U | S, ST | BIG | SMALL | SUB | SUP
                    //
                    case "[/B]":
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"<b>".$tagwert."</b>",$replace);
                        break;
                    case "[/I]":
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"<i>".$tagwert."</i>",$replace);
                        break;
                    case "[/TT]":
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"<tt>".$tagwert."</tt>",$replace);
                        break;
                    case "[/U]":
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"<u>".$tagwert."</u>",$replace);
                        break;
                    case "[/S]":
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"<s>".$tagwert."</s>",$replace);
                        break;
                    case "[/ST]":
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"<strike>".$tagwert."</strike>",$replace);
                        break;
                    case "[/BIG]":
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"<big>".$tagwert."</big>",$replace);
                        break;
                    case "[/SMALL]":
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"<small>".$tagwert."</small>",$replace);
                        break;
                    case "[/SUB]":
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"<sub>".$tagwert."</sub>",$replace);
                        break;
                    case "[/SUP]":
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"<sup>".$tagwert."</sup>",$replace);
                        break;
                    //
                    // eWeBuKi Spezial
                    // E | ! | ANK | EMAIL | HS |HL | IMGB | IN | M0 | M1 | M2 | QUOTE | SP | UP | PREV | NEXT
                    //
                    case "[/E]":
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"<pre>".$tagwert."</pre>",$replace);
                        break;
                    case "[/!]":
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"<!--".$tagwert."-->",$replace);
                        break;
                    case "[/ANK]":
                        if ( $sign == "]" ) {
                            $ausgabewert  = "<a name=\"".$tagwert."\"></a>";
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$ausgabewert,$replace);
                        } else {
                            $tagwerte = explode("]",$tagwert,2);
                            $ausgabewert  = "<a name=\"".$tagwerte[0]."\">".$tagwerte[1]."</a>";
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$ausgabewert,$replace);
                        }
                        break;
                    case "[/EMAIL]":
                        if ( $sign == "]" ) {
                            $ausgabewert  = "<a href=\"mailto:".$tagwert."\">".$tagwert."</a>";
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$ausgabewert,$replace);
                        } else {
                            $tagwerte = explode("]",$tagwert,2);
                            if ( $tagwerte[1] == "" ) {
                                $beschriftung = $tagwerte[0];
                            } else {
                                $beschriftung = $tagwerte[1];
                            }
                            $ausgabewert  = "<a href=\"mailto:".$tagwerte[0]."\">".$beschriftung."</a>";
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$ausgabewert,$replace);
                        }
                        break;
                    case "[/HS]":
                        $replace = str_replace($opentag.$tagoriginal.$closetag,$tagwert,$replace);
                        break;
                    case "[/HL]":
                        if ( $defaults["tag"]["hl"] == "" ) {
                          $defaults["tag"]["hl"] = "<hr />";
                          $defaults["tag"]["/hl"] = "";
                        }
                        $replace = str_replace($opentag.$tagoriginal.$closetag,$defaults["tag"]["hl"].$tagwert.$defaults["tag"]["/hl"],$replace);
                        break;
                    case "[/IMGB]":
                        $tagwerte = explode("]",$tagwert,2);
                        $imgwerte = explode(";",$tagwerte[0]);
                        $extrawerte = explode(":",$imgwerte[1]);
                        if ( $extrawerte[1] != "" ) $imgwerte[1] = $extrawerte[1];
                        $ausgaben["align"] = ""; $lspace = ""; $rspace = ""; $ausgaben["imgstyle"] = "";$ausgaben["float"] = "";
                        // "id" or "class" wird im template gesetzt (!#ausgaben_imgstyle)
                        if ( $imgwerte[1] == "r" ) {
                            $ausgaben["align"] = "right";
                            $ausgaben["float"] = "float:right;";
                            if ( $imgwerte[6] == "" ) {
                                $lspace = "10";
                            } else {
                                $lspace = $imgwerte[6];
                            }
                            $rspace = "0";
                        } elseif ( $imgwerte[1] == "l" ) {
                            $ausgaben["align"] = "left";
                            $ausgaben["float"] = "float:left;";
                            $lspace = "0";
                            if ( $imgwerte[6] == "" ) {
                                $rspace = "10";
                            } else {
                                $rspace = $imgwerte[6];
                            }
                        } elseif ( $imgwerte[1] != "" ) {
                            $ausgaben["imgstyle"] = $imgwerte[1];
                        }
                        if ( $imgwerte[2] == "0" ) {
                            $ausgaben["border"] = " border=\"0\"";
                        } elseif ( $imgwerte[2] > 0 ) {
                            $ausgaben["border"] = " border=\"".$imgwerte[2]."\"";
                        } else {
                            $ausgaben["border"] = "";
                        }
                        if ( $imgwerte[4] == "" ) {
                            $tspace = "0";
                        } else {
                            $tspace = $imgwerte[4];
                        }
                        if ( $imgwerte[5] == "" ) {
                            $bspace = "0";
                        } else {
                            $bspace = $imgwerte[5];
                        }
                        if ( $tagwerte[1] == "" ) {
                            $beschriftung = $imgwerte[0];
                        } else {
                            $beschriftung = $tagwerte[1];
                        }
                        $ausgaben["linka"] = "";
                        $ausgaben["linkb"] = "";
                        if ( strpos($imgwerte[0],"/") === false ) {
                            $imgfile = $pathvars["fileroot"]."images/".$environment["design"]."/".$imgwerte[0];
                            if ( file_exists($imgfile) ) {
                                $imgsize = getimagesize($imgfile);
                                $ausgaben["imgsize"] = " ".$imgsize[3];
                                $ausgaben["imgurl"] = $pathvars["images"].$imgwerte[0];
                            }
                        } else {
                            $imgurl = $imgwerte[0];
                            if ( strpos($imgurl,"http") === false ) {
                                if ( strpos($imgwerte[0],$cfg["file"]["base"]["pic"]["root"]) === false ) {
                                    $opt = explode("/",str_replace($pathvars["subdir"],"",$imgurl));
                                    $imgfile = $cfg["file"]["base"]["maindir"]
                                               .$cfg["file"]["base"]["pic"]["root"]
                                               .$cfg["file"]["base"]["pic"][$opt[4]]
                                               ."img_".$opt[3].".".$opt[2];
                                } elseif ( strpos($imgurl,$cfg["file"]["base"]["webdir"]) !== false ) {
                                    $imgfile = $cfg["file"]["base"]["maindir"].str_replace($cfg["file"]["base"]["webdir"],"",$imgurl);
                                } else {
                                    $imgfile = $pathvars["fileroot"].$imgwerte[0];
                                }
                                if ( file_exists($imgfile) ) {
                                    $imgsize = getimagesize($imgfile);
                                    $ausgaben["tabwidth"] = $imgsize[0];
                                    $ausgaben["imgsize"] = " ".$imgsize[3];
                                } else {
                                    $ausgaben["tabwidth"] = "";
                                    $ausgaben["imgsize"] = "";
                                }
                                if ( $imgwerte[7] != "" ) {
                                    $bilderstrecke = ",".$imgwerte[7];
                                } else {
                                    $bilderstrecke = "";
                                }
                                if ( $imgwerte[3] != "" ) {
                                    if ( strpos($imgurl,$cfg["file"]["base"]["pic"]["root"]) === false ) {
                                        $opt = explode("/",str_replace($pathvars["subdir"],"",$imgurl));
                                        $imgid = $opt[3];
                                    } else {
                                        $opt = split("[_.]",$imgurl);
                                        $imgid = $opt[1];
                                    }
                                    if ( substr( $pathvars["requested"], 0, 1 ) == '/') $path = substr( $pathvars["requested"], 1 );
                                    $path = dirname($pathvars["requested"]);
                                    if ( substr( $path, -1 ) != '/') $path = $path."/";
                                    $imglnk = $path.basename($pathvars["requested"],".html")."/view,".$imgwerte[3].",".$imgid.$bilderstrecke.".html";
                                    $ausgaben["linka"] = "<a href=\"".$imglnk."\">";
                                    $ausgaben["linkb"] = "</a>";
                                } else {
                                    $ausgaben["linka"] = "";
                                    $ausgaben["linkb"] = "";
                                }
                            }
                            $ausgaben["imgurl"] = $imgurl;
                        }
                        $ausgaben["alt"] = $beschriftung;
                        $ausgaben["beschriftung"] = $beschriftung;
                        $ausgaben["tspace"] = $tspace;
                        $ausgaben["lspace"] = $lspace;
                        $ausgaben["rspace"] = $rspace;
                        $ausgaben["bspace"] = $bspace;
                        $ausgabewert = str_replace(chr(13).chr(10),"",parser("imgb", ""));
                        $replace = str_replace($opentag.$tagoriginal.$closetag,$ausgabewert,$replace);
                        break;
                    case "[/SEL]":
                        if ( $sign == "]" ) {
                            $sel = "not ready";
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$sel,$replace);
                        } else {
                            $tag_value = explode("]",$tagwert,2);
                            $tag_param = explode(";",$tag_value[0]);
                            $tag_extra = explode(":",$tag_param[3]);

                            $path = dirname($pathvars["requested"]);
                            if ( substr( $path, -1 ) != '/') $path = $path."/";
                            $link = $path.basename($pathvars["requested"],".html")."/view,".$tag_param[1].",#,".$tag_param[0].",".$tag_param[2].".html"; #/view,größe,bild,selektion,thumbs

                            if ( $defaults["tag"]["sel"] == "" ) $defaults["tag"]["sel"] = "<div class=\"selection_teaser\">\n<b>##title##</b>\n<div>\n<ul>\n";
                            if ( $defaults["tag"]["*sel"] == "" ) $defaults["tag"]["*sel"] = "<li class=\"thumbs\">\n<a href=\"##link##\" ##lb##class=\"pic\" title=\"##funder##\"><img src=\"##tn##\" alt=\"##funder##\" title=\"##funder##\"/></a>\n</li>\n";
                            if ( $defaults["tag"]["/sel"] == "" ) $defaults["tag"]["/sel"] = "</ul>\n<div style=\"clear:both\"></div>\n</div>\n<span>g(compilation_info)(##count## g(compilation_pics))</span>\n</div>";

                            $sql = "SELECT *
                                        FROM site_file
                                        WHERE fhit like '%#p".$tag_param[0]."%'";
                            $result = $db -> query($sql);
                            $files = array();
                            while ( $data = $db -> fetch_array($result,1) ) {
                                preg_match("/#p".$tag_param[0]."[,]*([0-9]*)#/i",$data["fhit"],$match);
                                $files[] = array(
                                            "fid"    => $data["fid"],
                                            "sort"   => $match[1],
                                            "ffart"  => $data["ffart"],
                                            "funder" => $data["funder"]
                                            );
                            }
                            $sort = array();
                            foreach ($files as $key => $row) {
                                $sort[$key]  = $row['sort'];
                            }
                            array_multisort($sort, $files);

                            if ( $tag_param[3] == "" ) {
                                $changed = str_replace( "#", $files[0]["fid"], $link);
                                $sel = "<a href=\"".$changed."\">".$tag_value[1]."</a>";
                            } else {
                                $sel = str_replace("##title##",$tag_value[1],$defaults["tag"]["sel"]);
                                foreach ( $files as $row ) {

                                    $img = $cfg["file"]["base"]["webdir"]
                                         .$cfg["file"]["base"]["pic"]["root"]
                                         .$cfg["file"]["base"]["pic"]["b"]
                                         ."img_".$row["fid"].".".$row["ffart"];

                                    if ( !in_array( $row["fid"], $tag_extra ) ) {
                                        if ( $tag_param[4] == "l" ) $sel .= "<a href=\"".$img."\" rel=\"lightbox[group]\" title=\"".$row["funder"]."\"></a>";
                                        if ( $tag_param[3] != "a" && !in_array( $row["fid"], $tag_extra ) ) continue;
                                    }

                                    $tn = $cfg["file"]["base"]["webdir"]
                                         .$cfg["file"]["base"]["pic"]["root"]
                                         .$cfg["file"]["base"]["pic"]["tn"]
                                         ."tn_".$row["fid"].".".$row["ffart"];

                                    if ( $tag_param[4] == "l" ) {
                                        $changed = $img;
                                    } else {
                                        $changed = str_replace( "#", $row["fid"], $link);
                                    }
                                    $s = array("##link##", "##lb##", "##tn##", "##funder##",);
                                    $r = array($changed, "rel=\"lightbox[group]\" ", $tn, $row["funder"]);
                                    $sel .= str_replace($s,$r,$defaults["tag"]["*sel"]);
                                }
                                $sel .= str_replace("##count##",count($files),$defaults["tag"]["/sel"]);
                            }
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$sel,$replace);
                        }
                    case "[/IN]":
                        if ( $defaults["tag"]["in"] == "" ) {
                          $defaults["tag"]["in"] = "<em>";
                          $defaults["tag"]["/in"] = "</em>";
                        }
                        $replace = str_replace($opentag.$tagoriginal.$closetag,$defaults["tag"]["in"].$tagwert.$defaults["tag"]["/in"],$replace);
                    case "[/M0]":
                        if ( $sign == "]" ) {
                            $m0 = $ausgaben["M0"];
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$m0,$replace);
                        } else {
                            $tagwerte = explode("]",$tagwert,2);
                            $m0werte = explode(";",$tagwerte[0]);
                            if ( $m0werte[0] == "l" ) {
                                $m0 = $ausgaben["L0"];
                            } else {
                                $m0 = $ausgaben["M0"];
                            }
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$m0,$replace);
                        }
                        break;
                    case "[/M1]":
                        if ( $sign == "]" ) {
                            if ( $tagwert == "" ) {
                                $label = " .. ";
                            } else {
                                $label = $tagwert;
                            }
                            if ( $ausgaben["M1"] != "" ) {
                                $trenner = $defaults["split"]["m1"];
                            } else {
                                $trenner = "";
                            }
                            $m1 = "<a class=\"menu_punkte\" href=\"".$ausgaben["UP"]."\">".$label."</a>".$trenner.$ausgaben["M1"];
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$m1,$replace);
                        } else {
                            $tagwerte = explode("]",$tagwert,2);
                            $m1werte = explode(";",$tagwerte[0]);
                            if ( $tagwerte[1] == "" ) {
                                $label = " .. ";
                            } else {
                                $label = $tagwerte[1];
                            }
                            if ( $m1werte[0] == "l" ) {
                                $m1 = "";
                                if ( $m1werte[1] == "b" ) {
                                    $m1 = $defaults["split"]["l1"]."<a class=\"menu_punkte\" href=\"".$ausgaben["UP"]."\">".$label."</a><br />";
                                }
                                $m1 .= $ausgaben["L1"];
                            } else {
                                $m1 = "";
                                if ( $m1werte[1] == "b" ) {
                                    if ( $ausgaben["M1"] != "" ) {
                                        $trenner = $defaults["split"]["m1"];
                                    } else {
                                        $trenner = "";
                                    }
                                    $m1 = "<a class=\"menu_punkte\" href=\"".$ausgaben["UP"]."\">".$label."</a>".$trenner;
                                }
                                $m1 .= $ausgaben["M1"];
                            }
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$m1,$replace);
                        }
                        break;
                    case "[/M2]":
                        if ( $sign == "]" ) {
                            if ( $tagwert == "" ) {
                                $label = " .. ";
                            } else {
                                $label = $tagwert;
                            }
                            if ( $ausgaben["M2"] != "" ) {
                                $trenner = $defaults["split"]["m2"];
                            } else {
                                $trenner = "";
                            }
                            $m2 = "<a class=\"menu_punkte\" href=\"".$ausgaben["UP"]."\">".$label."</a>".$trenner.$ausgaben["M2"];
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$m2,$replace);
                        } else {
                            $tagwerte = explode("]",$tagwert,2);
                            $m2werte = explode(";",$tagwerte[0]);
                            if ( $tagwerte[1] == "" ) {
                                $label = " .. ";
                            } else {
                                $label = $tagwerte[1];
                            }
                        if ( $m2werte[0] == "l" ) {
                                $m2 = "";
                                if ( $m2werte[1] == "b" ) {
                                    $m2 = $defaults["split"]["l2"]."<a class=\"menu_punkte\" href=\"".$ausgaben["UP"]."\">".$label."</a><br />";
                                }
                                $m2 .= $ausgaben["L2"];
                            } else {
                                $m2 = "";
                                if ( $m2werte[1] == "b" ) {
                                    if ( $ausgaben["M2"] != "" ) {
                                        $trenner = $defaults["split"]["m2"];
                                    } else {
                                        $trenner = "";
                                    }
                                    $m2 = "<a class=\"menu_punkte\" href=\"".$ausgaben["UP"]."\">".$label."</a>".$trenner;
                                }
                                $m2 .= $ausgaben["M2"];
                            }
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$m2,$replace);
                        }
                        break;
                    case "[/M3]":
                        $replace = str_replace($opentag.$tagoriginal.$closetag,$ausgaben["M3"],$replace);
                        break;
                    case "[/UP]":
                        if ( $tagwert == "" ) {
                            $label = " .. ";
                        } else {
                            $label = $tagwert;
                        }
                        $up = "<a class=\"menu_punkte\" href=\"".$ausgaben["UP"]."\">".$label."</a>";
                        $replace = str_replace($opentag.$tagoriginal.$closetag,$up,$replace);
                        break;
                    case "[/PREV]":
                        $replace = str_replace($opentag.$tagoriginal.$closetag,$ausgaben["prev"],$replace);
                        break;
                    case "[/NEXT]":
                        $replace = str_replace($opentag.$tagoriginal.$closetag,$ausgaben["next"],$replace);
                        break;
                    case "[/QUOTE]":
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"&quot;".$tagwert."&quot;",$replace);
                        break;
                    case "[/SP]":
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"&nbsp;",$replace);
                        break;
                    default:
                        // unbekannte tags verstecken
                        $replace = str_replace($closetag,"[##".substr($closetag,1),$replace);
               }
           }
        }
        // unbekannte tags wiederherstellen
        $replace = str_replace("[##/","[/",$replace);

        return $replace;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>