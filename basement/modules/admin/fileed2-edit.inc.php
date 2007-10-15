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

    86343 Kï¿?igsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( $cfg["right"] == "" || $rechte[$cfg["right"]] == -1 ) {

        // funktions bereich fuer erweiterungen
        // ***

        if ( $environment["parameter"][1] == "" ) {
            if ( count($_SESSION["file_memo"]) > 0 ) {
                $environment["parameter"][1] = current($_SESSION["file_memo"]);
            } else {
                header("Location: ".$cfg["basis"]."/list.html");
            }
        }

        // +++
        // funktions bereich fuer erweiterungen


        // page basics
        // ***

        $sql = "SELECT *
                  FROM ".$cfg["db"]["file"]["entries"]."
                  JOIN ".$cfg["db"]["user"]["entries"]."
                    ON (".$cfg["db"]["file"]["user"]."=".$cfg["db"]["user"]["key"].")
                 WHERE ".$cfg["db"]["file"]["key"]."='".$environment["parameter"][1]."'";
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
        $result = $db -> query($sql);
        $form_values = $db -> fetch_array($result,1);
        if ( count($_POST) != 0 ) {
            $form_values = array_merge($form_values,$_POST);
            $form_values["ffart"] = strtolower(substr(strrchr($form_values["ffname"],"."),1));
        }

        // form options holen
        $form_options = form_options(crc32($environment["ebene"]).".modify");

        // form elememte bauen
        $element = form_elements( $cfg["db"]["file"]["entries"], $form_values );

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // form elemente erweitern
        // link zum thumbnail wird gebaut
        $type = $cfg["filetyp"][$form_values["ffart"]];
        if ( $type == "img" ) {
            $path = $cfg["fileopt"][$type]["path"]."original/";
            $filename = "img_".$form_values["fid"].".".$form_values["ffart"];
        } else {
            $path = $cfg["fileopt"][$type]["tnpath"].ltrim($cfg["iconpath"],"/");
            $filename = $cfg["fileopt"][$type]["thumbnail"];
        }
        $ausgaben["thumbnail"] = $pathvars["webroot"]."/images/magic.php?path=".$path.$filename."&size=280";


        if ( $_SESSION["uid"] == $form_values["fuid"] ) { # nur eigene dateien duerfen ersetzt werden
            $hidedata["upload"][0] = -1;
            $owner_error = "";
        } else {
            $owner_error = "#(error_edit)";
            $element["fdesc"] = str_replace(">"," readonly=\"true\">",$element["fdesc"]);
            $element["fhit"] = str_replace(">"," readonly=\"true\">",$element["fhit"]);
            $element["funder"] = str_replace(">"," readonly=\"true\">",$element["funder"]);
        }

        // besitzer feststellen
        $hidedata["owner"] = array(
             "name" => $form_values[$cfg["db"]["user"]["forename"]]." ".$form_values[$cfg["db"]["user"]["surname"]],
            "email" => $form_values[$cfg["db"]["user"]["email"]],
            "error" => $owner_error,
        );

        // wo im content wird die datei verwendet
        if ( content_check($environment["parameter"][1]) == True && $error == 0 ){
            $ausgaben["reference"] = implode("<br />",$arrError);
        }else{
            $ausgaben["reference"] = "---";
        }

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
        $ausgaben["form_aktion"] = $cfg["basis"]."/edit,".$environment["parameter"][1].",verify.html";
        $ausgaben["form_break"] = $cfg["basis"]."/list.html";

        // hidden values
        $ausgaben["form_hidden"] .= "";

        // was anzeigen
        $mapping["main"] = crc32($environment["ebene"]).".modify";
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
            form_errors( $form_options, $_POST );

            // evtl. zusaetzliche datensatz aendern
            if ( $ausgaben["form_error"] == ""   ) {

                if ( $owner_error == "" ){

                    // funktions bereich fuer erweiterungen
                    // ***

                    // file ersetzen
                    if ( $_FILES["upload"]["name"] != "" ) {
                        $file = file_verarbeitung($pathvars["filebase"]["new"], "upload", $cfg["filesize"], array( $form_values["ffart"] ), $pathvars["filebase"]["maindir"]);
                        if ( $file["returncode"] == 0 ) {
                            $file_id = $form_values["fid"];
                            $source = $pathvars["filebase"]["maindir"].$pathvars["filebase"]["new"].$file["name"];
                            arrange( $file_id, $source, $file["name"] );
                        } else {
                            $ausgaben["form_error"] .= "Ergebnis: ".$file["name"]." ".file_error($file["returncode"]);
                        }
                    }

                    ### put your code here ###

                    if ( $error ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                    // +++
                    // funktions bereich fuer erweiterungen

                    $kick = array( "PHPSESSID", "form_referer", "send", "image", "image_x", "image_y" );
                    foreach($_POST as $name => $value) {
                        if ( !in_array($name,$kick) && !strstr($name, ")" ) ) {
                            if ( $sqla != "" ) $sqla .= ", ";
                            $sqla .= $name."='".$value."'";
                        }
                    }

                    // Sql um spezielle Felder erweitern
                    #$ldate = $_POST["ldate"];
                    #$ldate = substr($ldate,6,4)."-".substr($ldate,3,2)."-".substr($ldate,0,2)." ".substr($ldate,11,9);
                    #$sqla .= ", ldate='".$ldate."'";

                    $sql = "update ".$cfg["db"]["file"]["entries"]." SET ".$sqla." WHERE ".$cfg["db"]["file"]["key"]."='".$environment["parameter"][1]."'";
                    if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                    $result  = $db -> query($sql);
                    if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                    if ( $header == "" ) $header = $cfg["basis"]."/edit.html";

                }else{
                    if ( $header == "" ) $header = $cfg["basis"]."/edit.html";
                }

                unset ($_SESSION["file_memo"][$environment["parameter"][1]]);
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
