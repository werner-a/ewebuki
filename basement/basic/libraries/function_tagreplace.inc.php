<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "tagreplace funktion";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2009 Werner Ammon ( wa<at>chaos.de )

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

    function tagreplace($replace) {
        global $db, $debugging, $cfg, $pathvars, $environment, $ausgaben, $defaults, $specialvars,$dataloop,$hidedata,$mapping, $LB_IMG_counter;

        // cariage return + linefeed fix
        if ( $specialvars["newbrmode"] != True ) {
            $sear = array("\r\n[TA", "\r\n[RO", "\r\n[CO", "/H1]\r\n", "/H2]\r\n", "/H3]\r\n", "/H4]\r\n", "/H5]\r\n", "/H6]\r\n[", "/HR]\r\n", "AB]\r\n", "OW]\r\n", "OL]\r\n", "IV]\r\n",);
            $repl = array("[TA",     "[RO",     "[CO",     "/H1]",     "/H2]",     "/H3]",     "/H4]",     "/H5]",     "/H6]",      "/HR]",     "AB]",     "OW]",     "OL]",     "IV]",);
            $replace = str_replace($sear,$repl,$replace);
        }

        $preg = "|\[\/[!A-Z0-9]{1,6}\]|";
        $selection_counter = 0;
        $imgb_counter = 0;
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
                        if ( $specialvars["newbrmode"] == True ) $tagwert = str_replace("\r\n","",$tagwert);
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
                    case "[/TABCSV]":
                        if ( $sign != "]" ) {
                            $tagwerte = explode("]",$tagwert,2);
                            $tabwerte = explode(";",$tagwerte[0]);
                            switch ( $tabwerte[4] ) {
                                case "o":
                                    $va = " valign=\"top\"";
                                    break;
                                case "u":
                                    $va = " valign=\"bottom\"";
                                    break;
                                case "m":
                                    $va = " valign=\"middle\"";
                                    break;
                                default:
                                    $va = "";
                            }
                            if ( $tabwerte[5] != "" ) {
                                $wochentag = array("So,&nbsp;",
                                                   "Mo,&nbsp;",
                                                   "Di,&nbsp;",
                                                   "Mi,&nbsp;",
                                                   "Do,&nbsp;",
                                                   "Fr,&nbsp;",
                                                   "Sa,&nbsp;");
                            }
                            if ( $tabwerte[6] != "" ) {
                                $vortext = array("");
                            }
                            // csv-datei
                            if ( $specialvars["subdir"] != "" ) {
                                $pfad = str_replace( $specialvars["subdir"]."/", "", $tabwerte[0] );
                            } else {
                                $pfad = $tabwerte[0];
                            }
                            $file_path = explode("/",$pfad);
                            $extension = $cfg["file"]["filetyp"][$file_path[2]];
                            $directory = $cfg["file"]["fileopt"][$extension]["path"];
                            $file_name = $extension."_".$file_path[3].".".$file_path[2];
                            if ( file_exists($directory.$file_name) ) {
                                $table = "";
                                $handle = fopen ($directory.$file_name,"r");
                                // enthaelt die erste zeile spaltenueberschriften
                                if ( $tabwerte[3] != "" ) {
                                    $cell_tag1 = "<th scope=\"col\">"; $cell_tag2 = "</th>\n";
                                    $row_tag1 = "<thead>\n<tr>"; $row_tag2 = "</tr>\n</thead>";
                                } else {
                                    $cell_tag1 = "<td".$va.">"; $cell_tag2 = "</td>\n";
                                    $row_tag1 = "<tr>"; $row_tag2 = "</tr>\n";
                                }
                                $thead = "";
                                // beim ersten Durchlauf der Schleife werden die Vortexte eingelesen, also brauchen
                                // wir dafuer ein Flag. (Man koennte auch aus der while eine for-schleife machen)
                                $firstln = "1";
                                while ( ($data = fgetcsv ($handle, 1500, ";")) !== FALSE ) {
                                    $row = "";
                                    $wieviele = sizeof($data);
                                    if ( $firstln != "" ) $vortext = $data;
                                    for ($i = 0; $i < $wieviele; $i++ ) {
                                        $value = $data[$i];
                                        // tabwert 5 = datumspruefung an
                                        if ( $tabwerte[5] != "" ) {
                                            // wir pruefen, ob die zelle ein Datum enthaelt
                                            if ( strtotime ($value) == TRUE ) {
                                                $beginn = strtotime ($value);
                                                $jetzt = time ();
                                                if ( $beginn + 86400 < $jetzt) {
                                                // wenn der beginn heute ist (jetzt und den ganzen tag), setzen wir skip
                                                // wenn $skip mit irgendwas gefuellt ist, wird die Zeile uebersprungen
                                                    $skip = 1;
                                                } else {
                                                // jetzt setzen wir noch den deutschen Wochentag vors Datum
                                                    $value = $wochentag[date ("w", $beginn)].date ("d.m.Y", $beginn);
                                                // Hier kommt noch ein undokumentierter "Spezialhack"
                                                // Wenn der Parameter 5 groesser 1 ist, wird geschaut, ob nach dem Datum
                                                // gleich noch ein Datum kommt. Wenn ja, wird das als "von-bis" in eine
                                                // Zelle gesetzt.
                                                    if ( $tabwerte[5] > 1 ) {
                                                        $i++;
                                                        if ( strtotime ($data[$i]) == TRUE ) {
                                                        $ende = strtotime ($data[$i]);
                                                        $tbeginn = $wochentag[date ("w", $beginn)].date ("d.m.Y", $beginn);
                                                        $tende = $wochentag[date ("w", $ende)].date ("d.m.Y", $ende);
                                                        $value = $tbeginn."<br />bis<br />".$tende;
                                                        $tbeginn = ""; $ende = ""; $tende = "";
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        // noch Teil des Spezialhacks: ignorieren.
                                        if ( $firstln != "" && $tabwerte[5] >1 && $i >= $tabwerte[5]-1 ) {
                                            if ($i == $tabwerte[5]-1 ) $i++;
                                            $value = $data[$i];
                                        }
                                        if ( $tabwerte[6] == "" ) {
                                            $row .= $cell_tag1.$value.$cell_tag2;
                                        } else {
                                        // tabwert 6 = don't screw my design-Modus
                                            if ( $firstln != "" ) {
                                                // Die Ueberschrift geben wir bis zum angegebenen Wert ganz normal aus,
                                                // der Rest wird ignoriert.
                                                if ( $i <= $tabwerte[6] && $tabwerte[3] != "" ) {
                                                    $row .= $cell_tag1.$value.$cell_tag2;
                                                }
                                            } else {
                                                if ( $i < $tabwerte[6] ) {
                                                // bis zum Wert, der im tabwert 6 angegeben ist, wird ganz normal ausgegeben.
                                                    $row .= $cell_tag1.$value.$cell_tag2;
                                                }
                                                if ( $i == $tabwerte[6] ) {
                                                // Beim Wert, der in tabwert 6 angegeben ist, oeffnen wir die Zelle
                                                // und setzen einen Zeilenwechsel.
                                                    if ( $tabwerte[7] != "" ) {
                                                    $row .= $cell_tag1."<".$tabwerte[7].">".$value."</".$tabwerte[7]."><br />";
                                                    } else {
                                                    $row .= $cell_tag1.$value."<br />";
                                                    }
                                                }
                                                if ( $i > $tabwerte[6] ) {
                                                    // Die Werte, die nach tabwert 6 kommen, werden bis zum vorletzten Wert
                                                    // (falls vorhanden) mit Vortext ausgegeben und dann der Wert und ein br
                                                    if ( $vortext[$i] != "" ) {
                                                        $row .= $vortext[$i].": ";
                                                    }
                                                    if ( $i < $wieviele-1 ) {
                                                        $row .= $value."<br />";
                                                    } else {
                                                    // der letzte Wert schliesst die Zelle ab
                                                        $row .= $value.$cell_tag2;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if ( !$skip == FALSE ) { $row = ""; $skip = ""; }
                                    if ( $row != "" ) $table .= $row_tag1.$row.$row_tag2;
                                    if ( strstr($cell_tag1,"<th") ) {
                                        $thead = $table;
                                        $table = "";
                                        $cell_tag1 = "<td".$va.">"; $cell_tag2 = "</td>\n";
                                        $row_tag1 = "<tr>"; $row_tag2 = "</tr>\n";
                                    }
                                $firstln = "";
                                }
                                // summary
                                if ( $tagwerte[1] != "" ) {
                                    $summary = " summary=\"".$tagwerte[1]."\"";
                                } else {
                                    $summary = "";
                                }
                                // breite
                                if ( $tabwerte[1] != "" ) {
                                    $width = " width=\"".$tabwerte[1]."%\"";
                                } else {
                                    $width = "";
                                }
                                // border
                                if ( $tabwerte[2] != "" ) {
                                    $border = " border=\"".$tabwerte[2]."\"";
                                } else {
                                    $border = "";
                                }
                                if ( $table != "" ) $table = "<table".$border.$width.$summary.">\n".$thead."<tbody>\n".$table."</tbody>\n</table>\n";
                            } else {
                                $table = "";
                            }
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$table,$replace);
                        } else {
                            $replace = str_replace($opentag.$tagoriginal.$closetag,"",$replace);
                        }
                        break;
                    case "[/ROW]":
                        if ( $specialvars["newbrmode"] == True ) $tagwert = str_replace("\r\n","",$tagwert);
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"<tr>".$tagwert."</tr>",$replace);
                        break;
                    case "[/TH]":
                        if ( $specialvars["newbrmode"] == True ) $tagwert = nlreplace($tagwert);
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"<th valign=\"top\">".$tagwert."</th>",$replace);
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
                                $clear = "clear=\"all\"";
                                $style_clear = "style=\"clear:both;\"";
                            } elseif ( $brwerte[0] == "l" ) {
                                $clear = "clear=\"left\"";
                                $style_clear = "style=\"clear:left;\"";
                            } elseif ( $brwerte[0] == "r" ) {
                                $clear = "clear=\"right\"";
                                $style_clear = "style=\"clear:right;\"";
                            } else {
                                $clear = "";
                                $style_clear = "";
                            }
                            if ( $specialvars["w3c"] == "strict" ) {
                                $replace = str_replace($opentag.$tagoriginal.$closetag,"<br ".$style_clear."/>",$replace);
                            } else {
                                $replace = str_replace($opentag.$tagoriginal.$closetag,"<br ".$clear."/>",$replace);
                            }
                        }
                        break;
                    case "[/IMG]":
                        $LB_IMG_counter ++;
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

                            if ( $defaults["tag"]["img_w3c"] == "" ) $defaults["tag"]["img_w3c"] = "<img src=\"##imgurl##\" title=\"##beschriftung##\" alt=\"##beschriftung##\"##imgsize## style=\"##style_align####style_border####style_hspace####style_vspace##\"##attrib## />";
                            if ( $defaults["tag"]["img"] == "" ) $defaults["tag"]["img"] = "<img src=\"##imgurl##\"##attrib####vspace####hspace## title=\"##beschriftung##\" alt=\"##beschriftung##\"##align####border####imgsize## />";
                            if ( $defaults["tag"]["img_link"] == "" ) $defaults["tag"]["img_link"] = "<a href=\"##imglnk##\" title=\"##beschriftung##\">";
                            if ( $defaults["tag"]["img_link_lb"] == "" ) $defaults["tag"]["img_link_lb"] = "<a href=\"##imglnk##\" title=\"##beschriftung##\" ##lightbox## >";
                            if ( $defaults["tag"]["/img_link"] == "" ) $defaults["tag"]["/img_link"] = "</a>";
                            $repl = array("imgurl","beschriftung", "funder","fdesc","imgsize","attrib","vspace","hspace","align","border","style_align","style_border","style_hspace","style_vspace","imglnk","lightbox");

                            $tagwerte = explode("]",$tagwert,2);
                            $imgwerte = explode(";",$tagwerte[0]);
                            $extrawerte = explode(":",$imgwerte[1]);
                            if ( $extrawerte[1] != "" ) $imgwerte[1] = $extrawerte[1];
                            if ( $extrawerte[0] == "id" ) {
                                $art = " id";
                            } else {
                                $art = " class";
                            }
                            $align = ""; $attrib = ""; $style_align = "";
                            if ( $imgwerte[1] == "r" ) {
                                $align = " align=\"right\"";
                                $style_align = "float:right;";
                            } elseif ( $imgwerte[1] == "l" ) {
                                $align = " align=\"left\"";
                                $style_align = "float:left;";
                            } elseif ( $imgwerte[1] != "" ) {
                                $attrib = " ".$art."=\"".$imgwerte[1]."\"";
                            }
                            if ( $imgwerte[2] == "0" ) {
                                $border = " border=\"0\"";
                                $style_border = "border-width:0;";
                            } elseif ( $imgwerte[2] > 0 ) {
                                $border = " border=\"".$imgwerte[2]."\"";
                                $style_border = "border-width:".$imgwerte[2]."px;";
                            } else {
                                $border = "";
                                $style_border = "";
                            }
                            if ($imgwerte[3] == "l" ) {
                                $lightbox = "rel=\"lightbox[".$LB_IMG_counter."]\"";
                            }
                            if ($imgwerte[4] == "" ) {
                                $vspace = "";
                                $style_vspace = "";
                            } else {
                                $vspace = " vspace=\"".$imgwerte[4]."\"";
                                $style_vspace = "margin-top:".$imgwerte[4]."px;margin-bottom:".$imgwerte[4]."px;";
                            }
                            if ($imgwerte[6] == "" ) {
                                $hspace = "";
                                $style_hspace = "";
                            } else {
                                $hspace = " hspace=\"".$imgwerte[6]."\"";
                                if ( $imgwerte[1] == "r" ) {
                                    $style_hspace = "margin-left:".$imgwerte[6]."px;margin-right:0px;";
                                } elseif ( $imgwerte[1] == "l" ) {
                                    $style_hspace = "margin-left:0px;margin-right:".$imgwerte[6]."px;";
                                } else {
                                    $style_hspace = "margin-left:".$imgwerte[6]."px;margin-right:".$imgwerte[6]."px;";
                                }
                            }
                            if ( $tagwerte[1] == "" ) {
                                $beschriftung = $imgwerte[0];
                            } else {
                                $beschriftung = $tagwerte[1];
                            }
                            // weitere informationen aus datenbank holen
                            if ( preg_match("/^\//",$tagwerte[0]) ) {
                                $img_path = explode("/",str_replace($cfg["file"]["base"]["maindir"],"",$tagwerte[0]) );
                                if ( is_numeric($img_path[3]) ) {
                                    $fid = $img_path[3];
                                //subdir quickfix
                                } elseif ( is_numeric($img_path[4]) ) {
                                    $fid = $img_path[4];
                                } else {
                                    $fid = substr($tagwerte[0],0,strpos($tagwerte[0],";"));
                                    $fid = strrchr($fid,"_");
                                    $fid = substr( $fid , 1 , strpos($fid,".") - 1 );
                                }
                                $sql = "SELECT *
                                            FROM site_file
                                            WHERE fid=".$fid;
                                $result = $db -> query($sql);
                                $data = $db -> fetch_array($result,1);
                                $funder = $data["funder"];
                                $fdesc = $data["fdesc"];
                            } else {
                                $funder = $beschriftung;
                                $fdesc = $beschriftung;
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
                                        if ( $imgwerte[3] == "l" ) {
                                            $imglnk = preg_replace("/\/(tn|s|m|b)\//","/o/",$imgurl);
                                            $linka = $defaults["tag"]["img_link_lb"];
                                        } else {
                                            $linka = $defaults["tag"]["img_link"];
                                        }
                                        $linkb = $defaults["tag"]["/img_link"];
                                    }
                                } else {
                                    $imgsize = "";
                                }
                            }

                            if ( $specialvars["w3c"] == "strict" ) {
                                $ausgabewert = $linka.$defaults["tag"]["img_w3c"].$linkb;
                            } else {
                                $ausgabewert = $linka.$defaults["tag"]["img"].$linkb;
                            }
                            foreach ( $repl as $value ) {
                                $ausgabewert = str_replace("##".$value."##",$$value,$ausgabewert);
                                $$value = "";
                            }
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$ausgabewert,$replace);
                        }
                        break;
                    case "[/LINK]":
                        if ( $sign == "]" ) {
                            $ausgabewert  = "<a href=\"".$tagwert."\" title=\"".$tagwert."\">".$tagwert."</a>";
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$ausgabewert,$replace);
                        } else {
                            $tagwerte = explode("]",$tagwert,2);
                            $linkwerte = explode(";",$tagwerte[0]);
                            $href = $linkwerte[0];
                            if ( $tagwerte[1] == "" ) {
                                $beschriftung = $href;
                            } else {
                                $beschriftung = $tagwerte[1];
                            }
                            // ziel
                            if ( $linkwerte[1] != "" ) {
                                $target = " target=\"".$linkwerte[1]."\"";
                            } else {
                                $target = "";
                            }
                            // title-tag
                            if ( $linkwerte[2] != "" ) {
                                $title = $linkwerte[2];
                            } else {
                                if ( $linkwerte[1] == "_blank" ) {
                                    $title = "Link in neuem Fenster: ".str_replace("http://","",$href);
                                } elseif ( !strstr($beschriftung,"<") ) {
                                    $title = $beschriftung;
                                } else {
                                    $title = "";
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
                            if ( $linkwerte[3] != "" ) { # oder manuell
                                $class .= " ".$linkwerte[3];
                            }
                            $class .= "\"";
                            // id
                            if ( $linkwerte[4] != "" ) {
                                $id = " id=\"".$linkwerte[4]."\"";
                            } else {
                                $id = "";
                            }
                            $ausgabewert = $pic."<a href=\"".$href."\"".$id.$target." title=\"".$title."\"".$class.">".$beschriftung."</a>";
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
                        $imgb_counter ++;
                        if ( $defaults["tag"]["img_link"] == "" ) $defaults["tag"]["img_link"] = "<a href=\"##imglnk##\">";
                        if ( $defaults["tag"]["img_link_lb"] == "" ) $defaults["tag"]["img_link_lb"] = "<a href=\"##imglnk##\" title=\"##beschriftung##\" ##lightbox## >";
                        if ( $defaults["tag"]["/img_link"] == "" ) $defaults["tag"]["/img_link"] = "</a>";
                        $repl = array("imgurl","imglnk","beschriftung", "funder","fdesc","lightbox");

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
                            $ausgaben["border"] = "border-width:0;";
                        } elseif ( $imgwerte[2] > 0 ) {
                            $ausgaben["border"] = "border-width:".$imgwerte[2].";";
                        } else {
                            $ausgaben["border"] = "";
                        }
                        if ($imgwerte[3] == "l" ) {
                            $lightbox = "rel=\"lightbox[b".$imgb_counter."]\"";
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
                        // weitere informationen aus datenbank holen
                        if ( preg_match("/^\//",$tagwerte[0]) ) {
                            $img_path = explode("/",str_replace($cfg["file"]["base"]["maindir"],"",$tagwerte[0]) );
                            if ( is_numeric($img_path[3]) ) {
                                $fid = $img_path[3];
                            } else {
                                $fid = substr($tagwerte[0],0,strpos($tagwerte[0],";"));
                                $fid = strrchr($fid,"_");
                                $fid = substr( $fid , 1 , strpos($fid,".") - 1 );
                            }
                            $sql = "SELECT *
                                        FROM site_file
                                        WHERE fid=".$fid;
                            $result = $db -> query($sql);
                            $data = $db -> fetch_array($result,1);
                            $funder = $data["funder"];
                            $fdesc = $data["fdesc"];
                        } else {
                            $funder = $beschriftung;
                            $fdesc = $beschriftung;
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
                                    if ( $imgwerte[3] == "l" ) {
                                        $imglnk = preg_replace("/\/(tn|s|m)\//","/b/",$imgurl);
                                        $ausgaben["linka"] = $defaults["tag"]["img_link_lb"];
                                    } else {
                                        $ausgaben["linka"] = $defaults["tag"]["img_link"];
                                    }
                                    foreach ( $repl as $value ) {
                                        $ausgaben["linka"] = str_replace("##".$value."##",$$value,$ausgaben["linka"]);
                                    }
                                    $ausgaben["linkb"] = $defaults["tag"]["/img_link"];

                                } else {
                                    $ausgaben["linka"] = "";
                                    $ausgaben["linkb"] = "";
                                }
                            }
                            $ausgaben["imgurl"] = $imgurl;
                        }
                        $ausgaben["alt"] = $beschriftung;
                        $ausgaben["beschriftung"] = $beschriftung;
                        $ausgaben["funder"] = $funder;
                        $ausgaben["fdesc"] = $fdesc;
                        $ausgaben["tspace"] = $tspace;
                        $ausgaben["lspace"] = $lspace;
                        $ausgaben["rspace"] = $rspace;
                        $ausgaben["bspace"] = $bspace;
                        $ausgabewert = str_replace(chr(13).chr(10),"",parser("imgb", ""));
                        $replace = str_replace($opentag.$tagoriginal.$closetag,$ausgabewert,$replace);
                        break;
                    case "[/SEL]":
                        $selection_counter ++;
                        $tag_value = explode("]",$tagwert,2);
                        $tag_param = explode(";",$tag_value[0]);
                        $tag_extra = explode(":",$tag_param[3]);
                        $tag_special = explode(":",$tag_param[0]);

                        if ( !preg_match("/[0-9]+/", $tag_param[0])  ) {
                            $sel = "selection not ready";
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$sel,$replace);
                        } else {
                            $path = dirname($pathvars["requested"]);
                            if ( substr( $path, -1 ) != '/') $path = $path."/";
                            $link = $path.basename($pathvars["requested"],".html")."/view,".$tag_param[1].",#,".$tag_param[0].",".$tag_param[2].".html"; #/view,groesse,bild,selektion,thumbs

                            if ( $defaults["tag"]["sel"] == "" ) $defaults["tag"]["sel"] = "<div style=\"position:relative\" class=\"selection_teaser\">##no_image####youtube_div##\n<b>##title## ##youtube_link##</b>\n##no_image_end##<div>\n<ul>\n";
                            if ( $defaults["tag"]["*sel"] == "" ) $defaults["tag"]["*sel"] = "<li class=\"thumbs\"##style##>\n<a href=\"##link##\" ##lb##class=\"pic\" title=\"##fdesc##\"><img src=\"##tn##\" alt=\"##funder##\" title=\"##funder##\"/></a>\n</li>\n";
                            if ( $defaults["tag"]["/sel"] == "" ) $defaults["tag"]["/sel"] = "</ul>\n</div>\n<span##display##>g(compilation_info)(##count## g(compilation_pics))</span>\n</div>";
                            if ( strstr($tag_param[0],":") ) {
                                $sel_pics = "";
                                foreach ( $tag_special as $pics ) {
                                    if ( $pics == "" ) continue;
                                    ( $sel_pics != "" ) ? $trenner = " ," : $trenner = "";
                                    $sel_pics .= $trenner.$pics;
                                }
                                if ( $sel_pics == "" ) $sel_pics = 0;
                                $sql = "SELECT * FROM site_file WHERE fid in (".$sel_pics.")";
                                $sel_pics1 = explode(":",$tag_param[0]);
                                $i = 0;
                                foreach ( $sel_pics1 as $key => $value ) {
                                    $i++;
                                    $tmp_sort[$value] = $i;
                                }

                                $result = $db -> query($sql);
                                $files = array();$sortarray = array();
                                while ( $data = $db -> fetch_array($result,1) ) {
                                    $sortarray[] = $tmp_sort[$data["fid"]];
                                    $files[] = array(
                                                "fid"    => $data["fid"],
                                                "sort"   => $counter,
                                                "ffart"  => $data["ffart"],
                                                "ffname" => $data["ffname"],
                                                "funder" => $data["funder"],
                                                "fdesc"  => $data["fdesc"]
                                                );
                                }
                                if ( count($files) > 0 ) {
                                    array_multisort( $sortarray, $files);
                                }
                            } else {
                                $sql = "SELECT *
                                          FROM site_file
                                         WHERE fhit LIKE '%#p".$tag_param[0]."%'";
                                $result = $db -> query($sql);
                                $files = array();
                                while ( $data = $db -> fetch_array($result,1) ) {
                                    preg_match("/#p".$tag_param[0]."[,]*([0-9]*)#/i",$data["fhit"],$match);
                                    $files[$match[1]] = array(
                                                "fid"    => $data["fid"],
                                                "sort"   => $match[1],
                                                "ffart"  => $data["ffart"],
                                                "ffname" => $data["ffname"],
                                                "funder" => $data["funder"],
                                                "fdesc"  => $data["fdesc"]
                                                );
                                }
                                ksort($files);
                                $sort = array();
                                foreach ($files as $key => $row) {
                                    $sort[$key]  = $row['sort'];
                                }
                                array_multisort($sort, $files);

                            }

                            $sel = str_replace("##title##",$tag_value[1],$defaults["tag"]["sel"]);

                            // wenn video-parameter vorhanden dann marken ersetzen
                            if ( $tag_param[5] != "") {
                                $sel = str_replace("##youtube_div##","<div class=\"new_box new_space_inside\" style=\"background: #EEF3FB;height:212px;width:250px;display:none\" id=\"".$tag_param[0]."_video\">
                                [OBJECT=http://www.youtube.com/v/".$tag_param[5]."&hl=de_DE&fs=1&;250;192;application/x-shockwave-flash]
                                [PARAM=movie]http://www.youtube.com/v/".$tag_param[5]."&hl=de_DE&fs=1&[/PARAM]
                                [PARAM=wmode]transparent[/PARAM]
                                [/OBJECT]
                                <span style=\"float:right\"><b><a onclick=\"Element.setStyle('".$tag_param[0]."_video', 'display:none');\">Schlie�en</a></b></span></div>",$sel);
                                $sel = str_replace("##youtube_link##","<a onclick=\"Element.setStyle('".$tag_param[0]."_video', 'display:block;position:absolute;left:-1px;top:-228px');\">Video</a>",$sel);
                            } else {
                                $sel = str_replace("##youtube_div##","",$sel);
                                $sel = str_replace("##youtube_link##","",$sel);
                            }

                            $lb_helper = "";
                            $file_counter = 0;
                            foreach ( $files as $row ) {
                                $file_counter ++;

                                if ( $cfg["file"]["base"]["realname"] == True ) {
                                    $img = $cfg["file"]["base"]["webdir"]
                                          .$row["ffart"]."/"
                                          .$row["fid"]."/"
                                          .$tag_param[1]."/"
                                          .$row["ffname"];
                                    $tn =$cfg["file"]["base"]["webdir"]
                                          .$row["ffart"]."/"
                                          .$row["fid"]."/"
                                          ."tn/"
                                          .$row["ffname"];
                                } else {
                                    $img = $cfg["file"]["base"]["webdir"]
                                          .$cfg["file"]["base"]["pic"]["root"]
                                          .$cfg["file"]["base"]["pic"][$tag_param[1]]
                                          ."img_".$row["fid"].".".$row["ffart"];
                                    $tn = $cfg["file"]["base"]["webdir"]
                                         .$cfg["file"]["base"]["pic"]["root"]
                                         .$cfg["file"]["base"]["pic"]["tn"]
                                         ."tn_".$row["fid"].".".$row["ffart"];
                                }


                                $style = "";
                                if ( !in_array( $row["fid"], $tag_extra ) && $tag_param[3] != "a" ) {
                                    if ( $tag_param[4] == "l" ) {
                                        $style = " style=\"display:none;\"";
                                    } else {
                                        continue;
                                    }
                                }


                                if ( $tag_param[4] == "l" ) {
                                    $changed = $img;
                                    $lb = "rel=\"lightbox[group_".$selection_counter."]\" ";
                                } else {
                                    $changed = str_replace( "#", $row["fid"], $link);
                                    $lb = "";
                                }
                                if ( $tag_param[3] == "" && $tag_param[4] == "l" && $file_counter == 1 ){
                                    $tn1 = $img;
                                    continue;
                                }

                                $s = array("##link##", "##lb##", "##tn##", "##funder##", "##fdesc##","##style##");
                                $r = array($changed, $lb, $tn, $row["funder"], $row["fdesc"],$style);
                                $sel .= str_replace($s,$r,$defaults["tag"]["*sel"]);
                            }

                            if ( $tag_param[3] == "" && $tag_param[4] == "l" ) {
                               $ArrayReplace = array(count($files)," style=\"display:none\"");
                            } else {
                               $ArrayReplace = array(count($files),"");
                            }

                            $sel .= str_replace(array("##count##","##display##"),$ArrayReplace,$defaults["tag"]["/sel"]);

                            if ( $tag_param[3] == "" ) {
                                if ( $tag_param[4] == "l" ) {
                                    $sel = str_replace("##no_image##","<a href=\"".$tn1."\" ".$lb.">",$sel);
                                    $sel = str_replace("##no_image_end##","</a>",$sel);
                                } else {
                                    $changed = str_replace( "#", $files[0]["fid"], $link);
                                    $sel = "<a href=\"".$changed."\">".$tag_value[1]."</a>";
                                }
                            } else {
                                $sel = str_replace("##no_image##","",$sel);
                                $sel = str_replace("##no_image_end##","",$sel);
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
                    case "[/BLOG]":

                        if ( $environment["ebene"] == "/wizard" && $environment["kategorie"] == "show" ) {
                            $kat = tname2path($environment["parameter"][2]);
                        } else {
                            if ( $environment["ebene"] == "" ) {
                                $kat = "/".$environment["kategorie"];
                            } else {
                                $kat = $environment["ebene"]."/".$environment["kategorie"];
                            }
                        }
                        $tagwerte = explode("]",$tagwert,2);
                        $url = $tagwerte[0];

                        // erstellen der tags die angezeigt werden
                        if ( is_array($cfg["bloged"]["blogs"][$url]["tags"]) ) {
                            foreach ( $cfg["bloged"]["blogs"][$url]["tags"] as $key => $value) {
                                $tags[$key] = $value;
                            }
                        }

                        require_once $pathvars["moduleroot"]."libraries/function_menu_convert.inc.php";
                        require_once $pathvars["moduleroot"]."libraries/function_show_blog.inc.php";

                        if ( $environment["parameter"][2] == "" || $environment["ebene"] == "/wizard" ) {
                            $dataloop["list"] = show_blog($url,$tags,$cfg["auth"]["ghost"]["contented"],$cfg["bloged"]["blogs"][$url]["rows"],$kat);
                        } else {
                            $all = show_blog($url,$tags,$cfg["auth"]["ghost"]["contented"],$cfg["bloged"]["blogs"][$url]["rows"],$kat);
                            unset($hidedata["new"]);
                            $hidedata["all"]["inhalt"] = $all[1]["all"];
                        }

                        if ( $cfg["bloged"]["blogs"][$url]["category"] != "" ) {
                            $replace = str_replace($opentag.$tagoriginal.$closetag,parser($mapping["main"],""),$replace);
                        } else {
                            $replace = str_replace($opentag.$tagoriginal.$closetag,"not allowed",$replace);
                        }
                        break;
                    case "[/OBJECT]":
                        $tagwerte = explode("]",$tagwert,2);
                        $objectwerte = explode(";",$tagwerte[0]);
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"<object type=\"".$objectwerte[3]."\" data=\"".$objectwerte[0]."\" width=\"".$objectwerte[1]."\" height=\"".$objectwerte[2]."\">".$tagwerte[1]."</object>",$replace);
                        break;
                    case "[/PARAM]":
                        $tagwerte = explode("]",$tagwert,2);
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"<param name=\"".$tagwerte[0]."\" value=\"".$tagwerte[1]."\"></param>",$replace);
                        break;
                    case "[/YT]":
                        // para1 = ausrichtung, para2 = weite, para3 = hoehe, para4 = 2klick, para5 = youtube info anzeigen
                        $tagwerte = explode("]",$tagwert,2);
                        $ytwerte = explode(";",$tagwerte[0]);
                        $ytalign = "left";
                        $ytinfo = "&showinfo=0";
                        if ( $ytwerte[0] == "r") $ytalign = "right";
                        if ( $ytwerte[4] == "-1") $ytinfo = "&showinfo=1";
                        $src = "//www.youtube.com/embed/".$tagwerte[1];
                        $yt = "<div class=\"youtube\" style=\"float:".$ytalign.";width:".$ytwerte[1]."\">";
                        $ausgaben["yt_counter"]++;
                        $ausgaben["yt_width"] = $ytwerte[1];
                        $ausgaben["yt_height"] = $ytwerte[2];
                        $ausgaben["yt_align"] = $ytalign;
                        $ausgaben["yt_id"]=$tagwerte[1];
                        $yt .= parser("youtube_head",'');
                        $yt .=  "<iframe width=\"100%\" height=\"".$ytwerte[2]."\" src=\"".$src."?autohide=1&wmode=opaque".$ytinfo."\"  frameborder=\"0\" allowfullscreen></iframe>";
                        $yt .= "</div>";
                        if ( $ytwerte[3] == -1 && !$_COOKIE["youtube_access"]) {
                            $yt = parser("youtube",'');
                        }
                        $replace = str_replace($opentag.$tagoriginal.$closetag,$yt,$replace);
                        break;
                    case "[/VIDEO]":
                        $tagwerte = explode("]",$tagwert,2);
                        $ytwerte = explode(";",$tagwerte[0]);
                        $video =  "<video controls width=\"".$ytwerte[0]."\" height=\"".$ytwerte[1]."\"";
                        $video .= "<source src=\"".$tagwerte[1]."\"></source>";
                        $video .= "<object><embed width=\"".$ytwerte[0]."\" height=\"".$ytwerte[1]."\" src=\"".$tagwerte[1]."\" type= \"application/x-shockwave-flash\" allowfullscreen=\"false\" allowscriptaccess=\"always\" /></object>";
                        $video .= "</video>";
                        $replace = str_replace($opentag.$tagoriginal.$closetag,$video,$replace);
                        break;
                    default:
                        $extra_tag = str_replace(array("/","]","["),array("","",""),$closetag);
                        if ( $defaults["extra_tags"][$extra_tag] != "" ) {
                            if (  file_exists($pathvars["moduleroot"].$defaults["extra_tags"][$extra_tag]) ) {
                                include $pathvars["moduleroot"].$defaults["extra_tags"][$extra_tag];
                                break;
                            }
                        }
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
