<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: function_calendar.inc.php 1131 2007-12-12 08:45:50Z chaot $";
// "funktion loader";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2007 Werner Ammon ( wa<at>chaos.de )

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

function calendar($monat="",$jahr="",$class="",$extendend="") {

    $tage = array( "Mo", "Di", "Mi","Do", "Fr", "Sa","So");
    $monate = array( "Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez");
    setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu');

    $aktuell = getdate();
    if ( $monat == "" && $jahr == "" ) {
        $jahr = $aktuell["year"];
        $monat = $aktuell["mon"];
    }

    $heute = getdate(mktime(0, 0, 0, ($monat+1), 0, $jahr));

    // start-tag
    $start = mktime ( 0, 0, 0, $heute["mon"], 1, $heute["year"] );
    $start = getdate($start);
    $start =  $start["wday"];
    // das hier ist notwendig um den sonntag nach hinten zu verschieben
    if ( $start == 0 ) $start = 7;
    $start = $start -1;
    // start-tag

    if ( $extendend == -1 ) {
        // array mit den monaten zurechtlegen
        for ( $i=1;$i<$heute["mon"];$i++ ) {
            $shift = array_shift($monate);
            array_push($monate,$shift);
        }
        // bauen der monatstabelle
        $ausgabe = "<table class=\"".$class." ".$class."_months\" >\n";
        $ausgabe .= "<tr class=\"first_line\" >\n";
        foreach ( $monate as $key => $value ) {
            $class_m = "";
            if ( is_int($key/4)  ) {
                if ( $key != 0 ) $ausgabe .= "</tr><tr>";
                $class_m = "first";
            }
            if ( !strstr($key/4-0.75,",") ) {
                $class_m = "last";
            }
            $ausgabe .= "<td class=\"".$class_m."\">".$value."</td>";
        }
        $ausgabe .= "</tr></table>";
    }

    $ausgabe .= "<table class=\"".$class."\">";
    $counter=0;
    $int_counter = "";

    // bauen der tabellenbeschriftung
    $ausgabe .= "<thead><tr><th colspan=\"7\" scope=\"col\" class=\"monat\">".strftime ("%B", $heute[0])."</th></tr>";
    $ausgabe .= "<tr>";
    foreach ( $tage as $key => $value ) {
        // ersten und letzten tag kennzeichnen
        if ( $key == 0 ) {
            $class = "first";
        } elseif ( $key == 6 ) {
            $class = "last";
        } else {
            $class = "";
        }
        $ausgabe .= "<th scope=\"col\" class=\"".$class."\">".$value."</th>";
    }
    $ausgabe .= "</tr></thead>\n";
    // bauen er tabellenbeschriftung

    $ausgabe .= "<tbody>";
    while ( $stop != "-1" ) {
        $ausgabe .= "<tr>";
        foreach ( $tage as $key => $value ) {
            // ersten und letzten tag kennzeichnen
            if ( $key == 0 ) {
                $class = "first";
            } elseif ( $key == 6 ) {
                $class = "last";
            } else {
                $class = "";
            }
            $counter++;
            if ( $counter > $start && $counter <= ($heute["mday"]+$start) ) {
                $int_counter++;
            } else {
                $int_counter = "";
            }
            ( $aktuell["mday"] == $int_counter ) ? $class_today=" today " : $class_today="";
            $ausgabe .= "<td class=\"".$class.$class_today."\">".$int_counter."</td>";
        }
        $ausgabe .= "</tr>";
        if ( $counter >= $heute["mday"]+7) $stop = -1;
    }
    $ausgabe .= "</tbody>";
    $ausgabe .= "</table>";

    return $ausgabe;

}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>