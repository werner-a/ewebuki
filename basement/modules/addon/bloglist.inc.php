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
    include $pathvars["moduleroot"]."admin/bloged.cfg.php";
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** ".$script["name"]." ** ]".$debugging["char"];

    #if ( $rechte[$cfg["bloglist"]["right"]] == "" || $rechte[$cfg["bloglist"]["right"]] == -1 ) {

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

    // erstellen der crc
    if ( $environment["ebene"] == "" ) {
        $kat = "/".$environment["kategorie"];
    } else {
        $kat = $environment["ebene"]."/".$environment["kategorie"];
    }

    if ( array_key_exists($kat,$cfg["bloged"]["blogs"]) ) {
        $crc = crc32($kat);
    } else {
        $crc = crc32($cfg["bloged"]["exclusion"][$kat]);
        $kat = $cfg["bloged"]["exclusion"][$kat];
    }

    // herausfinden der id,noetig fuer neueintrag
    include $pathvars["moduleroot"]."libraries/function_menu_convert.inc.php";
    $id = make_id($kat);
    $new = $id["mid"];

    // erster test einer suchanfrage per kalender
    //
    $where = "";
    if ( $_GET["year"] || $_GET["month"] || $_GET["day"] ) {
        $heute = getdate(mktime(0, 0, 0, ($_GET["month"])+1, 0, $_GET["year"]));
        if ( !$_GET["day"] ) {  
            $day1 = $heute["mday"];
            $day2 = "1";
        } else {
            $day1 = $_GET["day"];
            $day2 = $_GET["day"];
        }
        $where = "AND ( Cast(SUBSTR(content,6,19) as DATETIME) < '".$_GET["year"]."-".$_GET["month"]."-".$day1." 23:59:59' AND Cast(SUBSTR(content,6,19) as DATETIME) > '".$_GET["year"]."-".$_GET["month"]."-".$day2." 00:00:00'    )";
    }
    //
    // erster test einer suchanfrage per kalender

    $sql = "SELECT Cast(SUBSTR(content,6,19) as DATETIME) AS date,content,tname from site_text WHERE content REGEXP '^\\\[!\\\]1;' ".$where." AND tname like '".$crc.".%' order by date DESC";

    if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];

    // seiten umschalter
    $inhalt_selector = inhalt_selector( $sql, $environment["parameter"][1], $cfg["bloged"]["db"]["bloged"]["rows"], $parameter, 1, 4, $getvalues );
    $ausgaben["inhalt_selector"] = $inhalt_selector[0]."<br />";
    $sql = $inhalt_selector[1];
    $ausgaben["anzahl"] = $inhalt_selector[2];
    $counter = 0;
    $result = $db -> query($sql);
    $preg1 = "\.([0-9]*)$";

    // evtl wizard einbinden
    if ( $cfg["bloged"]["blogs"][$kat]["wizard"] == -1 ) {
        $editlink = "/wizard/show,";
    } else {
        $editlink = "/admin/contented/edit,";
    }

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
        if ( $cfg["bloged"]["blogs"][$kat]["right"] == "" || 
        ( priv_check($kat,$cfg["bloged"]["blogs"][$kat]["right"]) || ( function_exists(priv_check_old) && priv_check_old("",$cfg["bloged"]["blogs"][$kat]["right"]) ) )
        ) {
            $dataloop["list"][$counter]["deletelink"] = "<a href=\"".$cfg["bloged"]["basis"]."/delete,".$new.",".$regs[1].".html\">delete</a>";
            $dataloop["list"][$counter]["editlink"] = "<a href=\"".$pathvars["virtual"].$editlink.DATABASE.",".$data["tname"].",inhalt.html\">edit</a>";
        }
    }

    // fehlermeldungen
    if ( $HTTP_GET_VARS["error"] != "" ) {
        if ( $HTTP_GET_VARS["error"] == 1 ) {
            $ausgaben["form_error"] = "#(error1)";
        }
    } else {
        $ausgaben["form_error"] = "";
    }

    if ( $cfg["bloged"]["blogs"][$kat]["right"] == "" || 
     ( priv_check($kat,$cfg["bloged"]["blogs"][$kat]["right"]) || ( function_exists(priv_check_old) && priv_check_old("",$cfg["bloged"]["blogs"][$kat]["right"]) ) )
     ) {
        // navigation erstellen
        $hidedata["new"]["link"] = $pathvars["virtual"]."/admin/bloged/add,".$new.".html";
    }

    // hidden values
    #$ausgaben["form_hidden"] .= "";

    // was anzeigen
    if ($cfg["bloged"]["blogs"][$kat]["own_list_template"] != "" ) {
        $template = $cfg["bloged"]["blogs"][$kat]["own_list_template"];
    } else {
        $template = "list";
    }
    $mapping["main"] = "-2051315182.".$template;
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

    #} else {
    #    header("Location: ".$pathvars["virtual"]."/");
    #}

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
