<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: menued-functions.inc.php 311 2005-03-12 21:46:39Z chaot $";
// "funktion loader";
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

    function compilationlist($select=""){
        global $db;

        // selection-bilder, werden aus der site_file geholt
        $sql = "SELECT *
                FROM site_file
                WHERE fhit LIKE '%#p%'";
        $result = $db -> query($sql);

        $compilations = array();
        while ( $data = $db -> fetch_array($result,1) ){
            // alle gruppeneintraege holen
            preg_match_all("/#p([0-9]+)[,]*([0-9]*)#/i",$data["fhit"],$match);
            foreach ( $match[1] as $key=>$value ){

                if ( $match[2][$key] == "" ){
                    $sort[$value] = 0;
                } else {
                    $sort[$value] = $match[2][$key];
                }
                // falsche ausgabe verhindern, falls zwei dateien die gleiche sortiernummer hat
                if ( is_array($dataloop["compilations"][$value]["pics"]) ){
                    while ( is_array($dataloop["compilations"][$value]["pics"][$sort[$value]]) ){
                        $sort[$value]++;
                    }
                }

                $compilations[$value]["id"]     = $value;
                $compilations[$value]["name"]   = "---";
                $compilations[$value]["desc"]  .= $data["fdesc"]." ";

                if ( $value == $select ) {
                    $compilations[$value]["select"] = ' selected="true"';
                } else {
                    $compilations[$value]["select"] = "";
                }
            }
        }

        // aus dem content werden die gruppen rausgezogen und ggf. das dataloop um einen gruppennamen ergaenzt
        $sql = "SELECT * FROM site_text WHERE content LIKE '%[/SEL]%'";
        $result = $db -> query($sql);
        while ( $data = $db -> fetch_array($result,1) ) {

            preg_match_all("/(.*)\[SEL=(.*)\](.*)\[\/SEL\]/Usi",$data["content"],$match);

            foreach ( $match[2] as $key=>$value ){

                // den fall abfangen, dass die selection in [E]-Tags steht
                if ( !strstr($match[0][$key],"[E]")
                    || ( strstr($match[0][$key],"[E]") && strstr($match[0][$key],"[/E]") ) ){

                    $parameter = explode(";",$value);
                    $sel_name  = $match[3][$key];
                    $id = $parameter[0];

                    // gibt es keine bilder zur gruppe, werden die fehlenden dataloop-eintraege nachgeholt
                    if ( !is_array($compilations[$id]) ){
                        $compilations[$id]["id"]   = $id;
                        if ( $id == $select ) {
                            $compilations[$id]["select"] = ' selected="true"';
                        } else {
                            $compilations[$id]["select"] = "";
                        }
                    }

                    if ( $compilations[$id]["name"] == "---"
                    || $compilations[$id]["name"] == "" ){
                        $name = $sel_name;
                    } else {
                        // name wird nur erfasst, wenn er nicht schon drinsteht
                        $buffer_names = explode(", ",$compilations[$id]["name"]);
                        if ( !in_array($sel_name,$buffer_names) ){
                            $name = $compilations[$id]["name"].", ".$sel_name;
                        }
                    }

                    $compilations[$id]["name"] = $name;
                }

            }
        }

        ksort($compilations);

        return $compilations;
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
