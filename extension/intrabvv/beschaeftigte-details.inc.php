<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "Beschaeftigte details";
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


    //
    // Details anzeigen
    //
    if ( $environment[kategorie] == "details" ) {
        $sql = "SELECT * FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$environment[parameter][1]."'";
        $result = $db -> query($sql);
        $field = $db -> fetch_array($result,$nop);

        // ausgaben erweitern


        // Amtsbezeichnung des Beschäftigten holen (wach 2603)
        // ***
        $sql = "SELECT abamtbezlang FROM db_adrb_amtbez WHERE abamtbez_id='".$field["abamtbez"]."'";
        $result = $db -> query($sql);
        $amtbez = $db -> fetch_array($result,$nop);
        // +++
        // Amtsbezeichnung des Beschäftigten holen

        // Dienstposten des Beschäftigten holen (wach 0404)
        // ***
        $sql = "SELECT abdienst FROM db_adrb_dienst WHERE abdienst_id='".$field["abdstposten"]."'";
        $result = $db -> query($sql);
        $dstposten = $db -> fetch_array($result,$nop);
        // +++
        // Dienstposten des Beschäftigten holen


        // Interessen des Beschäftigten holen (wach 1704)
        // ***
        $sql = "SELECT abint FROM db_adrb_int";
        $result = $db -> query($sql);
        $interessen="";
        while ( $data = $db->fetch_array($result,$nop) ) {
            foreach (explode(";",$field["abinteressen"]) as $single_interest) {
                if ($single_interest == $data["abint"]) {
                    if ( $interessen != "" ) $interessen .= ", ";
                    $interessen .= $data["abint"];
                }
            }
        }

        // +++
        // Interessen des Beschäftigten holen


        // kategorie und dienststelle holen (weam 2005)
        // ***
        $sql = "SELECT adkate, adststelle, adstbfd FROM db_adrd WHERE adid='".$field["abdststelle"]."'";
        $result = $db -> query($sql);
        $dststelle = $db -> fetch_array($result,$nop);
        $ausgaben["abdstbfd"] = $dststelle["adstbfd"];
        // +++
        // kategorie und dienststelle holen (weam 2005)


        // bei allen nicht va bfd und abteilung holen (weam 2005)
        // ***
        if ( $dststelle["adkate"] != "VA" ) {
            $sql = "SELECT adstabt FROM db_adrd WHERE adbnet='".$field["abbnet"]."' AND adkate='BFD'";
            $sql = "SELECT adstabt FROM db_adrd WHERE adbnet='".$field["abbnet"]."'";
            $result = $db -> query($sql);
            $data = $db -> fetch_array($result,$nop);
            #$ausgaben["abdstbfd"] = $data["adstbfd"];
            $ausgaben["adstabt"] = $data["adstabt"];
        } else {
           # $ausgaben["abdstbfd"] = "--";
            $ausgaben["adstabt"] = "--";
        }
        // +++
        // bei allen nicht va bfd und abteilung holen (weam 2005)


        // ip bindung
        // ***
        if (($ip_class[1] != $field["abbnet"]) || ($ip_class[2] != $field["abcnet"])) $sperre=true;
        // +++
        // ip Bindung


        // alle veraenderten elemente umbauen (wach 0404)
        // ***
        foreach($field as $key => $value) {
            if ( $value == "" && $key != "abad") $value ="--";
            if ($key == "abad" && $value == -1) $value = "(im Außendienst)";
            if ($key == "abtitel" && $value == "--") $value = "";
            if ($key == "abgrad" && $value == "--") $value = "";
            if ($key == "abamtbez") $value = $amtbez[0];
            if ($key == "abdstposten") $value = $dstposten[0];
            if ($key == "abinteressen") $value = $interessen;
            if ($key == "abdststelle") $value = $dststelle["adkate"]." ".$dststelle["adststelle"];
            if (strstr($key,"abpriv") && $sperre) $value = "--";
            $ausgaben[$key] = $value;
        }
        // +++
        // alle veraenderten elemente umbauen (wach 0404)


        // Bauen des Register-Kopfes (mor 1905)
        // ***
        $ausgaben["reiter"] = $dststelle["adkate"]." ".$dststelle["adststelle"];
        $anrede = array("Raum" => array ("abnamra", ""),
                        "Herr" => array ("abnamra", "abnamvor"),
                        "Frau" => array ("abnamra", "abnamvor"));
        foreach ($anrede as $key => $value) {
            if ($key == $field["abanrede"]) {
                $ausgaben["namen"] = $field[$value[1]]." ".$field[$value[0]];
            }

        }
        // +++
        // Bauen des Register-Kopfes (mor 1905)


        // email und links mit href versehen (weam 1405)
        // ***
        $modify  = array ("abdstemail"     => "m",
                          "abprivemail"    => "m"
                         );
        foreach($modify as $key => $value) {
            if ( $value == "m" ) {
                $mailto = "mailto:";
            } else {
                $mailto = "";
            }
            if ( $ausgaben[$key] != "--" ) $ausgaben[$key] = "<a href=\"".$mailto.$ausgaben[$key]."\">".$ausgaben[$key]."</a>";
        }
        // +++
        // email und links mit href versehen (weam 1405)


        // navigation erstellen
        $ausgaben["navigation"] .= "<a href=\"".$_SERVER["HTTP_REFERER"]."\"><img src=\"".$pathvars[images]."left.png\" border=\"0\" alt=\"Zurück\" title=\"Zurück\" width=\"24\" height=\"18\"></a>";
        $ausgaben["navigation"] .= "<a href=\"".$cfg["basis"]."/print,".$environment["parameter"][1].".html\"><img src=\"".$pathvars["images"]."druck.png\" border=\"0\" alt=\"Zurück\" title=\"Details drucken\" width=\"24\" height=\"18\"></a>";

        // icon "bearbeiten" erstellen
        // wenn berechtigung vorhanden

        // icon schwarz für eigene dienststelle
        if ( $rechte[$cfg["right"]["adress"]] == -1 && $HTTP_SESSION_VARS["custom"] == $field["abdststelle"]) {
        #if ( $rechte[$cfg["right"]["adress"]] == -1 && ( $ip_class[1] == $field["abbnet"] &&  $ip_class[2] == $field["abcnet"] )) {
            $ausgaben["navigation"] .= "<a href=\"".$cfg["basis"]."/modify,edit,".$environment[parameter][1].".html\"><img src=\"".$pathvars[images]."edit.png\" border=\"0\" alt=\"Bearbeiten\" title=\"Bearbeiten\" width=\"24\" height=\"18\"></a>";

        // icon rot für andere dienststelle
        } elseif ( $rechte[$cfg["right"]["admin"]] == -1
                && in_array($field["abdststelle"],$HTTP_SESSION_VARS["dstzugriff"]) ) {
            $ausgaben["navigation"] .= "<a href=\"".$cfg["basis"]."/modify,edit,".$environment[parameter][1].".html\"><img src=\"".$pathvars[images]."edita.png\" border=\"0\" alt=\"Bearbeiten\" title=\"Fremde Beschäftigte bearbeiten\" width=\"24\" height=\"18\"></a>";
        }

        // icon "recht hinzufügen" bzw. "recht bearbeiten" erstellen
        // wenn berechtigung vorhanden

        if ( $rechte[$cfg["right"]["admin"]] == -1 && in_array($field["abdststelle"],$HTTP_SESSION_VARS["dstzugriff"]) && $field["abanrede"] != "Raum" ) {

            // icon schwarz für eigene dienststelle
            #if ( $ip_class[1] == $field["abbnet"] && $ip_class[2] == $field["abcnet"] ) {
            if ( $HTTP_SESSION_VARS["custom"] == $field["abdststelle"] ) {
                if ( $field["abpasswort"] == "") {
                    $ausgaben["navigation"]  .= "<a href=\"".$pathvars["virtual"]."/admin/usered/modify,add,".$field[$cfg["db"]["key"]].".html\"><img src=\"".$pathvars[images]."addr.png\" border=\"0\" alt=\"Rechte hinzufügen\" title=\"Rechte hinzufügen\" width=\"24\" height=\"18\"></a>";
                } else {
                    $ausgaben["navigation"] .= "<a href=\"".$pathvars["virtual"]."/admin/usered/modify,edit,".$field[$cfg["db"]["key"]].".html\"><img src=\"".$pathvars[images]."editr.png\" border=\"0\" alt=\"Rechte bearbeiten\" title=\"Rechte bearbeiten\" width=\"24\" height=\"18\"></a>";
                }
            // icon rot für andere dienststelle
            } else {
                if ( $field["abpasswort"] == "") {
                    $ausgaben["navigation"] .= "<a href=\"".$pathvars["virtual"]."/admin/usered/modify,add,".$field[$cfg["db"]["key"]].".html\"><img src=\"".$pathvars[images]."addra.png\" border=\"0\" alt=\"Rechte hinzufügen\" title=\"Fremde Rechte hinzufügen\" width=\"24\" height=\"18\"></a>";
                } else {
                    $ausgaben["navigation"] .= "<a href=\"".$pathvars["virtual"]."/admin/usered/modify,edit,".$field[$cfg["db"]["key"]].".html\"><img src=\"".$pathvars[images]."editra.png\" border=\"0\" alt=\"Rechte bearbeiten\" title=\"Fremde Rechte bearbeiten\" width=\"24\" height=\"18\"></a>";
                }
            }
        }


        // was anzeigen
        $mapping["main"] = crc32($environment["ebene"]).".details";
    }



////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
