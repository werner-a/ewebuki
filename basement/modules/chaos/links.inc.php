<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $script_name = "$Id$";
    $Script_desc = "links verwaltung";
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

  if ( $debugging[html_enable] ) $debugging[ausgabe] .= "[ ** $script_name ** ]".$debugging[char];

  if ( $debugging[html_enable] ) $debugging[ausgabe] .= "parameter 1: ".$subparam[1].$debugging[char];
    #$menu_output  = "&nbsp;&nbsp;dyn menu<br>";




  if ( $environment[subkatid] == "display" ) {

    // define der subparameter
    $links_katid = $environment[subparam][1];
    if ($debug) $debuginfo = $debuginfo."katid: ".$links_katid.$debug_chr;


    // submenu output erstellen
    #$submenu_output .= "&nbsp;&nbsp;&nbsp;&nbsp;create<br>";


    // submenu output erstellen
      if (!$links_katid) {
        $links_pattern = "katid NOT REGEXP '-'";
      } else {
        $links_pattern = "katid REGEXP '^".$links_katid."-[0-9]+$'";
      }
    if ($debug) $debuginfo = $debuginfo."pattern: ".$links_pattern.$debug_chr;

    $links_kekse = "<h1>Lesezeichen<h1>";

    $links_kategorie_sql = "select katid, kattitel from db_link_kat where $links_pattern order by 'kattitel'";
    $links_kategorie_result  = $db -> query($links_kategorie_sql);

    if ( $links_katid != "" ) {
        if ( strstr($links_katid,"-") ) {
          $links_prekategorie_cut = strrpos($links_katid, "-");
          $links_prekategorie = substr($links_katid, 0, $links_prekategorie_cut);
          $links_prekategorie_link = "display,".$links_prekategorie.".html";
        } else {
           $links_prekategorie_link = "display.html";
      }
      #$submenu_output .= "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"".$links_prekategorie_link."\">..</a><br>";
      $links_complete .= "<a href=\"".$links_prekategorie_link."\">..</a><br>";
    }

    $i = 0;
    while ( $links_kategorie_array = $db -> fetch_array($links_kategorie_result,1) ) {
        #$submenu_output .= "&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"display,".$links_kategorie_array[katid].".html\">".$links_kategorie_array[kattitel]."</a><br>";
      #$output .= "<a href=\"display,".$links_kategorie_array[katid].".html\">".$links_kategorie_array[kattitel]."</a><br>";
      $links_line = "<b><a href=\"display,".$links_kategorie_array[katid].".html\">".$links_kategorie_array[kattitel]."</a></b>";

      if ( $links_katid == "" ) {
        $links_detail_pattern = "katid REGEXP '^".$links_kategorie_array[katid]."-[0-9]+$'";
          $links_detail_kategorie_sql = "select katid, kattitel from db_link_kat where $links_detail_pattern order by 'kattitel'";
          $links_detail_kategorie_result  = $db -> query($links_detail_kategorie_sql);

          $links_detail = "";
          for ( $j=1; $j<=3; $j++) {
            $links_detail_kategorie_array = $db -> fetch_array($links_detail_kategorie_result,1);
            $links_detail .=  "<a href=\"display,".$links_detail_kategorie_array[katid].".html\">".$links_detail_kategorie_array[kattitel]."</a>";;
            if ( $links_detail_kategorie_array[katid] != "" ) {
              $links_detail .= ", ";
            }
          }
          $links_detail .= " ...";

          $i++;

          if ( $i >= 1 && $i <= 3 ) {
            $links_left .= $links_line."<br>".$links_detail."<br><br>";
          } elseif ( $i >= 4 && $i <= 6 ) {
            $links_middle .= $links_line."<br>".$links_detail."<br><br>";
          } elseif ( $i >= 7 && $i <= 10 ) {
            $links_right .= $links_line."<br>".$links_detail."<br><br>";
          }
      } else {
        $links_complete .= $links_line."<br>";
      }
    }
    $output .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">";
      $output .= "<tr>";
    $output .= "<td colspan=\"3\">".$links_kekse."</td>";
      $output .= "</tr>";
      $output .= "<tr>";
    $output .= "<td width=\"30%\" valign=\"top\">".$links_left."</td>";
    $output .= "<td width=\"30%\" valign=\"top\">".$links_middle."</td>";
    $output .= "<td width=\"30%\" valign=\"top\">".$links_right."</td>";
      $output .= "</tr>";
      $output .= "<tr>";
    $output .= "<td colspan=\"3\">".$links_complete."</td>";
      $output .= "</tr>";
        $output .= "</table>";


    #$output .= $links_left."<br><br>".$links_middle."<br><br>".$links_right;

      $links_daten_sql="SELECT * FROM db_link_dat AS A, db_link_lnk AS B
                                            WHERE A.datid = B.datid
                      AND B.katid = '$links_katid'
                                            order by A.dtitel asc";

    $links_daten_result = $db -> query($links_daten_sql);
    while ( $links_daten_array = $db -> fetch_array($links_daten_result,1) ) {
        $output .= "&nbsp;&nbsp;&nbsp;&nbsp;<a target=\"_blank\" href=\"".$links_daten_array[url]."\">".$links_daten_array[dtitel]."</a><br>";
    }

  }
  $ausgaben[output] = $output;

  if ( $debugging[html_enable] ) $debugging[ausgabe] .= "[ ++ $script_name ++ ]".$debugging[char];
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>