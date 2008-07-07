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

    86343 Königsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // was anzeigen
    $mapping["main"] = "wizard-edit";
    $hidedata["tab"] = array();

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

    // daten auflisten
    preg_match_all("/\[ROW\](.*)\[\/ROW\]/Us",$tag_meat[$tag_marken[0]][$tag_marken[1]]["meat"],$rows);
    $ausgaben["tabelle"] = "<table width=\"100%\">\n";
    $row_index = 0; $ausgaben["num_row"] = 0; $ausgaben["num_col"] = 0;
    foreach ( $rows[1] as $row ) {
        $ausgaben["tabelle"] .= "<tr>";
        preg_match_all("/\[COL\](.*)\[\/COL\]/Us",$row,$cells);
        $col_index = 0; $ausgaben["num_col"] = 0;
        foreach ( $cells[1] as $cell ) {
            $ausgaben["tabelle"] .= "<td>".
//                                     "<input type=\"text\" value=\"".$cell."\" name=\"cells[".$row_index."][".$col_index."]\" />".
                                    "<textarea name=\"cells[".$row_index."][".$col_index."]\">".$cell."</textarea>".
                                    "</td>";
            $col_index++; $ausgaben["num_col"]++;
        }
        $ausgaben["tabelle"] .= "</tr>";
        $row_index++; $ausgaben["num_row"]++;
    }
    $ausgaben["tabelle"] .= "</table>";


    // abspeichern, part 2
    // * * *
    if ( $environment["parameter"][7] == "verify"
        &&  ( $_POST["send"] != ""
            || $_POST["add"] != ""
            || $_POST["sel"] != ""
            || $_POST["refresh"] != ""
            || $_POST["upload"] != "" ) ) {


        // einzubauender content
        if ( $_FILES["csv_upload"]["type"] == "text/csv" ) {
            $handle = fopen ($_FILES["csv_upload"]["tmp_name"],"r");
            $tab = "[TAB=".implode(";",$_POST["tagwerte"])."]\n";
            while ( ($csv_data = fgetcsv ($handle, 1000, ";")) !== FALSE ) {
                $tab .= "[ROW]\n";
                foreach ( $csv_data as $cell ) {
                    $tab .= "[COL]".trim($cell)."[/COL]\n";
                }
                $tab .= "[/ROW]\n";
            }
            $tab .= "[/TAB]";
            fclose ($handle);
            $to_insert = $tab;
        } else {
            $tab = "[TAB=".implode(";",$_POST["tagwerte"])."]\n";
            for ($i=0;$i<$_POST["num_row"];$i++) {
                $tab .= "[ROW]\n";
                for ($k=0;$k<$_POST["num_col"];$k++) {
                    $tab .= "[COL]".trim($_POST["cells"][$i][$k])."[/COL]\n";
                }
                $tab .= "[/ROW]\n";
            }
            $tab .= "[/TAB]";
            $to_insert = $tab;
        }

    }
    // + + +

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>