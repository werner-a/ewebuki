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

    function menu_create($refid, $self = "") {
        global $buffy,$count,$counter,$last_refid,$cfg, $environment, $db, $pathvars, $specialvars, $rechte, $buffer,$positionArray;
                                                 
        $where = "AND (".$cfg["menu"]["db"]["entries"].".hide IS NULL OR ".$cfg["menu"]["db"]["entries"].".hide IN ('','0'))";
        
        $sql = "SELECT  *  FROM  ".$cfg["menu"]["db"]["entries"]."
            INNER JOIN  ".$cfg["menu"]["db"]["language"]."
                    ON  ".$cfg["menu"]["db"]["entries"].".mid = ".$cfg["menu"]["db"]["language"].".mid
                 WHERE (".$cfg["menu"]["db"]["entries"].".refid=".$refid.")
                   AND (".$cfg["menu"]["db"]["language"].".lang='".$environment["language"]."')
                   ".$where."
              ORDER BY  sort;";

        $result  = $db -> query($sql);
        $count = $db->num_rows($result);
        
        while ( $array = $db -> fetch_array($result,1) ) {

            // aufbau des pfads
            $buffer["pfad"] .= "/".$array["entry"];
            $buffer["pfad_label"] .= "/".$array["label"];

            $tmp = explode("/", $buffer["pfad"]);
            $ebene = count ($tmp)-1;

            
            
            $title = $array["label"];
            if ( $array["extend"] ) $title = $array["extend"];
            $href = "<a href=\"".$pathvars["virtual"].$buffer["pfad"].".html\" title=\"".$title."\">".$array["label"]."</a>";

            // wo geht der href hin?
            if ( $array["exturl"] != "" ) {
                $href = "<a href=".$array["exturl"].">".$array["label"]."</a>";
            }
            ;
            // in den buffer schreiben wieviel unterpunkte fuer jeweiligen Ueberpunkt vorhanden sind !
            if ( !isset($buffer[$refid]["zaehler"]) ) {
                $buffer[$refid]["zaehler"] = $count;
                if ( $cfg["menu"]["level".$ebene]["on"] ) {
                    $tree .= $cfg["menu"]["level".$ebene]["on"];
                }

            }    
            
            $last_refid = $refid;
            if ( $cfg["menu"]["level".$ebene]["on"] ) {
            // listenpunkt schreiben
            $tree .= $cfg["menu"]["level".$ebene]["link_on"].$href;

            // funktionsaufruf
            $tree .= menu_create($array["mid"],-1);

            // abschliessendes li anbringen
            $tree .= $cfg["menu"]["level".$ebene]["link_off"];
            }
            // abschliessendes ul anbringen u. pfad kuerzen
            if ( isset($buffer[$refid]["zaehler"]) ) {
                // pfad kuerzen
                $buffer["pfad"] = substr($buffer["pfad"],0,strrpos($buffer["pfad"],"/"));
                $buffer["pfad_label"] = substr($buffer["pfad_label"],0,strrpos($buffer["pfad_label"],"/"));
                // zaehler 1 zuruecksetzen
                $buffer[$refid]["zaehler"] = $buffer[$refid]["zaehler"] -1;
                // ul anbringen wenn zaehler bei 0
                if ( $buffer[$refid]["zaehler"] == 0  ) {
                    $tree .= $cfg["menu"]["level".$ebene]["off"];
                }
            }
        }
        return $tree;
    }

    $ausgaben["menu"] = menu_create('0');

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
