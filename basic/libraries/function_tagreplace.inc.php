<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// function_tagreplace.inc.php v1 chaot
// tagreplace funktion
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

    function tagreplace($replace) {
        global $db, $debugging, $cfg, $pathvars, $environment, $ausgaben, $defaults, $specialvars, $dataloop, $hidedata, $mapping, $LB_IMG_counter;

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
                    if ( !isset($defaults["tag"]["error"]) ) {
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
                    // H1-6 | P | PRE | DIV | LIST | HR | TAB, ROW, TH, COL | CENTER
                    //
                    case "[/H1]":
                    case "[/H2]":
                    case "[/H3]":
                    case "[/H4]":
                    case "[/H5]":
                    case "[/H6]":
                        $replace = tagreplace_h($replace, $opentag, $tagoriginal, $closetag, $sign);
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
                            if ( isset($extrawerte[1]) ) $pwerte[0] = $extrawerte[1];
                            if ( $extrawerte[0] == "id" ) {
                                $art = "id";
                            } else {
                                $art = "class";
                            }
                            $attrib = null;
                            if ( isset($pwerte[0]) ) {
                                $attrib = " ".$art."=\"".$pwerte[0]."\"";
                            }
                            $replace = str_replace($opentag.$tagoriginal.$closetag,"<p".$attrib.">".$tagwerte[1]."</p>",$replace);
                        }
                        break;
                    case "[/PRE]":
                        $replace = str_replace($opentag.$tagoriginal.$closetag,"<pre>".$tagwert."</pre>",$replace);
                        break;
                    case "[/DIV]":
                        $replace = tagreplace_div($replace, $opentag, $tagoriginal, $closetag, $sign);
                        break;
                    case "[/LIST]":
                        $replace = tagreplace_list($replace, $opentag, $tagoriginal, $closetag, $sign);
                        break;
                    case "[/HR]":
                        if ( !isset($defaults["tag"]["hr"]) ) {
                          $defaults["tag"]["hr"] = "<hr />";
                          $defaults["tag"]["/hr"] = null;
                        }
                        if ( $sign == "]" ) {
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$defaults["tag"]["hr"].$tagwert.$defaults["tag"]["/hr"],$replace);
                        } else {
                            $tagwerte = explode("]",$tagwert,2);
                            $attrib = " class=\"".$tagwerte[0]."\">";
                            $tagwithclass = str_replace(">", $attrib, $defaults["tag"]["hr"]);
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$tagwithclass.$tagwerte[1].$defaults["tag"]["/hr"],$replace);
                        }
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
                                $align_html5 = "float: left;";
                            } elseif ( $tabwerte[0] == "m" ) {
                                $align = " align=\"center\"";
                                $align_html5 = "margin-right: auto;margin-left: auto;";
                            } elseif ( $tabwerte[0] == "r" ) {
                                $align = " align=\"right\"";
                                $align_html5 = "float: right;";
                            } else {
                                $align = null;
                                $align_html5 = null;
                            }
                            if ( isset($tabwerte[1]) && $tabwerte[1] != "" ) {
                                $width = " width=\"".$tabwerte[1]."\"";
                                if ( preg_match("/[0-9]+$/",$tabwerte[1]) ) $tabwerte[1] .= "px";
                                $width_html5 = "width:".$tabwerte[1].";";
                            } else {
                                $width = null;
                                $width_html5 = null;
                            }
                            if ( !empty($tabwerte[2]) && $tabwerte[2] != "" ) {
                                $border = " border=\"".$tabwerte[2]."\"";
                                if ( preg_match("/[0-9]+$/",$tabwerte[2]) ) $tabwerte[2] .= "px";
                                $border_html5 = "border-width:".$tabwerte[2].";";
                            } else {
                                $border = null;
                                $border_html5 = null;
                            }
                            if ( isset($tabwerte[3]) && $tabwerte[3] != "" ) {
                                $cellspacing = " cellspacing=\"".$tabwerte[3]."\"";
                                if ( preg_match("/[0-9]+$/",$tabwerte[3]) ) $tabwerte[3] .= "px";
                                $cellspacing_html5 = "border-spacing: ".$tabwerte[3]."; border-collapse: separate;";
                            } else {
                                $cellspacing = " cellspacing=\"0\"";
                                $cellspacing_html5 = null;
                            }
                            if ( isset($tabwerte[4]) && $tabwerte[4] != "" ) {
                                $cellpadding = " cellpadding=\"".$tabwerte[4]."\"";
                                if ( preg_match("/[0-9]+$/",$tabwerte[4]) ) $tabwerte[4] .= "px";
                                $cellpadding_html = "padding: ".$tabwerte[4].";";
                            } else {
                                $cellpadding = " cellpadding=\"1\"";
                                $cellpadding_html = null;
                            }
                            if ( $specialvars["table_html5"] == True ) {
                                $replace = str_replace($opentag.$tagoriginal.$closetag,"<table style=\"".$align_html5.$width_html5.$border_html5.$cellspacing_html5."\">".$tagwerte[1]."</table>",$replace);
                                $replace = str_replace("<td style=\"", "<td style=\"".$cellpadding_html, $replace);
                                $replace = str_replace("<th style=\"", "<th style=\"".$cellpadding_html, $replace);
                            } else {
                                $replace = str_replace($opentag.$tagoriginal.$closetag,"<table".$cellspacing.$cellpadding.$width.$align.$border.">".$tagwerte[1]."</table>",$replace);
                            }
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
                                    $va = null;
                            }
                            if ( isset($tabwerte[5]) ) {
                                $wochentag = array("So,&nbsp;",
                                                   "Mo,&nbsp;",
                                                   "Di,&nbsp;",
                                                   "Mi,&nbsp;",
                                                   "Do,&nbsp;",
                                                   "Fr,&nbsp;",
                                                   "Sa,&nbsp;");
                            }
                            if ( isset($tabwerte[6]) ) {
                                $vortext = array("");
                            }
                            // csv-datei
                            if ( isset($specialvars["subdir"]) ) {
                                $pfad = str_replace( $specialvars["subdir"]."/", "", $tabwerte[0] );
                            } else {
                                $pfad = $tabwerte[0];
                            }
                            $file_path = explode("/",$pfad);
                            $extension = $cfg["file"]["filetyp"][$file_path[2]];
                            $directory = $cfg["file"]["fileopt"][$extension]["path"];
                            $file_name = $extension."_".$file_path[3].".".$file_path[2];
                            if ( file_exists($directory.$file_name) ) {
                                $table = null;
                                $handle = fopen ($directory.$file_name,"r");
                                // enthaelt die erste zeile spaltenueberschriften
                                if ( isset($tabwerte[3]) ) {
                                    $cell_tag1 = "<th scope=\"col\">"; $cell_tag2 = "</th>\n";
                                    $row_tag1 = "<thead>\n<tr>"; $row_tag2 = "</tr>\n</thead>";
                                } else {
                                    $cell_tag1 = "<td".$va.">"; $cell_tag2 = "</td>\n";
                                    $row_tag1 = "<tr>"; $row_tag2 = "</tr>\n";
                                }
                                $thead = null;
                                // beim ersten Durchlauf der Schleife werden die Vortexte eingelesen, also brauchen
                                // wir dafuer ein Flag. (Man koennte auch aus der while eine for-schleife machen)
                                $firstln = "1";
                                while ( ($data = fgetcsv ($handle, 1500, ";")) !== FALSE ) {
                                    $row = null;
                                    $wieviele = sizeof($data);
                                    if ( isset($firstln) ) $vortext = $data;
                                    for ($i = 0; $i < $wieviele; $i++ ) {
                                        $value = $data[$i];
                                        // tabwert 5 = datumspruefung an
                                        if ( isset($tabwerte[5]) ) {
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
                                        if ( isset($firstln) && $tabwerte[5] >1 && $i >= $tabwerte[5]-1 ) {
                                            if ($i == $tabwerte[5]-1 ) $i++;
                                            $value = $data[$i];
                                        }
                                        if ( !isset($tabwerte[6]) ) {
                                            $row .= $cell_tag1.$value.$cell_tag2;
                                        } else {
                                        // tabwert 6 = don't screw my design-Modus
                                            if ( isset($firstln) ) {
                                                // Die Ueberschrift geben wir bis zum angegebenen Wert ganz normal aus,
                                                // der Rest wird ignoriert.
                                                if ( $i <= $tabwerte[6] && isset($tabwerte[3]) ) {
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
                                                    if ( isset($tabwerte[7]) ) {
                                                    $row .= $cell_tag1."<".$tabwerte[7].">".$value."</".$tabwerte[7]."><br />";
                                                    } else {
                                                    $row .= $cell_tag1.$value."<br />";
                                                    }
                                                }
                                                if ( $i > $tabwerte[6] ) {
                                                    // Die Werte, die nach tabwert 6 kommen, werden bis zum vorletzten Wert
                                                    // (falls vorhanden) mit Vortext ausgegeben und dann der Wert und ein br
                                                    if ( isset($vortext[$i]) ) {
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
                                    if ( !$skip == FALSE ) { $row = null; $skip = null; }
                                    if ( isset($row) ) $table .= $row_tag1.$row.$row_tag2;
                                    if ( strstr($cell_tag1,"<th") ) {
                                        $thead = $table;
                                        $table = null;
                                        $cell_tag1 = "<td".$va.">"; $cell_tag2 = "</td>\n";
                                        $row_tag1 = "<tr>"; $row_tag2 = "</tr>\n";
                                    }
                                $firstln = null;
                                }
                                // summary
                                if ( isset($tagwerte[1]) ) {
                                    $summary = " summary=\"".$tagwerte[1]."\"";
                                } else {
                                    $summary = null;
                                }
                                // breite
                                if ( isset($tabwerte[1]) ) {
                                    $width = " width=\"".$tabwerte[1]."%\"";
                                } else {
                                    $width = null;
                                }
                                // border
                                if ( isset($tabwerte[2]) ) {
                                    $border = " border=\"".$tabwerte[2]."\"";
                                } else {
                                    $border = null;
                                }
                                if ( isset($table) ) $table = "<table".$border.$width.$summary.">\n".$thead."<tbody>\n".$table."</tbody>\n</table>\n";
                            } else {
                                $table = null;
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
                        if ( $sign == "]" ) {
                            $replace = str_replace($opentag.$tagoriginal.$closetag,"<th valign=\"top\">".$tagwert."</th>",$replace);
                        } else {
                            $tagwerte = explode("]",$tagwert,2);
                            $colwerte = explode(";",$tagwerte[0]);
                            if ( $colwerte[0] == "l" ) {
                                $align = " align=\"left\"";
                                $align_html5 = "text-align: left;";
                            } elseif ( $colwerte[0] == "m" ) {
                                $align = " align=\"center\"";
                                $align_html5 = "text-align: center;";
                            } elseif ( $colwerte[0] == "r" ) {
                                $align = " align=\"right\"";
                                $align_html5 = "text-align: right;";
                            } else {
                                $align = null;
                                $align_html5 = null;
                            }
                            if ( isset($colwerte[1]) ) {
                                $width = " width=\"".$colwerte[1]."\"";
                                if ( preg_match("/[0-9]+$/",$tabwerte[1]) ) $tabwerte[1] .= "px";
                                $width_html5 = "width:".$colwerte[1].";";
                            } else {
                                $width = null;
                                $width_html5 = null;
                            }
                            if ( isset($colwerte[2]) ) {
                                if ( $colwerte[2] == "o" ) {
                                    $valign = " valign=\"top\"";
                                    $valign_html5 = "vertical-align: top;";
                                } elseif ( $colwerte[2] == "m" ) {
                                    $valign = " valign=\"middle\"";
                                    $valign_html5 = "vertical-align: middle;";
                                } elseif ( $colwerte[2] == "u" ) {
                                    $valign = " valign=\"bottom\"";
                                    $valign_html5 = "vertical-align: bottom;";
                                } elseif ( $colwerte[2] == "g" ) {
                                    $valign = " valign=\"baseline\"";
                                    $valign_html5 = "vertical-align: baseline;";
                                } else {
                                    $valign = " valign=\"top\"";
                                    $valign_html5 = "vertical-align: top;";
                                }
                            } else {
                                $valign = " valign=\"top\"";
                                $valign_html5 = "vertical-align: top;";
                            }
                            if ( $specialvars["table_html5"] == True ) {
                                $replace = str_replace($opentag.$tagoriginal.$closetag,"<th style=\"".$align_html5.$width_html5.$valign_html5."\">".$tagwerte[1]."</th>",$replace);
                            } else {
                                $replace = str_replace($opentag.$tagoriginal.$closetag,"<th".$align.$width.$valign.">".$tagwerte[1]."</th>",$replace);
                            }
                        }
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
                                $align_html5 = "text-align: left;";
                            } elseif ( $colwerte[0] == "m" ) {
                                $align = " align=\"center\"";
                                $align_html5 = "text-align: center;";
                            } elseif ( $colwerte[0] == "r" ) {
                                $align = " align=\"right\"";
                                $align_html5 = "text-align: right;";
                            } else {
                                $align = null;
                                $align_html5 = null;
                            }
                            if ( isset($colwerte[1]) ) {
                                $width = " width=\"".$colwerte[1]."\"";
                                if ( preg_match("/[0-9]+$/",$tabwerte[1]) ) $tabwerte[1] .= "px";
                                $width_html5 = "width:".$colwerte[1].";";
                            } else {
                                $width = null;
                                $width_html5 = null;
                            }
                            if ( isset($colwerte[2]) ) {
                                if ( $colwerte[2] == "o" ) {
                                    $valign = " valign=\"top\"";
                                    $valign_html5 = "vertical-align: top;";
                                } elseif ( $colwerte[2] == "m" ) {
                                    $valign = " valign=\"middle\"";
                                    $valign_html5 = "vertical-align: middle;";
                                } elseif ( $colwerte[2] == "u" ) {
                                    $valign = " valign=\"bottom\"";
                                    $valign_html5 = "vertical-align: bottom;";
                                } elseif ( $colwerte[2] == "g" ) {
                                    $valign = " valign=\"baseline\"";
                                    $valign_html5 = "vertical-align: baseline;";
                                } else {
                                    $valign = " valign=\"top\"";
                                    $valign_html5 = "vertical-align: top;";
                                }
                            } else {
                                $valign = " valign=\"top\"";
                                $valign_html5 = "vertical-align: top;";
                            }
                            if ( $specialvars["table_html5"] == True ) {
                                $replace = str_replace($opentag.$tagoriginal.$closetag,"<td style=\"".$align_html5.$width_html5.$valign_html5."\">".$tagwerte[1]."</td>",$replace);
                            } else {
                                $replace = str_replace($opentag.$tagoriginal.$closetag,"<td".$align.$width.$valign.">".$tagwerte[1]."</td>",$replace);
                            }
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
                                $clear = null;
                                $style_clear = null;
                            }
                            if ( $specialvars["w3c"] == "strict" ) {
                                $replace = str_replace($opentag.$tagoriginal.$closetag,"<br ".$style_clear."/>",$replace);
                            } else {
                                $replace = str_replace($opentag.$tagoriginal.$closetag,"<br ".$clear."/>",$replace);
                            }
                        }
                        break;
                    case "[/IMG]":
                        $replace = tagreplace_img($replace, $opentag, $tagoriginal, $closetag, $sign);
                        break;
                    case "[/LINK]":
                        $replace = tagreplace_link($replace, $opentag, $tagoriginal, $closetag, $sign);
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
                            $attrib = null;
                            if ( isset($acrwerte[0]) ) {
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
                            if ( !isset($tagwerte[1]) ) {
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
                        if ( !isset($defaults["tag"]["hl"]) ) {
                          $defaults["tag"]["hl"] = "<hr />";
                          $defaults["tag"]["/hl"] = null;
                        }
                        $replace = str_replace($opentag.$tagoriginal.$closetag,$defaults["tag"]["hl"].$tagwert.$defaults["tag"]["/hl"],$replace);
                        break;
                    case "[/IMGB]":
                        $imgb_counter ++;
                        if ( !isset($defaults["tag"]["img_link"] ) ) $defaults["tag"]["img_link"] = "<a href=\"##imglnk##\">";
                        if ( !isset($defaults["tag"]["img_link_lb"]) ) $defaults["tag"]["img_link_lb"] = "<a href=\"##imglnk##\" title=\"##beschriftung##\" ##lightbox## >";
                        if ( !isset($defaults["tag"]["/img_link"]) ) $defaults["tag"]["/img_link"] = "</a>";
                        $repl = array("imgurl","imglnk","beschriftung", "funder","fdesc","lightbox");

                        $tagwerte = explode("]",$tagwert,2);
                        $imgwerte = explode(";",$tagwerte[0]);
                        $extrawerte = explode(":",$imgwerte[1]);
                        if ( isset($extrawerte[1]) ) $imgwerte[1] = $extrawerte[1];
                        $ausgaben["align"] = null; $lspace = null; $rspace = null; $ausgaben["imgstyle"] = null;$ausgaben["float"] = null;$ausgaben["class"] = null;
                        // "id" or "class" wird im template gesetzt (!#ausgaben_imgstyle)
                        if ( $imgwerte[1] == "r" ) {
                            $ausgaben["align"] = "right";
                            $ausgaben["float"] = "float:right;";
                            $ausgaben["class"] = "imgb-right";
                            if ( !isset($imgwerte[6]) ) {
                                $lspace = "10";
                            } else {
                                $lspace = $imgwerte[6];
                            }
                            $rspace = "0";
                        } elseif ( $imgwerte[1] == "l" ) {
                            $ausgaben["align"] = "left";
                            $ausgaben["float"] = "float:left;";
                            $ausgaben["class"] = "imgb-left";
                            $lspace = "0";
                            if ( !isset($imgwerte[6]) ) {
                                $rspace = "10";
                            } else {
                                $rspace = $imgwerte[6];
                            }
                        } elseif ( isset($imgwerte[1]) ) {
                            $ausgaben["imgstyle"] = $imgwerte[1];
                        }
                        if ( isset($imgwerte[2]) ) {
                            if ( $imgwerte[2] == "0" ) {
                                $ausgaben["border"] = "border-width:0;";
                            } elseif ( $imgwerte[2] > 0 ) {
                                $ausgaben["border"] = "border-width:".$imgwerte[2].";";
                            }
                        } else {
                            $ausgaben["border"] = null;
                        }
                        if ( empty($imgwerte[3]) ) $imgwerte[3] = null;
                        if ($imgwerte[3] == "l" ) {
                            $lightbox = "rel=\"lightbox[b".$imgb_counter."]\"";
                        }
                        if ( !isset($imgwerte[4]) ) {
                            $tspace = "0";
                        } else {
                            $tspace = $imgwerte[4];
                        }
                        if ( !isset($imgwerte[5]) ) {
                            $bspace = "0";
                        } else {
                            $bspace = $imgwerte[5];
                        }
                        if ( !isset($tagwerte[1]) ) {
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

                        $ausgaben["linka"] = null;
                        $ausgaben["linkb"] = null;
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
                                    $ausgaben["tabwidth"] = null;
                                    $ausgaben["imgsize"] = null;
                                }
                                if ( isset($imgwerte[7]) ) {
                                    $bilderstrecke = ",".$imgwerte[7];
                                } else {
                                    $bilderstrecke = null;
                                }
                                if ( isset($imgwerte[3]) ) {
                                    if ( strpos($imgurl,$cfg["file"]["base"]["pic"]["root"]) === false ) {
                                        $opt = explode("/",str_replace($pathvars["subdir"],"",$imgurl));
                                        $imgid = $opt[3];
                                    } else {
                                        #$opt = split("[_.]",$imgurl); // deprecated
                                        $opt = preg_split("[_.]",$imgurl);
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
                                        if ( !isset($$value) ) $$value = null;
                                        $ausgaben["linka"] = str_replace("##".$value."##",$$value,$ausgaben["linka"]);
                                    }
                                    $ausgaben["linkb"] = $defaults["tag"]["/img_link"];

                                } else {
                                    $ausgaben["linka"] = null;
                                    $ausgaben["linkb"] = null;
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
                        $replace = tagreplace_sel($replace, $opentag, $tagoriginal, $closetag, $sign);
                        break;
                    case "[/IN]":
                        if ( !isset($defaults["tag"]["in"]) ) {
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
                            if ( !isset($tagwert) ) {
                                $label = " .. ";
                            } else {
                                $label = $tagwert;
                            }
                            if ( isset($ausgaben["M1"]) ) {
                                $trenner = $defaults["split"]["m1"];
                            } else {
                                $trenner = null;
                            }
                            $m1 = "<a class=\"menu_punkte\" href=\"".$ausgaben["UP"]."\">".$label."</a>".$trenner.$ausgaben["M1"];
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$m1,$replace);
                        } else {
                            $tagwerte = explode("]",$tagwert,2);
                            $m1werte = explode(";",$tagwerte[0]);
                            if ( !isset($tagwerte[1]) ) {
                                $label = " .. ";
                            } else {
                                $label = $tagwerte[1];
                            }
                            if ( $m1werte[0] == "l" ) {
                                $m1 = null;
                                if ( @$m1werte[1] == "b" ) {
                                    $m1 = $defaults["split"]["l1"]."<a class=\"menu_punkte\" href=\"".$ausgaben["UP"]."\">".$label."</a><br />";
                                }
                                $m1 .= $ausgaben["L1"];
                            } else {
                                $m1 = null;
                                if ( @$m1werte[1] == "b" ) {
                                    if ( isset($ausgaben["M1"]) ) {
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
                            if ( !isset($tagwert) ) {
                                $label = " .. ";
                            } else {
                                $label = $tagwert;
                            }
                            if ( isset($ausgaben["M2"]) ) {
                                $trenner = $defaults["split"]["m2"];
                            } else {
                                $trenner = null;
                            }
                            $m2 = "<a class=\"menu_punkte\" href=\"".$ausgaben["UP"]."\">".$label."</a>".$trenner.$ausgaben["M2"];
                            $replace = str_replace($opentag.$tagoriginal.$closetag,$m2,$replace);
                        } else {
                            $tagwerte = explode("]",$tagwert,2);
                            $m2werte = explode(";",$tagwerte[0]);
                            if ( !isset($tagwerte[1]) ) {
                                $label = " .. ";
                            } else {
                                $label = $tagwerte[1];
                            }
                            $m2 = null;
                            if ( $m2werte[0] == "l" ) {
                                if ( @$m2werte[1] == "b" ) {
                                    $m2 = $defaults["split"]["l2"]."<a class=\"menu_punkte\" href=\"".$ausgaben["UP"]."\">".$label."</a><br />";
                                }
                                $m2 .= $ausgaben["L2"];
                            } else {
                                if ( $m2werte[1] == "b" ) {
                                    if ( isset($ausgaben["M2"]) ) {
                                        $trenner = $defaults["split"]["m2"];
                                    } else {
                                        $trenner = null;
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
                        if ( !isset($tagwert) ) {
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
                            if ( !isset($environment["ebene"]) ) {
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

                        if ( !isset($environment["parameter"][2]) || $environment["ebene"] == "/wizard" ) {
                            $dataloop["list"] = show_blog($url,$tags,$cfg["auth"]["ghost"]["contented"],$cfg["bloged"]["blogs"][$url]["rows"],$kat);
                        } else {
                            $all = show_blog($url,$tags,$cfg["auth"]["ghost"]["contented"],$cfg["bloged"]["blogs"][$url]["rows"],$kat);
                            unset($hidedata["new"]);
                            $hidedata["all"]["inhalt"] = $all[1]["all"];
                        }

                        if ( isset($cfg["bloged"]["blogs"][$url]["category"]) ) {
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
                        // para0 = ausrichtung, para1 = breite, para2 = hoehe,
                        // para3 = datenschutz aus, para4 = showinfo on, para5 = video tipps
                        $tagwerte = explode("]",$tagwert,2);
                        $ytwerte = explode(";",$tagwerte[0]);
                        $src = "//www.youtube-nocookie.com/embed/".$tagwerte[1]."?autohide=1";
                        $class = null;
                        $style = null;
                        if ( isset($ytwerte[0]) ) $class = " class=\"".$ytwerte[0]."\"";
                        if ( $ytwerte[0] == "l") { $style = " style=\"float:left\""; $class = ""; }
                        if ( $ytwerte[0] == "r") { $style = " style=\"float:right\""; $class = ""; }
                        if ( $ytwerte[3] == "-1") $src = "//www.youtube.com/embed/".$tagwerte[1]."?autohide=1";
                        if ( !isset($ytwerte[4]) ) $src .= "&showinfo=0";
                        if ( $ytwerte[4] == "-1") $src .= "&showinfo=1";
                        if ( $ytwerte[5] != "-1") $src .= "&rel=0";
                        $yt = null;
                        if ( isset($ytwerte[0]) ) $yt = "<div".$class.$style.">\n";
                        $yt .= "<iframe width=\"".$ytwerte[1]."\" height=\"".$ytwerte[2]."\" src=\"".$src."\" frameborder=\"0\" allowfullscreen></iframe>\n";
                        if ( isset($ytwerte[0]) ) $yt .= "</div>\n";
                        $replace = str_replace($opentag.$tagoriginal.$closetag,$yt,$replace);
                        break;
                    case "[/OYT]":
                        // para1 = ausrichtung, para2 = weite, para3 = hoehe, para4 = 2klick, para5 = youtube info anzeigen
                        $tagwerte = explode("]",$tagwert,2);
                        $ytwerte = explode(";",$tagwerte[0]);
                        if ( !isset($ytwerte[4]) ) $ytwerte[4] = null;
                        $ytalign = "left";
                        $ytinfo = "&showinfo=0";
                        if ( $ytwerte[0] == "r") $ytalign = "right";
                        if ( $ytwerte[4] == "-1") $ytinfo = "&showinfo=1";
                        $src = "//www.youtube.com/embed/".$tagwerte[1];
                        $yt = "<div class=\"youtube\" style=\"float:".$ytalign.";width:".$ytwerte[1]."\">";
                        if ( !isset($ausgaben["yt_counter"]) ) $ausgaben["yt_counter"] = null;;
                        $ausgaben["yt_counter"]++;
                        $ausgaben["yt_width"] = $ytwerte[1];
                        $ausgaben["yt_height"] = $ytwerte[2];
                        $ausgaben["yt_align"] = $ytalign;
                        $ausgaben["yt_id"] = $tagwerte[1];
                        $yt .= parser("youtube_head",'');
                        $yt .=  "<iframe width=\"100%\" height=\"".$ytwerte[2]."\" src=\"".$src."?autohide=1&wmode=opaque".$ytinfo."\"  frameborder=\"0\" allowfullscreen></iframe>";
                        $yt .= "</div>";
                        if ( $ytwerte[3] == -1 && @!$_COOKIE["youtube_access"]) {
                            $yt = parser("youtube",'');
                        }
                        $replace = str_replace($opentag.$tagoriginal.$closetag,$yt,$replace);
                        break;
                    case "[/VIDEO]":
                        $tagwerte = explode("]",$tagwert,2);
                        $ytwerte = explode(";",$tagwerte[0]);
                        if ( !isset($ytwerte[1]) ) $ytwerte[1] = "";
                        if ( !isset($ytwerte[2]) ) $ytwerte[2] = "";
                        $video =  "<video poster=\"".$ytwerte[2]."\" controls width=\"".$ytwerte[0]."\" height=\"".$ytwerte[1]."\"";
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
