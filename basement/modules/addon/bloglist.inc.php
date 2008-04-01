<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id: bloglist.inc.php $";
  $Script["desc"] = "short description";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001, 2002, 2003 Werner Ammon <wa@chaos.de>

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
#echo make_id("/buffy");
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** ".$script["name"]." ** ]".$debugging["char"];

    if ( $rechte[$cfg["bloglist"]["right"]] == "" || $rechte[$cfg["bloglist"]["right"]] == -1 ) {

        // page basics
        // ***

        // warnung ausgeben
        if ( get_cfg_var('register_globals') == 1 ) $debugging["ausgabe"] .= "Warnung: register_globals in der php.ini steht auf on, evtl werden interne Variablen ueberschrieben!".$debugging["char"];

        // path fuer die schaltflaechen anpassen
        if ( $cfg["bloglist"]["iconpath"] == "" ) $cfg["bloglist"]["iconpath"] = "/images/default/";

        // label bearbeitung aktivieren
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $specialvars["editlock"] = 0;
        } else {
            $specialvars["editlock"] = -1;
        }

        $leer[] = "";
        $test = split("/",$environment["ebene"]."/".$environment["kategorie"]);
        $cleaned_up = array_diff($test, $leer);

        $data["mid"] = 0;
        foreach ( $cleaned_up as $value ) {
            $sql = "SELECT *
                      FROM site_menu
                     WHERE entry = '".$value."'
                       AND refid = ".$data["mid"];
            if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            $result = $db -> query($sql);
            if ( $db -> num_rows($result) == 1 ) {
                $data = $db -> fetch_array($result,1);
            } else {
                break;
            }
        }
        $new = $data["mid"];

        // +++
        // page basics

        if ( $environment["ebene"] == "" ) {
            $kat = "/".$environment["kategorie"];
        } else {
            $kat = $environment["ebene"]."/".$environment["kategorie"];
        }
        $crc = crc32($kat);

        // funktions bereich
        // ***

        // kurzer test ohne inhalt-selector , bringt zeitlich ein bisschen was
#        $sql = "SELECT Cast(SUBSTR(content,6,19) as TIMESTAMP) AS date,content,tname from site_text WHERE content REGEXP '^\\\[!\\\]1;' AND tname like '".$crc.".%' order by date DESC";
#        $result = $db -> query($sql);
#        echo $db -> num_rows($result);

        $sql = "SELECT Cast(SUBSTR(content,6,19) as TIMESTAMP) AS date,content,tname from site_text WHERE content REGEXP '^\\\[!\\\]1;' AND tname like '".$crc.".%' order by date DESC";

        if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];

        // seiten umschalter
        $inhalt_selector = inhalt_selector( $sql, $environment["parameter"][1], $cfg["bloged"]["db"]["bloged"]["rows"], $parameter, 1, 4, $getvalues );
        $ausgaben["inhalt_selector"] = $inhalt_selector[0]."<br />";
        $sql = $inhalt_selector[1];
        $ausgaben["anzahl"] = $inhalt_selector[2];
        $counter = 0;

        $result = $db -> query($sql);
        $preg1 = "\.([0-9]*)$";
        while ( $data = $db -> fetch_array($result,1) ) {
            $counter++;
            $test = preg_replace("|\r\n|","\\r\\n",$data["content"]);
            foreach ( $cfg["bloged"]["blogs"][$kat]["tags"] as $key => $value ) {
                (strpos($value,"=")) ? $endtag= substr($value,0,strpos($value,"=")): $endtag=$value;
                if ( $endtag == "IMG" ) {
                    $preg = "\[IMG=\/file\/(png|jpg|gif)\/([0-9]*)\/(.*)\[\/".$endtag."\]";
                } else {
                    $preg = "\[".$value."\](.*)\[\/".$endtag."\]";
                }
                if ( preg_match("/$preg/U",$test,$regs) ) {
                    if ( $endtag == "IMG" ) {
                        $$key = $regs[2].".".$regs[1];
                    } else {
                        $$key = str_replace('\r\n',"<br>",$regs[1]);
                    }
                } else {
                    $$key = "unknown";
                }
                $dataloop["list"][$counter][$key] = $$key;
            }

            preg_match("/$preg1/",$data["tname"],$regs);  

            $dataloop["list"][$counter]["datum"] = substr($data["date"],8,2).".".substr($data["date"],5,2).".".substr($data["date"],0,4);
            $dataloop["list"][$counter]["detaillink"] = $pathvars["virtual"].$kat."/".$regs[1].".html";
            $dataloop["list"][$counter]["deletelink"] = $cfg["bloged"]["basis"]."/delete,".$data["tname"].",".$new.".html";
            $dataloop["list"][$counter]["editlink"] = $pathvars["virtual"]."/admin/contented/edit,".DATABASE.",".$data["tname"].",inhalt.html";

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
        $ausgaben["link_new"] = $pathvars["virtual"]."/admin/bloged/add,".$new.".html";

        // hidden values
        #$ausgaben["form_hidden"] .= "";

        // was anzeigen
        if ($cfg["bloged"]["blogs"][$kat]["own_list_template"] == -1 ) {
            if ( crc32($environment["ebene"]) == 0 ) {
                $mapping["main"] = $environment["kategorie"];
            } else {
                $mapping["main"] = crc32($environment["ebene"]).".".$environment["kategorie"];
            }
        } else {
            $mapping["main"] = "-2051315182.list";
        }
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
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
