<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "kunden-details";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    phpWEBkit - a easy website building kit
    Copyright (C)2001, 2002, 2003 Werner Ammon <wa@chaos.de>

    This script is a part of phpWEBkit

    phpWEBkit is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    phpWEBkit is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with phpWEBkit; If you did not, you may download a copy at:

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


    //
    // Details anzeigen
    //


    $sql = "SELECT * FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][1]."'";
    $result = $db -> query($sql);
    $field = $db -> fetch_array($result,1);

    // Entsprechende Kategorie aus $db_kate holen (mor 0304)
    //***
    $sql= "SELECT * FROM ".$cfg["db"]["entries_kago"]." WHERE katid = ".$field["akkate"];
    $result = $db -> query($sql);
    $field_kate = $db -> fetch_array($result,$nop);
    //+++
    // Entsprechende Kategorie aus $db_kate holen (mor 0304)



    // Ausgeben , Holen der Kategorie aus db und ausgeben (mor 0304)
    // ***
    foreach( $field as $key => $value) {
        if ( $value == "") $value ="--";
        if ($key == "akkate") {
            $ausgaben[$key] = $field_kate["kate"];
        } else {
        $ausgaben[$key] = $value;
        }
    }
    // +++
    // Ausgeben , Holen der Kategorie aus db und ausgeben (mor 0304)

    // ausgabe date bei feldgeschworene
    // ***
    if (in_array($field["akkate"],$feldarray)) {
        $ausgaben["afggebtxt"] = "Geburtsdatum:";
        $ausgaben["akfg"] = "Angaben zu den Feldgeschworenen";
        $ausgaben["afgstarttxt"] = "Startdatum";
        if ($ausgaben["akfggeb"] != "--") {
        #    $ausgaben["akfggeb"] = "--";
        #} else {
            $ausgaben["akfggeb"] = substr($ausgaben["akfggeb"],8,2).".".substr($ausgaben["akfggeb"],5,2).".".substr($ausgaben["akfggeb"],0,4);
        }
        if ($ausgaben["akfgstart"] != "--") {
        #    $ausgaben["akfgstart"] = "--";
        #} else {
            $ausgaben["akfgstart"] = substr($ausgaben["akfgstart"],8,2).".".substr($ausgaben["akfgstart"],5,2).".".substr($ausgaben["akfgstart"],0,4);
        }
    } else {
        $ausgaben["akfg"] = "";
        $ausgaben["afggebtxt"] = "";
        $ausgaben["afgstarttxt"] = "";
        $ausgaben["akfggeb"] = "";
        $ausgaben["akfgstart"] = "";
    }

    // +++
    // ausgabe date bei feldgeschworene


    // Bauen des Register-Kopfes (mor 1905)
    // ***
    $anrede = array("Firma" => array ("akfirma1", "akfirma2"),
                    "Herr" => array ("aknam", "akvor"),
                    "Frau" => array ("aknam", "akvor"));
    foreach ($anrede as $key => $value) {
        if ($key == $field["akanrede"]) {
            $ausgaben["reiter"] = $field[$value[0]]." ".$field[$value[1]];
            if ($key == "Firma") {
                $ausgaben["namen"] = $field[$value[0]];
            } else {
                $ausgaben["namen"] = $field[$value[1]]." ".$field[$value[0]];
            }
        }

    }
    // +++
    // Bauen des Register-Kopfes (mor 1905)


    // email und links mit href versehen (weam 1405)
    // ***
    $modify  = array ("akemail"     => "m",
                      "akinternet"  => "u",
                     );
    foreach($modify as $key => $value) {
        if ( $value == "m" ) {
            $mailto = "mailto:";
        } else {
            $mailto = "";
        }
        if ( $ausgaben[$key] != "--" ) $ausgaben[$key] = "<a href=\"".$mailto.$ausgaben[$key]."\">".$ausgaben[$key]."</a>";
        #echo $ausgaben[$key];
    }
    // +++
    // email und links mit href versehen (weam 1405)


    // Anzeigen der zugehörigen Ansprechpartner (mor 0904)
    // ***
    $ip_class = explode(".", $_SERVER["REMOTE_ADDR"]);
    #$ip_class[2] = 72;
    $ausgaben["ansprechpartner"]  = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
    $ausgaben["ansprechpartner"] .= "<tr class=\"hervorgehoben\"><td>Abteilung:</td><td>Name Vorname:</td><td>Telefon:</td><td>E-Mail:</td></tr>\n";
    $ausgaben["ansprechpartner"] .= "<tr><td colspan=\"4\"><img src=\"".$pathvars["images"]."/kart-linie.gif\" width=\"616\" height=\"11\"></td></tr>\n";
    $sql = "SELECT * FROM ".$cfg["db"]["entries_ans"]." where eid=".$environment["parameter"][1]." AND abnet=".$ip_class[1]." AND acnet=".$ip_class[2]." ORDER BY kaid";
    $result = $db -> query($sql);
    if ( $db->num_rows($result) == 0 ) {
        $ausgaben["ansprechpartner"] .= "<tr><td colspan=\"4\">Keine Ansprechpartner vorhanden.</tr>";
    } else {
        while ( $ans = $db -> fetch_array($result,1) ) {
            if ( $ans["kanam"] == "" ) $ans["kanam"] = "--";
            if ( $ans["kavor"] == "" ) $ans["kavor"] = "--";
            if ( $ans["katel"] == "" ) $ans["katel"] = "--";
            if ( $ans["kaemail"] == "" ) {
                $ans["kaemail"] = "--";
            } else {
                $ans["kaemail"] = "<a href=\"mailto:".$ans["kaemail"]."\">".$ans["kaemail"];
            }
            $ausgaben["ansprechpartner"] .= "<tr><td>".$ans["kanam"]."</td>";
            $ausgaben["ansprechpartner"] .= "<td>".$ans["kavor"]."</td>";
            $ausgaben["ansprechpartner"] .= "<td>".$ans["katel"]."</td>";
            $ausgaben["ansprechpartner"] .= "<td>".$ans["kaemail"]."</td>";
            $ausgaben["ansprechpartner"] .= "</tr>";
        }
    }
    $ausgaben["ansprechpartner"] .= "</table>";
    // +++
    // Anzeigen der zugehörigen Ansprechpartner (mor 0904)


    // navigation erstellen
    #$ausgaben["print_url"] = $environment["basis"]."/print,kunden,details,".$environment["parameter"][1].".html";
    $ausgaben["navigation"] .= "<a href=\"".$_SERVER["HTTP_REFERER"]."\"><img src=\"".$pathvars["images"]."left.png\" border=\"0\" alt=\"Zurück\" title=\"Zurück\" width=\"24\" height=\"18\"></a>";
    $ausgaben["navigation"] .= "<a href=\"".$environment["basis"]."/print,".$environment["parameter"][1].".html\"><img src=\"".$pathvars["images"]."druck.png\" border=\"0\" alt=\"Zurück\" title=\"Details drucken\" width=\"24\" height=\"18\"></a>";
    if ($rechte[$cfg["right"]["adress"]] == -1) {
        $ausgaben["navigation"] .= "<a href=\"".$environment["basis"]."/modify,edit,".$environment["parameter"][1].".html\"><img src=\"".$pathvars["images"]."edit.png\" border=\"0\" alt=\"Bearbeiten\" title=\"Bearbeiten\" width=\"24\" height=\"18\"></a>";
    }



    // was anzeigen
    #$mapping["navi"] = "adressprint";
    $mapping["main"] = crc32($environment["ebene"]).".details";



////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
