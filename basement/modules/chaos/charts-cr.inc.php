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

  if ( $environment["parameter"][1] == "check" ) {

      $newentry = array();

      for ( $i = 1; $i <= 4; $i++ ) {

          #echo $i;
          #$fp = fopen($pathvars[addonroot]."/chaos/cr".$environment["parameter"][1].".html",r);
          #$fp = fopen('http://www.chart-radio.de/charts/view/view_charts.php?thisChartArea=m&thisChartCode=110&thisPage=1',r);
          $fp = fopen($_FILES["upload".$i]["tmp_name"],r);

          $line = "";
          while (!feof($fp)) {
              $fileline = fgets($fp, 131072);
              
              if ( strstr($fileline,"<!-- pageArea : [ CHARTS-TABLE ]") ) {
                  $read = true;
                  #$ausgaben["output"] .= "begin found\n";
              }
              
              if ( strstr($fileline,"<!-- pageArea : [ PAGE-CONTROLS ]") ) {
                  $read = false;
                  #$ausgaben["output"] .= "end found\n";
              }
              
              if ( $read == true ) {                
                $fileline = trim($fileline);
                $line .= chop($fileline)." ";
              }
          }
          fclose($fp);                                            
              
          while ( strstr($line, "<b>") ) {
              $begin = strpos($line, "<b>");
              $line = substr($line,$begin+3);

              $end = strpos($line, "</b>");
              $wert = substr($line,0,$end);


              $next = strpos($line, "<b>");
              $new = strpos($line, "newanim.gif");
              if ( $j == 0 && $new > 1 && $new < $next ) {
                  $newentry[] = $wert;
              }

              $wert = str_replace("&amp;","&",$wert);

              $wert = str_replace("Ä","ä",$wert);
              $wert = str_replace("Ö","ö",$wert);
              $wert = str_replace("Ü","ü",$wert);

              $j++;
              $row[$j] = strtolower($wert);
              if ( $j == 3 ) {
                  $j = 0;
                  $data[] = array( $row[1], $row[2], $row[3] );
                  #$ausgaben["output"] .= $row[1]." - ".$row[2]." - ".$row[3].$marke."<br>";
              }
          }
              
      }


      for ( $i = 0; $i <= 99; $i++ ) {
          $platz = $i+1;
          if ( $data[$i][0] != $platz ) {
              $ausgaben["output"] .= "fehler bei platz: ".$platz."<br>";
              break;
          }
      }
      $ausgaben["output"] .= $i." Datensaetze ok<br><br>";

      #echo "<pre>";
      #print_r($data);
      #echo "</pre>";

      $erstellt = $HTTP_POST_VARS["date"]." ".$HTTP_POST_VARS["time"];

      foreach ( $data as $value ) {

          if ( in_array($value[0], $newentry) ) {
              $new = "-1";
          } else {
              $new = "0";
          }

          $sql = "SELECT id FROM ".$cfg["db"]["titel"]." WHERE titel = '".$value[1]."'";
          #echo $sql."<br>";
          #$result  = $db -> query($sql);
          $titel["data"] = $db -> fetch_row($result);
          if ( $titel["data"][0]  == "" ) {
              $sql = "INSERT INTO ".$cfg["db"]["titel"]." ( titel ) VALUES ( '".addslashes($value[1])."' )";
              #echo $sql."<br>";
              #$result  = $db -> query($sql);
              $titel["id"] = $db -> lastid();
          } else {
              $titel["id"] = $titel["data"][0];
          }

          $sql = "SELECT id FROM ".$cfg["db"]["interpret"]." WHERE interpret = '".$value[2]."'";
          #echo $sql."<br>";
          #$result  = $db -> query($sql);
          $interpret["data"] = $db -> fetch_row($result);
          if ( $interpret["data"][0]  == "" ) {
              $sql = "INSERT INTO ".$cfg["db"]["interpret"]." ( interpret ) VALUES ( '".addslashes($value[2])."' )";
              #echo $sql."<br>";
              #$result  = $db -> query($sql);
              $interpret["id"] = $db -> lastid();
          } else {
              $interpret["id"] = $interpret["data"][0];
          }

          $sql  = "INSERT INTO ".$cfg["db"]["platzierung"]." ( erstellt, newentry, platz, titelid, interpretid) VALUES ";
          $sql .= "( '".$erstellt."', '".$new."', '".$value[0]."', '".$titel["id"]."', '".$interpret["id"]."' )";
          #echo $sql."<br><br>";
          #$result = $db -> query($sql);
          $ausgaben["output"] .= $value[0]." - ".$value[1]." - ".$value[2];
          if ( in_array($value[0], $newentry) ) {
              $ausgaben["output"] .= " <b>(new)</b>";
          }
          $ausgaben["output"] .= "<br>";
      }

      /*
      $valid = array("html");
      for ( $i = 1; $i <= 4; $i++ ) {
          $file = file_verarbeitung("/my/upload", "upload".$i, 2000000, $valid);
          $ausgaben[output] .= "Filename: ".$file[name]."<br><br>";
      }
      */
  } elseif ( $environment["parameter"][1] == "change" ) {

      $sql    = "SELECT * FROM ".$cfg["db"]["interpret"]." WHERE 1";
      $result = $db -> query($sql);

      while ( $data = $db->fetch_array($result,$NOP) ) {
          $new = explode(",",$data["interpret"]);
          if ( $new[1] != "" ) $new[1] = trim($new[1])." ";

          $sql = "UPDATE ".$cfg["db"]["interpret"]." SET interpret = '".$new[1].trim($new[0])."' WHERE id = ".$data["id"];
          $db -> query($sql);
      }



  } else {

      $ausgaben["output"] .="<form action=\"".$cfg["basis"]."/cr,check.html\" method=\"post\" enctype=\"multipart/form-data\">";
      #$ausgaben[output] .="<input type=\"hidden\" name=\"MAX_FILE_SIZE\" value=\"200000000\">";
      $ausgaben["output"] .="<input type=\"text\" name=\"date\" value=\"".date("Y-m-d")."\"><br>";
      #$ausgaben["output"] .="<input type=\"text\" name=\"time\" value=\"".date("H:i:s")."\"><br>";
      $ausgaben["output"] .="<input type=\"text\" name=\"time\" value=\"12:00:00\"><br>";
      $ausgaben["output"] .="<input type=\"file\" name=\"upload1\"><br>";
      $ausgaben["output"] .="<input type=\"file\" name=\"upload2\"><br>";
      $ausgaben["output"] .="<input type=\"file\" name=\"upload3\"><br>";
      $ausgaben["output"] .="<input type=\"file\" name=\"upload4\"><br>";
      $ausgaben["output"] .="<input type=\"submit\" value=\"los\">";
      $ausgaben["output"] .="</form>";

  }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
