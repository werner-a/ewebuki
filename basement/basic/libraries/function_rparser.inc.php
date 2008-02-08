<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script_name = "$Id$";
  $Script_desc = "recursiver template parser";
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

/*
  aufruf rparser("template-name.tem.html", "default-template.tem.html");
*/

////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function rparser($startfile, $default_template, $overwrite_template="") {

        global $db, $debugging, $pathvars, $specialvars, $environment, $ausgaben, $element, $lnk, $dataloop, $hidedata, $mapping, $loopcheck;

        if ( $overwrite_template == "" ) {
            if ( file_exists($pathvars["templates"].$startfile) ) {
                $template = $pathvars["templates"].$startfile;
            } else {
                $template = $pathvars["fileroot"]."templates/default/".$startfile;
            }
            // wenn es fuer eine unterseite kein eigenes template gibt default.tem.html verwenden.
            if ( !file_exists($template) && $default_template != "" ) {
                if ( $startfile == $loopcheck ) {
                    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "rparser note: template \"".$template."\" not found. Loop detect!!!".$debugging["char"];
                } else {
                    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "rparser note: template \"".$template."\" not found, using: ".$default_template.$debugging["char"];
                    if ( file_exists($pathvars["templates"].$default_template) ) {
                        $template = $pathvars["templates"].$default_template;
                    } else {
                        $template = $pathvars["fileroot"]."templates/default/".$default_template;
                    }
                }
                $loopcheck = $startfile;
            } else {
                unset($loopcheck);
            }
        } else {
            if ( file_exists($pathvars["templates"].$overwrite_template) ) {
                $template = $pathvars["templates"].$overwrite_template;
            } else {
                $template = $pathvars["fileroot"]."templates/default/".$overwrite_template;
            }
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "rparser note: template \"".$startfile."\" overwrite, using: ".$template.$debugging["char"];
        }
        // reset template overwrite inner recursive procedure
        $overwrite_template = "";

        if ( file_exists($template) ) {
            $fd = fopen($template, "r");
            while (!feof($fd)) {
                $line = fgets($fd,1024);
                // alles vor ##begin und nach ##end wird nicht ausgegeben
                if ( strpos($line,"##begin")  !== false ) {
                    $begin="1";
                } else {
                    if ( strpos($line,"##end") !== false ) {
                        $begin="0";
                    } elseif ($begin=="1") {

                        // style path korrektur + dynamic style
                        if ( strpos($line,"css/".$environment["design"]."/") !== false ) {
                            if ( $specialvars["dynamiccss"] != "" ) {
                                $line = str_replace("_default","_".$specialvars["dynamiccss"],$line);
                            }
                            $line = str_replace("../../css/",$pathvars["subdir"]."/css/",$line);
                        } elseif ( strpos($line,"../../css/") !== false ) {
                            $line = str_replace("../../css/",$pathvars["subdir"]."/css/",$line);
                        }

                        // image path korrektur (subdir support siehe weiter unten)
                        if ( strpos($line,"../../images/") !== false ) {
                            $line=str_replace("../../images/","/images/",$line);
                        }

                        // js path korrektur
                        if ( strpos($line,"../../js/") !== false ) {
                            $line=str_replace("../../js/",$pathvars["subdir"]."/js/",$line);
                        }

                        // dynamic bg
                        if ( strpos($line,"background=\"!#specialvars_dynamicbg\"") !== false ) {
                            if ( $specialvars["dynamicbg"] != "" ) {
                                $line=str_replace("background=\"!#specialvars_dynamicbg\"","background=\"/images/".$environment["design"]."/".$specialvars["dynamicbg"]."\"",$line);
                            } else {
                                $line=str_replace("background=\"!#specialvars_dynamicbg\" ","",$line);
                            }

                        }

                        // image language korrektur
                        if ( strpos($line,"_".$specialvars["default_language"].".") !== false
                            && $environment["language"] != $specialvars["default_language"]
                            && $environment["language"] != "" ) {

                            $line=str_replace("_".$specialvars["default_language"].".","_".$environment["language"].".",$line);
                        }

                //////////////////////////////////////////////////////////////////////////////////////////////
                // language "#(label)" - erster lauf: hier kommt der text anhand von sprache,
                //                       template und marke aus der datenbank
                //                       ( der content kann !#ausgaben_xxx enthalten )
                //////////////////////////////////////////////////////////////////////////////////////////////

                        if ( strpos($line,"#(") !== false || strpos($line,"g(") !== false ) {
                            // wie heisst das template
                            $tname = substr($startfile,0,strpos($startfile,".tem.html"));
                            $line = content($line, $tname);
                        }

                //////////////////////////////////////////////////////////////////////////////////////////////
                // variable "!#marke" - hier werden die variablen in die ausgabe eingebaut
                //////////////////////////////////////////////////////////////////////////////////////////////

                        // !#ausgaben array pruefen und evtl. einsetzen
                        if ( strpos($line,"!#ausgaben_" ) !== false ) {
                            foreach($ausgaben as $name => $value) {
    // php5 only
    //                             $line = str_replace("!#ausgaben_$name", $value, $line, &$count);
    //                             if ( $count > 0 ) {
    //                                 if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "parser info (ausgaben): ".$name.$debugging["char"];
    //                             }
                                $line = str_replace("!#ausgaben_$name", $value, $line);
                                #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "parser info (ausgaben): ".$name.$debugging["char"];
                            }
                        }

                        // !#element array pruefen und evtl. einsetzen
                        if ( strpos($line,"!#element_" ) !== false ) {
                            foreach( (array)$element as $name => $value) {
                                $line = str_replace("!#element_$name", $value, $line);
                                #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "parser info (element): ".$name." : ".$element[$name].$debugging["char"];
                            }
                        }

                        // !#environment array pruefen und evtl. einsetzen
                        if ( strpos($line,"!#environment_" ) !== false ) {
                            foreach( (array)$environment as $name => $value) {
                                $line = str_replace("!#environment_$name", $value, $line);
                                #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "parser info (environment): ".$name." : ".$environment[$name].$debugging["char"];
                            }
                        }

                        // !#pathvars array pruefen und evtl. einsetzen
                        if ( strpos($line,"!#pathvars_" ) !== false ) {
                            foreach( (array)$pathvars as $name => $value) {
                                $line = str_replace("!#pathvars_$name", $value, $line);
                                #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "parser info (pathvars): ".$name." : ".$pathvars[$name].$debugging["char"];
                            }
                        }

                        // !#lnk array pruefen und evtl. einsetzen
                        // $lnk wird in kekse.inc.php erstellt
                        if ( strpos($line,"!#lnk_" )  !== false ) {
                            foreach( (array)$lnk as $name => $value) {
                                $line = str_replace("!#lnk_$name", $value, $line);
                                #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "parser info (lnk): ".$name." : ".$lnk[$name].$debugging["char"];
                            }
                        }

                        // ##loop-??? -> ##cont bereich bearbeiten
                        // und inhalte aus $dataloop array einbauen
                        if ( strpos($line,"##loop")  !== false ) {
                            $loop   = "1";
                            $loop_mark   = explode("-",strstr($line,"##loop"),3);
                            $loop_label  = $loop_mark[1];
                            $loop_buffer = "";
                        } else {
                            if ( strpos($line,"##cont") !== false ) {
                                $loop  = "0";
                                $loop_block = "";
                                $labelloop = $dataloop[$loop_label];
                                foreach ( (array) $labelloop as $data ) {
                                    $loop_work = $loop_buffer;
                                    foreach ( (array)$data as $name => $value ) {
                                        $loop_work = str_replace("!{".$name."}",$value,$loop_work);
                                    }
                                    $loop_work = ereg_replace("!\{[0-9a-zA-Z]+\}","&nbsp;",$loop_work);
                                    $loop_block .= $loop_work;
                                }
                                $line = $loop_block.trim($line)."\n";
                            } elseif ( $loop == "1" ) {
                                $loop_buffer .= trim($line)."\n";
                                continue;
                            }
                        }

                        // ##hide-??? - ##show bereich bearbeiten
                        // nur wenn $hidedata["???"] verfuegbar ist einblenden
                        if ( strpos($line,"##hide") !== false ) {
                            $hide   = "1";
                            $hide_mark   = explode("-",strstr($line,"##hide"),3);
                            $hide_label  = $hide_mark[1];
                            $hide_buffer = "";
                            continue; // marke ebenfalls kicken!
                        } else {
                            if ( strpos($line,"##show") !== false ) {
                                $hide  = "0";
                                $hide_block = "";
                                if ( is_array($hidedata[$hide_label]) ) {
                                    foreach ( $hidedata[$hide_label] as $name => $value ) {
                                        $hide_buffer = str_replace("!{".$name."}",$value,$hide_buffer);
                                    }
                                    $hide_block = ereg_replace("!\{[0-9a-zA-Z]+\}","&nbsp;",$hide_buffer);
                                }
                                #$line = $block.trim($line)."\n";
                                $line = $hide_block; // marke ebenfalls kicken!
                            } elseif ( $hide == "1" ) {
                                $hide_buffer .= trim($line)."\n";
                                continue;
                            }
                        }

                        if ( strpos($line,"!#") !== false && strpos($line,"<textarea") === false) {
                            $line=str_replace("!#date",gerdate(),$line);
                            $line=str_replace("!#specialvars_pagetitle",$specialvars["pagetitle"],$line);
                            $line=str_replace("!#specialvars_phpsessid",$specialvars["phpsessid"],$line);
                        }

                //////////////////////////////////////////////////////////////////////////////////////////////
                // language "#(label)" - zweiter lauf: hier kommt der text anhand von sprache,
                //                       template und marke aus der datenbank
                //                       ( wurde bei !#ausgaben_xxx ein #(label) eingebaut
                //                       wird auch dieses mit content versehen )
                //////////////////////////////////////////////////////////////////////////////////////////////

                        if ( strpos($line,"#(") !== false || strpos($line,"g(") !== false ) {
                            // wie heisst das template
                            $tname = substr($startfile,0,strpos($startfile,".tem.html"));
                            $line = content($line, $tname);
                        }

                //////////////////////////////////////////////////////////////////////////////////////////////
                // subdir support images
                //////////////////////////////////////////////////////////////////////////////////////////////

                        if ( $pathvars["subdir"] != "" ) {
                            // images in templates + funktionen
                            $line = str_replace("/images/",$pathvars["subdir"]."/images/",$line);
                            // images im content aber nur bei der ausgabe (nicht im cms editor und im filesystem (magic.php))
    #                        if ( strpos($line,"=".$cfg["file"]["base"]["webdir"]) === false && strpos($line,$cfg["file"]["base"]["maindir"]) === false ) {
                            if ( strpos($line,"textarea") === false && strpos($line,$cfg["file"]["base"]["maindir"]) === false ) {
                                $line = str_replace($cfg["file"]["base"]["webdir"],$pathvars["subdir"].$cfg["file"]["base"]["webdir"],$line);
                            } #else {
                            #    echo "####".$line."####";
                            #}
                        }

                //////////////////////////////////////////////////////////////////////////////////////////////
                // automatic "#{marke}" - rekursives !!!, automatisches einparsen von sub templates
                //////////////////////////////////////////////////////////////////////////////////////////////

                        if ( strpos($line,"#{") !== false ) {
                            // tausche wenn nötig die inhalte aus
                            if ( isset($mapping) ) {
                                foreach($mapping as $name => $value) {
                                    #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "parser info: #{".$name."}:"."#{".$value."}".$debugging["char"];

                                    // evtl. globaler print button
                                    #if ( strstr($line,"#{main") ) {
                                    #  global $HTTP_GET_VARS;
                                    #  if ( $HTTP_GET_VARS["print"] != true ) $print = "<table cellpadding=\"0\" cellspacing=\"0\" width=\"660\"><tr><td width=\"16\">&nbsp;</td><td width=\"628\" align=\"right\"><a href=\"".$pathvars["uri"]."?print=true\">Print Ausgabe</a></td><td width=\"16\">&nbsp;</td></tr></table>";
                                    #  if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "print schalter: ".$environment["template"].$debugging["char"];
                                    #}
                                    #$line = str_replace("#{".$name."}","#{".$value."}".$print,$line);
                                    #if ( strstr($line,"#{main}") ) {

                                    // datenbank wechseln -> variablen in menuctrl.inc.php
                                    if ( strpos($line,"#{main}")  !== false && $specialvars["dynlock"] == "" ) {
                                        if ( $environment["fqdn"][0] == $specialvars["dyndb"] ) {
                                            $db->selectDb($specialvars["dyndb"],FALSE);
                                            #echo "1: ".$db->getDb();
                                            $specialvars["changed"] = "###switchback###";
                                        }
                                    }

                                    $line = str_replace("#{".$name."}","#{".$value."}".$specialvars["changed"],$line);
                                }
                            }

                            // marke aus der zeile schneiden und anfang und ende merken
                            while ( strpos($line,"#{") !== false ) {
                                // wo beginnt die marke
                                $tokenbeg = strpos($line,"#{");
                                // wo endet die marke
                                $tokenend = strpos($line,"}",$tokenbeg); // loopfix
                                // wie lang ist die marke
                                $tokenlen = $tokenend-$tokenbeg;
                                // anfang der zeile merken
                                $lline = substr($line,0,$tokenbeg);
                                // ende der zeile merken
                                $rline = substr($line,$tokenend+1);
                                // token name extrahieren
                                $token_name=substr($line,$tokenbeg+2,$tokenlen-2);
                                // den token aus der zeile loeschen
                                $token_replace="#{".$token_name."}";
                                $line=str_replace($token_replace,"",$line);

                                if ( $specialvars["crc32"] == -1 ) {
                                    if ( $environment["ebene"] != "" && $token_name == $environment["kategorie"] ) {
                                        // das normale template ist!
                                        $newstartfile = crc32($environment["ebene"]).".".$token_name.".tem.html";
                                        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "crc32 template/content basis: ".$newstartfile." for ebene (".$environment["ebene"].")".$debugging["char"];
                                        // gibt es ein overwrite template?
                                        $path_element = explode("/", substr($environment["ebene"]."/",1).$environment["kategorie"]);
                                        foreach ( $path_element as $value ) {
                                            $find_kategorie =  array_pop($path_element);
                                            if ( $value != "" ) {
                                                $find_ebene = "/".implode("/",$path_element);
                                                if ( $find_ebene != "/" ) {
                                                    $overwrite_template = crc32($find_ebene).".".$find_kategorie.".tem.html";
                                                } else {
                                                    $overwrite_template = $find_kategorie.".tem.html";
                                                }
                                                if ( !file_exists($pathvars["templates"].$overwrite_template) ) {
                                                    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "crc32 overwrite template search: ".$overwrite_template." for ebene (".$find_ebene.")".$debugging["char"];
                                                    $overwrite_template = "";
                                                } else {
                                                    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "crc32 overwrite template found: ".$overwrite_template." for ebene (".$find_ebene.")".$debugging["char"];
                                                    break;
                                                }
                                            }
                                        }
                                    } else {
                                        // token name und template endung zusammen bauen
                                        $newstartfile = $token_name.".tem.html";
                                    }
                                } else {
                                    // ist das eine sub kategorie ?
                                    if ( $token_name == $environment["katid"] && $environment["subkatid"] != "" ) {
                                        // token name und template endung zusammen bauen
                                        $newstartfile = $token_name.".".$environment["subkatid"].".tem.html";
                                        // es gibt kein besonderes template
                                        if ( !file_exists($pathvars["templates"].$newstartfile)) {
                                            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "no ".$newstartfile." template found using ".$token_name.".tem.html".$debugging["char"];
                                            $newstartfile = $token_name.".tem.html";
                                        }
                                    } else {
                                        // token name und template endung zusammen bauen
                                        $newstartfile = $token_name.".tem.html";
                                    }
                                }

                                // gemerkten zeilen anfang ausgeben
                                echo ltrim($lline);
                                // parser nochmal aufrufen um untertemplate mit dem namen: "$token".tem.html zu parsen
                                rparser($newstartfile, $default_template, $overwrite_template);

                                // reset template overwrite outer recursive procedure
                                $overwrite_template = "";

                                if ( strpos($rline,"###switchback###") !== false ) {
                                    $db -> selectDb(DATABASE,FALSE);
                                    #echo "<br />2: ".$db->getDb();
                                    unset($specialvars["changed"]);
                                    $rline = str_replace("###switchback###","",$rline);
                                }

                                // gemerktes zeilen ende ausgeben
                                echo rtrim($rline)."\n";
                            }
                        } else {
                            // eWeBuKi tag schutz part 4
                            $mark_o = array( "#(", "g(", "#{", "!#" );
                            $hide_o = array( "::1::", "::2::", "::3::", "::4::" );
                            $line = str_replace( $hide_o, $mark_o, $line );

                            // da keine marken fuer sub templates da waren zeile unveraendert ausgeben
                            echo trim($line)."\n";
                        } # ende automatic "#{marke}"
                    } # hier passiert alles bevor ##end
                } # ende zeile enthaelt kein ##begin
            } # ende der file while schleife
            fclose($fd);
        } else {
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "rparser error: template ".$template." not found!!!".$debugging["char"];
        } # ende der file existenz pruefung
    }# ende der rparser funktion

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
