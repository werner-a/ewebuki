<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script_name = "$Id$";
  $Script_desc = "recursiver template parser";
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

/*
  aufruf rparser("template-name.tem.html", "default-template.tem.html");
*/

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  function rparser($startfile, $default_template) {
    global $db, $debugging, $pathvars, $specialvars, $environment, $ausgaben, $element, $lnk, $dataloop, $mapping, $loopcheck;

    // original template find
    #$template = $pathvars["templates"].$startfile;
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
          if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "rparser note: template \"".$template."\" not found using: ".$default_template.$debugging["char"];
          // original template find
          #$template = $pathvars["templates"].$default_template;
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
    if ( file_exists($template) ) {
      $fd = fopen($template, "r");
        while (!feof($fd)) {
          $line = fgets($fd,1024);
          // alles vor ##begin und nach ##end wird nicht ausgegeben
          if (strstr($line,"##begin")) {
            $begin="1";
          } else {
            if (strstr($line,"##end")) {
              $begin="0";
            } elseif ($begin=="1") {

              // image path korrektur
              if ( strstr($line,"../../images/") ) {
                $line=str_replace("../../images/","/images/",$line);
              }

              // style path korrektur + dynamic style
              if ( (strstr($line,"../../css/".$environment["design"].".css"))) {
                if ( substr($specialvars["dynamiccss"],0,1) == "_" ) {
                    $stylename = $environment["design"].$specialvars["dynamiccss"];
                } elseif ( $specialvars["dynamiccss"] != "" ) {
                    $stylename = $specialvars["dynamiccss"];
                } else {
                    $stylename = $environment["design"];
                }
                $line=str_replace("../../css/".$environment["design"].".css",$pathvars["webcss"].$stylename.".css",$line);
              }

              // dynamic bg
              if ( strstr($line,"background=\"!#specialvars_dynamicbg\"") ) {
                if ( $specialvars["dynamicbg"] != "" ) {
                    $line=str_replace("background=\"!#specialvars_dynamicbg\"","background=\"/images/".$environment["design"]."/".$specialvars["dynamicbg"]."\"",$line);
                } else {
                    $line=str_replace("background=\"!#specialvars_dynamicbg\" ","",$line);
                }

              }

              // image language korrektur
              if ( strstr($line,"_".$specialvars["default_language"].".")
                && $environment["language"] != $specialvars["default_language"]
                && $environment["language"] != "" ) {

                $line=str_replace("_".$specialvars["default_language"].".","_".$environment["language"].".",$line);
              }

      //////////////////////////////////////////////////////////////////////////////////////////////
      // language "#(label)" - erster lauf: hier kommt der text anhand von sprache,
      //                       template und marke aus der datenbank
      //                       ( der content kann !#ausgaben_xxx enthalten )
      //////////////////////////////////////////////////////////////////////////////////////////////

              if ( strstr($line,"#(") ) {
                // wie heisst das template
                $tname = substr($startfile,0,strpos($startfile,".tem.html"));
                $line = content($line, $tname);
              }

      //////////////////////////////////////////////////////////////////////////////////////////////
      // variable "!#marke" - hier werden die variablen in die ausgabe eingebaut
      //////////////////////////////////////////////////////////////////////////////////////////////

                // !#ausgaben array pruefen und evtl. einsetzen
                if ( strstr($line,"!#ausgaben_" ) ) {
                    foreach($ausgaben as $name => $value) {
                        #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "parser info: \$ausgaben[$name]".$debugging["char"];
                        $line=str_replace("!#ausgaben_$name",$value,$line);
                    }
                }

                // !#element array pruefen und evtl. einsetzen
                if ( strstr($line,"!#element_" ) ) {
                    foreach( (array)$element as $name => $value) {
                        #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "parser info: \$element[$name]".$debugging["char"];
                        $line=str_replace("!#element_$name",$value,$line);
                    }
                }

                // !#lnk array pruefen und evtl. einsetzen
                // $lnk wird in kekse.inc.php erstellt
                if ( strstr($line,"!#lnk_" ) ) {
                    foreach( (array)$lnk as $name => $value) {
                        #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "parser info: \$lnk[$name]".$debugging["char"];
                        $line=str_replace("!#lnk_$name",$value,$line);
                    }
                }

                // ##loop-??? -> ##cont bereich bearbeiten
                // und inhalte aus $dataloop array einbauen
                if ( strstr($line,"##loop") ) {
                    $loop   = "1";
                    $mark  = explode("-",strstr($line,"##loop"),3);
                    $label  = $mark[1];
                    $buffer = "";
                } else {
                    if ( strstr($line,"##cont") ) {
                        $loop = "0";
                        $block = "";
                        $labelloop = $dataloop[$label];
                        foreach ( (array) $labelloop as $data ) {
                            $work = $buffer;
                            foreach ( (array)$data as $name => $value ) {
                                $work = str_replace("!{".$name."}",$value,$work);
                            }
                            $work = ereg_replace("!\{[0-9]+\}","&nbsp;",$work);
                            $block .= $work;
                        }
                        $line = $block.trim($line)."\n";
                    } elseif ( $loop == "1" ) {
                        $buffer .= trim($line)."\n";
                        continue;
                    }
                }

                if ( strstr($line,"!#" ) ) {
                    $line=str_replace("!#pathvars_webroot",$pathvars["webroot"],$line);
                    $line=str_replace("!#pathvars_menuroot",$pathvars["menuroot"],$line);
                    $line=str_replace("!#specialvars_pagetitle",$specialvars["pagetitle"],$line);
                    $line=str_replace("!#date",gerdate(),$line);
                    $line=str_replace("!#environment_kekse",$environment["kekse"],$line);
                    $line=str_replace("!#environment_katid",$environment["katid"],$line);
                    $line=str_replace("!#environment_subkatid",$environment["subkatid"],$line);
                    $line=str_replace("!#specialvars_phpsessid",$specialvars["phpsessid"],$line);
                }

      //////////////////////////////////////////////////////////////////////////////////////////////
      // language "#(label)" - zweiter lauf: hier kommt der text anhand von sprache,
      //                       template und marke aus der datenbank
      //                       ( wurde bei !#ausgaben_xxx ein #(label) eingebaut
      //                       wird auch dieses mit content versehen )
      //////////////////////////////////////////////////////////////////////////////////////////////

              if ( strstr($line,"#(") ) {
                // wie heisst das template
                $tname = substr($startfile,0,strpos($startfile,".tem.html"));
                $line = content($line, $tname);
              }

      //////////////////////////////////////////////////////////////////////////////////////////////
      // automatic "#{marke}" - rekursives !!!, automatisches einparsen von sub templates
      //////////////////////////////////////////////////////////////////////////////////////////////

              if ( strstr($line,"#{") ) {
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
                        if ( strstr($line,"#{main}") && $specialvars["dynlock"] == "" ) {
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
                while ((strstr($line,"#{"))) {
                  // wo beginnt die marke
                  $tokenbeg=strpos($line,"#{");
                  // wo endet die marke
                  $tokenend=strpos($line,"}",$tokenbeg); // loopfix
                  // wie lang ist die marke
                  $tokenlen=$tokenend-$tokenbeg;
                  // anfang der zeile merken
                  $lline=substr($line,0,$tokenbeg);
                  // ende der zeile merken
                  $rline=substr($line,$tokenend+1);
                  // token name extrahieren
                  $token_name=substr($line,$tokenbeg+2,$tokenlen-2);
                  // den token aus der zeile loeschen
                  $token_replace="#{".$token_name."}";
                  $line=str_replace($token_replace,"",$line);

                  if ( $specialvars["crc32"] == -1 ) {
                      if ( $environment["ebene"] != "" && $token_name == $environment["kategorie"] ) {
                        $path_element = explode("/", substr($environment["ebene"]."/",1));

                        foreach ( $path_element as $value ) {
                            array_pop($path_element); // thanks @ Günther Morhart
                            if ( $value != "" ) {
                                $find_ebene = "/".implode("/",$path_element);
                                $newstartfile = crc32($find_ebene).".".$token_name.".tem.html";
                                if ( !file_exists($pathvars["templates"].$newstartfile) ) {
                                  if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "no ".$newstartfile." crc32 template found for ebene (".$find_ebene.")".$debugging["char"];
                                } else {
                                  break; // thanks @ Günther Wach
                                }
                            } else {
                                global $HTTP_GET_VARS;
                                if ( $HTTP_GET_VARS["lost"] == "" ) {
                                    $newstartfile = crc32($environment["ebene"]).".".$token_name.".tem.html";
                                    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "reset to: ".$newstartfile." crc32 content for ebene (".$environment["ebene"].")".$debugging["char"];
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
                  rparser($newstartfile, $default_template);

                  if ( strstr($rline,"###switchback###") ) {
                      $db -> selectDb(DATABASE,FALSE);
                      #echo "<br />2: ".$db->getDb();
                      unset($specialvars["changed"]);
                      $rline = str_replace("###switchback###","",$rline);
                  }

                  // gemerktes zeilen ende ausgeben
                  echo rtrim($rline)."\n";
                }
              } else {
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
  }# ende der rcfilein funktion
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
