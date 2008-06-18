<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $script_name = "$Id$";
    $Script_desc = "parser for sub templates";
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
    environment:
    ============

    objekte:   $db
    arrays:    $debugging, $pathvars, $specialvars, $environment, $ausgaben
    variablen: $marke ( z.B. bei !{marke} )

    beispiel:
    =========

    $ausgaben["funktion"] = parser( "$parse_name", "$parse_path/");

    $parse_name  = "name";                              (str) haupbestandteil template
    $parse_path  = "main/";                             (str) pfad des template

    require "data/parser.inc.php";                      include dieser funktion
*/

////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    function parser($parse_name, $parse_path) {

        // variableninit
        global $db, $debugging, $pathvars, $specialvars, $environment, $ausgaben,$dataloop,$hidedata;

        // original template find
        #$template = $pathvars["templates"].$parse_path.$parse_name.".tem.html";
        if ( file_exists($pathvars["templates"].$parse_path.$parse_name.".tem.html") ) {
          $template = $pathvars["templates"].$parse_path.$parse_name.".tem.html";
        } else {
          $template = $pathvars["fileroot"]."templates/default/".$parse_path.$parse_name.".tem.html";
        }

        // file auf existenz ueberpruefen
        if ( file_exists($template) == -1 ) {
            $fd = fopen($template,"r");

            $ii = 0;
            $parse_print = 0;
            $parse_mod = "";
            $parse_out = "";

            // wenn "disabled" uebergeben wurde, parser ausgabe generell aktivieren
            #if ( $parse_marke == "disabled" ) $parse_print = 1;

            // template parser

            while ( !feof($fd) ) {
                $parse_mod = fgets($fd,1024);
                #if ( strstr ($parse_mod, "##begin") ) $parse_print = 1;
                #if ( $parse_print == 1  ) {

                // alles vor ##begin und nach ##end wird nicht ausgegeben
                if ( strpos($parse_mod,"##begin") !== false ) {
                    $parse_print="1";
                } else {
                    if ( strpos($parse_mod,"##end") !== false ) {
                        $$parse_print="0";
                    } elseif ($parse_print=="1") {

                    // !#ausgaben array pruefen und evtl. einsetzen
                    if ( strpos($parse_mod,"!#ausgaben_" ) !== false ) {
                        foreach($ausgaben as $name => $value) {
                            #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "parser info: \$ausgaben[$name]".$debugging["char"];
                            $parse_mod = str_replace("!#ausgaben_$name",$value,$parse_mod);
                        }
                    }

                    // !#element array pruefen und evtl. einsetzen
                    if ( strpos($parse_mod,"!#element_" ) !== false ) {
                        if ( is_array($element) ) {
                            foreach($element as $name => $value) {
                                #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "parser info: \$element[$name]".$debugging["char"];
                                $parse_mod = str_replace("!#element_$name",$value,$parse_mod);
                            }
                        }
                    }

                    if ( strpos($parse_mod,"##loop")  !== false ) {
                        $loop   = "1";
                        $loop_mark   = explode("-",strstr($parse_mod,"##loop"),3);
                        $loop_label  = $loop_mark[1];
                        $loop_buffer = "";
                        continue; // marke ebenfalls kicken!
                    } else {
                        if ( strpos($parse_mod,"##cont") !== false ) {
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
                            $parse_mod = $loop_block.trim($parse_mod)."\n";
                        } elseif ( $loop == "1" ) {
                            $loop_buffer .= trim($parse_mod)."\n";
                            continue;
                        }
                    }

                    // ##hide-??? - ##show bereich bearbeiten
                    // nur wenn $hidedata["???"] verfuegbar ist einblenden
                    if ( strpos($parse_mod,"##hide") !== false ) {
                        $hide   = "1";
                        $hide_mark   = explode("-",strstr($parse_mod,"##hide"),3);
                        $hide_label  = $hide_mark[1];
                        $hide_buffer = "";
                        continue; // marke ebenfalls kicken!
                    } else {
                        if ( strpos($parse_mod,"##show") !== false ) {
                            $hide  = "0";
                            $hide_block = "";
                            if ( is_array($hidedata[$hide_label]) ) {
                                foreach ( $hidedata[$hide_label] as $name => $value ) {
                                    $hide_buffer = str_replace("!{".$name."}",$value,$hide_buffer);
                                }
                                $hide_block = ereg_replace("!\{[0-9a-zA-Z]+\}","&nbsp;",$hide_buffer);
                            }
                            #$line = $block.trim($line)."\n";
                            $parse_mod = $hide_block; // marke ebenfalls kicken!

                        } elseif ( $hide == "1" ) {
                            $hide_buffer .= trim($parse_mod)."\n";

                            continue;
                        }
                    }

                    // hier wird automatisch die variable $marke eingespult
                    while ( strpos($parse_mod, "!{") !== false ) {
                        // wo beginnt die marke
                        $markbeg = strpos($parse_mod,"!{");
                        // wo endet die marke
                        $markend = strpos($parse_mod,"}",$markbeg); // loopfix
                        // wie lang ist die marke
                        $marklen = $markend-$markbeg;
                        // token name extrahieren
                        $marke = substr($parse_mod,$markbeg+2,$marklen-2);

                        global $$marke;
                        $parse_mod = str_replace("!{".$marke."}",$$marke,$parse_mod);
                    }

                    // hier alles eintragen was einmal pro zeile passieren soll

                    // image path anpassen
                    if ( strpos($parse_mod,"../../images/") !== false ) {
                        $parse_mod=str_replace("../../images/","/images/",$parse_mod);
                    }

                    // image language korrektur
                    if ( strpos($parse_mod,"_".$specialvars["default_language"].".") !== false
                        && $environment["language"] != $specialvars["default_language"]
                        && $environment["language"] != "" ) {

                        $parse_mod=str_replace("_".$specialvars["default_language"].".","_".$environment["language"].".",$parse_mod);
                    }

                    //////////////////////////////////////////////////////////////////////////////////////////////
                    // language "#(label)" - hier kommt der text anhand von sprache,
                    //                       template und marke aus der datenbank
                    //////////////////////////////////////////////////////////////////////////////////////////////

                    if ( strpos($parse_mod,"#(") !== false || strpos($parse_mod,"g(") !== false ) {
                         // wie heisst das template
                         $tname = substr($startfile,0,strpos($startfile,".tem.html"));
                         $parse_mod = content($parse_mod, $parse_name);
                    }

                    //////////////////////////////////////////////////////////////////////////////////////////////

                    $parse_out .= $parse_mod;
                }
            }
            if ( strpos($parse_mod,"##end") !== false ) $parse_print = 0;
        }
        // variable ausgabe variable erstellen
        $parse_vari = "$parse_name"."_out";
        $$parse_vari .= $parse_out;

        // parse marke fuer spaetere verwendung zurueck setzen
        unset($parse_marke);

        } else {
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "parser note: file \"".$template."\" existiert nicht!".$debugging["char"];
        }
        return $parse_out;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
