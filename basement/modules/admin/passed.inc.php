<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  $script["name"] = "$Id$";
  $Script["desc"] = "user password change";
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

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** ".$script["name"]." ** ]".$debugging["char"];

    // warning ausgeben
    if ( get_cfg_var('register_globals') == 1 ) $debugging["ausgabe"] .= "Warning register_globals in der php.ini steht auf on, evtl werden interne Variablen ueberschrieben!".$debugging["char"];

    // path fuer die schaltflaechen anpassen
    if ( $cfg["iconpath"] == "" ) $cfg["iconpath"] = "/images/default/";

    // label bearbeitung aktivieren
    if ( isset($HTTP_GET_VARS["edit"]) ) {
        $specialvars["editlock"] = 0;
    } else {
        $specialvars["editlock"] = -1;
    }

    // warning ausgeben
    if ( get_cfg_var('register_globals') == 1 ) $debugging["ausgabe"] .= "Warning register_globals in der php.ini steht auf on, evtl werden interne Variablen ueberschrieben!".$debugging["char"];

    if ( $HTTP_SESSION_VARS["auth"] == -1 ) {

        if ( count($HTTP_POST_VARS) == 0 ) {
            $sql = "SELECT * FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$HTTP_SESSION_VARS["uid"]."'";
            $result = $db -> query($sql);
            $form_values = $db -> fetch_array($result,$nop);
        } else {
            $form_values = $HTTP_POST_VARS;
        }

        // form otions holen
        $form_options = form_options(crc32($environment["ebene"]).".".$environment["kategorie"]);

        // form elememte bauen
        $element = form_elements( $cfg["db"]["entries"], $form_values );

        // form elemente erweitern
        $element["oldpass"] = str_replace($cfg["db"]["pass"]."\"","oldpass\"",$element[$cfg["db"]["pass"]]);
        $element["newpass"] = str_replace($cfg["db"]["pass"]."\"","newpass\"",$element[$cfg["db"]["pass"]]);
        $element["chkpass"] = str_replace($cfg["db"]["pass"]."\"","chkpass\"",$element[$cfg["db"]["pass"]]);
        $element[$cfg["db"]["pass"]] = "";

        // was anzeigen
        #$mapping["main"] = crc32($environment["ebene"]).".modify";
        $mapping["navi"] = "leer";

        // wohin schicken
        $ausgaben["form_error"] = "";
        $ausgaben["form_aktion"] = $cfg["basis"]."/modify,edit,".$environment["parameter"][2].",verify.html";

        // unzugaengliche #(marken) sichtbar machen
        if ( isset($HTTP_GET_VARS["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_chkpass) #(error_chkpass)<br />";
            $ausgaben["inaccessible"] .= "# (error_oldpass) #(error_oldpass)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // referer im form mit hidden element mitschleppen
        if ( $HTTP_POST_VARS["form_referer"] == "" ) {
            $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        } else {
            $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
            $ausgaben["form_break"] = $ausgaben["form_referer"];
        }

        if ( $environment["parameter"][3] == "verify"
            && $HTTP_POST_VARS["send"] != "" ) {

            // form eigaben prüfen
            form_errors( $form_options, $form_values );

            // altes salt aus der user tabelle holen
            $sql = "SELECT ".$cfg["db"]["pass"]."
                      FROM ".$cfg["db"]["entries"]."
                     WHERE ".$cfg["db"]["key"]."='".$HTTP_SESSION_VARS["uid"]."'";
            $result  = $db -> query($sql);
            if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
            $data = $db -> fetch_array($result,0);
            $salt = substr($data[$cfg["db"]["pass"]],0,2);

            // ist passwort db = gesendetes altes passwort
            if ( $data[$cfg["db"]["pass"]] == crypt($HTTP_POST_VARS["oldpass"],$salt) ) {
                // neues passwort vorhanden und die wiederholung stimmt
                if ( $HTTP_POST_VARS["newpass"] != ""
                    && ( $HTTP_POST_VARS["newpass"] == $HTTP_POST_VARS["chkpass"] ) ) {

                    // neues passwort verschluesseln ( mysql = ecncrypt() )
                    $checked_password = $HTTP_POST_VARS["newpass"];
                    mt_srand((double)microtime()*1000000);
                    $a=mt_rand(1,128);
                    $b=mt_rand(1,128);
                    $mysalt = chr($a).chr($b);
                    $checked_password = crypt($checked_password, $mysalt);

                    // da ich das passwort erstellt habe, klappt magic_quotes_gpc nicht
                    $checked_password = addslashes($checked_password);

                } else {
                    $ausgaben["form_error"] .= "#(error_chkpass)";
                }
            } else {
                $ausgaben["form_error"] .= "#(error_oldpass)";
            }

            // ohne fehler sql bauen und ausfuehren
            if ( $ausgaben["form_error"] == "" ) {
                $sql = "UPDATE auth_user
                           SET ".$cfg["db"]["pass"]." = '".$checked_password."'
                         WHERE ".$cfg["db"]["key"]." = ".$HTTP_SESSION_VARS["uid"];
                if ( $debugging["sql_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                $result  = $db -> query($sql);
                if ( !$result ) $ausgaben["form_error"] .= $db -> error("#(error_result)<br />");
            }

            // ohne fehlermeldungen, weiterschicken
            if ( $ausgaben["form_error"] == "" ) {
                header("Location: ".$ausgaben["form_referer"]);
            }

        }
    } else {
        header("Location: ".$pathvars["webroot"]."/".$environment["design"]."/".$environment["language"]."/index.html");
    }

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
