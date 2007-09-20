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

        // loop mit den ausgewaehlten Dateien wird erzeugt
        if ( $environment["parameter"][1] != "" ){
            $sql = "SELECT *
                      FROM ".$cfg["db"]["file"]["entries"]."
                     WHERE fhit LIKE '%#p".$environment["parameter"][1]."%'";
            $ausgaben["groupid"] = $environment["parameter"][1];
            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $result = $db -> query($sql);
            $i = 1;
            while ( $data = $db -> fetch_array($result,1) ) {
                preg_match("/#p".$environment["parameter"][1]."[,0-9]*#/i",$data["fhit"],$match);
                $sort = (str_replace(array("#p","#"),"",$match[0]));
                $sort = substr(strstr($sort,","),1);
                $dataloop["list"][$sort] = array(
                    "id"   => $data["fid"],
                    "item" => $data["funder"]." (".$data["fhit"].")",
                    "sort" => $sort,
                );
                ksort($dataloop["list"]);
                // was steht schon im fhit-feld
                $form_values[$data["fid"]]["fhit"] = $data["fhit"];
            }
        }else{
            $sql = "SELECT *
                      FROM ".$cfg["db"]["file"]["entries"]."
                     WHERE ".$cfg["db"]["file"]["key"]." IN (".implode(",",$_SESSION["file_memo"]).")";
            $ausgaben["groupid"] = $_POST["groupid"];
            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $result = $db -> query($sql);
            $i = 1;
            while ( $data = $db -> fetch_array($result,1) ) {
                $dataloop["list"][$i] = array(
                    "id"   => $data["fid"],
                    "item" => $data["funder"]." (".$data["fhit"].")",
                    "sort" => $i*10,
                );
                $i++;
                // was steht schon im fhit-feld
                $form_values[$data["fid"]]["fhit"] = $data["fhit"];
            }
        }

        // form options holen
        $form_options = form_options(crc32($environment["ebene"]).".modify");

        // form elememte bauen
        $element = form_elements( $cfg["db"]["file"]["entries"], $form_values );

        // +++
        // page basics


        // funktions bereich fuer erweiterungen
        // ***

        ### put your code here ###

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
        if ( isset($HTTP_GET_VARS["edit"]) ) {
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
                        // bei leerem sortierfeld wird das bild rausgeworfen
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
                if ( $header == "" ) $header = $cfg["basis"]."/edit.html";

//                 unset ($_SESSION["file_memo"][$environment["parameter"][1]]);

                ### put your code here ###

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
