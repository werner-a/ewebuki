<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: leer-edit.inc.php 667 2007-08-10 18:13:18Z chaot $";
// "contented - history";
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


// Zeilen-Array aus den Texten machen
#$lines1 = explode("\n", $hallo);
#$lines2 = explode("\n", $hallo1);

// Objekte erstellen
#$diff = new Text_Diff('auto', array($lines1, $lines2));
#$renderer = new Text_Diff_Renderer_inline();
    #$renderer1 = $diff -> reverse();
    #$renderer =& new Text_Diff_Renderer_unified();
    #$renderer =& new Text_Diff_Renderer_context();
    #$renderer =& new Text_Diff_Engine_native();
// Ausgabe


#echo "<pre>";
#print_r($renderer1);
#print_r ($renderer->render($diff));
#echo "</pre>";


    if ( priv_check("/".$cfg["contented"]["subdir"]."/".$cfg["contented"]["name"],$cfg["contented"]["right"]) ||
        priv_check_old("",$cfg["contented"]["right"]) ) {
        if ( $environment["parameter"][3] == "" )  $environment["parameter"][3] = $cfg["contented"]["default_label"];
        $environment["parameter"][2] = preg_replace("/^0./","",$environment["parameter"][2]);
        // page basics
        // ***
        $ausgaben["diff"] = "";
        #if ( count($HTTP_POST_VARS) == 0 ) {
            $sql = "SELECT *
                      FROM site_text
                     WHERE tname='".$environment["parameter"][2]."' AND label='".$environment["parameter"][3]."' ORDER BY version DESC";
            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $result = $db -> query($sql);

            while ( $form_values = $db -> fetch_array($result,1) ) {
                $dataloop["list"][$form_values["version"]]["url"] = $_SESSION["REFERER"].",v".$form_values["version"].".html";
                $dataloop["list"][$form_values["version"]]["date"] = $form_values["changed"];
                $dataloop["list"][$form_values["version"]]["name"] = $form_values["bysurname"];
                $dataloop["list"][$form_values["version"]]["cb1"] = $form_values["version"];
                $dataloop["list"][$form_values["version"]]["cb2"] = $form_values["version"];
            }
        #} else {
           # $form_values = $HTTP_POST_VARS;
        #}

        if ( $_POST["old"] != "" && $_POST["new"]!= "" ) {
            $sql = "SELECT content,version FROM site_text WHERE tname='".$environment["parameter"][2]."' AND label='".$environment["parameter"][3]."' AND version=".$_POST["old"];
            $result = $db -> query($sql);
            $data_old = $db -> fetch_array($result,1);
            $sql = "SELECT content,version FROM site_text WHERE tname='".$environment["parameter"][2]."' AND label='".$environment["parameter"][3]."' AND version=".$_POST["new"];
            $result = $db -> query($sql);
            $data_new = $db -> fetch_array($result,1);

            if ( $data_new["version"] > $data_old["version"] ) {
                $first = $data_new["content"];
                $second = $data_old["content"];
            } else {
                $first = $data_old["content"];
                $second = $data_new["content"];
            }

            $old_array = explode("\n", $second);
            $new_array = explode("\n", $first);

            if ( strstr($cfg["contented"]["diff_engine"],"phpdiff") ) {
                if ( $cfg["contented"]["diff_engine"] == "phpdiff3" ) {
                    $diff = phpdiff3($second,$first);
                } else {
                    $diff = arr_diff($old_array,$new_array);
                }
                $ausgaben["diff"] = $diff;
            } else {
                $diff = new Text_Diff('auto', array($old_array, $new_array));
                $renderer = new Text_Diff_Renderer_inline();
                $ausgaben["diff"] = str_replace("\n","<br>",$renderer->render($diff));
            }
        }

        #if ( $environment["parameter"][4] ) {
        #    $sql = "SELECT * FROM site_text WHERE tname='".$environment["parameter"][2]."' AND label='".$environment["parameter"][3]."' AND version='".$environment["parameter"][4]."'";
        #    $result = $db -> query($sql);
        #    $form_values = $db -> fetch_array($result,1);
        #}

        // form options holen
        $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

        // form elememte bauen
        $element = form_elements( $cfg["contented"]["db"]["leer"]["entries"], $form_values );

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

        $ausgaben["form_aktion"] = $cfg["contented"]["basis"]."/history,".$environment["parameter"][1].",".$environment["parameter"][2].",".$environment["parameter"][3].",verify.html";
        $ausgaben["form_break"] = $cfg["contented"]["basis"]."/list.html";

        // hidden values
        $ausgaben["form_hidden"] .= "";

        // was anzeigen
        #$mapping["main"] = crc32($environment["ebene"]).".modify";
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
            &&  ( $HTTP_POST_VARS["send"] != ""
                || $HTTP_POST_VARS["extension1"] != ""
                || $HTTP_POST_VARS["extension2"] != "" ) ) {

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

                // Sql um spezielle Felder erweitern
                #$ldate = $HTTP_POST_VARS["ldate"];
                #$ldate = substr($ldate,6,4)."-".substr($ldate,3,2)."-".substr($ldate,0,2)." ".substr($ldate,11,9);
                #$sqla .= ", ldate='".$ldate."'";

                $sql = "update ".$cfg["contented"]["db"]["leer"]["entries"]." SET ".$sqla." WHERE ".$cfg["contented"]["db"]["leer"]["key"]."='".$environment["parameter"][1]."'";
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $result  = $db -> query($sql);
                if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                if ( $header == "" ) $header = $cfg["contented"]["basis"]."/list.html";
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
