<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id$";
  $Script["desc"] = "kontakt form";
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/*
    eWeBuKi - a easy website building kit
    Copyright (C)2001-2007 Werner Ammon ( wa<at>chaos.de )

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

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** ".$script["name"]." ** ]".$debugging["char"];

    if ( $rechte[$cfg["kontakt"]["right"]] == "" || $rechte[$cfg["kontakt"]["right"]] == -1 ) {

        // page basics
        // ***

        // warnung ausgeben
        if ( get_cfg_var('register_globals') == 1 ) $debugging["ausgabe"] .= "Warnung: register_globals in der php.ini steht auf on, evtl werden interne Variablen ueberschrieben!".$debugging["char"];

        // path fuer die schaltflaechen anpassen
        if ( $cfg["kontakt"]["iconpath"] == "" ) $cfg["kontakt"]["iconpath"] = "/images/default/";

        // label bearbeitung aktivieren
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $specialvars["editlock"] = 0;
        } else {
            $specialvars["editlock"] = -1;
        }

        #if ( count($HTTP_POST_VARS) == 0 ) {
        #} else {
            $form_values = $HTTP_POST_VARS;
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
            function captcha_randomize($length) {
                global $cfg;
                $random = "";
                while ( strlen($random) < $length ) {
                    $random .= substr($cfg["kontakt"]["captcha"]["letter_pot"], rand(0,strlen($cfg["kontakt"]["captcha"]["letter_pot"])), 1);
                }
                return $random;
            }

            function captcha_create($text) {
                global $cfg;
                // anzahl der zeichen
                $count = strlen($text);
                // schriftarten festlegen
                $ttf = $cfg["kontakt"]["captcha"]["ttf"];
                // schriftgroesse festlegen
                $ttf_size = $cfg["kontakt"]["captcha"]["font"]["size"];
                // schriftabstand rechts
                $ttf_x = $cfg["kontakt"]["captcha"]["font"]["x"];
                // schriftabstand oben
                $ttf_y = $cfg["kontakt"]["captcha"]["font"]["y"];

                // hintergrund erstellen
                $ttf_img = ImageCreate($count*2*$ttf_size,2*$ttf_size);
                // bgfarbe festlegen
                $bg_color = ImageColorAllocate ($ttf_img, $cfg["kontakt"]["captcha"]["bg_color"][0], $cfg["kontakt"]["captcha"]["bg_color"][1], $cfg["kontakt"]["captcha"]["bg_color"][2]);
                // textfarbe festlegen
                $font_color = ImageColorAllocate($ttf_img, $cfg["kontakt"]["captcha"]["font_color"][0], $cfg["kontakt"]["captcha"]["font_color"][1], $cfg["kontakt"]["captcha"]["font_color"][2]);
                // schrift in bild einfuegen
                foreach ( str_split($text) as $key=>$character ) {
                    // schriftwinkel festlegen
                    $ttf_angle = rand(-25,25);
                    // schriftarten auswaehlen
                    $ttf_font = $ttf[rand(0,(count($ttf)-1))];
                    imagettftext($ttf_img, $ttf_size, $ttf_angle, $ttf_size*2*$key+$ttf_x, $ttf_y, $font_color, $ttf_font, $character);
                }
                // bild temporaer als datei ausgeben
                $captcha_crc = crc32($text.$cfg["kontakt"]["captcha"]["randomize"]);
                $captcha_name = "captcha-".$captcha_crc.".png";
                $captcha_path = $cfg["file"]["base"]["maindir"].$cfg["file"]["base"]["new"];
                imagepng($ttf_img,$captcha_path.$captcha_name);
                // bild loeschen
                imagedestroy($ttf_img);
            }

            // zufaellige zeichen erzeugen
            $captcha_text = captcha_randomize($cfg["kontakt"]["captcha"]["length"]);
            // bild erzeugen
            captcha_create($captcha_text);
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
                if ( (mktime() - filemtime($captcha_file)) > 600 ) unlink($captcha_file);
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
            $ausgaben["last_viewed"] = $_POST["last_viewed"];
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
            if ( $_GET["referer"] != "" ) {
                $hidedata["referer"]["link"] = $_GET["referer"];
                $ausgaben["last_viewed"] = "";
            }
        }

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_result) #(error_result)<br />";
            $ausgaben["inaccessible"] .= "# (error_captcha) #(error_captcha)<br />";
            $ausgaben["inaccessible"] .= "# (error_dupe) #(error_dupe)<br />";
            $ausgaben["inaccessible"] .= "# (success) #(success)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        #n/a

        // +++
        // page basics

        if ( $environment["parameter"][2] == "verify"
            &&  ( $HTTP_POST_VARS["send"] != ""
                || $HTTP_POST_VARS["extension1"] != ""
                || $HTTP_POST_VARS["extension2"] != "" ) ) {

            // form eigaben pruefen
            form_errors( $form_options, $HTTP_POST_VARS );

            if ( is_array($cfg["kontakt"]["captcha"]) ) {
                if ( $_POST["captcha_proof"] != crc32($_POST["captcha"].$cfg["kontakt"]["captcha"]["randomize"])
                  || !file_exists($captcha_path_srv."captcha-".$_POST["captcha_proof"].".png") ) {
                    $ausgaben["form_error"] .= "#(error_captcha)";
                    $dataloop["form_error"]["captcha"]["text"] = "#(error_captcha)";
                    $hidedata["captcha"]["class"] = "form_error";
                }
                if (file_exists($captcha_path_srv."captcha-".$_POST["captcha_proof"].".png")) unlink($captcha_path_srv."captcha-".$_POST["captcha_proof"].".png");
            }

            // evtl. zusaetzliche datensatz anlegen
            if ( $ausgaben["form_error"] == ""  ) {

                // kunde
                $email_adresse = $HTTP_POST_VARS["ansprechpartner"]." <".$HTTP_POST_VARS["e-mail"].">";

                foreach ( $HTTP_POST_VARS as $key => $value ) {
                   $$key = $value;
                }

                $message1 = parser("kontakt-email1","");
                $message2 = parser("kontakt-email2","");

                // happy bouncing
                ini_set("sendmail_from",$cfg["kontakt"]["email"]["robot"]);

                // mail an betreiber
                $subject1 = $cfg["kontakt"]["email"]["subj1"].$ausgaben["name"];
                if ( $_POST["betreff"] != "" ) $subject1 .= ": ".$_POST["betreff"];
                $header1  = "From: ".$cfg["kontakt"]["email"]["robot"]."\r\n";
                $header1 .= "Reply-To: ".$email_adresse."\r\n";
                if ( $cfg["kontakt"]["email"]["encoding"] != "" ) {
                    $header1 .= "Content-Type: text/plain; charset=".$cfg["kontakt"]["email"]["encoding"]."\r\n";
                }
                $result = mail($cfg["kontakt"]["email"]["owner"],$subject1,$message1,$header1);
                if ( !$result ) $ausgaben["form_error"] .= "<font color='red'>#(error_result) (". htmlspecialchars($cfg["kontakt"]["email"]["owner"]).")</font><br />";

                // kopie an kunden
                $subject2 = $cfg["kontakt"]["email"]["subj2"].$ausgaben["name"];
                if ( $_POST["betreff"] != "" ) $subject2 .= ": ".$_POST["betreff"];
                $header2  = "From: ".$cfg["kontakt"]["email"]["owner"]."\r\n";
                if ( $cfg["kontakt"]["email"]["encoding"] != "") {
                    $header2 .= "Content-Type: text/plain; charset=".$cfg["kontakt"]["email"]["encoding"]."\r\n";
                }
                $result = mail($email_adresse,$subject2,$message2,$header2);
                #if ( !$result ) $ausgaben["form_error"] .= "<font color='red'>#(error_result) (".htmlspecialchars($email_adresse).")</font><br />";

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

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>

