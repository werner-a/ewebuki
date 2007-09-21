<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: fileed-edit.inc.php,v 1.6 2006/10/10 11:04:15 chaot Exp $";
// "edit - edit funktion";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2006 Werner Ammon ( wa<at>chaos.de )

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

        // funktions bereich fuer erweiterungen
        // ***

        // +++
        // funktions bereich fuer erweiterungen


        // page basics
        // ***

        // bauen des sql
        if ( $environment["parameter"][1] != "" ){
            // eine bildergruppe wurde angewaehlt
            $sql = "SELECT *
                      FROM ".$cfg["db"]["file"]["entries"]."
                     WHERE fhit LIKE '%#p".$environment["parameter"][1]."%'";
            $ausgaben["groupid"] = $environment["parameter"][1];
        }else{
            // ausgewaehlte dateien werden einer gruppe zugewiesen
            $sql = "SELECT *
                      FROM ".$cfg["db"]["file"]["entries"]."
                     WHERE ".$cfg["db"]["file"]["key"]." IN (".implode(",",$_SESSION["file_memo"]).")";
            $ausgaben["groupid"] = $_POST["groupid"];
            $i = 1;
        }
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql (bilder der gruppe): ".$sql.$debugging["char"];
        $result = $db -> query($sql);

        // dataloop wird gebaut
        while ( $data = $db -> fetch_array($result,1) ) {
            // in welchen gruppen ist die datei bereits
            preg_match_all("/#p([0-9]*)[,0-9]*#/i",$data["fhit"],$match);
            $containedGroups = array();
            foreach ( $match[1] as $value ){
                $containedGroups[$value] = $value;
                ksort($containedGroups);
            }

            // festlegung der bild-sortierung
            if ( $environment["parameter"][1] != "" ){
                // der fhit eintrag wird gesucht, und sortiert
                preg_match("/#p".$environment["parameter"][1]."[,]*([0-9]*)#/i",$data["fhit"],$match);
                $sort = $match[1];
            }else{
                $sort = $i*10;
                $i++;
            }

            $dataloop["list"][$sort] = array(
                "id"   => $data["fid"],
                "item" => $data["funder"]." (enthalten in folgenden Gruppen: ".implode($containedGroups,", ").")",
                "sort" => $sort,
            );

            // welche werte stehen bereits in fhit
            $form_values[$data["fid"]]["fhit"] = $data["fhit"];

        }
        ksort($dataloop["list"]);

        // form options holen
        $form_options = form_options(crc32($environment["ebene"]).".modify");

        // form elememte bauen
        $element = form_elements( $cfg["db"]["file"]["entries"], $form_values );

        // +++
        // page basics


        // funktions bereich fuer erweiterungen
        // ***

        // dropdown mit bereits vorhandenen gruppen
        $sql = "SELECT *
                    FROM ".$cfg["db"]["file"]["entries"]."
                    WHERE fhit LIKE '%#p%'";
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql (dropdown): ".$sql.$debugging["char"];
        $result = $db -> query($sql);

        while ( $data = $db -> fetch_array($result,1) ) {
            // alle gruppeneintraege holen
            preg_match_all("/#p([0-9]*)[,0-9]*#/i",$data["fhit"],$match);
            foreach ( $match[1] as $value ){
                $dataloop["group_dropdown"][$value] = array(
                    "id" => $value
                );
            }
            ksort($dataloop["group_dropdown"]);
        }

        // +++
        // funktions bereich fuer erweiterungen


        // page basics
        // ***

        // fehlermeldungen
        #$ausgaben["form_error"] = ""; siehe edit sperre!

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["basis"]."/collect,".$environment["parameter"][1].",verify.html";
        $ausgaben["form_break"] = $cfg["basis"]."/list.html";

        // hidden values
        $ausgaben["form_hidden"] .= "";

        // was anzeigen
        $mapping["main"] = crc32($environment["ebene"]).".collect";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($_GET["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_edit) #(error_edit)<br />";
            $ausgaben["inaccessible"] .= "# (error_result) #(error_result)<br />";
            $ausgaben["inaccessible"] .= "# (error_dupe) #(error_dupe)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        #n/a

        // +++
        // page basics

        if ( $environment["parameter"][2] == "verify"
            &&  ( $_POST["send"] != ""
                || $_POST["extension1"] != ""
                || $_POST["extension2"] != "" ) ) {

            // form eingaben prüfen
//             form_errors( $form_options, $_POST );

            // evtl. zusaetzliche datensatz aendern
            if ( $ausgaben["form_error"] == ""  ) {

                // funktions bereich fuer erweiterungen
                // ***

                foreach ( $form_values as $key=>$value ){
                    // testen, ob die p-nummer schon vorhanden ist
                    if ( strstr($value["fhit"],"#p".$_POST["groupid"]) ){
                        // bereits vorhandene marke finden und entfernen
                        $fhit  = trim(preg_replace("/#p".$_POST["groupid"]."[,0-9]*#/i", "",$value["fhit"]));
                        // bei leerem sortier-input-feld wird das bild rausgeworfen
                        if ( $_POST["sort"][$key] != 0 || $_POST["sort"][$key] == "" ){
                            $fhit .= " #p".$_POST["groupid"].",".$_POST["sort"][$key]."#";
                        }
                    }else{
                        $fhit = $value["fhit"]." #p".$_POST["groupid"].",".$_POST["sort"][$key]."#";
                    }
                    $sql = "UPDATE ".$cfg["db"]["file"]["entries"]."
                               SET fhit='".$fhit."'
                             WHERE ".$cfg["db"]["file"]["key"]."='".$key."'";
                    $result  = $db -> query($sql);
                }

                // vorerst sprung zur entsprechenden bildergruppe
                if ( $header == "" ) $header = $cfg["basis"]."/collect,".$_POST["groupid"].".html";

                unset ($_SESSION["file_memo"][$environment["parameter"][1]]);

                if ( $error ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                // +++
                // funktions bereich fuer erweiterungen
            }

            // wenn es keine fehlermeldungen gab, die uri $header laden
            if ( $ausgaben["form_error"] == "" ) {
                header("Location: ".$header);
            }
        }
    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
