<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $script_name = "$Id$";
    $Script_desc = "usered.details.inc.php";
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
    $sql = "SELECT * FROM ".$cfg["db"]["entries"]." WHERE " .$cfg["db"]["key"]."='".$environment["parameter"][1]."'";

    $result = $db -> query($sql);
    $field = $db -> fetch_array($result,$nop);
    foreach($field as $name => $value) {
      $ausgaben[$name] = $value;
    }

    $ausgaben["abdstemail"] = "<a href=\"mailto:".$ausgaben["abdstemail"]."\">".$ausgaben["abdstemail"]."</a>";

    // kategorie und dienststelle holen (weam 2005)
    // ***
    $sql = "SELECT adkate, adststelle FROM db_adrd WHERE adid='".$field["abdststelle"]."'";
    $result = $db -> query($sql);
    $dststelle = $db -> fetch_array($result,$nop);
    $ausgaben["abdststelle"] = $dststelle["adkate"]." ".$dststelle["adststelle"];


    // +++
    // kategorie und dienststelle holen (weam 2005)

    // level management form form elemente begin
    // ***
    $sql = "SELECT auth_right.lid, auth_level.level FROM auth_level INNER JOIN auth_right ON auth_level.lid = auth_right.lid WHERE auth_right.uid = ".$environment["parameter"][1]." order by level";
    $result = $db -> query($sql);
    while ( $all = $db -> fetch_array($result,1) ) {
      if ( isset($ausgaben["level"]) ) $ausgaben["level"] .= ", ";
      $ausgaben["level"] .= $all["level"]."";
    }
    if ( !isset($ausgaben["level"]) ) $ausgaben["level"] = "---";
    // +++
    // level management form form elemente end


    $ldate = $ausgaben["ldate"];
    $ausgaben["ldate"] = substr($ldate,8,2).".".substr($ldate,5,2).".".substr($ldate,0,4)." ".substr($ldate,11,9);

    $ausgaben["ldetail"] = nlreplace($ausgaben["ldetail"]);

    $ausgaben["navigation"] .= "<a href=\"".$_SERVER["HTTP_REFERER"]."\"><img src=\"".$pathvars["images"]."left.png\" border=\"0\" alt=\"Zurück\" title=\"Zurück\" width=\"24\" height=\"18\"></a>";

        // icon "recht hinzufügen" bzw. "recht bearbeiten" erstellen
        // wenn berechtigung vorhanden

        if ( $rechte[$cfg["right"]["admin"]] == -1 && in_array($field["abdststelle"],$HTTP_SESSION_VARS["dstzugriff"]) && $field["abanrede"] != "Raum" ) {

            // icon schwarz für eigene dienststelle
            if ( $HTTP_SESSION_VARS["custom"] == $field["abdststelle"] ) {
                $ausgaben["navigation"] .= "<a href=\"".$cfg["basis"]."/modify,edit,".$environment["parameter"][1].".html\"><img src=\"".$pathvars["images"]."editr.png\" border=\"0\" alt=\"Bearbeiten\" title=\"Recht bearbeiten\" width=\"24\" height=\"18\"></a>";

            // icon rot für andere dienststelle
            } else {
                $ausgaben["navigation"] .= "<a href=\"".$cfg["basis"]."/modify,edit,".$environment["parameter"][1].".html\"><img src=\"".$pathvars["images"]."editra.png\" border=\"0\" alt=\"Bearbeiten\" title=\"Fremdes Recht bearbeiten\" width=\"24\" height=\"18\"></a>";
            }
        }

    #$ausgaben["navigation"] .= "<a href=\"".$cfg["basis"]."/modify,edit,".$environment["parameter"][1].".html\"><img src=\"".$pathvars["images"]."/editr.png\" border=\"0\" alt=\"Bearbeiten\" title=\"Bearbeiten\" width=\"24\" height=\"18\"></a>";
    $mapping["main"] = crc32($environment["ebene"]).".details";


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
