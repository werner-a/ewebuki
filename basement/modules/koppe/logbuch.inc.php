<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $script_name = "$Id$";
    $Script_desc = "Logbuch View / Edit Applikation";
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

      // konfiguration
      require $pathvars[addonroot]."koppe/logbuch.cfg.php";


      //
      // Bearbeiten
      //
      if ( $environment[kategorie] == "modify" ) {

          // warning ausgeben
          if ( get_cfg_var('register_globals') == 1 ) $debugging[ausgabe] .= "Warning register_globals in der php.ini steht auf on, evtl werden interne Variablen ueberschrieben!".$debugging[char];

          if ( $environment[parameter][1] == "add" ) {

              // form otions holen
              $form_options = form_options(crc32($environment[ebene]).".".$environment[kategorie]);

              // form elememte bauen
              $element = form_elements( $logbuch_entries, $HTTP_POST_VARS );

              // was anzeigen
              $mapping[main] = crc32($environment[ebene]).".modify";

              // wohin schicken
              $ausgaben[form_error] = "";
              $ausgaben[form_aktion] = $environment[basis]."/modify,add,verify.html";
              $ausgaben[form_break] = $_SERVER["HTTP_REFERER"];

              if ( $environment[parameter][2] == "verify" ) {

                  // form eigaben prüfen
                  form_errors( $form_options, $HTTP_POST_VARS );

                  // ohne fehler sql bauen und ausfuehren
                  if ( $ausgaben[form_error] == "" ) {
                      $kick = array( "PHPSESSID", "ldate", "submit" );
                      foreach($HTTP_POST_VARS as $name => $value) {
                          if ( !in_array($name,$kick) ) {
                              if ( $sqla != "" ) $sqla .= ",";
                              $sqla .= " ".$name;
                              if ( $sqlb != "" ) $sqlb .= ",";
                              $sqlb .= " '".$value."'";
                          }
                      }

                      // Sql um spezielle Felder erweitern
                      $ldate = $HTTP_POST_VARS[ldate];
                      $ldate = substr($ldate,6,4)."-".substr($ldate,3,2)."-".substr($ldate,0,2)." ".substr($ldate,11,9);
                      $sqla .= ", ldate";
                      $sqlb .= ", '".$ldate."'";

                      $sql = "insert into ".$logbuch_entries." (".$sqla.") VALUES (".$sqlb.")";
                      $result  = $db -> query($sql);
                      if ( $debugging[html_enable] ) $debugging[ausgabe] .= "sql: ".$sql.$debugging[char];

                      #$ausgaben[output] = "Verarbeitung Ihrer Daten ";
                      #if ($result) {
                      #    $ausgaben[output] .= "... mit Erfolg abgeschlossen.";
                      #} else {
                      #    $ausgaben[output] .= "... konnte nicht abgeschlossen werden.";
                      #}
                      header("Location: ".$environment[basis].".html");
                  }
              }
          } elseif ( $environment[parameter][1] == "edit" ) {

              if ( count($HTTP_POST_VARS) == 0 ) {
                  $sql = "SELECT * FROM ".$logbuch_entries." WHERE lid='".$environment[parameter][2]."'";
                  $result = $db -> query($sql);
                  $form_values = $db -> fetch_array($result,$nop);
              } else {
                  $form_values = $HTTP_POST_VARS;
              }

              // form otions holen
              $form_options = form_options(crc32($environment[ebene]).".".$environment[kategorie]);

              // form elememte bauen
              $element = form_elements( $logbuch_entries, $form_values );

              // was anzeigen
              $mapping[main] = crc32($environment[ebene]).".modify";

              // wohin schicken
              $ausgaben[form_error] = "";
              $ausgaben[form_aktion] = $environment[basis]."/modify,edit,".$environment[parameter][2].",verify.html";
              $ausgaben[form_break] = $_SERVER["HTTP_REFERER"];

              if ( $environment[parameter][3] == "verify" ) {

                  // form eigaben prüfen
                  form_errors( $form_options, $HTTP_POST_VARS );

                  // ohne fehler sql bauen und ausfuehren
                  if ( $ausgaben[form_error] == "" ) {

                      $kick = array( "PHPSESSID", "ldate", "submit" );
                      foreach($HTTP_POST_VARS as $name => $value) {
                          if ( !in_array($name,$kick) ) {
                              if ( $sqla != "" ) $sqla .= ", ";
                              $sqla .= $name."='".$value."'";
                          }
                      }

                      // Sql um spezielle Felder erweitern
                      $ldate = $HTTP_POST_VARS[ldate];
                      $ldate = substr($ldate,6,4)."-".substr($ldate,3,2)."-".substr($ldate,0,2)." ".substr($ldate,11,9);
                      $sqla .= ", ldate='".$ldate."'";

                      $sql = "update ".$logbuch_entries." SET ".$sqla." WHERE lid='".$environment[parameter][2]."'";
                      $result  = $db -> query($sql);
                      if ( $debugging[html_enable] ) $debugging[ausgabe] .= "sql: ".$sql.$debugging[char];
                      header("Location: ".$environment[basis].".html");
                  }
              }

          } elseif ( $environment[parameter][1] == "delete" ) {

              $sql = "SELECT * FROM ".$logbuch_entries." WHERE lid='".$environment[parameter][2]."'";
              $result = $db -> query($sql);
              $field = $db -> fetch_array($result,$nop);
              foreach($field as $name => $value) {
                  $ausgaben[$name] = $value;
              }
              $ausgaben[navigation] .= "<a href=\"".$_SERVER["HTTP_REFERER"]."\"><img src=\"".$pathvars[images]."/left.gif\" border=\"0\" alt=\"Zurück\" title=\"Zurück\" width=\"24\" height=\"18\"></a>";
              $ausgaben[navigation] .= "<a href=\"".$environment[basis]."/modify,delete,".$environment[parameter][2].",verify.html\"><img src=\"".$pathvars[images]."/delete.gif\" border=\"0\" alt=\"Endgültig Löschen\" title=\"Endgültig Löschen\" width=\"24\" height=\"18\"></a>";
              $mapping[main] = crc32($environment[ebene]).".delete";

              if ( $environment[parameter][3] == "verify" ) {
                  $sql = "DELETE FROM ".$logbuch_entries." WHERE lid='".$environment[parameter][2]."'";
                  $result  = $db -> query($sql);
                  header("Location: ".$environment[basis].".html");
              }
          }

      //
      // Details anzeigen
      //
      } elseif ( $environment[kategorie] == "details" ) {

          $sql = "SELECT * FROM ".$logbuch_entries." WHERE lid='".$environment[parameter][1]."'";
          $result = $db -> query($sql);
          $field = $db -> fetch_array($result,$nop);
          foreach($field as $name => $value) {
              $ausgaben[$name] = $value;
          }

          $ldate = $ausgaben[ldate];
          $ausgaben[ldate] = substr($ldate,8,2).".".substr($ldate,5,2).".".substr($ldate,0,4)." ".substr($ldate,11,9);

          $ausgaben[ldetail] = nlreplace($ausgaben[ldetail]);
          $ausgaben[ldetail] = tagreplace($ausgaben[ldetail]);

          $ausgaben[navigation] .= "<a href=\"".$_SERVER["HTTP_REFERER"]."\"><img src=\"".$pathvars[images]."/left.gif\" border=\"0\" alt=\"Zurück\" title=\"Zurück\" width=\"24\" height=\"18\"></a>";
          $ausgaben[navigation] .= "<a href=\"".$environment[basis]."/modify,edit,".$environment[parameter][1].".html\"><img src=\"".$pathvars[images]."/edit.gif\" border=\"0\" alt=\"Bearbeiten\" title=\"Bearbeiten\" width=\"24\" height=\"18\"></a>";
          $mapping[main] = crc32($environment[ebene]).".details";

      //
      // Liste anzeigen
      //
      } elseif ( $environment[kategorie] == "logbuch" || $environment[kategorie] == "list" ) {

          $position = $environment[parameter][1]+0;

          // Suche
          $ausgaben[form_aktion] = $environment[basis]."/list,".$position.",search.html";
          if ( $environment[parameter][2] == "search" ) {
              if ( $HTTP_POST_VARS[search] != "" ) {
                  $search_value = $HTTP_POST_VARS[search];
              } else {
                  $search_value = $environment[parameter][3];
              }
              $parameter = ",search,".$search_value;
              $where = " WHERE lentry LIKE '%".$search_value."%' OR ldetail LIKE '%".$search_value."%'";
          }

          // Sql Query
          $sql = "SELECT * FROM ".$logbuch_entries.$where." ORDER by ldate";

          // Inhalt Selector erstellen und SQL modifizieren
          $inhalt_selector = inhalt_selector( $sql, $position, $logbuch_rows, $parameter );
          $ausgaben[inhalt_selector] .= $inhalt_selector[0];
             $sql = $inhalt_selector[1];


          // Daten holen und ausgeben

          $ausgaben[output] .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";

          $ausgaben[output] .= "<tr>";
          $class = " class=\"lines\"";
          $ausgaben[output] .= "<td".$class." colspan=\"14\"><img src=\"".$pathvars[images]."/pos.png\" alt=\"\" width=\"1\" height=\"1\"></td>";
          $ausgaben[output] .= "</tr>";
          $class = " class=\"contenthead\"";
          #$size  = " width=\"30\" height=\"20\"";
          #$ausgaben[output] .= "<td".$class.$size.">&nbsp;</td>";
          #$ausgaben[output] .= "<td".$class.">&nbsp;</td>";

          $size  = " width=\"5\"";
          $ausgaben[output] .= "<td".$class.">Datum</td>";
          $ausgaben[output] .= "<td".$class.$size.">&nbsp;</td>";
          $ausgaben[output] .= "<td".$class.">Name</td>";
          $ausgaben[output] .= "<td".$class.$size.">&nbsp;</td>";
          $ausgaben[output] .= "<td".$class.">Eintrag</td>";
          $ausgaben[output] .= "<td".$class.$size.">&nbsp;</td>";
          $ausgaben[output] .= "<td".$class.">Aktion</td>";
          $ausgaben[output] .= "<td".$class.$size.">&nbsp;</td>";
          $ausgaben[output] .= "</tr><tr>";
          $class = " class=\"lines\"";
          $ausgaben[output] .= "<td".$class." colspan=\"14\"><img src=\"".$pathvars[images]."/pos.png\" alt=\"\" width=\"1\" height=\"1\"></td>";
          $ausgaben[output] .= "</tr>";


          $result = $db -> query($sql);
          $modify  = array (
                "details"     => array("", "Details"),
              "edit"        => array("modify,", "Editieren"),
              "delete"    => array("modify,", "Löschen")
          );
          $imgpath = $pathvars[images];

          while ( $field = $db -> fetch_array($result,$nop) ) {
              $ausgaben[output] .= "<tr>";
              $class = " class=\"contenttabs\"";
              #$size  = " width=\"30\" height=\"20\"";
              #$ausgaben[output] .= "<td".$class.$size.">&nbsp;</td>";
              #$ausgaben[output] .= "<td".$class.">&nbsp;</td>";

              $size  = " width=\"5\"";

              $ldate = $field[ldate];
              $field[ldate] = substr($ldate,8,2).".".substr($ldate,5,2).".".substr($ldate,0,4)." ".substr($ldate,11,9);
              $ausgaben[output] .= "<td".$class.">".$field[ldate]."</td>";
              $ausgaben[output] .= "<td".$class.$size.">&nbsp;</td>";
              $ausgaben[output] .= "<td".$class.">".$field[luser]."</td>";
              $ausgaben[output] .= "<td".$class.$size.">&nbsp;</td>";
              $ausgaben[output] .= "<td".$class.">".$field[lentry]."</td>";
              $ausgaben[output] .= "<td".$class.$size.">&nbsp;</td>";

              $aktion = "";
              foreach($modify as $name => $value) {
                  $aktion .= " <a href=\"".$environment[basis]."/".$value[0].$name.",".$field[lid].".html\"><img src=\"".$imgpath."/".$name.".gif\" border=\"0\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></a>";
              }
              $ausgaben[output] .= "<td".$class.">".$aktion."</td>";
              $ausgaben[output] .= "<td".$class.$size.">&nbsp;</td>";


              $ausgaben[output] .= "</tr><tr>";
              $class = " class=\"lines\"";
              $ausgaben[output] .= "<td".$class." colspan=\"14\"><img src=\"".$pathvars[images]."/pos.png\" alt=\"\" width=\"1\" height=\"1\"></td>";
              $ausgaben[output] .= "</tr>";


          }
          $ausgaben[output] .= "</table>";

          $mapping[main] = crc32($environment[ebene]).".".$environment[name];
      }

  if ( $debugging[html_enable] ) $debugging[ausgabe] .= "[ ++ $script_name ++ ]".$debugging[char];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>