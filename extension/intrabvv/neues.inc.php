<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script_name = "$Id$";
  $Script_desc = "Neues Applikation";
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

      //
      // Bearbeiten
      //
      if ( strstr($environment["kategorie"], "modify") ) {

          // warning ausgeben
          if ( get_cfg_var('register_globals') == 1 ) $debugging["ausgabe"] .= "Warning register_globals in der php.ini steht auf on, evtl werden interne Variablen ueberschrieben!".$debugging["char"];


          if ( $environment["parameter"][1] == "add" ) {

              // form options holen
              $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

              // form elememte bauen
              $element = form_elements( $db_entries, $HTTP_POST_VARS );

              // form elemente erweitern
              #$modify  = array ( "abdsttel", "abdstmobil", "abdstfax", "abprivtel", "abprivmobil");
              #foreach($modify as $key => $value) {
              #    if ( $HTTP_POST_VARS[$value] == "" ) {
              #          $element[$value] = str_replace($value."\"", $value."\" value=\"+49-\"", $element[$value]);
              #    }
              #}

              // was anzeigen
              $mapping["main"] = crc32($environment["ebene"]).".".$environment["kategorie"];

              // wohin schicken
              $ausgaben["form_error"] = "";
              $ausgaben["form_aktion"] = $environment["basis"]."/".$environment["kategorie"].",add,verify.html";

              // referer im form mit hidden element mitschleppen
              if ( $HTTP_POST_VARS["form_referer"] == "" ) {
                  $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
                  $ausgaben["form_break"] = $ausgaben["form_referer"];
              } else {
                  $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
                  $ausgaben["form_break"] = $ausgaben["form_referer"];
              }

              if ( $environment["parameter"][2] == "verify" ) {

                  // form eigaben prüfen
                  form_errors( $form_options, $HTTP_POST_VARS );

                  // ohne fehler sql bauen und ausfuehren
                  if ( $ausgaben["form_error"] == "" && ( $HTTP_POST_VARS["submit"] != "" || $HTTP_POST_VARS["image"] != "" ) ) {
                      $kick = array( "PHPSESSID", "submit", "image", "image_x", "image_y", "form_referer", "ndatum" );
                      foreach($HTTP_POST_VARS as $name => $value) {
                          if ( !in_array($name,$kick) ) {
                              if ( $sqla != "" ) $sqla .= ",";
                              $sqla .= " ".$name;
                              if ( $sqlb != "" ) $sqlb .= ",";
                              $sqlb .= " '".$value."'";
                          }
                      }

                      // Sql um spezielle Felder erweitern
                      $ndatum = $HTTP_POST_VARS["ndatum"];
                      $ndatum = substr($ndatum,6,4)."-".substr($ndatum,3,2)."-".substr($ndatum,0,2)." ".substr($ndatum,11,9);
                      $sqla .= ", ndatum";
                      $sqlb .= ", '".$ndatum."'";

                      $sql = "insert into ".$db_entries." (".$sqla.") VALUES (".$sqlb.")";
                      $result  = $db -> query($sql);
                      if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                      header("Location: ".$ausgaben["form_referer"]);
                      #header("Location: ".$environment["basis"]."/list.html");
                  }
              }
          } elseif ( $environment["parameter"][1] == "edit" ) {

              if ( count($HTTP_POST_VARS) == 0 ) {
                  $sql = "SELECT * FROM ".$db_entries." WHERE ".$db_entries_key."='".$environment["parameter"][2]."'";
                  $result = $db -> query($sql);
                  $form_values = $db -> fetch_array($result,$nop);
              } else {
                  $form_values = $HTTP_POST_VARS;
              }

              // form otions holen
              $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

              // form elememte bauen
              $element = form_elements( $db_entries, $form_values );

              // was anzeigen
              $mapping["main"] = crc32($environment["ebene"]).".modify";

              // wohin schicken
              $ausgaben["form_error"] = "";
              $ausgaben["form_aktion"] = $environment["basis"]."/modify,edit,".$environment["parameter"][2].",verify.html";

              // referer im form mit hidden element mitschleppen
              if ( $HTTP_POST_VARS["form_referer"] == "" ) {
                  $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
                  $ausgaben["form_break"] = $ausgaben["form_referer"];
              } else {
                  $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
                  $ausgaben["form_break"] = $ausgaben["form_referer"];
              }

              if ( $environment["parameter"][3] == "verify" ) {

                  // form eigaben prüfen
                  form_errors( $form_options, $HTTP_POST_VARS );

                  // ohne fehler sql bauen und ausfuehren
                  if ( $ausgaben["form_error"] == "" && ( $HTTP_POST_VARS["submit"] != "" || $HTTP_POST_VARS["image"] != "" ) ){

                      $kick = array( "PHPSESSID", "submit", "image", "image_x", "image_y", "form_referer" );
                      foreach($HTTP_POST_VARS as $name => $value) {
                          if ( !in_array($name,$kick) ) {
                              if ( $sqla != "" ) $sqla .= ", ";
                              $sqla .= $name."='".$value."'";
                          }
                      }

                      // Sql um spezielle Felder erweitern
                      $ndatum = $HTTP_POST_VARS["ndatum"];
                      $ndatum = substr($ndatum,6,4)."-".substr($ndatum,3,2)."-".substr($ndatum,0,2)." ".substr($ndatum,11,9);
                      $sqla .= ", ndatum='".$ndatum."'";

                      $sql = "update ".$db_entries." SET ".$sqla." WHERE ".$db_entries_key."='".$environment["parameter"][2]."'";
                      $result  = $db -> query($sql);
                      if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                      header("Location: ".$ausgaben["form_referer"]);
                      #header("Location: ".$environment["basis"]."/list.html");
                  }
              }

          } elseif ( $environment["parameter"][1] == "delete" ) {

              // ausgaben variablen bauen
              $sql = "SELECT * FROM ".$db_entries." WHERE ".$db_entries_key."='".$environment["parameter"][2]."'";
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
                  $sql = "DELETE FROM ".$db_entries." WHERE ".$db_entries_key."='".$environment["parameter"][2]."'";
                  $result  = $db -> query($sql);
                  header("Location: ".$environment["basis"]."/list.html");
              }
          }


      //
      // Details anzeigen
      //
      } elseif ( $environment["kategorie"] == "details" ) {

          $sql = "SELECT * FROM ".$db_entries." WHERE ".$db_entries_key."='".$environment["parameter"][1]."'";
          $result = $db -> query($sql);
          $field = $db -> fetch_array($result,$nop);
          foreach($field as $name => $value) {
              $ausgaben[$name] = $value;
          }

          // ausgaben erweitern
          $ndatum = $ausgaben["ndatum"];
          $ausgaben["ndatum"] = substr($ndatum,8,2).".".substr($ndatum,5,2).".".substr($ndatum,0,4)." ".substr($ndatum,11,9);

          $ausgaben["nbeschreibung"] = nlreplace($ausgaben["nbeschreibung"]);
          $ausgaben["nbeschreibung"] = tagreplace($ausgaben["nbeschreibung"]);


          $ausgaben["navigation"] .= "<a href=\"".$_SERVER["HTTP_REFERER"]."\"><img src=\"".$pathvars["images"]."left.png\" border=\"0\" alt=\"Zurück\" title=\"Zurück\" width=\"24\" height=\"18\"></a>";

          if ( $rechte["sti"] == -1 ) {
            $ausgaben["navigation"] .= "<a href=\"".$environment["basis"]."/modify,edit,".$environment["parameter"][1].".html\"><img src=\"".$pathvars["images"]."edit.png\" border=\"0\" alt=\"Bearbeiten\" title=\"Bearbeiten\" width=\"24\" height=\"18\"></a>";
          }
          $mapping["main"] = crc32($environment["ebene"]).".details";

      //
      // Liste anzeigen
      //
      } elseif ( $environment["kategorie"] == "list" || $environment["kategorie"] == $environment["name"] ) {

          $position = $environment["parameter"][1]+0;

          // Suche
          $ausgaben["form_aktion"] = $environment["basis"]."/list,".$position.",search.html";
          $ausgaben["search"] = $HTTP_POST_VARS["search"];
          if ( $HTTP_POST_VARS["search"] != "" ) {
              $ausgaben["result"] = "Ihre Schnellsuche nach \"".$HTTP_POST_VARS["search"]."\" hat folgende Einträge gefunden:<br><br>";
          } else {
              $ausgaben["result"] = "";
          }

          if ( $environment["parameter"][2] == "search" ) {
              if ( $HTTP_POST_VARS["search"] != "" ) {
                  $search_value = $HTTP_POST_VARS["search"];
              } else {
                  $search_value = $environment["parameter"][3];
              }
              $parameter = ",search,".$search_value;
              $where = " WHERE neintrag LIKE '%".$search_value."%' OR nbeschreibung LIKE '%".$search_value."%'";
          }

          // Sql Query
          $sql = "SELECT * FROM ".$db_entries.$where." ORDER by ".$db_entries_order;

          // Inhalt Selector erstellen und SQL modifizieren
          $inhalt_selector = inhalt_selector( $sql, $position, $db_rows, $parameter, 1, 10 );
          $ausgaben["inhalt_selector"] .= $inhalt_selector[0];
          $sql = $inhalt_selector[1];
          $ausgaben["gesamt"] = $inhalt_selector[2];


          // Daten holen und ausgeben

          $ausgaben["output"] .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";

          $ausgaben["output"] .= "<tr>";
          $class = " class=\"lines\"";
          $ausgaben["output"] .= "<td".$class." colspan=\"8\"><img src=\"".$pathvars["images"]."/pos.png\" alt=\"\" width=\"1\" height=\"1\"></td>";
          $ausgaben["output"] .= "</tr>";
          $class = " class=\"contenthead\"";
          #$size  = " width=\"30\" height=\"20\"";
          #$ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
          #$ausgaben["output"] .= "<td".$class.">&nbsp;</td>";

          $size  = " width=\"5\"";
          $ausgaben["output"] .= "<td".$class.">Datum</td>";
          $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
          $ausgaben["output"] .= "<td".$class.">Eintrag</td>";
          $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
          $ausgaben["output"] .= "<td".$class.">Entwickler</td>";
          $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
          #$ausgaben["output"] .= "<td".$class.">eMail</td>";
          #$ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
          #$ausgaben["output"] .= "<td".$class.">Telefon</td>";
          #$ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
          $ausgaben["output"] .= "<td align=\"right\"".$class.">Aktion</td>";
          $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
          $ausgaben["output"] .= "</tr><tr>";
          $class = " class=\"lines\"";
          $ausgaben["output"] .= "<td".$class." colspan=\"8\"><img src=\"".$pathvars["images"]."/pos.gif\" alt=\"\" width=\"1\" height=\"1\"></td>";
          $ausgaben["output"] .= "</tr>";


          $result = $db -> query($sql);
          $modify  = array (
            "edit"        => array("modify,", "Bearbeiten", "sti"), ###
            "delete"      => array("modify,", "Löschen", "sti"),
            "details"     => array("", "Details", ""),
          );
          $imgpath = $pathvars["images"];

          while ( $field = $db -> fetch_array($result,$nop) ) {
              $ausgaben["output"] .= "<tr>";
              $class = " class=\"contenttabs\"";
              #$size  = " width=\"30\" height=\"20\"";
              #$ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
              #$ausgaben["output"] .= "<td".$class.">&nbsp;</td>";

              $size  = " width=\"5\"";

              // ausgaben erweitern
              $ndatum = $field["ndatum"];
              $field["ndatum"] = substr($ndatum,8,2).".".substr($ndatum,5,2).".".substr($ndatum,0,4)." ".substr($ndatum,11,9);

              $ausgaben["output"] .= "<td".$class."><a href=\"".$environment["basis"]."/details,".$field[$db_entries_key].".html\">".$field["ndatum"]."</a></td>";
              $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
              $ausgaben["output"] .= "<td".$class."><a href=\"".$environment["basis"]."/details,".$field[$db_entries_key].".html\">".$field["neintrag"]."</a></td>";
              $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";

              // ausgaben erweitern
              $entwicklerid = "";
              if ( $field["nentwickler"] == "mei" ) {
                  $entwicklerid = "92";
                  $entwicklerart = "beschaeftigte";
              } elseif ( $field["nentwickler"] == "mor" ) {
                  $entwicklerid = "94";
                  $entwicklerart = "beschaeftigte";
              } elseif ( $field["nentwickler"] == "sche" ) {
                  $entwicklerid = "97";
                  $entwicklerart = "beschaeftigte";
              } elseif ( $field["nentwickler"] == "wach" ) {
                  $entwicklerid = "102";
                  $entwicklerart = "beschaeftigte";
              } elseif ( $field["nentwickler"] == "weam" ) {
                  $entwicklerid = "1";
                  $entwicklerart = "kunden";
              }
              if ( $entwicklerid != "" ) $field["nentwickler"] = "<a href=\"".$pathvars["virtual"]."/treffpunkt/adressen/".$entwicklerart."/details,".$entwicklerid.".html\">".$field["nentwickler"]."</a>";

              $ausgaben["output"] .= "<td".$class.">".$field["nentwickler"]."</td>";
              $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
              #$ausgaben["output"] .= "<td".$class."><a href=\"mailto:".$field["abdstemail"]."\">".$field["abdstemail"]."</a></td>";
              #$ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
              #$ausgaben["output"] .= "<td".$class.">".$field["abdsttel"]."</td>";
              #$ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";

              $aktion = "";

              foreach($modify as $name => $value) {
                if ( $value[2] == "" || $rechte[$value[2]] == -1 ) {
                        $aktion .= "<a href=\"".$environment["basis"]."/".$value[0].$name.",".$field[$db_entries_key].".html\"><img src=\"".$imgpath.$name.".png\" border=\"0\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></a>";
                    } else {
                        $aktion .= "<img src=\"".$imgpath."/pos.png\" alt=\"\" width=\"24\" height=\"18\">";
                }
              }
              $ausgaben["output"] .= "<td align=\"right\"".$class.">".$aktion."</td>";
              $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";


              $ausgaben["output"] .= "</tr><tr>";
              $class = " class=\"lines\"";
              $ausgaben["output"] .= "<td".$class." colspan=\"8\"><img src=\"".$pathvars["images"]."/pos.gif\" alt=\"\" width=\"1\" height=\"1\"></td>";
              $ausgaben["output"] .= "</tr>";


          }
          $ausgaben["output"] .= "</table>";
          if ( $rechte["sti"] == -1 ) {
            $ausgaben["eintrag_neu"] = "<a href=\"".$environment["basis"]."/modify,add.html\"><img src=\"".$pathvars["images"]."button-neuerbeitrag.png\" width=\"80\" height=\"18\" border=\"0\"></a>";
          } else {
            $ausgaben["eintrag_neu"] = "<img src=\"".$pathvars["images"]."/pos.png\" width=\"80\" height=\"18\" border=\"0\">";
          }
          #$mapping["main"] = crc32($environment["ebene"]).".".$environment["name"];
          #$mapping["main"] = crc32($environment["ebene"]).".list";
          $mapping["main"] = "23692892.list";
      }

  if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ $script_name ++ ]".$debugging["char"];
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
