<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  "$Id$";
//  "autoform - list";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2010 Werner Ammon ( wa<at>chaos.de )

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

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** ".$script["name"]." ** ]".$debugging["char"];

    if ( $cfg["autoform"]["right"] == "" || priv_check($environment["ebene"]."/".$environment["kategorie"],$cfg["autoform"]["right"]) ){

        ////////////////////////////////////////////////////////////////////
        // achtung: bei globalen funktionen, variablen nicht zuruecksetzen!
        // z.B. $ausgaben["form_error"],$ausgaben["inaccessible"]
        ////////////////////////////////////////////////////////////////////

        // page basics
        // ***

        if ( $_GET["eintragen"] ) {
            $preg = "^(-)?([0-9])*$";
            if ( preg_match("/$preg/",$_GET["eintragen"],$regs) ) {
                $sql = "SELECT * FROM ".$cfg["autoform"]["location"][$environment["ebene"]]["db"]." WHERE crc='".$regs[0]."' AND confirm !='-1'";
                $result = $db -> query($sql);
                if ( $db -> num_rows($result) > 0 ) {
                    $sql = "UPDATE ".$cfg["autoform"]["location"][$environment["ebene"]]["db"]." SET confirm='-1' WHERE crc='".$regs[0]."' AND confirm !='-1'";
                    $result = $db -> query($sql);
                    $hidedata["confirm_yes"]["enable"] = -1;
                    exit;
                } else {
                    $hidedata["confirm_no"]["enable"] = -1;
                    exit;
                }
            } else {
                $hidedata["confirm_yes"]["no"] = -1;
                exit;
            }
        }

        // art des forms
        switch($cfg["autoform"]["location"][$environment["ebene"]]["art"]) {
            case "mailsave":
                $db_entry = -1;
                $mail_order = -1;
                break;
            case "mail":
                $mail_order = -1;
                break;
            case "save":
                $db_entry = -1;
                break;
            case "confirm":
                $db_entry = -1;
                $mail_order = -1;
                $confirm = -1;
                break;
            default:
                $ausgaben["form_error"] = "Art wählen";
        }

        // schutz vor automatischen pushen
        if ( $_POST["send"] != "" && $_SERVER["HTTP_REFERER"] == "" ) {
            header("Location: ".$pathvars["virtual"]."/");
        }

        $ausgaben["form_error"] = "";

        // warnung ausgeben
        if ( get_cfg_var('register_globals') == 1 ) $debugging["ausgabe"] .= "Warnung: register_globals in der php.ini steht auf on, evtl werden interne Variablen ueberschrieben!".$debugging["char"];

        // path fuer die schaltflaechen anpassen
        if ( $cfg["autoform"]["iconpath"] == "" ) $cfg["autoform"]["iconpath"] = "/images/default/";

        // label bearbeitung aktivieren
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $specialvars["editlock"] = 0;
        } else {
            $specialvars["editlock"] = -1;
        }


        // form options holen
        $form_options = form_options(eCRC($environment["ebene"]).".".$environment["kategorie"]);

        // form elememte bauen
        $element = form_elements( $cfg["autoform"]["location"][$environment["ebene"]]["db"], $_POST );

        // +++
        // page basics


        // funktions bereich
        // ***


        // captcha - bild erzeugen
        if ( $cfg["autoform"]["location"][$environment["ebene"]]["captcha"] ) {
            // zufaellige zeichen erzeugen
            $captcha_text = captcha_randomize($cfg["autoform"]["captcha"]["length"],$cfg["autoform"]["captcha"]);
            // bild erzeugen
            captcha_create($captcha_text,$cfg["autoform"]["captcha"]);
            // captcha-info erzeugen
            $captcha_crc = crc32($captcha_text.$cfg["autoform"]["captcha"]["randomize"]);
            $captcha_name = "captcha-".$captcha_crc.".png";
            $captcha_path_web = $cfg["file"]["base"]["webdir"].$cfg["file"]["base"]["new"];
            $captcha_path_srv = $cfg["file"]["base"]["maindir"].$cfg["file"]["base"]["new"];
            // ausgeben
            $hidedata["captcha"]["url"] = $captcha_path_web.$captcha_name;
            $hidedata["captcha"]["proof"] = $captcha_crc;
            // alte, unnuetze bilder entfernen
            foreach ( glob($captcha_path_srv."captcha-*.png") as $captcha_file) {
                if ( (mktime() - filemtime($captcha_file)) > 600 ) unlink($captcha_file);
            }
        }

        // bereich im template sichtbar machen
        $hidedata[$cfg["autoform"]["location"][$environment["ebene"]]["db"]][0] = "enable";
        $hidedata["form"][0] = "enable";

        // "form referer"
        if ( $_POST["last_viewed"] != "" ) {
            $ausgaben["last_viewed"] = $_POST["last_viewed"];
        } else {
            $ausgaben["last_viewed"] = $_SERVER["HTTP_REFERER"];
        }

        // +++
        // funktions bereich


        // page basics
        // ***

        if ( $environment["parameter"][1] == "verify" && $_POST["send"] != "" ) {

            // form eigaben pruefen
            form_errors( $form_options, $_POST );

            // hier wird die captcha-eingabe geprueft
            if ( $cfg["autoform"]["location"][$environment["ebene"]]["captcha"] ) {
                if ( $_POST["captcha_proof"] != crc32($_POST["captcha"].$cfg["autoform"]["captcha"]["randomize"])
                  || !file_exists($captcha_path_srv."captcha-".$_POST["captcha_proof"].".png") ) {

                    $ausgaben["form_error"] .= "#(error_captcha)";
                    $dataloop["form_error"]["captcha"]["text"] = "#(error_captcha)";
                    $hidedata["captcha"]["class"] = "form_error";
                }
                if (file_exists($captcha_path_srv."captcha-".$_POST["captcha_proof"].".png")) unlink($captcha_path_srv."captcha-".$_POST["captcha_proof"].".png");
            }

            // hier erfolgt der mail-versand bzw db-eintrag
            if ( $ausgaben["form_error"] == ""  ) {
                if ( $mail_order == -1 ) {
                    if ( $confirm == -1 ) {
                        ( $cfg["autoform"]["captcha"]["letter_pot"] ) ? $put = $cfg["autoform"]["captcha"] : $put["letter_pot"] = "abcde";
                        $hazard = crc32(captcha_randomize("43",$put));
                        $bestaetigungslink = "http://".$_SERVER["HTTP_HOST"].$environment["ebene"].".html?eintragen=".$hazard;
                        $message = parser($cfg["autoform"]["location"][$environment["ebene"]]["email"]["confirm_template"],"");
                        mail($_POST[$cfg["autoform"]["location"][$environment["ebene"]]["email"]["form_email_feld"]],"Confirm",$message);
                    } else {
                        foreach ( $_POST as $key => $value ) {
                            $$key = $value;
                        }
                        $message1 = parser($cfg["autoform"]["location"][$environment["ebene"]]["email"]["template1"],"");
                        $message2 = parser($cfg["autoform"]["location"][$environment["ebene"]]["email"]["template2"],"");
                        mail_order($_POST,$cfg["autoform"]["location"][$environment["ebene"]]["email"]);
                    }
                }
                if ( $db_entry == -1 ) {
                    $kick = array( "PHPSESSID", "form_referer", "send", "last_viewed","captcha","captcha_proof" );
                    foreach($_POST as $name => $value) {
                        if ( !in_array($name,$kick) && !strstr($name, ")" ) ) {
                            // posts absichern
                            if ( !get_magic_quotes_gpc() ) {
                                $value = addslashes($value);
                            }
                            if ( $sqla != "" ) $sqla .= ", ";
                            if ( $sqlb != "" ) $sqlb .= ", ";
                            $sqla .= " `".$name."`";
                            $sqlb .= "'".$value."'";
                        }
                    }
                    if ( $confirm == -1 ) {
                            $sqla .= ", `crc`";
                            $sqlb .= ", '".$hazard."'";
                    }
                    $sql = "INSERT INTO ".$cfg["autoform"]["location"][$environment["ebene"]]["db"]." (".$sqla.") VALUES (".$sqlb.")";
                    $result  = $db -> query($sql);
                }

                // wenn es keine fehlermeldungen gab, die uri $header laden
                unset($hidedata[$cfg["autoform"]["location"][$environment["ebene"]]["db"]]);
                unset($hidedata["form"]);
                unset($hidedata["captcha"]);
                $hidedata["success"] = array();
                if ( $_POST["last_viewed"] != "" ) {
                    $hidedata["success"]["link"] = $_POST["last_viewed"];
                } else {
                    $hidedata["success"]["link"] = $environment["ebene"];
                }
            }   
        }

        // was anzeigen
        $mapping["main"] = "autoform";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error1) #(error1)<br />";
            $ausgaben["inaccessible"] .= "# (success) #(success)<br />";
            $ausgaben["inaccessible"] .= "# (confirm_yes) #(confirm_yes)<br />";
            $ausgaben["inaccessible"] .= "# (confirm_no) #(confirm_no)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // navigation erstellen
        $ausgaben["form_aktion"] = $environment["ebene"]."/list,verify.html";
        $ausgaben["form_break"] = "index.html";

        // +++
        // page basics

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
