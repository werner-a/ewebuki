<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
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

        if ( isset($_GET["insert"]) ) {
            for ($i = 1; $i <= $_GET["insert"]; $i++) {
                $sql = "INSERT INTO  site_file (frefid,fuid,fdid,ftname, ffname,ffart,fdesc,funder,fhit)
                             VALUES (0,1,1,'','test.jpg','jpg','TempoTest','TempoTest','TempoTest #p".rand(1000,1100).",".$i."# #p".rand(1000,1100).",".$i."# #p".rand(1000,1100).",".$i."# #p".rand(1000,1100).",".$i."# #p".rand(1000,1100).",".$i."#');";
                $result = $db -> query($sql);
            }
        }

        // loeschen von bilder aus der gruppe
        if ( is_numeric($_GET["del"]) ) {
            // loeschen aus der SESSION-variable
            if ( isset($_SESSION["file_memo"][$_GET["del"]]) ) unset($_SESSION["file_memo"][$_GET["del"]]);
            // loeschen aus dem fhit-feld
            if ( $environment["parameter"][1] != "" ) {
                $sql = "SELECT *
                        FROM site_file
                        WHERE fid=".$_GET["del"]." AND fhit LIKE '%".$environment["parameter"][1].",%'";
                $result = $db -> query($sql);
                if ( $db->num_rows($result) > 0 ) {
                    $data = $db -> fetch_array($result,1);
                    $fhit = preg_replace("/#p".$environment["parameter"][1].",[,0-9]*#/i","",$data["fhit"]);
                    $sql = "UPDATE site_file
                               SET fhit='".trim($fhit)."'
                             WHERE fid=".$_GET["del"];
                    $result = $db -> query($sql);
                }
            }
            header("Location: ".$_SERVER["HTTP_REFERER"]);
        }

        // +++
        // funktions bereich fuer erweiterungen

        // page basics
        // ***

        // bauen des sql
        if ( $environment["parameter"][1] != "" ) {
            // eine bildergruppe wurde angewaehlt (id in der url)
            $sql = "SELECT *
                      FROM ".$cfg["db"]["file"]["entries"]."
                     WHERE fhit LIKE '%#p".$environment["parameter"][1].",%'";
            $ausgaben["groupid"] = $environment["parameter"][1];
        } else {
            // ausgewaehlte dateien werden einer gruppe zugewiesen
            $sql = "SELECT *
                      FROM ".$cfg["db"]["file"]["entries"]."
                     WHERE ".$cfg["db"]["file"]["key"]." IN (".implode(",",$_SESSION["file_memo"]).")";
            $ausgaben["groupid"] = $_POST["groupid"];
            $i = 1;
        }
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql (bilder der gruppe): ".$sql.$debugging["char"];
        $result = $db -> query($sql);
        // falls keine bilder vorhanden sind zur gruppen-uebersicht springen
        if ( $db->num_rows($result) == 0 ) header("Location: ".$cfg["basis"]."/list.html");

        // dataloop mit den ausgewaehlten bildern wird gebaut
        while ( $data = $db -> fetch_array($result,1) ) {
            // in welchen gruppen ist die datei bereits
            preg_match_all("/#p([0-9]*)[,0-9]*#/i",$data["fhit"],$match);
            $containedGroups = array();
            foreach ( $match[1] as $value ) {
                $containedGroups[$value] = $value;
                ksort($containedGroups);
            }

            // festlegung der bild-sortierung
            if ( $environment["parameter"][1] != "" ) {
                // der fhit eintrag wird gesucht, und sortiert
                preg_match("/#p".$environment["parameter"][1]."[,]*([0-9]*)#/i",$data["fhit"],$match);
                $sort = $match[1];
                if ( $match[1] == "" ) $sort = 1;
                // falsche ausgabe verhindern, falls zwei dateien die gleiche sortiernummer hat
                while ( is_array($dataloop["list"][$sort]) ) {
                    $sort++;
                }
            } else {
                $sort = $i*10;
                $i++;
            }

            $dataloop["list"][$sort] = array(
                    "id"   => $data["fid"],
                    "item" => $data["funder"]." (enthalten in folgenden Gruppen: ".implode($containedGroups,", ").")",
                   "title" => $data["funder"],
                     "src" => $pathvars["filebase"]["webdir"].$data["ffart"]."/".$data["fid"]."/tn/".$data["ffname"],
                    "link" => $cfg["basis"]."/".$environment["allparameter"]."/view,o,".$data["fid"].",".$environment["parameter"][1].".html",
                    "sort" => $sort,
                  "delete" => "?del=".$data["fid"],
            );

            // welche werte stehen bereits in fhit
            $form_values[$data["fid"]]["fhit"] = $data["fhit"];

        }
        ksort($dataloop["list"]);

        if ( isset($_GET["renumber"]) ) {
            $i = 1;
            foreach ( $dataloop["list"] as $key=>$value ) {
                $dataloop["list"][$key]["sort"] = $i*10;
                $i++;
            }
        }
        $ausgaben["renumber"] = $cfg["basis"]."/".$environment["allparameter"].".html?renumber";

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

        $dataloop["group_dropdown"] = array();
        while ( $data = $db -> fetch_array($result,1) ) {
            // alle gruppeneintraege holen
            preg_match_all("/#p([0-9]*)[,0-9]*#/i",$data["fhit"],$match);
            foreach ( $match[1] as $value ) {
                $select = "";
                if ( $value == $environment["parameter"][1] ) {
                    $select = ' selected="true"';
                    $ausgaben["groupid"] = "";
                }
                $dataloop["group_dropdown"][$value] = array(
                    "id" => $value,
                    "select" => $select,
                );
            }
        }
        ksort($dataloop["group_dropdown"]);
        // +++
        // funktions bereich fuer erweiterungen


        // page basics
        // ***

        // fehlermeldungen
        $ausgaben["form_error"] = "";

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

            // form eingaben pruefen
            form_errors( $form_options, $_POST );

            // evtl. zusaetzliche datensatz aendern
            if ( $ausgaben["form_error"] == ""  ) {

                // funktions bereich fuer erweiterungen
                // ***

                $groupid = $_POST["groupid"];
                if ( $_POST["all_groups"] != "" ) $groupid = $_POST["all_groups"];

                foreach ( $form_values as $key=>$value ) {
                    // testen, ob die p-nummer schon vorhanden ist
                    if ( strstr($value["fhit"],"#p".$groupid) ) {
                        // bereits vorhandene marke finden und entfernen
                        $fhit  = trim(preg_replace("/#p".$groupid."[,0-9]*#/i", "",$value["fhit"]));
                        $fhit .= " #p".$groupid.",".$_POST["sort"][$key]."#";
                    } else {
                        $fhit = $value["fhit"]." #p".$groupid.",".$_POST["sort"][$key]."#";
                    }
                    $sql = "UPDATE ".$cfg["db"]["file"]["entries"]."
                               SET fhit='".$fhit."'
                             WHERE ".$cfg["db"]["file"]["key"]."='".$key."'";
                    $result  = $db -> query($sql);
                }

                // vorerst sprung zur entsprechenden bildergruppe
                if ( $header == "" ) $header = $cfg["basis"]."/collect,".$groupid.".html";

                unset ($_SESSION["file_memo"]);

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
