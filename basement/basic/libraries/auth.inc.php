<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $script_name = "$Id$";
    $Script_desc = "authentifikation modul (mysql encrypt/ php crypt)";
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

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** $script_name ** ]".$debugging["char"];

    // ACHTUNG: auth.cfg.php im Config Directory wird inkludiert!

    // login ueberpruefen
    if ( $HTTP_POST_VARS["login"] == "login" ) {
        if ( $cfg["db"]["user"]["custom"] != "" ) $custom = ", ".$cfg["db"]["user"]["custom"];
        $sql = "SELECT ".$cfg["db"]["user"]["id"].", ".$cfg["db"]["user"]["name"].", ".$cfg["db"]["user"]["pass"].$custom." FROM ".$cfg["db"]["user"]["entries"]." WHERE ".$cfg["db"]["user"]["name"]."='".$HTTP_POST_VARS["user"]."'";
        $result  = $db -> query($sql);
        $AUTH = $db -> fetch_array($result,0);

        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "CRYPT_SALT_LENGTH = ".CRYPT_SALT_LENGTH.$debugging["char"];
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "CRYPT_STD_DES = ".CRYPT_STD_DES.$debugging["char"];
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "CRYPT_EXT_DES = ".CRYPT_EXT_DES.$debugging["char"];
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "CRYPT_MD5 = ".CRYPT_MD5.$debugging["char"];
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "CRYPT_BLOWFISH  = ".CRYPT_BLOWFISH.$debugging["char"];

        if ( $AUTH[$cfg["db"]["user"]["pass"]] != "" ) {
            $SALT = substr($AUTH[$cfg["db"]["user"]["pass"]],0,2);
        }
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "SA = ".$SALT.$debugging["char"];
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "DB = ".$AUTH[$cfg["db"]["user"]["pass"]].$debugging["char"];

        $CRYPT_PASS = crypt($HTTP_POST_VARS["pass"],$SALT);
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "FM = ".$CRYPT_PASS.$debugging["char"];

        if ( $AUTH[$cfg["db"]["user"]["id"]] != "" && $AUTH[$cfg["db"]["user"]["pass"]] == $CRYPT_PASS ) {
            session_register("auth");
            $HTTP_SESSION_VARS["auth"] = -1;
            session_register("uid");
            $HTTP_SESSION_VARS["uid"] = $AUTH[$cfg["db"]["user"]["id"]];
            session_register("username");
            $HTTP_SESSION_VARS["username"] = $AUTH[$cfg["db"]["user"]["name"]];
            session_register("custom");
            $HTTP_SESSION_VARS["custom"] = $AUTH[$cfg["db"]["user"]["custom"]];
            $destination = str_replace($pathvars["virtual"],$pathvars["virtual"]."/auth",$pathvars["requested"]);
            header("Location: ".$destination);
        } else {
            session_start();
            session_unset();
            session_destroy();
            $ausgaben["login_meldung"] = "#(denied)";
        }
    }

    // logout durchfuehren und session loeschen
    if ( $HTTP_POST_VARS["logout"] == "logout" ) {
            session_start();
            session_unset();
            session_destroy();
            $ausgaben["login_meldung"] = "#(logout)";
    }

    // session variablen nur holen wenn /auth/ in der url
    if ( strstr($_SERVER["REQUEST_URI"],"/auth/") ) {

        session_register("auth");
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "auth = ".$HTTP_SESSION_VARS["auth"].$debugging["char"];

        // bei ungueltiger session auth aus der url nehmen
        if ( $HTTP_SESSION_VARS["auth"] != -1 ) {
            header("Location: ".$ausgaben["auth_url"]);
        }

        session_register("uid");
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "uid = ".$HTTP_SESSION_VARS["uid"].$debugging["char"];
        if ( $HTTP_SESSION_VARS["uid"] != "" ) {
            $sql = "SELECT level FROM ".$cfg["db"]["level"]["entries"]."
                    INNER JOIN ".$cfg["db"]["right"]["entries"]."
                    ON ".$cfg["db"]["level"]["entries"].".".$cfg["db"]["level"]["id"]." = ".$cfg["db"]["right"]["entries"].".".$cfg["db"]["right"]["levelkey"]."
                    WHERE ".$cfg["db"]["right"]["entries"].".uid = ".$HTTP_SESSION_VARS["uid"];
            $result  = $db -> query($sql);
            while ( $row = $db -> fetch_row($result) ) {
                $rechte[$row[0]] = -1;
            }
            // load customer addon
            if ( $cfg["custom"]["load"] == -1 ) {
                include $pathvars["addonroot"].$cfg["custom"]["path"]."/".$cfg["custom"]["file"].".inc.php";
            }
        }
        $specialvars["phpsessid"] = "?PHPSESSID=".session_id();
     }

    // daten fuer login, logout formular setzen
    if ( $HTTP_SESSION_VARS["auth"] != -1 ) {
        $ausgaben["login_meldung"] .= "";
        if ( $cfg["hidden"]["set"] != True || $environment["kategorie"] == $cfg["hidden"]["kategorie"] ) {
          $ausgaben["auth"] = parser( "auth.login", "");
        } else {
          $ausgaben["auth"] = "";
        }
    } else {
        $ausgaben["logout_meldung"] = "\"".$HTTP_SESSION_VARS["username"]."\"";
        $ausgaben["logout_rechte"] = "";

        foreach( $cfg["menu"] as $funktion => $werte) {
            $array = explode(";", $werte[1]);
            foreach( $array as $value) {
                if ( $rechte[$value] == -1 || $value == "" ) {
                    $ausgaben["logout_rechte"] .= "<a href=\"".$pathvars["virtual"]."/admin/".$funktion."/".$werte[0].".html\">#(".$funktion.")</a><br>";
                    break;
                }
            }
        }
        if ( $ausgaben["logout_rechte"] != "" ) $ausgaben["logout_rechte"] = "<br>#(desc)<br><br>".$ausgaben["logout_rechte"];

        $ausgaben["auth"] = parser( "auth.logout", "");
    }

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ $script_name ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
