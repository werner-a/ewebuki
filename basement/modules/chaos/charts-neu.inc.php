<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "short description";
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

    // define der parameter
    $charts_dtime = $environment[subparam][1];
    if ( $debugging[html_enable] ) $debugging[ausgabe] .= "charts display time: ".$charts_dtime.$debugging[char];

    if ( $environment[subkatid] == "komplett" || $environment[subkatid] == "neu" ) {
        // select moeglichkeit erstellen
        $charts_itime_sql = "SELECT DISTINCT erstellt from mc_charts order by erstellt asc";
        $charts_itime_result  = $db -> query($charts_itime_sql);
        while ( $charts_itime_array = $db -> fetch_array($charts_itime_result,3) ) {
            if ( $charts_itime_array[erstellt] != "" ) {
                $charts_itime = substr($charts_itime_array[erstellt],0,10);
                #$charts_dtime_label = substr($charts_itime,8,2)."/".substr($charts_itime,5,2);
                $charts_dtime_label = strftime ("%V",  mktime(0,0,0,substr($charts_itime,5,2),substr($charts_itime,8,2),substr($charts_itime,0,4)));
                $output .= "<a href=\"".$pathvars[virtual]."/".$environment[katid]."/".$environment[subkatid].",".$charts_itime.".html\">".$charts_dtime_label."</a> ";
            }
        }
        $output .= "<br><br>";
        if ( $charts_dtime == "" ) {
            $charts_stime = $charts_itime;
        } else {
            $charts_stime = $charts_dtime;
        }
        if ( $debugging[html_enable] ) $debugging[ausgabe] .= "charts select time: ".$charts_stime.$debugging[char];
    }

    /* neueinsteiger
    SELECT mc_charts.erstellt, mc_charts.platz, mc_charts.newentry, mc_charts_interpret.interpret, mc_charts_titel.titel
    FROM (mc_charts INNER JOIN mc_charts_interpret ON mc_charts.interpretid = mc_charts_interpret.id)
    INNER JOIN mc_charts_titel ON mc_charts.titelid = mc_charts_titel.id
    WHERE (((mc_charts.newentry)="-1") AND ((mc_charts.erstellt)="2002-01-11 13:00:58"))
    ORDER BY mc_charts.platz;
    */

    $sql = "SELECT mc_charts.erstellt, mc_charts.platz, mc_charts.newentry, mc_charts_interpret.interpret, mc_charts_titel.titel
                      FROM (mc_charts INNER JOIN mc_charts_interpret ON mc_charts.interpretid = mc_charts_interpret.id)
                      INNER JOIN mc_charts_titel ON mc_charts.titelid = mc_charts_titel.id
                      WHERE (((mc_charts.newentry)='-1') AND ((mc_charts.erstellt) like '".$charts_stime."%'))
                      ORDER BY mc_charts.platz;";
    $top100result  = $db -> query($sql);
    while ( $top100array = $db -> fetch_array($top100result,3) ) {
        $output .= $top100array[platz]." ";
        $output .= $top100array[interpret]." - ";
        $output .= $top100array[titel]." ";
        if ( $top100array[2] == "-1" ) {
            $output .= " <b>[new]</b>";
        }
        $output .= "<br>";
    }
    $ausgaben[output] = $output;

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
