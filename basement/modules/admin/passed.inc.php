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

    // content umschaltung verhindern
    $specialvars["dynlock"] = True;

    // warning ausgeben
    if ( get_cfg_var('register_globals') == 1 ) $debugging["ausgabe"] .= "Warning register_globals in der php.ini steht auf on, evtl werden interne Variablen ueberschrieben!".$debugging["char"];

    if ( $HTTP_SESSION_VARS["auth"] == -1 ) {

        if ( count($HTTP_POST_VARS) == 0 ) {
            #$sql = "SELECT ".$cfg["db"]["pass"]." FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$HTTP_SESSION_VARS["uid"]."'";
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
            form_errors( $form_options, $form_values );

            // form eingaben prüfen erweitern
            $sql = "SELECT ".$cfg["db"]["pass"]." FROM ".$cfg["db"]["entries"]." WHERE ".$cfg["db"]["key"]."='".$HTTP_SESSION_VARS["uid"]."'";
            $result  = $db -> query($sql);
            $data = $db -> fetch_array($result,0);
            $salt = substr($data[$cfg["db"]["pass"]],0,2);
            $oldpass = crypt($HTTP_POST_VARS["oldpass"],$salt);

            if ( $oldpass == $data[$cfg["db"]["pass"]] ) {               
                if ( $HTTP_POST_VARS["newpass"] == $HTTP_POST_VARS["chkpass"] && $HTTP_POST_VARS["newpass"] != "" ) {                   
                    $checked_password = $HTTP_POST_VARS["newpass"];
                    mt_srand((double)microtime()*1000000);
                    $a=mt_rand(1,128);
                    $b=mt_rand(1,128);
                    $mysalt = chr($a).chr($b);
                    $checked_password = crypt($checked_password, $mysalt);
                } else {
                    $ausgaben["form_error"] .= $form_options[$cfg["db"]["pass"]]["ferror"]." ( <> )";
                }
            } else {
                $ausgaben["form_error"] .= $form_options[$cfg["db"]["pass"]]["ferror"]." ( !! )";
            }

            // ohne fehler sql bauen und ausfuehren
            if ( $ausgaben["form_error"] == "" && ( $HTTP_POST_VARS["submit"] || $HTTP_POST_VARS["image"] != "" ) ){
                $kick = array( "PHPSESSID", "ablogin", "oldpass", "newpass", "chkpass", "submit", "submit_x", "submit_y", "form_referer" );

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
                if ( $checked_password != "" ) {
                    $sqla .= $cfg["db"]["pass"]."='".$checked_password."'";
                }
                $sql = "update ".$cfg["db"]["entries"]." SET ".$sqla." WHERE ".$cfg["db"]["key"]."='".$HTTP_SESSION_VARS["uid"]."'";               
                $result  = $db -> query($sql);
                if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "sql: ".$sql.$debugging["char"];
                header("Location: ".$ausgaben["form_referer"]);
            }
        }
    } else {
        header("Location: ".$pathvars["webroot"]."/".$environment["design"]."/".$environment["language"]."/index.html");
    }


    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ ".$script["name"]." ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
