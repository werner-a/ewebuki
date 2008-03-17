<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "short description";
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

    if ( $rechte[$cfg["bloged"]["right"]] == "" || $rechte[$cfg["bloged"]["right"]] == -1 ) {

        $hidedata["list"]["on"] = "on";

        // funktions bereich
        // ***
        $sql = "SELECT tname, max(version) as version FROM site_text 
                WHERE tname LIKE '".crc32($environment["ebene"]).".%' AND tname REGEXP '[0-9]$' 
                GROUP BY tname ORDER BY tname DESC";
        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];

        // seiten umschalter
        $inhalt_selector = inhalt_selector( $sql, $environment["parameter"][1], $cfg["bloged"]["db"]["bloged"]["rows"], $parameter, 1, 4, $getvalues );
        $ausgaben["inhalt_selector"] = $inhalt_selector[0]."<br />";
        $sql = $inhalt_selector[1];
        $ausgaben["anzahl"] = $inhalt_selector[2];


        $result = $db -> query($sql);
        $counter = 0;
        while ( $data = $db -> fetch_array($result,1) ) {
            $counter++;
            $sql_in = "SELECT content,tname FROM site_text WHERE tname ='".$data["tname"]."' and version ='".$data["version"]."'";
            $result_in = $db -> query($sql_in);
            $data_in  = $db -> fetch_array($result_in,1);
            $preg = "^\[!\]([0-9]{4})-([0-9]{2})-([0-9]{2})\40([0-9]{1,2}):([0-9]{2}):([0-9]{2})\[\/!\][\r\n|\40]*\[H1\](.*)\[\/H1\]";

            if ( preg_match("/$preg/",$data_in["content"],$regs) ) {
                $dataloop["list"][$counter]["date_mk"] = mktime($regs[4],$regs[5],$regs[6],$regs[3],$regs[2],$regs[1]);
                $dataloop["list"][$counter]["date"] = $regs[3].".".$regs[2].".".$regs[1];
                $dataloop["list"][$counter]["teaser"] = $regs[7];
                $dataloop["list"][$counter]["detaillink"] = substr($data_in["tname"],strrpos($data_in["tname"],".")+1).".html"; 
                $dataloop["list"][$counter]["editlink"] = $pathvars["virtual"]."/admin/contented/edit,".DATABASE.",".$data_in["tname"].",inhalt.html";
                $dataloop["list"][$counter]["edittitel"] = "#(edittitel)";
                $dataloop["list"][$counter]["deletelink"] = $pathvars["virtual"].$environment["ebene"]."/delete,".$data_in["tname"].".html";
                $dataloop["list"][$counter]["deletetitel"] = "#(deletetitel)";
            }
        }
        if ( is_array($dataloop["list"]) ) {
            sort($dataloop["list"]);
            $dataloop["list"] = array_reverse($dataloop["list"]);
        }

        for ( $i=0; $i <= count($dataloop["list"])-1;$i++) {
            if (is_int($i/2) ) {
                $color = $cfg["bloged"]["color"]["a"];
            } else {
                $color = $cfg["bloged"]["color"]["b"];
            }
            $dataloop["list"][$i]["color"] = $color;
        }

        // +++
        // funktions bereich


        // page basics
        // ***

        // fehlermeldungen
        if ( $HTTP_GET_VARS["error"] != "" ) {
            if ( $HTTP_GET_VARS["error"] == 1 ) {
                $ausgaben["form_error"] = "#(error1)";
            }
        } else {
            $ausgaben["form_error"] = "";
        }

        // navigation erstellen
        $ausgaben["link_new"] = "add.html";

        // hidden values
        #$ausgaben["form_hidden"] .= "";

        // was anzeigen
#        $mapping["main"] = crc32($environment["ebene"]).".list";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error1) #(error1)<br />";
            $ausgaben["inaccessible"] .= "# (edittitel) #(edittitel)<br />";
            $ausgaben["inaccessible"] .= "# (deletetitel) #(deletetitel)<br />";
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
