<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "righted - edit funktion";
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

    if ( priv_check("/".$cfg["righted"]["subdir"]."/".$cfg["righted"]["name"],$cfg["righted"]["right"]) ||
        priv_check_old("",$cfg["righted"]["right"]) ) {

        // page basics
        // ***
        function make_ebene($mid, $ebene="") {
            # call: make_ebene(refid);
            global $db, $cfg;
            $sql = "SELECT refid, entry
                    FROM site_menu
                    WHERE mid='".$mid."'";
            $result = $db -> query($sql);
            $array = $db -> fetch_array($result,$nop);
            $ebene = "/".$array["entry"].$ebene;
            if ( $array["refid"] != 0 ) {
                $ebene = make_ebene($array["refid"],$ebene);
            }
            return $ebene;
        }
        $url = make_ebene($environment["parameter"][1]);

        $ausgaben["url"] = $url;

        $sql = "SELECT * FROM ".$cfg["righted"]["db"]["content"]["entries"]." 
                INNER JOIN ".$cfg["righted"]["db"]["group"]["entries"]." ON ( ".$cfg["righted"]["db"]["content"]["entries"].".gid=".$cfg["righted"]["db"]["group"]["entries"].".gid) 
                INNER JOIN ".$cfg["righted"]["db"]["priv"]["entries"]." ON ( ".$cfg["righted"]["db"]["content"]["entries"].".pid=".$cfg["righted"]["db"]["priv"]["entries"].".pid ) 
                WHERE tname='".$url."'";
        $result = $db -> query($sql);
        while ( $all = $db -> fetch_array($result,1) ) {
            $dataloop["actual"][] = array("group" => $all["beschreibung"], 
                                          "priv" => $all["priv"],
                                          "gid" => $all["gid"],
                                          "pid" => $all["pid"]
                                        );
        }

        $sql ="SELECT * FROM ".$cfg["righted"]["db"]["group"]["entries"];
        $result = $db -> query($sql);
        while ( $all = $db -> fetch_array($result,1) ) {
                $dataloop["group"][] = array(
                                            "value" => $all[$cfg["righted"]["db"]["group"]["key"]],
                                            "name" => $all[$cfg["righted"]["db"]["group"]["name"]]
                                        );
        }

        $sql ="SELECT * FROM ".$cfg["righted"]["db"]["priv"]["entries"];
        $result = $db -> query($sql);
        while ( $all = $db -> fetch_array($result,1) ) {
                $dataloop["priv"][] = array(
                                            "value" => $all[$cfg["righted"]["db"]["priv"]["key"]],
                                            "name" => $all[$cfg["righted"]["db"]["priv"]["name"]]
                                        );
        }


        // form options holen
        $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

        // form elememte bauen
        $element = form_elements( $cfg["righted"]["db"]["content"]["entries"], $form_values );

        // form elemente erweitern
        $element["extension1"] = "<input name=\"extension1\" type=\"text\" maxlength=\"5\" size=\"5\">";
        $element["extension2"] = "<input name=\"extension2\" type=\"text\" maxlength=\"5\" size=\"5\">";

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
        $ausgaben["form_error"] = "";

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["righted"]["basis"]."/edit,".$environment["parameter"][1].",verify.html";
        $sql = "SELECT refid FROM site_menu WHERE mid=".$environment["parameter"][1];
        $result = $db -> query($sql);
        $data = $db -> fetch_array($result,1);
        $ausgaben["form_break"] = $pathvars["virtual"]."/admin/menued/list,".$data["refid"].".html";

        // hidden values
        $ausgaben["form_hidden"] .= "";

        // was anzeigen
        $mapping["main"] = crc32($environment["ebene"]).".modify";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
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
            &&  ( ( $HTTP_POST_VARS["add"] != ""
                && ($HTTP_POST_VARS["group"] != "" && $HTTP_POST_VARS["priv"] != "" ))
            ||  $HTTP_POST_VARS["del"] != "" ) ){

            // form eingaben prüfen
            form_errors( $form_options, $HTTP_POST_VARS );

            // evtl. zusaetzliche datensatz aendern
            if ( $ausgaben["form_error"] == ""  ) {

                // funktions bereich fuer erweiterungen
                // ***

                ### put your code here ###

                if ( $error ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                // +++
                // funktions bereich fuer erweiterungen
            }

            // datensatz aendern
            if ( $ausgaben["form_error"] == ""  ) {

                $kick = array( "PHPSESSID", "form_referer", "send" );
                foreach($HTTP_POST_VARS as $name => $value) {
                    if ( !in_array($name,$kick) && !strstr($name, ")" ) ) {
                        if ( $sqla != "" ) $sqla .= ", ";
                        $sqla .= $name."='".$value."'";
                    }
                }

                // recht hinzufuegen
                if ( $HTTP_POST_VARS["add"] ) {
                        $sql = "SELECT * FROM ".$cfg["righted"]["db"]["content"]["entries"]."
                                WHERE tname='".$url."' AND gid=".$HTTP_POST_VARS["group"]." AND pid=".$HTTP_POST_VARS["priv"];
                        $result = $db -> query($sql);
                        if ( $db -> num_rows($result) == 0 ) {
                            $sql = "INSERT INTO ".$cfg["righted"]["db"]["content"]["entries"]."
                                                (gid,pid,tname)
                                        VALUES ('".$HTTP_POST_VARS["group"]."','".$HTTP_POST_VARS["priv"]."','".$url."')";

                            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                            $result = $db -> query($sql);
                        } else {
                            $ausgaben["form_error"] = "#(error_result)<br />";
                        }

                        #if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");

                    $header = $cfg["righted"]["basis"]."/edit,".$environment["parameter"][1].".html";
                }
                if ( $HTTP_POST_VARS["del"] ) {
                    $del = explode(",",key($HTTP_POST_VARS["del"]));
                    $gid =  $del[0];
                    $pid = $del[1];
                    $sql = "DELETE FROM auth_content WHERE tname='".$url."' AND gid=".$gid." AND pid=".$pid;
                    $result = $db -> query($sql);
                    $header = $cfg["righted"]["basis"]."/edit,".$environment["parameter"][1].".html";
                }

                if ( $header == "" ) $header = $cfg["righted"]["basis"]."/list.html";
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
