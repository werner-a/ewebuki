<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "kunden-modify";
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
    // Bearbeiten
    //


    // warning ausgeben
    if ( get_cfg_var('register_globals') == 1 ) $debugging["ausgabe"] .= "Warning register_globals in der php.ini steht auf on, evtl werden interne Variablen ueberschrieben!".$debugging["char"];

    if ( $environment["parameter"][1] == "add"  && $rechte[$cfg["right"]["adress"]] == -1) {

        if ( count($HTTP_POST_VARS) == 0 ) {
            // spezielle default values setzen
            #$form_values["akfgstart"] = "01.01.1000";
            #$form_values["akfggeb"] = "01.01.1000";
        } else {
            $form_values = $HTTP_POST_VARS;
        }

        // form options holen
        $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);


        // form elememte bauen
        $element = form_elements( $cfg["db"]["entries"], $form_values );

        // form elemente erweitern
        $modify  = array ( "aktel", "akmobil", "akfax");
        foreach($modify as $key => $value) {
            if ( $form_values[$value] == "" ) {
                $element[$value] = str_replace($value."\"", $value."\" value=\"+49-\"", $element[$value]);
            }
        }


        // dropdown Kategorie erstellen (mor 0204)
        // ***
        $selected = "";
        $sql = "SELECT * FROM ".$cfg["db"]["entries_kago"]." ORDER BY kate";
        $result = $db -> query($sql);
        $formularobject  = "<select class=\"".$cfg["form_defaults"]["class"]["dropdown"]."\" name=\"akkate\">\n";
        $formularobject .= "<option value=\"\">Bitte auswählen</option>\n";
        while ( $data = $db->fetch_array($result,$nop) ) {
            if ($form_values["akkate"] == $data[0]) {
                $selected = " selected";
            } else {
                $selected = "";
            }
            $formularobject .= "<option value=\"".$data["katid"]."\"".$selected.">" .$data["kate"] ."</option>\n";
        }
        $formularobject .= "</select>";
        foreach($element as $name => $value) {
            if ($name == "akkate") {
                $element[$name] = $formularobject;
            }
        }
        // +++
        // dropdown Kategorie erstellen (mor 0204)



        // Leere Ansprechpartner bauen (mor 0904)
        // ***
        $ausgaben["ansprechpartner"]  = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
        $ausgaben["ansprechpartner"] .= "<tr class=\"hervorgehoben\"><td>Abteilung</td><td>Name Vorname</td><td>Telefon</td><td>E-Mail</td></tr>\n";
        for ( $i=0; $i < $cfg["db"]["ansp_anzahl"]; $i++ ) {
            $ausgaben["ansprechpartner"] .= "<tr>";
            $ausgaben["ansprechpartner"] .= "<td height=\"18\"><input class=\"textfield-klein\" type=text name=\"".$i.")kanam\" value=\"".$HTTP_POST_VARS[$i.")kanam"]."\"></td>";
            $ausgaben["ansprechpartner"] .= "<td><input class=\"textfield-klein\" type=text name=\"".$i.")kavor\" value=\"".$HTTP_POST_VARS[$i.")kavor"]."\"></td>";
            $ausgaben["ansprechpartner"] .= "<td><input class=\"textfield-klein\" type=text name=\"".$i.")katel\" value=\"".$HTTP_POST_VARS[$i.")katel"]."\"></td>";
            $ausgaben["ansprechpartner"] .= "<td><input class=\"textfield-klein\" type=text name=\"".$i.")kaemail\" value=\"".$HTTP_POST_VARS[$i.")kaemail"]."\"></td>";
            $ausgaben["ansprechpartner"] .= "<td width=\"24\"></td>";
            $ausgaben["ansprechpartner"] .= "</tr>";
        }
        $ausgaben["ansprechpartner"] .= "</table>";
        // +++
        // Leere Ansprechpartner bauen (mor 0904)


        // was anzeigen
        $mapping["main"] = crc32($environment["ebene"]).".".$environment["kategorie"];
        $mapping["navi"] = "leer";

        // wohin schicken
        $ausgaben["form_error"] = "";
        $ausgaben["form_aktion"] = $environment["basis"]."/".$environment["kategorie"].",add,verify.html";

        // referer im form mit hidden element mitschleppen
        if ( $HTTP_POST_VARS["form_referer"] == "" ) {
            $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        } else {
            $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        }

        if ( $environment["parameter"][2] == "verify" ) {

            $ip_class = explode(".", $_SERVER["REMOTE_ADDR"]);


            // wenn kein feldgeschworener beide daten auf leer setzen
            if (!in_array($form_values["akkate"],$feldarray)) {
                $form_values["akfgstart"] = "";
                $form_values["akfggeb"] = "";
            }

            // Je nach Anrede required setzen (mor 2204)
            // ***

            if ($form_values["akanrede"] == "Firma") {
                $form_values["aknam"] = "";
                $form_values["akvor"] = "";
                $form_options["akfirma1"]["frequired"] = -1;
            }
            if ($form_values["akanrede"] == "Frau" || $form_values["akanrede"] == "Herr") {
                $form_values["akfirma1"] = "";
                $form_values["akfirma2"] = "";
                $form_options["aknam"]["frequired"] = -1;
            }
            // +++
            // Je nach Anrede required setzen (mor 2204)

            // E-Mail der Ansprechpartner prüfen (mor 2804)
            // ***
            for ($i=0; $i < $cfg["db"]["ansp_anzahl"]; $i++) {
                $form_options[$i.")kaemail"] = $form_options["akemail"];
                $form_options[$i.")kaemail"]["fchkerror"] = str_replace ("eMail","eMail des ".($i+1).".Ansprechpartners",$form_options[$i.")kaemail"]["fchkerror"]);
            }
            // +++
            // E-Mail der Ansprechpartner prüfen (mor 2804)

            // Großbuchstaben in der E-mail entfernen (mor 2105)
            $form_values["akemail"] = strtolower($form_values["akemail"]);

            // form eigaben prüfen
            #bugfix# form_errors( $form_options, $HTTP_POST_VARS );
            form_errors( $form_options, $form_values );
            #echo $ausgaben["form_error"];

            // ohne fehler sql bauen und ausfuehren
            #if ( $ausgaben["form_error"] == "" && ( $HTTP_POST_VARS["submit"] != "" || $HTTP_POST_VARS["image"] != "" || $HTTP_POST_VARS["add"] != "" ) ) {  // mit überflüssigen "submit"
            if ( $ausgaben["form_error"] == "" && ( $HTTP_POST_VARS["image"] != "" || $HTTP_POST_VARS["add"] != "" ) ) {

                if ($form_values["akfgstart"] == "0") $form_values["akfgstart"] = "";  // Lösung warum matcht regex ?
                if ($form_values["akfggeb"] == "0") $form_values["akfggeb"] = ""; // Lösung warum matcht regex ?

                $kick = array( "PHPSESSID", "submit", "image", "image_x", "image_y", "form_referer", "add", "add_x", "add_y","akfggeb","akfgstart" );
                foreach ($form_values as $name => $value ) {
                    if ( strstr($name, ")") ) $kick[] = $name;
                }
                foreach($form_values as $name => $value) {
                    if ( !in_array($name,$kick) ) {
                      if ( $sqla != "" ) $sqla .= ",";
                      $sqla .= " ".$name;
                      if ( $sqlb != "" ) $sqlb .= ",";
                      $sqlb .= " '".$value."'";
                    }
                }

                // Sql um spezielle Felder erweitern
                session_register("custom");

                // ip der dienststelle des eingeloggten users anspeichern
                $sql = "SELECT adbnet, adcnet FROM db_adrd WHERE adid = ".$HTTP_SESSION_VARS["custom"];
                $result  = $db -> query($sql);
                $eigen = $db -> fetch_array($result,$nop);

                $sqla .= ", abnet, acnet, akdst";
                $sqlb .= ", '".$eigen["adbnet"]."', '".$eigen["adcnet"]."', '".$HTTP_SESSION_VARS["custom"]."'";



                // Sql um spezielle Felder (feldgeschworener) erweitern
                $change = array( "akfggeb", "akfgstart" );
                foreach( $change as $value ) {
                    if ($form_values[$value] != "") {#echo $form_values[$value];
                        $$value = $form_values[$value];
                        $$value = substr($$value,6,4)."-".substr($$value,3,2)."-".substr($$value,0,2);
                        $sqla .= ", ".$value;
                        $sqlb .= ", '".$$value."'";
                    }
                    #echo $$value.":".$value."<br>";
                }



                $sql = "insert into ".$cfg["db"]["entries"]." (".$sqla.") VALUES (".$sqlb.")";
                #echo $sql;
                $result  = $db -> query($sql);


                // neue Ansprechpartner in db schreiben (mor 0904)
                // ***
                if ($result == 1) {
                    $lid = $db -> lastid();
                    for ( $i=0; $i < $cfg["db"]["ansp_anzahl"]; $i++ ) {
                        if (!$form_values[$i.")kanam"] == "" || !$form_values[$i.")kavor"] == "" || !$form_values[$i.")katel"] == "" || !$form_values[$i.")kaemail"] == "") {
                            $form_values[$i.")kaemail"] = strtolower($form_values[$i.")kaemail"]);
                            $sql = "INSERT INTO ".$cfg["db"]["entries_ans"] ."(kanam,kavor,katel,kaemail,eid,abnet,acnet,kadst) VALUES ('".$form_values[$i.")kanam"]."','".$form_values[$i.")kavor"]."','".$form_values[$i.")katel"]."','".$form_values[$i.")kaemail"]."',".$lid.",".$eigen["adbnet"].",".$eigen["adcnet"].",".$HTTP_SESSION_VARS["custom"].")";
                            $result  = $db -> query($sql);
                        }
                    }
                }
                // +++
                // neue Ansprechpartner in db schreiben (mor 0904)


                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                header("Location: ".$ausgaben["form_referer"]);
            }
        }
    } elseif ( $environment["parameter"][1] == "edit" && $rechte[$cfg["right"]["adress"]] == -1) {


        if ( count($HTTP_POST_VARS) == 0 ) {
            $sql = "SELECT * FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][2]."'";
            $result = $db -> query($sql);
            $form_values = $db -> fetch_array($result,$nop);
        } else {
            $form_values = $HTTP_POST_VARS;
        }


        // form options holen
        $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

        // wenn kein recht vorhanden allgemeine felder auf readonly setzen
        if ($form_values["akdst"] != $HTTP_SESSION_VARS["custom"]) {
            $rechtarray = array ("akfirma1","akfirma2","aknam","akvor","akort","akstr","akplz","akpplz","akpfach","aktel","akfax","akemail","akinternet","akmobil","akfgstart","akfggeb");
            foreach ( $rechtarray as $value) {
                $form_options[$value]["foption"] = "readonly";
            }
        }
        #echo "<pre>";
        #print_r($form_options);
        #echo "</pre>";
        // form elememte bauen
        $element = form_elements( $cfg["db"]["entries"], $form_values );


        // dropdown erstellen (mor 0204)
        // ***
        $sql = "SELECT * FROM ".$cfg["db"]["entries_kago"]." ORDER BY kate";
        $result = $db -> query($sql);
        $formularobject  = "<select class=\"".$cfg["form_defaults"]["class"]["dropdown"]."\" name=\"akkate\">\n";
        #$formularobject .= "<option value=\"\"></option>\n";
        while ( $data = $db->fetch_array($result,$nop) ) {
            if ($form_values["akkate"] == $data[0]) {
                $selected = " selected";
            } else {
                $selected = "";
            }
            $formularobject .= "<option value=\"".$data[0]."\"".$selected.">" .$data["kate"] ."</option>\n";
        }
        $formularobject .= "</select>";
        foreach($element as $name => $value) {
            if ($name == "akkate") {
                $element[$name] = $formularobject;
            }
        }
        // +++
        // dropdown erstellen (mor 0204)

        // Ansprechpartner Form bauen mor(0804)
        // ***
       # echo "<pre>";
       # print_r($form_values);
       # echo "</pre>";
        $ip_class = explode(".", $_SERVER["REMOTE_ADDR"]);
        #$ip_class[2] = 72;
        if ( count($HTTP_POST_VARS) == 0 ) {
#            $sql = "SELECT * FROM ".$cfg["db"]["entries_ans"]." where eid=".$environment["parameter"][2]." AND abnet=".$ip_class[1]." AND acnet=".$ip_class[2]." ORDER BY kaid";
            $sql = "SELECT * FROM ".$cfg["db"]["entries_ans"]." where eid=".$environment["parameter"][2]." AND kadst=".$HTTP_SESSION_VARS["custom"]." ORDER BY kaid";
            #echo $sql;
            $result = $db -> query($sql);
            while ( $ans = $db -> fetch_array($result,1) ) {
                $element_ans[$ans["kaid"]] = form_elements( $cfg["db"]["entries_ans"], $ans );
            }
        } else {
            foreach ( $form_values as $key => $value ) {
                if  ( strstr($key, ")") ) {
                    $felder = explode(")",$key,2);
                    $ans[$felder[0]][$felder[1]] = $value;
                }
            }
            if ( is_array($ans) ) {
                foreach ( $ans as $key => $value ) {
                    $form_options[$key.")kaemail"] = $form_options["akemail"];
                    $element_ans[$key] = form_elements( $cfg["db"]["entries_ans"], $value );
                }
            }
        }

        $ausgaben["ansprechpartner"]  = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
        if ( is_array($element_ans) ) {
            $ausgaben["ansprechpartner"]  .= "<tr class=\"hervorgehoben\"><td>Abteilung</td><td>Name Vorname</td><td>Telefon</td><td>E-Mail</td></tr>";
            $ausgaben["ansprechpartner"] .= "<tr><td colspan=\"4\"><img src=\"".$pathvars["images"]."/kart-linie.gif\" width=\"600\" height=\"11\"></td></tr>\n";
            foreach ( $element_ans as $key => $value ) {
                $ausgaben["ansprechpartner"] .= "<tr><td>".str_replace("name=\"","name=\"".$key.")",$value["kanam"])."</td>";
                $ausgaben["ansprechpartner"] .= "<td>".str_replace("name=\"","name=\"".$key.")",$value["kavor"])."</td>";
                $ausgaben["ansprechpartner"] .= "<td>".str_replace("name=\"","name=\"".$key.")",$value["katel"])."</td>";
                $ausgaben["ansprechpartner"] .= "<td>".str_replace("name=\"","name=\"".$key.")",$value["kaemail"])."</td><td></td><td></td><td></td>";
                #$ausgaben["ansprechpartner"] .= "<input name=\"edit\" type=\"image\" src=\"".$pathvars["images"]."/edit.gif\" width=\"24\" height=\"18\" border=\"0\" value=\"".$ans["kaid"]."\">";
                $ausgaben["ansprechpartner"] .= "<td align=\"right\"><input name=\"delete\" type=\"image\" src=\"".$pathvars["images"]."delete.png\" width=\"24\" height=\"18\" border=\"0\" value=\"".$key."\"></td></tr>";
                #$ausgaben["ansprechpartner"] .= "<tr>$counter</tr>";
            }
        } else {
            $ausgaben["ansprechpartner"] .= "<tr><td colspan=\"4\">Keine Ansprechpartner vorhanden.</tr>";
        }

        $ausgaben["ansprechpartner"] .= "</table>";
        // +++
        // Ansprechpartner Form bauen mor(0804)


        // was anzeigen
        $mapping["main"] = crc32($environment["ebene"]).".modify";
        $mapping["navi"] = "leer";

        // wohin schicken
        $ausgaben["form_error"] = "";
        $ausgaben["form_aktion"] = $environment["basis"]."/modify,edit,".$environment["parameter"][2].",verify.html";

        // referer im form mit hidden element mitschleppen
        if ( $HTTP_GET_VARS["referer"] != "" ) {
            $ausgaben["form_referer"] = $HTTP_GET_VARS["referer"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        } elseif ( $HTTP_POST_VARS["form_referer"] == "" ) {
            $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        } else {
            $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        }


        if ( $environment["parameter"][3] == "verify" ) {

            // wenn kein feldgeschworener beide daten auf leer setzen
            if (!in_array($form_values["akkate"],$feldarray)) {
                $form_values["akfgstart"] = "";
                $form_values["akfggeb"] = "";
            }

            // Je nach Anrede required setzen (mor 2204)
            // ***
            if ($form_values["akanrede"] == "Firma") {
                $form_values["aknam"] = "";
                $form_values["akvor"] = "";
                $form_options["akfirma1"]["frequired"] = -1;
            }
            if ($form_values["akanrede"] == "Frau" || $form_values["akanrede"] == "Herr") {
                $form_options["aknam"]["frequired"] = -1;
                $form_values["akfirma1"] = "";
                $form_values["akfirma2"] = "";
            }
            // +++
            // Je nach Anrede required setzen (mor 2204)


            // form eingaben prüfen
            #bugfix#form_errors( $form_options, $HTTP_POST_VARS );
            form_errors( $form_options, $form_values );

            // ohne fehler sql bauen und ausfuehren
            if ( $ausgaben["form_error"] == "" /* && ( $HTTP_POST_VARS[submit] != "" || $HTTP_POST_VARS[image] != "" ) */ ){
                #echo "fehler";
                if ($form_values["akfgstart"] == "0") $form_values["akfgstart"] = "";
                if ($form_values["akfggeb"] == "0") $form_values["akfggeb"] = "";

                $kick = array( "PHPSESSID", "submit", "image", "image_x", "image_y", "form_referer", "add", "add_x", "add_y", "delete", "delete_x", "delete_y","akfggeb","akfgstart" );
                foreach($form_values as $name => $value) {
                    if ( !in_array($name,$kick) && !strstr($name, ")" ) ) {
                        if ( $sqla != "" ) $sqla .= ", ";
                        $sqla .= $name."='".$value."'";
                    }
                }

                // Sql um spezielle Felder (feldgeschworener) erweitern
                $change = array( "akfggeb", "akfgstart" );
                foreach( $change as $value ) {
                    #echo $form_values[$value];
                    if ($form_values[$value] != "") {
                        $$value = $form_values[$value];
                        $$value = substr($$value,6,4)."-".substr($$value,3,2)."-".substr($$value,0,2);

                        $sqla .= ",".$value."='".$$value."'";
                    } else {
                        $sqla .= ",".$value."= NULL";
                    }
                    #$sqlb .= ", '".$$value."'";
                    #echo $$value.":".$value."<br>";
                }


                // Sql um spezielle Felder erweitern

                $sql = "update ".$cfg["db"]["entries"]." SET ".$sqla." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][2]."'";
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "mainsql: ".$sql.$debugging["char"];
                #echo $sql;
                $result  = $db -> query($sql);
                if ($result == 1) {
                    foreach ($form_values as $name => $value) {
                        if ( strstr($name, ")") )  {
                            $pos = strpos($name,")");
                            $sql = "UPDATE ".$cfg["db"]["entries_ans"]." SET ".substr($name,$pos+1)."=\"".$form_values[$name]."\" WHERE kaid=".substr($name,0,$pos);
                            $result  = $db -> query($sql);
                        }
                    }
                }

                // Ansprechpartner Elemente abfragen und sql bauen (mor 0804)
                // ***
                if ($HTTP_POST_VARS["add"]) {
                    $sql = "SELECT adbnet,adcnet FROM db_adrd WHERE adid = ".$HTTP_SESSION_VARS["custom"];
                    $result  = $db -> query($sql);
                    $eigen = $db -> fetch_array($result,$nop);
                    $sql = "INSERT INTO ".$cfg["db"]["entries_ans"]." (eid,abnet,acnet,kadst) VALUES (".$environment["parameter"][2].",".$eigen["adbnet"].",".$eigen["adcnet"].",".$HTTP_SESSION_VARS["custom"].")";
                    #echo $sql;
                    $result  = $db -> query($sql);
                    header("Location: ".$environment["basis"]."/modify,edit,".$environment["parameter"][2].",verify.html?referer=".$ausgaben["form_referer"]);
                } elseif ($HTTP_POST_VARS["delete"]) {
                    $sql = "DELETE FROM ".$cfg["db"]["entries_ans"]." WHERE kaid=".$HTTP_POST_VARS["delete"];
                    $result  = $db -> query($sql);
                    header("Location: ".$environment["basis"]."/modify,edit,".$environment["parameter"][2].",verify.html?referer=".$ausgaben["form_referer"]);
                }/*elseif ($HTTP_POST_VARS["edit"]) {
                    $sql = "UPDATE ".$cfg["db"]["entries_ans"]." SET kanam=\"".$HTTP_POST_VARS[$HTTP_POST_VARS["edit"].")kanam"]."\",kavor=\"".$HTTP_POST_VARS[$HTTP_POST_VARS["edit"].")kavor"]."\",katel=\"".$HTTP_POST_VARS[$HTTP_POST_VARS["edit"].")katel"]."\" WHERE kaid = ".$HTTP_POST_VARS["edit"];
                    $result  = $db -> query($sql);
                    header("Location: ".$environment["basis"]."/modify,edit,".$environment["parameter"][2].",verify.html?referer=".$ausgaben["form_referer"]);
                }*/
                // +++
                // Ansprechpartner Elemente abfragen und sql bauen (mor 0804)

                #if ($HTTP_POST_VARS["image"] == "Daten senden") {
                #    header("Location: ".$ausgaben["form_referer"]);
                #}
                if ( $HTTP_POST_VARS["image"] == "Daten senden") header("Location: ".$ausgaben["form_referer"]);
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
            }
        }
    } elseif (( $environment["parameter"][1] == "delete" ) && ($rechte[$cfg["right"]["adress"]] == -1)) {
            $ausgaben["form_error"] = "";

            $sql = "SELECT * FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][2]."'";
            $result = $db -> query($sql);
            $field = $db -> fetch_array($result,$nop);

            // zugang verbieten wenn es ein fremder kunde ist
            if ( $HTTP_SESSION_VARS["custom"] != $field["akdst"]  ) {
                die(" Access denied, <br> your ip-adress has been logged");
            }

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


            // Bauen des Register-Kopfes (mor 1905)
            // ***
            $anrede = array("Firma" => array ("akfirma1", "akfirma2"),
                            "Herr" => array ("aknam", "akvor"),
                            "Frau" => array ("aknam", "akvor"));
            foreach ($anrede as $key => $value) {
                if ($key == $field["akanrede"]) {
                    $ausgaben["reghead"] = $field[$value[0]]." ".$field[$value[1]];
                }
            }
            // +++
            // Bauen des Register-Kopfes (mor 1905)


            // Anzeigen der zugehörigen Ansprechpartner (mor 0904)
            // ***
            $ip_class = explode(".", $_SERVER["REMOTE_ADDR"]);
            $ausgaben["ansprechpartner"]  = "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n";
            $ausgaben["ansprechpartner"] .= "<tr class=\"hervorgehoben\"><td>Abteilung:</td><td>Name Vorname:</td><td>Telefon:</td><td>E-Mail:</td></tr>\n";
            $ausgaben["ansprechpartner"] .= "<tr><td colspan=\"4\"><img src=\"".$pathvars["images"]."/kart-delete-linie.gif\" width=\"616\" height=\"11\"></td></tr>\n";
            $sql = "SELECT * FROM ".$cfg["db"]["entries_ans"]." where eid=".$environment["parameter"][2]." AND abnet=".$ip_class[1]." AND acnet=".$ip_class[2];
            $result = $db -> query($sql);
            if ( $db->num_rows($result) == 0 ) {
                $ausgaben["ansprechpartner"] .= "<tr><td colspan=\"4\">Keine Ansprechpartner vorhanden.</tr>";
            } else {
                while ( $ans = $db -> fetch_array($result,1) ) {
                    if ($ans["kanam"] == "") $ans["kanam"] = "--";
                    if ($ans["kavor"] == "") $ans["kavor"] = "--";
                    if ($ans["katel"] == "") $ans["katel"] = "--";
                    if ($ans["kaemail"] == "") $ans["kaemail"] = "--";
                    $ausgaben["ansprechpartner"] .= "<td>".$ans["kanam"]."</td>";
                    $ausgaben["ansprechpartner"] .= "<td>".$ans["kavor"]."</td>";
                    $ausgaben["ansprechpartner"] .= "<td>".$ans["katel"]."</td>";
                    $ausgaben["ansprechpartner"] .= "<td>".$ans["kaemail"]."</td>";
                    $ausgaben["ansprechpartner"] .= "</tr>";
                }
            }
            $ausgaben["ansprechpartner"] .= "</table>";
            // +++
            // Anzeigen der zugehörigen Ansprechpartner (mor 0904)


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


            // was anzeigen
            $mapping["main"] = crc32($environment["ebene"]).".delete";
            $mapping["navi"] = "leer";

          // referer im form mit hidden element mitschleppen
            if ( $HTTP_POST_VARS["form_referer"] == "" ) {
                $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
                $ausgaben["form_break"] = $ausgaben["form_referer"];
            } else {
                $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
                $ausgaben["form_break"] = $ausgaben["form_referer"];
            }

            // wohin schicken
            $ausgaben["form_aktion"] = $environment["basis"]."/modify,delete,".$environment["parameter"][2].".html";
            $ausgaben["form_break"] = $ausgaben["form_referer"]; // Nun mit Post im hidden element (mor 0605)

            if ( $HTTP_POST_VARS["delete"] == "true" ) {
                $sql = "SELECT DISTINCT abnet,acnet FROM ".$cfg["db"]["entries_ans"]." where eid=".$environment["parameter"][2];
                $result = $db -> query($sql);
                if ( $db->num_rows($result) == 0 ) {
                    $sql = "DELETE FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][2]."'";
                    $result  = $db -> query($sql);
                    header("Location: ".$environment["basis"]."/list.html");
                } else {
                    $ausgaben["form_error"] .= "An folgenden Dienststellen sind noch Ansprechpartner vorhanden: <br>";
                    while ( $ans = $db -> fetch_array($result,$nop) ) {
                        $sql_dst = "SELECT * FROM db_adrd WHERE adbnet = ".$ans["abnet"]." AND adcnet =".$ans["acnet"];
                        $result_dst = $db -> query($sql_dst);
                        while ( $data = $db -> fetch_array($result_dst,$nop) ) {
                            $ausgaben["form_error"] .= $data["adkate"] ." ".$data["adststelle"]."<br>";
                        }
                    }
                }
            }
        } else {
            header("Location: ".$pathvars["webroot"]."/".$environment["design"]."/".$environment["language"]."/index.html");
            #$ausgaben["output"] .= "ZUGRIFF VERWEIGERT";
        }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
