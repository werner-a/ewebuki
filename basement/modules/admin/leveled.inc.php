<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $script_name = "$Id$";
    $Script_desc = "Level Management Applikation";
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

  if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** $script_name ** ]".$debugging["char"];

      // content umschaltung verhindern
      $specialvars["dynlock"] = True;

      //
      // Bearbeiten
      //
      if ( $environment["kategorie"] == "modify" ) {

          // warning ausgeben
          if ( get_cfg_var('register_globals') == 1 ) $debugging["ausgabe"] .= "Warning register_globals in der php.ini steht auf on, evtl werden interne Variablen ueberschrieben!".$debugging["char"];

          if ( $environment["parameter"][1] == "add" && $rechte["cms_admin"] == -1 ) {

              // form otions holen
              $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

              // form elememte bauen
              $element = form_elements( $data_entries, $HTTP_POST_VARS );

              // form elemente erweitern
              #$element["newpass"] = str_replace("pass\"","newpass\"",$element["pass"]);
              #$element["chkpass"] = str_replace("pass\"","chkpass\"",$element["pass"]);
              #$element["pass"] = "";

              // user management form form elemente begin
              // ***
              $element["add"] = "";
              $element["del"] = "";
              $element["actual"] = "";
              $element["avail"] = "<select name=\"avail[]\" size=\"10\" multiple>";
              $sql = "SELECT uid, username FROM auth_user ORDER by username";
              $result = $db -> query($sql);
              while ( $all = $db -> fetch_array($result,1) ) {
                  $element["avail"] .= "<option value=\"".$all["uid"]."\">".$all["username"]."</option>\n";
              }
              $element["avail"] .= "</select>";
              // +++
              // user management form form elemente end

              // was anzeigen
              $mapping["main"] = crc32($environment["ebene"]).".modify";

              // wohin schicken
              $ausgaben["form_error"] = "";
              $ausgaben["form_aktion"] = $environment["basis"]."/modify,add,verify.html";
              $ausgaben["form_break"] = $environment["basis"].".html";

              if ( $environment["parameter"][2] == "verify" ) {

                  // form eingaben prüfen
                  form_errors( $form_options, $HTTP_POST_VARS );

                  // form eingaben prüfen erweitern
                  #if ( $HTTP_POST_VARS["newpass"] != "" && $HTTP_POST_VARS["newpass"] == $HTTP_POST_VARS["chkpass"] ) {
                  #    $checked_password = $HTTP_POST_VARS["newpass"];
                  #} else {
                  #    $ausgaben["form_error"] .= $form_options["pass"]["ferror"];
                  #}

                  // ohne fehler sql bauen und ausfuehren
                  if ( $ausgaben["form_error"] == "" ) {
                      $kick = array( "PHPSESSID", "submit", "avail" );
                      foreach($HTTP_POST_VARS as $name => $value) {
                          if ( !in_array($name,$kick) ) {
                              if ( $sqla != "" ) $sqla .= ",";
                              $sqla .= " ".$name;
                              if ( $sqlb != "" ) $sqlb .= ",";
                              $sqlb .= " '".$value."'";
                          }
                      }

                      // Sql um spezielle Felder erweitern
                      #$sqla .= ", pass";
                      #$sqlb .= ", password('".$checked_password."')";

                      $sql = "insert into ".$data_entries." (".$sqla.") VALUES (".$sqlb.")";
                      $result  = $db -> query($sql);
                      if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];

                      if ($result) {

                          // user management sql begin
                          // ***
                          if ( is_array($HTTP_POST_VARS["avail"]) ) {
                              $lid = $db -> lastid();
                              foreach ($HTTP_POST_VARS["avail"] as $name => $value ) {
                                  $sql = "INSERT INTO auth_right (lid, uid) VALUES ('".$lid."', '".$value."')";
                                  $db -> query($sql);
                                  if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                              }
                          }
                          // +++
                          // user management sql end

                          header("Location: ".$environment["basis"].".html");
                      } else {
                          if ( @$db -> error() == 1062 ) {
                              if ( $form_options["level"]["fdberror"] != "" ) {
                                  $ausgaben["form_error"] .= $form_options["level"]["fdberror"];
                              } else {
                                  $ausgaben["form_error"] .= "duplicate level, please change";
                              }
                          }
                          $error = $db -> error("sql error:");
                          if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= $error.$debugging["char"];
                      }

                  }
              }
          } elseif ( $environment["parameter"][1] == "edit" && $rechte["cms_admin"] == -1 ) {

              if ( count($HTTP_POST_VARS) == 0 ) {
                  $sql = "SELECT * FROM ".$data_entries." WHERE lid='".$environment["parameter"][2]."'";
                  $result = $db -> query($sql);
                  $form_values = $db -> fetch_array($result,$nop);
              } else {
                  $form_values = $HTTP_POST_VARS;
              }

              // form otions holen
              $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

              // form elememte bauen
              $element = form_elements( $data_entries, $form_values );

              // form elemente erweitern
              #$element["newpass"] = str_replace("pass\"","newpass\"",$element["pass"]);
              #$element["chkpass"] = str_replace("pass\"","chkpass\"",$element["pass"]);
              #$element["pass"] = "";

              // user management form form elemente begin
              // ***
              $element["add"] = "<input type=\"submit\" name=\"add\" value=\"&lt;&lt;&lt;\">";
              $element["del"] = "<input type=\"submit\" name=\"del\" value=\"&gt;&gt;&gt;\">";
              $element["actual"] = "<select name=\"actual[]\" size=\"10\" multiple>";
              $element["avail"] = "<select name=\"avail[]\" size=\"10\" multiple>";
              # nice sql query tnx@bastard!
              $sql = "SELECT auth_user.uid, auth_user.username, auth_right.lid, auth_right.rid FROM auth_user LEFT JOIN auth_right ON auth_user.uid = auth_right.uid and auth_right.lid = ".$environment["parameter"][2]." ORDER by username";
              $result = $db -> query($sql);
              while ( $all = $db -> fetch_array($result,1) ) {
                  if ( $all["lid"] == $environment["parameter"][2] ) {
                      $element["actual"] .= "<option value=\"".$all["rid"]."\">".$all["username"]."</option>\n";
                  } else {
                      $element["avail"] .= "<option value=\"".$all["uid"]."\">".$all["username"]."</option>\n";
                  }
              }
              $element["actual"] .= "</select>";
              $element["avail"] .= "</select>";
              // +++
              // user management form form elemente end

              // was anzeigen
              $mapping["main"] = crc32($environment["ebene"]).".modify";

              // wohin schicken
              $ausgaben["form_error"] = "";
              $ausgaben["form_aktion"] = $environment["basis"]."/modify,edit,".$environment["parameter"][2].",verify.html";
              $ausgaben["form_break"] = $environment["basis"].".html";

              if ( $environment["parameter"][3] == "verify" ) {

                  // form eigaben prüfen
                  form_errors( $form_options, $HTTP_POST_VARS );

                  // form eingaben prüfen erweitern
                  if ( $HTTP_POST_VARS["newpass"] != "" && $HTTP_POST_VARS["newpass"] == $HTTP_POST_VARS["chkpass"] ) {
                      $checked_password = $HTTP_POST_VARS["newpass"];
                  } elseif ( $HTTP_POST_VARS["newpass"] != "" )  {
                      $ausgaben["form_error"] .= $form_options["pass"]["ferror"];
                  }

                  // ohne fehler sql bauen und ausfuehren
                  if ( $ausgaben["form_error"] == "" ) {

                      // user management sql begin
                      // ***
                      if ( is_array($HTTP_POST_VARS["avail"]) ) {
                          foreach ($HTTP_POST_VARS["avail"] as $name => $value ) {
                              $sql = "INSERT INTO auth_right (lid, uid) VALUES ('".$environment["parameter"][2]."', '".$value."')";
                              $db -> query($sql);
                              if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                          }
                          if ( isset($HTTP_POST_VARS["add"]) ) {
                              header("Location: ".$environment["basis"]."/modify,edit,".$environment["parameter"][2].",verify.html");
                          }
                      }

                      if ( is_array($HTTP_POST_VARS["actual"]) ) {
                          foreach ($HTTP_POST_VARS["actual"] as $name => $value ) {
                              $sql = "DELETE FROM auth_right where rid='".$value."'";
                              $db -> query($sql);
                              if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                          }
                          if ( isset($HTTP_POST_VARS["del"]) ) {
                              header("Location: ".$environment["basis"]."/modify,edit,".$environment["parameter"][2].",verify.html");
                          }
                      }
                      // +++
                      // user management sql end

                      $kick = array( "PHPSESSID", "submit", "add", "del", "actual", "avail" );
                      foreach($HTTP_POST_VARS as $name => $value) {
                          if ( !in_array($name,$kick) ) {
                              if ( $sqla != "" ) $sqla .= ", ";
                              $sqla .= $name."='".$value."'";
                          }
                      }

                      if ( isset($HTTP_POST_VARS["submit"]) ) {
                          // Sql um spezielle Felder erweitern
                          #if ( $checked_password != "" ) {
                          #    $sqla .= ", pass=password('".$checked_password."')";
                          #}

                          $sql = "update ".$data_entries." SET ".$sqla." WHERE lid='".$environment["parameter"][2]."'";
                          $result  = $db -> query($sql);
                          if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                          header("Location: ".$environment["basis"].".html");
                      }
                  }
              }

          } elseif ( $environment["parameter"][1] == "delete" && $rechte["cms_admin"] == -1 ) {

              // ausgaben variablen bauen
              $sql = "SELECT * FROM ".$data_entries." WHERE lid='".$environment["parameter"][2]."'";
              $result = $db -> query($sql);
              $field = $db -> fetch_array($result,$nop);
              foreach($field as $name => $value) {
                  $ausgaben[$name] = $value;
              }

              // was anzeigen
              $mapping["main"] = crc32($environment["ebene"]).".delete";
              $mapping["navi"] = "leer";

              // wohin schicken
              $ausgaben["form_aktion"] = $environment["basis"]."/modify,delete,".$environment["parameter"][2].".html";
              $ausgaben["form_break"] = $_SERVER["HTTP_REFERER"];

              if ( $HTTP_POST_VARS["delete"] == "true" ) {

                  // user management sql begin
                  // ***
                  $sql = "DELETE FROM auth_right where lid='".$environment["parameter"][2]."'";
                  $db -> query($sql);
                  // +++
                  // user management sql end

                  $sql = "DELETE FROM ".$data_entries." WHERE lid='".$environment["parameter"][2]."'";
                  $result  = $db -> query($sql);

                  header("Location: ".$environment["basis"].".html");
              }
          }

      //
      // Details anzeigen
      //
      } elseif ( $environment["kategorie"] == "details" ) {

          $sql = "SELECT * FROM ".$data_entries." WHERE lid='".$environment["parameter"][1]."'";
          $result = $db -> query($sql);
          $field = $db -> fetch_array($result,$nop);
          foreach($field as $name => $value) {
              $ausgaben[$name] = $value;
          }

          // user management form form elemente begin
          // ***
          $sql = "SELECT auth_right.lid, auth_user.username FROM auth_user INNER JOIN auth_right ON auth_user.uid = auth_right.uid WHERE auth_right.lid = ".$environment["parameter"][1]." order by username";
          $result = $db -> query($sql);
          while ( $all = $db -> fetch_array($result,1) ) {
              if ( isset($ausgaben["username"]) ) $ausgaben["username"] .= ", ";
              $ausgaben["username"] .= $all["username"]."";
          }
          if ( !isset($ausgaben["username"]) ) $ausgaben["username"] = "---";
          // +++
          // user management form form elemente end

          $ldate = $ausgaben["ldate"];
          $ausgaben["ldate"] = substr($ldate,8,2).".".substr($ldate,5,2).".".substr($ldate,0,4)." ".substr($ldate,11,9);

          $ausgaben["ldetail"] = nlreplace($ausgaben["ldetail"]);

          $ausgaben["navigation"] .= "<a href=\"".$_SERVER["HTTP_REFERER"]."\"><img src=\"".$pathvars["images"]."left.png\" border=\"0\" alt=\"Zurück\" title=\"Zurück\" width=\"24\" height=\"18\"></a>";
          $ausgaben["navigation"] .= "<a href=\"".$environment["basis"]."/modify,edit,".$environment["parameter"][1].".html\"><img src=\"".$pathvars["images"]."edit.png\" border=\"0\" alt=\"Bearbeiten\" title=\"Bearbeiten\" width=\"24\" height=\"18\"></a>";
          $mapping["main"] = crc32($environment["ebene"]).".details";

      //
      // Liste anzeigen
      //
      } elseif ( $environment["kategorie"] == $environment["name"] || $environment["kategorie"] == "list" ) {

          $position = $environment["parameter"][1]+0;

          // Suche
          $ausgaben["form_aktion"] = $environment["basis"]."/list,".$position.",search.html";
          if ( $environment["parameter"][2] == "search" ) {
              if ( $HTTP_POST_VARS["search"] != "" ) {
                  $search_value = $HTTP_POST_VARS["search"];
              } else {
                  $search_value = $environment["parameter"][3];
              }
              $parameter = ",search,".$search_value;
              $where = " WHERE bproject LIKE '%".$search_value."%' OR bsign LIKE '%".$search_value."%' OR bshort LIKE '%".$search_value."%' OR bdetail LIKE '%".$search_value."%'";
          }

          // Sql Query
          $sql = "SELECT * FROM ".$data_entries.$where." ORDER by level";

          // Inhalt Selector erstellen und SQL modifizieren
          $inhalt_selector = inhalt_selector( $sql, $position, $data_rows, $parameter );
          $ausgaben["inhalt_selector"] .= $inhalt_selector[0];
          $sql = $inhalt_selector[1];


          // Daten holen und ausgeben

          $ausgaben["output"] .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";

          $ausgaben["output"] .= "<tr>";
          $class = " class=\"lines\"";
          $ausgaben["output"] .= "<td".$class." colspan=\"14\"><img src=\"".$pathvars["images"]."/pos.png\" alt=\"\" width=\"1\" height=\"1\"></td>";
          $ausgaben["output"] .= "</tr>";
          $class = " class=\"contenthead\"";
          #$size  = " width=\"30\" height=\"20\"";
          #$ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
          #$ausgaben["output"] .= "<td".$class.">&nbsp;</td>";

          $size  = " width=\"30\"";
          $ausgaben["output"] .= "<td".$class.">Level</td>";
          $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
          $ausgaben["output"] .= "<td".$class.">Beschreibung</td>";
          $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
          #$ausgaben["output"] .= "<td".$class.">unused</td>";
          #$ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
          $ausgaben["output"] .= "<td".$class.">Bearbeiten</td>";
          $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
          $ausgaben["output"] .= "</tr><tr>";
          $class = " class=\"lines\"";
          $ausgaben["output"] .= "<td".$class." colspan=\"14\"><img src=\"".$pathvars["images"]."pos.png\" alt=\"\" width=\"1\" height=\"1\"></td>";
          $ausgaben["output"] .= "</tr>";


          $result = $db -> query($sql);
          $modify  = array (
              "edit"      => array("modify,", "Editieren", "cms_admin"),
              "delete"    => array("modify,", "Löschen", "cms_admin"),
              "details"   => array("", "Details")
          );
          $imgpath = $pathvars["images"];

          while ( $field = $db -> fetch_array($result,$nop) ) {
              $ausgaben["output"] .= "<tr>";
              $class = " class=\"contenttabs\"";
              #$size  = " width=\"30\" height=\"20\"";
              #$ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
              #$ausgaben["output"] .= "<td".$class.">&nbsp;</td>";

              $size  = " width=\"30\"";

              # $ldate = $field["ldate"];
              # $field["ldate"] = substr($ldate,8,2).".".substr($ldate,5,2).".".substr($ldate,0,4)." ".substr($ldate,11,9);
              $ausgaben["output"] .= "<td".$class.">".$field["level"]."</td>";
              $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
              $ausgaben["output"] .= "<td".$class.">".substr($field["beschreibung"],0,20)." ...</td>";
              $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
              #$ausgaben["output"] .= "<td".$class.">".$field["unused"]."</td>";
              #$ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";

              $aktion = "";
              foreach($modify as $name => $value) {

              }
              foreach($modify as $name => $value) {
                  if ( $value[2] == "" || $rechte[$value[2]] == -1) {
                        $aktion .= "<a href=\"".$environment["basis"]."/".$value[0].$name.",".$field["lid"].".html\"><img src=\"".$imgpath.$name.".png\" border=\"0\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></a>";
              } else {
                        $aktion .= "<img src=\"".$pathvars["images"]."pos.png\" alt=\"\" width=\"24\" height=\"18\">";
                  }
              }
              $ausgaben["output"] .= "<td".$class.">".$aktion."</td>";
              $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";


              $ausgaben["output"] .= "</tr><tr>";
              $class = " class=\"lines\"";
              $ausgaben["output"] .= "<td".$class." colspan=\"14\"><img src=\"".$pathvars["images"]."/pos.png\" alt=\"\" width=\"1\" height=\"1\"></td>";
              $ausgaben["output"] .= "</tr>";


          }
          $ausgaben["output"] .= "</table>";

          $mapping["main"] = crc32($environment["ebene"]).".list";
      }

  if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ $script_name ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
