<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $script_name = "$Id$";
    $Script_desc = "User-modify";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    phpWEBkit - a easy website building kit
    Copyright (C)2001, 2002, 2003 Werner Ammon <wa@chaos.de>

    This script is a part of phpWEBkit

    phpWEBkit is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    phpWEBkit is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with phpWEBkit; If you did not, you may download a copy at:

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

          // warning ausgeben
          if ( get_cfg_var('register_globals') == 1 ) $debugging["ausgabe"] .= "Warning register_globals in der php.ini steht auf on, evtl werden interne Variablen ueberschrieben!".$debugging["char"];

          if ( $environment["parameter"][1] == "add" && $rechte["administration"] == -1 ) {

              // Wach 0407
              // ***
              if ( count($HTTP_POST_VARS) == 0 ) {
                  $sql = "SELECT * FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][2]."'";
                  $result = $db -> query($sql);
                  $form_values = $db -> fetch_array($result,$nop);
              } else {
                  $form_values = $HTTP_POST_VARS;
              }
              // +++
              // Wach




              // form otions holen
              $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

              // form elememte bauen
              #$element = form_elements( $data_entries, $HTTP_POST_VARS );
              $element = form_elements( $cfg["db"]["entries"], $form_values );

              if ( $HTTP_POST_VARS["ablogin"] == "" ) {
                  $ausgaben["form_error"] .= $form_options["ablogin"]["ferror"];
              }



              // form elemente erweitern
              // Wa 0907
              #$element["newpass"] = str_replace("pass\"","newpass\"",$element["pass"]);
              #$element["chkpass"] = str_replace("pass\"","chkpass\"",$element["pass"]);
              #$element["pass"] = "";

              $element["newpass"] = str_replace("abpasswort\"","newpass\"",$element["abpasswort"]);
              $element["chkpass"] = str_replace("abpasswort\"","chkpass\"",$element["abpasswort"]);
              $element["abpasswort"] = "";

              // intrabvv hack weam 0307
              // ***
              #if ( $HTTP_POST_VARS["uid"] == "" ) {
              #    $element["uid"] = str_replace("uid\"","uid\" value=\"".$environment["parameter"][2]."\"",$element["uid"]);
              #}
              // +++
              // intrabvv hack

              // level management form form elemente begin
              // ***
              $element["add"] = "";
              $element["del"] = "";
              $element["actual"] = "";
              $element["avail"] = "<select name=\"avail[]\" size=\"10\" multiple>";
              $sql = "SELECT lid, level FROM auth_level ORDER by level";
              $result = $db -> query($sql);
              while ( $all = $db -> fetch_array($result,1) ) {
                  $element["avail"] .= "<option value=\"".$all["lid"]."\">".$all["level"]."</option>\n";
              }
              $element["avail"] .= "</select>";
              // +++
              // level management form form elemente end

              // was anzeigen
              $mapping["main"] = crc32($environment["ebene"]).".modify";

              // wohin schicken
              $ausgaben["form_error"] = "";
              $ausgaben["form_aktion"] = $cfg["basis"]."/modify,add,verify.html";
              $ausgaben["form_break"] = $cfg["basis"]."/list.html";

              if ( $environment["parameter"][2] == "verify" ) {

                  // form eingaben prüfen
                  form_errors( $form_options, $HTTP_POST_VARS );

                  // form eingaben prüfen erweitern
                  if ( $HTTP_POST_VARS["newpass"] != "" && $HTTP_POST_VARS["newpass"] == $HTTP_POST_VARS["chkpass"] ) {
                      $checked_password = $HTTP_POST_VARS["newpass"];
                      mt_srand((double)microtime()*1000000);
                      $a=mt_rand(1,127);
                      $b=mt_rand(1,127);
                      $mysalt = chr($a).chr($b);
                      $checked_password = crypt($checked_password, $mysalt);
                  } else {
                      $ausgaben["form_error"] .= $form_options["abpasswort"]["ferror"];
                  }

                  // wach 0708
                  if ( $form_values["ablogin"] == "" ) {
                      $ausgaben["form_error"] .= $form_options["ablogin"]["ferror"];
                  }

                  /*
                  if ( $HTTP_POST_VARS["newpass"] == "" || $HTTP_POST_VARS["newpass"] != $HTTP_POST_VARS["chkpass"]  ) {
                      $ausgaben["form_error"] .= $form_options["abpasswort"]["ferror"];
                  } else {
                      $checked_password = $HTTP_POST_VARS["newpass"];
                      mt_srand((double)microtime()*1000000);
                      $a=mt_rand(1,127);
                      $b=mt_rand(1,127);
                      $mysalt = chr($a).chr($b);
                      $checked_password = crypt($checked_password, $mysalt);
                  }
                  */

                  // ohne fehler sql bauen und ausfuehren
                  if ( $ausgaben["form_error"] == "" ) {

                      /*
                      $kick = array( "PHPSESSID", "pass", "newpass", "chkpass", "submit", "avail", "image", "submit_x", "submit_y" );
                      foreach($HTTP_POST_VARS as $name => $value) {
                          if ( !in_array($name,$kick) ) {
                              if ( $sqla != "" ) $sqla .= ",";
                              $sqla .= " ".$name;
                              if ( $sqlb != "" ) $sqlb .= ",";
                              $sqlb .= " '".$value."'";
                          }
                      }

                      */

                      // Sql um spezielle Felder erweitern
                      #$sqla .= ", pass";
                      $sqla = "abpasswort";
                      #$sqlb .= ", '".addslashes($checked_password)."'";
                      $sqlb = "'".addslashes($checked_password)."'";

                      #$sql = "insert into ".$data_entries." (".$sqla.") VALUES (".$sqlb.")";
                      $sql = "UPDATE ".$cfg["db"]["entries"]." SET ".$sqla."=".$sqlb." WHERE ".$cfg["db"]["key"]."='".$HTTP_POST_VARS["abid"]."'";

                      $result  = $db -> query($sql);
                      if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];

                      if ($result) {

                          // level management sql begin
                          // ***
                          if ( is_array($HTTP_POST_VARS["avail"]) ) {
                              #$uid = $db -> lastid();
                              foreach ($HTTP_POST_VARS["avail"] as $name => $value ) {
                                  $sql = "INSERT INTO auth_right (uid, lid) VALUES ('".$HTTP_POST_VARS["abid"]."', '".$value."')";
                                  $db -> query($sql);
                                  if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                              }
                          }
                          // +++
                          // level management sql end

                          header("Location: ".$cfg["basis"]."/list.html");
                      } else {
                          if ( @$db -> error() == 1062 ) {
                              if ( $form_options["level"]["fdberror"] != "" ) {
                                  $ausgaben["form_error"] .= $form_options["level"]["fdberror"];
                              } else {
                                  $ausgaben["form_error"] .= "duplicate username, please change";
                              }
                          }
                          $error = $db -> error("sql error:");
                          if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= $error.$debugging["char"];
                      }

                  }
              }



          } elseif ( $environment["parameter"][1] == "edit" && $rechte[$cfg["right"]["admin"]] == -1  ) {


              #if ( count($HTTP_POST_VARS) == 0 ) {
                  $sql = "SELECT * FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][2]."'";
                  $result = $db -> query($sql);
                  $form_values = $db -> fetch_array($result,$nop);
              #} else {
                  #$form_values = $HTTP_POST_VARS;
              #}

              // wenn berechtigung nicht vorhanden , die
              if ( !in_array($form_values["abdststelle"],$HTTP_SESSION_VARS["dstzugriff"]) ) {
                 die(" Access denied, <br> your ip-adress has been logged");
              }

              // form otions holen
              $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

              // form elememte bauen
              $element = form_elements( $cfg["db"]["entries"], $form_values );


              // form elemente erweitern
              $element["newpass"] = str_replace("abpasswort\"","newpass\"",$element["abpasswort"]);
              $element["chkpass"] = str_replace("abpasswort\"","chkpass\"",$element["abpasswort"]);
              $element["abpasswort"] = "";

              // level management form form elemente begin
              // ***
              $element["add"] = "<input type=\"submit\" name=\"add\" value=\"&lt;&lt;&lt;\">";
              $element["del"] = "<input type=\"submit\" name=\"del\" value=\"&gt;&gt;&gt;\">";
              $element["actual"] = "<select name=\"actual[]\" size=\"10\" multiple>";
              $element["avail"] = "<select name=\"avail[]\" size=\"10\" multiple>";
              # nice sql query tnx@bastard!
              $sql = "SELECT auth_level.lid, auth_level.level, auth_right.uid, auth_right.rid FROM auth_level LEFT JOIN auth_right ON auth_level.lid = auth_right.lid and auth_right.uid = ".$environment["parameter"][2]." ORDER by level";
              $result = $db -> query($sql);
              while ( $all = $db -> fetch_array($result,1) ) {
                  if ( $all["uid"] == $environment["parameter"][2] ) {
                      $element["actual"] .= "<option value=\"".$all["rid"]."\">".$all["level"]."</option>\n";
                  } else {
                      $element["avail"] .= "<option value=\"".$all["lid"]."\">".$all["level"]."</option>\n";
                  }
              }
              $element["actual"] .= "</select>";
              $element["avail"] .= "</select>";
              // +++
              // level management form form elemente end

              // was anzeigen
              $mapping["main"] = crc32($environment["ebene"]).".modify";

              // referer im form mit hidden element mitschleppen
              if ( $HTTP_POST_VARS["form_referer"] == "" ) {
                  $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
                  $ausgaben["form_break"] = $ausgaben["form_referer"];
              } else {
                  $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
                  $ausgaben["form_break"] = $ausgaben["form_referer"];
              }

              // wohin schicken
              $ausgaben["form_error"] = "";


              $ausgaben["form_aktion"] = $cfg["basis"]."/modify,edit,".$environment["parameter"][2].",verify.html";
              #$ausgaben["form_break"] = $cfg["basis"]."/list.html";

              if ( $environment["parameter"][3] == "verify" ) {

                  // form eigaben prüfen
                  form_errors( $form_options, $HTTP_POST_VARS );

                  // form eingaben prüfen erweitern
                  if ( $HTTP_POST_VARS["newpass"] != "" && $HTTP_POST_VARS["newpass"] == $HTTP_POST_VARS["chkpass"] ) {
                      $checked_password = $HTTP_POST_VARS["newpass"];
                      mt_srand((double)microtime()*1000000);
                      $a=mt_rand(1,128);
                      $b=mt_rand(1,128);
                      $mysalt = chr($a).chr($b);
                      $checked_password = crypt($checked_password, $mysalt);
                  } elseif ( $HTTP_POST_VARS["newpass"] != '' || $HTTP_POST_VARS["chkpass"] != '' ) {
                      $ausgaben["form_error"] .= $form_options["abpasswort"]["ferror"];
                  }
                  // wach 0708
                  if ( $form_values["ablogin"] == "" ) {
                      $ausgaben["form_error"] .= $form_options["ablogin"]["ferror"];
                  }

                  // ohne fehler sql bauen und ausfuehren
                  if ( $ausgaben["form_error"] == "" ) {

                      // level management sql begin
                      // ***
                      if ( is_array($HTTP_POST_VARS["avail"]) ) {
                          foreach ($HTTP_POST_VARS["avail"] as $name => $value ) {
                              $sql = "INSERT INTO auth_right (uid, lid) VALUES ('".$environment["parameter"][2]."', '".$value."')";
                              $db -> query($sql);
                              if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                          }
                          if ( isset($HTTP_POST_VARS["add"]) ) {
                              header("Location: ".$cfg["basis"]."/modify,edit,".$environment["parameter"][2].",verify.html");
                          }
                      }

                      if ( is_array($HTTP_POST_VARS["actual"]) ) {
                          foreach ($HTTP_POST_VARS["actual"] as $name => $value ) {
                              $sql = "DELETE FROM auth_right where rid='".$value."'";
                              $db -> query($sql);
                              if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                          }
                          if ( isset($HTTP_POST_VARS["del"]) ) {
                              header("Location: ".$cfg["basis"]."/modify,edit,".$environment["parameter"][2].",verify.html");
                          }
                      }
                      // +++
                      // level management sql end

                      $kick = array( "PHPSESSID", "newpass", "chkpass", "submit", "actual", "avail", "submit_x", "submit_y",
                                     "abid",
                                     "abnamra",
                                     "abnamvor",
                                     "abdstemail" );
                      foreach($HTTP_POST_VARS as $name => $value) {
                          if ( !in_array($name,$kick) ) {
                              if ( $sqla != "" ) $sqla .= ", ";
                              $sqla .= $name."='".$value."'";
                          }
                      }

                      if ( isset($HTTP_POST_VARS["submit"]) ) {
                          // Sql um spezielle Felder erweitern
                          if ( $checked_password != "" ) {
                              $sqla .= ", abpasswort='".$checked_password."'";
                          }
                          $sql = "update ".$cfg["db"]["entries"]." SET ".$sqla." WHERE abid='".$environment["parameter"][2]."'";
                          $result  = $db -> query($sql);
                          if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                          header("Location: ".$cfg["basis"]."/list.html");
                      }
                  }
              }

          } elseif ( $environment["parameter"][1] == "delete" && $rechte["administration"] == -1 ) {

              // ausgaben variablen bauen
              #$sql = "SELECT * FROM ".$data_entries." WHERE uid='".$environment["parameter"][2]."'";
              $sql = "SELECT * FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][2]."'";
              $result = $db -> query($sql);
              $field = $db -> fetch_array($result,$nop);
              foreach($field as $name => $value) {
                  $ausgaben[$name] = $value;
              }

              // wenn berechtigung nicht vorhanden , die
              if ( $HTTP_SESSION_VARS["custom"] != $field["abdststelle"]  ) {
                  die(" Access denied, <br> your ip-adress has been logged");
              }

              // level management form form elemente begin
              // ***
              $sql = "SELECT auth_right.lid, auth_level.level FROM auth_level INNER JOIN auth_right ON auth_level.lid = auth_right.lid WHERE auth_right.uid = ".$environment["parameter"][2]." order by level";
              $result = $db -> query($sql);
              while ( $all = $db -> fetch_array($result,1) ) {
                if ( isset($ausgaben["level"]) ) $ausgaben["level"] .= ", ";
                $ausgaben["level"] .= $all["level"]."";
              }
              if ( !isset($ausgaben["level"]) ) $ausgaben["level"] = "---";
              // +++
              // level management form form elemente end

              // referer im form mit hidden element mitschleppen
              if ( $HTTP_POST_VARS["form_referer"] == "" ) {
                  $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
                  $ausgaben["form_break"] = $ausgaben["form_referer"];
              } else {
                  $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
                  $ausgaben["form_break"] = $ausgaben["form_referer"];
              }

              // was anzeigen
              $mapping["main"] = crc32($environment["ebene"]).".delete";
              $mapping["navi"] = "leer";

              // wohin schicken
              $ausgaben["form_aktion"] = $cfg["basis"]."/modify,delete,".$environment["parameter"][2].".html";
              $ausgaben["form_break"] = $_SERVER["HTTP_REFERER"];

              if ( $HTTP_POST_VARS["delete"] == "true" ) {

                  // level management sql begin
                  // ***
                  $sql = "DELETE FROM auth_right where uid='".$environment["parameter"][2]."'";
                  $db -> query($sql);
                  // +++
                  // level management sql end

                  #$sql = "DELETE FROM ".$data_entries." WHERE uid='".$environment["parameter"][2]."'";
                  $sql = "UPDATE ".$cfg["db"]["entries"]." SET abpasswort='' WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][2]."'";
                  $result  = $db -> query($sql);
                  header("Location: ".$cfg["basis"]."/list.html");
              }
          }


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
