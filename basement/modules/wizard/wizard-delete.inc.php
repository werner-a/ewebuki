<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// "$Id: wizard-delete.inc.php 1242 2008-02-08 16:16:50Z chaot $";
// "wizard - delete funktion";
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

    // parameter-verzeichnis:
    // 1: Datenbank
    // 2: tname
    // 3: label

    // erlaubnis bei intrabvv speziell setzen
    $database = $environment["parameter"][1];
    if ( is_array($_SESSION["katzugriff"]) ) {
        if ( in_array("-1:".$database.":".$environment["parameter"][2],$_SESSION["katzugriff"]) ) $erlaubnis = -1;
    }

    if ( is_array($_SESSION["dbzugriff"]) ) {
        if ( in_array($database,$_SESSION["dbzugriff"]) ) $erlaubnis = -1;
    }

    $url = tname2path($environment["parameter"][2]);
    $tname2path = tname2path($environment["parameter"][2]);

    if ( is_array($cfg["bloged"]["blogs"][substr($tname2path,0,strrpos($tname2path,"/"))])
        && $cfg["bloged"]["blogs"][substr($tname2path,0,strrpos($tname2path,"/"))]["category"] != "" ) {
        $kate = $cfg["bloged"]["blogs"][substr($tname2path,0,strrpos($tname2path,"/"))]["category"];
        $laenge = strlen($kate)+2;
        $sql = "SELECT SUBSTR(content,POSITION('[".$kate."]' IN content)+".$laenge.",POSITION('[/".$kate."]' IN content)-".$laenge."-POSITION('[".$kate."]' IN content) )as check_url from site_text where tname = '".$environment["parameter"][2]."'";
        $result = $db -> query($sql);
        $data = $db -> fetch_array($result,1);
        $artikel_check = priv_check($data["check_url"],$cfg["wizard"]["right"]["edit"]);
        $artikel_check_publish = priv_check($data["check_url"],"publish");

    }

    if ( $cfg["wizard"]["right"]["edit"] == "" ||
        priv_check($url,$cfg["wizard"]["right"]["edit"]) || priv_check(tname2path($environment["parameter"][2]),$cfg["wizard"]["right"]["edit"]) ||
        priv_check_old("",$cfg["wizard"]["right"]) || $artikel_check ||
        $rechte["administration"] == -1 ||
        $erlaubnis == -1 ) {

        // unzugaengliche #(marken) sichtbar machen
        // ***
        if ( isset($_GET["edit"]) ) {
            $ausgaben["inaccessible"] = "inaccessible values:<br />";
            $ausgaben["inaccessible"] .= "# (error_result) #(error_result)<br />";
            $ausgaben["inaccessible"] .= "# (error_dupe) #(error_dupe)<br />";
        } else {
            $ausgaben["inaccessible"] = "";
        }

        // form options holen
        $form_options = form_options(eCRC("/admin/menued").".add");

        // fehlermeldungen
        $ausgaben["form_error"] = $_SESSION["form_error"]["desc"];

        if ( strstr($_SERVER["HTTP_REFERER"],"auth/login") ) {
            $ausgaben["form_referer"] = "/auth/login.html";
        } else {
            $ausgaben["form_referer"] = $_SERVER["HTTP_REFERER"];
        }

        // form elememte bauen
        $element = array_merge(form_elements( "site_menu", $form_values ), form_elements( "site_menu_lang", $form_values ));

        // freigabe-test
        if ( $specialvars["content_release"] == -1 ) {
            $hidedata["add_menu"]["hide"] = -1;
        } else {
            $hidedata["add_menu"]["hide"] = "";
        }

        //wohin schicken
        $ausgaben["form_aktion"] = $pathvars["virtual"]."/wizard/delete,devel0,".$environment["parameter"][2].",inhalt.html";
        $ausgaben["refid"] = $point["mid"];

        $sql = "SELECT status,version, html, content, changed, byalias
                FROM ". SITETEXT ."
                WHERE lang = '".$environment["language"]."'
                AND label ='".$environment["parameter"][3]."'
                AND tname ='".$environment["parameter"][2]."'
            ORDER BY version ASC";

        $result = $db -> query($sql);
        $security = "";
        while ( $data = $db -> fetch_array($result,0) ) {
            if ( $data["status"] == -2 ) $security = -1;
            if ( $data["status"] == 0 ) {
                $dataloop["allartikel"][$data["version"]]["color"] = "";
                $dataloop["allartikel"][$data["version"]]["text"] = "historisch";
            } elseif ( $data["status"] == 1 ) {
                $dataloop["allartikel"][$data["version"]]["color"] = "";
                $dataloop["allartikel"][$data["version"]]["text"] = "aktuell";
            } else {
                $dataloop["allartikel"][$data["version"]]["color"] = "#FF9148";
                $dataloop["allartikel"][$data["version"]]["text"] = "in Bearbeitung - wird geloescht";
            }
            $dataloop["allartikel"][$data["version"]]["version"] = $data["version"];
        }
            $hidedata["vorschau"]["on"] = "AJAX";

        if ( $_POST["ajax"] == "on" ) {
            $sql = "SELECT content
                    FROM ". SITETEXT ."
                    WHERE lang = '".$environment["language"]."'
                    AND label ='".$environment["parameter"][3]."'
                    AND tname ='".$environment["parameter"][2]."'
                    AND version ='".$_POST["version"]."'";
            $result = $db -> query($sql);
            $data = $db -> fetch_array($result,0);
            echo tagreplace($data["content"]);
            $hidedata["vorschau"]["on"] = "AJAX";
            header("HTTP/1.0 200 OK");
            die;
        }
        if ( $_POST["delete"] && $security != -1 ) {
                $sql = "DELETE FROM ". SITETEXT ."
                        WHERE lang = '".$environment["language"]."'
                        AND label ='".$environment["parameter"][3]."'
                        AND tname ='".$environment["parameter"][2]."'
                        AND status ='-1'";
                $result = $db -> query($sql);
                header("Location: ".$pathvars["virtual"]."/login.html");
        }

        // was anzeigen
        $mapping["main"] = "wizard-delete";

    } else {
        header("Location: ".$pathvars["virtual"]."/");
    }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>