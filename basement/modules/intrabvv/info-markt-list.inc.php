<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  "$Id$";
//  "info-markt-list";
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

    global $HTTP_SESSION_VARS;

    if ( $environment["fqdn"][0] == "www" ) {
        $database = "intrabvv";
    } else {
        $database = $environment["fqdn"][0];
    }
    #    if ( is_array($HTTP_SESSION_VARS["dbzugriff"]) ) {
#        if ( in_array($database,$HTTP_SESSION_VARS["dbzugriff"]) ) $dbzugriff = -1;
#    }

    #echo $environment[ebene]."/".$environment[kategorie]."<br>";
    $dbtname = crc32($environment[ebene]).".".$environment[kategorie];
    $pfad = explode("/",$environment["ebene"]);
    $anzahl = count($pfad);
    #echo $anzahl;

    if ( is_array($HTTP_SESSION_VARS["katzugriff"]) ) {


        if ( in_array("0:".$database.":".$dbtname,$HTTP_SESSION_VARS["katzugriff"]) ) $katzugriff = -1;
    }

    #echo $pfad[1];
    echo "<pre>";
    print_r($HTTP_SESSION_VARS["katzugriff"]);
    echo "</pre>";

    #echo $dbtname."<br>";
    #echo "0:".$database.":".$dbtname;

    if ( $cfg["ebene"]["drei"] ) {
        $kat = "/".$cfg["ebene"]["drei"];
    } else {
        $kat = "";
    }

    if ( $rechte[$cfg["right"]["red"]] == -1
#      || $rechte["administration"] == -1 && $dbzugriff == -1
      || $katzugriff == -1 ) {
        $ausgaben["neu"] = "<a href=\"".$cfg["basis"]."/".$cfg["ebene"]["zwei"].$kat."/modify,add.html\">|Neuer Beitrag| </a>";
    } else {
        $ausgaben["neu"] = "";
    }


/*
    if ( $rechte[$cfg["right"]["red"]] == -1 ) {
        $ausgaben["neu"] = "<a href=\"".$cfg["basis"]."/".$cfg["ebene"]["zwei"]."/modify,add.html\">|Neuer Beitrag| </a>";
    } else {
        $ausgaben["neu"] = "";
    }
*/

    $position = $environment["parameter"][1]+0;



    // nur jeweiligen global / local inhalt anzeigen
    $where = " WHERE ifqdn0 = '".$environment["fqdn"][0]."'";


    // nur aktuellen bereich anzeigen
    if ($cfg["ebene"]["zwei"] == "") {
        $ausgaben["neu"] = "";
        $where .= " AND iuebersicht = '-1'";
    } else {
        $where .= " AND ibereich = '".$cfg["ebene"]["zwei"]."'";
    }

    // kategorie durch ebene drei steuerbar
    if ( $cfg["ebene"]["drei"] != "" ) {
        #$ausgaben["neu"] = "";
        $where .= " AND ikategorie = '".$cfg["ebene"]["drei"]."'";
    }

    // archivierte artikel verbergen
    $where = $where." AND iarchiv != '-1'";

    // nur redakteurs volk sieht alle artikel
    if ( $rechte["chefredaktion"] != -1 && $rechte["chefredaktion"] != -1 ) {
        $where = $where." AND iaktiv = '-1'";
    }

    // links fuer den bereich erstellen
    switch ( $cfg["ebene"]["zwei"] ) {
        case "bezirksfinanzdirektionen":
            #$ausgaben["links"]  = "<br>";
            #$ausgaben["links"] .= "<a href=\"".$cfg["basis"]."/".$cfg["ebene"]["zwei"]."/ansbach.html\">ansbach</a><br>";
            #$ausgaben["links"] .= "<a href=\"".$cfg["basis"]."/".$cfg["ebene"]["zwei"]."/augsburg.html\">augsburg</a><br>";
            #$ausgaben["links"] .= "<br>";
            $ausgaben["links"] = "";
            break;
        case "fachkreise":
            #$ausgaben["links"]  = "<br>";
            $ausgaben["links"] .= "<a href=\"".$pathvars["virtual"]."/protokoll.html\">|Ergebnisprotokolle|</a><br>";
            #$ausgaben["links"] .= "<br>";
            break;
        case "projekte":
            #$ausgaben["links"]  = "<br>";
            $ausgaben["links"] .= "<a href=\"".$pathvars["virtual"]."/projektuebersicht.html\">|Projektübersicht|</a><br>";
            #$ausgaben["links"] .= "<br>";
            break;
        default:
            $ausgaben["links"] = "";
    }


    /*
    // Suche
    $ausgaben["form_aktion"] = $cfg["basis"]."/list,".$position.",search.html";
    $ausgaben["search"] = $HTTP_POST_VARS["search"];
    if ( $HTTP_POST_VARS["search"] != "" ) {
        $ausgaben["result"] = "Ihre Schnellsuche nach \"".$HTTP_POST_VARS["search"]."\" hat folgende Einträge gefunden:<br><br>";
    } else {
        $ausgaben["result"] = "";
    }

    if ( $environment["parameter"][2] == "search" ) {
        if ( $HTTP_POST_VARS["search"] != "" ) {
            $search_value = $HTTP_POST_VARS["search"];
        } else {
            $search_value = $environment["parameter"][3];
        }
        $parameter = ",search,".$search_value;
        $where = " WHERE (abnamra LIKE '%".$search_value."%' OR abnamvor LIKE '%".$search_value."%')";
    }
    */

    $form_values = $HTTP_POST_VARS;

    // form options holen

    #$form_options = form_options(crc32($environment[ebene]).".".$environment[kategorie]);
    $form_options = form_options("1943315524.list");

    // form elememte bauen
    $element = form_elements( $cfg["db"]["entries"], $HTTP_POST_VARS );


    // form elememt itext umbauen
    $form_fields = array("itext"
                         );
    foreach ($form_fields as $key){
        ( $form_options[$key]["fsize"] > 0 ) ? $size = " size=\"".$form_options[$key]["fsize"]."\"" : $size = "";
        ( $form_options[$key]["fclass"] != "" ) ? $class = " class=\"".$form_options[$key]["fclass"]."\"" : $class = " class=\"".$form_defaults["class"]["textfield"]."\"";
        ( $form_options[$key]["fstyle"] != "" ) ? $style = " style=\"".$form_options[$key]["fstyle"]."\"" : $style = "";
        ( $form_values[$key] != "" ) ? $value = " value=\"".$form_values[$key]."\"" : $value = "";

        $element[$key] = "<input type=\"text\"".$size.$class.$style." name=\"".$key."\" ".$value.">\n";
    }

    //datumsfeld von und bis leeren (wach 2508)
    $pos=strrpos($element["ivon"], "value");
    $element["ivon"] = substr($element["ivon"], 0, $pos-1).">";

    $pos=strrpos($element["ibis"], "value");
    $element["ibis"] = substr($element["ibis"], 0, $pos-1).">";


    #$ausgaben["form_aktion"] = $cfg["basis"]."/".$cfg["ebene"]["zwei"]."/list,".$position.",esearch.html";
    $ausgaben["form_aktion"] = $cfg["basis"]."/".$cfg["ebene"]["zwei"].",0,esearch.html";


    //  Suche (wa 2208)
    // ***
    #if ( $environment[parameter][2] == "esearch" ) {
    if ( $environment[parameter][2] == "esearch" || $HTTP_GET_VARS["esearch"] == true) {

            if ( $HTTP_GET_VARS["esearch"] == true ) {
                $kick = array( "image_x", "image_y", "esearch");
            } else {
                $kick = array( "image_x", "image_y");
            }
            foreach ($HTTP_GET_VARS as $key => $value) {
                if ( !in_array($key,$kick)  ) {
                    if ( $value != "" ) {
                        if ($getvalues != "") $getvalues .= "&";
                        // sql WHERE bauen
                        $where2 .= " AND ";
                        if ($key == "ivon") {
                            $getval = substr($value,0,10);
                            $convert = $value;
                            $value = substr($convert,6,4)."-".substr($convert,3,2)."-".substr($convert,0,2)." 00:00:00";
                            $where2 .= "ierstellt >= '".$value."'";
                        } elseif ($key == "ibis") {
                            $getval = substr($value,0,10);
                            $convert = $value;
                            $value = substr($convert,6,4)."-".substr($convert,3,2)."-".substr($convert,0,2)." 23:59:59";
                            $where2 .= "ierstellt <= '".$value."'";
                        } elseif ($key == "itext") {
                            $where2 .= "(".$key." LIKE '%".$value."%' OR ilead LIKE '%".$value."%' OR ititel LIKE '%".$value."%')";
                            $getval = $value;
                        } else {
                            $where2 .= $key." LIKE '%".$value."%'";
                            $getval = $value;
                        }

                        $getvalues .= $key."=".$getval;
                        // suchergebnis ausgabe bauen
                        #if ( $suchergebnis !="" ) $suchergebnis .= " und ";
                        #$suchergebnis .= "\"".$value."\"";
                    }
                }
            }
            $getvalues .= "&esearch=true";
            if ( $where != "" ) {
                $ausgaben["result"] = "Ihre Erweiterte Suche nach ".$suchergebnis." hat";
                // where zusammenbauen
                $where .= $where2;
            }
    }

    // +++
    // Suche (wa 2208)

    // Sql Query
    $sql = "SELECT * FROM ".$cfg["db"]["entries"].$where." ORDER by ".$cfg["db"]["order"]." DESC";
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "<font color=\"#FF0000\">HIER:".$HTTP_SESSION_VARS["uid"]."</font>".$debugging["char"];
    #echo $sql;
    // Inhalt Selector erstellen und SQL modifizieren
    $inhalt_selector = inhalt_selector( $sql, $position, $cfg["db"]["rows"], $parameter, 1, 10, $getvalues );
    $ausgaben["inhalt_selector"] .= $inhalt_selector[0];
    $sql = $inhalt_selector[1];
    $ausgaben["gesamt"] = $inhalt_selector[2];

    // head spulen
    $ausgaben["output"] .= parser( "1943315524.list-head", "");

    // query absetzen, variablen bauen
    $result = $db -> query($sql);
    $modify  = array (
      "edit"        => array("modify,", "Bearbeiten", $cfg["right"]["red"]),
      "replace"     => array("modify,", "Ersetzen", $cfg["right"]["chf"]),
      #"details"     => array("", "Details", ""),
      #"form"       => array("email,", "E-Mail", ""),
      #"delete"      => array("modify,", "Löschen")
    );
    $imgpath = $pathvars["images"];

    // daten holen, row spulen
    while ( $data = $db -> fetch_array($result,$nop) ) {

        foreach($data as $key => $value) {
            $$key = $value;
        }

        // titel verlinken
        #$ititel = "<a class=\"id_head\" \"href=\"".$cfg["basis"]."/".$cfg["ebene"]["zwei"]."/details,".$data[$cfg["db"]["key"]].".html\">".$ititel."</a>";

        // datum richtig setzen
        $ierstellt = substr($ierstellt,8,2).".".substr($ierstellt,5,2).".".substr($ierstellt,0,4);

        // nicht freigegebene artikel markieren
        if ( $iaktiv != -1 ) $ierstellt = $ierstellt."<br><i>(keine Freigabe)</i>";

        // wenn es keinen lead gibt 300 zeichen text ohne tags nehmen
        $ilead = tagremove($ilead);
        if ( $ilead == "" ) {
            $itext = tagremove($itext);
            $ilead = substr($itext,0,300);
        }

        if ( $ikategorie ) {
            $ikategorie = ",".$ikategorie;
        #}
        }
        if ( $data["ikategorie"] ) {
            $kat = "/".$data["ikategorie"];
        } else {
            $kat = "";
        }

        #echo $data["ikategorie"];
        // mehr link
        $mehr = $cfg["basis"]."/".$data["ibereich"]."/details,".$data[$cfg["db"]["key"]].".html";
        #$mehr = $cfg["basis"]."/".$cfg["ebene"]["zwei"]."/details,".$data[$cfg["db"]["key"]].".html";

        // aktionen erstellen
        $aktion = "";
        foreach($modify as $name => $value) {
            #$aktion .= "<a href=\"".$cfg["basis"]."/".$cfg["ebene"]["zwei"]."/".$value[0].$name.",".$data[$cfg["db"]["key"]].".html\"><img src=\"".$imgpath."/".$name.".png\" border=\"0\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></a>";
            if ( $rechte[$value[2]] == -1 || $value[2] == "" ) {
                #$aktion .= "<a href=\"".$cfg["basis"]."/".$cfg["ebene"]["zwei"]."/".$value[0].$name.",".$data[$cfg["db"]["key"]].".html\"><img src=\"".$imgpath.$name.".png\" border=\"0\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></a>";
                $aktion .= "<a href=\"".$cfg["basis"]."/".$data["ibereich"].$kat."/".$value[0].$name.",".$data[$cfg["db"]["key"]].".html\"><img src=\"".$imgpath.$name.".png\" border=\"0\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></a>";
            #} elseif ( $rechte["cms_admin"] == -1 && $name == "edit" ) {
            #    $aktion .= "<a href=\"".$environment["basis"]."/".$value[0].$name.",".$field[$db_entries_key].".html\"><img src=\"".$imgpath."/".$name."a.gif\" border=\"0\" alt=\"".$value[1]."\" title=\"".$value[1]."\" width=\"24\" height=\"18\"></a>";
            } else {
                $aktion .= "<img src=\"".$imgpath."/pos.png\" alt=\"\" width=\"24\" height=\"18\">";
            }

        }
        $ausgaben["output"] .= parser( "1943315524.list-row", "");
    }

    // foot spulen
    #if ( $rechte[$cfg["right"]["red"]] == -1 ) {
    #    $neu = "<a href=\"".$cfg["basis"]."/".$cfg["ebene"]["zwei"]."/modify,add.html\">Neu</a>";
    #} else {
    #    $neu = "";
    #}

    $ausgaben["output"] .= parser( "1943315524.list-foot", "");

    // was anzeigen
    #$mapping["main"] = crc32($environment["ebene"]).".".$environment["name"];
    #$mapping["main"] = crc32($environment["ebene"]).".list";
    $mapping["main"] = "1943315524.list";
    $mapping["navi"] = "leer";
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "<font color=\"#FF0000\">ATTENTION: template overwrite -> ".$mapping["main"].".tem.html</font>".$debugging["char"];
    #[LINK=../protokoll.html]Protokolle[/LINK]
    $ausgaben["kopf1"] = "Info-Markt";
    $ausgaben["kopf2"] = ucfirst($cfg["ebene"]["zwei"]);

    // wohin schicken ?
    # noch nirgens


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
