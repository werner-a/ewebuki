<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: leer-list.inc.php 738 2007-09-13 11:28:23Z chaot $";
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

        // selection-bilder, werden aus der site_file geholt
        $sql = "SELECT *
                  FROM ".$cfg["db"]["file"]["entries"]."
                 WHERE fhit LIKE '%#p%'";
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql (dropdown): ".$sql.$debugging["char"];
        $result = $db -> query($sql);

        $dataloop["compilations"] = array();
        while ( $data = $db -> fetch_array($result,1) ) {
            // alle gruppeneintraege holen
            preg_match_all("/#p([0-9]*)[,]*([0-9]*)#/i",$data["fhit"],$match);
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

                $dataloop["compilations"][$value]["id"]     = $value;
                $dataloop["compilations"][$value]["name"]   = "---";
                $dataloop["compilations"][$value]["desc"]  .= " ".$data["funder"];
                $dataloop["compilations"][$value]["link"]   = $cfg["basis"]."/collect,".$value.".html";
                $dataloop["compilations"][$value]["pics"][$sort[$value]] = array(
                          "id" => $data["fid"],
                        "art"  => $data["ffart"],
                        "name" => $data["ffname"],
                        "alt"  => $data["funder"]
                );
                // sortieren anhand der angegebenen reihenfolge
                ksort($dataloop["compilations"][$value]["pics"]);
            }
        }

        // aus dem content werden die gruppen rausgezogen und ggf. das dataloop um einen gruppennamen ergaenzt
        $sql = "SELECT * FROM site_text WHERE content LIKE '%[/SEL]%'";
        $result = $db -> query($sql);
        while ( $data = $db -> fetch_array($result,1) ) {

            $preg = "/\[SEL=(.*)\](.*)\[\/SEL\]/";
            $content = $data["content"];
            preg_match_all($preg, $content, $match);

            foreach ( $match[1] as $key=>$value  ){
                $tagwerte = explode(";",$value);

                // gibt es keine bilder zur gruppe, werden die fehlenden dataloop-eintraege nachgeholt
                if ( !is_array($dataloop["compilations"][$tagwerte[1]]) ){
                    $dataloop["compilations"][$tagwerte[1]]["id"]   = $tagwerte[1];
                    $dataloop["compilations"][$tagwerte[1]]["link"] = $cfg["basis"]."/list.html";
                    $dataloop["compilations"][$tagwerte[1]]["pics"] = array();
                }

                $dataloop["compilations"][$tagwerte[1]]["name"] = $match[2][$key];
            }
        }

        // gruppen werden nach ID sortiert
        ksort($dataloop["compilations"]);

        // die einzelnen bilder werden zu vorschauzwecken im dataloop aufgedroeselt
        // ausserdem wird der suchbegriff gefiltert
        foreach ( $dataloop["compilations"] as $key=>$value ){
            if ( ( $search != "" && ( stristr($dataloop["compilations"][$key]["name"],$search)
                                   || stristr($dataloop["compilations"][$key]["desc"],$search) ) )
               || $search == "" ){
                if ( is_array($value["pics"]) ){
                    reset($value["pics"]);
                    for ( $i=0 ; $i <= $cfg["db"]["compilation"]["items"] ; $i++ ){
                        if ( current($value["pics"]) == FALSE ){
                            $dataloop["compilations"][$key]["src_pic".$i] = "/images/default/pos.png";
                        }else{
                            $buffer = current($value["pics"]);
                            $dataloop["compilations"][$key]["src_pic".$i] = $pathvars["filebase"]["webdir"].
                                                                            $buffer["art"]."/".
                                                                            $buffer["id"]."/tn/".
                                                                            $buffer["name"];
                            $dataloop["compilations"][$key]["alt_pic".$i] = $buffer["alt"];
                            $dataloop["compilations"][$key]["href".$i]    = $cfg["basis"]."/".
                                                                            $environment["allparameter"].
                                                                            "/view,o,".$buffer["id"].",".$key.".html";
                        }
                        next($value["pics"]);
                    }
                    if ( count($value["pics"]) == 1 ){
                        $dataloop["compilations"][$key]["num_pics"] = count($value["pics"])." #(img_sing)";
                    }else{
                        $dataloop["compilations"][$key]["num_pics"] = count($value["pics"])." #(img_plural)";
                    }
                }else{
                    for ( $i=0 ; $i <= $cfg["db"]["compilation"]["items"] ; $i++ ){
                        $dataloop["compilations"][$key]["src_pic".$i] = "/images/default/pos.png";
                        $dataloop["compilations"][$key]["alt_pic".$i] = "";
                    }
                    $dataloop["compilations"][$key]["num_pics"] = 0;
                }
            }else{
                unset($dataloop["compilations"][$key]);
            }
        }

        if ( $search == "" ){
            $ausgaben["result"] = "";
        }else{
            $ausgaben["result"] = "#(answera) \"".$search."\" #(answerb) ";
            if ( count($dataloop["compilations"]) == 0 ){
                $ausgaben["result"] .= " #(answerc_no)";
            }else{
                $ausgaben["result"] .= count($dataloop["compilations"])." #(answerc_yes)";
            }
        }

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
