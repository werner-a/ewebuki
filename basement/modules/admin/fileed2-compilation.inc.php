<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "leer - list funktion";
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

    86343 Königsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( $cfg["right"] == "" || $rechte[$cfg["right"]] == -1 ) {

        // funktions bereich
        // ***

        $search = "";
        if ( $_GET["send"] ){
            $search = $_GET["search"];
        }
        $ausgaben["search"] = $search;

        function _compare($a, $b) {
            return ($a["sort"] < $b["sort"]) ? -1 : 1;
        }

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
                    }else{
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

                        $parameter = explode(",",$value);
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
                        }else{
                            $name = $compilations[$id]["name"].", ".$sel_name;
                        }

                        $compilations[$id]["name"] = $name;
                    }

                }
            }

            ksort($compilations);

            return $compilations;
        }

        $dataloop["groups"] = compilationlist($_GET["compID"]);

        // beim ersten aufruf wird die erste compilation genommen
        reset($dataloop["groups"]);
        if ( $_GET["compID"] ){
            $groupid = $_GET["compID"];
        } else {
            $buffer = current($dataloop["groups"]);
            $groupid = $buffer["id"];
        }

        // vor- und zurueck-links
        $vor = "";
        $zurueck = "";
        $aktuell = "";
        foreach ( $dataloop["groups"] as $value ){
            if ( $aktuell != "" ){
                $vor = $value["id"];
                break;
            }
            if ( $value["id"] == $groupid ){
                $aktuell = $groupid;
                $ausgaben["compilation"] = "#".$value["id"].": ".$value["name"];
            }
            if ( $aktuell == "" ) {
                $zurueck = $value["id"];
            }
        }
        if ( $vor != "" ){
            $hidedata["vor"]["link"] = $cfg["basis"]."/compilation.html?compID=".$vor;
        }
        if ( $zurueck != "" ){
            $hidedata["zurueck"]["link"] = $cfg["basis"]."/compilation.html?compID=".$zurueck;
        }

        // dataloop mit den bildern
        $sql = "SELECT *
                  FROM site_file
                 WHERE fhit
                  LIKE '%#p".$_GET["compID"]."%' ORDER BY fid";
        $result = $db -> query($sql);

        // dataloop wird ueber eine share-funktion aufgebaut
        filelist($result,$_GET["compID"]);
        if ( count($dataloop["list"]) > 0 ) {
            usort($dataloop["list"],"_compare");
        }

        $ausgaben["count"] = count($dataloop["list"]);

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["basis"]."/compilation.html";
        $ausgaben["form_break"] = $cfg["basis"]."/list.html";

        // hidden values
        #$ausgaben["form_hidden"] .= "";

        // was anzeigen
        $cfg["path"] = str_replace($pathvars["virtual"],"",$cfg["basis"]);
        $mapping["main"] = crc32($cfg["path"]).".compilation";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (img_plural) #(img_plural)<br />";
            $ausgaben["inaccessible"] .= "# (img_sing) #(img_sing)<br />";

            $ausgaben["inaccessible"] .= "# (answera) #(answera)<br />";
            $ausgaben["inaccessible"] .= "# (answerb) #(answerb)<br />";
            $ausgaben["inaccessible"] .= "# (answerc_no) #(answerc_no)<br />";
            $ausgaben["inaccessible"] .= "# (answerc_yes) #(answerc_yes)<br />";
            $ausgaben["inaccessible"] .= "# (answerc_yes_sing) #(answerc_yes_sing)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        #n/a

        // +++
        // page basics

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
