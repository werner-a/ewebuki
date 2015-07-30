<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  "$Id$";
//  "kontakt form";
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

    86343 K�nigsbrunn

    URL: http://www.chaos.de
*/
////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ( $rechte[$cfg["kontakt"]["right"]] == "" || $rechte[$cfg["kontakt"]["right"]] == -1 ) {

        // page basics
        // ***

        #if ( count($_POST) == 0 ) {
        #} else {
            $form_values = $_POST;
        #}

        // form options holen
        $form_options = form_options(eCRC($environment["ebene"]).".".$environment["kategorie"]);

        // form elememte bauen
        $element = form_elements( $cfg["kontakt"]["db"]["entries"], $form_values );


        $hidedata["form"] = array();

        // +++
        // page basics

        // funktions bereich fuer erweiterungen
        // ***

        if ( is_array($cfg["kontakt"]["captcha"]) ) {
            $captcha_delete_time = 600;
            if ( $cfg["kontakt"]["captcha"]["delete_time"] ) {
                $captcha_delete_time = $cfg["kontakt"]["captcha"]["delete_time"];
            }
            // zufaellige zeichen erzeugen
            $captcha_text = captcha_randomize($cfg["kontakt"]["captcha"]["length"],$cfg["kontakt"]["captcha"]);
            // bild erzeugen
            captcha_create($captcha_text,$cfg["kontakt"]["captcha"]);
            // captcha-info erzeugen
            $captcha_crc = crc32($captcha_text.$cfg["kontakt"]["captcha"]["randomize"]);
            $captcha_name = "captcha-".$captcha_crc.".png";
            $captcha_path_web = $cfg["file"]["base"]["webdir"].$cfg["file"]["base"]["new"];
            $captcha_path_srv = $cfg["file"]["base"]["maindir"].$cfg["file"]["base"]["new"];
            // ausgeben
            $hidedata["captcha"]["url"] = $captcha_path_web.$captcha_name;
            $hidedata["captcha"]["proof"] = $captcha_crc;
            // alte, unnuetze bilder entfernen
            foreach ( glob($captcha_path_srv."captcha-*.png") as $captcha_file) {
                if ( (time() - filemtime($captcha_file)) > $captcha_delete_time ) unlink($captcha_file);
            }
        }

        // +++
        // funktions bereich fuer erweiterungen

        // page basics
        // ***

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["kontakt"]["basis"].",".$environment["parameter"][1].",verify.html";
        $ausgaben["form_break"] = "index.html";

        // hidden values
        $ausgaben["form_hidden"] .= "";

        // "form referer"
        if ( $_POST["last_viewed"] != "" ) {
            $ausgaben["last_viewed"] = htmlentities($_POST["last_viewed"]);
        } else {
            $ausgaben["last_viewed"] = $_SERVER["HTTP_REFERER"];
        }

        // was anzeigen
        #$mapping["main"] = eCRC($environment["ebene"]).".modify";
        #$mapping["navi"] = "leer";
        if ( $environment["parameter"]["2"] == "sent" ) {
            unset($hidedata["form"]);
            unset($hidedata["captcha"]);
            $hidedata["success"] = array();
            if ( $_GET["referer"] != "" && preg_match("/^http:\/\/[A-Za-z_\-\.0-9\/]+$/",$_GET["referer"]) ) {
                $hidedata["referer"]["link"] = htmlentities($_GET["referer"]);
                $ausgaben["last_viewed"] = "";
        }
        }

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($_GET["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_result) #(error_result)<br />";
            $ausgaben["inaccessible"] .= "# (error_captcha) #(error_captcha)<br />";
            $ausgaben["inaccessible"] .= "# (error_dupe) #(error_dupe)<br />";
            $ausgaben["inaccessible"] .= "# (success) #(success)<br />";
            $ausgaben["inaccessible"] .= "# (referer) #(referer)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        #n/a

        // +++
        // page basics

        if ( $environment["parameter"][2] == "verify"
            &&  ( $_POST["send"] != ""
                || $_POST["extension1"] != ""
                || $_POST["extension2"] != "" ) ) {

            // form eigaben pruefen
            form_errors( $form_options, $_POST );

            if ( is_array($cfg["kontakt"]["captcha"]) ) {
                if ( $_POST["captcha_proof"] != crc32($_POST["captcha"].$cfg["kontakt"]["captcha"]["randomize"])
                  || !file_exists($captcha_path_srv."captcha-".$_POST["captcha_proof"].".png") ) {
                    $ausgaben["form_error"] .= "#(error_captcha)";
                    $dataloop["form_error"]["captcha"]["text"] = "#(error_captcha)";
                    $hidedata["captcha"]["class"] = "form_error";
                    $hidedata["form_error"] = array();
                }
                if (file_exists($captcha_path_srv."captcha-".$_POST["captcha_proof"].".png")) unlink($captcha_path_srv."captcha-".$_POST["captcha_proof"].".png");
            }

            // evtl. zusaetzliche datensatz anlegen
            if ( $ausgaben["form_error"] == ""  ) {

                // kunde
                if ( $_POST[$cfg["kontakt"]["email"]["form_email_feld"]] == "" ) {
                    $email_adresse = $cfg["kontakt"]["email"]["robot"];
                } else {
                    $email_adresse = str_replace(",","",$_POST[$cfg["kontakt"]["email"]["form_name_feld"]])." <".$_POST[$cfg["kontakt"]["email"]["form_email_feld"]].">";
                }

                foreach ( $_POST as $key => $value ) {
                   $$key = $value;
                }

                if ( $_POST[$cfg["kontakt"]["email"]["form_email_feld"]] != "" ) {
                    $secure = crc32($_POST["captcha_proof"].$_POST["email"]);
                }

                $message1 = parser($cfg["kontakt"]["email"]["template1"],"");
                $message2 = parser($cfg["kontakt"]["email"]["template2"],"");

                // happy bouncing
                #ini_set("sendmail_from",$cfg["kontakt"]["email"]["robot"]);
                #if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sendmail_from = ".ini_get('sendmail_from').$debugging["char

                // mail an betreiber
                $subject1 = $cfg["kontakt"]["email"]["subj1"];
                if ( is_array($cfg["kontakt"]["email"]["repl1"]) ) {
                    foreach ( $cfg["kontakt"]["email"]["repl1"] as $value ) {
                        $subject1 = str_replace("!{".$value."}",$$value,$subject1);
                    }
                }

                if ( $_POST["betreff"] != "" ) $subject1 .= ": ".$_POST["betreff"];
                $header1  = "From: ".$cfg["kontakt"]["email"]["robot"]."\r\n";
                $header1 .= "Reply-To: ".$email_adresse."\r\n";
                if ( $cfg["kontakt"]["email"]["encoding"] != "" ) {
                    $header1 .= "Content-Type: text/plain; charset=".$cfg["kontakt"]["email"]["encoding"]."\r\n";
                }
                if ( $cfg["kontakt"]["email"]["add_para"] ) {                    
                    $result = mail($cfg["kontakt"]["email"]["owner"],$subject1,$message1,$header1,$cfg["kontakt"]["email"]["add_para"]);
                } else {
                    $result = mail($cfg["kontakt"]["email"]["owner"],$subject1,$message1,$header1);
                }
                if ( !$result ) $ausgaben["form_error"] .= "<font color='red'>#(error_result) (". htmlspecialchars($cfg["kontakt"]["email"]["owner"]).")</font><br />";

                // kopie an kunden
                $subject2 = $cfg["kontakt"]["email"]["subj2"].$ausgaben["name"];
                if ( is_array($cfg["kontakt"]["email"]["repl2"]) ) {
                    foreach ( $cfg["kontakt"]["email"]["repl2"] as $value ) {
                        $subject2 = str_replace("!{".$value."}",$$value,$subject2);
                    }
                }
                if ( $_POST["betreff"] != "" ) $subject2 .= ": ".$_POST["betreff"];
                $header2  = "From: ".$cfg["kontakt"]["email"]["owner"]."\r\n";
                if ( $cfg["kontakt"]["email"]["encoding"] != "") {
                    $header2 .= "Content-Type: text/plain; charset=".$cfg["kontakt"]["email"]["encoding"]."\r\n";
                }
                if ( $cfg["kontakt"]["email"]["add_para"] ) {
                    $result = mail($email_adresse,$subject2,$message2,$header2,$cfg["kontakt"]["email"]["add_para"]);
                } else {
                    $result = mail($email_adresse,$subject2,$message2,$header2);                    
                }
                if ( !$result ) $ausgaben["form_error"] .= "<font color='red'>#(error_result) (".htmlspecialchars($email_adresse).")</font><br />";
                
                if ( $cfg["kontakt"]["email"]["save"] == -1 ) {

                    $sqla = "";
                    $sqlb = "";
                    $trenner = "";
                    $kick_array = array("send","last_viewed","form_referer","captcha","captcha_proof");
                    foreach ( $_POST as $key => $value ) {
                        if ( in_array($key, $kick_array) ) continue;
                        if ( $sqla != "" ) $trenner = ",";
                        $sqla .= $trenner."\"".$key."\"";
                        if ( !get_magic_quotes_gpc() && $value != "" ) {
                            $sqlb .= $trenner."'".addslashes($value)."'";
                        } else {
                            $sqlb .= $trenner."'".$value."'";
                        }                           
                    }
                    if ( $_POST[$cfg["kontakt"]["email"]["form_email_feld"]] != "" ) {
                        $sqla .= ",secure_key";
                        $sqlb .= ",'".crc32($_POST["captcha_proof"].$_POST["email"])."'";
                    }
                        $sql = "INSERT INTO ".$cfg["kontakt"]["db"]["entries"]."(".$sqla.") VALUES (".$sqlb.")";
                        $result = $db -> query($sql);
                }

                if ( $debugging["html_enable"] ){
                    $ausgaben["output"] = "<textarea name=\"debug1\" cols=\"60\" rows=\"20\">";
                    $ausgaben["output"] .= "Subject: ".$subject1."\n\n";
                    $ausgaben["output"] .= $message1;
                    $ausgaben["output"] .= "</textarea>";
                    $ausgaben["output"] .= "<br /><br />";
                    $ausgaben["output"] .= "<textarea name=\"debug2\" cols=\"60\" rows=\"20\">";
                    $ausgaben["output"] .= "Subject: ".$subject2."\n\n";
                    $ausgaben["output"] .= $message2;
                    $ausgaben["output"] .= "</textarea>";
                }
                if ( $error ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                // +++
                // funktions bereich fuer erweiterungen
            }

            // info anlegen
            if ( $ausgaben["form_error"] == ""  ) {
//                 if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
//                 if ( $header == "" ) $header = $cfg["kontakt"]["basis"]."/danke.html";
            }

            // wenn es keine fehlermeldungen gab, die uri $header laden
            if ( $ausgaben["form_error"] == "" ) {
                header("Location: ".$cfg["kontakt"]["basis"].",,sent.html?referer=".$_POST["last_viewed"]);
            }
        }

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>

