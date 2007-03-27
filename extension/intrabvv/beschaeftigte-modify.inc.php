<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "Beschaeftigte modify";
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


    if ( strstr($environment["kategorie"], "modify") ) {

        // warning ausgeben
        if ( get_cfg_var('register_globals') == 1 ) $debugging["ausgabe"] .= "Warning register_globals in der php.ini steht auf on, evtl werden interne Variablen ueberschrieben!".$debugging["char"];



        if ( $environment["parameter"][1] == "add" && $rechte[$cfg["right"]["adress"]] == -1) {
            $form_values = $HTTP_POST_VARS;

            // form options holen
            $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

            // form elememte bauen
            $element = form_elements( $cfg["db"]["entries"], $HTTP_POST_VARS );

            // dropdown amtsbezeichnung aus db erstellen (wach 0304)
            // ***
            $sql = "SELECT abamtbez_id, abamtbezkurz FROM db_adrb_amtbez ORDER by abamtbez_sort";
            $result = $db -> query($sql);
            $formularobject[1] = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"abamtbez\">\n";
            $formularobject[1] .= "<option value=\"\">Bitte auswählen</option>\n";

            while ( $field = $db->fetch_row($result,$nop) ) {
                if ($form_values["abamtbez"] == $field[0]) {
                    $selected = " selected";
                } else {
                    $selected = "";
                }
                $formularobject[1] .= "<option value=\"".$field[0]."\"".$selected.">".$field[1] ."</option>\n";
            }
            $formularobject[1] .= "</select>\n";
            // +++
            // dropdown amtsbezeichnung aus db erstellen


            // dropdown dienstposten aus db erstellen (wach 0404)
            // ***
            $sql = "SELECT abdienst_id, abdienst FROM db_adrb_dienst ORDER by abdienst_sort";
            $result = $db -> query($sql);
            $formularobject[2] = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"abdstposten\">\n";
            $formularobject[2] .= "<option value=\"\">Bitte auswählen</option>\n";
            while ( $field = $db->fetch_row($result,$nop) ) {
                if ($form_values["abdstposten"] == $field[0]) {
                    $selected = " selected";
                } else {
                    $selected = "";
                }
                $formularobject[2] .= "<option value=\"".$field[0]."\"".$selected.">".$field[1] ."</option>\n";
            }
            $formularobject[2] .= "</select>\n";
            // +++
            // dropdown dienstposten aus db erstellen


            // dropdown interessen aus db erstellen (wach 1104)
            // ***
            $sql = "SELECT abint FROM db_adrb_int ORDER by abint_sort";
            $result = $db -> query($sql);
            $formularobject[3] = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"abinteressen[]\" size=\"5\" multiple>\n";
            $formularobject[3] .= "<option value=\"\">Bitte auswählen... (Mehrfach)</option>\n";

            if ( !is_array($form_values["abinteressen"]) ) $form_values["abinteressen"] = explode(";", $form_values["abinteressen"]);

            while ( $field = $db->fetch_row($result,$nop) ) {
                foreach ($form_values["abinteressen"] as $single_interest) {
                    if ($single_interest == $field[0]) {
                        $selected = " selected";
                        break;
                    } else {
                        $selected = "";
                    }
                }
                $formularobject[3] .= "<option value=\"".$field[0]."\"".$selected.">".$field[0] ."</option>\n";
            }
            $formularobject[3] .= "</select>\n";
            // +++
            // dropdown interessen aus db erstellen



            // feld kategorie, bfd, tel und fax aus db erstellen (wach 1704)
            // ***
            $sql = "SELECT adid, adkate, adststelle, adstbfd, adtelver, adfax, adbnet, adcnet FROM db_adrd WHERE adbnet=\"".$ip_class[1]."\" AND adcnet=\"".$ip_class[2]."\"";
            $result = $db -> query($sql);
            $field = $db -> fetch_array($result,$nop);
            #$formularobject[4] = $field["adkate"]." ".$field["adststelle"];
            #$formularobject[5] = $field["adstbfd"];
            $ausgaben["abdststelle"] = $field["adkate"]." ".$field["adststelle"];
            $formularobject[4] = str_replace("abdststelle\"", "abdststelle\" value=\"".$field["adid"]."\"", $element["abdststelle"]);
            $ausgaben["abdstbfd"] = $field["adstbfd"];


            $pos=strrpos($field["adtelver"], "-");
            $tel = substr($field["adtelver"],0,$pos+1);
            $fax = $field["adfax"];

            $formularobject[6] = str_replace("abdsttel\"", "abdsttel\" value=\"$tel\"", $element["abdsttel"]);
            $formularobject[7] = str_replace("abdstfax\"", "abdstfax\" value=\"$fax\"", $element["abdstfax"]);
            // +++
            // feld kategorie, bfd, tel und fax aus db erstellen



            // alle veraenderten elemente umbauen
            // ***
            foreach($element as $name => $value) {
                if ($name == "abamtbez") {
                    $element[$name] = $formularobject[1];
                } elseif ($name == "abdstposten") {
                    $element[$name] = $formularobject[2];
                } elseif ($name == "abinteressen") {
                    $element[$name] = $formularobject[3];
                // abdststelle hidden miführen wegen HTTP_POST_VARS (wach 2304)
                } elseif ($name == "abdststelle") {
                    $element[$name] = $formularobject[4];
                } elseif ($name == "abdsttel") {
                    if (!$form_values["abdsttel"]) {
                        $element[$name] = $formularobject[6];
                    }
                } elseif ($name == "abdstfax") {
                    if (!$form_values["abdstfax"]) {
                        $element[$name] = $formularobject[7];
                    }
                }
            }
            // +++
            // alle veraenderten elemente umbauen

            // form elemente erweitern
            #$modify  = array ("abdstmobil", "abprivtel", "abprivmobil");
            #foreach($modify as $key => $value) {
                #if ( $HTTP_POST_VARS[$value] == "" )
                #{
                    #$element[$value] = str_replace($value."\"", $value."\" value=\"+49-\"", $element[$value]);
                #}
            #}

            // marke !#ausgaben_administration verschwinden lassen (wird im sql gesetzt)
            $ausgaben["administration"] = "";

            // was anzeigen
            $mapping["main"] = crc32($environment["ebene"]).".".$environment["kategorie"];
            $mapping["navi"] = "leer";

            // wohin schicken
            $ausgaben["form_error"] = "";
            $ausgaben["form_aktion"] = $cfg["basis"]."/".$environment["kategorie"].",add,verify.html";

            // referer im form mit hidden element mitschleppen
            if ( $HTTP_POST_VARS["form_referer"] == "" ) {
                $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
                $ausgaben["form_break"] = $ausgaben["form_referer"];
            } else {
                $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
                $ausgaben["form_break"] = $ausgaben["form_referer"];
            }

            // interessen mit ";" trennen für Datenbankeintrag
            // ***
            if ( is_array($form_values["abinteressen"]) ) {
                $form_values["abinteressen"] = implode(";", $form_values["abinteressen"]);
            }
            // +++
            // interessen mit ";" trennen für Datenbankeintrag



            if ( $environment["parameter"][2] == "verify" ) {

                // pflichtfelder setzen bei anrede Herr oder Frau (wa 0705)
                // Datenbankeintrag (nicht required) wird ignoriert
                // ***
                if ( $form_values["abanrede"] == "Frau" || $form_values["abanrede"] == "Herr" ) {
                    $form_options["abnamvor"][frequired] = -1;
                    $form_options["abnamkurz"][frequired] = -1;
                    $form_options["abamtbez"][frequired] = -1;
                    $form_options["abdstposten"][frequired] = -1;
                    $form_options["abdstemail"][frequired] = -1;

                    // Feld dienstposten ist kein Pflichtfeld bei amtsbezeichnung Arbeiter und Angestellte
                    // 1=Arb, 2=Arbin, 3=VA, 4=VAe
                    // ***
                    #$amtsbezeichnung = array(1,2,3,4);
                    #foreach ( $amtsbezeichnung as $value ) {
                    #    if ( $value == $form_values["abamtbez"] ) {
                    #        $form_options["abdstposten"][frequired] = 0;
                    #        break;
                    #    } else {
                    #        $form_options["abdstposten"][frequired] = -1;
                    #    }
                    #}
                    // +++
                    // Feld dienstposten ist kein Pflichtfeld bei amtsbezeichnung Arbeiter und Angestellte

                } else {
                    $form_values["abamtbez"] = "";
                    $form_values["abnamkurz"] = "";
                    $form_values["abtitel"] = "";
                    $form_values["abnamvor"] = "";
                    $form_values["abad"] = 0;
                }
                // +++
                // pflichtfelder setzen bei anrede Herr oder Frau (wa 0705)

                // form eingaben prüfen
                #bugfix# form_errors( $form_options, $HTTP_POST_VARS );
                form_errors( $form_options, $form_values );

                //  namenskuerzel ist an dienststelle bereits vorhanden
                $sql = "SELECT abnamkurz, abdststelle FROM ".$cfg["db"]["entries"] ." WHERE abnamkurz='".$form_values["abnamkurz"]."' AND abdststelle='".$form_values["abdststelle"]."'";
                $result = $db -> query($sql);
                $field = $db -> fetch_array($result,$nop);
                if ( $field["abnamkurz"] != "" )  {
                    $ausgaben["form_error"] = "Namenskürzel ist bereits an Ihrer Dienststelle vorhanden, bitte ändern.";
                }

                // dienststellennr. holen und ablogin zusammensetzen
                if ( $form_values["abnamkurz"] != "") {
                    $sql = "SELECT adakz FROM db_adrd WHERE adid='".$form_values["abdststelle"]."'";
                    $result = $db -> query($sql);
                    $field = $db -> fetch_array($result,$nop);
                    $login_name = $form_values["abnamkurz"].$field["adakz"];
                }

                // ohne fehler sql bauen und ausfuehren
                if ( $ausgaben["form_error"] == "" /* && ( $HTTP_POST_VARS["submit"] != "" || $HTTP_POST_VARS["image"] != "" ) */) {
                    $kick = array( "PHPSESSID", "submit", "image", "image_x", "image_y", "form_referer", "bnet", "cnet" );
                    #bugfix# foreach($HTTP_POST_VARS as $name => $value) {
                        foreach($form_values as $name => $value) {
                        if ( !in_array($name,$kick) ) {
                             if ( $sqla != "" ) $sqla .= ",";
                             $sqla .= " ".$name;
                             if ( $sqlb != "" ) $sqlb .= ",";
                             $sqlb .= " '".$value."'";
                        }
                    }

                    // Sql um spezielle Felder erweitern

                    $sqla .= ", abbnet, abcnet, ablogin";
                    $sqlb .= ", '".$ip_class[1]."', '".$ip_class[2]."', '".$login_name."'";

                    #$sqla .= ", abbnet, abcnet";
                    #$sqlb .= ", '".$ip_class[1]."', '".$ip_class[2]."'";


                    #$ldate = $HTTP_POST_VARS["ldate"];
                    #$ldate = substr($ldate,6,4)."-".substr($ldate,3,2)."-".substr($ldate,0,2)." ".substr($ldate,11,9);
                    #$sqla .= ", ldate";
                    #$sqlb .= ", '".$ldate."'";
                    $sql = "insert into ".$cfg["db"]["entries"]." (".$sqla.") VALUES (".$sqlb.")";
                    $result  = $db -> query($sql);
                    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                    header("Location: ".$ausgaben["form_referer"]);
                }
            }



        } elseif ( $environment["parameter"][1] == "edit" && $rechte[$cfg["right"]["adress"]] == -1 ) {

            if ( count($HTTP_POST_VARS) == 0 ) {
                $sql = "SELECT * FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][2]."'";
                $result = $db -> query($sql);
                $form_values = $db -> fetch_array($result,$nop);
            } else {
                $form_values = $HTTP_POST_VARS;
            }

            // wenn berechtigung nicht vorhanden , die
            if ( !in_array($form_values["abdststelle"],$HTTP_SESSION_VARS["dstzugriff"]) ) {
            #if ( $form_values["abdststelle"] != $HTTP_SESSION_VARS["custom"] ) {
                die(" Access denied, <br> your ip-adress has been logged");
            }

            // form otions holen
            $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

            // form elememte bauen
            $element = form_elements( $cfg["db"]["entries"], $form_values );

            // ip bindung private felder und bearbeitung, admin ueberschreibt
            // ***
            if ( $ip_class[1] != $form_values["abbnet"] || $ip_class[2] != $form_values["abcnet"] ) $sperre=true ;
            #if ( $rechte["administration"] == -1 ) $sperre = false;
            // +++
            // ip bindung private felder und bearbeitung, admin ueberschreibt


            // dropdown amtsbezeichnung aus db erstellen (wach 0304)
            // ***
            $sql = "SELECT abamtbez_id, abamtbezkurz FROM db_adrb_amtbez ORDER by abamtbez_sort";
            $result = $db -> query($sql);
            $formularobject[1] = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"abamtbez\">\n";
            $formularobject[1] .= "<option value=\"\">Bitte auswählen</option>\n";
            while ( $field = $db->fetch_row($result,$nop) ) {
                if ($form_values["abamtbez"] == $field[0]) {
                    $selected = " selected";
                } else {
                    $selected = "";
                }
                $formularobject[1] .= "<option value=\"".$field[0]."\"".$selected.">".$field[1] ."</option>\n";
            }
            $formularobject[1] .= "</select>\n";
            // +++
            // dropdown amtsbezeichnung aus db erstellen


            // dropdown dienstposten aus db erstellen (wach 0404)
            // ***
            $sql = "SELECT abdienst_id, abdienst FROM db_adrb_dienst ORDER by abdienst_sort";
            $result = $db -> query($sql);
            $formularobject[2] = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"abdstposten\">\n";
            $formularobject[2] .= "<option value=\"\">Bitte auswählen</option>\n";
            while ( $field = $db->fetch_row($result,$nop) ) {
                if ($form_values["abdstposten"] == $field[0]) {
                    $selected = " selected";
                } else {
                    $selected = "";
                }
                $formularobject[2] .= "<option value=\"".$field[0]."\"".$selected.">".$field[1] ."</option>\n";
            }
            $formularobject[2] .= "</select>\n";
            // +++
            // dropdown dienstposten aus db erstellen


            // dropdown interessen aus db erstellen (wach 1104)
            // ***
            $sql = "SELECT abint FROM db_adrb_int ORDER by abint_sort";
            $result = $db -> query($sql);
            $formularobject[3] = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"abinteressen[]\" size=\"5\" multiple>\n";
            $formularobject[3] .= "<option value=\"\">Bitte auswählen... (Mehrfach)</option>\n";

            if ( !is_array($form_values["abinteressen"]) ) {
                $form_values["abinteressen"] = explode(";", $form_values["abinteressen"]);
            }

            while ( $field = $db->fetch_row($result,$nop) ) {
                foreach ($form_values["abinteressen"] as $single_interest) {
                    if ($single_interest == $field[0]) {
                        $selected = " selected";
                        break;
                    } else {
                        $selected = "";
                    }
                }
                $formularobject[3] .= "<option value=\"".$field[0]."\"".$selected.">".$field[0] ."</option>\n";
            }
            $formularobject[3] .= "</select>\n";
            // +++
            // dropdown interessen aus db erstellen


            // feld kategorie und bfd aus db erstellen (wach 1704)
            // ***
            $sql = "SELECT adid, adkate, adststelle, adstbfd, adcnet FROM db_adrd WHERE adid=\"".$form_values["abdststelle"]."\"";
            $result = $db -> query($sql);
            $field = $db -> fetch_array($result,$nop);
            $ausgaben["abdststelle"] = $field["adkate"]." ".$field["adststelle"];
            $formularobject[4] = str_replace("abdststelle\"", "abdststelle\" value=\"".$field["adid"]."\"", $element["abdststelle"]);
            $ausgaben["abdstbfd"] = $field["adstbfd"];
            // +++
            // feld kategorie und bfd aus db erstellen


            // alle veraenderten elemente umbauen (wach 0404)
            // ***
            foreach($element as $name => $value) {
                if (strstr($name,"abpriv") && $sperre) {
                    $element[$name] = "---";
                } elseif ($name == "abamtbez") {
                    $element[$name] = $formularobject[1];
                } elseif ($name == "abdstposten") {
                    $element[$name] = $formularobject[2];
                } elseif ($name == "abinteressen") {
                    $element[$name] = $formularobject[3];
                } elseif ($name == "abdststelle") {
                    $element[$name] = $formularobject[4];
                }
            }
            // +++
            // alle veraenderten elemente umbauen

            // ip sperre b- und c-net kann nur vom admin bearbeitet werden (weam 0206)
            // ***

            if ( $rechte["sti"] == -1 ) {         ### loesung?
            #if ( $rechte["administration"] == -1 ) {
                $element["abbnet"] = str_replace("type=\"hidden\" ","",$element["abbnet"]);
                $element["abcnet"] = str_replace("type=\"hidden\" ","",$element["abcnet"]);
                $ausgaben["administration"] = "<b>Administration IP-Netz:</b> ".$element["abbnet"]." ".$element["abcnet"];
            } else {
                $ausgaben["administration"] = $element["abbnet"].$element["abcnet"];
            }
            // +++
            // b- und c-net kann nur vom admin bearbeitet werden (weam 0206)


            // interessen mit ";" trennen für Datenbankeintrag
            // ***
            if ( is_array($form_values["abinteressen"]) ) {
                $form_values["abinteressen"] = implode(";", $form_values["abinteressen"]);
            }
            // +++
            // interessen mit ";" trennen für Datenbankeintrag


            // referer im form mit hidden element mitschleppen
            if ( $HTTP_POST_VARS["form_referer"] == "" ) {
                $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
                $ausgaben["form_break"] = $ausgaben["form_referer"];
            } else {
                $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
                $ausgaben["form_break"] = $ausgaben["form_referer"];
            }


            // was anzeigen
            $mapping["main"] = crc32($environment["ebene"]).".modify";
            $mapping["navi"] = "leer";

            // ip sperre fuer anzeige
            #if ( $sperre == true ) {
                #$mapping["main"] = "default";
                #$ausgaben["output"] = "ZUGRIFF VERWEIGERT!";
            #}

            // wohin schicken
            $ausgaben["form_error"] = "";
            $ausgaben["form_aktion"] = $cfg["basis"]."/modify,edit,".$environment["parameter"][2].",verify.html";


            if ( $environment["parameter"][3] == "verify" ) {

                // pflichtfelder setzen bei anrede Herr oder Frau (wach 0705)
                // Datenbankeintrag (nicht required) wird ignoriert
                // ***
                if ( $form_values["abanrede"] == "Frau" || $form_values["abanrede"] == "Herr" ) {
                    $form_options["abnamvor"]["frequired"] = -1;
                    $form_options["abnamkurz"]["frequired"] = -1;
                    $form_options["abamtbez"]["frequired"] = -1;
                    $form_options["abdstposten"]["frequired"] = -1;
                    $form_options["abnamkurz"]["frequired"] = -1;
                    $form_options["abdstemail"]["frequired"] = -1;
                 } else {
                    $form_values["abamtbez"] = "";
                    $form_values["abnamkurz"] = "";
                    $form_values["abtitel"] = "";
                    $form_values["abnamvor"] = "";
                    $form_values["abad"] = 0;
                }
                // +++
                // pflichtfelder setzen bei anrede Herr oder Frau (wach 0705)

                // form eigaben prüfen
                #bugfix# form_errors( $form_options, $HTTP_POST_VARS );
                form_errors( $form_options, $form_values );

                //  namenskuerzel ist an dienststelle bereits vorhanden
                $sql = "SELECT abnamkurz, abdststelle FROM ".$cfg["db"]["entries"] ." WHERE abnamkurz='".$form_values["abnamkurz"]."' AND abdststelle='".$form_values["abdststelle"]."' AND abid<>'".$environment["parameter"][2]."'";
                $result = $db -> query($sql);
                $field = $db -> fetch_array($result,$nop);
                if ( $field["abnamkurz"] != "" )  {
                    $ausgaben["form_error"] = "Namenskürzel ist bereits an Ihrer Dienststelle vorhanden, bitte ändern.";
                }

                // dienststellennr. holen und ablogin zusammensetzen
                if ( $form_values["abnamkurz"] != "") {
                    $sql = "SELECT adakz FROM db_adrd WHERE adid='".$form_values["abdststelle"]."'";
                    $result = $db -> query($sql);
                    $field = $db -> fetch_array($result,$nop);
                    $form_values["ablogin"] = $form_values["abnamkurz"].$field["adakz"];
                }

                // ohne fehler sql bauen und ausfuehren
                if ( $ausgaben["form_error"] == "" /* && ( $HTTP_POST_VARS["submit"] != "" || $HTTP_POST_VARS["image"] != "" ) */ ){

                    $kick = array( "PHPSESSID", "submit", "image", "image_x", "image_y", "form_referer", "bnet", "cnet" );
                    #bugfix# foreach($HTTP_POST_VARS as $name => $value) {
                    foreach($form_values as $name => $value) {
                        if ( !in_array($name,$kick) ) {
                            if ( $sqla != "" ) $sqla .= ", ";
                            $sqla .= $name."='".$value."'";
                        }
                    }

                    // Sql um spezielle Felder erweitern
                    #$ldate = $HTTP_POST_VARS["ldate"];
                    #$ldate = substr($ldate,6,4)."-".substr($ldate,3,2)."-".substr($ldate,0,2)." ".substr($ldate,11,9);
                    #$sqla .= ", ldate='".$ldate."'";

                    $sql = "update ".$cfg["db"]["entries"]." SET ".$sqla." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][2]."'";
                    $result  = $db -> query($sql);
                    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                    header("Location: ".$ausgaben["form_referer"]);
                    #header("Location: ".$environment["basis"]."/list.html");
                }

            }



        } elseif ( $environment["parameter"][1] == "delete" && $rechte[$cfg["right"]["adress"]] == -1 ) {
            $ausgaben["form_error"] = "";

            $sql = "SELECT * FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][2]."'";
            $result = $db -> query($sql);
            $field = $db -> fetch_array($result,$nop);

            // wenn berechtigung nicht vorhanden , die
            if ( $HTTP_SESSION_VARS["custom"] != $field["abdststelle"]  ) {
                die(" Access denied, <br> your ip-adress has been logged");
            }

            // Amtsbezeichnung des Beschäftigten holen (2603)
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
            while ( $data = $db->fetch_row($result,$nop) ) {
                foreach (explode(";",$field["abinteressen"]) as $single_interest) {
                    if ($single_interest == $data[0]) {
                        if ( $interessen != "" ) $interessen .= ", ";
                        $interessen .= $data[0];
                    }
                }
            }
            // +++
            // Interessen des Beschäftigten holen

            /*
            // Dienststelle und BFD holen (wach 0404)
            // ***
            $sql = "SELECT adid, adkate, adststelle, adstbfd, adcnet FROM db_adrd WHERE adid='".$field["abdststelle"]."'";
            $result = $db -> query($sql);
            $dststelle = $db -> fetch_array($result,$nop);
            $ausgaben["abdstbfd"] = $dststelle["adstbfd"];
            // +++
            // Dienststelle und BFD holen
            */

            // kategorie und dienststelle holen (weam 2005)
            // ***
            $sql = "SELECT adkate, adststelle FROM db_adrd WHERE adid='".$field["abdststelle"]."'";
            $result = $db -> query($sql);
            $dststelle = $db -> fetch_array($result,$nop);
            // +++
            // kategorie und dienststelle holen (weam 2005)


            // bei allen nicht va bfd und abteilung holen (weam 2005)
            // ***
            if ( $dststelle["adkate"] != "VA" ) {
                $sql = "SELECT adstbfd, adstabt FROM db_adrd WHERE adbnet='".$field["abbnet"]."' AND adkate='BFD'";
                $result = $db -> query($sql);
                $data = $db -> fetch_array($result,$nop);
                $ausgaben["abdstbfd"] = $data["adstbfd"];
                $ausgaben["adstabt"] = $data["adstabt"];
            } else {
                $ausgaben["abdstbfd"] = "--";
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
                if (strstr($key,"abpriv") && $sperre) $value = "---";
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
                    $ausgaben["namen"] = $field[$value[1]]." ".$field[$value[0]];;
                }

            }
            // +++
            // Bauen des Register-Kopfes (mor 1905)
            #echo "<pre>";
            #print_r($HTTP_POST_VARS);
            #echo "</pre>";

            if ( $HTTP_POST_VARS["delete"] == "true" ) {
                $sql = "SELECT adkate, adststelle, adleiter, adwebmid1, adwebmid2 FROM db_adrd WHERE adwebmid1='".$environment["parameter"][2]."' OR adwebmid2='".$environment["parameter"][2]."' OR adleiter='".$environment["parameter"][2]."'";
                $result = $db -> query($sql);
                if ( $db->num_rows($result) == 0 ) {
                    $sql = "DELETE FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][2]."'";
                    $result  = $db -> query($sql);

                    // rechte löschen
                    // ***
                    $sql = "DELETE FROM auth_right where uid='".$environment["parameter"][2]."'";
                    $db -> query($sql);
                    // +++
                    // rechte löschen

                    header("Location: ".$cfg["basis"]."/list.html");
                } else {
                    $data = $db -> fetch_array($result,$nop);
                        $ausgaben["form_error"] .= "Löschen nicht möglich<br>";
                        if ($data["adleiter"] ==  $environment["parameter"][2]) $ausgaben["form_error"] .= "Amtsleiter<br>";
                        if ($data["adwebmid1"] ==  $environment["parameter"][2]) $ausgaben["form_error"] .= "Redakteur 1<br>";
                        if ($data["adwebmid2"] ==  $environment["parameter"][2]) $ausgaben["form_error"] .= "Redakteur 2<br>";
                        $ausgaben["form_error"] .= $data["adkate"]." ".$data["adststelle"];
                }
            }

            // referer im form mit hidden element mitschleppen
            if ( $HTTP_POST_VARS["form_referer"] == "" ) {
                $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
                $ausgaben["form_break"] = $ausgaben["form_referer"];
            } else {
                $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
                $ausgaben["form_break"] = $ausgaben["form_referer"];
            }

            // was anzeigen
            $mapping["main"] = crc32($environment["ebene"]).".delete";
            $mapping["navi"] = "leer";

            // wohin schicken
            $ausgaben["form_aktion"] = $cfg["basis"]."/modify,delete,".$environment["parameter"][2].".html";
            #$ausgaben["form_break"] = $ausgaben["form_referer"]; // Nun mit Post im hidden element (mor 0605)

        } else {
            header("Location: ".$pathvars["webroot"]."/".$environment["design"]."/".$environment["language"]."/index.html");
            #$ausgaben["output"] .= "ZUGRIFF VERWEIGERT";
        }
     }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
