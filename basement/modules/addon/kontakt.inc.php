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

    86343 Königsbrunn

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
        $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

        // form elememte bauen
        $element = form_elements( $cfg["kontakt"]["db"]["entries"], $form_values );

        // fehlermeldungen
        $ausgaben["form_error"] = "";

        // navigation erstellen
        $ausgaben["form_aktion"] = $cfg["kontakt"]["basis"].",verify.html";
        $ausgaben["form_break"] = "index.html";

        // hidden values
        $ausgaben["form_hidden"] .= "";

        // was anzeigen
        #$mapping["main"] = crc32($environment["ebene"]).".modify";
        #$mapping["navi"] = "leer";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_result) #(error_result)<br />";
            $ausgaben["inaccessible"] .= "# (error_dupe) #(error_dupe)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // wohin schicken
        #n/a

        // +++
        // page basics

        if ( $environment["parameter"][1] == "verify"
            &&  ( $HTTP_POST_VARS["send"] != ""
                || $HTTP_POST_VARS["extension1"] != ""
                || $HTTP_POST_VARS["extension2"] != "" ) ) {

            // form eigaben prüfen
            form_errors( $form_options, $HTTP_POST_VARS );

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
                $header1  = "From: ".$cfg["kontakt"]["email"]["robot"]."\r\n";
                $header1 .= "Reply-To: ".$email_adresse."\r\n";
                $result = mail($cfg["kontakt"]["email"]["owner"],$subject1,$message1,$header1);
                if ( !$result ) $ausgaben["form_error"] .= "<font color='red'>#(error_result) (". htmlspecialchars($cfg["kontakt"]["email"]["owner"]).")</font><br />";

                // kopie an kunden
                $subject2 = $cfg["kontakt"]["email"]["subj2"].$ausgaben["name"];;
                $header2  = "From: ".$cfg["kontakt"]["email"]["owner"]."\r\n";
                $result = mail($email_adresse,$subject2,$message2,$header2);
                if ( !$result ) $ausgaben["form_error"] .= "<font color='red'>#(error_result) (".htmlspecialchars($email_adresse).")</font><br />";

                $ausgaben["output"] = "<textarea name=\"debug1\" cols=\"60\" rows=\"20\">";
                $ausgaben["output"] .= "Subject: ".$subject1."\n\n";
                $ausgaben["output"] .= $message1;
                $ausgaben["output"] .= "</textarea>";
                $ausgaben["output"] .= "<br /><br />";
                $ausgaben["output"] .= "<textarea name=\"debug2\" cols=\"60\" rows=\"20\">";
                $ausgaben["output"] .= "Subject: ".$subject2."\n\n";
                $ausgaben["output"] .= $message2;
                $ausgaben["output"] .= "</textarea>";

                if ( $error ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                // +++
                // funktions bereich fuer erweiterungen
            }

            // info anlegen
            if ( $ausgaben["form_error"] == ""  ) {
                if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
                if ( $header == "" ) $header = $cfg["kontakt"]["basis"]."/danke.html";
            }

            // wenn es keine fehlermeldungen gab, die uri $header laden
            if ( $ausgaben["form_error"] == "" ) {
                header("Location: ".$header);
            }
        }

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>

