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
                         "art" => $data["ffart"],
                        "name" => $data["ffname"],
                         "alt" => $data["funder"],
                );
                // sortieren anhand der angegebenen reihenfolge
                ksort($dataloop["compilations"][$value]["pics"]);
            }
        }

        // aus dem content werden die gruppen rausgezogen und ggf. das dataloop um einen gruppennamen ergaenzt
        $sql = "SELECT * FROM site_text WHERE content LIKE '%[/SEL]%'";
        $result = $db -> query($sql);
        while ( $data = $db -> fetch_array($result,1) ) {

            $parts = explode("[/SEL]",$data["content"]);
            array_pop($parts);

            foreach ( $parts as $value ){
                $sel_wert = explode("[SEL=",$value);
                $buffer = explode("]",$sel_wert[1]);

                $parameter = explode(";",$buffer[0]);
                $sel_name  = $buffer[1];

                // gibt es keine bilder zur gruppe, werden die fehlenden dataloop-eintraege nachgeholt
                if ( !is_array($dataloop["compilations"][$parameter[1]]) ){
                    $dataloop["compilations"][$parameter[1]]["id"]   = $parameter[1];
                    $dataloop["compilations"][$parameter[1]]["link"] = $cfg["basis"]."/list.html";
                    $dataloop["compilations"][$parameter[1]]["pics"] = array();
                }

                if ( $dataloop["compilations"][$parameter[1]]["name"] == "---" || $dataloop["compilations"][$parameter[1]]["name"] == "" ){
                    $name = $sel_name;
                }else{
                    $name = $dataloop["compilations"][$parameter[1]]["name"].", ".$sel_name;
                }

                $dataloop["compilations"][$parameter[1]]["name"] = $name;

            }
        }

        // gruppen werden nach ID sortiert
        ksort($dataloop["compilations"]);

        // die einzelnen bilder werden zu vorschauzwecken im dataloop aufgedroeselt
        // ausserdem wird der suchbegriff gefiltert
        foreach ( $dataloop["compilations"] as $key=>$value ){
            if ( ( $search != "" && ( stristr($dataloop["compilations"][$key]["name"],$search)
                                   || stristr($dataloop["compilations"][$key]["id"],  $search)
                                   || stristr($dataloop["compilations"][$key]["desc"],$search) ) )
               || $search == "" ){
                if ( is_array($value["pics"]) ){

                    $i = 0;
                    foreach( $value["pics"] as $pic ){
                        if ( $i == $cfg["db"]["compilation"]["items"] ) break;
                        $ausgaben["scr"]  = $pathvars["filebase"]["webdir"].
                                            $pic["art"].
                                            "/".$pic["id"].
                                            "/tn/".
                                            $pic["name"];
                        $ausgaben["link"] = $pathvars["virtual"].
                                            $environment["ebene"].
                                            "/".$environment["allparameter"].
                                            "/view,o,".$pic["id"].",".$key.".html";
                        $ausgaben["alt"]  = $pic["desc"];
                        $dataloop["compilations"][$key]["thumbs"] .= parser("compilation-preview", "");
                        $i++;
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
            }elseif ( count($dataloop["compilations"]) == 1 ){
                $ausgaben["result"] .= count($dataloop["compilations"])." #(answerc_yes_sing)";
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