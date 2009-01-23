<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "Seiten Umschalter bauen";
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

    // aufruf: $inhalt_selector = inhalt_selector( sql, position, menge, parameter, [art], [selektorenanzahl], [getvalues]);
    //         $inhalt_selector[0] = $ausgaben["inh_selektor"]
    //         $inhalt_selector[1] = $sql

    function inhalt_selector($sql, $position = 0, $menge, $parameter, $art = False, $selects = 6, $getvalues = False) {

        global $db, $debugging, $pathvars, $environment, $defaults;

        // default images setzen
        $defaults["select"]["none"] == "" ? $defaults["select"]["none"] = "<img src=\"/images/default/pos.png\" height=\"18\" width=\"24\" border=\"0\" align=\"top\" alt=\"\" />" : NOP;
        $defaults["select"]["prev"] == "" ? $defaults["select"]["prev"] = "<img src=\"/images/default/left.png\" height=\"18\" width=\"24\" border=\"0\" align=\"top\" alt=\"#(prev)\" title=\"#(prev)\" />" : NOP;
        $defaults["select"]["next"] == "" ? $defaults["select"]["next"] = "<img src=\"/images/default/right.png\" height=\"18\" width=\"24\" border=\"0\" align=\"top\" alt=\"#(next)\" title=\"#(next)\" />" : NOP;

        // muss ein integer sein, sonst geht der select daneben!
        $position = (int) $position;

        if ( $getvalues != False ) {
            $getvalues = "?".$getvalues;
        } else {
            $getvalues = "";
        }

        $result  = $db -> query($sql);
        $gesamt = $db -> num_rows($result);
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "Gesamt: ".$gesamt.$debugging["char"];

        $sql = $sql." LIMIT ".$position.",".$menge;
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
        $result  = $db -> query($sql);

        if ( $gesamt > $menge ) {
            $links = $position-$menge;
            $rechts = $position+$menge;
            if ( $position != 0 ) {
                $inh_selector .= "<a href=\"".$pathvars["virtual"].$environment["ebene"]."/".$environment["kategorie"].",".$links.$parameter.".html".$getvalues."\">".$defaults["select"]["prev"]."</a> ";
            } else {
                $inh_selector .= $defaults["select"]["none"];
            }


            // anzahl der seiten pro selktoren gruppe
            $selpages = $selects * $menge;

            // ups ... zu viele selektoren, neu berechnen
            if ( $selpages >= $gesamt ) {
                $newselpages = $gesamt / $menge + 1;
                $selpages = (int)$newselpages * $menge;
            }

            // nur wenn teilbar, naechster shift - ansonsten beibehalten
            $faktor = $position / $selpages;
            if ( is_int($faktor) ) {
                #echo "fak: ".$faktor."<br />";
                $shift  = $faktor * $selpages;
            } else {
                $faktor = (int)$faktor;
                #echo "fak: ".$faktor."<br />";
                $shift  = $faktor * $selpages;
            }

            // die start und stop werte setzen
            $dirselbeg = $dirselbeg + $shift;
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "beg: ".$dirselbeg.$debugging["char"];
            $dirselend = ( $selpages - ( $menge - 1 )) + $shift;
            if ( $dirselend > $gesamt ) {
                $dirselend = $gesamt;
            }
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "end: ".$dirselend.$debugging["char"];

            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "ges: ".$gesamt.$debugging["char"];
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "pos: ".$position.$debugging["char"];
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sft: ".$shift.$debugging["char"];
            if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sep: ".$selpages.$debugging["char"];

            for ( $j = $dirselbeg, $i = $dirselbeg; $i < $dirselend; $i+=$menge ) {
            #for ( $j = 0, $i = 0; $i < $gesamt; $i+=$menge ) {
                $j++;
                // meidow selector
                if ( $art == 1 ) {

                    $erster = $i+1;
                    $letzter = $i+$menge;

                    // wenn nötig auf den max wert stellen
                    if ( $letzter > $gesamt ) {
                        $letzter = $gesamt;
                    }

                    if ( $erster != 1 ) $trenner = "| ";
                    $label = $erster."-".$letzter;

                    if ( $faktor >= 1 && $j == $dirselbeg +1 ) {
                        $inh_selector .= "... ";
                    }
                    #if ( $dirselbeg+$shift <= $gesamt && $selpages < $gesamt ) $weiter = "| ...";
                    if ( $dirselbeg <= $gesamt && $selpages < $gesamt ) $weiter = "| ...";
                    #if ( $dirselend != $gesamt ) $weiter = "| ...";

                } else {
                    #$label = $j;
                    $label = $i / $menge + 1;
                }

                if ( $position == $i ) {
                    $inh_selector .= $trenner."<b>".$label."</b> ";
                    $selected = $label;
                } else {
                    $inh_selector .= $trenner."<a href=\"".$pathvars["virtual"].$environment["ebene"]."/".$environment["kategorie"].",".$i.$parameter.".html".$getvalues."\">".$label."</a> ";
                }
            }
            if ( $position <= $gesamt-$menge && $position != $gesamt-$menge ) {
                if ( $art == 1 ) {
                    $inh_selector .= $weiter;
                }
                $inh_selector .= "<a href=\"".$pathvars["virtual"].$environment["ebene"]."/".$environment["kategorie"].",".$rechts.$parameter.".html".$getvalues."\">".$defaults["select"]["next"]."</a> ";
            } else {
                $inh_selector .= $defaults["select"]["none"];
            }
        } elseif ( $gesamt > 0 ) {
            $selected = "1-".$gesamt;
        } else {
            $selected = "0";
        }

        // fix htdig looping
        if ( $position < 0 ) $inh_selector = "Error";

        $return[] = $inh_selector;
        $return[] = $sql;
        $return[] = $gesamt;
        $return[] = $selected;
        return $return;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
