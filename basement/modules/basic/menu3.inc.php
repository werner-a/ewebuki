<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id: menu2.inc.php 1977 2014-05-30 12:14:43Z guenther.morhart@googlemail.com $";
  $Script["desc"] = "nexte generation des menu script";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2007 Werner Ammon ( wa<at>chaos.de )

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

    86343 Koenigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** ".$script["name"]." ** ]".$debugging["char"];

    function menu_create($refid = 0, $level = 1) {
        global $buffy,$count,$counter,$last_refid,$cfg, $environment, $db, $pathvars, $specialvars, $rechte, $buffer,$positionArray;
        
        // Variablen definieren, falls sie nicht in der cfg gesetzt wurden
        if ( $cfg["menu"]["css"]["item_active"]         == "" ) $cfg["menu"]["css"]["item_active"]         = "menu-active";
        if ( $cfg["menu"]["level".$level]["enable"]     == "" ) $cfg["menu"]["level".$level]["enable"]     = -1; 
        if ( $cfg["menu"]["level".$level]["full"]       == "" ) $cfg["menu"]["level".$level]["full"]       = -1; 
        if ( $cfg["menu"]["level".$level]["force"]      == "" ) $cfg["menu"]["level".$level]["force"]      =  0; 
        if ( $cfg["menu"]["level".$level]["length"]     == "" ) $cfg["menu"]["level".$level]["length"]     =  1000; 
        if ( $cfg["menu"]["level".$level]["target"]     == "" ) $cfg["menu"]["level".$level]["target"]     = ""; 
        if ( $cfg["menu"]["level".$level]["on"]         == "" ) $cfg["menu"]["level".$level]["on"]         = "<ul>"; 
        if ( $cfg["menu"]["level".$level]["link_on"]    == "" ) $cfg["menu"]["level".$level]["link_on"]    = "<li class=\"##class##\">"; 
        if ( $cfg["menu"]["level".$level]["item_link"]  == "" ) $cfg["menu"]["level".$level]["item_link"]  = "<a href=\"##href##\" title=\"##title##\">##label##</a>"; 
        if ( $cfg["menu"]["level".$level]["item_blank"] == "" ) $cfg["menu"]["level".$level]["item_blank"] = "<span>##label##</span>"; 
        if ( $cfg["menu"]["level".$level]["link_off"]   == "" ) $cfg["menu"]["level".$level]["link_off"]   = "</li>"; 
        if ( $cfg["menu"]["level".$level]["off"]        == "" ) $cfg["menu"]["level".$level]["off"]        = "</ul>"; 
        
        if ( $cfg["menu"]["level".$level]["enable"] == "-1" ) {

            // SQL-Filter bauen
            $where_array = array();
            // ID des Menü-Punktes
            $where_array[] = "(".$cfg["menu"]["db"]["entries"].".refid=".$refid.")";
            // Sprache des Menüpunktes
            $where_array[] = "(".$cfg["menu"]["db"]["language"].".lang='".$environment["language"]."')";
            // keine versteckten Punkte anzeigen
            $where_array[] = "(".$cfg["menu"]["db"]["entries"].".hide IS NULL OR ".$cfg["menu"]["db"]["entries"].".hide IN ('','0'))";
            // nur wenn die Level-Variable "full" gesetzt (-1) wird der Menü-Punkt unabhängig vom Mandatory-Wert angezeigt
            if ( $cfg["menu"]["level".$level]["full"] != "-1" ) {
                $where_array[] = "(".$cfg["menu"]["db"]["entries"].".mandatory='-1')";
            }

            // SQL zusammensetzen
            $sql = "SELECT  *  
                      FROM  ".$cfg["menu"]["db"]["entries"]."
                INNER JOIN  ".$cfg["menu"]["db"]["language"]."
                        ON (".$cfg["menu"]["db"]["entries"].".mid = ".$cfg["menu"]["db"]["language"].".mid)
                     WHERE  ".implode("
                       AND  ",$where_array)."
                  ORDER BY  sort;";

            $result  = $db -> query($sql);
            $count = $db->num_rows($result);

            while ( $array = $db -> fetch_array($result,1) ) {

                // aufbau des pfads
                $buffer["pfad"] .= "/".$array["entry"];
                $buffer["pfad_label"] .= "/".$array["label"];

                // feststellen in welcher Ebene man sich befindet
                $arrEbene = explode("/", $buffer["pfad"]);
                $level    = count($arrEbene)-1;

                // Link-Infos definieren
                $label = $array["label"];
                if ( strlen($label) > $cfg["menu"]["level".$level]["length"] ) {
                    $label = substr($label,0,$cfg["menu"]["level".$level]["length"]-3)."...";
                }
                $title = $array["label"];
                if ( $array["extend"] ) $title = $array["extend"];
                if ( $array["exturl"] != "" ) {
                    $href   = $array["exturl"];
                    $target = $cfg["menu"]["level".$level]["target"];
                } else {
                    $href   = $pathvars["virtual"].$buffer["pfad"].".html";
                    $target = "";
                }

                // Link bauen
                if ( $array["menu_no_link"] == -1 ) {
                    // Falls laut site_menu-Eintrag kein Link gewünscht ist...
                    $link = str_replace(
                                array("##label##", "##title##", "##href##", "##target##"),
                                array($label, $title, $href, $target),
                                $cfg["menu"]["level".$level]["item_blank"]
                            );
                } else {
                    $link = str_replace(
                                array("##label##", "##title##", "##href##", "##target##"),
                                array($label, $title, $href, $target),
                                $cfg["menu"]["level".$level]["item_link"]
                            );
                }

                // in den Buffer schreiben wieviel Unterpunkte fuer den jeweiligen Ueberpunkt vorhanden sind!
                // Falls wir beim ersten Menü-Punkt sind wird der Start der Ebene geschrieben
                if ( !isset($buffer[$refid]["zaehler"]) ) {
                    $buffer[$refid]["zaehler"] = $count;
                    if ( $cfg["menu"]["level".$level]["on"] ) {
                        $tree .= $cfg["menu"]["level".$level]["on"];
                    }

                }    

                $last_refid = $refid;
                if ( $cfg["menu"]["level".$level]["on"] ) {

                    // Start des Punktes
                    $item_start   = $cfg["menu"]["level".$level]["link_on"];

                    // Inhalt des Punktes
                    $item_content = $link;

                    // CSS-Klasse festlegen
                    if ( $array["menu_css"] != "" ) {
                        $class = $array["menu_css"];
                    } else {
                        $class = "";
                    }

                    // Unterpunkte des Punktes
                    if (preg_match("/^".preg_quote($buffer["pfad"],"/")."/", $environment["ebene"]."/".$environment["kategorie"]) ) {
                        // Der Menü-Punkt kommt in der Url vor:
                        $item_sub = menu_create($array["mid"], $level + 1);
                        // Überprüfen, ob der Menüpunkt der Url entspricht
                        $class .= " ".$cfg["menu"]["css"]["item_active"];
                    } elseif ( $cfg["menu"]["level".($level+1)]["force"] == -1 ) {
                        // laut cfg soll das Anzeigen der Menü-Ebenen erzwungen werden:
                        $item_sub = menu_create($array["mid"], $level + 1);
                    } else {
                        $item_sub = "";
                    }

                    // Ende des Punktes
                    $item_end     = $cfg["menu"]["level".$level]["link_off"];

                    // Menü-Punkt zusammenbauen
                    $tree .= str_replace(
                                "##class##",
                                $class,
                                $item_start.$item_content
                             )
                             .$item_sub
                             .$item_end;

                }

                // Überprüfen, ob man beim letzten Menüpunkt der Ebene ist
                // Wenn das der Fall ist wird das Ende der Ebene geschrieben
                if ( isset($buffer[$refid]["zaehler"]) ) {
                    // pfad kuerzen
                    $buffer["pfad"] = substr($buffer["pfad"],0,strrpos($buffer["pfad"],"/"));
                    $buffer["pfad_label"] = substr($buffer["pfad_label"],0,strrpos($buffer["pfad_label"],"/"));
                    // zaehler 1 zuruecksetzen
                    $buffer[$refid]["zaehler"] = $buffer[$refid]["zaehler"] - 1;
                    // Ende der Ebene anbringen wenn zaehler bei 0
                    if ( $buffer[$refid]["zaehler"] == 0  ) {
                        $tree .= $cfg["menu"]["level".$level]["off"];
                    }
                }
            }
            return $tree;
        }
    }

    $ausgaben["menu"] = menu_create();

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
