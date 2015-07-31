<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// bloged-list.inc.php v1 emnili
// list funktion der blogs
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2015 Werner Ammon ( wa<at>chaos.de )

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

    86343 Koenigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** ".$script["name"]." ** ]".$debugging["char"];

    if ( $rechte[$cfg["bloglist"]["right"]] == "" || $rechte[$cfg["bloglist"]["right"]] == -1 ) {

        // page basics
        // ***

        // warnung ausgeben
        if ( get_cfg_var('register_globals') == 1 ) $debugging["ausgabe"] .= "Warnung: register_globals in der php.ini steht auf on, evtl werden interne Variablen ueberschrieben!".$debugging["char"];

        // path fuer die schaltflaechen anpassen
        if ( $cfg["bloglist"]["iconpath"] == "" ) $cfg["bloglist"]["iconpath"] = "/images/default/";

        // label bearbeitung aktivieren
        if ( isset($_GET["edit"]) ) {
            $specialvars["editlock"] = 0;
        } else {
            $specialvars["editlock"] = -1;
        }



        $counter = 0;
        if ( $environment["parameter"][1] ) {
            switch($environment["parameter"][2]) {
                // wiederherstellen von artikeln
                case "recover":
                    $sql = "SELECT * FROM site_text WHERE tname ='".eCRC(make_ebene($environment["parameter"][1])).".".$environment["parameter"][3]."' ORDER BY version DESC";
                    $result = $db -> query($sql);
                    $data = $db -> fetch_array($result,1);
                    $sql = "UPDATE site_text SET status = 1 WHERE tname ='".eCRC(make_ebene($environment["parameter"][1])).".".$environment["parameter"][3]."' AND version='".$data["version"]."'";
                    $result = $db -> query($sql);
                    header("Location: ".$pathvars["virtual"]."/admin/bloged/list,".$environment["parameter"][1].".html");
                    break;
                // endgueltiges loeschen 
                case "delete":
                    $trenner = "=";
                    if ( $environment["parameter"][3] == "all" ) {
                        $environment["parameter"][3] = "%";
                        $trenner = " like ";
                    }
                    $sql = "DELETE FROM site_text WHERE tname ".$trenner."'".eCRC(make_ebene($environment["parameter"][1])).".".$environment["parameter"][3]."'";
                    $result = $db -> query($sql);
                    header("Location: ".$pathvars["virtual"]."/admin/bloged/list,".$environment["parameter"][1].".html");
                    break;
                // liste der zum loeschen vorgemerkten artikel
                default:
                    $hidedata["admin"]["beschriftung1"] = "Pfad";
                    $hidedata["admin"]["beschriftung2"] = "sichtbare Eintr&auml;ge";
                    $hidedata["admin"]["beschriftung3"] = "alle Eintr&auml;ge";
                    $hidedata["admin_clear"]["beschriftung4"] = "<a href=\"list,".$environment["parameter"][1].",delete,all.html\">blog leeren</a>";
                    // liste der geloeschten artikel
                    $tag = array_shift($cfg["bloged"]["blogs"][make_ebene($environment["parameter"][1])]["tags"]);
                    $sql = "SELECT max(version) as version,tname from site_text WHERE content REGEXP '^\\\[!\\\]' AND tname like '".eCRC(make_ebene($environment["parameter"][1])).".%' GROUP by tname having max(status) < '1'";
                    $result = $db -> query($sql);
                    $ausgaben["anzahl"] = $db->num_rows($result);
                    while ( $data = $db -> fetch_array($result,1) ) {
                        $counter++;
                        $sql_in = "SELECT * from site_text WHERE tname ='".$data["tname"]."' AND version='".$data["version"]."' AND status = 0";
                        $result_in = $db -> query($sql_in);
                        if ( $db -> num_rows($result_in) == 0 ) continue;
                        $data_in = $db -> fetch_array($result_in,1);
                        $preg = "\[".$tag["tag"]."\](.*)\[\/".$tag["tag"]."\]";
                        $preg1 = "\.([0-9]*)$";
                        $test = preg_replace("|\r\n|","\\r\\n",$data_in["content"]);
                        preg_match("/$preg/U",$test,$regs);
                        if ( $regs[1] == "" ) $regs[1] = "unknown";
                        $dataloop["blogs"][$counter]["name"] = $regs[1];
                        preg_match("/$preg1/",$data_in["tname"],$regs);
                        $dataloop["blogs"][$counter]["link"] = $pathvars["virtual"].make_ebene($environment["parameter"][1])."/".$regs[1].",v".$data["version"].".html";
                        $dataloop["blogs"][$counter]["anzahl1"] = "<a href=\"list,".$environment["parameter"][1].",recover,".$regs[1].".html\">wiederherstellen</a>";
                        $dataloop["blogs"][$counter]["anzahl2"] = "<a href=\"list,".$environment["parameter"][1].",delete,".$regs[1].".html\">loeschen</a>";
                    }
            }
        } else {
            // uebersicht ueber alle blogs
            $hidedata["admin"]["beschriftung1"] = "Pfad";
            $hidedata["admin"]["beschriftung2"] = "sichtbare Eintr&auml;ge";
            $hidedata["admin"]["beschriftung3"] = "alle Eintr&auml;ge";
            foreach ( $cfg["bloged"]["blogs"] as $key => $value ) {
                $id = make_id($key);
                $counter++;
                $dataloop["blogs"][$counter]["link"] = "list,".$id["mid"].".html";
                $dataloop["blogs"][$counter]["name"] = $key;
                $sql = "SELECT content,tname from site_text WHERE content REGEXP '^\\\[!\\\]'AND status = '1' AND tname like '".eCRC($key).".%'";
                $result = $db -> query($sql);
                $dataloop["blogs"][$counter]["anzahl1"] = $db ->num_rows($result);
                $sql = "SELECT content,tname from site_text WHERE content REGEXP '^\\\[!\\\]' AND tname like '".eCRC($key).".%'";
                $result = $db -> query($sql);
                $dataloop["blogs"][$counter]["anzahl2"] = $db ->num_rows($result);
            }

            $ausgaben["anzahl"] = count($cfg["bloged"]["blogs"]);

        }
        // seiten umschalter
        #$inhalt_selector = inhalt_selector( $sql, $environment["parameter"][1], $cfg["bloged"]["db"]["bloged"]["rows"], $parameter, 1, 4, $getvalues );
        $ausgaben["inhalt_selector"] = "";
        #$sql = $inhalt_selector[1];


        // +++
        // funktions bereich


        // page basics
        // ***

        // fehlermeldungen
        if ( $_GET["error"] != "" ) {
            if ( $_GET["error"] == 1 ) {
                $ausgaben["form_error"] = "#(error1)";
            }
        } else {
            $ausgaben["form_error"] = "";
        }

        // hidden values
        #$ausgaben["form_hidden"] .= "";

        // was anzeigen
        $mapping["main"] = "-2051315182.list";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($_GET["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error1) #(error1)<br />";
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

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
