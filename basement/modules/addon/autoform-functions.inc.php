<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: autoform-functions.inc.php 1131 2007-12-12 08:45:50Z chaot $";
// "funktion loader";
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

    /* um funktionen z.b. in der kategorie add zu laden, leer.cfg.php wie folgt aendern
    /*
    /*    "function" => array(
    /*                 "add" => array( "function1_name", "function2_name"),
    */

    // beschreibung der funktion
    if ( in_array("mail_order", $cfg["autoform"]["function"][$environment["kategorie"]]) ) {
         function mail_order(  $posts, $config ) {
                // kunde
                if ( $posts[$config["form_email_feld"]] == "" ) {
                    echo $config["robot"];
                    $email_adresse = $config["robot"];
                } else {
                    $email_adresse = str_replace(",","",$posts[$config["form_name_feld"]])." <".$posts[$config["form_email_feld"]].">";
                }

                foreach ( $posts as $key => $value ) {
                   $$key = $value;
                }

                $message1 = parser($config["template1"],"");
                $message2 = parser($config["template2"],"");

                // mail an betreiber
                $subject1 = $config["subj1"];
                foreach ( $config["repl1"] as $value ) {
                    $subject1 = str_replace("!{".$value."}",$$value,$subject1);
                }

                if ( $posts["betreff"] != "" ) $subject1 .= ": ".$posts["betreff"];
                $header1  = "From: ".$config["robot"]."\r\n";
                $header1 .= "Reply-To: ".$email_adresse."\r\n";
                if ( $config["encoding"] != "" ) {
                    $header1 .= "Content-Type: text/plain; charset=".$config["encoding"]."\r\n";
                }
                $result = mail($config["owner"],$subject1,$message1,$header1);
                if ( !$result ) $ausgaben["form_error"] .= "<font color='red'>#(error_result) (". htmlspecialchars($config["owner"]).")</font><br />";

                // kopie an kunden
                $subject2 = $config["subj2"].$ausgaben["name"];
                foreach ( $config["repl2"] as $value ) {
                    $subject2 = str_replace("!{".$value."}",$$value,$subject2);
                }
                if ( $posts["betreff"] != "" ) $subject2 .= ": ".$posts["betreff"];
                $header2  = "From: ".$config["owner"]."\r\n";
                if ( $config["encoding"] != "") {
                    $header2 .= "Content-Type: text/plain; charset=".$config["encoding"]."\r\n";
                }
                $result = mail($email_adresse,$subject2,$message2,$header2);
                if ( !$result ) $ausgaben["form_error"] .= "<font color='red'>#(error_result) (".htmlspecialchars($email_adresse).")</font><br />";

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
    }

    ### platz fuer weitere funktionen ###

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
