<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: contented-edit.inc.php 1242 2008-02-08 16:16:50Z chaot $";
// "contented - edit funktion";
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

    86343 K�nigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // was anzeigen
    $mapping["main"] = "wizard-edit";
    $hidedata["tab"]["num"] = $tag_marken[1] + 1;
    $ausgaben["max_tab_num"] = $cfg["wizard"]["tab_edit"]["max_cells"];

    // parameter bestimmen
    $opentag = str_replace(array("[","]"),"",$tag_meat[$tag_marken[0]][$tag_marken[1]]["tag_start"]);
    $tag_werte = explode(";",trim(strstr($opentag,"="),"="));
    for ($i=0;$i<5;$i++){
        if ( is_array($_POST["tagwerte"]) ) {
            $ausgaben["tagwerte".$i] = $_POST["tagwerte"][$i];
        } elseif ( $tag_werte[$i] != "" ) {
            $ausgaben["tagwerte".$i] = $tag_werte[$i];
        } else {
            $ausgaben["tagwerte".$i] = "";
        }
    }

    // ausgaben nullen
    $ausgaben["tabelle"] = "";
    $ausgaben["num_row"] = 0;
    $ausgaben["num_col"] = 0;

    // infos aus tag holen
    // * * *
    // tag nach zeilen aufsplitten
    preg_match_all("/\[ROW\](.*)\[\/ROW\]/Us",$tag_meat[$tag_marken[0]][$tag_marken[1]]["meat"],$rows);
    $tab_rows_tag = array();
    $row_index = 0; $ausgaben["num_col_tag"] = 0;
    foreach ( $rows[1] as $row_value ) {
        // tag nach zellen aufsplitten
        preg_match_all("/\[COL.*\](.*)\[\/COL\]/Us",$row_value,$cells);
        $col_index = 0; $row_buffer = array();
        foreach ( $cells[1] as $cell_value ) {
            $row_buffer[] = "<td>\n".
                                "<textarea name=\"cells[".$row_index."][".$col_index."]\" onclick=\"ebCanvas=this\">".$cell_value."</textarea>\n".
                            "</td>\n";
            $col_index++;
        }
        if ( $col_index >= $ausgaben["num_col_tag"] = 0 ) $ausgaben["num_col_tag"] = $col_index;
        if ( count($row_buffer) > 0 ) $tab_rows_tag[] = implode("",$row_buffer);
        $row_index++;
    }
    $ausgaben["num_row_tag"] = $row_index;
    // + + +

    if ( $_FILES["csv_upload"]["tmp_name"] ) {

        $handle = fopen ($_FILES["csv_upload"]["tmp_name"],"r");
        $row_index = 0; $ausgaben["num_col"] = 0;
        while ( ($csv_data = fgetcsv ($handle, 4096, ";")) !== FALSE ) {
            $col_index = 0; $row_buffer = array();
            foreach ( $csv_data as $cell_value ) {
                $row_buffer[] = "<td>\n".
                                    "<textarea name=\"cells[".$row_index."][".$col_index."]\" onclick=\"ebCanvas=this\">".$cell_value."</textarea>\n".
                                "</td>\n";
                $col_index++;
            }
            if ( $col_index >= $ausgaben["num_col"] = 0 ) $ausgaben["num_col"] = $col_index;
            if ( count($row_buffer) > 0 ) $tab_rows[] = implode("",$row_buffer);
            $row_index++;
        }
        $ausgaben["num_row"] = $row_index;

    } elseif ( count($_POST) > 0 ) {

        $ausgaben["num_row"] = $_POST["num_row"];
        $ausgaben["num_col"] = $_POST["num_col"];
        // zeilen durchgehen
        for ( $row_index = 0 ; $row_index < $_POST["num_row"] ; $row_index++ ) {
            // zellen durchgehen
            $row_buffer = array();
            for ( $col_index = 0 ; $col_index < $_POST["num_col"] ; $col_index++ ) {
                $row_buffer[] = "<td>\n".
                                    "<textarea name=\"cells[".$row_index."][".$col_index."]\" onclick=\"ebCanvas=this\">".$_POST["cells"][$row_index][$col_index]."</textarea>\n".
                                "</td>\n";
            }
            if ( count($row_buffer) > 0 ) $tab_rows[] = implode("",$row_buffer);
        }
        $ausgaben["num_row"] = $row_index;

    } else {

        $tab_rows = $tab_rows_tag;
        $ausgaben["num_row"] = $ausgaben["num_row_tag"];
        $ausgaben["num_col"] = $ausgaben["num_col_tag"];

    }

    // gesamt-anzahl der felder ueberpruefen
    $count_cells = ((int) $ausgaben["num_row"])*((int) $ausgaben["num_col"]);
    $count_cells = $ausgaben["num_row"]*$ausgaben["num_col"];
    if ( $count_cells > $cfg["wizard"]["tab_edit"]["max_cells"] ) {
        $hidedata["form_error"] = array();
        $ausgaben["form_error"] .= "#(error_tab_cells)".$cfg["wizard"]["tab_edit"]["max_cells"];
        $tab_rows = $tab_rows_tag;
        $ausgaben["num_row"] = $ausgaben["num_row_tag"];
        $ausgaben["num_col"] = $ausgaben["num_col_tag"];
    }

    // tabelle zusammenbauen
    if ( count($tab_rows) > 0 ) {
        $ausgaben["tabelle"]  = "<table width=\"100%\">\n";
        $ausgaben["tabelle"] .= "<tr>\n".implode("</tr>\n<tr>\n",$tab_rows)."</tr>\n";
        $ausgaben["tabelle"] .= "</table>";
    }

    // abspeichern, part 2
    // * * *
    if ( $environment["parameter"][7] == "verify"
        &&  ( $_POST["send"] != ""
            || $_POST["add"] != ""
            || $_POST["sel"] != ""
            || $_POST["refresh"] != ""
            || $_POST["upload"] != "" ) ) {

            $tab = "[TAB=".implode(";",$_POST["tagwerte"])."]\n";
            $num_cell = 0;
            for ($i=0;$i<$_POST["num_row"];$i++) {
                $tab .= "[ROW]\n";
                for ($k=0;$k<$_POST["num_col"];$k++) {
                    $tab .= "[COL]".trim($_POST["cells"][$i][$k])."[/COL]\n";
                    $num_cell++;
                }
                $tab .= "[/ROW]\n";
            }
            $tab .= "[/TAB]";
            $to_insert = $tab;

    }
    // + + +

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>