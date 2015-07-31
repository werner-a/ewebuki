<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// funtion_calendar.inc.php v1 emnili
// funktion loader: calendar
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

function calendar($monat="",$jahr="",$class="",$extendend="",$linked="",$no_secure="",$start_parameter=3,$inhalt="") {
    global $environment,$pathvars;
    $tage = array( "Mo", "Di", "Mi","Do", "Fr", "Sa","So");
    $monate = array( "Jan", "Feb", "M&auml;r", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez");
    $monate_full = array( "Januar", "Februar", "M&auml;rz", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");
    setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu');

    $aktuell = getdate();
    if ( $monat == "" && $jahr == "" ) {
        $jahr = $aktuell["year"];
        $monat = $aktuell["mon"];
    }

    if (  $environment["parameter"][$start_parameter+1] != "" ) {
        ( $environment["parameter"][$start_parameter+2] ) ? $monat = $environment["parameter"][$start_parameter+2] : $monat =1;
        #$monat = $environment["parameter"][$start_parameter+2];
        $jahr = $environment["parameter"][$start_parameter+1];
    }
    $jahr = min($jahr,'2035');
    $jahr = max($jahr,'1970');
    if ( !preg_match("/^[0-9]{4}$/",$jahr,$regs) ) $jahr = $aktuell["year"];
    if ( !preg_match("/^[0-9]{1,2}$/",$monat,$regs) ) $monat = $aktuell["mon"];

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

        for ( $i=1; $i<=$start_parameter;$i++ ) {
            ( $i == 1 ) ? $para = "" : $para = $environment["parameter"][$i];
            $protect_parameter .= ",".$para;
        }

        $jump_month_for = $monat+1;
        $jump_month_bac = $monat-1;
        $jump_year_bac = $jahr;
        $jump_year_for = $jahr;
        if ( $jump_month_bac == 0 ) {
            $jump_month_bac = 12;
            $jump_year_bac = $jahr-1;
        }
        if ( $jump_month_for == 13 ) {
            $jump_month_for = 1;
            $jump_year_for = $jahr+1;
        }

        $forward = $jahr+1;
        $back = $jahr-1;
        // bauen der monatstabelle
        $ausgabe = "#(ueberschrift)";
        $ausgabe .= "<table class=\"".$class." ".$class."_months\" >\n";
        $jump_back = "<a href=\"".$environment["parameter"][0].$protect_parameter.",".$back.".html\">";
        $jump_forward = "<a href=\"".$environment["parameter"][0].$protect_parameter.",".$forward.".html\">";

        $jump_month_back = "<a href=\"".$environment["parameter"][0].$protect_parameter.",".$jump_year_bac.",".$jump_month_bac.".html\">";
        $jump_month_forward = "<a href=\"".$environment["parameter"][0].$protect_parameter.",".$jump_year_for.",".$jump_month_for.".html\">";

        // SECURITY
        $SecureYear = 1;
        if ( $no_secure > $SecureYear ) $SecureYear = $no_secure;
        if (is_numeric($environment["parameter"][$start_parameter+1]) && abs($aktuell["year"] - $environment["parameter"][$start_parameter+1]) > $SecureYear) {
            header("Location: ".$pathvars["virtual"]."/index.html");
        }
        if ( $back-$aktuell["year"] < -$SecureYear) {
            $jump_back = "";
        }
        if ( $forward-$aktuell["year"] > $SecureYear) {
            $jump_forward = "";
        }

        if ( $jump_year_bac-$aktuell["year"] < -$SecureYear) {
            $jump_month_back = "";
        }
        if ( $jump_year_for-$aktuell["year"] > $SecureYear) {
            $jump_month_forward = "";
        }

        $ausgabe .= "<tr class=\"first_line\"><td class=\"first\">".$jump_back."<img src=\"/images/default/left.png\" alt=\"\" /></a></td><td style=\"text-align:center\" colspan=\"2\"><b><a href=\"".$environment["parameter"][0].$protect_parameter.",".$jahr.".html\">".$jahr."</a></b></td><td style=\"text-align:right\" class=\"last\">".$jump_forward."<img src=\"/images/default/right.png\" alt=\"\" /></a></td></tr>\n";
        $ausgabe .= "<tr class=\"first_line\" >\n";
        foreach ( $monate as $key => $value ) {
            $month = $key+1;
            if ($month > 12) $month = $month-12;
            if ( $linked == -1 || $linked == -2 ) {
                $value = "<a href=\"".$environment["parameter"][0].$protect_parameter.",".$heute["year"].",".$month.".html\">".$value."</a>";
            }
            $class_m = "";
            if ( is_int($key/4)  ) {
                if ( $key != 0 ) $ausgabe .= "</tr><tr>";
                $class_m = "first";
            }
            if ( is_int(($key+1)/4) ) {
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
    $mon_out = preg_replace("/^0/","",strftime ("%m", $heute[0]));
    $ausgabe .= "<colgroup><col width=\"14%\"><col width=\"14%\"><col width=\"14%\"><col width=\"14%\"><col width=\"14%\"><col width=\"14%\"><col width=\"14%\"></colgroup>";
    $ausgabe .= "<thead><tr><th>".$jump_month_back."<img src=\"/images/default/left.png\" alt=\"\" /></img></a></th><th style=\"text-align:center\" colspan=\"5\" scope=\"col\" class=\"monat\">".$monate_full[$mon_out-1]."</th><th>".$jump_month_forward."<img src=\"/images/default/right.png\" alt=\"\" /></th></tr>";
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
            if ( $int_counter == $heute["mday"]) {
                $stop = -1;
                break;
            }
            // ersten und letzten tag kennzeichnen
            if ( $key == 0 ) {
                $class = "first";
            } elseif ( $key == 6 ) {
                $class = "last";
            } else {
                $class = "";
            }
            $counter++;
            $style = "";
            $onclick = "";
            if ( $counter > $start && $counter <= ($heute["mday"]+$start) ) {
                $int_counter++;
                $timestamp =mktime(0,0,0,$monat,$int_counter,$jahr);
                if (is_array($inhalt) ) {
                    foreach ( $inhalt as $key => $value ) {
                        $int_array = key($value);
                        if ( $timestamp >= key($value)  && $timestamp <= $value[$int_array]["end"] )  {
                            $style = " title=\"".$value[$int_array]["name"]."\" style=\"font-weight:bold;color:white;background-color:".$value[$int_array]["color"]."\"";
                            $onclick="onclick=\"jQuery('#dialog".$value[$int_array]["id"]."').dialog ()\"";
                        }
                    }
                }
            } else {
                $int_counter = "";
            }
            ( $aktuell["mday"] == $int_counter && $aktuell["mon"] == $monat && $aktuell["year"] == $jahr) ? $class_today=" today " : $class_today="";
            $out = $int_counter;
            if ( $int_counter != "" && $linked == -1 ) {
                $out = "<a href=\"".$environment["parameter"][0].$protect_parameter.",".$heute["year"].",".$heute["mon"].",".$int_counter.".html\">".$int_counter."</a>";
            }
            $ausgabe .= "<td ".$onclick." ".$style." class=\"".$class.$class_today."\">".$out."</td>";
        }
        $ausgabe .= "</tr>";
    }

    $ausgabe .= "</tbody>";
    $ausgabe .= "</table>";

    return $ausgabe;

}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
