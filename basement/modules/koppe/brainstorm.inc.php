<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $script_name = "$Id$";
    $Script_desc = "Brainstorm View / Edit Applikation";
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
      require $pathvars[addonroot]."koppe/brainstorm.cfg.php";

      // warning ausgeben
      if ( get_cfg_var('register_globals') == 1 ) $debugging[ausgabe] .= "Warning register_globals in der php.ini steht auf on, evtl werden interne Variablen ueberschrieben!".$debugging[char];

      //
      // Bearbeiten
      //
      if ( $environment[kategorie] == "modify" ) {

          if ( $environment[parameter][1] == "add" ) {

              // form otions holen
              $form_options = form_options(crc32($environment[ebene]).".".$environment[kategorie]);

              // form elememte bauen
              $element = form_elements( $data_entries, $HTTP_POST_VARS );

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
                      $kick = array( "PHPSESSID", "submit" );
                      foreach($HTTP_POST_VARS as $name => $value) {
                          if ( !in_array($name,$kick) ) {
                              if ( $sqla != "" ) $sqla .= ",";
                              $sqla .= " ".$name;
                              if ( $sqlb != "" ) $sqlb .= ",";
                              $sqlb .= " '".$value."'";
                          }
                      }

                      // Sql um spezielle Felder erweitern
                      #$ldate = $HTTP_POST_VARS[ldate];
                      #$ldate = substr($ldate,6,4)."-".substr($ldate,3,2)."-".substr($ldate,0,2)." ".substr($ldate,11,9);
                      #$sqla .= ", ldate";
                      #$sqlb .= ", '".$ldate."'";

                      $sql = "insert into ".$data_entries." (".$sqla.") VALUES (".$sqlb.")";
                      $result  = $db -> query($sql);
                      if ( $debugging[html_enable] ) $debugging[ausgabe] .= "sql: ".$sql.$debugging[char];

                      header("Location: ".$environment[basis].".html");
                  }
              }
          } elseif ( $environment[parameter][1] == "edit" ) {

              if ( count($HTTP_POST_VARS) == 0 ) {
                  $sql = "SELECT * FROM ".$data_entries." WHERE lid='".$environment[parameter][2]."'";
                  $result = $db -> query($sql);
                  $form_values = $db -> fetch_array($result,$nop);
              } else {
                  $form_values = $HTTP_POST_VARS;
              }

              // form otions holen
              $form_options = form_options("brainstorm");

              // form elememte bauen
              $element = form_elements( $data_entries, $form_values );

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

                      $kick = array( "PHPSESSID", "submit" );
                      foreach($HTTP_POST_VARS as $name => $value) {
                          if ( !in_array($name,$kick) ) {
                              if ( $sqla != "" ) $sqla .= ", ";
                              $sqla .= $name."='".$value."'";
                          }
                      }

                      // Sql um spezielle Felder erweitern
                      #$ldate = $HTTP_POST_VARS[ldate];
                      #$ldate = substr($ldate,6,4)."-".substr($ldate,3,2)."-".substr($ldate,0,2)." ".substr($ldate,11,9);
                      #$sqla .= ", ldate='".$ldate."'";

                      $sql = "update ".$data_entries." SET ".$sqla." WHERE lid='".$environment[parameter][2]."'";
                      $result  = $db -> query($sql);
                      if ( $debugging[html_enable] ) $debugging[ausgabe] .= "sql: ".$sql.$debugging[char];
                      header("Location: ".$environment[basis].".html");
                  }
              }

          } elseif ( $environment[parameter][1] == "delete" ) {

              $sql = "SELECT * FROM ".$data_entries." WHERE lid='".$environment[parameter][2]."'";
              $result = $db -> query($sql);
              $field = $db -> fetch_array($result,$nop);
              foreach($field as $name => $value) {
                  $ausgaben[$name] = $value;
              }
              $ausgaben[navigation] .= "<a href=\"".$_SERVER["HTTP_REFERER"]."\"><img src=\"".$pathvars[images]."/left.gif\" border=\"0\" alt=\"Zurück\" title=\"Zurück\" width=\"24\" height=\"18\"></a>";
              $ausgaben[navigation] .= "<a href=\"".$environment[basis]."/modify,delete,".$environment[parameter][2].",verify.html\"><img src=\"".$pathvars[images]."/delete.gif\" border=\"0\" alt=\"Endgültig Löschen\" title=\"Endgültig Löschen\" width=\"24\" height=\"18\"></a>";
              $mapping[main] = crc32($environment[ebene]).".delete";

              if ( $environment[parameter][3] == "verify" ) {

                  // datei verwaltung/delete begin
                  // ***
                  $sql = "SELECT * FROM ".$data_entries_file." WHERE frefid = ".$environment[parameter][2]." AND ftname ='".crc32($environment[ebene]).".".$environment[name]."'";
                  if ( $debugging[html_enable] ) $debugging[ausgabe] .= "sql: ".$sql.$debugging[char];
                  $result = $db -> query($sql);
                  $anzahl = $db -> num_rows($result);
                  while ( $all = $db -> fetch_array($result,1) ) {
                    if ( substr($_SERVER["DOCUMENT_ROOT"],-1,1) == "/" ) {
                      $document_root = substr($_SERVER["DOCUMENT_ROOT"],0,-1);
                    } else {
                      $document_root = $_SERVER["DOCUMENT_ROOT"];
                    }
                    if ( @unlink($document_root.$file_path."/".$all[ffname]) == 1 ) {
                      $sql = "DELETE FROM site_file WHERE fid = ".$all[fid];
                      if ( $debugging[html_enable] ) $debugging[ausgabe] .= "sql: ".$sql.$debugging[char];
                      $result2 = $db -> query($sql);
                      $error  = @$db -> error();
                      if ( $error != 0 ) {
                        $errormsg = $db -> error("sql error:");
                        if ( $debugging[html_enable] ) $debugging[ausgabe] .= $errormsg.$debugging[char];
                        }
                      $delete_count++;
                      $files_deleted_ok .= $all[ffname]." ";
                    } else {
                      $files_deleted_err .= $all[ffname]." ";
                    }
                  }
                  if ( $debugging[html_enable] ) $debugging[ausgabe] .= "file delete ok: ".$files_deleted_ok.$debugging[char];
                  if ( $delete_count != $anzahl ) {
                    $errorcode = $errorcode+1;
                    if ( $debugging[html_enable] ) $debugging[ausgabe] .= "file delete err: ".$files_deleted_err.$debugging[char];
                  }
                  // +++
                  // datei verwaltung/delete end

                  if ( $errorcode == 0 ) {
                    $sql = "DELETE FROM ".$data_entries." WHERE lid='".$environment[parameter][2]."'";
                    if ( $debugging[html_enable] ) $debugging[ausgabe] .= "sql: ".$sql.$debugging[char];
                    $result = $db -> query($sql);
                    $error = @$db -> error();
                    if ( $error != 0 ) {
                      $errorcode = $errorcode+4;
                      $errormsg = $db -> error("sql error:");
                      if ( $debugging[html_enable] ) $debugging[ausgabe] .= $errormsg.$debugging[char];
                    }
                  }

                  if ( $errorcode == 0 ) {
                    header("Location: ".$environment[basis].".html");
                  }
              }
          }

      //
      // datei verarbeitung
      //
      } elseif ( $environment[kategorie] == "file" ) {

          if ( $environment[parameter][1] == "new" ) {
              // form otions holen
              $form_options = form_options(crc32($environment[ebene]).".".$environment[kategorie]);

              // form elememte bauen
              $element = form_elements( $data_entries_file, $HTTP_POST_VARS );

              // form elemente erweitern
              if ( $HTTP_POST_VARS[frefid] == "" ) $element[frefid] = str_replace("frefid\" ","frefid\" value=\"".$environment[parameter][2]."\"",$element[frefid]);

              // was anzeigen
              $mapping[main] = crc32($environment[ebene]).".file";

              // wohin schicken
              $ausgaben[form_error] = "";
              $ausgaben[form_aktion] = $environment[basis]."/file,new,verify.html";
              $ausgaben[form_break] = $_SERVER["HTTP_REFERER"];

              if ( $environment[parameter][2] == "verify" ) {

                  // datei verwaltung/save begin
                  // ***
                  $valid = array("zip", "pdf");
                  $file = file_verarbeitung($file_path, "ffname", 4000000, $valid);
                  $HTTP_POST_VARS[ffname] = $file[name];
                  $HTTP_POST_VARS[ftname] = crc32($environment[ebene]).".".$environment[name];
                  $error = $file[returncode];
                  if ( $error == 0 ) {
                      #$ausgaben[form_error] .= "There is no error, the file uploaded with success.";
                  } elseif ( $error == 1 ) {
                      $ausgaben[form_error] .= "The uploaded file exceeds the upload_max_filesize directive ( ".get_cfg_var(upload_max_filesize)." ) in php.ini.<br>";
                  } elseif ( $error == 2 ) {
                      $ausgaben[form_error] .= "The uploaded file exceeds the MAX_FILE_SIZE ( ".$HTTP_POST_VARS[MAX_FILE_SIZE]."kb ) directive that was specified in the html form.<br>";
                  } elseif ( $error == 3 ) {
                      $ausgaben[form_error] .= "The uploaded file was only partially uploaded.<br>";
                  } elseif ( $error == 4 ) {
                      $ausgaben[form_error] .= "No file was uploaded.<br>";
                  } elseif ( $error == 7 ) {
                      $ausgaben[form_error] .= "File Size to big.<br>";
                  } elseif ( $error == 8 ) {
                      $ausgaben[form_error] .= "File Type not valid.<br>";
                  } elseif ( $error == 9 ) {
                      $ausgaben[form_error] .= "File Name already exists.<br>";
                  } elseif ( $error == 10 ) {
                      $ausgaben[form_error] .= "Unknown Error z.B. post_max_size directive ( ".get_cfg_var(post_max_size)." ) in php.ini.<br>";
                      $mapping[main] = crc32($environment[ebene]).".error";
                  } elseif ( $error == 11 ) {
                      $ausgaben[form_error] .= "Sorry, you need minimal PHP/4.x.x to handle uploads!<br>";
                  }
                  // +++
                  // datei verwaltung/save end

                  // form eigaben prüfen
                  form_errors( $form_options, $HTTP_POST_VARS );

                  // ohne fehler sql bauen und ausfuehren
                  if ( $ausgaben[form_error] == "" ) {
                      $kick = array( "PHPSESSID", "submit" );
                      foreach($HTTP_POST_VARS as $name => $value) {
                          if ( !in_array($name,$kick) ) {
                              if ( $sqla != "" ) $sqla .= ",";
                              $sqla .= " ".$name;
                              if ( $sqlb != "" ) $sqlb .= ",";
                              $sqlb .= " '".$value."'";
                          }
                      }

                      $sql = "insert into ".$data_entries_file." (".$sqla.") VALUES (".$sqlb.")";
                      $result  = $db -> query($sql);
                      if ( $debugging[html_enable] ) $debugging[ausgabe] .= "sql: ".$sql.$debugging[char];
                      if ($result) {
                          header("Location: ".$environment[basis].".html");
                      } else {
                          $error = $db -> error("sql error:");
                          if ( $debugging[html_enable] ) $debugging[ausgabe] .= $error.$debugging[char];
                      }
                  }

               }
          }

      //
      // Details anzeigen
      //
      } elseif ( $environment[kategorie] == "details" ) {

          $sql = "SELECT * FROM ".$data_entries." WHERE lid='".$environment[parameter][1]."'";
          $result = $db -> query($sql);
          $field = $db -> fetch_array($result,$nop);
          foreach($field as $name => $value) {
              $ausgaben[$name] = $value;
          }

          // datei verwaltung/anzeige begin
          // ***
          $sql = "SELECT * FROM ".$data_entries_file." WHERE frefid = ".$environment[parameter][1]." AND ftname ='".crc32($environment[ebene]).".".$environment[name]."'";
          if ( $debugging[html_enable] ) $debugging[ausgabe] .= "sql: ".$sql.$debugging[char];
          $result = $db -> query($sql);
          while ( $all = $db -> fetch_array($result,1) ) {
              if ( isset($ausgaben[dateien]) ) $ausgaben[dateien] .= "<br>";
              $ausgaben[dateien] .= "<a target=\"preview\" href=\"".$file_path."/".$all[ffname]."\">".$all[ffname]."</a>";
          }
          if ( !isset($ausgaben[dateien]) ) $ausgaben[dateien] = "---";
          // +++
          // datei verwaltung/anzeige end

          $ldate = $ausgaben[ldate];
          $ausgaben[bdate] = substr($bdate,8,2).".".substr($bdate,5,2).".".substr($bdate,0,4)." ".substr($bdate,11,9);

          $ausgaben[bdetail] = nlreplace($ausgaben[bdetail]);
          $ausgaben[bdetail] = tagreplace($ausgaben[bdetail]);

          $ausgaben[navigation] .= "<a href=\"".$_SERVER["HTTP_REFERER"]."\"><img src=\"".$pathvars[images]."/left.gif\" border=\"0\" alt=\"Zurück\" title=\"Zurück\" width=\"24\" height=\"18\"></a>";
          $ausgaben[navigation] .= "<a href=\"".$environment[basis]."/modify,edit,".$environment[parameter][1].".html\"><img src=\"".$pathvars[images]."/edit.gif\" border=\"0\" alt=\"Bearbeiten\" title=\"Bearbeiten\" width=\"24\" height=\"18\"></a>";
          $ausgaben[navigation] .= "<a href=\"".$environment[basis]."/modify,delete,".$environment[parameter][1].".html\"><img src=\"".$pathvars[images]."/delete.gif\" border=\"0\" alt=\"Löschen\" title=\"Löschen\" width=\"24\" height=\"18\"></a>";
          $ausgaben[navigation] .= "<a href=\"".$environment[basis]."/file,new,".$environment[parameter][1].".html\"><img src=\"".$pathvars[images]."/new.gif\" border=\"0\" alt=\"Dateianhang\" title=\"Dateianhang\" width=\"24\" height=\"18\"></a>";
          $mapping[main] = crc32($environment[ebene]).".details";

      //
      // Liste anzeigen
      //
      } elseif ( $environment[kategorie] == "brainstorm" || $environment[kategorie] == "list" ) {

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
              $where = " WHERE bproject LIKE '%".$search_value."%' OR bsign LIKE '%".$search_value."%' OR bshort LIKE '%".$search_value."%' OR bdetail     LIKE '%".$search_value."%'";
          }

          // Sql Query
          $sql = "SELECT * FROM ".$data_entries.$where." ORDER by lid";

          // Inhalt Selector erstellen und SQL modifizieren
          $inhalt_selector = inhalt_selector( $sql, $position, $data_rows, $parameter );
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
          $ausgaben[output] .= "<td".$class.">Projekt</td>";
          $ausgaben[output] .= "<td".$class.$size.">&nbsp;</td>";
          $ausgaben[output] .= "<td".$class.">Namenszeichen</td>";
          $ausgaben[output] .= "<td".$class.$size.">&nbsp;</td>";
          $ausgaben[output] .= "<td".$class.">Kurzbechreibung</td>";
          $ausgaben[output] .= "<td".$class.$size.">&nbsp;</td>";
          $ausgaben[output] .= "<td".$class.">Aktion</td>";
          $ausgaben[output] .= "<td".$class.$size.">&nbsp;</td>";
          $ausgaben[output] .= "</tr><tr>";
          $class = " class=\"lines\"";
          $ausgaben[output] .= "<td".$class." colspan=\"14\"><img src=\"".$pathvars[images]."/pos.png\" alt=\"\" width=\"1\" height=\"1\"></td>";
          $ausgaben[output] .= "</tr>";


          $result = $db -> query($sql);
          $modify  = array (
              "details"   => array("", "Details"),
              "edit"      => array("modify,", "Editieren"),
              "delete"    => array("modify,", "Löschen"),
              "new"       => array("file,", "Dateianhang")
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
              $ausgaben[output] .= "<td".$class.">".$field[bproject]."</td>";
              $ausgaben[output] .= "<td".$class.$size.">&nbsp;</td>";
              $ausgaben[output] .= "<td".$class.">".$field[bsign]."</td>";
              $ausgaben[output] .= "<td".$class.$size.">&nbsp;</td>";
              $ausgaben[output] .= "<td".$class.">".$field[bshort]."</td>";
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