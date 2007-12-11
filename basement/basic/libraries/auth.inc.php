<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    $script_name = "$Id$";
    $Script_desc = "authentifikation modul (mysql encrypt/ php crypt)";
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

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ** $script_name ** ]".$debugging["char"];

    // ACHTUNG: auth.cfg.php im Config Directory wird inkludiert!

    // link zur hidden login kategorie erstellen
    $pathvars["pretorian"] = $pathvars["menuroot"]."/".$cfg["auth"]["hidden"]["kategorie"].".html";

    // referer im form mit hidden element mitschleppen
    if ( $HTTP_POST_VARS["form_referer"] == "" ) {
        $a = 4;
        if ( $pathvars["subdir"] != "" ) $a++;
        $path = split("/",$_SERVER["HTTP_REFERER"],$a);
        $ausgaben["form_referer"] = "/".$path[--$a];
        $ausgaben["form_break"] = $ausgaben["form_referer"];
    } else {
        $ausgaben["form_referer"] = $HTTP_POST_VARS["form_referer"];
        $ausgaben["form_break"] = $ausgaben["form_referer"];
    }
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "referer = ".$_SERVER["HTTP_REFERER"].$debugging["char"];
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "path = ".$path[$a].$debugging["char"];
    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "form_referer = ".$ausgaben["form_referer"].$debugging["char"];

    // login ueberpruefen
    if ( $HTTP_POST_VARS["login"] == "login" ) {
        if ( $cfg["auth"]["db"]["user"]["custom"] != "" ) $custom = ", ".$cfg["auth"]["db"]["user"]["custom"];
        $sql = "SELECT ".$cfg["auth"]["db"]["user"]["id"].",
                       ".$cfg["auth"]["db"]["user"]["surname"].",
                       ".$cfg["auth"]["db"]["user"]["forename"].",
                       ".$cfg["auth"]["db"]["user"]["email"].",
                       ".$cfg["auth"]["db"]["user"]["alias"].",
                       ".$cfg["auth"]["db"]["user"]["pass"]."
                       ".$custom."
                  FROM ".$cfg["auth"]["db"]["user"]["entries"]."
                 WHERE ".$cfg["auth"]["db"]["user"]["alias"]."='".$HTTP_POST_VARS["user"]."'";
        $result  = $db -> query($sql);
        $AUTH = $db -> fetch_array($result,0);

        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "CRYPT_SALT_LENGTH = ".CRYPT_SALT_LENGTH.$debugging["char"];
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "CRYPT_STD_DES = ".CRYPT_STD_DES.$debugging["char"];
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "CRYPT_EXT_DES = ".CRYPT_EXT_DES.$debugging["char"];
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "CRYPT_MD5 = ".CRYPT_MD5.$debugging["char"];
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "CRYPT_BLOWFISH  = ".CRYPT_BLOWFISH.$debugging["char"];

        if ( $AUTH[$cfg["auth"]["db"]["user"]["pass"]] != "" ) {
            $SALT = substr($AUTH[$cfg["auth"]["db"]["user"]["pass"]],0,2);
        }
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "SA = ".$SALT.$debugging["char"];
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "DB = ".$AUTH[$cfg["auth"]["db"]["user"]["pass"]].$debugging["char"];

        $CRYPT_PASS = crypt($HTTP_POST_VARS["pass"],$SALT);
        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "FM = ".$CRYPT_PASS.$debugging["char"];

        if ( $AUTH[$cfg["auth"]["db"]["user"]["id"]] != "" && $AUTH[$cfg["auth"]["db"]["user"]["pass"]] == $CRYPT_PASS ) {

            session_start();
            $_SESSION["auth"] = -1;
            $_SESSION["uid"] = $AUTH[$cfg["auth"]["db"]["user"]["id"]];
            $_SESSION["username"] = $AUTH[$cfg["auth"]["db"]["user"]["alias"]]; # old
            $_SESSION["surname"] = $AUTH[$cfg["auth"]["db"]["user"]["surname"]];
            $_SESSION["forename"] = $AUTH[$cfg["auth"]["db"]["user"]["forename"]];
            $_SESSION["email"] = $AUTH[$cfg["auth"]["db"]["user"]["email"]];
            $_SESSION["alias"] = $AUTH[$cfg["auth"]["db"]["user"]["alias"]];
            $_SESSION["custom"] = $AUTH[$cfg["auth"]["db"]["user"]["custom"]];

             if ( $specialvars["security"]["new"] == True ) {
                 $sql = "SELECT tname,auth_priv.priv FROM auth_content
                         INNER JOIN auth_member ON (auth_content.gid=auth_member.gid )
                         INNER JOIN auth_priv ON ( auth_priv.pid=auth_content.pid )
                         WHERE auth_member.uid=".$AUTH[$cfg["auth"]["db"]["user"]["id"]];
                 $result = $db -> query($sql);
                 while ( $data = $db -> fetch_array($result,$nop) ) {

                    $_SESSION["content"][$data["tname"]] .= $data["priv"].",";
                 }

             }

            // wenn content_right on dann katzugriff array bauen
            if ( $specialvars["security"]["enable"] == -1 ) {
                $sql = "SELECT ".$cfg["auth"]["db"]["special"]["contentkey"].",
                               ".$cfg["auth"]["db"]["special"]["dbasekey"].",
                               ".$cfg["auth"]["db"]["special"]["tnamekey"]."
                          FROM ".$cfg["auth"]["db"]["special"]["entries"]."
                         WHERE ".$cfg["auth"]["db"]["special"]["userkey"]."='".$_SESSION["uid"]."'";
                $result = $db -> query($sql);
                while ( $data = $db -> fetch_array($result,$nop) ) {
                    $_SESSION["katzugriff"][] = $data["content"].":".$data["sdb"].":".$data["stname"];
                }
            }

            // referer oder aktuelle seite
            if ( $cfg["auth"]["hidden"]["set"] == True ) {
                $destination_src = $ausgaben["form_referer"];
            } else {
                $destination_src = $pathvars["requested"];
            }

            // replace with virtual path
            if ( $pathvars["virtual_depth"] != 1 ) {
                $pos = strpos($destination_src, $in);
                $destination = substr($destination_src, 0, $pos ) .
                               $pathvars["virtual"]."/auth" .
                               substr($destination_src, $pos+strlen($pathvars["virtual"]));
            } else {
                $destination = "/auth".$destination_src;
            }

            session_write_close();
            header("Location: ".$pathvars["subdir"].$destination);
            exit; // Sicherstellen, dass nicht trotz Umleitung der nachfolgende Code ausgeführt wird.
        } else {
            session_start();
            $_SESSION = array();
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time()-42000, '/');
            }
            session_destroy();
            $hidedata["authArea"]["message"] = "#(denied)";

        }
    }



    // logout durchfuehren und session loeschen
    if ( $HTTP_POST_VARS["logout"] == "logout" ) {
        session_start();
        $_SESSION = array();
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time()-42000, '/');
        }
        session_destroy();
        $hidedata["authArea"]["message"] = "#(logout)";
    }

    // session variablen nur holen wenn /auth/ in der url
    if ( strstr($_SERVER["REQUEST_URI"],"/auth/") ) {

        session_start();


        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "auth = ".$_SESSION["auth"].$debugging["char"];

        // bei ungueltiger session auth aus der url nehmen
        if ( $_SESSION["auth"] != -1 ) {
            header("Location: ". str_replace("/auth","",$_SERVER["REQUEST_URI"]));
        }

        if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "uid = ".$_SESSION["uid"].$debugging["char"];
        if ( $_SESSION["uid"] != "" ) {
            $sql = "SELECT level FROM ".$cfg["auth"]["db"]["level"]["entries"]."
                    INNER JOIN ".$cfg["auth"]["db"]["right"]["entries"]."
                    ON ".$cfg["auth"]["db"]["level"]["entries"].".".$cfg["auth"]["db"]["level"]["id"]." = ".$cfg["auth"]["db"]["right"]["entries"].".".$cfg["auth"]["db"]["right"]["levelkey"]."
                    WHERE ".$cfg["auth"]["db"]["right"]["entries"].".uid = ".$_SESSION["uid"];
            $result  = $db -> query($sql);
            while ( $row = $db -> fetch_row($result) ) {
                $rechte[$row[0]] = -1;
            }
            // load customer addon
            if ( $cfg["auth"]["custom"]["load"] == -1 ) {
                include $pathvars["moduleroot"].$cfg["auth"]["custom"]["path"]."/".$cfg["auth"]["custom"]["file"].".inc.php";
            }
        }
        $specialvars["phpsessid"] = "?PHPSESSID=".session_id();
     }

    // da hidden menu aktiviert ist, 404 fuer diese kategorie ausschalten
    if ( $cfg["auth"]["hidden"]["set"] == True ) $specialvars["404"]["nochk"]["kategorie"][] =  $cfg["auth"]["hidden"]["kategorie"];

    // label bearbeitung aktivieren
    if ( isset($HTTP_GET_VARS["edit"]) ) {
        $specialvars["editlock"] = 0;
    } else {
        $specialvars["editlock"] = -1;
    }

    // daten fuer login, logout formular setzen
    if ( $_SESSION["auth"] != -1 ) {
            if ( $cfg["auth"]["hidden"]["set"] == True ) {
                $hidedata["authArea"]["message"] = "#(secret)";
            } else {
                $hidedata["authArea"]["message"] = "";
            }
        if ( $cfg["auth"]["hidden"]["set"] != True || $environment["kategorie"] == $cfg["auth"]["hidden"]["kategorie"] ) {
            $hidedata["authLogin"]["nop"] = "";
        }
    } else {
        $hidedata["authArea"]["message"] = "#(vorher) \"".$_SESSION["username"]."\" #(nachher)";
        $hidedata["authLogout"]["nop"] = "";

        // in place functions
        $path = dirname($pathvars["requested"]);
        if ( substr( $path, -1 ) != '/') $path = $path."/";

        foreach ( $cfg["auth"]["inplace"] as $key => $value ) {
            if ( priv_check_old("","cms_admin") == True  || priv_check($environment["ebene"]."/".$environment["kategorie"],$value) ) {
                $hidedata["authInPlace"]["newlink"] = $path.basename($pathvars["requested"],".html")."/".$key.".html";
            }
        }

        // ed links
        $hidedata["authTools"]["links"] = "on";
        foreach( $cfg["auth"]["menu"] as $funktion => $werte) {
            if ( $cfg["auth"]["boxed"] == True ) {
                $label = strtoupper($funktion[0]);
                $end = " ";
                $hidedata["authBox"]["nop"] = "";
            } else {
                $label = "#(".$funktion.")";
                $end = "<br />";
            }

            if ( $specialvars["security"]["new"] == -1 ) {
                $check = priv_check("/admin/".$funktion."/".$werte[0],$werte[1]);
            } else {
                $check = priv_check_old("/admin/".$funktion."/".$werte[0]);
            }

            if ( $check == True ) {
                $dataloop["authTools"][$funktion]["url"] = $pathvars["subdir"].$pathvars["virtual"]."/admin/".$funktion."/".$werte[0].".html";
                $dataloop["authTools"][$funktion]["label"] = $label;
                $dataloop["authTools"][$funktion]["title"] = "#(".$funktion.")";
                $dataloop["authTools"][$funktion]["end"] = $end;
            }
        }


    }

    $specialvars["editlock"] = 0;

    if ( $debugging["html_enable"] ) $debugging["ausgabe"] .= "[ ++ $script_name ++ ]".$debugging["char"];

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
