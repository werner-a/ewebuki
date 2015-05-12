<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "eine verbesserte version der sitemap-funktion";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2015 Werner Ammon ( wa<at>chaos.de )

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

    function menutree2($refid, $script_name, $art = "", $modify = "", $self = "") {
        global $cfg, $defaults, $environment, $db, $pathvars, $ausgaben, $buffer;

        if ( isset($ausgaben["path"]) ) $ausgaben["path"] = null;
        if ( !isset($environment["parameter"][2]) ) $environment["parameter"][2] = null;
       
        $tree = null;
        $where = null;

        $ReplaceArray1 = array('##href##','##title##','##label##', '##mid##','##refid##');

        $sql = "SELECT  *  FROM  ".$cfg[$script_name]["db"]["menu"]["entries"]."
            INNER JOIN  ".$cfg[$script_name]["db"]["lang"]["entries"]."
                    ON  ".$cfg[$script_name]["db"]["menu"]["entries"].".mid = ".$cfg[$script_name]["db"]["lang"]["entries"].".mid
                 WHERE (".$cfg[$script_name]["db"]["menu"]["entries"].".refid=".$refid.")
                   AND (".$cfg[$script_name]["db"]["lang"]["entries"].".lang='".$environment["language"]."')
                   ".$where."
              ORDER BY  ".$cfg[$script_name]["db"]["menu"]["order"].";";
        $result  = $db -> query($sql);
        $count = $db->num_rows($result);

        if ( empty($buffer["pfad"]) ) $buffer["pfad"] = null;
        if ( empty($buffer["pfad_label"]) ) $buffer["pfad_label"] = null;
        while ( $array = $db -> fetch_array($result,1) ) {
            // aufbau des pfads
            $buffer["pfad"] .= "/".$array["entry"];
            $buffer["pfad_label"] .= "/".$array["label"];
            $title = $array["label"];
            if ( isset($array["extend"]) ) $title = $array["extend"];
            
            $ReplaceArray2 = array("href=\"".$pathvars["virtual"].$buffer["pfad"].".html\"","title=\"".$title."\"",$array["label"],$array["mid"],$array["refid"]);
            
            $item = str_replace($ReplaceArray1, $ReplaceArray2,$cfg["publikationen"]["tree"]["item0"]["b"]);
            
            $item1 = str_replace($ReplaceArray1, $ReplaceArray2,$cfg["publikationen"]["tree"]["item1"]["b"]);
            
            $ausgaben["label"] = $array["label"];
            // wo geht der href hin?
            if ( $array["exturl"] != "" ) {
                $item = "<a class="." href=".$array["exturl"].">".$array["label"]."</a>";
            }

            // in den buffer schreiben wieviel unterpunkte fuer jeweiligen Ueberpunkt vorhanden sind !
            if ( !isset($buffer[$refid]["zaehler"]) ) {
                $buffer[$refid]["zaehler"] = $count;
                $tree .=  str_replace($ReplaceArray1, $ReplaceArray2,$cfg["publikationen"]["tree"]["node"]["b"]);
            }

            // listenpunkt schreiben
            $tree .= str_replace($ReplaceArray1, $ReplaceArray2,$cfg["publikationen"]["tree"]["line"]["b"]).$item.$cfg["publikationen"]["tree"]["item0"]["e"];
            $tree .= $item1.$cfg["publikationen"]["tree"]["item1"]["e"];
            
            // selbstaendiger funktionsaufruf
            $tree .= menutree2($array["mid"], $script_name, $art, $modify, -1);

            // abschliessendes li anbringen
            $tree .= $cfg["publikationen"]["tree"]["line"]["e"];

            // abschliessendes ul anbringen u. pfad kuerzen
            if ( isset($buffer[$refid]["zaehler"]) ) {
                // pfad kuerzen
                $buffer["pfad"] = substr($buffer["pfad"],0,strrpos($buffer["pfad"],"/"));
                $buffer["pfad_label"] = substr($buffer["pfad_label"],0,strrpos($buffer["pfad_label"],"/"));
                // zaehler 1 zuruecksetzen
                $buffer[$refid]["zaehler"] = $buffer[$refid]["zaehler"] -1;
                // ul anbringen wenn zaehler bei 0
                if ( $buffer[$refid]["zaehler"] == 0 ) {
                    if ( $self != "" ) {
                        $tree .= $cfg["publikationen"]["tree"]["node"]["e"];
                    }
                }
            }
        }
        return $tree;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>