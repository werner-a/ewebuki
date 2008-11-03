<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
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
        $position = $environment["parameter"][1]+0;

    if ( priv_check("/".$cfg["contented"]["subdir"]."/".$cfg["contented"]["name"],$cfg["contented"]["right"]) ||
         priv_check_old("",$cfg["contented"]["right"]) ) {

        // ueberschrift
        $ausgaben["url"] = $pfad;

        // als tname werden die SESSIONS "ebene" u. "kategorie" verwendet
        if ( $environment["parameter"][2] == "" ) {
            if ( $_SESSION["ebene"] != "" ) {
                $pfad = $_SESSION["ebene"]."/".$_SESSION["kategorie"];
                $tname = eCRC($_SESSION["ebene"]).".".$_SESSION["kategorie"];
            } else {
                $pfad = "/".$_SESSION["kategorie"];
                $tname = $_SESSION["kategorie"];
            }
        } else {
            $tname = $environment["parameter"][2];
            $pfad = tname2path($tname);
        }

        // label steuerung wenn kein para dann wird default-label aus cfg hergenommen
        if ( $environment["parameter"][3] == "" ) {
            $label = $cfg["contented"]["default_label"];
        } else {
            $label = $environment["parameter"][3];
        }

        $old = $environment["parameter"][4];
        $new = $environment["parameter"][5];
        if ( $_POST["old"] != "" || $_POST["new"]!= ""
          || $_GET["old"] != ""  || $_GET["new"]!= "" ) {
            if ( $_POST["old"] || $_GET["old"] ) {
                $_POST["old"] != "" ? $old = $_POST["old"] : $old = $_GET["old"];
            }
            if ( $_POST["new"] || $_GET["new"] ) {
                $_POST["new"] != "" ? $new = $_POST["new"] : $new = $_GET["new"];
            }
            header("Location: ".$cfg["contented"]["basis"]."/history,".$environment["parameter"][1].",".$tname.",".$label.",".$old.",".$new.",".$environment["parameter"][6].",".$environment["parameter"][7].",verify.html");
        }

        // page basics
        // ***
        $ausgaben["diff"] = "";
        $ausgaben["rows"] = $cfg["contented"]["history_rows"];

        // hoechste und niedrigste versionsnummer rausfinden
        $sql = "SELECT max(version), min(version)
                  FROM site_text
                 WHERE tname='".$tname."'
                   AND label='".$label."'";
        $result = $db -> query($sql);
        $data = $db -> fetch_array($result,1);
        $last_version = $data["max"];
        $first_version = $data["min"];

        $sql = "SELECT *
                  FROM site_text
                 WHERE tname='".$tname."'
                   AND label='".$label."'
              ORDER BY version DESC";
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
        $result = $db -> query($sql);

        // Inhalt Selector erstellen und SQL modifizieren
        $parameter = ",".$tname.",".$label.",".$old.",".$new.",".$environment["parameter"][6].",".$environment["parameter"][7];
        $inhalt_selector = inhalt_selector( $sql, $position, $cfg["contented"]["history_rows"], $parameter );
        $ausgaben["inhalt_selector"] .= $inhalt_selector[0];
        $sql = $inhalt_selector[1];
        $ausgaben["gesamt"] = $inhalt_selector[2];

        $result = $db -> query($sql);
        $counter = "";
        while ( $form_values = $db -> fetch_array($result,1) ) {

            $counter++;
            $selected_new = "";
            if ( ($new == "" && $counter == 1)
               || $new == $form_values["version"] ) {
                $selected_new = " checked=\"checked\"";
                $ausgaben["new_sel_id"] = $form_values["version"];
            }
            $selected_old = "";
            if ( ($old == "" && $counter == 2)
               || $old == $form_values["version"] ) {
                $selected_old = " checked=\"checked\"";
                $ausgaben["old_sel_id"] = $form_values["version"];
            }

            $dataloop["list"][$form_values["version"]] = array(
                             "url" => $pathvars["virtual"].$pfad.",v".$form_values["version"].".html",
                            "date" => substr($form_values["changed"],8,2).". ".gerdate("gml",substr($form_values["changed"],5,2))." ".substr($form_values["changed"],0,4)." ".substr($form_values["changed"],11,5),
                            "name" => $form_values["bysurname"]." ".$form_values["byforename"],
                             "cb1" => $form_values["version"],
                             "cb2" => $form_values["version"],
                     "visible_old" => "visible",
                     "visible_new" => "visible",
                    "selected_old" => $selected_old,
                    "selected_new" => $selected_new,
                          "max_id" => $last_version,
                            "rows" => $cfg["contented"]["history_rows"],
                         "current" => "?old=".$form_values["version"]."&new=".$last_version,
                        "previous" => "?new=".$form_values["version"]."&old=".($form_values["version"]-1),
            );


        }

        // links und radiobuttons ggf ausblenden
        if ( is_array($dataloop["list"][$first_version]) ) {
            $dataloop["list"][$first_version]["visible_previous"] = "hidden";
            $dataloop["list"][$first_version]["visible_new"] = "hidden";
        }
        if ( is_array($dataloop["list"][$last_version]) ) {
            $dataloop["list"][$last_version]["visible_current"] = "hidden";
            $dataloop["list"][$last_version]["visible_old"] = "hidden";
        }

        // hier erfolgt der diff
        if ( $old != "" && $new != "" ) {

            $sql = "SELECT content,version
                      FROM site_text
                     WHERE tname='".$tname."'
                       AND label='".$label."'
                       AND version=".$old;
            $result = $db -> query($sql);
            $data_old = $db -> fetch_array($result,1);
            $sql = "SELECT content,version
                      FROM site_text
                     WHERE tname='".$tname."'
                       AND label='".$label."'
                       AND version=".$new;
            $result = $db -> query($sql);
            $data_new = $db -> fetch_array($result,1);

            // tagreplace?
            if ( $environment["parameter"][6] == "html" ) {
                if ( $data_new["version"] > $data_old["version"] ) {
                    $first = tagreplace($data_new["content"]);
                    $second = tagreplace($data_old["content"]);
                } else {
                    $first = tagreplace($data_old["content"]);
                    $second = tagreplace($data_new["content"]);
                }
            } else {
                if ( $data_new["version"] > $data_old["version"] ) {
                    $first = $data_new["content"];
                    $second = $data_old["content"];
                } else {
                    $first = $data_old["content"];
                    $second = $data_new["content"];
                }
                $old_array = explode("\n", $second);
                $new_array = explode("\n", $first);
            }
            $old_array = explode("\n", $second);
            $new_array = explode("\n", $first);

            // diff-methode festlegen
            if ( $environment["parameter"][7] != "" ) {
                $diff_type = $environment["parameter"][7];
            } else {
                $diff_type = $cfg["contented"]["diff_engine"];
            }
            if ( $diff_type == "phpdiff3" ) {
                $diff = phpdiff3($second,$first);
            } elseif ( strstr($diff_type,"phpdiff") ) {
                $diff = arr_diff($old_array,$new_array);
            } else {
                $diff = new Text_Diff('auto', array($old_array, $new_array));
                $renderer = new Text_Diff_Renderer_inline();
                $diff = str_replace("\n","<br>",$renderer->render($diff));
            }
            $ausgaben["diff"] = $diff;

        }

        // form options holen
        $form_options = form_options(eCRC($environment["ebene"]).".".$environment["kategorie"]);

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
        $ausgaben["form_aktion"] = $cfg["contented"]["basis"]."/history,".$environment["parameter"][1].",".$tname.",".$label.",".$old.",".$new.",".$environment["parameter"][6].",".$environment["parameter"][7].",verify.html";
        $ausgaben["form_break"] = $cfg["contented"]["basis"]."/list.html";

        // hidden values
        $ausgaben["form_hidden"] .= "";

        // was anzeigen
        #$mapping["main"] = eCRC($environment["ebene"]).".modify";
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

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>