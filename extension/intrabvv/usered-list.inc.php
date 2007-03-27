<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $script_name = "$Id$";
    $Script_desc = "Usered-list.inc.php";
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

    $position = $environment["parameter"][1]+0;

    // where bauen
    $where =" WHERE abpasswort!='' ";

    if ( in_array($HTTP_SESSION_VARS["custom"],$HTTP_SESSION_VARS["dstzugriff"]) ){
        foreach($HTTP_SESSION_VARS["dstzugriff"] as $name => $value) {
            if ($whereb != "") {
                $whereb .= " or ";
            } else {
                $whereb ="AND (";
            }
            $whereb .= "abdststelle='".$value."'";
        }
        $whereb .= ")";
    }
    $where .= $whereb;

    // Schnellsuche (mor1305)
    // ***
    $ausgaben["search"] = "";
    if ( $HTTP_GET_VARS["search"] != "" ) {
        $search_value = $HTTP_GET_VARS["search"];
        $ausgaben["search"] = $search_value;
        $ausgaben["result"] = "Ihre Schnellsuche nach \"".$search_value."\" hat ";
        $search_value = explode(" ",$search_value);
        // sql aus get vars erstellen
        $suche = array("abnamkurz","abnamra","abnamvor");
        $wherea = "";

        foreach ( $search_value as $value1 ) {
            if ( $value1 != "" ) {
                if ($getvalues == "") $getvalues = "search=";
                $getvalues .= $value1." ";
                foreach ($suche as $value2) {
                    if ($wherea != "") $wherea .= " or ";
                    $wherea .= $value2. " LIKE '%" .$value1."%'";
               }
            }
        }
        $where .= " AND (".$wherea.")";
    } else {
        $ausgaben["result"] = "";
    }


    // Sql Query

    $sql = "SELECT abid, abbnet, abcnet, abnamra, abnamvor, abnamkurz, abdststelle, adkate, adststelle FROM ".$cfg["db"]["entries"]." INNER JOIN db_adrd ON abdststelle=adid".$where." ORDER by ".$cfg["db"]["order"];

    // Inhalt Selector erstellen und SQL modifizieren
    $inhalt_selector = inhalt_selector( $sql, $position, $cfg["db"]["rows"], $parameter, 1, 10, $getvalues);
    $ausgaben["inhalt_selector"] .= $inhalt_selector[0];
    $sql = $inhalt_selector[1];
    $ausgaben["gesamt"] = $inhalt_selector[2];

    // Daten holen und ausgeben

    $ausgaben["output"] .= "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">";

    $ausgaben["output"] .= "<tr>";
    $class = " class=\"lines\"";
    $ausgaben["output"] .= "<td".$class." colspan=\"14\"><img src=\"".$pathvars["images"]."/pos.png\" alt=\"\" width=\"1\" height=\"1\"></td>";
    $ausgaben["output"] .= "</tr>";
    $class = " class=\"contenthead\"";

    $size  = " width=\"5\"";
    $ausgaben["output"] .= "<td".$class.">Namenskürzel</td>";
    $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
    $ausgaben["output"] .= "<td".$class.">Nachname</td>";
    $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
    $ausgaben["output"] .= "<td".$class.">Vorname</td>";
    $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
    $ausgaben["output"] .= "<td".$class.">Dienststelle</td>";
    $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
    $ausgaben["output"] .= "<td".$class." align=\"right\">Aktion</td>";

    $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
    $ausgaben["output"] .= "</tr><tr>";
    $class = " class=\"lines\"";
    $ausgaben["output"] .= "<td".$class." colspan=\"14\"><img src=\"".$pathvars["images"]."/pos.png\" alt=\"\" width=\"1\" height=\"1\"></td>";
    $ausgaben["output"] .= "</tr>";


    $result = $db -> query($sql);

    $modify  = array (
      "edit"      => array("modify,", "Recht bearbeiten", "administration"),
      "delete"    => array("modify,", "Rechte löschen", "administration"),
      "details"   => array("", "Details","","details")
    );

    $imgpath = $pathvars["images"];

    while ( $field = $db -> fetch_array($result,$nop) ) {

      // naechster datensatz, wenn beschaeftigter nicht angezeigt werden darf   (wach 2507)
      // ***
      if ( !in_array($field["abdststelle"],$HTTP_SESSION_VARS["dstzugriff"]) ){
         continue;
      }
      // +++
      // naechster datensatz, wenn beschaeftigter nicht angezeigt werden darf   (wach 2507)

      $ausgaben["output"] .= "<tr>";
      $class = " class=\"contenttabs\"";

      $size  = " width=\"5\"";
      # $ldate = $field["ldate"];
      # $field["ldate"] = substr($ldate,8,2).".".substr($ldate,5,2).".".substr($ldate,0,4)." ".substr($ldate,11,9);
      $ausgaben["output"] .= "<td".$class.">".$field["abnamkurz"]."</td>";
      $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
      $ausgaben["output"] .= "<td".$class."><a href=\"".$cfg["basis"]."/details,".$field[$cfg["db"]["key"]].".html\">".$field["abnamra"]."</td>";
      $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
      $ausgaben["output"] .= "<td".$class."><a href=\"".$cfg["basis"]."/details,".$field[$cfg["db"]["key"]].".html\">".$field["abnamvor"]."</td>";
      $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
      $ausgaben["output"] .= "<td".$class.">".$field["adkate"]." ".$field["adststelle"]."</td>";
      $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
      $aktion = "";

      // icons "bearbeiten", "löschen" und "details" erstellen
      // wenn berechtigung vorhanden
      foreach($modify as $name => $value) {

          // icon schwarz für eigene dienststelle
          if ( $rechte[$value[2]] == -1
                  && $HTTP_SESSION_VARS["custom"] == $field["abdststelle"]
                  || $value[2] == "") {
                $aktion .= "<a href=\"".$cfg["basis"]."/".$value[0].$name.",".$field[$cfg["db"]["key"]].".html\"><img src=\"".$imgpath.$name.".png\" border=\"0\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></a>";

         // icon rot für fremde dienststelle
         } elseif ( $rechte[$value[2]] == -1
                  && in_array($field["abdststelle"],$HTTP_SESSION_VARS["dstzugriff"])
                  && $name == "edit" ) {
               $aktion .= "<a href=\"".$cfg["basis"]."/".$value[0].$name.",".$field[$cfg["db"]["key"]].".html\"><img src=\"".$imgpath.$name."a.png\" border=\"0\" alt=\"Fremdes Recht bearbeiten"."\" title=\"Fremdes Recht bearbeiten "."\" width=\"24\" height=\"18\"></a>";

          } else {
               $aktion .= "<img src=\"".$pathvars["images"]."pos.png\" alt=\"\" width=\"24\" height=\"18\">";
          }
      }

      $ausgaben["output"] .= "<td".$class." align=\"right\">".$aktion."</td>";
      $ausgaben["output"] .= "<td".$class.$size.">&nbsp;</td>";
      $ausgaben["output"] .= "</tr><tr>";
      $class = " class=\"lines\"";
      $ausgaben["output"] .= "<td".$class." colspan=\"14\"><img src=\"".$pathvars["images"]."/pos.png\" alt=\"\" width=\"1\" height=\"1\"></td>";
      $ausgaben["output"] .= "</tr>";
    }
    $ausgaben["output"] .= "</table>";

    // wohin schicken
    $ausgaben["form_aktion"] = $cfg["basis"]."/list.html";

    #$mapping["main"] = "210295197.list";
    $mapping["main"] = crc32($environment["ebene"]).".list";

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
