<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id$";
// "Dienststellen modify";
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
    if ( strstr($environment["kategorie"], "modify") ) {

        // warning ausgeben
        if ( get_cfg_var('register_globals') == 1 ) $debugging["ausgabe"] .= "Warning register_globals in der php.ini steht auf on, evtl werden interne Variablen ueberschrieben!".$debugging["char"];

        if ( $environment["parameter"][1] == "add" && $rechte[$cfg["right"]["adress"]] == -1 ) {

            $form_values = $HTTP_POST_VARS;

            // form options holen
            $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

            // form elememte bauen
            $element = form_elements( $cfg["db"]["entries"], $form_values );


            // dropdown Kategorie erstellen (schae 1504)
            // ***
            $sql = "SELECT DISTINCT adkate FROM db_adrd ORDER BY adkate";
            $result = $db -> query($sql);
            $formularobject[1]  = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"adkate\">\n";
            $formularobject[1] .= "<option value=\"\">Bitte auswählen</option>\n";
            while ( $data = $db->fetch_array($result,$nop) ) {
                if ($form_values["adkate"] == $data["adkate"]) {
                    $selected = " selected";
                } else {
                    $selected = "";
                }
                $formularobject[1] .= "<option value=\"".$data["adkate"]."\"".$selected.">" .$data["adkate"] ."</option>\n";
            }
            $formularobject[1] .= "</select>";
            // +++
            // dropdown Kateogrie erstellen (schae 1504)


            // dropdown Dienststelle erstellen (schae 1504)
            // ***
            $sql = "SELECT DISTINCT adststelle FROM db_adrd ORDER BY adststelle";
            $result = $db -> query($sql);
            $formularobject[2]  = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"adststelle\">\n";
            $formularobject[2] .= "<option value=\"\">Bitte auswählen</option>\n";
            while ( $data = $db->fetch_array($result,$nop) ) {
                if ($form_values["adststelle"] == $data["adststelle"]) {
                    $selected = " selected";
                } else {
                    $selected = "";
                }
                $formularobject[2] .= "<option value=\"".$data["adststelle"]."\"".$selected.">" .$data["adststelle"] ."</option>\n";
            }
            $formularobject[2] .= "</select>";
            // +++
            // dropdown Dienststelle erstellen (schae 1504)


            // dropdown BFD erstellen (schae 250403)
            // ***
            $sql = "SELECT DISTINCT adstbfd FROM db_adrd ORDER BY adstbfd";
            $result = $db -> query($sql);
            $formularobject[3]  = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"adstbfd\">\n";
            $formularobject[3] .= "<option value=\"\">Bitte auswählen</option>\n";
            while ( $data = $db->fetch_array($result,$nop) ) {
                if ($form_values["adstbfd"] == $data["adstbfd"]) {
                    $selected = " selected";
                } else {
                    $selected = "";
                }
                $formularobject[3] .= "<option value=\"".$data["adstbfd"]."\"".$selected.">" .$data["adstbfd"] ."</option>\n";
            }
            $formularobject[3] .= "</select>";
            // +++
            // dropdown BFD erstellen (schae 250403)



            // sql dropdown personen
            $sql = "SELECT * FROM db_adrb WHERE abbnet='".$ip_class[1]."' AND abcnet= '".$ip_class[2]."' AND abanrede != 'Raum' ORDER BY abnamra";


            // dropdown Amtsleiter erstellen (schae 230403)
            // ***
            // sql siehe oben
            $result = $db -> query($sql);
            $formularobject[4]  = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"adleiter\">\n";
            $formularobject[4] .= "<option value=\"\">Bitte auswählen</option>\n";
            while ( $data = $db->fetch_array($result,$nop) ) {
                if ($form_values["adleiter"] == $data["abid"]) {
                    $selected = " selected";
                } else {
                    $selected = "";
                }
                $formularobject[4] .= "<option value=\"".$data["abid"]."\"".$selected.">" .$data["abnamra"]." ".$data["abnamvor"] ."</option>\n";
            }
            $formularobject[4] .= "</select>";
            // +++
            // dropdown Amtsleiter erstellen (schae 230403)


            // dropdown Webmaster1 erstellen (schae 250403)
            // ***
            #$sql = "SELECT * FROM db_adrb WHERE abbnet='".$ip_class[1]."' AND abcnet= '".$ip_class[2]."' ORDER BY abnamra";
            #$result = $db -> query($sql);
            // sql siehe oben
            $result = $db -> query($sql);
            $formularobject[5]  = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"adwebmid1\">\n";
            $formularobject[5] .= "<option value=\"\">Bitte auswählen</option>\n";
            while ( $data = $db->fetch_array($result,$nop) ) {
                if ($form_values["adwebmid1"] == $data["abid"]) {
                    $selected = " selected";
                } else {
                    $selected = "";
                }
                $formularobject[5] .= "<option value=\"".$data["abid"]."\"".$selected.">" .$data["abnamra"]." ".$data["abnamvor"] ."</option>\n";
            }

            $formularobject[5] .= "</select>";
            // +++
            // dropdown Webmaster1 erstellen (schae 250403)


            // dropdown Webmaster2 erstellen (schae 020503)
            // ***
            #$sql = "SELECT * FROM db_adrb WHERE abbnet='".$ip_class[1]."' AND abcnet= '".$ip_class[2]."' ORDER BY abnamra";
            #$result = $db -> query($sql);
            // sql siehe oben
            $result = $db -> query($sql);
            $formularobject[6]  = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"adwebmid2\">\n";
            $formularobject[6] .= "<option value=\"\">Bitte auswählen</option>\n";
            while ( $data = $db->fetch_array($result,$nop) ) {
                if ($form_values["adwebmid2"] == $data["abid"]) {
                    $selected = " selected";
                } else {
                    $selected = "";
                }
                $formularobject[6] .= "<option value=\"".$data["abid"]."\"".$selected.">" .$data["abnamra"]." ".$data["abnamvor"] ."</option>\n";
            }
            $formularobject[6] .= "</select>";
            // +++
            // dropdown Webmaster2 erstellen (schae 020503)


            // alle veraenderten elemente umbauen weam 1205
            // ***
            foreach( $element as $key => $value) {
                if ( $key == "adkate") {
                    $element[$key] = $formularobject[1];
                } elseif ( $key == "adststelle") {
                    $element[$key] = $formularobject[2];
                } elseif ( $key == "adstbfd") {
                    $element[$key] = $formularobject[3];
                } elseif ( $key == "adleiter") {
                    $element[$key] = $formularobject[4];
                } elseif ( $key == "adwebmid1" ) {
                    $element[$key] = $formularobject[5];
                } elseif ( $key == "adwebmid2" ) {
                    $element[$key] = $formularobject[6];
                }
            }
            // +++
            // alle veraenderten elemente umbauen weam 1205


            // form elemente vorbelegen weam 1305
            // ***
            $sql = "SELECT adtelver, adfax, adinternet, ademail, adbnet, adcnet FROM db_adrd";
            $result = $db -> query($sql);
            $modify  = array ( "adtelver"   => "+49-",
                               "adfax"      => "+49-",
                               "adinternet" => "www.geodatenonline.de",
                               "ademail"    => "poststelle@.bayern.de",
                               "adbnet"     => $ip_class[1],
                               "adcnet"     => $ip_class[2]
                             );
            foreach($modify as $key => $value) {
                if ( $HTTP_POST_VARS[$value] == "" ) {
                    $element[$key] = str_replace($key."\"", $key."\" value=\"".$value."\"", $element[$key]);
                }
            }
            // +++
            // form elemente vorbelegen weam 1305


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

                // form eigaben prüfen
                form_errors( $form_options, $form_values );

                // ohne fehler sql bauen und ausfuehren
                if ( $ausgaben["form_error"] == "" && ( $HTTP_POST_VARS["submit"] != "" || $HTTP_POST_VARS["image"] != "" ) ) {
                    $kick = array( "PHPSESSID", "submit", "image", "image_x", "image_y", "form_referer" );
                    foreach( $form_values as $name => $value) {
                        if ( !in_array($name,$kick) ) {
                            if ( $sqla != "" ) $sqla .= ",";
                            $sqla .= " ".$name;
                            if ( $sqlb != "" ) $sqlb .= ",";
                            $sqlb .= " '".$value."'";
                        }
                    }

                    // Sql um spezielle Felder erweitern
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

            #// wenn berechtigung nicht vorhanden , die
            #if ( !in_array($form_values["adid"],$HTTP_SESSION_VARS["dstzugriff"]) ) {
                #die(" Access denied, <br> your ip-adress has been logged");
            #}

            // form otions holen
            $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

            // form elememte bauen
            $element = form_elements( $cfg["db"]["entries"], $form_values );

            // keine Editierung von B- und C-Netz (schae 050503)
            // ***
            $ausgaben["adbnet"] = $form_values["adbnet"];
            $ausgaben["adcnet"] = $form_values["adcnet"];
            // +++
            // keine Editierung  B- und C-Netz (schae 050503)


            // dropdown Amtsleiter erstellen (schae 240403)
            // ***
            $sql = "SELECT * FROM db_adrb WHERE abdststelle ='".$environment["parameter"][2]."'ORDER BY abnamra";
            $result = $db -> query($sql);
            $formularobject[1]  = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"adleiter\">\n";
            $formularobject[1] .= "<option value=\"\">Bitte auswählen</option>\n";
            while ( $data = $db->fetch_array($result,$nop) ) {
                if ($form_values["adleiter"] == $data["abid"]) {
                    $selected = " selected";
                } else {
                    $selected = "";
                }
                $formularobject[1] .= "<option value=\"".$data["abid"]."\"".$selected.">" .$data["abnamra"]." ".$data["abnamvor"] ."</option>\n";
            }
            $formularobject[1] .= "</select>";
            // +++
            // dropdown Amtsleiter erstellen (schae 240403)


            // dropdown Webmaster1 erstellen (schae 250403)
            // ***
            $sql = "SELECT * FROM db_adrb WHERE abdststelle='".$environment["parameter"][2]."' ORDER BY abnamra";
            $result = $db -> query($sql);
            $formularobject[2]  = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"adwebmid1\">\n";
            $formularobject[2] .= "<option value=\"\">Bitte auswählen</option>\n";
            while ( $data = $db->fetch_array($result,$nop) ) {
                if ($form_values["adwebmid1"] == $data["abid"]) {
                    $selected = " selected";
                } else {
                    $selected = "";
                }
                $formularobject[2] .= "<option value=\"".$data["abid"]."\"".$selected.">" .$data["abnamra"]." ".$data["abnamvor"] ."</option>\n";
            }
            $formularobject[2] .= "</select>";
            // +++
            // dropdown Webmaster1 erstellen (schae 250403)


            // dropdown Webmaster2 erstellen (schae 020503)
            // ***
            $sql = "SELECT * FROM db_adrb WHERE abdststelle='".$environment["parameter"][2]."' ORDER BY abnamra";
            $result = $db -> query($sql);
            $formularobject[3]  = "<select class=\"".$form_defaults["class"]["dropdown"]."\" name=\"adwebmid2\">\n";
            $formularobject[3] .= "<option value=\"\">Bitte auswählen</option>\n";
            while ( $data = $db->fetch_array($result,$nop) ) {
                if ($form_values["adwebmid2"] == $data["abid"]) {
                    $selected = " selected";
                } else {
                    $selected = "";
                }
                $formularobject[3] .= "<option value=\"".$data["abid"]."\"".$selected.">" .$data["abnamra"]." ".$data["abnamvor"] ."</option>\n";
            }
            $formularobject[3] .= "</select>";
            // +++
            // dropdown Webmaster2 erstellen (schae 020503)


            // alle veraenderten elemente umbauen weam 1205
            // ***
            foreach( $element as $key => $value) {
                if ( $key == "adleiter") {
                    $element[$key] = $formularobject[1];
                } elseif ( $key == "adwebmid1" ) {
                    $element[$key] = $formularobject[2];
                } elseif ( $key == "adwebmid2" ) {
                    $element[$key] = $formularobject[3];
                }
            }
            // +++
            // alle veraenderten elemente umbauen weam 1205


            // was anzeigen
            $mapping["main"] = crc32($environment["ebene"]).".modify";
            $mapping["navi"] = "leer";

            // wohin schicken
            $ausgaben["form_error"] = "";
            $ausgaben["form_aktion"] = $environment["basis"]."/modify,edit,".$environment["parameter"][2].",verify.html";

            // referer im form mit hidden element mitschleppen
            if ( $HTTP_POST_VARS["form_referer"] == "" ) {
                $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
                $ausgaben["form_break"] = $ausgaben["form_referer"];
            } else {
                $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
                $ausgaben["form_break"] = $ausgaben["form_referer"];
            }
            if ( $environment["parameter"][3] == "verify" ) {

                // form eigaben prüfen
                // #bugfix#form_errors( $form_options, $HTTP_POST_VARS );  (schae 250403)
                form_errors( $form_options, $form_values );

                // ohne fehler sql bauen und ausfuehren
                if ( $ausgaben["form_error"] == "" && ( $HTTP_POST_VARS["submit"] != "" || $HTTP_POST_VARS["image"] != "" ) ){
                    $kick = array( "PHPSESSID", "submit", "image", "image_x", "image_y", "form_referer" );

                    //#bugfix# foreach ($HTTP_POST_VARS as $name => $value ) { (schae 250403)
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

            $sql = "SELECT * FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][2]."'";
            $result = $db -> query($sql);
            $field = $db -> fetch_array($result,$nop);
            foreach($field as $name => $value) {
                $ausgaben[$name] = $value;
            }

            // amtsleiter daten holen und variablen bauen weam 1205
            // ***
            $sql = "SELECT abtitel,
                           abnamvor,
                           abnamra,
                           abamtbezlang as abamtbez,
                           abdsttel,
                           abdstfax,
                           abdstfax,
                           abdstmobil,
                           abdstemail
                    FROM db_adrb INNER JOIN db_adrb_amtbez ON (abamtbez = abamtbez_id)
                    WHERE abid='".$ausgaben["adleiter"]."'" ;
            $result = $db -> query($sql);
            $data = $db -> fetch_array($result,$nop);
            if ( is_array($data) ){
                foreach($data as $key => $value) {
                    $ausgaben[$key] = $value;
                }
            } else {
                $ausgaben["abtitel"] = "--";
                $ausgaben["abnamvor"] = "";
                $ausgaben["abnamra"] = "";
                $ausgaben["abamtbez"] = "--";
                $ausgaben["abdsttel"] = "--";
                $ausgaben["abdstfax"] = "--";
                $ausgaben["abdstmobil"] = "--";
                $ausgaben["abdstemail"] = "--";
            }
            // +++
            // amtsleiter daten holen und variablen bauen weam 1205

            // redaktion daten holen und variablen bauen weam 1205
            // ***
            for ( $i = 1; $i <= 2; $i++ ) {
                $sql = "SELECT abnamra as adwebname,
                               abnamvor as adwebvorname,
                               abdsttel as adwebtel,
                               abdstemail as adwebemail
                        FROM db_adrb where abid='".$ausgaben["adwebmid".$i]."'" ;
                $result = $db -> query($sql);
                $data = $db -> fetch_array($result,$nop);
                if ( is_array($data) ){
                   foreach($data as $key => $value) {
                        $ausgaben[$key.$i] = $value;
                   }
                } else {
                    $ausgaben["adwebname".$i] = "--";
                    $ausgaben["adwebvorname".$i] = "--";
                    $ausgaben["adwebtel".$i] = "--";
                    $ausgaben["adwebemail".$i] = "--";
                }
            }
            // +++
            // redaktion daten holen und variablen bauen weam 1205


            // was anzeigen
            $mapping["main"] = crc32($environment["ebene"]).".delete";
            $mapping["navi"] = "leer";

            // wohin schicken
            $ausgaben["form_aktion"] = $environment["basis"]."/modify,delete,".$environment["parameter"][2].".html";
            $ausgaben["form_break"] = $_SERVER["HTTP_REFERER"];

            if ( $HTTP_POST_VARS["delete"] == "true" ) {
                $sql = "DELETE FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$environment["parameter"][2]."'";
                $result  = $db -> query($sql);
                header("Location: ".$environment["basis"]."/list.html");
            }
        #}
        // wa 1707
        } else {
            header("Location: ".$pathvars["webroot"]."/".$environment["design"]."/".$environment["language"]."/index.html");
        }

    }


////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
