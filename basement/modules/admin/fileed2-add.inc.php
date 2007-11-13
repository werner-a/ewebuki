<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "fileed - add funktion";
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

        // auf session losgehen, falls zip bearbeitet wurde
        if ( count($_SESSION["zip_extracted"]) == 0 ) unset($_SESSION["zip_extracted"]);
        if ( is_array($_SESSION["zip_extracted"])  ){
            reset($_SESSION["zip_extracted"]);
            $file_buffer = current($_SESSION["zip_extracted"]);
            $file = $file_buffer["name"];
            while( !file_exists($pathvars["filebase"]["maindir"].$pathvars["filebase"]["new"].$file) ){
                unset($_SESSION["zip_extracted"][$file]);
                $file_buffer = current($_SESSION["zip_extracted"]);
                $file = $file_buffer["name"];
            }
        }
        if ( $file == "" ) {
            $ausgaben["thumbnail"] = thumbnail();
        }

        // keine files in dem new-ordner
        if ( $file == "" ) {
            unset($_SESSION["zip_extracted"]);
            header("Location: ".$cfg["basis"]."/list.html");
        }

        // page basics
        // ***

        #if ( count($_POST) == 0 ) {
        #} else {
            $form_values = $_POST;
        #}

        // form options holen
        $form_options = form_options(crc32($environment["ebene"]).".modify");

        // form elememte bauen
        $element = form_elements( $cfg["db"]["file"]["entries"], $form_values );

        // form elemente erweitern
        $element["upload"] = "";
        $element["fid"] = "";
        $element["ffname"] = str_replace("ffname\"", "ffname\" value=\"".str_replace($_SESSION["uid"]."_","",$file)."\"", $element["ffname"]);
        if ( is_array($_SESSION["zip_extracted"]) ){
            $element["fdesc"] = str_replace("></textarea>", "/>".$file_buffer["desc"]."</textarea>", $element["fdesc"]);
            $element["funder"] = str_replace("value=\"\"", "value=\"".$file_buffer["funder"]."\"", $element["funder"]);
            $element["fhit"] = str_replace("value=\"\"", "value=\"".$file_buffer["compilation"]." ".$file_buffer["fhit"]."\"", $element["fhit"]);
        }

        // +++
        // page basics

        preg_match("/(.*)\.([a-zA-z]{1,4})/i",$file,$match);
        // thumbnail wird vorlaeufig gebaut
        $thumb_srv = $pathvars["filebase"]["maindir"].$pathvars["filebase"]["new"]."tmp".$match[1]."_preview.".$match[2];
        $thumb_web = $pathvars["filebase"]["webdir"].$pathvars["filebase"]["new"]."tmp".$match[1]."_preview.".$match[2];
        if ( !file_exists($thumb_srv) ) {
            $type = $cfg["filetyp"][$match[2]];
            if ( $type == "img" ) {
                switch ( strtolower($match[2]) ) {
                    case "gif":
                        $img_src = @imagecreatefromgif($pathvars["filebase"]["maindir"].$pathvars["filebase"]["new"].$file);
                        break;
                    case "jpg":
                        $img_src = @imagecreatefromjpeg($pathvars["filebase"]["maindir"].$pathvars["filebase"]["new"].$file);
                        break;
                    case "png":
                        $img_src = @imagecreatefrompng($pathvars["filebase"]["maindir"].$pathvars["filebase"]["new"].$file);
                        break;
                    default:
                        die("config error. can't handle ".$match[2]." file");
                }
                resize( $pathvars["filebase"]["maindir"].$pathvars["filebase"]["new"].$file,
                        "preview",
                        $img_src,
                        $cfg["size"][$cfg["fileopt"]["preview_size"]],
                        preg_replace("/\/$/i","",$pathvars["filebase"]["maindir"].$pathvars["filebase"]["new"]),
                        "tmp".$match[1]
                );
            } else {
                $thumb_web = $cfg["iconpath"].$cfg["fileopt"][$type]["thumbnail"];
            }
        }
        $ausgaben["thumbnail"] = $thumb_web;

        // falls zip wird der inhalt gebaut
        if ( $match[2] == "zip" ) {
            $dataloop["zip"] = zip_handling($pathvars["filebase"]["maindir"].$pathvars["filebase"]["new"].$file);
            if ( count($dataloop["zip"]) > 0 ){
                $hidedata["zip"][] = -1;
            }
        }

        // funktions bereich fuer erweiterungen
        // ***

        ### put your code here ###

        // +++
        // funktions bereich fuer erweiterungen


        // page basics
        // ***

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["basis"]."/add,".$environment["parameter"][1].",verify.html";
        $ausgaben["form_break"] = $cfg["basis"]."/list.html";

        // hidden values
        $ausgaben["form_hidden"] .= "";

        // was anzeigen
        $mapping["main"] = crc32($environment["ebene"]).".modify";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($_GET["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
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
                || $_POST["extract"] != ""
                || $_POST["extension2"] != "" ) ) {

            // form eigaben prüfen
            form_errors( $form_options, $_POST );

            // evtl. zusaetzliche datensatz anlegen
            if ( $ausgaben["form_error"] == ""  ) {

                // funktions bereich fuer erweiterungen
                // ***

                ### put your code here ###
                if ( $_POST["extract"] != "" ){
                    // naechste freie compilation-id suchen
                    if ( $_POST["selection"] == -1 ){
                        $buffer = compilation_list();
                        end($buffer);
                        $compid = key($buffer) + 1;
                    } else {
                        $compid = "";
                    }
                    // zip auspacken
                    $not_extracted = zip_handling($pathvars["filebase"]["maindir"].$pathvars["filebase"]["new"].$file,
                                                 $pathvars["filebase"]["maindir"].$pathvars["filebase"]["new"],
                                                 $cfg["filetyp"],
                                                 $cfg["filesize"],
                                                 "",
                                                 $compid
                    );
                    if ( count($not_extracted) > 0 ) {
                        $buffer = array();
                        foreach ( $not_extracted as $value ){
                            $buffer[] = $value["name"];
                        }
                        $ausgaben["form_error"] .= "#(not_compl_extracted)".implode(", ",$buffer);
                    } else {
                        unlink( $pathvars["filebase"]["maindir"].$pathvars["filebase"]["new"].$file );
                        header("Location: ".$cfg["basis"]."/add.html");
                        exit;
                    }
                }

                if ( $error ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                // +++
                // funktions bereich fuer erweiterungen
            }

            // datensatz anlegen
            if ( $ausgaben["form_error"] == ""  ) {

                $kick = array( "PHPSESSID", "form_referer", "send", "image", "image_x", "image_y", "extract", "selection", "bnet", "cnet" );
                foreach($_POST as $name => $value) {
                    if ( !in_array($name,$kick) ) {
                        if ( $sqla != "" ) $sqla .= ",";
                        $sqla .= " ".$name;
                        if ( $sqlb != "" ) $sqlb .= ",";
                        $sqlb .= " '".$value."'";
                    }
                }

                // Sql um spezielle Felder erweitern
                #$sqla .= ", pass";
                #$sqlb .= ", password('".$checked_password."')";
                $sqla .= ", ffart";
                $sqlb .= ", '".strtolower(substr(strrchr($file,"."),1))."'";
                $sqla .= ", fuid";
                $sqlb .= ", '".$_SESSION["uid"]."'";
                $sqla .= ", fdid";
                $sqlb .= ", '".$_SESSION["custom"]."'";


                $sql = "INSERT INTO ".$cfg["db"]["file"]["entries"]." (".$sqla.") VALUES (".$sqlb.")";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $result  = $db -> query($sql);
                #if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                if ( $result ) {
                    $file_id = $db->lastid();
                    $source = $pathvars["filebase"]["maindir"].$pathvars["filebase"]["new"].$file;
                    arrange( $file_id, $source, $file );
                    if ( file_exists($thumb_srv) ) unlink( $thumb_srv );
                    unset($_SESSION["zip_extracted"][$file]);
                } else {
                    $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                }
                if ( $header == "" ) $header = $cfg["basis"]."/add.html";
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
